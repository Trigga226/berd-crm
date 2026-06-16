<?php

namespace App\Support;

/**
 * Référentiel des pièces standard composant les offres technique et financière.
 *
 * Sert à la fois :
 *  - de pré-remplissage des répéteurs réordonnables dans le formulaire d'offre ;
 *  - de source de vérité pour la migration de reprise des données existantes
 *    (intitulés + ordre par défaut).
 *
 * Les utilisateurs restent libres de renommer, réordonner, ajouter ou supprimer
 * ces pièces par offre ; ce référentiel ne fournit que la composition initiale.
 */
class OfferDocumentSections
{
    /**
     * Pièces standard de l'offre technique, dans l'ordre, indexées par clé technique.
     *
     * @return array<string, string>
     */
    public static function technical(): array
    {
        return [
            'tech_cover'   => 'Page de garde',
            'tech_summary' => 'Sommaire',
            'tech_1_1'     => 'Lettre de soumission',
            'tech_1_2'     => 'Accord de groupement',
            'tech_1_3'     => 'Pouvoir Habilitation / Statut Juridique',
            'tech_1_4'     => 'Pouvoir Chef de file',
            'tech_1_5'     => 'Pièces Admin',
            'tech_2_a'     => 'Organisation',
            'tech_2_b'     => 'Expérience',
            'tech_3_a'     => 'Commentaires sur TDR',
            'tech_3_b'     => 'Commentaires sur Personnel/Prestations',
            'tech_4'       => 'Méthodologie',
            'tech_5'       => 'Programme et Calendrier',
            'tech_6_1'     => 'Composition Équipe',
            'tech_6_2'     => 'CVs et Disponibilité',
            'tech_7'       => 'Code de Conduite',
            'tech_other_1' => 'Document Additionnel 1',
            'tech_other_2' => 'Document Additionnel 2',
            'tech_other_3' => 'Document Additionnel 3',
        ];
    }

    /**
     * Pièces standard de l'offre financière, dans l'ordre, indexées par clé technique.
     *
     * @return array<string, string>
     */
    public static function financial(): array
    {
        return [
            'fine_cover'   => 'Page de garde',
            'fine_1'       => 'Lettre de soumission',
            'fine_2'       => 'Tableau Récapitulatif',
            'fine_3'       => 'Sous détail rémunération',
            'fine_4'       => 'Autres dépenses',
            'fine_5'       => 'Déclaration des coûts',
            'fine_other_1' => 'Document Additionnel 1',
            'fine_other_2' => 'Document Additionnel 2',
            'fine_other_3' => 'Document Additionnel 3',
        ];
    }

    /**
     * Retourne l'intitulé par défaut d'une clé technique (ou null si inconnue).
     */
    public static function labelFor(string $type): ?string
    {
        return self::technical()[$type] ?? self::financial()[$type] ?? null;
    }

    /**
     * Pré-remplissage par défaut d'un répéteur, pour une catégorie donnée.
     *
     * @return list<array{category: string, type: string, label: string, sort_order: int}>
     */
    public static function defaults(string $category): array
    {
        $map = $category === 'financial' ? self::financial() : self::technical();

        $items = [];
        $order = 0;
        foreach ($map as $type => $label) {
            $items[] = [
                'category'   => $category,
                'type'       => $type,
                'label'      => $label,
                'sort_order' => $order++,
            ];
        }

        return $items;
    }
}
