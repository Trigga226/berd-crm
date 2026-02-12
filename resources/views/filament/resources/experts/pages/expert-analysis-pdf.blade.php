<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analyse des Experts - {{ $date }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; font-size: 12px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
        .title { color: #4f46e5; font-size: 18px; font-weight: bold; }
        .meta { color: #666; font-size: 10px; }
        
        .section-title { font-size: 14px; font-weight: bold; border-left: 4px solid #4f46e5; padding-left: 8px; margin-top: 20px; margin-bottom: 10px; background: #f9fafb; padding-top: 5px; padding-bottom: 5px; }
        
        .expert-item { margin-bottom: 5px; padding: 5px; border-bottom: 1px solid #eee; }
        .expert-name { font-weight: bold; color: #111; }
        
        .message { margin-bottom: 15px; padding: 10px; border-radius: 5px; }
        .message-user { background: #f3f4f6; border-left: 3px solid #6b7280; }
        .message-assistant { background: #eef2ff; border-left: 3px solid #4f46e5; }
        .message-role { font-weight: bold; font-size: 10px; margin-bottom: 4px; text-transform: uppercase; }
        .message-role-user { color: #4b5563; }
        .message-role-assistant { color: #4f46e5; }
        .message-content { white-space: pre-wrap; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Rapport d'Analyse des Candidatures</div>
        <div class="meta">Généré le {{ $date }} par BERD CRM Assistance IA</div>
    </div>

    <div class="section-title">Pool d'Experts Analysés</div>
    @foreach($experts as $expert)
        <div class="expert-item">
            <span class="expert-name">{{ $expert->first_name }} {{ $expert->last_name }}</span> - {{ $expert->title ?? 'Expert' }} ({{ $expert->years_of_experience }} ans d'exp.)
        </div>
    @endforeach

    <div class="section-title">Historique de l'Analyse</div>
    @foreach($messages as $msg)
        @if($msg['content'] === 'Bonjour ! Je suis prête à analyser les experts sélectionnés. Posez-moi une question ou donnez-moi vos critères.') @continue @endif
        
        <div class="message message-{{ $msg['role'] }}">
            <div class="message-role message-role-{{ $msg['role'] }}">
                {{ $msg['role'] === 'user' ? 'Utilisateur' : 'Assistante IA (Lara)' }} - {{ $msg['time'] ?? '' }}
            </div>
            <div class="message-content">{!! strip_tags(str_replace(['<br>', '<br/>', '<p>', '</p>'], ["\n", "\n", "", "\n"], Str::markdown($msg['content'])), '<b><strong>') !!}</div>
        </div>
    @endforeach

    <div class="footer">
        Document confidentiel - BERD CRM - Page {PAGENO}
    </div>
</body>
</html>
