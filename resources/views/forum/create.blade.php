@extends('layouts.app')

@section('title', 'Nouveau sujet')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Nouveau sujet dans : {{ $course->title }}</h1>

    <form action="{{ route('courses.forum.store', $course) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Message</label>
            <textarea name="body" rows="5" class="w-full border rounded px-3 py-2" required></textarea>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="{{ route('courses.forum.index', $course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Publier</button>
        </div>
    </form>
</div>
@endsection