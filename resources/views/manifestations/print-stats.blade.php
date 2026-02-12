<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Statistique - Manifestations d'Intérêt</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-50 p-8 text-sm">

    <div class="max-w-7xl mx-auto bg-white p-8 shadow-sm print:shadow-none print:p-0">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 border-b pb-4">
            <div class="flex items-center gap-4">
                <!-- Logo Placeholer -->
                <div class="text-2xl font-bold text-gray-800">BERD CRM</div>
                <div class="h-8 w-px bg-gray-300 mx-2"></div>
                <h1 class="text-xl font-semibold text-gray-700">Rapport Manifestations d'Intérêt</h1>
            </div>
            <div class="text-right">
                <p class="text-gray-500">Généré le: {{ now()->format('d/m/Y H:i') }}</p>
                <button onclick="window.print()" class="no-print mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Imprimer / PDF</button>
            </div>
        </div>

        <!-- Filters Applied -->
        <div class="mb-6 bg-gray-50 p-4 rounded border">
            <h3 class="font-semibold mb-2">Filtres appliqués:</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($filters as $key => $value)
                    @if(!empty($value) && $value !== 'all')
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs border border-blue-200">
                            {{ ucfirst($key) }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                        </span>
                    @endif
                @endforeach
                @if(empty($filters) || (count($filters) == 1 && ($filters['period'] ?? '') == 'all'))
                    <span class="text-gray-500 italic">Aucun filtre spécifique (Tout afficher)</span>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-5 gap-4 mb-8">
            <div class="bg-white p-4 rounded border shadow-sm">
                <div class="text-gray-500 text-xs uppercase font-semibold">Total</div>
                <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
            </div>
            <div class="bg-white p-4 rounded border shadow-sm border-blue-200 bg-blue-50">
                <div class="text-blue-600 text-xs uppercase font-semibold">Soumis</div>
                <div class="text-2xl font-bold text-blue-700">{{ $submitted }}</div>
            </div>
            <div class="bg-white p-4 rounded border shadow-sm border-green-200 bg-green-50">
                <div class="text-green-600 text-xs uppercase font-semibold">Gagnés</div>
                <div class="text-2xl font-bold text-green-700">{{ $won }}</div>
                <div class="text-xs text-green-600 mt-1">Conv: {{ $conversionRate }}%</div>
            </div>
            <div class="bg-white p-4 rounded border shadow-sm border-red-200 bg-red-50">
                <div class="text-red-600 text-xs uppercase font-semibold">Perdus</div>
                <div class="text-2xl font-bold text-red-700">{{ $lost }}</div>
            </div>
            <div class="bg-white p-4 rounded border shadow-sm border-amber-200 bg-amber-50">
                <div class="text-amber-600 text-xs uppercase font-semibold">Abandonnés</div>
                <div class="text-2xl font-bold text-amber-700">{{ $abandoned }}</div>
            </div>
        </div>

        <!-- Chart -->
        <div class="mb-8 border rounded p-4 h-80">
            <h3 class="font-semibold mb-4">Évolution Temporelle</h3>
            <canvas id="statsChart"></canvas>
        </div>

        <!-- List Table -->
        <div class="mt-8">
            <h3 class="font-semibold mb-4">Liste des Manifestations ({{ count($manifestations) }})</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300">
                        <th class="p-2 border-b">Titre</th>
                        <th class="p-2 border-b">Client</th>
                        <th class="p-2 border-b">Pays</th>
                        <th class="p-2 border-b">Date Limite</th>
                        <th class="p-2 border-b">Statut</th>
                        <th class="p-2 border-b">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($manifestations as $manifestation)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2 text-xs truncate max-w-xs" title="{{ $manifestation->avisManifestation?->title }}">{{ $manifestation->avisManifestation?->title ?? '-' }}</td>
                        <td class="p-2 text-xs">{{ $manifestation->client_name }}</td>
                        <td class="p-2 text-xs">{{ $manifestation->country }}</td>
                        <td class="p-2 text-xs">{{ $manifestation->deadline?->format('d/m/Y') ?? '-' }}</td>
                        <td class="p-2 text-xs">
                            <span class="px-2 py-0.5 rounded-full text-[10px] 
                                @if($manifestation->status === 'won') bg-green-100 text-green-800 
                                @elseif($manifestation->status === 'lost') bg-red-100 text-red-800 
                                @elseif($manifestation->status === 'submitted') bg-blue-100 text-blue-800 
                                @elseif($manifestation->status === 'abandoned') bg-amber-100 text-amber-800 
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($manifestation->status) }}
                            </span>
                        </td>
                        <td class="p-2 text-xs font-mono">{{ $manifestation->score ? $manifestation->score .'/100' : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('statsChart').getContext('2d');
            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                             ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    animation: false // Disable animation for printing
                }
            });
        });
    </script>
</body>
</html>
