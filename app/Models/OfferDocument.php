<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferDocument extends Model
{
    protected $fillable = [
        'offer_id',
        'technical_offer_id',
        'financial_offer_id',
        'category',
        'type',
        'label',
        'sort_order',
        'path',
        'original_name',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        // Déplace les fichiers téléversés dans le dossier temporaire vers le
        // dossier définitif de l'offre dès que le titre (donc le slug) est connu.
        static::saved(function (OfferDocument $document) {
            $document->moveOutOfTempIfNeeded();
        });
    }

    protected function moveOutOfTempIfNeeded(): void
    {
        $path = $this->path;

        if (! $path || ! str_contains($path, '/temp/')) {
            return;
        }

        $title = $this->offer?->title;

        if (! $title) {
            return;
        }

        $newPath = str_replace('/temp/', '/' . Str::slug($title) . '/', $path);

        if ($newPath === $path) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path) && ! $disk->exists($newPath)) {
            $disk->move($path, $newPath);
        }

        // Évite la récursion : mise à jour silencieuse du chemin.
        $this->path = $newPath;
        $this->saveQuietly();
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function technicalOffer()
    {
        return $this->belongsTo(TechnicalOffer::class);
    }

    public function financialOffer()
    {
        return $this->belongsTo(FinancialOffer::class);
    }
}
