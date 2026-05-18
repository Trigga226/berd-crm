<?php

namespace App\Services;

use App\Models\ProjectInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class ProjectInvoicePdfService
{
    /**
     * Génère et retourne un PDF de facture projet en streaming.
     */
    public function generate(ProjectInvoice $invoice): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $invoice->load(['project.client', 'deliverable']);

        $pdf = Pdf::loadView('pdf.project-invoice', ['invoice' => $invoice])
            ->setPaper('a4', 'portrait');

        $filename = 'Facture_' . $invoice->invoice_number . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}
