@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-display font-bold mb-4">{{ $lesson->title }}</h1>
    <p class="text-on-surface-variant mb-6">{{ $lesson->description }}</p>

    @if($lesson->type == 'video')
        @if($lesson->video_path)
            <div class="aspect-video rounded-2xl overflow-hidden shadow-lg mb-6">
                <video controls class="w-full h-full">
                    <source src="{{ asset('storage/'.$lesson->video_path) }}" type="video/mp4">
                    Votre navigateur ne supporte pas la vidéo.
                </video>
            </div>
        @elseif($lesson->video_url)
            @php
                // Convertir l'URL YouTube standard en URL embed
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
                <div class="aspect-video rounded-2xl overflow-hidden shadow-lg mb-6">
                    <iframe class="w-full h-full" 
                            src="{{ $embedUrl }}" 
                            title="YouTube video player" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
            @else
                <p class="text-red-600">Lien vidéo invalide.</p>
            @endif
        @endif
    @elseif($lesson->type == 'text')
        <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
            {!! $lesson->content !!}
        </div>
    @endif

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

    <form action="{{ route('lessons.complete', $lesson) }}" method="POST">
        @csrf
        <button type="submit" class="btn-primary w-full md:w-auto">
            <i class="fas fa-check-circle mr-2"></i> Marquer comme terminée
        </button>
    </form>
</div>
@endsection