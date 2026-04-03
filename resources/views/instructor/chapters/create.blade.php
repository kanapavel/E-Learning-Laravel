@extends('layouts.app')

@section('title', 'Ajouter un chapitre')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Ajouter un chapitre à : {{ $course->title }}</h1>

    <form action="{{ route('instructor.courses.chapters.store', $course) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre du chapitre</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Ordre</label>
            <input type="number" name="order" value="{{ old('order', $course->chapters->count() + 1) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Enregistrer</button>
        </div>
    </form>
</div>
@endsection