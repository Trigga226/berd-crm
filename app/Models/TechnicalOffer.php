<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TechnicalOffer extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'deadline' => 'datetime',
        'internal_control_date' => 'datetime',
        'submission_date' => 'datetime',
    ];

    protected $pendingDocuments = [];

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (str_starts_with($key, 'documents_')) {
                $this->pendingDocuments[substr($key, 10)] = $value;
                unset($attributes[$key]);
            }
        }
        return parent::fill($attributes);
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            foreach ($model->pendingDocuments as $type => $path) {
                if ($path) {
                    // Check if path is in temp directory and needs moving
                    if (str_contains($path, '/temp/') && $model->offer?->title) {
                        $newDir = 'offres/' . Str::slug($model->offer->title) . '/' . explode('/', $path)[2] . '/fichiers'; // extract subfolder from temp path
                        // Wait, regex might be safer. Temp path: offres/temp/subfolder/fichiers/filename.pdf
                        // We want: offres/slug/subfolder/fichiers/filename.pdf

                        $newPath = str_replace('/temp/', '/' . Str::slug($model->offer->title) . '/', $path);

                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                            \Illuminate\Support\Facades\Storage::disk('public')->move($path, $newPath);
                            $path = $newPath;
                        }
                    }

                    $model->documents()->updateOrCreate(
                        ['type' => $type],
                        ['path' => $path, 'offer_id' => $model->offer_id]
                    );
                } else {
                    $model->documents()->where('type', $type)->delete();
                }
            }
            $model->pendingDocuments = [];
        });
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function documents()
    {
        return $this->hasMany(OfferDocument::class);
    }
}
