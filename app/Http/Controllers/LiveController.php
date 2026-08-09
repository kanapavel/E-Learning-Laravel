<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use Illuminate\Support\Facades\Auth;

class LiveController extends Controller
{
    public function show(LiveSession $live)
    {
        // Charger la relation course pour éviter null
        $live->load('course');

        $user = Auth::user();

        // Vérifier l'accès : inscrit au cours OU instructeur OU admin
        $courseId = $live->course_id;
        $isInstructor = $live->course && $user->id === $live->course->user_id;
        $isAdmin = $user->isAdmin();
        $isEnrolled = $courseId ? $user->isEnrolledIn((int) $courseId) : false;

        if (!$isEnrolled && !$isInstructor && !$isAdmin) {
            abort(403, 'Vous devez être inscrit à ce cours pour accéder au live.');
        }

        // La vue gérera l'affichage selon le statut
        return view('live.show', compact('live'));
    }
}