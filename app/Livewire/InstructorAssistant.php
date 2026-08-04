<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Services\AIService;
use Livewire\Component;

class InstructorAssistant extends Component
{
    public $isOpen = false;
    public $activeTab = 'plan';
    public $courseId = null;
    public $course = null;

    // Planification
    public $subject = '';
    public $level = 'débutant';
    public $duration = '10 heures';
    public $targetAudience = 'débutants';
    public $generatedPlan = null;
    public $isGeneratingPlan = false;

    // Contenu
    public $selectedChapter = null;
    public $lessonTitle = '';
    public $generatedContent = null;
    public $isGeneratingContent = false;
    public $contentStyle = 'éducatif';
    public $contentLength = 'moyen';
    public $generatedExercises = null;

    // Quiz
    public $quizTopic = '';
    public $quizDifficulty = 'moyen';
    public $quizCount = 10;
    public $generatedQuiz = null;
    public $isGeneratingQuiz = false;

    // Optimisation
    public $optimizationResults = null;
    public $isOptimizing = false;

    protected $listeners = ['openAssistant', 'refreshCourse'];

    public function mount()
    {
        // Récupérer l'ID depuis la route
        $this->courseId = request()->route('course');
        
        // Si c'est un modèle, on prend son ID
        if ($this->courseId instanceof Course) {
            $this->courseId = $this->courseId->id;
        }

        if ($this->courseId) {
            $this->course = Course::with('chapters.lessons')->find($this->courseId);
            if ($this->course) {
                $this->subject = $this->course->title;
                $this->level = $this->course->level ?? 'débutant';
            }
        }
    }

    public function openAssistant($tab = 'plan')
    {
        $this->isOpen = true;
        $this->activeTab = $tab;
        $this->refreshCourse();
    }

    public function refreshCourse()
    {
        if ($this->courseId) {
            $this->course = Course::with('chapters.lessons')->find($this->courseId);
        }
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    // ─── PLANIFICATION ──────────────────────────────────────────

    public function generatePlan()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'level' => 'required|in:débutant,intermédiaire,avancé',
            'duration' => 'required|string',
            'targetAudience' => 'required|string',
        ]);

        $this->isGeneratingPlan = true;

        try {
            $ai = app(AIService::class);
            $this->generatedPlan = $ai->generateCoursePlan(
                $this->subject,
                $this->level,
                $this->duration,
                $this->targetAudience
            );
        } catch (\Exception $e) {
            $this->addError('plan_error', $e->getMessage());
        }

        $this->isGeneratingPlan = false;
    }

    public function applyPlan()
    {
        if (!$this->generatedPlan) return;

        try {
            if (!$this->course) {
                $this->course = Course::create([
                    'user_id' => auth()->id(),
                    'title' => $this->generatedPlan['title'],
                    'description' => $this->generatedPlan['description'],
                    'level' => $this->level,
                    'published' => false,
                    'language' => 'fr',
                    'price' => 0,
                ]);
                $this->courseId = $this->course->id;
            } else {
                $this->course->update([
                    'title' => $this->generatedPlan['title'],
                    'description' => $this->generatedPlan['description'],
                    'level' => $this->level,
                ]);
            }

            foreach ($this->generatedPlan['chapters'] as $index => $chapterData) {
                $chapter = Chapter::create([
                    'course_id' => $this->course->id,
                    'title' => $chapterData['title'],
                    'description' => $chapterData['description'],
                    'order' => $index + 1,
                ]);

                $nbLessons = $chapterData['nb_lessons'] ?? 3;
                for ($i = 1; $i <= $nbLessons; $i++) {
                    Lesson::create([
                        'chapter_id' => $chapter->id,
                        'title' => "Leçon $i : " . $chapterData['title'],
                        'description' => "Contenu de la leçon $i du chapitre " . $chapterData['title'],
                        'type' => 'text',
                        'is_free' => $i === 1,
                        'order' => $i,
                        'duration_minutes' => 0,
                    ]);
                }
            }

            $this->refreshCourse();
            $this->generatedPlan = null;
            $this->dispatch('courseUpdated');

            return redirect()->route('instructor.courses.edit', $this->course)
                ->with('success', 'Plan de cours appliqué avec succès !');

        } catch (\Exception $e) {
            $this->addError('apply_error', $e->getMessage());
        }
    }

    // ─── CONTENU ────────────────────────────────────────────────

    public function generateLessonContent()
    {
        $this->validate([
            'lessonTitle' => 'required|string|max:255',
            'selectedChapter' => 'required|exists:chapters,id',
        ]);

        $this->isGeneratingContent = true;

        try {
            $chapter = Chapter::find($this->selectedChapter);
            $ai = app(AIService::class);

            $this->generatedContent = $ai->generateLessonContent(
                $chapter->title,
                $this->lessonTitle,
                $this->level,
                ['style' => $this->contentStyle, 'length' => $this->contentLength]
            );

            $this->generatedExercises = $ai->generateExercises(
                $this->lessonTitle,
                $this->level,
                3
            );

        } catch (\Exception $e) {
            $this->addError('content_error', $e->getMessage());
        }

        $this->isGeneratingContent = false;
    }

    public function createLesson()
    {
        if (!$this->generatedContent) return;

        try {
            $lesson = Lesson::create([
                'chapter_id' => $this->selectedChapter,
                'title' => $this->lessonTitle,
                'type' => 'text',
                'content' => $this->formatContentForDb($this->generatedContent),
                'is_free' => false,
                'order' => 0,
                'duration_minutes' => 0,
            ]);

            $this->refreshCourse();
            $this->generatedContent = null;
            $this->lessonTitle = '';
            $this->generatedExercises = null;

            $this->dispatch('lessonCreated', $lesson->id);

        } catch (\Exception $e) {
            $this->addError('lesson_error', $e->getMessage());
        }
    }

    private function formatContentForDb($content): string
    {
        $html = '<h2>Introduction</h2><p>' . $content['introduction'] . '</p>';

        foreach ($content['sections'] as $section) {
            $html .= '<h3>' . $section['title'] . '</h3>';
            $html .= '<p>' . $section['content'] . '</p>';
        }

        if (!empty($content['examples'])) {
            $html .= '<h3>Exemples</h3><ul>';
            foreach ($content['examples'] as $example) {
                $html .= '<li>' . $example . '</li>';
            }
            $html .= '</ul>';
        }

        $html .= '<h3>Conclusion</h3><p>' . $content['conclusion'] . '</p>';

        if (!empty($content['reflection_question'])) {
            $html .= '<h3>Question de réflexion</h3><p><em>' . $content['reflection_question'] . '</em></p>';
        }

        return $html;
    }

    // ─── QUIZ ────────────────────────────────────────────────────

    public function generateQuiz()
    {
        $this->validate([
            'quizTopic' => 'required|string|max:255',
            'quizCount' => 'required|integer|min:3|max:20',
        ]);

        $this->isGeneratingQuiz = true;

        try {
            $ai = app(AIService::class);
            $this->generatedQuiz = $ai->generateQuiz(
                $this->quizTopic,
                $this->quizCount,
                $this->quizDifficulty
            );
        } catch (\Exception $e) {
            $this->addError('quiz_error', $e->getMessage());
        }

        $this->isGeneratingQuiz = false;
    }

    public function saveQuiz()
    {
        if (!$this->generatedQuiz || !$this->course) return;

        try {
            $quiz = \App\Models\Quiz::create([
                'course_id' => $this->course->id,
                'title' => 'Quiz : ' . $this->quizTopic,
                'description' => 'Quiz généré par l\'assistant sur ' . $this->quizTopic,
                'pass_score' => 60,
                'max_attempts' => 3,
                'show_answers' => true,
            ]);

            foreach ($this->generatedQuiz as $qData) {
                $question = \App\Models\Question::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $qData['question'],
                    'type' => $qData['type'] ?? 'single',
                    'points' => 1,
                    'order' => 0,
                ]);

                if (isset($qData['answers'])) {
                    foreach ($qData['answers'] as $index => $answerText) {
                        \App\Models\Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $answerText,
                            'is_correct' => $index === $qData['correct'],
                            'order' => $index,
                        ]);
                    }
                }
            }

            $this->generatedQuiz = null;
            $this->quizTopic = '';
            $this->dispatch('quizSaved', $quiz->id);

        } catch (\Exception $e) {
            $this->addError('quiz_save_error', $e->getMessage());
        }
    }

    // ─── OPTIMISATION ────────────────────────────────────────────

    public function optimizeCourse()
    {
        if (!$this->course) return;

        $this->isOptimizing = true;

        try {
            $chaptersData = $this->course->chapters->map(function ($chapter) {
                return [
                    'title' => $chapter->title,
                    'description' => $chapter->description,
                ];
            })->toArray();

            $ai = app(AIService::class);
            $this->optimizationResults = $ai->optimizeCourse(
                $this->course->title,
                $this->course->description,
                $chaptersData
            );
        } catch (\Exception $e) {
            $this->addError('optimize_error', $e->getMessage());
        }

        $this->isOptimizing = false;
    }

    public function render()
    {
        $chapters = $this->course ? $this->course->chapters : collect();
        return view('livewire.instructor-assistant', [
            'chapters' => $chapters,
        ]);
    }
}