<?php

namespace App\Livewire;

use App\Services\AIService;
use Livewire\Component;

class AiAssistant extends Component
{
    public $isOpen = false;
    public $messages = [];
    public $question = '';
    public $isLoading = false;
    public $summaryLoading = false;
    public $courseId = null;
    public $courseContext = '';
    public $suggestedQuestions = [];

    protected $listeners = ['refreshAssistant'];

    /**
     * Montage du composant avec récupération du courseId
     */
    public function mount($courseId = null)
    {
        if ($courseId) {
            $this->courseId = $courseId;
            $course = \App\Models\Course::find($courseId);
            if ($course) {
                $this->courseContext = "Cours : " . $course->title . "\nDescription : " . $course->description;
            }
        }

        $this->messages = session()->get('ai_assistant_messages', [
            ['role' => 'assistant', 'content' => '👋 Bonjour ! Je suis votre assistant pédagogique. Posez-moi vos questions !']
        ]);
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function refreshAssistant($courseId)
    {
        $this->courseId = $courseId;
        $course = \App\Models\Course::find($courseId);
        if ($course) {
            $this->courseContext = "Cours : " . $course->title . "\nDescription : " . $course->description;
        }
    }

    public function ask()
    {
        $this->validate(['question' => 'required|string|max:1000']);

        $question = $this->question;

        // Détection d'une demande de résumé
        $keywords = ['résumé', 'synthèse', 'resume', 'synthèse du cours', 'résume ce cours', 'synthétise', 'récap', 'synthèse de cours'];
        if ($this->courseId && preg_match('/(' . implode('|', array_map('preg_quote', $keywords)) . ')/i', $question)) {
            $this->question = '';
            $this->generateSummary();
            return;
        }

        // Comportement normal
        $this->messages[] = ['role' => 'user', 'content' => $question];
        session()->put('ai_assistant_messages', $this->messages);

        $this->question = '';
        $this->isLoading = true;

        try {
            $ai = app(AIService::class);
            $response = $ai->chat($question, $this->courseContext, '');

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $response['answer'],
            ];
            $this->suggestedQuestions = $response['suggested_questions'] ?? [];

            session()->put('ai_assistant_messages', $this->messages);

        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => '❌ Désolé, une erreur s\'est produite : ' . $e->getMessage(),
            ];
        } finally {
            $this->isLoading = false;
        }

        $this->dispatch('scrollToBottom');
    }

    public function generateSummary()
    {
        if (!$this->courseId) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => '❌ Aucun cours sélectionné pour générer une synthèse. Ouvrez un cours d\'abord.'
            ];
            session()->put('ai_assistant_messages', $this->messages);
            $this->dispatch('scrollToBottom');
            return;
        }

        $course = \App\Models\Course::with('chapters')->find($this->courseId);
        if (!$course) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => '❌ Cours introuvable.'
            ];
            session()->put('ai_assistant_messages', $this->messages);
            $this->dispatch('scrollToBottom');
            return;
        }

        $chapters = $course->chapters->map(function($ch) {
            return ['title' => $ch->title, 'description' => $ch->description];
        })->toArray();

        $this->summaryLoading = true;

        try {
            $ai = app(AIService::class);
            $summary = $ai->summarizeCourse($course->title, $course->description, $chapters);

            $this->messages[] = [
                'role' => 'assistant',
                'content' => "📄 **Synthèse du cours : {$course->title}**\n\n" . $summary
            ];
            session()->put('ai_assistant_messages', $this->messages);

        } catch (\Exception $e) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => '❌ Désolé, une erreur s\'est produite lors de la génération du résumé : ' . $e->getMessage()
            ];
            session()->put('ai_assistant_messages', $this->messages);
        } finally {
            $this->summaryLoading = false;
        }

        $this->dispatch('scrollToBottom');
    }

    public function askSuggestion($question)
    {
        $this->question = $question;
        $this->ask();
    }

    public function clearHistory()
    {
        $this->messages = [
            ['role' => 'assistant', 'content' => '👋 Bonjour ! Je suis votre assistant pédagogique. Posez-moi vos questions !']
        ];
        session()->put('ai_assistant_messages', $this->messages);
        $this->suggestedQuestions = [];
    }

    public function render()
    {
        return view('livewire.ai-assistant');
    }
}