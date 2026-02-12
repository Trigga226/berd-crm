<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Projet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc2626;
            color: white;
            padding: 20px;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .alert-box {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 15px 0;
        }
        .alert-item {
            margin: 15px 0;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            border-left: 3px solid #6b7280;
        }
        .alert-item.danger {
            border-left-color: #dc2626;
            background-color: #fef2f2;
        }
        .alert-item.warning {
            border-left-color: #f59e0b;
            background-color: #fffbeb;
        }
        .alert-item.info {
            border-left-color: #3b82f6;
            background-color: #eff6ff;
        }
        .info-row {
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
        }
        .value {
            color: #111827;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">🚨 Alerte Projet</h1>
    </div>
    
    <div class="content">
        <div class="alert-box">
            <h2 style="margin-top: 0; color: #dc2626;">{{ $alertType }}</h2>
            <p style="margin: 5px 0 0 0; color: #6b7280;">{{ $count }} alerte(s) détectée(s)</p>
        </div>

        @foreach($alerts as $alert)
        <div class="alert-item {{ $alert['severity'] }}">
            @if($alert['severity'] === 'danger')
                <span class="badge badge-danger">URGENT</span>
            @elseif($alert['severity'] === 'warning')
                <span class="badge badge-warning">ATTENTION</span>
            @else
                <span class="badge badge-info">INFO</span>
            @endif

            <div class="info-row">
                <span class="label">Projet :</span>
                <span class="value">{{ $alert['project'] }}</span>
            </div>

            <div class="info-row">
                <span class="label">Client :</span>
                <span class="value">{{ $alert['client'] }}</span>
            </div>

            <div class="info-row">
                <span class="label">Détails :</span>
                <span class="value">{{ $alert['details'] }}</span>
            </div>
        </div>
        @endforeach

        <div style="margin-top: 20px; padding: 15px; background-color: #eff6ff; border-radius: 5px;">
            <p style="margin: 0;"><strong>Action requise :</strong></p>
            <p style="margin: 5px 0 0 0;">Veuillez consulter ces projets dans le système CRM pour prendre les mesures nécessaires.</p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement par le système BERD CRM.</p>
            <p>Date d'envoi : {{ now()->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
