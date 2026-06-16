<?php

namespace App\Services;

use App\Models\Manifestation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class ManifestationPdfService
{
    protected array $tempFiles  = [];
    protected int   $totalPages = 0;

    public function generate(Manifestation $manifestation): string
    {
        $this->tempFiles  = [];
        $this->totalPages = 0;

        $pdf = new Fpdi();

        $documentTypes = [
            'page_garde'  => 'pageGardeDocuments',
            'sommaire'    => 'sommaireDocuments',
            'lettre'      => 'lettreDocuments',
            'piece_admin' => 'pieceAdminDocuments',
            'presentation'=> 'presentationDocuments',
            'adresse'     => 'adresseDocuments',
            'reference'   => 'referenceDocuments',
            'autre'       => 'autreDocuments',
        ];

        // 1. Documents standard de la manifestation
        foreach ($documentTypes as $type => $relation) {
            $docs = $manifestation->$relation;
            foreach ($docs as $doc) {
                if (!empty($doc->file_path)) {
                    $this->addPdfToMerge($pdf, $doc->file_path, "[{$type}]");
                }
            }
        }

        // 2. CVs des experts assignés (cv spécifique pivot, sinon cv principal)
        foreach ($manifestation->experts as $expert) {
            $cvPath = $expert->pivot->cv_path ?: $expert->cv_path;
            if ($cvPath) {
                $name = $expert->first_name . ' ' . $expert->last_name;
                $this->addPdfToMerge($pdf, $cvPath, "[CV:{$name}]");
            }
        }

        Log::info("ManifestationPdfService: {$this->totalPages} page(s) importée(s) pour manifestation #{$manifestation->id}");

        if ($this->totalPages === 0) {
            $this->cleanup();
            throw new \RuntimeException(
                "Aucun document PDF valide trouvé pour cette manifestation (id:{$manifestation->id}). " .
                "Vérifiez que les fichiers sont bien uploadés et accessibles dans storage/app/public/."
            );
        }

        $avisTitle = $manifestation->avisManifestation?->title ?? 'manifestation_' . $manifestation->id;
        $slug      = \Illuminate\Support\Str::slug($avisTitle);
        $fileName  = "manifestations/{$slug}/manifestation_complete.pdf";

        Storage::disk('public')->makeDirectory("manifestations/{$slug}");
        $outputPath = Storage::disk('public')->path($fileName);

        $pdf->Output($outputPath, 'F');
        $this->cleanup();

        $manifestation->update(['generated_file_path' => $fileName]);

        return $fileName;
    }

    protected function addPdfToMerge(Fpdi $pdf, string $path, string $label = ''): void
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!file_exists($fullPath)) {
            Log::warning("ManifestationPdfService {$label}: fichier introuvable → {$fullPath}");
            return;
        }

        try {
            $pageCount = $pdf->setSourceFile($fullPath);
        } catch (\Exception $e) {
            Log::info("ManifestationPdfService {$label}: lecture directe échouée ({$e->getMessage()}), tentative Ghostscript → {$fullPath}");
            try {
                $tempPath        = $this->convertPdfTo14($fullPath);
                $this->tempFiles[] = $tempPath;
                $pageCount       = $pdf->setSourceFile($tempPath);
            } catch (\Exception $conversionError) {
                Log::error("ManifestationPdfService {$label}: impossible de fusionner {$path} — {$conversionError->getMessage()}");
                return;
            }
        }

        for ($page = 1; $page <= $pageCount; $page++) {
            $templateId = $pdf->importPage($page);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
            $this->totalPages++;
        }

        Log::debug("ManifestationPdfService {$label}: +{$pageCount} page(s) depuis {$path}");
    }

    protected function convertPdfTo14(string $originalPath): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'pdf_14_') . '.pdf';
        $null     = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';

        // Normalize path separators — mixed slashes cause Ghostscript to fail on Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $originalPath = str_replace('/', DIRECTORY_SEPARATOR, $originalPath);
            $tempPath     = str_replace('/', DIRECTORY_SEPARATOR, $tempPath);
        }

        $binaries = PHP_OS_FAMILY === 'Windows'
            ? $this->findGhostscriptBinaries()
            : ['gs'];

        foreach ($binaries as $bin) {
            $command = sprintf(
                '%s -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -sOutputFile=%s %s 2>%s',
                escapeshellarg($bin),
                escapeshellarg($tempPath),
                escapeshellarg($originalPath),
                $null
            );

            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($tempPath) && filesize($tempPath) > 0) {
                return $tempPath;
            }
        }

        throw new \Exception("Ghostscript introuvable ou conversion échouée pour : {$originalPath}");
    }

    protected function findGhostscriptBinaries(): array
    {
        // Start with PATH-based names (work if Ghostscript was added to PATH)
        $binaries = ['gswin64c', 'gswin32c', 'gs'];

        // Search common Windows installation directories and prepend found paths
        $roots = ['C:\\Program Files\\gs', 'C:\\Program Files (x86)\\gs'];
        foreach ($roots as $root) {
            foreach (['gswin64c.exe', 'gswin32c.exe'] as $exe) {
                $found = glob($root . '\\*\\bin\\' . $exe);
                if ($found) {
                    // Most recent version first (glob sorts alphabetically, so reverse)
                    rsort($found);
                    array_unshift($binaries, ...$found);
                }
            }
        }

        return array_unique($binaries);
    }

    protected function cleanup(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
        $this->tempFiles = [];
    }
}
