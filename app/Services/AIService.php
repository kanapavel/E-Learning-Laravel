<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';
    protected $model = 'llama-3.1-8b-instant';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
    }

    public function generateText(string $prompt, string $systemPrompt = '', float $temperature = 0.7, int $maxTokens = 1024): string
    {
        $messages = [];

        if ($systemPrompt) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }

        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post($this->baseUrl, [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => $temperature,
            'max_tokens'  => $maxTokens,
        ]);

        if ($response->failed()) {
            Log::error('Groq API error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Erreur IA : ' . $response->body());
        }

        return trim($response->json('choices.0.message.content', ''));
    }

    public function chat(string $question, string $courseContext = '', string $userHistory = ''): array
    {
        try {
            $system = "Tu es un assistant pédagogique bienveillant sur Skillora. Tu réponds en français, de façon claire et concise (max 3 paragraphes). Tu encourages l'apprentissage avec des exemples concrets.";

            $prompt = '';
            if ($courseContext) $prompt .= "Contexte : $courseContext\n\n";
            if ($userHistory)   $prompt .= "Historique :\n$userHistory\n\n";
            $prompt .= "Question : $question";

            $answer = $this->generateText($prompt, $system, 0.7, 512);

            return [
                'answer' => $answer,
                'suggested_questions' => $this->generateSuggestedQuestions($question),
            ];
        } catch (\Exception $e) {
            // ✅ FALLBACK : réponse simulée avec l'erreur pour déboguer
            return [
                'answer' => "⚠️ Je n'arrive pas à contacter l'API IA. Voici l'erreur : " . $e->getMessage() . "\n\nEn attendant, voici une réponse simulée : Pensez à vérifier votre clé API Groq dans le fichier .env et à vous assurer que votre compte est actif.",
                'suggested_questions' => [
                    'Comment configurer la clé API ?',
                    'Où trouver mon token Groq ?',
                    'Quels sont les modèles disponibles ?',
                ],
            ];
        }
    }

    private function generateSuggestedQuestions(string $question): array
    {
        try {
            $prompt = "À partir de : '$question', propose 3 questions courtes pour approfondir.
Réponds UNIQUEMENT au format JSON : [\"question 1\", \"question 2\", \"question 3\"]";

            $response = $this->generateText($prompt, '', 0.5, 150);
            $clean    = preg_replace('/^[^\[]*|\][^\]]*$/s', '', $response);
            $questions = json_decode(trim($clean), true);

            if (is_array($questions) && count($questions) === 3) return $questions;
        } catch (\Exception $e) {}

        return [
            'Pouvez-vous me donner un exemple ?',
            'Quels sont les points difficiles ?',
            'Comment l\'appliquer en pratique ?',
        ];
    }

    public function generateCoursePlan(string $subject, string $level, string $duration, string $targetAudience): array
    {
        $prompt = "Génère un plan de cours JSON pour : '$subject', niveau $level, durée $duration, public : $targetAudience.
Réponds UNIQUEMENT en JSON valide :
{\"title\":\"\",\"description\":\"\",\"objectives\":[],\"prerequisites\":[],\"chapters\":[{\"title\":\"\",\"description\":\"\",\"nb_lessons\":3,\"key_points\":[]}]}";

        $response = $this->generateText($prompt, '', 0.4, 2048);
        $json     = $this->extractJson($response);
        $decoded  = json_decode($json, true);

        return $decoded ?? $this->fallbackCoursePlan($subject);
    }

    public function generateLessonContent(string $chapterTitle, string $lessonTitle, string $level, array $options = []): array
    {
        $length = ['court' => '200 mots', 'moyen' => '400 mots', 'long' => '700 mots'][$options['length'] ?? 'moyen'];

        $prompt = "Génère le contenu d'une leçon en $length sur '$lessonTitle' (chapitre : $chapterTitle, niveau : $level).
Réponds UNIQUEMENT en JSON :
{\"introduction\":\"\",\"sections\":[{\"title\":\"\",\"content\":\"\"}],\"examples\":[],\"conclusion\":\"\",\"reflection_question\":\"\"}";

        $response = $this->generateText($prompt, '', 0.6, 2048);
        $decoded  = json_decode($this->extractJson($response), true);

        return $decoded ?? [
            'introduction'       => "Introduction à $lessonTitle.",
            'sections'           => [['title' => 'Contenu principal', 'content' => 'Contenu de la leçon.']],
            'examples'           => ['Exemple pratique'],
            'conclusion'         => 'Résumé des points clés.',
            'reflection_question'=> 'Comment appliquez-vous ce concept ?',
        ];
    }

    public function generateExercises(string $topic, string $level, int $nb = 3): array
    {
        $prompt = "Génère $nb exercices sur '$topic' niveau $level.
JSON : [{\"title\":\"\",\"description\":\"\",\"instructions\":[],\"solution\":\"\",\"hint\":\"\"}]";

        $response = $this->generateText($prompt, '', 0.5, 2048);
        $decoded  = json_decode($this->extractJson($response), true);
        return is_array($decoded) ? $decoded : [];
    }

    public function generateQuiz(string $topic, int $nb = 10, string $difficulty = 'moyen', array $options = []): array
    {
        $prompt = "Génère $nb questions QCM sur '$topic', difficulté $difficulty.
JSON : [{\"type\":\"qcm\",\"question\":\"\",\"answers\":[\"A\",\"B\",\"C\",\"D\"],\"correct\":0}]
correct = index (0-3) de la bonne réponse. Réponds UNIQUEMENT en JSON.";

        $response = $this->generateText($prompt, '', 0.3, 2048);
        $decoded  = json_decode($this->extractJson($response), true);
        return is_array($decoded) ? $decoded : $this->fallbackQuiz($topic, $nb);
    }

    public function optimizeCourse(string $title, string $description, array $chapters): array
    {
        $chaptersText = collect($chapters)->map(fn($c) => "- {$c['title']}: {$c['description']}")->join("\n");

        $prompt = "Analyse ce cours '$title' et propose des améliorations.
Chapitres :\n$chaptersText
JSON : {\"structure\":[],\"engagement\":[],\"clarity\":[],\"resources\":[],\"assessments\":[]}";

        $response = $this->generateText($prompt, '', 0.5, 1500);
        $decoded  = json_decode($this->extractJson($response), true);

        return $decoded ?? [
            'structure'   => ['Ajouter une introduction engageante'],
            'engagement'  => ['Intégrer des quiz interactifs'],
            'clarity'     => ['Simplifier le langage'],
            'resources'   => ['Proposer des lectures complémentaires'],
            'assessments' => ['Créer des quiz d\'auto-évaluation'],
        ];
    }

    private function extractJson(string $text): string
    {
        // Extraire le premier bloc JSON valide
        if (preg_match('/(\{.*\}|\[.*\])/s', $text, $m)) {
            return $m[1];
        }
        return $text;
    }

    private function fallbackCoursePlan(string $subject): array
    {
        return [
            'title'         => "Formation : $subject",
            'description'   => "Cours complet sur $subject.",
            'objectives'    => ["Comprendre les bases", "Maîtriser les concepts"],
            'prerequisites' => ["Aucun prérequis"],
            'chapters'      => [
                ['title' => "Introduction", 'description' => "Bases", 'nb_lessons' => 3, 'key_points' => ["Point 1"]],
                ['title' => "Approfondissement", 'description' => "Avancé", 'nb_lessons' => 4, 'key_points' => ["Point 1"]],
            ],
        ];
    }

    private function fallbackQuiz(string $topic, int $nb): array
    {
        return array_fill(0, $nb, [
            'type'     => 'qcm',
            'question' => "Question sur $topic",
            'answers'  => ['Réponse A (correcte)', 'Réponse B', 'Réponse C', 'Réponse D'],
            'correct'  => 0,
        ]);
    }

   /**
 * Génère un résumé structuré d'un cours
 */
    public function summarizeCourse(string $title, string $description, array $chapters): string
    {
        $chaptersText = collect($chapters)->map(function($ch) {
            return "- " . $ch['title'] . ": " . ($ch['description'] ?? '');
        })->join("\n");

        $prompt = "Génère un résumé structuré du cours suivant :

        Titre : $title
        Description : $description
        Chapitres :
        $chaptersText

        Le résumé doit contenir :
        1. Introduction (2-3 phrases)
        2. 5 points clés (avec une brève explication pour chacun)
        3. Compétences acquises à la fin du cours (3-4)

        Réponds en français, de façon claire, structurée et pédagogique. Utilise un ton engageant.";

        return $this->generateText($prompt, '', 0.5, 800);
    }
}