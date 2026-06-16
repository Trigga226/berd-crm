<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MistralAnalysisService
{
    protected ?string $apiKey;
    // Define models with fallback
    protected array $availableModels = [
        'mistral-large-latest',
        'mistral-small-latest',
    ];
    protected string $baseUrl = 'https://api.mistral.ai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.mistral.key');
    }

    /**
     * Send a chat request to Mistral with optional tools.
     * Handles model fallback automatically.
     *
     * @param array $messages Full conversation history (role/content).
     * @param array $tools Optional list of tools definition.
     * @param string $toolChoice Strategy for tools ('auto', 'any', 'none').
     * @return \Illuminate\Http\Client\Response The raw HTTP response (to handle status/tools in caller).
     */
    public function chat(array $messages, array $tools = [], string $toolChoice = 'auto'): \Illuminate\Http\Client\Response
    {
        $messages = $this->sanitizeMessages($messages);
        if (empty($this->apiKey)) {
            throw new Exception("Clé API Mistral non configurée.");
        }

        $lastException = null;

        foreach ($this->availableModels as $model) {
            try {
                $payload = [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                ];

                if (!empty($tools)) {
                    $payload['tools'] = $tools;
                    $payload['tool_choice'] = $toolChoice;
                }

                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::withToken($this->apiKey)
                    ->timeout(120)
                    ->post($this->baseUrl, $payload);

                // If success or valid API response (even if it's a tool call), return it
                if ($response->successful()) {
                    return $response;
                }

                // If it's a rate limit or capacity issue, continue to next model
                if ($response->status() === 429 || str_contains($response->body(), 'capacity_exceeded')) {
                    continue;
                }

                // Other errors are likely fatal for this request
                throw new Exception("Mistral Error ({$model}): " . $response->body());
            } catch (Exception $e) {
                $lastException = $e;
                continue;
            }
        }

        throw $lastException ?? new Exception("Impossible de contacter Mistral API après plusieurs essais.");
    }

    /**
     * Analyse un AvisManifestation (PDF + description) et extrait :
     * domains[], summary (string), score (0-10).
     */
    public function analyzeAvis(\App\Models\AvisManifestation $avis): array
    {
        $content = $avis->description ?? '';

        if ($avis->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($avis->file_path)) {
            try {
                $parser  = new \Smalot\PdfParser\Parser();
                $pdf     = $parser->parseFile(\Illuminate\Support\Facades\Storage::disk('public')->path($avis->file_path));
                $content .= "\n\n" . substr($pdf->getText(), 0, 6000);
            } catch (\Exception $e) {
                Log::warning('AvisManifestation PDF parse failed: ' . $e->getMessage());
            }
        }

        if (empty(trim($content))) {
            throw new Exception("Aucun contenu disponible pour l'analyse (ni description, ni PDF).");
        }

        $domains = implode(', ', array_keys(\App\Utils\Domaines::$DOMAINES));

        $systemPrompt = <<<PROMPT
Tu es un expert en analyse d'appels à manifestation d'intérêt. Analyse le texte suivant et réponds UNIQUEMENT avec un objet JSON valide (sans markdown, sans explications) contenant :
- "domains": un tableau de 1 à 5 domaines choisis parmi cette liste exacte: [{$domains}]
- "summary": un résumé exécutif en français de 3 à 5 phrases
- "score": un nombre décimal entre 0 et 10 représentant la pertinence pour un cabinet d'ingénierie conseil africain

Réponds uniquement avec du JSON pur, exemple: {"domains":["BTP","Hydraulique"],"summary":"...","score":7.5}
PROMPT;

        try {
            $response = $this->chat([
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user',   'content' => $content],
            ]);

            $rawContent = $response->json('choices.0.message.content') ?? '{}';
            $cleanContent = trim(preg_replace('/^```json\s*|\s*```$/s', '', $rawContent));
            $result = json_decode($cleanContent, true);

            if (!is_array($result)) {
                throw new Exception("Réponse IA non parseable : {$rawContent}");
            }

            return [
                'domains' => array_filter((array) ($result['domains'] ?? []), fn($d) => isset(\App\Utils\Domaines::$DOMAINES[$d])),
                'summary' => $result['summary'] ?? '',
                'score'   => is_numeric($result['score'] ?? null) ? round((float) $result['score'], 1) : null,
            ];
        } catch (Exception $e) {
            Log::error('MistralAnalysisService::analyzeAvis failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Backward compatibility wrapper for simple analysis.
     */
    public function analyzeCandidates(array $experts, string $criteria): string
    {
        // Construct the prompt
        $candidatesText = "";
        foreach ($experts as $index => $expert) {
            $candidatesText .= "--- CANDIDAT " . ($index + 1) . ": " . $expert['name'] . " ---\n";
            $candidatesText .= substr($expert['cv_text'] ?? 'Pas de CV', 0, 3000) . "\n\n";
        }

        $systemPrompt = <<<EOT
Tu es un expert en recrutement. Ta mission est d'analyser les profils des candidats ci-dessous et de les classer selon les critères suivants fournis par l'utilisateur.

CRITÈRES DE RECRUTEMENT :
{$criteria}

INSTRUCTIONS :
1. Analyse chaque candidat par rapport aux critères.
2. Établis un classement du meilleur au moins bon.
3. Pour chaque candidat, donne :
   - Son rang.
   - Une justification concise (points forts/faibles vis-à-vis des critères).
   - Un score de pertinence sur 100.
4. Conclus avec une recommandation finale.

Réponds en français, format Markdown clair.
EOT;

        try {
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $candidatesText]
            ];

            $response = $this->chat($messages);
            return $response->json('choices.0.message.content') ?? "Pas de réponse de l'IA.";
        } catch (Exception $e) {
            Log::error("Mistral Service Exception: " . $e->getMessage());
            return "Une erreur technique est survenue : " . $e->getMessage();
        }
    }
    /**
     * Recursively sanitize strings in messages for UTF-8 validity
     */
    private function sanitizeMessages(array $messages): array
    {
        return array_map(function ($message) {
            if (isset($message['content']) && is_string($message['content'])) {
                $content = $message['content'];
                if (!mb_check_encoding($content, 'UTF-8')) {
                    $content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, mb_detect_order(), true) ?: 'ISO-8859-1');
                }
                $message['content'] = iconv('UTF-8', 'UTF-8//IGNORE', $content);
            }
            return $message;
        }, $messages);
    }
}
