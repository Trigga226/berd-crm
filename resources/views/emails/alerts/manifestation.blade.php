<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Manifestation</title>
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
        .info-row {
            margin: 10px 0;
            padding: 10px;
            background-color: white;
            border-radius: 3px;
        }
        .label {
            font-weight: bold;
            color: #6b7280;
        }
        .value {
            color: #111827;
        }
        .urgent {
            color: #dc2626;
            font-weight: bold;
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
        <h1 style="margin: 0;">🚨 Alerte Manifestation</h1>
    </div>
    
    <div class="content">
        <div class="alert-box">
            <h2 style="margin-top: 0; color: #dc2626;">{{ $title }}</h2>
        </div>

        <div class="info-row">
            <span class="label">Statut :</span>
            <span class="value">{{ $status }}</span>
        </div>

        @if($deadline)
        <div class="info-row">
            <span class="label">Date Limite :</span>
            <span class="value {{ $daysUntilDeadline !== null && $daysUntilDeadline <= 4 ? 'urgent' : '' }}">
                {{ $deadline->format('d/m/Y à H:i') }}
                @if($daysUntilDeadline !== null)
                    @if($daysUntilDeadline < 0)
                        (⚠️ En retard de {{ round(abs($daysUntilDeadline),0) }} jour(s))
                    @elseif($daysUntilDeadline == 0)
                        (⚠️ Aujourd'hui !)
                    @else
                        (Dans {{ $daysUntilDeadline }} jour(s))
                    @endif
                @endif
            </span>
        </div>
        @endif

        @if($internalControlDate)
        <div class="info-row">
            <span class="label">Date de Contrôle Interne :</span>
            <span class="value {{ $daysUntilControl !== null && $daysUntilControl <= 2 ? 'urgent' : '' }}">
                {{ $internalControlDate->format('d/m/Y à H:i') }}
                @if($daysUntilControl !== null)
                    @if($daysUntilControl < 0)
                        (⚠️ En retard de {{ round(abs($daysUntilControl),0) }} jour(s))
                    @elseif($daysUntilControl == 0)
                        (⚠️ Aujourd'hui !)
                    @else
                        (Dans {{ $daysUntilControl }} jour(s))
                    @endif
                @endif
            </span>
        </div>
        @endif

        <div style="margin-top: 20px; padding: 15px; background-color: #eff6ff; border-radius: 5px;">
            <p style="margin: 0;"><strong>Action requise :</strong></p>
            <p style="margin: 5px 0 0 0;">Veuillez consulter cette manifestation dans le système CRM pour prendre les mesures nécessaires.</p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement par le système BERD CRM.</p>
            <p>Date d'envoi : {{ now()->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
