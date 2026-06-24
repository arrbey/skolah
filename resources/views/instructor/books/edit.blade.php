@extends('layouts.instructor')

@section('title', 'Edit Buku — ' . $book->title)

@section('page-header')
    <div class="flex items-center gap-3">
        <a href="{{ route('instructor.books.index') }}" class="p-2 rounded-lg hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Buku</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $book->title }}</p>
        </div>
    </div>
@endsection

@section('content')
    {{-- Quick stats --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Terjual</p>
            <p class="text-xl font-bold text-primary-600 mt-1">{{ $book->orders_count ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Stok</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $book->stock ?? '∞' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Halaman</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $book->pages ?? '-' }}</p>
        </div>
    </div>

    <form action="{{ route('instructor.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('instructor.books._form', ['book' => $book])
    </form>
@endsection

@include('partials.tinymce')
