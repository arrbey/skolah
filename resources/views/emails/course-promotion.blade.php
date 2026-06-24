@extends('emails.layouts.base')

@section('title', 'Kursus Baru' . ' — ' . \App\Models\Setting::get('site_name', '' . \App\Models\Setting::get('site_name', 'Skolah.com') . ''))

@section('content')
<div style="text-align: center; margin-bottom: 24px;">
    <div style="font-size: 48px; margin-bottom: 8px;">🎓</div>
    <h2 style="font-size: 22px; color: #0F172A; margin: 12px 0 4px;">
        Kursus Baru Tersedia!
    </h2>
    <p style="color: #64748B; font-size: 14px; margin: 0; line-height: 1.6;">
        Halo <strong>{{ $user->name }}</strong>, ada kursus baru yang mungkin cocok untukmu!
    </p>
</div>

<hr class="divider">

{{-- Course Card --}}
<div style="background: linear-gradient(135deg, #2563EB, #38BDF8); border-radius: 16px; padding: 24px; margin: 16px 0; text-align: center; color: #FFFFFF;">
    <p style="font-size: 12px; text-transform: uppercase; letter-spacing: 2px; margin: 0 0 8px; opacity: 0.85;">Kursus Baru</p>
    <h3 style="font-size: 24px; font-weight: 800; margin: 0 0 12px; line-height: 1.3;">{{ $course->title }}</h3>

    @if($course->instructor)
        <p style="font-size: 13px; margin: 0 0 12px; opacity: 0.9;">
            👨‍🏫 oleh <strong>{{ $course->instructor->name }}</strong>
        </p>
    @endif

    <div style="background: rgba(255,255,255,0.2); display: inline-block; padding: 10px 28px; border-radius: 10px; margin-bottom: 8px;">
        @if($course->has_discount)
            <span style="font-size: 14px; text-decoration: line-through; opacity: 0.7; margin-right: 8px;">{{ rupiah($course->price) }}</span>
            <span style="font-size: 24px; font-weight: 800;">{{ rupiah($course->effective_price) }}</span>
        @elseif($course->price === 0)
            <span style="font-size: 24px; font-weight: 800;">GRATIS</span>
        @else
            <span style="font-size: 24px; font-weight: 800;">{{ rupiah($course->price) }}</span>
        @endif
    </div>

    @if($course->level)
        <p style="font-size: 12px; margin: 8px 0 0; opacity: 0.85;">
            📊 Level: {{ ucfirst($course->level) }}
        </p>
    @endif
</div>

{{-- Custom Message --}}
@if($customMessage)
<div style="background: #F8FAFC; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 14px; color: #1E293B; margin: 0; line-height: 1.6;">
        {{ $customMessage }}
    </p>
</div>
@endif

{{-- Course Details --}}
<div class="info-box">
    <table>
        @if($course->category)
        <tr>
            <td class="info-label">Kategori</td>
            <td class="info-value">{{ $course->category->name }}</td>
        </tr>
        @endif
        <tr>
            <td class="info-label">Level</td>
            <td class="info-value">{{ ucfirst($course->level ?? 'Semua Level') }}</td>
        </tr>
        <tr>
            <td class="info-label">Harga</td>
            <td class="info-value">
                @if($course->has_discount)
                    <span style="text-decoration: line-through; color: #94A3B8;">{{ rupiah($course->price) }}</span>
                    → <strong style="color: #10B981;">{{ rupiah($course->effective_price) }}</strong>
                @else
                    {{ $course->price_formatted }}
                @endif
            </td>
        </tr>
        @if($course->sections_count ?? false)
        <tr>
            <td class="info-label">Materi</td>
            <td class="info-value">{{ $course->sections_count }} bab</td>
        </tr>
        @endif
    </table>
</div>

{{-- Description --}}
@if($course->description)
<div style="background: #F0F9FF; border-radius: 12px; padding: 16px; margin: 16px 0;">
    <p style="font-size: 13px; color: #1E293B; margin: 0; line-height: 1.6;">
        {{ Str::limit(strip_tags($course->description), 250) }}
    </p>
</div>
@endif

{{-- CTA --}}
<div style="text-align: center; margin: 24px 0;">
    <a href="{{ url('/courses/' . $course->slug) }}" class="cta-button">
        🎓 Lihat Kursus →
    </a>
</div>

<p style="font-size: 12px; color: #94A3B8; text-align: center; line-height: 1.5;">
    *Harga dan ketersediaan dapat berubah sewaktu-waktu.
</p>
@endsection
