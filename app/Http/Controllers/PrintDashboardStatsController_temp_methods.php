private function getManifestationTrendData($filters)
{
$query = Manifestation::query();
$this->applyGlobalFilters($query, $filters, 'manifestation');

$start = match ($filters['period'] ?? '1_month') {
'1_month' => now()->subMonth(),
'3_months' => now()->subMonths(3),
'6_months' => now()->subMonths(6),
'1_year' => now()->subYear(),
'2_years' => now()->subYears(2),
'all' => now()->subYears(5),
default => now()->subMonth(),
};
$end = now();

$dataSubmitted = Trend::query((clone $query)->where('status', 'submitted'))
->between(start: $start, end: $end)
->perMonth()
->count();

$dataWon = Trend::query((clone $query)->where('status', 'won'))
->between(start: $start, end: $end)
->perMonth()
->count();

return [
'labels' => $dataSubmitted->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
'datasets' => [
[
'label' => 'Soumis',
'data' => $dataSubmitted->map(fn(TrendValue $value) => $value->aggregate),
'backgroundColor' => '#3b82f6',
],
[
'label' => 'Gagnés',
'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
'backgroundColor' => '#22c55e',
]
]
];
}

private function getOfferTrendData($filters)
{
$query = Offer::query();
$this->applyGlobalFilters($query, $filters, 'offer');

$start = match ($filters['period'] ?? '1_month') {
'1_month' => now()->subMonth(),
'3_months' => now()->subMonths(3),
'6_months' => now()->subMonths(6),
'1_year' => now()->subYear(),
'2_years' => now()->subYears(2),
'all' => now()->subYears(5),
default => now()->subMonth(),
};
$end = now();

$dataWon = Trend::query((clone $query)->where('result', 'won'))
->between(start: $start, end: $end)
->perMonth()
->count();

$dataLost = Trend::query((clone $query)->where('result', 'lost'))
->between(start: $start, end: $end)
->perMonth()
->count();

return [
'labels' => $dataWon->map(fn(TrendValue $value) => Carbon::parse($value->date)->translatedFormat('M Y')),
'datasets' => [
[
'label' => 'Gagnées',
'data' => $dataWon->map(fn(TrendValue $value) => $value->aggregate),
'backgroundColor' => '#22c55e',
],
[
'label' => 'Perdues',
'data' => $dataLost->map(fn(TrendValue $value) => $value->aggregate),
'backgroundColor' => '#ef4444',
]
]
];
}