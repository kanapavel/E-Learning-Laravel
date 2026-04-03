@extends('layouts.app')

@section('title', 'Ajouter une question')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">Ajouter une question au quiz : {{ $quiz->title }}</h1>

    <form action="{{ route('instructor.questions.store', $quiz) }}" method="POST" class="bg-white shadow rounded p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Texte de la question</label>
            <textarea name="question_text" rows="3" class="w-full border rounded px-3 py-2" required>{{ old('question_text') }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="single">Choix unique</option>
                    <option value="multiple">Choix multiples</option>
                    <option value="true_false">Vrai / Faux</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Points</label>
                <input type="number" name="points" value="{{ old('points', 1) }}" class="w-full border rounded px-3 py-2" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Explication (optionnel)</label>
            <textarea name="explanation" rows="2" class="w-full border rounded px-3 py-2">{{ old('explanation') }}</textarea>
        </div>

        <h3 class="font-bold mb-2">Réponses</h3>
        <div id="answers-container">
            <div class="answer-item grid grid-cols-[1fr,auto] gap-2 mb-2">
                <input type="text" name="answers[0][text]" class="w-full border rounded px-3 py-2" placeholder="Texte de la réponse" required>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="answers[0][is_correct]" value="1" class="mr-1"> Correcte
                </label>
            </div>
        </div>
        <button type="button" id="add-answer" class="text-indigo-600 text-sm mb-4">+ Ajouter une réponse</button>

        <div class="flex justify-end space-x-2">
            <a href="{{ route('instructor.courses.quizzes.edit', [$quiz->course, $quiz]) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Annuler</a>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Enregistrer</button>
        </div>
    </form>
</div>

<script>
    let answerCount = 1;
    document.getElementById('add-answer').addEventListener('click', function() {
        const container = document.getElementById('answers-container');
        const div = document.createElement('div');
        div.className = 'answer-item grid grid-cols-[1fr,auto] gap-2 mb-2';
        div.innerHTML = `
            <input type="text" name="answers[${answerCount}][text]" class="w-full border rounded px-3 py-2" placeholder="Texte de la réponse" required>
            <label class="inline-flex items-center">
                <input type="checkbox" name="answers[${answerCount}][is_correct]" value="1" class="mr-1"> Correcte
            </label>
        `;
        container.appendChild(div);
        answerCount++;
    });
</script>
@endsection