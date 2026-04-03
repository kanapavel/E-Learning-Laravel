@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="container mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold mb-2">{{ $quiz->title }}</h1>
    <p class="text-gray-600 mb-6">{{ $quiz->description }}</p>

    <form action="{{ route('quizzes.submit', $quiz) }}" method="POST">
        @csrf
        @foreach($quiz->questions as $question)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex justify-between">
                    <p class="font-semibold">{{ $question->question_text }}</p>
                    <span class="text-sm text-gray-500">{{ $question->points }} pt(s)</span>
                </div>

                @if($question->type == 'single')
                    <div class="mt-4 space-y-2">
                        @foreach($question->answers as $answer)
                            <label class="flex items-center">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="mr-2" required>
                                <span>{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($question->type == 'multiple')
                    <div class="mt-4 space-y-2">
                        @foreach($question->answers as $answer)
                            <label class="flex items-center">
                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $answer->id }}" class="mr-2">
                                <span>{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif($question->type == 'true_false')
                    <div class="mt-4 space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="answers[{{ $question->id }}]" value="true" class="mr-2" required> Vrai
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="answers[{{ $question->id }}]" value="false" class="mr-2" required> Faux
                        </label>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded">Soumettre</button>
        </div>
    </form>
</div>
@endsection