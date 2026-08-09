@extends('layouts.app')

@section('title', 'Sessions en direct')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

    {{-- EN-TÊTE AVEC BOUTON À DROITE --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight">Sessions en direct</h1>
            <p class="text-sm text-on-surface-variant mt-1">{{ $course->title }}</p>
        </div>
        <a href="{{ route('instructor.courses.live.create', $course) }}" 
           class="inline-flex items-center gap-2 px-5 py-2 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition-all shadow-md font-medium whitespace-nowrap">
            <i class="fas fa-plus-circle"></i> Programmer une session
        </a>
    </div>

    {{-- CARTES STATISTIQUES EN GRILLE 3 COLONNES RESPONSIVE --}}
    @php
        $liveCount = $sessions->where('status', 'live')->count();
        $scheduledCount = $sessions->where('status', 'scheduled')->count();
        $endedCount = $sessions->where('status', 'ended')->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        {{-- En direct --}}
        <div class="bg-white rounded-2xl border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">En direct</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $liveCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                <i class="fas fa-broadcast text-green-600 text-xl"></i>
            </div>
        </div>

        {{-- Programmées --}}
        <div class="bg-white rounded-2xl border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Programmées</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $scheduledCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                <i class="fas fa-calendar-plus text-blue-600 text-xl"></i>
            </div>
        </div>

        {{-- Terminées --}}
        <div class="bg-white rounded-2xl border border-outline/20 p-5 flex items-center justify-between hover:shadow-md transition">
            <div>
                <p class="text-xs font-medium text-on-surface-variant uppercase tracking-wide">Terminées</p>
                <p class="text-3xl font-bold text-gray-500 mt-1">{{ $endedCount }}</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-check-circle text-gray-500 text-xl"></i>
            </div>
        </div>
    </div>

    {{-- GRILLE DES SESSIONS (flex avec gaps) --}}
    @if($sessions->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($sessions as $session)
                <div class="group bg-white rounded-2xl border border-outline/20 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden flex flex-col">
                    {{-- Miniature --}}
                    <div class="relative h-40 overflow-hidden bg-surface-low">
                        @if($session->thumbnail)
                            <img src="{{ asset('storage/'.$session->thumbnail) }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                 alt="{{ $session->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-surface-low">
                                <i class="fas fa-video text-4xl text-on-surface-variant/20"></i>
                            </div>
                        @endif
                        {{-- Badge statut en overlay --}}
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium shadow-sm
                                @if($session->status == 'scheduled') bg-blue-500 text-white
                                @elseif($session->status == 'live') bg-red-500 text-white animate-pulse
                                @elseif($session->status == 'ended') bg-gray-500 text-white
                                @else bg-gray-400 text-white @endif">
                                @if($session->status == 'live')
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                @endif
                                {{ ucfirst($session->status) }}
                            </span>
                        </div>
                        @if($session->status == 'live')
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-600 text-white uppercase tracking-wider animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    live
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Contenu --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-display font-semibold text-base text-on-surface line-clamp-1">{{ $session->title }}</h3>
                        
                        @if($session->description)
                            <p class="text-xs text-on-surface-variant mt-1 line-clamp-2 flex-1">{{ $session->description }}</p>
                        @endif

                        {{-- Métadonnées --}}
                        <div class="mt-3 space-y-1 text-xs text-on-surface-variant">
                            @if($session->scheduled_at)
                                <div><i class="far fa-calendar-alt w-4"></i> {{ $session->scheduled_at->format('d/m/Y H:i') }}</div>
                            @endif
                            @if($session->started_at)
                                <div><i class="fas fa-play-circle w-4"></i> Démarré {{ $session->started_at->diffForHumans() }}</div>
                            @endif
                            @if($session->ended_at)
                                <div><i class="fas fa-stop-circle w-4"></i> Terminé {{ $session->ended_at->diffForHumans() }}</div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 pt-3 border-t border-outline/10 flex flex-wrap items-center gap-1">
                            @if($session->status == 'scheduled')
                                <a href="{{ route('instructor.courses.live.edit', [$course, $session]) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1 text-xs text-primary hover:bg-primary-fixed rounded-lg transition">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <form action="{{ route('instructor.courses.live.start', [$course, $session]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 text-xs text-green-600 hover:bg-green-50 rounded-lg transition">
                                        <i class="fas fa-play"></i> Démarrer
                                    </button>
                                </form>
                            @endif

                            @if($session->status == 'live')
                                <a href="{{ route('live.show', ['live' => $session]) }}" target="_blank" 
                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs text-primary hover:bg-primary-fixed rounded-lg transition">
                                    <i class="fas fa-eye"></i> Voir
                                </a>
                                <form action="{{ route('instructor.courses.live.end', [$course, $session]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 text-xs text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="fas fa-stop"></i> Terminer
                                    </button>
                                </form>
                            @endif

                            @if(!in_array($session->status, ['live', 'ended']))
                                <form action="{{ route('instructor.courses.live.destroy', [$course, $session]) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 text-xs text-red-500 hover:bg-red-50 rounded-lg transition" 
                                            onclick="return confirm('Supprimer cette session ?')">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-10 flex justify-center">
            {{ $sessions->links() }}
        </div>

    @else
        {{-- État vide --}}
        <div class="text-center py-16 bg-white rounded-2xl border border-outline/20 shadow-sm">
            <div class="w-16 h-16 mx-auto bg-primary-fixed/30 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-video text-3xl text-primary/60"></i>
            </div>
            <h3 class="text-xl font-display font-semibold text-on-surface">Aucune session programmée</h3>
            <p class="text-sm text-on-surface-variant mt-2 max-w-sm mx-auto">
                Commencez à interagir avec vos étudiants en programmant votre première session en direct.
            </p>
        </div>
    @endif
</div>
@endsection