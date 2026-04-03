@extends('layouts.app')

@section('title', 'Ajouter une leçon')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Ajouter une leçon au chapitre : {{ $chapter->title }}</h1>

    <form action="{{ route('instructor.chapters.lessons.store', $chapter) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Type de leçon</label>
            <select name="type" class="w-full border rounded px-3 py-2">
                <option value="video">Vidéo</option>
                <option value="text">Texte</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Fichier vidéo (optionnel)</label>
            <input type="file" name="video_file" accept="video/*" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">URL vidéo (YouTube, Vimeo)</label>
            <input type="url" name="video_url" value="{{ old('video_url') }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Contenu texte (si type texte)</label>
            <textarea name="content" rows="5" class="w-full border rounded px-3 py-2">{{ old('content') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Durée (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Ordre</label>
                <input type="number" name="order" value="{{ old('order', $chapter->lessons->count() + 1) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_free" value="1" class="mr-2"> Leçon gratuite (prévisualisable)
            </label>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $chapter->course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Enregistrer</button>
        </div>
    </form>
</div>
@endsection