<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerte Offre</title>
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
        .section {
            margin: 20px 0;
            padding: 15px;
            background-color: white;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .section-title {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .info-row {
            margin: 8px 0;
            padding: 8px;
            background-color: #f9fafb;
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
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-technical {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-financial {
            background-color: #d1fae5;
            color: #065f46;
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
        <h1 style="margin: 0;">🚨 Alerte Offre</h1>
    </div>
    
    <div class="content">
        <div class="alert-box">
            <h2 style="margin-top: 0; color: #dc2626;">{{ $title }}</h2>
        </div>

        @if($technicalAlert)
        <div class="section">
            <div class="section-title">
                <span class="badge badge-technical">OFFRE TECHNIQUE</span>
            </div>

            @if($technicalAlert['deadline'])
            <div class="info-row">
                <span class="label">Date Limite :</span>
                <span class="value {{ $technicalAlert['daysUntilDeadline'] !== null && $technicalAlert['daysUntilDeadline'] <= 4 ? 'urgent' : '' }}">
                    {{ $technicalAlert['deadline']->format('d/m/Y à H:i') }}
                    @if($technicalAlert['daysUntilDeadline'] !== null)
                        @if($technicalAlert['daysUntilDeadline'] < 0)
                            (⚠️ En retard de {{ round(abs($technicalAlert['daysUntilDeadline']),0) }} jour(s))
                        @elseif($technicalAlert['daysUntilDeadline'] == 0)
                            (⚠️ Aujourd'hui !)
                        @else
                            (Dans {{ $technicalAlert['daysUntilDeadline'] }} jour(s))
                        @endif
                    @endif
                </span>
            </div>
            @endif

            @if($technicalAlert['internalControlDate'])
            <div class="info-row">
                <span class="label">Contrôle Interne :</span>
                <span class="value {{ $technicalAlert['daysUntilControl'] !== null && $technicalAlert['daysUntilControl'] <= 2 ? 'urgent' : '' }}">
                    {{ $technicalAlert['internalControlDate']->format('d/m/Y à H:i') }}
                    @if($technicalAlert['daysUntilControl'] !== null)
                        @if($technicalAlert['daysUntilControl'] < 0)
                            (⚠️ En retard de {{ round(abs($technicalAlert['daysUntilControl']),0) }} jour(s))
                        @elseif($technicalAlert['daysUntilControl'] == 0)
                            (⚠️ Aujourd'hui !)
                        @else
                            (Dans {{ $technicalAlert['daysUntilControl'] }} jour(s))
                        @endif
                    @endif
                </span>
            </div>
            @endif
        </div>
        @endif

        @if($financialAlert)
        <div class="section">
            <div class="section-title">
                <span class="badge badge-financial">OFFRE FINANCIÈRE</span>
            </div>

            @if($financialAlert['deadline'])
            <div class="info-row">
                <span class="label">Date Limite :</span>
                <span class="value {{ $financialAlert['daysUntilDeadline'] !== null && $financialAlert['daysUntilDeadline'] <= 4 ? 'urgent' : '' }}">
                    {{ $financialAlert['deadline']->format('d/m/Y à H:i') }}
                    @if($financialAlert['daysUntilDeadline'] !== null)
                        @if($financialAlert['daysUntilDeadline'] < 0)
                            (⚠️ En retard de {{ round(abs($financialAlert['daysUntilDeadline']),0) }} jour(s))
                        @elseif($financialAlert['daysUntilDeadline'] == 0)
                            (⚠️ Aujourd'hui !)
                        @else
                            (Dans {{ $financialAlert['daysUntilDeadline'] }} jour(s))
                        @endif
                    @endif
                </span>
            </div>
            @endif

            @if($financialAlert['internalControlDate'])
            <div class="info-row">
                <span class="label">Contrôle Interne :</span>
                <span class="value {{ $financialAlert['daysUntilControl'] !== null && $financialAlert['daysUntilControl'] <= 2 ? 'urgent' : '' }}">
                    {{ $financialAlert['internalControlDate']->format('d/m/Y à H:i') }}
                    @if($financialAlert['daysUntilControl'] !== null)
                        @if($financialAlert['daysUntilControl'] < 0)
                            (⚠️ En retard de {{ round(abs($financialAlert['daysUntilControl']),0) }} jour(s))
                        @elseif($financialAlert['daysUntilControl'] == 0)
                            (⚠️ Aujourd'hui !)
                        @else
                            (Dans {{ $financialAlert['daysUntilControl'] }} jour(s))
                        @endif
                    @endif
                </span>
            </div>
            @endif
        </div>
        @endif

        <div style="margin-top: 20px; padding: 15px; background-color: #eff6ff; border-radius: 5px;">
            <p style="margin: 0;"><strong>Action requise :</strong></p>
            <p style="margin: 5px 0 0 0;">Veuillez consulter cette offre dans le système CRM pour prendre les mesures nécessaires.</p>
        </div>

        <div class="footer">
            <p>Cet email a été généré automatiquement par le système BERD CRM.</p>
            <p>Date d'envoi : {{ now()->format('d/m/Y à H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
