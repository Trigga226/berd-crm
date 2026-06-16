<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Offer;
use App\Models\OfferDocument;
use App\Services\OfferPdfService;
use Illuminate\Support\Facades\Storage;

// Liste toutes les offres
$offers = Offer::withTrashed()->take(5)->get();
echo "=== Offres disponibles ===\n";
foreach ($offers as $o) {
    echo "  id={$o->id} | {$o->title}\n";
}

// Cherche une offre avec des documents
$offer = Offer::withTrashed()->whereHas('documents')->first();
if (!$offer) {
    echo "\nAucune offre avec documents trouvée.\n";

    echo "\n=== Tous les OfferDocuments ===\n";
    foreach (OfferDocument::take(10)->get() as $d) {
        echo "  id={$d->id} | offer_id={$d->offer_id} | type={$d->type} | path={$d->path}\n";
    }
    exit;
}

echo "\n=== Offre #{$offer->id}: {$offer->title} ===\n";
echo "Documents:\n";
foreach ($offer->documents as $d) {
    $fullPath = Storage::disk('public')->path($d->path);
    echo "  type={$d->type} | path={$d->path} | exists=" . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
}

echo "\n--- Tentative saveToStorage (technical) ---\n";
$service = new OfferPdfService();
try {
    $path = $service->saveToStorage($offer, 'technical');
    if ($path) {
        $fullPath = Storage::disk('public')->path($path);
        echo "SUCCESS: {$path}\n";
        echo "File size: " . (file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'MISSING') . "\n";
    } else {
        echo "RETURNED NULL (0 pages ou aucun document)\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
