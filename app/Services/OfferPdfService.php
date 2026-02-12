<?php

namespace App\Services;

use App\Models\Offer;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\StreamReader;

class OfferPdfService
{
    protected array $technicalOrder = [
        'tech_cover',
        'tech_summary',
        'tech_1_1',
        'tech_1_2',
        'tech_1_3',
        'tech_1_4',
        'tech_1_5',
        'tech_2_a',
        'tech_2_b',
        'tech_3_a',
        'tech_3_b',
        'tech_4',
        'tech_5',
        'tech_6_1',
        'tech_6_2',
        'tech_7',
        'tech_other_1',
        'tech_other_2',
        'tech_other_3',
    ];

    protected array $financialOrder = [
        'fine_cover',
        'fine_1',
        'fine_2',
        'fine_3',
        'fine_4',
        'fine_5',
        'fine_other_1',
        'fine_other_2',
        'fine_other_3',
    ];

    public function generateTechnicalOfferPdf(Offer $offer)
    {
        return $this->mergeDocuments($offer, $this->technicalOrder, 'Offre_Technique_Complete.pdf');
    }

    public function generateFinancialOfferPdf(Offer $offer)
    {
        return $this->mergeDocuments($offer, $this->financialOrder, 'Offre_Financiere_Complete.pdf');
    }

    protected function mergeDocuments(Offer $offer, array $order, string $outputName)
    {
        $pdf = new Fpdi();
        $filesFound = 0;

        foreach ($order as $type) {
            $path = $offer->documents()->where('type', $type)->value('path');

            if ($path && Storage::disk('public')->exists($path)) {
                $fullPath = Storage::disk('public')->path($path);

                try {
                    $pageCount = $pdf->setSourceFile($fullPath);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $pdf->importPage($pageNo);
                        $pdf->AddPage();
                        $pdf->useTemplate($templateId);
                    }
                    $filesFound++;
                } catch (\Exception $e) {
                    // Log error or ignore invalid PDF
                    // Log::warning("Could not merge PDF: $path - " . $e->getMessage());
                }
            }
        }

        if ($filesFound === 0) {
            return null;
        }

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('D', 'merged.pdf'); // Output to string or stream
        }, $outputName);
    }
}
