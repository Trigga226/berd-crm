<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class MistralCVParser
{
    protected ?string $apiKey;
    protected string $baseUrl = 'https://api.mistral.ai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.mistral.key');
    }

    /**
     * Parse a PDF CV and return structured data
     *
     * @param string $filePath Absolute path to the PDF file
     * @return array
     * @throws Exception
     */
    public function parse(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        if (empty($this->apiKey)) {
            Log::warning('Mistral API Key is missing');
            return [];
        }

        // 1. Extract Text from PDF
        $parser = new Parser();
        try {
            $pdf = $parser->parseFile($filePath);
            $text = $pdf->getText();

            // Sanitize UTF-8
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text, mb_detect_order(), true) ?: 'ISO-8859-1');
            }
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);

            Log::info("CV Parser - Text extracted: " . substr($text, 0, 100) . "...");

            if (empty(trim($text))) {
                Log::warning("CV Parser - Empty text extracted from PDF");
                return [];
            }
        } catch (Exception $e) {
            Log::error("PDF Parsing Error: " . $e->getMessage());
            throw new Exception("Could not read PDF file.");
        }

        // 2. Call Mistral API
        $prompt = <<<EOT
Tu es un assistant RH expert. Analyse ce CV (texte brut ci-dessous) et extrait les informations suivantes au format JSON strict uniquement (pas de markdown, pas de texte avant/après).

Champs requis :
- first_name (string, null si non trouvé)
- last_name (string, null si non trouvé)
- email (string, null si non trouvé)
- phone (string, null si non trouvé, format international si possible)
- years_of_experience (integer, calcule le nombre total d'années d'expérience professionnelle pertinent. Par défaut 0. Sois précis.)
- skills (array of strings, compétences techniques et comportementales clés)
- formations (array of objects { "diplome": string, "ecole": string, "date_obtention": string|null }. Limite aux 3 plus pertinentes.)
- experiences (array of objects { "poste": string, "entreprise": string, "duree": string|null }. Limite aux 3 plus récentes.)

Texte du CV :
----------------
{$text}
----------------
JSON :
EOT;

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->baseUrl, [
                    'model' => 'mistral-medium', // ou mistral-small selon crédits
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.1,
                    'response_format' => ['type' => 'json_object']
                ]);

            if ($response->failed()) {
                Log::error("Mistral API Error: " . $response->body());
                throw new Exception("Mistral API failed.");
            }

            $content = $response->json('choices.0.message.content');
            Log::info("CV Parser - API Response: " . $content);

            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error("CV Parser - JSON Decode Error: " . json_last_error_msg());
            }

            return $data ?? [];
        } catch (Exception $e) {
            Log::error("CV Parsing/Mistral Error: " . $e->getMessage());
            // On ne bloque pas tout le process, on retourne un tableau vide ou partiel
            return ['full_cv_text' => $text]; // Au moins on a le texte
        }
    }
}
