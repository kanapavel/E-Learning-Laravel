@extends('layouts.app')

@section('title', 'Modifier le quiz')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Modifier : {{ $quiz->title }}</h1>

    <form action="{{ route('instructor.courses.quizzes.update', [$quiz->course, $quiz]) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Titre</label>
            <input type="text" name="title" value="{{ old('title', $quiz->title) }}" class="w-full border rounded px-3 py-2" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $quiz->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Score de réussite (%)</label>
                <input type="number" name="pass_score" value="{{ old('pass_score', $quiz->pass_score) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Temps limite (minutes)</label>
                <input type="number" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Tentatives max</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Leçon associée</label>
                <select name="lesson_id" class="w-full border rounded px-3 py-2">
                    <option value="">-- Aucune --</option>
                    @foreach($quiz->course->lessons as $lesson)
                        <option value="{{ $lesson->id }}" @selected($quiz->lesson_id == $lesson->id)>{{ $lesson->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="show_answers" value="1" @checked($quiz->show_answers) class="mr-2"> Afficher les réponses après soumission
            </label>
        </div>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.edit', $quiz->course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Mettre à jour</button>
        </div>
    </form>

    <hr class="my-8">

    <h2 class="text-xl font-bold mb-4">Questions</h2>
    @if($quiz->questions->count())
        <ul class="space-y-4">
            @foreach($quiz->questions as $question)
                <li class="bg-gray-50 p-4 rounded">
                    <div class="flex justify-between">
                        <span class="font-semibold">{{ $question->question_text }}</span>
                        <div>
                            <a href="{{ route('instructor.questions.edit', [$quiz, $question]) }}" class="text-indigo-600 text-sm mr-2">Modifier</a>
                            <form action="{{ route('instructor.questions.destroy', [$quiz, $question]) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 text-sm" onclick="return confirm('Supprimer cette question ?')">Supprimer</button>
                            </form>
                        </div>
                    </div>
                    <p class="text-gray-500 text-sm">Points : {{ $question->points }} | Type : {{ $question->type }}</p>
                </li>
            @endforeach
        </ul>
    @else
        <p>Aucune question pour ce quiz.</p>
    @endif
    <a href="{{ route('instructor.questions.create', $quiz) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block mt-4">Ajouter une question</a>
</div>
@endsection