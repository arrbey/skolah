@extends('layouts.instructor')

@section('title', 'Edit Bootcamp — ' . $bootcamp->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.bootcamps.index') }}" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
        <div>
            <h1 class="text-lg font-bold text-gray-900">Edit Bootcamp</h1>
            <p class="text-sm text-gray-500 truncate max-w-xs">{{ $bootcamp->title }}</p>
        </div>
    </div>
@endsection

@section('content')
<form method="POST" action="{{ route('instructor.bootcamps.update', $bootcamp->id) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')
    @include('instructor.bootcamps._form', ['bootcamp' => $bootcamp])
</form>
@endsection

@include('partials.tinymce')
