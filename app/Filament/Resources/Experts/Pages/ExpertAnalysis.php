<?php

namespace App\Filament\Resources\Experts\Pages;

use App\Filament\Resources\Experts\ExpertResource;
use App\Models\Expert;
use App\Services\MistralAnalysisService;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ExpertAnalysis extends Page
{
    protected static string $resource = ExpertResource::class;

    use \Livewire\Features\SupportFileUploads\WithFileUploads;
    use \Filament\Actions\Concerns\InteractsWithActions;

    protected string $view = 'filament.resources.experts.pages.expert-analysis';

    public $expertIds = [];
    public $userInput = '';
    public $attachment;
    public $messages = [];
    public $isAnalyzing = false;

    public function mount()
    {
        // Get experts IDs from query string or session
        $ids = request()->query('ids');
        if ($ids) {
            $this->expertIds = explode(',', $ids);
        }

        // Welcome message
        $this->messages[] = [
            'role' => 'assistant',
            'content' => 'Bonjour ! Je suis prête à analyser les experts sélectionnés. Posez-moi une question ou donnez-moi vos critères.',
            'time' => now()->format('H:i')
        ];
    }

    public function getExpertsProperty()
    {
        return Expert::whereIn('id', $this->expertIds)->get();
    }

    public function sendMessage()
    {
        if (empty(trim($this->userInput)) && !$this->attachment) {
            return;
        }

        $this->isAnalyzing = true;

        // 1. Build User Content
        $userContent = $this->userInput;
        $attachmentName = '';

        if ($this->attachment) {
            try {
                $text = $this->extractTextFromFile($this->attachment->getRealPath(), $this->attachment->getMimeType());
                if (!empty($text)) {
                    $userContent .= "\n\n[Fichier joint '{$this->attachment->getClientOriginalName()}']:\n" . $text;
                    $attachmentName = $this->attachment->getClientOriginalName();
                }
            } catch (\Exception $e) {
                Notification::make()->title('Erreur lecture fichier')->body($e->getMessage())->warning()->send();
            }
        }

        // Add to UI history
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userInput . ($attachmentName ? " 📎 $attachmentName" : ''),
            'time' => now()->format('H:i')
        ];

        // 2. Prepare Context (Experts Data + History)
        $apiMessages = [];

        // System Prompt
        $systemPrompt = "Tu es l'assistant RH de BERD. Ta mission est d'analyser les profils d'experts.\n" .
            "Utilise les données fournies pour répondre.\n" .
            "Si on te demande de classer, sois rigoureux et justifie.\n" .
            "Tu peux chercher sur le web si nécessaire (infos marché, technos...).";

        $apiMessages[] = ['role' => 'system', 'content' => $systemPrompt];

        // Inject Experts Context
        if (!$this->experts->isEmpty()) {
            $expertsContext = "Voici les profils des experts sélectionnés :\n";
            foreach ($this->experts as $expert) {
                $cvText = $this->getExpertCvText($expert);
                $expertsContext .= "--- EXPERT: {$expert->first_name} {$expert->last_name} ---\n";
                $expertsContext .= "Titre: {$expert->title}\n";
                $expertsContext .= "Expérience: {$expert->years_of_experience} ans\n";
                $expertsContext .= "CV Brut: " . substr($cvText, 0, 3000) . "\n\n";
            }
            $apiMessages[] = ['role' => 'user', 'content' => $expertsContext];
            $apiMessages[] = ['role' => 'assistant', 'content' => "Bien reçu. J'ai analysé les profils. Je suis prêt."];
        }

        // Append conversation history
        foreach ($this->messages as $msg) {
            // Skip the initial assistant message if there's experts context, or just skip clearly
            if ($msg['content'] === 'Bonjour ! Je suis prête à analyser les experts sélectionnés. Posez-moi une question ou donnez-moi vos critères.') continue;

            if ($msg === end($this->messages)) {
                $apiMessages[] = ['role' => 'user', 'content' => $userContent];
            } else {
                $apiMessages[] = ['role' => $msg['role'], 'content' => $msg['content']];
            }
        }

        try {
            $service = new MistralAnalysisService();

            // Call API (Simple chat without tools)
            $response = $service->chat($apiMessages);
            $responseData = $response->json();
            $finalContent = $responseData['choices'][0]['message']['content'] ?? "Pas de réponse de l'IA.";

            $this->messages[] = [
                'role' => 'assistant',
                'content' => $finalContent,
                'time' => now()->format('H:i')
            ];
        } catch (\Exception $e) {
            Log::error($e);
            Notification::make()->title("Erreur: " . $e->getMessage())->danger()->send();
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "❌ Une erreur est survenue : " . $e->getMessage(),
                'time' => now()->format('H:i')
            ];
        } finally {
            $this->isAnalyzing = false;
            $this->userInput = '';
            $this->attachment = null;
        }
    }

    protected function getExpertCvText($expert)
    {
        $text = $expert->full_cv_text;

        if (empty($text) && $expert->cv_path) {
            try {
                $path = storage_path('app/public/' . $expert->cv_path);
                if (file_exists($path)) {
                    $text = $this->extractTextFromFile($path, mime_content_type($path));
                }
            } catch (\Exception $e) {
            }
        }

        if (empty($text)) return "Info de base : " . $expert->first_name . " " . $expert->last_name;

        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text, mb_detect_order(), true) ?: 'ISO-8859-1');
        }
        return iconv('UTF-8', 'UTF-8//IGNORE', $text);
    }

    private function extractTextFromFile(string $path, string $mime): string
    {
        try {
            if (str_contains($mime, 'pdf')) {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                $text = $pdf->getText();
            } else {
                $text = file_get_contents($path);
            }

            if (empty($text)) return "";

            // Sanitize UTF-8
            if (!mb_check_encoding($text, 'UTF-8')) {
                $text = mb_convert_encoding($text, 'UTF-8', mb_detect_encoding($text, mb_detect_order(), true) ?: 'ISO-8859-1');
            }
            return iconv('UTF-8', 'UTF-8//IGNORE', $text);
        } catch (\Exception $e) {
            return "";
        }
    }

    public function exportPdfAction(): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make('exportPdf')
            ->label('Exporter PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () {
                if (empty($this->messages)) {
                    Notification::make()->title('Rien à exporter')->warning()->send();
                    return;
                }

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament.resources.experts.pages.expert-analysis-pdf', [
                    'messages' => $this->messages,
                    'date' => now()->format('d/m/Y H:i'),
                    'experts' => $this->experts
                ]);

                return response()->streamDownload(
                    fn() => print($pdf->output()),
                    'analyse-experts-' . now()->format('Ymd_His') . '.pdf'
                );
            });
    }
}
