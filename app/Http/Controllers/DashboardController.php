<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function student()
    {
        $user = auth()->user();
        $enrollments = $user->enrollments()->with('course.chapters')->latest()->get();
        $totalLessonsCompleted = $user->lessonProgress()->where('completed', true)->count();
        
        // Simulons un streak (vous pouvez le stocker en base)
        $currentStreak = 4;
        
        // Calcul des modules restants (leçons non terminées)
        $pendingModules = 0;
        foreach ($enrollments as $enrollment) {
            $totalLessons = $enrollment->course->lessons()->count();
            $completedLessons = $user->lessonProgress()
                ->where('completed', true)
                ->whereHas('lesson.chapter', fn($q) => $q->where('course_id', $enrollment->course_id))
                ->count();
            $pendingModules += max(0, $totalLessons - $completedLessons);
        }

        return view('dashboard.student', compact('enrollments', 'totalLessonsCompleted', 'currentStreak', 'pendingModules'));
    }

    public function instructor()
    {
        $user = auth()->user();
        $courses = $user->courses()->withCount('enrollments')->latest()->get();
        $totalStudents = $courses->sum('enrollments_count');
        $publishedCourses = $courses->where('published', true)->count();

        return view('dashboard.instructor', compact('courses', 'totalStudents', 'publishedCourses'));
    }

    public function admin()
    {
        $totalUsers = \App\Models\User::count();
        $totalCourses = \App\Models\Course::count();
        $totalEnrollments = \App\Models\Enrollment::count();
        $totalRevenue = \App\Models\Enrollment::sum('paid_amount');

        return view('dashboard.admin', compact('totalUsers', 'totalCourses', 'totalEnrollments', 'totalRevenue'));
    }
}