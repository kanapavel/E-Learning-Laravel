@extends('layouts.app')

@section('title', 'Programmer une session')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">

    <!-- Fil d'Ariane -->
    <nav class="mb-6 text-sm text-on-surface-variant">
        <a href="{{ route('instructor.courses.live.index', $course) }}" class="hover:text-primary transition">
            <i class="fas fa-arrow-left mr-1"></i> Sessions en direct
        </a>
        <span class="mx-2">/</span>
        <span>Programmer</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-display font-bold tracking-tight mb-6">
        Programmer une session en direct
    </h1>

    <div class="bg-white rounded-2xl border border-outline/20 shadow-sm p-6 sm:p-8">
        <form action="{{ route('instructor.courses.live.store', $course) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Titre -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Titre de la session <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" class="input-field w-full" required>
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Description <span class="text-on-surface-variant text-xs font-normal">(optionnelle)</span>
                </label>
                <textarea name="description" rows="3" class="input-field w-full resize-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date et heure -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Date et heure <span class="text-on-surface-variant text-xs font-normal">(optionnelle)</span>
                </label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" class="input-field w-full">
                <p class="text-xs text-on-surface-variant mt-1">Programmez une date future pour que les étudiants soient notifiés.</p>
                @error('scheduled_at')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- URL du stream -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    URL du stream (RTMP ou page d'embed)
                </label>
                <input type="url" name="stream_url" value="{{ old('stream_url') }}" class="input-field w-full" placeholder="https://...">
                @error('stream_url')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code d'intégration (iframe) -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Code d'intégration (iframe)
                </label>
                <textarea name="stream_embed" rows="3" class="input-field w-full resize-none font-mono text-sm" placeholder="<iframe src='...' frameborder='0'></iframe>">{{ old('stream_embed') }}</textarea>
                <p class="text-xs text-on-surface-variant mt-1">Utilisez ce champ si vous voulez intégrer une vidéo depuis YouTube, Vimeo ou une autre plateforme.</p>
                @error('stream_embed')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Miniature -->
            <div>
                <label class="block text-sm font-semibold text-on-surface mb-2">
                    Miniature
                </label>
                <div class="flex items-center gap-4 flex-wrap">
                    <label class="cursor-pointer bg-primary/10 hover:bg-primary/20 text-primary px-4 py-2 rounded-lg transition text-sm font-medium">
                        Choisir une image
                        <input type="file" name="thumbnail" accept="image/*" class="hidden" onchange="previewThumbnail(event)">
                    </label>
                    <span id="file_name" class="text-sm text-on-surface-variant">Aucun fichier</span>
                </div>
                <div id="thumbnail_preview" class="mt-3 hidden">
                    <p class="text-xs text-on-surface-variant mb-1">Aperçu :</p>
                    <img id="preview_img" class="w-32 h-32 object-cover rounded-xl border border-outline/20 shadow-sm">
                </div>
                @error('thumbnail')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Boutons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                <a href="{{ route('instructor.courses.live.index', $course) }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 rounded-xl border border-outline/30 text-on-surface-variant hover:bg-surface-low transition font-medium">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:bg-primary-container hover:scale-[1.02] transition-all duration-200 shadow-md font-medium">
                    <i class="fas fa-save"></i> Programmer
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function previewThumbnail(event) {
        const file = event.target.files[0];
        const fileNameSpan = document.getElementById('file_name');
        const previewDiv = document.getElementById('thumbnail_preview');
        const previewImg = document.getElementById('preview_img');

        if (file) {
            fileNameSpan.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            fileNameSpan.textContent = 'Aucun fichier';
            previewDiv.classList.add('hidden');
        }
    }
</script>
@endsection