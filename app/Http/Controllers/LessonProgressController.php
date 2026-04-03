<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{
    public function complete(Request $request, Lesson $lesson)
    {
        $user = auth()->user();
        $course = $lesson->chapter->course;

        if (!$user->isEnrolledIn($course->id)) {
            return back()->with('error', 'Vous devez être inscrit pour valider cette leçon.');
        }

        $progress = $user->lessonProgress()->firstOrCreate([
            'lesson_id' => $lesson->id,
        ]);

        if (!$progress->completed) {
            $progress->completed = true;
            $progress->completed_at = now();
            $progress->save();

            // Vérifier si toutes les leçons du cours sont terminées
            $totalLessons = $course->lessons()->count();
            $completedLessons = $user->lessonProgress()
                ->where('completed', true)
                ->whereHas('lesson.chapter', fn($q) => $q->where('course_id', $course->id))
                ->count();

            if ($totalLessons > 0 && $completedLessons == $totalLessons) {
                $enrollment = $user->enrollments()->where('course_id', $course->id)->first();
                $enrollment->status = 'completed';
                $enrollment->completed_at = now();
                $enrollment->save();
            }

            // Trouver la leçon suivante
            $nextLesson = null;
            $lessons = $course->lessons()->orderBy('order')->get();
            $found = false;
            foreach ($lessons as $l) {
                if ($found) {
                    $nextLesson = $l;
                    break;
                }
                if ($l->id == $lesson->id) {
                    $found = true;
                }
            }

            if ($nextLesson) {
                return redirect()->route('lessons.show', $nextLesson)->with('success', 'Leçon terminée ! Passage à la suivante.');
            } else {
                return redirect()->route('courses.show', $course)->with('success', 'Félicitations ! Vous avez terminé toutes les leçons de ce cours.');
            }
        }

        return redirect()->route('courses.show', $course)->with('info', 'Cette leçon était déjà terminée.');
    }
}