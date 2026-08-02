@extends('layouts.app')

@section('title', $live->title)

@section('content')
@php
    $statusMap = [
        'live'      => ['label' => 'EN DIRECT',  'dot' => '#ef4444', 'pulse' => true],
        'scheduled' => ['label' => 'Programmée', 'dot' => '#3b82f6', 'pulse' => false],
        'ended'     => ['label' => 'Terminée',   'dot' => '#9ca3af', 'pulse' => false],
    ];
    $status = $statusMap[$live->status] ?? $statusMap['ended'];

    $videoId  = null;
    $embedSrc = null;
    if ($live->stream_url) {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $live->stream_url, $m)) {
                $videoId  = $m[1];
                $embedSrc = 'https://www.youtube-nocookie.com/embed/' . $videoId
                          . '?autoplay=1&rel=0&modestbranding=1&playsinline=1';
                break;
            }
        }
        if (!$videoId) $embedSrc = $live->stream_url;
    }

    $channelName  = $live->instructor->name ?? 'Instructeur';
    $avatarUrl    = 'https://ui-avatars.com/api/?name=' . urlencode($channelName) . '&background=0040a1&color=fff&size=64';
    $isLive       = $live->status === 'live';
    $isInstructor = auth()->check() && auth()->user()->isInstructor() && auth()->id() == $live->instructor_id;
@endphp

<style>
/* ── Reset essentiel ── */
*,*::before,*::after{box-sizing:border-box;}

/* ── Layout ── */
.lv-wrap{max-width:1200px;margin:0 auto;padding:1.5rem 1.25rem 3rem;}
.lv-grid{display:grid;grid-template-columns:1fr 340px;gap:1.5rem;align-items:start;}
.lv-left{display:flex;flex-direction:column;gap:1rem;}
.lv-right{display:flex;flex-direction:column;gap:1rem;position:sticky;top:80px;}

/* ── Player ── */
.player-shell{
    background:#0a0a0a;
    border-radius:1rem;
    overflow:hidden;
    box-shadow:0 20px 60px rgba(0,0,0,.5);
    position:relative;
}

/* Conteneur 16:9 */
.player-stage{
    position:relative;
    width:100%;
    padding-top:56.25%;
    background:#0f0f0f;
    overflow:hidden;
}

/* Couche générique : occupe tout le stage */
.player-layer{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    border:none;
}

/* ── Couches par z-index ──
   z=1  : fond skeleton / placeholder
   z=2  : iframe YouTube / embed
   z=5  : flux caméra (VIDEO)
   z=10 : badge LIVE
   z=20 : overlay "activer caméra"
   z=30 : indicateur + bouton stop
   ─────────────────────────────── */

#layer-skeleton  { z-index:1; }
#layer-youtube   { z-index:2; }
#layer-embed     { z-index:2; }
#layer-camera    { z-index:5; display:none; object-fit:cover; background:#000; }
#layer-live-badge{ z-index:10; position:absolute; top:.75rem; left:.75rem; pointer-events:none; }
#layer-cam-overlay{
    z-index:20;
    background:rgba(0,0,0,.7);
    backdrop-filter:blur(6px);
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:.75rem;
    color:white;
    padding:1.5rem;
}
#layer-cam-indicator{
    z-index:30;
    position:absolute;
    top:.75rem;
    right:.75rem;
    display:none;
}
#btn-stop-cam{
    z-index:30;
    position:absolute;
    bottom:4.5rem;
    right:1rem;
    display:none;
    background:rgba(239,68,68,.9);
    color:white;
    border:none;
    padding:.4rem .875rem;
    border-radius:.5rem;
    font-size:.75rem;
    font-weight:600;
    cursor:pointer;
    backdrop-filter:blur(4px);
    transition:background .15s;
}
#btn-stop-cam:hover{background:#dc2626;}

/* Skeleton shimmer */
.skeleton-bg{
    background:linear-gradient(90deg,#1a1a1a 25%,#2b2b2b 50%,#1a1a1a 75%);
    background-size:400% 100%;
    animation:shimmer 1.8s ease-in-out infinite;
}
@keyframes shimmer{
    0%  {background-position:100% 50%;}
    100%{background-position:-100% 50%;}
}

/* Placeholder centré */
.ph-center{
    position:absolute;
    inset:0;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:1rem;
    text-align:center;
    padding:2rem;
    color:rgba(255,255,255,.55);
}
.ph-ring{
    width:72px;height:72px;
    border-radius:50%;
    border:2px solid rgba(255,255,255,.15);
    background:rgba(255,255,255,.05);
    display:flex;align-items:center;justify-content:center;
}
.ph-ring i{font-size:1.5rem;}

/* ── Badge LIVE ── */
.live-badge{
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    background:#ef4444;
    color:white;
    font-size:.6875rem;
    font-weight:700;
    letter-spacing:.08em;
    text-transform:uppercase;
    padding:.25rem .75rem;
    border-radius:4px;
}
.live-dot{
    width:7px;height:7px;
    border-radius:50%;
    background:white;
    animation:pdot 1.2s ease-in-out infinite;
}
@keyframes pdot{
    0%,100%{opacity:1;transform:scale(1);}
    50%{opacity:.4;transform:scale(.65);}
}

/* ── Player footer ── */
.player-footer{
    padding:.875rem 1.25rem;
    background:#111;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:1rem;
}
.pf-title{font-weight:600;font-size:.9375rem;color:rgba(255,255,255,.9);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pf-meta{font-size:.75rem;color:rgba(255,255,255,.4);margin-top:.15rem;}
.yt-btn{
    display:inline-flex;align-items:center;gap:.5rem;
    background:#ff0000;color:white;
    font-size:.8125rem;font-weight:600;
    padding:.5rem 1rem;border-radius:.5rem;
    text-decoration:none;white-space:nowrap;
    transition:background .15s,transform .15s;
    flex-shrink:0;
}
.yt-btn:hover{background:#cc0000;transform:scale(1.02);color:white;}

/* ── Cards ── */
.card{background:white;border-radius:.875rem;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);}
.card-hd{
    padding:.875rem 1.25rem;
    border-bottom:1px solid rgba(0,0,0,.06);
    font-weight:700;font-size:.9375rem;color:#111;
    display:flex;align-items:center;gap:.5rem;
}
.card-bd{padding:1.25rem;}

/* ── Status pill ── */
.spill{
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.4rem .875rem;border-radius:100px;
    font-size:.8125rem;font-weight:600;
}
.sdot{width:8px;height:8px;border-radius:50%;display:inline-block;}

/* ── Rows ── */
.inst-row{
    display:flex;align-items:center;gap:.875rem;
    padding:1rem 0;border-top:1px solid rgba(0,0,0,.06);
}
.inst-av{width:42px;height:42px;border-radius:50%;object-fit:cover;flex-shrink:0;}
.meta-row{
    display:flex;align-items:flex-start;gap:.875rem;
    padding:.75rem 0;border-top:1px solid rgba(0,0,0,.06);
}
.meta-ico{
    width:32px;height:32px;border-radius:.5rem;
    background:#f3f4f6;display:flex;align-items:center;justify-content:center;
    color:#6b7280;font-size:.8125rem;flex-shrink:0;
}
.ml{font-size:.6875rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;font-weight:600;}
.mv{font-size:.875rem;color:#111;font-weight:500;margin-top:.1rem;}
.ms{font-size:.75rem;color:#9ca3af;}

/* ── Buttons ── */
.abtn{
    display:inline-flex;align-items:center;justify-content:center;gap:.5rem;
    padding:.625rem 1rem;border-radius:.625rem;
    font-size:.875rem;font-weight:500;
    cursor:pointer;text-decoration:none;
    border:1.5px solid transparent;
    transition:all .15s;font-family:inherit;
}
.abtn-primary{background:#0040a1;color:white;border-color:#0040a1;}
.abtn-primary:hover{background:#0056d2;color:white;}
.abtn-danger{background:#ef4444;color:white;border-color:#ef4444;}
.abtn-danger:hover{background:#dc2626;color:white;}
.abtn-secondary{background:white;color:#374151;border-color:#e5e7eb;}
.abtn-secondary:hover{background:#f9fafb;color:#111;}
.abtn-outline{background:transparent;color:#6b7280;border-color:#e5e7eb;width:100%;}
.abtn-outline:hover{background:#f3f4f6;color:#374151;}

/* ── Chat ── */
.chat-shell{
    background:white;border-radius:.875rem;overflow:hidden;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    display:flex;flex-direction:column;height:400px;
}
.chat-hd{
    padding:.875rem 1.25rem;
    border-bottom:1px solid rgba(0,0,0,.06);
    font-weight:700;font-size:.875rem;
    display:flex;align-items:center;gap:.5rem;
}
.chat-body{flex:1;position:relative;background:#0f0f0f;}
.chat-body iframe{position:absolute;inset:0;width:100%;height:100%;border:none;}
.chat-empty{
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    height:100%;color:#9ca3af;text-align:center;padding:1.5rem;gap:.5rem;background:#f9fafb;
}
.chat-empty i{font-size:2rem;opacity:.2;}

/* ── Back link ── */
.back-link{
    display:inline-flex;align-items:center;gap:.5rem;
    font-size:.875rem;color:#6b7280;text-decoration:none;
    padding:.375rem .75rem;border-radius:.5rem;transition:all .15s;margin-left:-.75rem;
}
.back-link:hover{background:#f3f4f6;color:#111;}

/* ── Toast ── */
.copy-toast{
    position:fixed;bottom:1.5rem;left:50%;
    transform:translateX(-50%) translateY(80px);
    background:#111;color:white;
    padding:.625rem 1.25rem;border-radius:.625rem;
    font-size:.875rem;font-weight:500;
    box-shadow:0 8px 24px rgba(0,0,0,.3);
    z-index:999;transition:transform .3s ease;pointer-events:none;
}
.copy-toast.show{transform:translateX(-50%) translateY(0);}

/* ── Responsive ── */
@media(max-width:900px){
    .lv-grid{grid-template-columns:1fr;}
    .lv-right{position:static;}
}
</style>

<div class="lv-wrap">

    {{-- Retour --}}
    <div style="margin-bottom:1rem;">
        <a href="{{ url()->previous() ?? route('courses.show', $live->course_id) }}" class="back-link">
            <i class="fas fa-arrow-left" style="font-size:.75rem;"></i> Retour au cours
        </a>
    </div>

    <div class="lv-grid">

        {{-- ══════════════ GAUCHE ══════════════ --}}
        <div class="lv-left">

            {{-- PLAYER --}}
            <div class="player-shell">
                <div class="player-stage">

                    {{-- z=1 : Skeleton / placeholder de fond --}}
                    @if(!$live->stream_embed && (!$embedSrc || !$isLive))
                        <div id="layer-skeleton" class="player-layer skeleton-bg">
                            <div class="ph-center">
                                @if($live->status === 'scheduled')
                                    @if($videoId)
                                        <img src="https://img.youtube.com/vi/{{ $videoId }}/maxresdefault.jpg"
                                             alt="{{ $live->title }}"
                                             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.3;"
                                             onerror="this.style.display='none'">
                                    @endif
                                    <div style="position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:.875rem;">
                                        <div class="ph-ring"><i class="fas fa-clock"></i></div>
                                        <div>
                                            <p style="font-size:1rem;font-weight:600;color:rgba(255,255,255,.85);">Diffusion à venir</p>
                                            @if($live->scheduled_at)
                                                <p style="font-size:.8125rem;margin-top:.25rem;">{{ $live->scheduled_at->format('d/m/Y à H:i') }}</p>
                                            @endif
                                        </div>
                                        @if($videoId)
                                            <a href="{{ $live->stream_url }}" target="_blank"
                                               style="background:rgba(255,255,255,.12);color:white;padding:.5rem 1.25rem;border-radius:.5rem;font-size:.8125rem;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.2);backdrop-filter:blur(8px);">
                                                <i class="fab fa-youtube" style="margin-right:.4rem;"></i> Voir sur YouTube
                                            </a>
                                        @endif
                                    </div>
                                @elseif($live->status === 'live')
                                    <div class="ph-ring" style="border-color:rgba(239,68,68,.4);">
                                        <i class="fas fa-broadcast-tower" style="color:#ef4444;"></i>
                                    </div>
                                    <span class="live-badge"><span class="live-dot"></span>En direct</span>
                                    <p style="font-size:.8125rem;color:rgba(255,255,255,.35);margin-top:.25rem;">Aucun flux configuré.</p>
                                @else
                                    <div class="ph-ring"><i class="fas fa-check-circle"></i></div>
                                    <p style="font-size:.9375rem;font-weight:600;color:rgba(255,255,255,.6);">Session terminée</p>
                                    <p style="font-size:.8125rem;color:rgba(255,255,255,.3);">Le replay n'est pas disponible ici.</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- z=2 : Embed brut --}}
                    @if($live->stream_embed)
                        <div id="layer-embed" class="player-layer" style="z-index:2;">
                            {!! $live->stream_embed !!}
                        </div>
                    @endif

                    {{-- z=2 : iFrame YouTube (visible seulement si live actif) --}}
                    @if($embedSrc && $isLive && !$live->stream_embed)
                        <iframe
                            id="layer-youtube"
                            class="player-layer"
                            src="{{ $embedSrc }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                        </iframe>
                    @endif

                    {{-- z=5 : Flux caméra (VIDEO — toujours présent mais caché par défaut) --}}
                    <video
                        id="layer-camera"
                        class="player-layer"
                        autoplay
                        playsinline
                        muted>
                    </video>

                    {{-- z=10 : Badge EN DIRECT --}}
                    @if($isLive)
                        <div id="layer-live-badge">
                            <span class="live-badge"><span class="live-dot"></span>En direct</span>
                        </div>
                    @endif

                    {{-- z=20 : Overlay "Activer caméra" (instructeur + live seulement) --}}
                    @if($isInstructor && $isLive)
                        <div id="layer-cam-overlay" class="player-layer">
                            <button id="btn-start-cam" class="abtn abtn-primary" style="padding:.75rem 1.75rem;font-size:1rem;border-radius:.75rem;">
                                <i class="fas fa-video"></i> Activer ma caméra
                            </button>
                            <p style="font-size:.8rem;opacity:.45;margin:0;">Diffusez votre flux webcam ici</p>
                        </div>
                    @endif

                    {{-- z=30 : Indicateur caméra active --}}
                    <div id="layer-cam-indicator">
                        <span class="live-badge" style="background:#7c3aed;">
                            <span class="live-dot"></span> CAMÉRA ACTIVE
                        </span>
                    </div>

                    {{-- z=30 : Bouton stop caméra --}}
                    <button id="btn-stop-cam">
                        <i class="fas fa-stop-circle"></i> Arrêter
                    </button>

                </div>{{-- /player-stage --}}

                {{-- Footer --}}
                <div class="player-footer">
                    <div style="min-width:0;flex:1;">
                        <div class="pf-title">{{ $live->title }}</div>
                        <div class="pf-meta">
                            {{ $channelName }}
                            @if($isLive && $live->started_at)
                                · Démarré {{ $live->started_at->diffForHumans() }}
                            @elseif($live->scheduled_at && !$isLive)
                                · Le {{ $live->scheduled_at->format('d/m/Y à H:i') }}
                            @endif
                        </div>
                    </div>
                    @if($videoId && in_array($live->status, ['live','scheduled']))
                        <a href="{{ $live->stream_url }}" target="_blank" rel="noopener" class="yt-btn">
                            <i class="fab fa-youtube"></i> YouTube
                        </a>
                    @endif
                </div>

            </div>{{-- /player-shell --}}

            {{-- Description --}}
            @if($live->description)
                <div class="card"><div class="card-bd">
                    <p style="font-size:.8125rem;font-weight:700;color:#111;margin-bottom:.5rem;">
                        <i class="fas fa-align-left" style="color:#0040a1;margin-right:.4rem;"></i>Description
                    </p>
                    <p style="font-size:.875rem;color:#4b5563;line-height:1.7;margin:0;">{{ $live->description }}</p>
                </div></div>
            @endif

            {{-- Contrôles instructeur --}}
            @if($isInstructor)
                <div class="card"><div class="card-bd">
                    <p style="font-size:.6875rem;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.875rem;">
                        Contrôles instructeur
                    </p>
                    <div style="display:flex;flex-wrap:wrap;gap:.625rem;">
                        @if($live->status === 'scheduled')
                            <form action="{{ route('instructor.courses.live.start', ['course' => $live->course_id, 'live' => $live->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="abtn abtn-primary" style="padding:.625rem 1.25rem;">
                                    <i class="fas fa-play"></i> Démarrer le live
                                </button>
                            </form>
                        @endif
                        @if($live->status === 'live')
                            <form action="{{ route('instructor.courses.live.end', ['course' => $live->course_id, 'live' => $live->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="abtn abtn-danger" style="padding:.625rem 1.25rem;">
                                    <i class="fas fa-stop"></i> Terminer le live
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('instructor.courses.live.edit', ['course' => $live->course_id, 'live' => $live->id]) }}"
                           class="abtn abtn-secondary" style="padding:.625rem 1.25rem;">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                    </div>
                </div></div>
            @endif

        </div>{{-- /lv-left --}}

        {{-- ══════════════ DROITE ══════════════ --}}
        <div class="lv-right">

            {{-- Détails --}}
            <div class="card">
                <div class="card-hd">
                    <i class="fas fa-circle-info" style="color:#0040a1;font-size:.875rem;"></i> Détails
                </div>
                <div class="card-bd">
                    <div>
                        <span class="ml">Statut</span>
                        <div style="margin-top:.375rem;">
                            <span class="spill"
                                  style="background:{{ $live->status==='live'?'#fee2e2':($live->status==='scheduled'?'#dbeafe':'#f3f4f6') }};
                                         color:{{ $live->status==='live'?'#991b1b':($live->status==='scheduled'?'#1e40af':'#4b5563') }};">
                                <span class="sdot"
                                      style="background:{{ $status['dot'] }};{{ $status['pulse']?'animation:pdot 1.2s ease-in-out infinite;':'' }}">
                                </span>
                                {{ $status['label'] }}
                            </span>
                        </div>
                    </div>

                    <div class="inst-row">
                        <img src="{{ $avatarUrl }}" alt="{{ $channelName }}" class="inst-av">
                        <div>
                            <div style="font-weight:600;font-size:.875rem;color:#111;">{{ $channelName }}</div>
                            <div style="font-size:.75rem;color:#6b7280;">Instructeur</div>
                        </div>
                    </div>

                    @if($live->scheduled_at)
                        <div class="meta-row">
                            <div class="meta-ico"><i class="far fa-calendar-alt"></i></div>
                            <div>
                                <div class="ml">Programmée</div>
                                <div class="mv">{{ $live->scheduled_at->format('d/m/Y à H:i') }}</div>
                                <div class="ms">{{ $live->scheduled_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif

                    @if($live->started_at)
                        <div class="meta-row">
                            <div class="meta-ico" style="background:#dcfce7;color:#16a34a;"><i class="fas fa-play"></i></div>
                            <div>
                                <div class="ml">Démarré</div>
                                <div class="mv">{{ $live->started_at->format('d/m/Y à H:i') }}</div>
                                <div class="ms">{{ $live->started_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif

                    @if($live->ended_at)
                        <div class="meta-row">
                            <div class="meta-ico" style="background:#fee2e2;color:#ef4444;"><i class="fas fa-stop"></i></div>
                            <div>
                                <div class="ml">Terminée</div>
                                <div class="mv">{{ $live->ended_at->format('d/m/Y à H:i') }}</div>
                                <div class="ms">{{ $live->ended_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif

                    @if($live->stream_url)
                        <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid rgba(0,0,0,.06);">
                            <button onclick="copyLink('{{ $live->stream_url }}')" class="abtn abtn-outline">
                                <i class="fas fa-link"></i> Copier le lien du live
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Chat --}}
            <div class="chat-shell">
                <div class="chat-hd">
                    @if($isLive && $videoId)
                        <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;animation:pdot 1.2s ease-in-out infinite;display:inline-block;flex-shrink:0;"></span>
                    @else
                        <i class="fas fa-comments" style="color:#9ca3af;font-size:.875rem;"></i>
                    @endif
                    Chat en direct
                    @if($isLive && $videoId)
                        <span style="margin-left:auto;font-size:.6875rem;color:#9ca3af;font-weight:400;">YouTube Live</span>
                    @endif
                </div>

                @if($isLive && $videoId)
                    <div class="chat-body">
                        <iframe src="https://www.youtube.com/live_chat?v={{ $videoId }}&embed_domain={{ parse_url(url('/'), PHP_URL_HOST) }}" frameborder="0"></iframe>
                    </div>
                @else
                    <div class="chat-empty">
                        <i class="fas fa-comments"></i>
                        <p style="font-size:.875rem;font-weight:600;color:#374151;margin:0;">
                            @if($live->status === 'live') Flux non YouTube
                            @elseif($live->status === 'scheduled') Disponible au démarrage
                            @else Aucun chat disponible @endif
                        </p>
                        <p style="font-size:.8125rem;color:#9ca3af;margin:0;">
                            @if($live->status === 'scheduled') Le chat s'ouvrira au démarrage.
                            @elseif($live->status === 'live') Uniquement pour les streams YouTube.
                            @else La session est terminée. @endif
                        </p>
                    </div>
                @endif
            </div>

        </div>{{-- /lv-right --}}
    </div>{{-- /lv-grid --}}
</div>

{{-- Toast --}}
<div class="copy-toast" id="copyToast">
    <i class="fas fa-check" style="margin-right:.4rem;color:#4ade80;"></i> Lien copié !
</div>

<script>
/* ── Copy ── */
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const t = document.getElementById('copyToast');
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2200);
    });
}

/* ════════════════════════════════════════════════════════════════════════
   🎥 GESTION CAMÉRA — ROBUSTE (avec fallback)
   ════════════════════════════════════════════════════════════════════════ */
@if($isInstructor && $isLive)
(function () {
    const videoEl   = document.getElementById('layer-camera');
    const btnStart  = document.getElementById('btn-start-cam');
    const btnStop   = document.getElementById('btn-stop-cam');
    const overlay   = document.getElementById('layer-cam-overlay');
    const indicator = document.getElementById('layer-cam-indicator');
    const skeleton  = document.getElementById('layer-skeleton');
    const ytIframe  = document.getElementById('layer-youtube');

    let stream = null;

    // Fonction utilitaire pour essayer différentes contraintes
    async function tryGetCamera(constraints) {
        try {
            return await navigator.mediaDevices.getUserMedia(constraints);
        } catch (e) {
            console.warn('Échec avec contraintes :', constraints, e);
            return null;
        }
    }

    async function startCamera() {
        try {
            // 1. Vérifier si la caméra est disponible
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === 'videoinput');
            if (videoDevices.length === 0) {
                alert('Aucune caméra détectée sur votre appareil.');
                return;
            }

            // 2. Essayer différentes configurations
            let stream = null;

            // Essai 1 : contraintes idéales
            stream = await tryGetCamera({
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            if (!stream) {
                // Essai 2 : sans contrainte de résolution
                stream = await tryGetCamera({
                    video: { facingMode: 'user' },
                    audio: false
                });
            }

            if (!stream) {
                // Essai 3 : caméra par défaut (sans aucune contrainte)
                stream = await tryGetCamera({ video: true, audio: false });
            }

            if (!stream) {
                throw new Error('Impossible de démarrer la caméra avec aucune configuration.');
            }

            // 3. Connecter le flux
            videoEl.srcObject = stream;
            await videoEl.play().catch(() => {});

            // 4. Forcer l'affichage de la vidéo (z-index=5)
            videoEl.style.display = 'block';
            videoEl.style.width = '100%';
            videoEl.style.height = '100%';
            videoEl.style.objectFit = 'cover';

            // 5. Masquer les couches gênantes
            if (skeleton) skeleton.style.display = 'none';
            if (ytIframe) ytIframe.style.display = 'none';
            overlay.style.display = 'none';

            // 6. Afficher les contrôles
            indicator.style.display = 'block';
            btnStop.style.display = 'block';

            // 7. Désactiver le bouton start
            btnStart.innerHTML = '<i class="fas fa-check-circle"></i> Caméra active';
            btnStart.disabled = true;
            btnStart.style.opacity = '0.6';
            btnStart.style.cursor = 'default';

            console.log('✅ Caméra activée avec succès');

        } catch (err) {
            console.error('Erreur finale :', err);
            alert(
                'Impossible d’accéder à la caméra :\n\n' +
                err.message +
                '\n\n' +
                'Vérifiez que :\n' +
                '1. Aucune autre application n’utilise votre caméra (Zoom, Teams, etc.)\n' +
                '2. Vous avez autorisé l’accès à la caméra pour ce site\n' +
                '3. Votre caméra est bien connectée et fonctionnelle'
            );
        }
    }

    function stopCamera() {
        // Arrêter le flux
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }

        // Nettoyer la vidéo
        videoEl.pause();
        videoEl.srcObject = null;
        videoEl.style.display = 'none';

        // Rétablir l'affichage
        if (skeleton) skeleton.style.display = 'block';
        if (ytIframe) ytIframe.style.display = 'block';
        overlay.style.display = 'flex';
        indicator.style.display = 'none';
        btnStop.style.display = 'none';

        // Réactiver le bouton start
        btnStart.innerHTML = '<i class="fas fa-video"></i> Activer ma caméra';
        btnStart.disabled = false;
        btnStart.style.opacity = '1';
        btnStart.style.cursor = 'pointer';

        console.log('⏹️ Caméra arrêtée');
    }

    // Événements
    btnStart?.addEventListener('click', startCamera);
    btnStop?.addEventListener('click', stopCamera);

    // Nettoyage automatique en cas de fermeture de page
    window.addEventListener('beforeunload', () => {
        if (stream) stream.getTracks().forEach(t => t.stop());
    });
})();
@endif
</script>

{{-- Auto-refresh --}}
@if($live->status === 'scheduled')
    <script>setTimeout(() => location.reload(), 30000);</script>
@elseif($live->status === 'live')
    <script>setTimeout(() => location.reload(), 120000);</script>
@endif

@endsection