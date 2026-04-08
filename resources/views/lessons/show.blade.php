@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-display font-bold mb-4">{{ $lesson->title }}</h1>
    <p class="text-on-surface-variant mb-6">{{ $lesson->description }}</p>

    {{-- Vidéo --}}
    @if(in_array($lesson->type, ['video', 'mixed']) && ($lesson->video_path || $lesson->video_url))
        <div class="aspect-video rounded-2xl overflow-hidden shadow-lg mb-6 bg-black">
            @if($lesson->video_path)
                <video controls class="w-full h-full object-contain">
                    <source src="{{ asset('storage/'.$lesson->video_path) }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo.
                </video>
            @elseif($lesson->video_url)
                @php
                    $videoId = null;
                    $url = $lesson->video_url;
                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
                        $videoId = $matches[1];
                    }
                    $embedUrl = $videoId ? "https://www.youtube-nocookie.com/embed/{$videoId}" : null;
                @endphp
                @if($embedUrl)
                    <iframe class="w-full h-full" src="{{ $embedUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                @else
                    <p class="text-red-600 p-4">Lien vidéo invalide.</p>
                @endif
            @endif
        </div>
    @endif

    {{-- Contenu texte structuré --}}
    @if(in_array($lesson->type, ['text', 'mixed']) && $lesson->content)
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 lesson-content">
            {!! $lesson->content !!}
        </div>
    @endif

    {{-- Quiz --}}
    @if($lesson->quiz)
        <div class="bg-primary-fixed/20 rounded-2xl p-5 mb-6">
            <div class="flex flex-wrap justify-between items-center gap-3">
                <div>
                    <p class="font-semibold">Quiz disponible</p>
                    <p class="text-sm text-on-surface-variant">Testez vos connaissances sur cette leçon.</p>
                </div>
                <a href="{{ route('quizzes.take', $lesson->quiz) }}" class="btn-primary py-2 px-5 text-sm">Passer le quiz</a>
            </div>
        </div>
    @endif

    {{-- Ressources --}}
    @if($lesson->resources->count())
        <div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
            <h3 class="font-display font-semibold mb-3">Ressources</h3>
            <ul class="divide-y divide-outline/20">
                @foreach($lesson->resources as $resource)
                    <li class="py-2 flex justify-between items-center">
                        <span>{{ $resource->title }}</span>
                        <a href="{{ $resource->url }}" class="text-primary hover:underline text-sm" target="_blank">
                            Télécharger ({{ $resource->formatted_size }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Marquer terminée --}}
    <form action="{{ route('lessons.complete', $lesson) }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary w-full md:w-auto">
            <i class="fas fa-check-circle mr-2"></i> Marquer comme terminée
        </button>
    </form>
</div>

{{-- Styles CSS pour structurer le contenu HTML (fallback si Typography non installé) --}}
<style>
    .lesson-content {
        font-family: 'Inter', sans-serif;
        line-height: 1.6;
        color: #191c1e;
    }
    .lesson-content h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-family: 'Manrope', sans-serif;
    }
    .lesson-content h2 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 1.25rem;
        margin-bottom: 0.75rem;
        font-family: 'Manrope', sans-serif;
    }
    .lesson-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    .lesson-content p {
        margin-bottom: 1rem;
    }
    .lesson-content ul, .lesson-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .lesson-content li {
        margin-bottom: 0.25rem;
    }
    .lesson-content pre {
        background-color: #f3f4f6;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 1rem;
        font-family: monospace;
        font-size: 0.875rem;
    }
    .lesson-content code {
        background-color: #eef0f4;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.875rem;
    }
    .lesson-content hr {
        margin: 1.5rem 0;
        border-color: #d1d4e0;
    }
    .lesson-content blockquote {
        border-left: 4px solid #0056d2;
        padding-left: 1rem;
        color: #424654;
        margin: 1rem 0;
    }
    .lesson-content strong {
        font-weight: 600;
    }
</style>
@endsection