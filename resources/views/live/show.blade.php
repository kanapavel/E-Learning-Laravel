@extends('layouts.app')

@section('title', $session->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-2xl font-display font-bold mb-2">{{ $session->title }}</h1>
    <p class="text-on-surface-variant mb-6">{{ $session->description }}</p>

    @if($session->status == 'live')
        <div class="aspect-w-16 aspect-h-9 bg-black rounded-2xl overflow-hidden mb-4">
            @if($session->stream_embed)
                {!! $session->stream_embed !!}
            @elseif($session->stream_url)
                <iframe src="{{ $session->stream_url }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
            @else
                <div class="flex items-center justify-center h-full text-white">
                    <p>Le flux vidéo n'est pas encore disponible.</p>
                </div>
            @endif
        </div>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <p class="text-green-700 font-semibold">🔴 En direct maintenant !</p>
        </div>
    @elseif($session->status == 'scheduled')
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <p>📅 Cette session est programmée pour le {{ $session->scheduled_at->format('d/m/Y H:i') }}.</p>
        </div>
    @else
        <div class="bg-gray-50 border-l-4 border-gray-400 p-4 rounded">
            <p>⏹️ Cette session est terminée.</p>
        </div>
    @endif

    <div class="mt-6">
        {{-- 🔽 Lien dynamique : revient à la page précédente ou vers le cours --}}
        <a href="{{ url()->previous() ?? route('courses.show', $session->course_id) }}" class="btn-secondary inline-flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>
@endsection