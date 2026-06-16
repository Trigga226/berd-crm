<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Projet - {{ $project->code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Segoe UI', sans-serif; font-size: 10pt; color: #1f2937; line-height: 1.4; margin: 0; padding: 30px; background: #fff; }
        .header { border-bottom: 3px solid #3b82f6; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header h1 { margin: 0; font-size: 24px; color: #111827; }
        .project-code { background: #3b82f6; color: white; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 14px; }
        
        .section { margin-bottom: 30px; break-inside: avoid; }
        .section-title { font-size: 16px; font-weight: bold; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-item label { display: block; font-size: 10px; color: #6b7280; text-transform: uppercase; font-weight: bold; }
        .info-item span { font-size: 12px; color: #111827; font-weight: 500; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .stat-card { border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-value { font-size: 20px; font-weight: bold; }
        .stat-label { font-size: 10px; color: #6b7280; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f9fafb; text-align: left; padding: 10px; font-size: 11px; border-bottom: 2px solid #e5e7eb; }
        td { padding: 10px; font-size: 11px; border-bottom: 1px solid #f3f4f6; }
        
        .badge { padding: 2px 8px; border-radius: 9999px; font-size: 9px; font-weight: bold; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #e0f2fe; color: #075985; }

        .print-btn { position: fixed; top: 20px; right: 20px; background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">🖨 Imprimer Rapport</button>

    <div class="header">
        <div>
            <span class="project-code">{{ $project->code }}</span>
            <h1>{{ $project->title }}</h1>
            <p style="color: #6b7280; margin-top: 5px;">Client : {{ $project->client->name ?? 'N/A' }}</p>
        </div>
        <div style="text-align: right; font-size: 11px; color: #9ca3af;">
            Généré le {{ $date->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informations Générales</div>
        <div class="info-grid">
            <div class="info-item">
                <label>Statut</label>
                <span class="badge badge-info">{{ match($project->status) { 'preparation' => 'Préparation', 'ongoing' => 'En cours', 'suspended' => 'Suspendu', 'completed' => 'Terminé', 'cancelled' => 'Annulé', default => $project->status } }}</span>
            </div>
            <div class="info-item">
                <label>Chef de Projet</label>
                <span>{{ $project->manager->name ?? 'Non assigné' }}</span>
            </div>
            <div class="info-item">
                <label>Localisation</label>
                <span>{{ $project->country }}</span>
            </div>
            <div class="info-item">
                <label>Date Début</label>
                <span>{{ $project->planned_start_date?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>Date Fin Prévue</label>
                <span>{{ $project->planned_end_date?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>Date Fin Réelle</label>
                <span>{{ $project->actual_end_date?->format('d/m/Y') ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Indicateurs de Performance</div>
        <div class="stats-grid">
            <div class="stat-card" style="border-left: 5px solid #3b82f6;">
                <div class="stat-value">{{ number_format($project->execution_percentage, 1) }}%</div>
                <div class="stat-label">Exécution</div>
            </div>
            <div class="stat-card" style="border-left: 5px solid #22c55e;">
                <div class="stat-value">{{ $project->deliverables->where('status', 'validated')->count() }} / {{ $project->deliverables->count() }}</div>
                <div class="stat-label">Livrables Validés</div>
            </div>
            <div class="stat-card" style="border-left: 5px solid #ef4444;">
                <div class="stat-value">{{ $project->risks->count() }}</div>
                <div class="stat-label">Risques Actifs</div>
            </div>
            <div class="stat-card" style="border-left: 5px solid #f59e0b;">
                <div class="stat-value">{{ number_format($project->recoveryRate(), 1) }}%</div>
                <div class="stat-label">Recouvrement</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Situation Financière</div>
        <table style="margin-bottom: 15px;">
            <tr>
                <td style="width: 25%; font-weight: bold; background: #f9fafb;">Coût du Marché</td>
                <td>{{ number_format($project->total_budget, 0, ',', ' ') }} XOF</td>
                <td style="width: 25%; font-weight: bold; background: #f9fafb;">Budget Consommé</td>
                <td style="color: {{ $project->consumed_budget > $project->total_budget ? '#ef4444' : 'inherit' }}">
                    {{ number_format($project->consumed_budget, 0, ',', ' ') }} XOF
                    ({{ number_format(($project->consumed_budget / max(1, $project->total_budget)) * 100, 1) }}%)
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background: #f9fafb;">Total Facturé</td>
                <td>{{ number_format($project->totalInvoiced(), 0, ',', ' ') }} XOF</td>
                <td style="font-weight: bold; background: #f9fafb;">Total Encaissé</td>
                <td style="color: #166534; font-weight: bold;">{{ number_format($project->totalPaid(), 0, ',', ' ') }} XOF</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Timeline des Livrables</div>
        <table>
            <thead>
                <tr>
                    <th>Titre du Livrable</th>
                    <th>Date Prévue</th>
                    <th>Date Réelle</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->deliverables as $deliv)
                <tr>
                    <td style="font-weight: 500;">{{ $deliv->title }}</td>
                    <td>{{ $deliv->planned_date?->format('d/m/Y') }}</td>
                    <td>{{ $deliv->actual_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $deliv->status === 'validated' ? 'badge-success' : ($deliv->planned_date?->isPast() ? 'badge-danger' : 'badge-warning') }}">
                            {{ match($deliv->status) { 'pending' => 'En attente', 'submitted' => 'Soumis', 'validated' => 'Validé', 'rejected' => 'Rejeté', default => $deliv->status } }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Analyse des Risques</div>
        <table>
            <thead>
                <tr>
                    <th>Risque</th>
                    <th>Impact</th>
                    <th>Probabilité</th>
                    <th>Stratégie</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->risks as $risk)
                <tr>
                    <td style="font-weight: 500;">{{ $risk->title }}</td>
                    <td><span class="badge {{ $risk->impact === 'high' ? 'badge-danger' : 'badge-warning' }}">{{ ucfirst($risk->impact) }}</span></td>
                    <td><span class="badge {{ $risk->probability === 'high' ? 'badge-danger' : 'badge-warning' }}">{{ ucfirst($risk->probability) }}</span></td>
                    <td>{{ $risk->mitigation_strategy }}</td>
                    <td>{{ ucfirst($risk->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 50px; border-top: 1px solid #e5e7eb; padding-top: 20px; display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af;">
        <div>BERD CRM - Système de Gestion de Projet</div>
        <div>Page 1 / 1</div>
    </div>

</body>
</html>
