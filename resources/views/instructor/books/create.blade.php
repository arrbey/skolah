@extends('layouts.instructor')

@section('title', 'Tambah Buku Baru')

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.books.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Buku Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Isi detail buku yang akan diterbitkan</p>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('instructor.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('instructor.books._form', ['book' => null])
    </form>
@endsection

@include('partials.tinymce')
