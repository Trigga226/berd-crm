<?php

namespace App\Utils;

class Domaines
{
    /**
     * Create a new class instance.
     */
    public static $DOMAINES = [
        // BTP & Construction
        'Architecture' => 'Architecture',
        'BTP' => 'Bâtiment et Travaux Publics',
        'Charpente / Couverture' => 'Charpente / Couverture',
        'Electricité Bâtiment' => 'Electricité Bâtiment',
        'Génie Civil' => 'Génie Civil',
        'Géotechnique' => 'Géotechnique',
        'Hydraulique' => 'Hydraulique',
        'Menuiserie' => 'Menuiserie',
        'Peinture / Finition' => 'Peinture / Finition',
        'Plomberie / Sanitaire' => 'Plomberie / Sanitaire',
        'Topographie' => 'Topographie',
        'Urbanisme' => 'Urbanisme',
        'VRD' => 'Voirie et Réseaux Divers',

        // Ingénierie & Industrie
        'Aéronautique' => 'Aéronautique',
        'Automatisme / Instrumentation' => 'Automatisme / Instrumentation',
        'Chimie' => 'Chimie',
        'Electronique' => 'Electronique',
        'Energie Renouvelable' => 'Energie Renouvelable',
        'Génie Chimique' => 'Génie Chimique',
        'Génie Climatique (CVC)' => 'Génie Climatique (CVC)',
        'Génie des Matériaux' => 'Génie des Matériaux',
        'Génie des Procédés' => 'Génie des Procédés',
        'Génie Electrique' => 'Génie Electrique',
        'Génie Industriel' => 'Génie Industriel',
        'Génie Mécanique' => 'Génie Mécanique',
        'Maintenance Industrielle' => 'Maintenance Industrielle',
        'Métallurgie / Soudure' => 'Métallurgie / Soudure',
        'Mines et Géologie' => 'Mines et Géologie',
        'Pétrole et Gaz' => 'Pétrole et Gaz',
        'Qualité, Hygiène, Sécurité, Environnement (QHSE)' => 'Qualité, Hygiène, Sécurité, Environnement (QHSE)',
        'Télécommunications' => 'Télécommunications',

        // Informatique & Numérique
        'Administration Système / Réseaux' => 'Administration Système / Réseaux',
        'Cloud Computing' => 'Cloud Computing',
        'Cybersécurité' => 'Cybersécurité',
        'Data Science / Big Data' => 'Data Science / Big Data',
        'Développement Mobile' => 'Développement Mobile',
        'Développement Web / Logiciel' => 'Développement Web / Logiciel',
        'DevOps' => 'DevOps',
        'Gestion de Projet IT' => 'Gestion de Projet IT',
        'Helpdesk / Support IT' => 'Helpdesk / Support IT',
        'Intelligence Artificielle' => 'Intelligence Artificielle',
        'UI/UX Design' => 'UI/UX Design',
        'Webmarketing / SEO' => 'Webmarketing / SEO',

        // Tertiaire & Services
        'Achats / Approvisionnements' => 'Achats / Approvisionnements',
        'Assurance' => 'Assurance',
        'Audit / Conseil' => 'Audit / Conseil',
        'Banque / Finance' => 'Banque / Finance',
        'Commerce / Vente' => 'Commerce / Vente',
        'Communication / Publicité' => 'Communication / Publicité',
        'Comptabilité' => 'Comptabilité',
        'Droit / Juridique' => 'Droit / Juridique',
        'Enseignement / Formation' => 'Enseignement / Formation',
        'Gestion / Administration' => 'Gestion / Administration',
        'Immobilier' => 'Immobilier',
        'Logistique / Supply Chain' => 'Logistique / Supply Chain',
        'Marketing' => 'Marketing',
        'Ressources Humaines' => 'Ressources Humaines',
        'Secrétariat / Assistanat' => 'Secrétariat / Assistanat',
        'Tourisme / Hôtellerie / Restauration' => 'Tourisme / Hôtellerie / Restauration',
        'Traduction / Interprétariat' => 'Traduction / Interprétariat',
        'Transport' => 'Transport',

        // Santé & Social
        'Action Sociale' => 'Action Sociale',
        'Biologie Médicale' => 'Biologie Médicale',
        'Médecine' => 'Médecine',
        'Pharmacie' => 'Pharmacie',
        'Paramédical' => 'Paramédical',
        'Psychologie' => 'Psychologie',
        'Santé Publique' => 'Santé Publique',
        'Soins Infirmiers' => 'Soins Infirmiers',

        // Agriculture & Environnement
        'Agriculture / Agronomie' => 'Agriculture / Agronomie',
        'Agroalimentaire' => 'Agroalimentaire',
        'Elevage' => 'Elevage',
        'Environnement' => 'Environnement',
        'Pêche / Aquaculture' => 'Pêche / Aquaculture',
        'Sylviculture / Forêt' => 'Sylviculture / Forêt',

        // Art & Culture
        'Architecture d\'intérieur' => 'Architecture d\'intérieur',
        'Artisanat' => 'Artisanat',
        'Audiovisuel / Cinéma' => 'Audiovisuel / Cinéma',
        'Design / Graphisme' => 'Design / Graphisme',
        'Journalisme / Édition' => 'Journalisme / Édition',
        'Mode / Textile' => 'Mode / Textile',
        'Musique / Spectacle' => 'Musique / Spectacle',
        'Audit et Conseil' => 'Audit et Conseil',
        'Aménagement du territoire' => 'Aménagement du territoire',
        'Social' => 'Social',
        'Appuis aux institutions' => 'Appuis aux institutions',
        'Infrastructures et aménagements' => 'Infrastructures et aménagements',
        'Développement Durable' => 'Développement Durable',
        'Eau et  Assainissement' => 'Eau et  Assainissement',
        'Suivis evaluations' => 'Suivis evaluations',
        'SIG et cartographie' => 'SIG et cartographie',
        'Développement Rural' => 'Développement Rural',
        'Planification' => 'Planification',
        'Autre' => 'Autre',
    ];

    public static function getOptions(): array
    {
        return collect(self::$DOMAINES)->map(fn($v) => iconv('UTF-8', 'UTF-8//IGNORE', $v))->toArray();
    }

    public static function getKeys(): array
    {
        return array_keys(self::$DOMAINES);
    }
}
