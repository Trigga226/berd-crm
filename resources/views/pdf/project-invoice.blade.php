<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1f2937; background: #fff; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; padding: 30px 40px 20px; border-bottom: 3px solid #1e40af; }
        .company-info h1 { font-size: 22px; color: #1e40af; font-weight: 700; letter-spacing: 1px; }
        .company-info p { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .invoice-badge { background: #1e40af; color: #fff; padding: 10px 20px; border-radius: 6px; text-align: center; }
        .invoice-badge .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }
        .invoice-badge .number { font-size: 18px; font-weight: 700; margin-top: 4px; }

        .meta-section { display: flex; justify-content: space-between; padding: 24px 40px; background: #f8fafc; }
        .meta-box h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 6px; }
        .meta-box p { font-size: 12px; color: #111827; font-weight: 600; }
        .meta-box .sub { font-size: 10px; color: #6b7280; font-weight: 400; margin-top: 2px; }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-draft    { background: #f3f4f6; color: #6b7280; }
        .status-sent     { background: #dbeafe; color: #1d4ed8; }
        .status-paid     { background: #d1fae5; color: #065f46; }
        .status-overdue  { background: #fee2e2; color: #991b1b; }
        .status-cancelled{ background: #f3f4f6; color: #374151; }

        .section { padding: 20px 40px; }
        .section-title { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb; }

        table { width: 100%; border-collapse: collapse; }
        table th { background: #1e40af; color: #fff; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        table tr:last-child td { border-bottom: none; }
        table .amount { text-align: right; font-weight: 700; }

        .totals { margin-top: 20px; padding: 20px 40px; }
        .totals-box { margin-left: auto; width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        .totals-row.total { font-size: 14px; font-weight: 700; color: #1e40af; border-top: 2px solid #1e40af; border-bottom: none; padding-top: 10px; margin-top: 4px; }
        .totals-row.paid-row { color: #065f46; }
        .totals-row.remaining { color: #dc2626; font-weight: 700; }

        .notes-section { padding: 16px 40px; background: #fefce8; border-top: 1px solid #fef08a; }
        .notes-section h4 { font-size: 10px; text-transform: uppercase; color: #92400e; margin-bottom: 6px; }
        .notes-section p { font-size: 10px; color: #78350f; }

        .footer { padding: 20px 40px; border-top: 2px solid #e5e7eb; margin-top: 20px; display: flex; justify-content: space-between; }
        .footer p { font-size: 9px; color: #9ca3af; }
        .footer .highlight { color: #1e40af; font-weight: 600; }
    </style>
</head>
<body>

{{-- En-tête --}}
<div class="header">
    <div class="company-info">
        <h1>BERD</h1>
        <p>Bureau d'Études et de Recherche pour le Développement</p>
        <p style="margin-top: 8px; font-size: 11px; color: #374151;">{{ $invoice->project?->client?->name ?? '' }}</p>
    </div>
    <div class="invoice-badge">
        <div class="label">Facture</div>
        <div class="number">{{ $invoice->invoice_number }}</div>
    </div>
</div>

{{-- Méta-données --}}
<div class="meta-section">
    <div class="meta-box">
        <h3>Projet</h3>
        <p>{{ $invoice->project?->title ?? 'N/A' }}</p>
        <p class="sub">Réf : {{ $invoice->project?->code ?? '' }}</p>
    </div>
    <div class="meta-box">
        <h3>Client</h3>
        <p>{{ $invoice->project?->client?->name ?? 'N/A' }}</p>
    </div>
    <div class="meta-box">
        <h3>Date d'émission</h3>
        <p>{{ $invoice->issue_date?->format('d/m/Y') ?? '—' }}</p>
    </div>
    <div class="meta-box">
        <h3>Date d'échéance</h3>
        <p style="color: {{ $invoice->due_date?->isPast() && $invoice->status !== 'paid' ? '#dc2626' : '#111827' }}">
            {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}
        </p>
    </div>
    <div class="meta-box">
        <h3>Statut</h3>
        @php
            $statusClass = match($invoice->status) {
                'draft' => 'status-draft',
                'sent' => 'status-sent',
                'paid' => 'status-paid',
                'overdue' => 'status-overdue',
                'cancelled' => 'status-cancelled',
                default => 'status-draft',
            };
            $statusLabel = match($invoice->status) {
                'draft' => 'Brouillon',
                'sent' => 'Envoyée',
                'paid' => 'Payée',
                'overdue' => 'En retard',
                'cancelled' => 'Annulée',
                default => $invoice->status,
            };
        @endphp
        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
    </div>
</div>

{{-- Objet --}}
<div class="section">
    <div class="section-title">Objet de la facture</div>
    <table>
        <thead>
            <tr>
                <th style="width: 50%">Désignation</th>
                <th>Livrable associé</th>
                <th class="amount" style="text-align: right">Montant (XOF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Prestation de services — {{ $invoice->project?->title }}</td>
                <td>{{ $invoice->deliverable?->title ?? '—' }}</td>
                <td class="amount">{{ number_format($invoice->amount, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- Totaux --}}
<div class="totals">
    <div class="totals-box">
        <div class="totals-row">
            <span>Montant facturé</span>
            <span>{{ number_format($invoice->amount, 0, ',', ' ') }} XOF</span>
        </div>
        <div class="totals-row paid-row">
            <span>Montant payé</span>
            <span>{{ number_format($invoice->paid_amount, 0, ',', ' ') }} XOF</span>
        </div>
        @if($invoice->remainingAmount() > 0)
        <div class="totals-row remaining">
            <span>Reste à payer</span>
            <span>{{ number_format($invoice->remainingAmount(), 0, ',', ' ') }} XOF</span>
        </div>
        @endif
        <div class="totals-row total">
            <span>Total Net</span>
            <span>{{ number_format($invoice->amount, 0, ',', ' ') }} XOF</span>
        </div>
    </div>
</div>

{{-- Notes --}}
@if($invoice->notes)
<div class="notes-section">
    <h4>Notes</h4>
    <p>{{ $invoice->notes }}</p>
</div>
@endif

{{-- Pied de page --}}
<div class="footer">
    <div>
        <p>Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
        <p>Facture N° <span class="highlight">{{ $invoice->invoice_number }}</span></p>
    </div>
    <div style="text-align: right;">
        <p>Merci pour votre confiance.</p>
        <p class="highlight">BERD — Bureau d'Études et de Recherche pour le Développement</p>
    </div>
</div>

</body>
</html>
