<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumThread;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumPostController extends Controller
{
    public function store(Request $request, Course $course, ForumThread $thread)
    {
        $data = $request->validate(['body' => 'required|string']);
        $thread->posts()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);
        return back()->with('success', 'Réponse ajoutée.');
    }

    public function markSolution(Course $course, ForumPost $post)
    {
        $thread = $post->thread;
        // Seul l'auteur du sujet peut marquer une solution
        if ($thread->user_id !== auth()->id()) {
            abort(403);
        }
        $thread->posts()->where('is_solution', true)->update(['is_solution' => false]);
        $post->update(['is_solution' => true]);
        return back()->with('success', 'Solution marquée.');
    }
}