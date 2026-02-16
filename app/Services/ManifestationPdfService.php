<?php

namespace App\Services;

use App\Models\Manifestation;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class ManifestationPdfService
{
    protected array $tempFiles = [];

    public function generate(Manifestation $manifestation): string
    {
        $pdf = new Fpdi();

        $documentTypes = [
            'page_garde' => 'pageGardeDocuments',
            'sommaire' => 'sommaireDocuments',
            'lettre' => 'lettreDocuments',
            'piece_admin' => 'pieceAdminDocuments',
            'presentation' => 'presentationDocuments',
            'adresse' => 'adresseDocuments',
            'reference' => 'referenceDocuments',
        ];

        // 1. Process Standard Documents
        foreach ($documentTypes as $type => $relation) {
            foreach ($manifestation->$relation as $doc) {
                $this->addPdfToMerge($pdf, $doc->file_path);
            }
        }

        // 2. Process Partners Documents (Partner Admin & Presentation if logically stored separately? 
        // The prompt says: "4- Nos pieces administratives (en cas de groupement les pièces administratives des partenaires se rajoutent)"
        // Since I didn't create a complex structure for partner docs inside Manifestation (user didn't specify exact partner doc structure inside manifestation, but Partner model has documents),
        // I will assume for now we only merge what is uploaded directly to the Manifestation via the tabs.
        // If partner docs are uploaded in the "Pièces Administratives" tab (which allows multiple), they are covered above.
        // If the user meant auto-pulling from Partner model, that's a complex logic not fully detailed, assuming manual upload for now as per Form schema.

        // 3. Process Experts CVs
        foreach ($manifestation->experts as $expert) {
            if ($expert->pivot->cv_path) {
                $this->addPdfToMerge($pdf, $expert->pivot->cv_path);
            }
        }

        // Output
        $avisTitle = $manifestation->avisManifestation?->title ?? 'manifestation_' . $manifestation->id;
        $slug = \Illuminate\Support\Str::slug($avisTitle);
        $fileName = "manifestations/{$slug}/manifestation_complete.pdf";

        // Ensure directory exists
        Storage::disk('public')->makeDirectory("manifestations/{$slug}");

        $outputPath = Storage::disk('public')->path($fileName);
        $pdf->Output($outputPath, 'F');

        $manifestation->update(['generated_file_path' => $fileName]);

        // Cleanup temp files
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return $fileName;
    }

    protected function addPdfToMerge(Fpdi $pdf, string $path)
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!file_exists($fullPath)) {
            return;
        }

        try {
            $pageCount = $pdf->setSourceFile($fullPath);
        } catch (\Exception $e) {
            // Attempt normalization if likely due to version/compression
            try {
                $tempPath = $this->convertPdfTo14($fullPath);
                $this->tempFiles[] = $tempPath;
                $pageCount = $pdf->setSourceFile($tempPath);
            } catch (\Exception $conversionError) {
                // If conversion fails, log original error
                \Illuminate\Support\Facades\Log::error("Failed to merge PDF: {$path}. Error: " . $e->getMessage());
                return;
            }
        }

        for ($page = 1; $page <= $pageCount; $page++) {
            $templateId = $pdf->importPage($page);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
        }
    }

    protected function convertPdfTo14(string $originalPath): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'pdf_14_') . '.pdf';

        $command = sprintf(
            'gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=%s %s',
            escapeshellarg($tempPath),
            escapeshellarg($originalPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception("Ghostscript conversion failed with exit code $returnCode");
        }

        return $tempPath;
    }
}
