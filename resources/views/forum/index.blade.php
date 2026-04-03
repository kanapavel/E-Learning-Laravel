@extends('layouts.app')

@section('title', 'Forum - '.$course->title)

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Forum : {{ $course->title }}</h1>
        <a href="{{ route('courses.forum.create', $course) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">Nouveau sujet</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y">
            @forelse($threads as $thread)
                <a href="{{ route('courses.forum.show', [$course, $thread]) }}" class="block p-4 hover:bg-gray-50">
                    <div class="flex justify-between">
                        <h3 class="font-semibold text-indigo-600">{{ $thread->title }}</h3>
                        <span class="text-sm text-gray-500">{{ $thread->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">par {{ $thread->author->name }}</p>
                    <div class="text-xs text-gray-400 mt-2">
                        Réponses : {{ $thread->posts->count() }} | Vues : {{ $thread->views }}
                    </div>
                </a>
            @empty
                <p class="p-4 text-center text-gray-500">Aucun sujet pour le moment. Soyez le premier à poster !</p>
            @endforelse
        </div>
    </div>
    <div class="mt-6">
        {{ $threads->links() }}
    </div>
</div>
@endsection