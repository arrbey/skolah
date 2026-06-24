<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderBannerRequest;
use App\Http\Requests\Admin\StoreBannerRequest;
use App\Http\Requests\Admin\UpdateBannerRequest;
use App\Models\Banner;
use App\Services\MinioStorageService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::ordered()->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = app(MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        $data['order'] = (Banner::max('order') ?? 0) + 1;

        Banner::create($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($banner->image) {
                app(MinioStorageService::class)->delete($banner->image);
            }
            $data['image'] = app(MinioStorageService::class)->uploadBanner($request->file('image'));
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            app(MinioStorageService::class)->delete($banner->image);
        }

        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus.');
    }

    public function toggleActive(Banner $banner)
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        $label = $banner->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Banner berhasil {$label}.");
    }

    public function reorder(ReorderBannerRequest $request)
    {
        foreach ($request->validated('order') as $index => $id) {
            Banner::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
