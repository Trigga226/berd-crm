<?php

namespace App\Services;

use App\Models\Archive;
use App\Models\Manifestation;
use App\Models\Offer;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ArchiveService
{
    /**
     * Retourne le folder_path normalisé : Archives/{type}/{année}/{domaine}/{slug}
     */
    public function buildFolderPath(string $type, int $annee, string $domaine, string $slug): string
    {
        return 'Archives/' . $type . '/' . $annee . '/' . $domaine . '/' . $slug;
    }

    /**
     * Archive une Manifestation — compile l'ensemble de ses fichiers (documents, fichier généré).
     */
    public function archiverManifestation(Manifestation $manifestation): Archive
    {
        $manifestation->loadMissing('documents', 'avisManifestation');

        $annee   = $manifestation->deadline?->year ?? now()->year;
        $domaine = $this->formatDomaine($manifestation->domains[0] ?? 'Autre');
        $slug    = 'MANIF-' . $annee . '-' . $manifestation->id;
        $titre   = $manifestation->avisManifestation?->title ?? 'Manifestation #' . $manifestation->id;

        // Compilation de tous les fichiers du dossier de manifestation
        $fichiers = [];
        foreach ($manifestation->documents as $doc) {
            $fichiers[] = [
                'nom'    => $doc->title ?? $doc->type,
                'type'   => $doc->type,
                'chemin' => $doc->file_path,
            ];
        }
        if ($manifestation->generated_file_path) {
            $fichiers[] = [
                'nom'    => 'Dossier généré',
                'type'   => 'dossier_complet',
                'chemin' => $manifestation->generated_file_path,
            ];
        }

        return Archive::updateOrCreate(
            ['entite_type' => Manifestation::class, 'entite_id' => $manifestation->id],
            [
                'titre'        => $titre,
                'type'         => 'Manifestation',
                'annee'        => $annee,
                'domaine'      => $domaine,
                'folder_path'  => $this->buildFolderPath('Manifestations', $annee, $domaine, $slug),
                'fichier'      => $fichiers,
                'date_archive' => now(),
                'archive_par'  => Auth::id(),
                'resultat'     => $manifestation->status,
                'statut'       => 'archived',
            ]
        );
    }

    /**
     * Archive une Offre — compile l'offre technique + financière + documents annexes.
     */
    public function archiverOffre(Offer $offer): Archive
    {
        $offer->loadMissing('documents', 'technicalOffer.documents', 'financialOffer.documents', 'manifestation');

        $annee   = $offer->created_at->year;
        $domaine = $this->formatDomaine(
            $offer->manifestation?->domains[0] ?? 'Autre'
        );
        $slug    = 'OFFRE-' . $annee . '-' . $offer->id;

        // Compilation : documents principaux + offre technique + offre financière
        $fichiers = [];

        if ($offer->dp_path) {
            $fichiers[] = ['nom' => 'Dossier de participation', 'type' => 'dp', 'chemin' => $offer->dp_path];
        }

        foreach ($offer->documents as $doc) {
            $fichiers[] = ['nom' => $doc->original_name, 'type' => $doc->type, 'chemin' => $doc->path];
        }

        if ($offer->technicalOffer) {
            foreach ($offer->technicalOffer->documents as $doc) {
                $fichiers[] = ['nom' => $doc->original_name, 'type' => 'technique_' . $doc->type, 'chemin' => $doc->path];
            }
        }

        if ($offer->financialOffer) {
            foreach ($offer->financialOffer->documents as $doc) {
                $fichiers[] = ['nom' => $doc->original_name, 'type' => 'financier_' . $doc->type, 'chemin' => $doc->path];
            }
        }

        return Archive::updateOrCreate(
            ['entite_type' => Offer::class, 'entite_id' => $offer->id],
            [
                'titre'        => $offer->title,
                'type'         => 'Offres',
                'annee'        => $annee,
                'domaine'      => $domaine,
                'folder_path'  => $this->buildFolderPath('Offres', $annee, $domaine, $slug),
                'fichier'      => $fichiers,
                'date_archive' => now(),
                'archive_par'  => Auth::id(),
                'resultat'     => $offer->result ?? 'inconnu',
                'statut'       => 'archived',
            ]
        );
    }

    /**
     * Archive un Projet — compile contrat, livrables, rapports, avenants.
     */
    public function archiverProjet(Project $project): Archive
    {
        $project->loadMissing('reports', 'deliverables', 'amendments');

        $annee   = $project->actual_end_date?->year
                ?? $project->planned_end_date?->year
                ?? now()->year;
        $domaine = $this->formatDomaine(
            $project->offer?->manifestation?->domains[0] ?? 'Projets'
        );
        $slug    = $project->code ?? 'PROJ-' . $annee . '-' . $project->id;

        $fichiers = [];

        if ($project->contract_path) {
            $fichiers[] = ['nom' => 'Contrat', 'type' => 'contrat', 'chemin' => $project->contract_path];
        }

        foreach ($project->reports as $report) {
            if ($report->file_path) {
                $fichiers[] = ['nom' => $report->title, 'type' => 'rapport', 'chemin' => $report->file_path];
            }
        }

        foreach ($project->deliverables as $deliv) {
            if ($deliv->file_path) {
                $fichiers[] = ['nom' => $deliv->title, 'type' => 'livrable', 'chemin' => $deliv->file_path];
            }
        }

        foreach ($project->amendments as $amendment) {
            if ($amendment->file_path) {
                $fichiers[] = ['nom' => $amendment->title, 'type' => 'avenant', 'chemin' => $amendment->file_path];
            }
        }

        return Archive::updateOrCreate(
            ['entite_type' => Project::class, 'entite_id' => $project->id],
            [
                'titre'        => $project->title,
                'type'         => 'Livrable',
                'annee'        => $annee,
                'domaine'      => $domaine,
                'folder_path'  => $this->buildFolderPath('Projets', $annee, $domaine, $slug),
                'fichier'      => $fichiers,
                'date_archive' => now(),
                'archive_par'  => Auth::id(),
                'resultat'     => $project->status,
                'statut'       => 'archived',
            ]
        );
    }

    private function formatDomaine(string $domaine): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $domaine));
    }
}
