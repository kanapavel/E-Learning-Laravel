@extends('layouts.app')

@section('title', $thread->title)

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">{{ $thread->title }}</h1>
        <a href="{{ route('courses.forum.index', $course) }}" class="text-indigo-600 hover:underline">← Retour au forum</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between">
            <p class="font-semibold">{{ $thread->author->name }}</p>
            <span class="text-sm text-gray-500">{{ $thread->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <p class="mt-4">{{ $thread->body }}</p>
    </div>

    @foreach($posts as $post)
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <div class="flex justify-between">
                <p class="font-semibold">{{ $post->author->name }}</p>
                <div>
                    @if($post->is_solution)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Solution</span>
                    @endif
                    <span class="text-sm text-gray-500 ml-2">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
            <p class="mt-4">{{ $post->body }}</p>
            @if($thread->user_id == auth()->id() && !$post->is_solution)
                <div class="mt-4 text-right">
                    <form action="{{ route('courses.forum.posts.store', [$course, $thread]) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm">Marquer comme solution</button>
                    </form>
                </div>
            @endif
        </div>
    @endforeach

    {{ $posts->links() }}

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="font-bold mb-4">Répondre</h3>
        <form action="{{ route('courses.forum.posts.store', $thread) }}" method="POST">
            @csrf
            <textarea name="body" rows="3" class="w-full border rounded px-3 py-2 mb-4" required></textarea>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">Envoyer</button>
        </form>
    </div>
</div>
@endsection