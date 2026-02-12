<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques du Tableau de Bord - {{ $date->format('d/m/Y') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            color: #1f2937;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            color: #111827;
        }
        .meta {
            margin-top: 5px;
            font-size: 12px;
            color: #6b7280;
        }
        
        .module-section {
            break-inside: avoid;
            margin-bottom: 30px;
            border-bottom: 1px dashed #e5e7eb;
            padding-bottom: 20px;
        }
        .module-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 15px;
            border-left: 5px solid #3b82f6;
            padding-left: 10px;
            background: #f9fafb;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        
        /* Filters */
        .filters {
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            font-size: 11px;
        }
        .filter-item strong {
            color: #4b5563;
        }

        /* Layout Grid */
        .content-container {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1.5fr; /* Pie smaller, Bar wider */
            gap: 20px;
            align-items: center;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            background: #fff;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        .stat-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #6b7280;
            margin-top: 3px;
            font-weight: 600;
        }
        .stat-desc {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* Chart */
        .chart-container {
            position: relative;
            width: 100%;
            height: 180px; 
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        /* Print Specifics */
        @media print {
            @page { margin: 0.8cm; size: landscape; }
            body { 
                padding: 0; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact;
            }
            .chart-container, .charts-row {
                break-inside: avoid;
            }
            .no-print {
                display: none !important;
            }
            .module-section {
                break-after: always;
                page-break-after: always;
                border-bottom: none;
            }
            .module-section:last-child {
                break-after: auto;
                page-break-after: auto;
            }
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .print-btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn no-print">🖨 Imprimer</button>

    <div class="header">
        <h1>Rapport Global d'Activité</h1>
        <div class="meta">Généré le {{ $date->format('d/m/Y à H:i') }} | BERD CRM</div>
    </div>

    <!-- Filtres -->
    <div class="filters">
        <!-- ... filters content same as before ... -->
        <div class="filter-item">
            <strong>Pays :</strong> {{ $filters['country'] ?? 'Tous' }}
        </div>
        <div class="filter-item">
            <strong>Statut :</strong> {{ isset($filters['status']) ? ucfirst($filters['status']) : 'Tous' }}
        </div>
        <div class="filter-item">
            <strong>Domaine :</strong> {{ $filters['domains'] ?? 'Tous' }}
        </div>
        <div class="filter-item">
            <strong>Score Min :</strong> {{ $filters['score_min'] ?? 'Aucun' }}
        </div>
        <div class="filter-item">
            <strong>Période :</strong>
            @switch($filters['period'] ?? '1_month')
                @case('1_month') 1 Mois @break
                @case('3_months') 3 Mois @break
                @case('6_months') 6 Mois @break
                @case('1_year') 1 An @break
                @case('2_years') 2 Ans @break
                @case('all') Tout @break
                @default 1 Mois
            @endswitch
        </div>
    </div>

    <!-- 1. MANIFESTATIONS -->
    @if($section === 'all' || $section === 'manifestations')
    <div class="module-section">
        <div class="section-title">Manifestations d'Intérêt</div>
        <div class="content-container">
            <div class="charts-row">
                <div class="chart-container">
                    <canvas id="manifestationChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="manifestationTrendChart"></canvas>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-card" style="background: #f0f9ff; border-color: #bae6fd;">
                    <div class="stat-value" style="color: #0284c7;">{{ $manifestationStats['total'] }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $manifestationStats['submitted'] }}</div>
                    <div class="stat-label">Soumises</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #22c55e;">{{ $manifestationStats['won'] }}</div>
                    <div class="stat-label">Gagnées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #ef4444;">{{ $manifestationStats['lost'] + $manifestationStats['abandoned'] }}</div>
                    <div class="stat-label">Perdues/Aband.</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 2. OFFERS -->
    @if($section === 'all' || $section === 'offers')
    <div class="module-section">
        <div class="section-title">Offres Techniques & Financières</div>
        <div class="content-container">
            <div class="charts-row">
                <div class="chart-container">
                    <canvas id="offerChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="offerTrendChart"></canvas>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-card" style="background: #fdf4ff; border-color: #f5d0fe;">
                    <div class="stat-value" style="color: #a21caf;">{{ $offerStats['total'] }}</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $offerStats['active'] }}</div>
                    <div class="stat-label">En Cours</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #22c55e;">{{ $offerStats['won'] }}</div>
                    <div class="stat-label">Gagnées</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #ef4444;">{{ $offerStats['lost'] + $offerStats['abandoned'] }}</div>
                    <div class="stat-label">Perdues/Aband.</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 3. PROJECTS -->
    @if($section === 'all' || $section === 'projects')
    <div class="module-section">
        <div class="section-title">Projets & Exécution</div>
        <div class="content-container">
            <div class="charts-row">
                <div class="chart-container">
                    <canvas id="projectChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="projectTrendChart"></canvas>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" style="color: #3b82f6;">{{ $projectStats['total'] }}</div>
                    <div class="stat-label">Total Projets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $projectStats['ongoing'] }}</div>
                    <div class="stat-label">En Cours</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #22c55e;">{{ $projectStats['completed'] }}</div>
                    <div class="stat-label">Terminés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" style="color: #f59e0b;">{{ $projectStats['delayed'] }}</div>
                    <div class="stat-label">En Retard</div>
                </div>
                <div class="stat-card" style="grid-column: span 2; background: #f9fafb;">
                    <div class="stat-value">{{ number_format($projectStats['total_budget'], 0, ',', ' ') }} <small>XOF</small></div>
                    <div class="stat-label">Budget Total</div>
                    <div class="stat-desc">
                        {{ number_format($projectStats['consumed_budget'], 0, ',', ' ') }} XOF consommés
                        ({{ round($projectStats['budget_utilization'], 1) }}%)
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        Document généré automatiquement par le système BERD CRM. <br>
        Date d'impression : {{ now()->format('d/m/Y H:i:s') }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                alert('Erreur : La librairie Chart.js n\'a pas pu être chargée. Vérifiez votre connexion internet.');
                return;
            }

            // Disable Animation for Print
            Chart.defaults.animation = false;
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;

            const pieConfig = {
                type: 'pie',
                options: {
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { boxWidth: 10, font: { size: 9 } }
                        },
                        title: { display: false }
                    }
                }
            };

            const barConfig = {
                type: 'bar',
                options: {
                    plugins: {
                        legend: { display: false },
                        title: { display: true, text: 'Évolution Mensuelle', font: { size: 10 } }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { font: { size: 9 } } },
                        x: { ticks: { font: { size: 9 } } }
                    }
                }
            };

            try {
                // Manifestation Charts
                const manifestationCtx = document.getElementById('manifestationChart');
                if (manifestationCtx) {
                    new Chart(manifestationCtx.getContext('2d'), {
                        ...pieConfig,
                        data: @json($manifestationChart)
                    });
                }
                
                const manifestationTrendCtx = document.getElementById('manifestationTrendChart');
                if (manifestationTrendCtx) {
                    new Chart(manifestationTrendCtx.getContext('2d'), {
                        ...barConfig,
                        data: @json($manifestationTrend)
                    });
                }

                // Offer Charts
                const offerCtx = document.getElementById('offerChart');
                if (offerCtx) {
                    new Chart(offerCtx.getContext('2d'), {
                        ...pieConfig,
                        data: @json($offerChart)
                    });
                }

                const offerTrendCtx = document.getElementById('offerTrendChart');
                if (offerTrendCtx) {
                    new Chart(offerTrendCtx.getContext('2d'), {
                        ...barConfig,
                        data: @json($offerTrend)
                    });
                }

                // Project Chart
                const projectCtx = document.getElementById('projectChart');
                if (projectCtx) {
                    new Chart(projectCtx.getContext('2d'), {
                        ...pieConfig,
                        data: @json($projectChart)
                    });
                }

                const projectTrendCtx = document.getElementById('projectTrendChart');
                if (projectTrendCtx) {
                    new Chart(projectTrendCtx.getContext('2d'), {
                        ...barConfig,
                        data: @json($projectTrend)
                    });
                }
            } catch (e) {
                console.error("Erreur lors de l'initialisation des graphiques", e);
                alert("Une erreur est survenue lors de l'affichage des graphiques.");
            }
        });
    </script>

</body>
</html>
