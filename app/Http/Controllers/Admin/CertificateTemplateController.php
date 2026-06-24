<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCertificateTemplateRequest;
use App\Models\CertificateTemplate;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateTemplateController extends Controller
{
    public function index()
    {
        $templates = CertificateTemplate::latest()->get();
        return view('admin.certificate-templates.index', compact('templates'));
    }

    public function create()
    {
        $template = CertificateTemplate::makeDefault();
        return view('admin.certificate-templates.form', compact('template'));
    }

    public function store(StoreCertificateTemplateRequest $request)
    {
        $validated = $request->validated();

        // Handle upload background ke MinIO (s3) via secure service
        if ($request->hasFile('background_image')) {
            $validated['background_image'] = app(MinioStorageService::class)
                ->uploadCertificateBackground($request->file('background_image'));
        }

        $template = CertificateTemplate::create($validated);

        if ($request->boolean('set_active')) {
            $template->setAsActive();
        }

        return redirect()
            ->route('admin.certificate-templates.index')
            ->with('success', "Template \"{$template->name}\" berhasil disimpan.");
    }

    public function edit(CertificateTemplate $certificateTemplate)
    {
        $template = $certificateTemplate;
        return view('admin.certificate-templates.form', compact('template'));
    }

    public function update(StoreCertificateTemplateRequest $request, CertificateTemplate $certificateTemplate)
    {
        $validated = $request->validated();

        // Handle upload background baru (opsional saat edit)
        if ($request->hasFile('background_image')) {
            // Hapus file lama dari MinIO jika ada
            if ($certificateTemplate->background_image) {
                Storage::disk('s3')->delete($certificateTemplate->background_image);
            }
            $validated['background_image'] = app(MinioStorageService::class)
                ->uploadCertificateBackground($request->file('background_image'));
        } else {
            // Pertahankan background lama
            unset($validated['background_image']);
        }

        $certificateTemplate->update($validated);

        if ($request->boolean('set_active')) {
            $certificateTemplate->setAsActive();
        }

        return redirect()
            ->route('admin.certificate-templates.index')
            ->with('success', "Template \"{$certificateTemplate->name}\" berhasil diperbarui.");
    }

    public function setActive(CertificateTemplate $certificateTemplate)
    {
        if (!$certificateTemplate->background_image) {
            return back()->with('error', 'Template harus memiliki gambar background sebelum diaktifkan.');
        }
        $certificateTemplate->setAsActive();
        return back()->with('success', "Template \"{$certificateTemplate->name}\" sekarang menjadi desain aktif.");
    }

    public function destroy(CertificateTemplate $certificateTemplate)
    {
        if ($certificateTemplate->is_active) {
            return back()->with('error', 'Template aktif tidak dapat dihapus. Aktifkan template lain terlebih dahulu.');
        }
        if ($certificateTemplate->background_image) {
            Storage::disk('s3')->delete($certificateTemplate->background_image);
        }
        $name = $certificateTemplate->name;
        $certificateTemplate->delete();
        return back()->with('success', "Template \"{$name}\" berhasil dihapus.");
    }

    /**
     * Preview HTML sertifikat dengan template dari form (GET dengan query string).
     * Dipakai oleh iframe di halaman form.
     */
    public function preview(Request $request)
    {
        // Bangun objek template dari query string
        $tpl = new CertificateTemplate();
        $tpl->forceFill([
            'name_x'              => $request->get('name_x', 50),
            'name_y'              => $request->get('name_y', 52),
            'name_font_size'      => $request->get('name_font_size', 36),
            'name_font_color'     => $request->get('name_font_color', '#1E3A5F'),
            'name_align'          => $request->get('name_align', 'center'),
            'name_bold'           => $request->boolean('name_bold', true),
            'course_x'            => $request->get('course_x', 50),
            'course_y'            => $request->get('course_y', 64),
            'course_font_size'    => $request->get('course_font_size', 18),
            'course_font_color'   => $request->get('course_font_color', '#2563EB'),
            'course_align'        => $request->get('course_align', 'center'),
            'course_bold'         => $request->boolean('course_bold', true),
            'show_cert_number'    => $request->boolean('show_cert_number', true),
            'cert_num_x'          => $request->get('cert_num_x', 50),
            'cert_num_y'          => $request->get('cert_num_y', 76),
            'cert_num_font_size'  => $request->get('cert_num_font_size', 11),
            'cert_num_font_color' => $request->get('cert_num_font_color', '#64748B'),
            'show_date'           => $request->boolean('show_date', true),
            'date_x'              => $request->get('date_x', 50),
            'date_y'              => $request->get('date_y', 82),
            'date_font_size'      => $request->get('date_font_size', 12),
            'date_font_color'     => $request->get('date_font_color', '#475569'),
        ]);

        // Background: ambil dari template tersimpan jika template_id ada,
        // lalu inject ke $tpl->background_image agar blade bisa resolve URL-nya
        if ($templateId = $request->get('template_id')) {
            $saved = CertificateTemplate::find($templateId);
            if ($saved?->background_image) {
                $tpl->background_image = $saved->background_image;
            }
        }

        // Dummy data
        $dummyCertificate = (object)['certificate_number' => 'SKOL-2025-000001'];
        $dummyUser        = (object)['name' => 'Nama Penerima Sertifikat'];
        $dummyCourse      = (object)[
            'title'      => 'Nama Kursus / Pelatihan yang Diikuti',
            'instructor' => (object)['name' => 'Nama Instruktur'],
        ];

        return view('pdf.certificate', [
            'certificate' => $dummyCertificate,
            'user'        => $dummyUser,
            'course'      => $dummyCourse,
            'issuedAt'    => now(),
            'template'    => $tpl,
            'isPreview'   => true,
        ]);
    }
}
