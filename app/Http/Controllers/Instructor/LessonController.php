<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function create(Chapter $chapter)
    {
        return view('instructor.lessons.create', compact('chapter'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'required|in:video,text,mixed',
            'video_file'       => 'nullable|file|mimes:mp4,mov,avi|max:204800',
            'video_url'        => 'nullable|url',
            'content'          => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free'          => 'boolean',
            'order'            => 'nullable|integer|min:0',
            'resources'        => 'nullable|array',
            'resources.*'      => 'file|max:51200',
        ]);

        $data['chapter_id'] = $chapter->id;
        $data['order'] = $data['order'] ?? ($chapter->lessons()->max('order') + 1);
        $data['is_free'] = $request->has('is_free');
        $data['duration_minutes'] = $data['duration_minutes'] ?? 0; // ✅ valeur par défaut

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $data['video_path'] = $path;
        }

        $lesson = Lesson::create($data);

        // Gestion des ressources
        if ($request->hasFile('resources')) {
            foreach ($request->file('resources') as $file) {
                $path = $file->store('resources', 'public');
                $lesson->resources()->create([
                    'title'     => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('instructor.courses.edit', $chapter->course)->with('success', 'Leçon ajoutée.');
    }

    public function edit(Chapter $chapter, Lesson $lesson)
    {
        return view('instructor.lessons.edit', compact('chapter', 'lesson'));
    }

    public function update(Request $request, Chapter $chapter, Lesson $lesson)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'type'             => 'required|in:video,text,mixed',
            'video_file'       => 'nullable|file|mimes:mp4,mov,avi|max:204800', // 200 Mo
            'video_url'        => 'nullable|url',
            'content'          => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free'          => 'boolean',
            'order'            => 'nullable|integer|min:0',
            'new_resources'    => 'nullable|array',
            'new_resources.*'  => 'file|max:51200',
            'deleted_resources' => 'nullable|array',
            'deleted_resources.*' => 'integer|exists:resources,id',
        ]);

        $data['is_free'] = $request->has('is_free');

        // Gestion de la vidéo uploadée
        if ($request->hasFile('video_file')) {
            // Supprimer l'ancienne vidéo si existante
            if ($lesson->video_path && Storage::disk('public')->exists($lesson->video_path)) {
                Storage::disk('public')->delete($lesson->video_path);
            }
            $path = $request->file('video_file')->store('lessons/videos', 'public');
            $data['video_path'] = $path;
            $data['video_url'] = null; // priorité au fichier local
        } elseif ($request->filled('video_url')) {
            // Si on fournit une URL, on supprime l'éventuel fichier local
            if ($lesson->video_path && Storage::disk('public')->exists($lesson->video_path)) {
                Storage::disk('public')->delete($lesson->video_path);
                $data['video_path'] = null;
            }
        } else {
            // Rien de nouveau pour la vidéo, on garde les anciennes valeurs
            unset($data['video_path'], $data['video_url']);
        }

        $lesson->update($data);

        // Gestion des ressources (identique à store)
        if ($request->has('deleted_resources')) {
            Resource::whereIn('id', $request->deleted_resources)
                ->where('lesson_id', $lesson->id)
                ->each(function ($resource) {
                    Storage::disk('public')->delete($resource->file_path);
                    $resource->delete();
                });
        }

        if ($request->hasFile('new_resources')) {
            foreach ($request->file('new_resources') as $file) {
                $path = $file->store('resources', 'public');
                $lesson->resources()->create([
                    'title'     => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('instructor.courses.edit', $chapter->course)
                         ->with('success', 'Leçon mise à jour avec succès.');
    }

    public function show(Lesson $lesson)
    {
        $this->authorize('view', $lesson);
        return view('lessons.show', compact('lesson'));
    }

    public function destroy(Chapter $chapter, Lesson $lesson)
    {
        // Supprimer la vidéo locale
        if ($lesson->video_path) {
            Storage::disk('public')->delete($lesson->video_path);
        }
        // Supprimer les ressources associées
        foreach ($lesson->resources as $resource) {
            Storage::disk('public')->delete($resource->file_path);
            $resource->delete();
        }
        $lesson->delete();

        return redirect()->route('instructor.courses.edit', $chapter->course)->with('success', 'Leçon supprimée.');
    }
}