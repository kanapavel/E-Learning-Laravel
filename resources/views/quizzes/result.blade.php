@extends('layouts.app')

@section('title', 'Résultat du quiz')

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-2">Résultat : {{ $submission->quiz->title }}</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-8 text-center">
        <p class="text-4xl font-bold text-indigo-600">{{ $submission->score }}%</p>
        @if($submission->passed)
            <p class="text-green-600 font-semibold mt-2">Félicitations ! Vous avez réussi.</p>
        @else
            <p class="text-red-600 font-semibold mt-2">Vous n'avez pas atteint le score requis.</p>
        @endif
    </div>

    @if($submission->quiz->show_answers)
        <h2 class="text-xl font-bold mb-4">Corrigé</h2>
        @foreach($submission->quiz->questions as $question)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <p class="font-semibold">{{ $question->question_text }}</p>
                @php
                    $userAnswer = $submission->submissionAnswers->where('question_id', $question->id)->first();
                @endphp

                @if($question->type == 'single')
                    <div class="mt-2 space-y-1">
                        @foreach($question->answers as $answer)
                            <div class="flex items-center">
                                <span class="w-6">{{ $userAnswer && $userAnswer->answer_id == $answer->id ? '✓' : ' ' }}</span>
                                <span class="@if($answer->is_correct) text-green-600 font-semibold @endif">{{ $answer->answer_text }}</span>
                                @if($answer->is_correct) <span class="ml-2 text-green-500">(Bonne réponse)</span> @endif
                            </div>
                        @endforeach
                    </div>
                @elseif($question->type == 'multiple')
                    {{-- À implémenter si nécessaire --}}
                @elseif($question->type == 'true_false')
                    {{-- À implémenter --}}
                @endif

                @if($question->explanation)
                    <div class="mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded">{{ $question->explanation }}</div>
                @endif
            </div>
        @endforeach
    @endif

    <a href="{{ route('courses.show', $submission->quiz->course) }}" class="inline-block bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded">Retour au cours</a>
</div>
@endsection