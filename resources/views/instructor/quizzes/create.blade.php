@extends('layouts.app')

@section('title', 'Créer un quiz')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Créer un quiz pour : {{ $course->title }}</h1>

    <form action="{{ route('instructor.courses.quizzes.store', $course) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Score de réussite (%)</label>
                <input type="number" name="pass_score" value="{{ old('pass_score', 70) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Temps limite (minutes)</label>
                <input type="number" name="time_limit" value="{{ old('time_limit') }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Tentatives max</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', 3) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Leçon associée (optionnel)</label>
                <select name="lesson_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Aucune (quiz de cours) --</option>
                    @foreach($lessons as $lesson)
                        <option value="{{ $lesson->id }}" @selected(old('lesson_id') == $lesson->id)>{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="show_answers" value="1" @checked(old('show_answers')) class="mr-2"> Afficher les réponses après soumission
            </label>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Créer le quiz</button>
        </div>
    </form>
</div>
@endsection