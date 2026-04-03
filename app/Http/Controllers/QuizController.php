<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\SubmissionAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function take(Quiz $quiz)
    {
        $user = auth()->user();

        // Vérifier que l'utilisateur est inscrit au cours
        if (!$user->isEnrolledIn($quiz->course_id)) {
            abort(403);
        }

        // Vérifier le nombre de tentatives
        $attempts = $quiz->attemptsCountFor($user->id);
        if ($attempts >= $quiz->max_attempts) {
            return redirect()->back()->with('error', 'Vous avez atteint le nombre maximal de tentatives.');
        }

        return view('quizzes.take', compact('quiz'));
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $user = auth()->user();

        // Validation simple : chaque question doit avoir une réponse
        $rules = [];
        foreach ($quiz->questions as $question) {
            $rules['answers.' . $question->id] = 'required';
        }
        $request->validate($rules);

        $totalPoints = 0;
        $earnedPoints = 0;
        $submissionAnswers = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = $request->input('answers.' . $question->id);
            $isCorrect = false;

            if ($question->type === 'single') {
                $correctAnswer = $question->answers()->where('is_correct', true)->first();
                if ($correctAnswer && $userAnswer == $correctAnswer->id) {
                    $isCorrect = true;
                }
            }
            // Tu peux étendre pour d'autres types de questions (multiple, true_false)

            if ($isCorrect) {
                $earnedPoints += $question->points;
            }

            $submissionAnswers[] = [
                'question_id' => $question->id,
                'answer_id'   => ($question->type === 'single') ? $userAnswer : null,
                'is_correct'  => $isCorrect,
            ];
        }

        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $passed = $score >= $quiz->pass_score;

        // Créer la soumission
        $submission = QuizSubmission::create([
            'user_id'      => $user->id,
            'quiz_id'      => $quiz->id,
            'score'        => $score,
            'passed'       => $passed,
            'attempt'      => $quiz->attemptsCountFor($user->id) + 1,
            'submitted_at' => now(),
        ]);

        // Enregistrer les réponses
        foreach ($submissionAnswers as $answerData) {
            $answerData['quiz_submission_id'] = $submission->id;
            SubmissionAnswer::create($answerData);
        }

        return redirect()->route('quizzes.result', $submission);
    }

    public function result(QuizSubmission $submission)
    {
        // Vérifier que la soumission appartient bien à l'utilisateur connecté
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        return view('quizzes.result', compact('submission'));
    }
}