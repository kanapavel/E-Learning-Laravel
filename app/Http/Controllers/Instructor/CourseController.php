<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index()
    {
        $courses = auth()->user()->courses()->latest()->paginate(10);
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        return view('instructor.courses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'level'       => 'required|in:beginner,intermediate,advanced',
            'language'    => 'required|string|size:2',
            'price'       => 'required|numeric|min:0',
            'thumbnail'   => 'nullable|image|max:2048',
            'published'   => 'boolean',
        ]);

        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);
        $data['published'] = $request->has('published');

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        Course::create($data);

        return redirect()->route('instructor.courses.index')->with('success', 'Cours créé.');
    }

    public function edit(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'level'       => 'required|in:beginner,intermediate,advanced',
            'language'    => 'required|string|size:2',
            'price'       => 'required|numeric|min:0',
            'thumbnail'   => 'nullable|image|max:2048',
            'published'   => 'boolean',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $data['slug'] = Str::slug($data['title']);
        $data['published'] = $request->has('published');

        $course->update($data);

        return redirect()->route('instructor.courses.index')->with('success', 'Cours mis à jour.');
    }

    public function destroy(Course $course)
    {
        if ($course->user_id !== auth()->id()) {
            abort(403);
        }

        $course->delete();

        return redirect()->route('instructor.courses.index')->with('success', 'Cours supprimé.');
    }
}