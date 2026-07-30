<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LiveSession;
use App\Notifications\NewLiveSessionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class LiveSessionController extends Controller
{
    public function index(Course $course)
    {
        $sessions = $course->liveSessions()->latest()->paginate(10);
        return view('instructor.live.index', compact('course', 'sessions'));
    }

    public function create(Course $course)
    {
        return view('instructor.live.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'scheduled_at'  => 'nullable|date|after:now',
            'stream_url'    => 'nullable|url',
            'stream_embed'  => 'nullable|string',
            'thumbnail'     => 'nullable|image|max:2048',
        ]);

        $data['course_id'] = $course->id;
        $data['instructor_id'] = auth()->id();
        $data['status'] = 'scheduled';

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('live_thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $session = LiveSession::create($data);

        $students = $course->students;
        Notification::send($students, new NewLiveSessionNotification($session));

        return redirect()->route('instructor.courses.live.index', $course)
                         ->with('success', 'Session en direct programmée !');
    }

    // ⭐ MODIFICATION ICI : $session → $live
    public function edit(Course $course, LiveSession $live)
    {
        return view('instructor.live.edit', compact('course', 'live'));
    }

    // ⭐ MODIFICATION ICI : $session → $live
    public function update(Request $request, Course $course, LiveSession $live)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'scheduled_at'  => 'nullable|date|after:now',
            'stream_url'    => 'nullable|url',
            'stream_embed'  => 'nullable|string',
            'thumbnail'     => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('live_thumbnails', 'public');
            $data['thumbnail'] = $path;
        }

        $live->update($data);

        return redirect()->route('instructor.courses.live.index', $course)
                         ->with('success', 'Session mise à jour.');
    }

    // ⭐ MODIFICATION ICI : $session → $live
    public function start(Course $course, LiveSession $live)
    {
        $live->update([
            'status' => 'live',
            'started_at' => now(),
        ]);

        return redirect()->route('instructor.courses.live.index', $course)
                         ->with('success', 'Session en direct lancée !');
    }

    // ⭐ MODIFICATION ICI : $session → $live
    public function end(Course $course, LiveSession $live)
    {
        $live->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        return redirect()->route('instructor.courses.live.index', $course)
                         ->with('success', 'Session terminée.');
    }

    // ⭐ MODIFICATION ICI : $session → $live
    public function destroy(Course $course, LiveSession $live)
    {
        $live->delete();
        return redirect()->route('instructor.courses.live.index', $course)
                         ->with('success', 'Session supprimée.');
    }
}