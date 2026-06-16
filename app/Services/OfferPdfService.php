<?php

namespace App\Services;

use App\Models\Offer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class OfferPdfService
{
    protected array $tempFiles  = [];
    protected int   $totalPages = 0;

    public function generateTechnicalOfferPdf(Offer $offer)
    {
        return $this->mergeDocuments($offer, 'technical', 'Offre_Technique_Complete.pdf');
    }

    public function generateFinancialOfferPdf(Offer $offer)
    {
        return $this->mergeDocuments($offer, 'financial', 'Offre_Financiere_Complete.pdf');
    }

    public function saveToStorage(Offer $offer, string $type = 'technical'): ?string
    {
        $this->tempFiles  = [];
        $this->totalPages = 0;

        $category = $type === 'financial' ? 'financial' : 'technical';
        $label    = $type === 'financial' ? 'financiere' : 'technique';
        $filename = "offers/generated/offre_{$label}_{$offer->id}.pdf";

        $pdf = new Fpdi();

        foreach ($this->orderedDocuments($offer, $category) as $document) {
            $this->addPdfToMerge($pdf, $document->path, "[{$document->label}]");
        }

        Log::info("OfferPdfService ({$label}): {$this->totalPages} page(s) importée(s) pour offre #{$offer->id}");

        if ($this->totalPages === 0) {
            $this->cleanup();
            return null;
        }

        Storage::disk('public')->makeDirectory('offers/generated');
        $outputPath = Storage::disk('public')->path($filename);
        $pdf->Output($outputPath, 'F');

        $this->cleanup();

        return $filename;
    }

    protected function mergeDocuments(Offer $offer, string $category, string $outputName)
    {
        $this->tempFiles  = [];
        $this->totalPages = 0;

        $pdf = new Fpdi();

        foreach ($this->orderedDocuments($offer, $category) as $document) {
            $this->addPdfToMerge($pdf, $document->path, "[{$document->label}]");
        }

        Log::info("OfferPdfService (download): {$this->totalPages} page(s) importée(s) pour offre #{$offer->id}");

        $this->cleanup();

        if ($this->totalPages === 0) {
            return null;
        }

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('D', 'merged.pdf');
        }, $outputName);
    }

    /**
     * Pièces d'une catégorie ('technical' | 'financial') possédant un fichier,
     * dans l'ordre défini par l'utilisateur (intitulé + sort_order).
     *
     * @return \Illuminate\Support\Collection<int, \App\Models\OfferDocument>
     */
    protected function orderedDocuments(Offer $offer, string $category)
    {
        return $offer->documents()
            ->where('category', $category)
            ->whereNotNull('path')
            ->where('path', '!=', '')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    protected function addPdfToMerge(Fpdi $pdf, string $path, string $label = ''): void
    {
        $fullPath = Storage::disk('public')->path($path);

        if (!file_exists($fullPath)) {
            Log::warning("OfferPdfService {$label}: fichier introuvable → {$fullPath}");
            return;
        }

        try {
            $pageCount = $pdf->setSourceFile($fullPath);
        } catch (\Exception $e) {
            Log::info("OfferPdfService {$label}: lecture directe échouée ({$e->getMessage()}), tentative Ghostscript → {$fullPath}");
            try {
                $tempPath          = $this->convertPdfTo14($fullPath);
                $this->tempFiles[] = $tempPath;
                $pageCount         = $pdf->setSourceFile($tempPath);
            } catch (\Exception $conversionError) {
                Log::error("OfferPdfService {$label}: impossible de fusionner {$path} — {$conversionError->getMessage()}");
                return;
            }
        }

        for ($page = 1; $page <= $pageCount; $page++) {
            $templateId = $pdf->importPage($page);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);
            $this->totalPages++;
        }

        Log::debug("OfferPdfService {$label}: +{$pageCount} page(s) depuis {$path}");
    }

    protected function convertPdfTo14(string $originalPath): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'pdf_14_') . '.pdf';
        $null     = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';

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
        $binaries = ['gswin64c', 'gswin32c', 'gs'];

        $roots = ['C:\\Program Files\\gs', 'C:\\Program Files (x86)\\gs'];
        foreach ($roots as $root) {
            foreach (['gswin64c.exe', 'gswin32c.exe'] as $exe) {
                $found = glob($root . '\\*\\bin\\' . $exe);
                if ($found) {
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
