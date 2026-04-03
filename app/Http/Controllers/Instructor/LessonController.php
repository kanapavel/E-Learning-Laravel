<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Lesson;
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
            'type'             => 'required|in:video,text,quiz',
            'video_file'       => 'nullable|file|mimes:mp4,mov,avi|max:204800',
            'video_url'        => 'nullable|url',
            'content'          => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free'          => 'boolean',
            'order'            => 'nullable|integer|min:0',
        ]);

        $data['chapter_id'] = $chapter->id;
        $data['order'] = $data['order'] ?? ($chapter->lessons()->max('order') + 1);
        $data['is_free'] = $request->has('is_free');

        if ($request->hasFile('video_file')) {
            $path = $request->file('video_file')->store('videos', 'public');
            $data['video_path'] = $path;
        }

        Lesson::create($data);

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
            'type'             => 'required|in:video,text,quiz',
            'video_file'       => 'nullable|file|mimes:mp4,mov,avi|max:204800',
            'video_url'        => 'nullable|url',
            'content'          => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_free'          => 'boolean',
            'order'            => 'nullable|integer|min:0',
        ]);

        $data['is_free'] = $request->has('is_free');

        if ($request->hasFile('video_file')) {
            if ($lesson->video_path) {
                Storage::disk('public')->delete($lesson->video_path);
            }
            $path = $request->file('video_file')->store('videos', 'public');
            $data['video_path'] = $path;
        }

        $lesson->update($data);

        return redirect()->route('instructor.courses.edit', $chapter->course)->with('success', 'Leçon mise à jour.');
    }

     public function show(Lesson $lesson)
    {
        // Vérifie que l'utilisateur est bien inscrit au cours de cette leçon
        $this->authorize('view', $lesson);

        return view('lessons.show', compact('lesson'));
    }

    public function destroy(Chapter $chapter, Lesson $lesson)
    {
        if ($lesson->video_path) {
            Storage::disk('public')->delete($lesson->video_path);
        }
        $lesson->delete();

        return redirect()->route('instructor.courses.edit', $chapter->course)->with('success', 'Leçon supprimée.');
    }
}