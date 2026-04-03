@extends('layouts.app')

@section('title', 'Créer un cours')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Créer un cours</h1>

    <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="5" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Niveau</label>
                <select name="level" class="w-full border rounded px-3 py-2">
                    <option value="beginner">Débutant</option>
                    <option value="intermediate">Intermédiaire</option>
                    <option value="advanced">Avancé</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Langue</label>
                <select name="language" class="w-full border rounded px-3 py-2">
                    <option value="fr">Français</option>
                    <option value="en">Anglais</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Prix (FCFA)</label>
                <input type="number" name="price" value="{{ old('price', 0) }}" step="0.01" min="0" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Miniature</label>
                <input type="file" name="thumbnail" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="published" value="1" class="mr-2"> Publier immédiatement
            </label>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Enregistrer</button>
        </div>
    </form>
</div>
@endsection