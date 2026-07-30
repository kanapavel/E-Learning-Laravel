<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;

class LiveController extends Controller
{
    public function show(LiveSession $session)
    {
        $user = auth()->user();

        // Vérifier que l'utilisateur est inscrit OU qu'il est l'instructeur du cours
        if (!$user->isEnrolledIn($session->course_id) && $user->id != $session->course->user_id) {
            abort(403);
        }

        if ($session->status === 'ended' || $session->status === 'canceled') {
            return redirect()->route('courses.show', $session->course_id)
                             ->with('error', 'Cette session est terminée.');
        }

        return view('live.show', compact('session'));
    }
}