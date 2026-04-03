@extends('layouts.app')

@section('title', 'Modifier une leçon')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Modifier : {{ $lesson->title }}</h1>

    <form action="{{ route('instructor.chapters.lessons.update', [$chapter, $lesson]) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title', $lesson->title) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $lesson->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Type</label>
            <select name="type" class="w-full border rounded px-3 py-2">
                <option value="video" @selected($lesson->type == 'video')>Vidéo</option>
                <option value="text" @selected($lesson->type == 'text')>Texte</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Nouvelle vidéo (optionnel)</label>
            <input type="file" name="video_file" accept="video/*" class="w-full border rounded px-3 py-2">
            @if($lesson->video_path)
                <div class="mt-1 text-sm text-gray-500">Vidéo existante : {{ basename($lesson->video_path) }}</div>
            @endif
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">URL vidéo</label>
            <input type="url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Contenu texte</label>
            <textarea name="content" rows="5" class="w-full border rounded px-3 py-2">{{ old('content', $lesson->content) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Durée (minutes)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Ordre</label>
                <input type="number" name="order" value="{{ old('order', $lesson->order) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_free" value="1" @checked($lesson->is_free) class="mr-2"> Leçon gratuite
            </label>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $chapter->course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Mettre à jour</button>
        </div>
    </form>
</div>
@endsection