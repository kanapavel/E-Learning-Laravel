@extends('layouts.app')

@section('title', 'Modifier le cours')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Modifier : {{ $course->title }}</h1>

    <form action="{{ route('instructor.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title', $course->title) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="5" class="w-full border rounded px-3 py-2">{{ old('description', $course->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Niveau</label>
                <select name="level" class="w-full border rounded px-3 py-2">
                    <option value="beginner" @selected($course->level == 'beginner')>Débutant</option>
                    <option value="intermediate" @selected($course->level == 'intermediate')>Intermédiaire</option>
                    <option value="advanced" @selected($course->level == 'advanced')>Avancé</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Langue</label>
                <select name="language" class="w-full border rounded px-3 py-2">
                    <option value="fr" @selected($course->language == 'fr')>Français</option>
                    <option value="en" @selected($course->language == 'en')>Anglais</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Prix (FCFA)</label>
                <input type="number" name="price" value="{{ old('price', $course->price) }}" step="0.01" min="0" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Miniature</label>
                <input type="file" name="thumbnail" accept="image/*" class="w-full border rounded px-3 py-2">
                @if($course->thumbnail)
                    <div class="mt-2">
                        <img src="{{ $course->thumbnail_url }}" class="h-20 rounded">
                    </div>
                @endif
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="published" value="1" @checked($course->published) class="mr-2"> Publié
            </label>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Mettre à jour</button>
        </div>
    </form>

    <hr class="my-8">

    <h2 class="text-xl font-bold mb-4">Chapitres</h2>
    @foreach($course->chapters as $chapter)
        <div class="bg-gray-50 rounded p-4 mb-4">
            <div class="flex justify-between">
                <h3 class="font-semibold">{{ $chapter->title }}</h3>
                <div>
                    <a href="{{ route('instructor.courses.chapters.edit', [$course, $chapter]) }}" class="text-indigo-600 text-sm mr-2">Modifier</a>
                    <form action="{{ route('instructor.courses.chapters.destroy', [$course, $chapter]) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 text-sm" onclick="return confirm('Supprimer ce chapitre ?')">Supprimer</button>
                    </form>
                </div>
            </div>
            <p class="text-gray-600 text-sm">{{ $chapter->description }}</p>
            <div class="mt-2">
                <a href="{{ route('instructor.chapters.lessons.create', $chapter) }}" class="text-green-600 text-sm">Ajouter une leçon</a>
            </div>
            @if($chapter->lessons->count())
                <ul class="mt-2 space-y-1">
                    @foreach($chapter->lessons as $lesson)
                        <li class="flex justify-between text-sm">
                            <span>{{ $lesson->title }}</span>
                            <div>
                                <a href="{{ route('instructor.chapters.lessons.edit', [$chapter, $lesson]) }}" class="text-indigo-600 mr-2">Modifier</a>
                                <form action="{{ route('instructor.chapters.lessons.destroy', [$chapter, $lesson]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600" onclick="return confirm('Supprimer cette leçon ?')">Supprimer</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endforeach
    <a href="{{ route('instructor.courses.chapters.create', $course) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block">Ajouter un chapitre</a>

    <hr class="my-8">

    <h2 class="text-xl font-bold mb-4">Quiz du cours</h2>
    @if($course->quizzes->count())
        <ul>
            @foreach($course->quizzes as $quiz)
                <li class="flex justify-between">
                    <span>{{ $quiz->title }}</span>
                    <div>
                        <a href="{{ route('instructor.courses.quizzes.edit', [$course, $quiz]) }}" class="text-indigo-600">Modifier</a>
                        <form action="{{ route('instructor.courses.quizzes.destroy', [$course, $quiz]) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Supprimer ce quiz ?')">Supprimer</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p>Aucun quiz pour ce cours.</p>
    @endif
    <a href="{{ route('instructor.courses.quizzes.create', $course) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block mt-2">Ajouter un quiz</a>
</div>
@endsection