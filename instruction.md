# Integration du module de gestion de projet.

1. Concepte général 
Tu es un développeur senior laravel filament php.Analyse le projet et intègre la gestion de projet.

Ce module gèrera les projets de la société.
Un projet decoule d'un offre et comprend:
 * Une titre,pays,client,etat,pourcentage d'exécution, etc...
 * Un planning des activities,des livrables,etc...
 * Une equipe d'expert
 * Un chef de projet qui peut faire partire des utilisateurs de l'entreprise ou un expert
 * la gestion des contrats des experts (Juste sélectionner les experts depuis la base de données et uploader son contrat)
 * le document du contrat du projet
 * Les date de debut et de fin prevue du projet
 * Les avenant au contrat et leur incidence 
 * La gestion de le facturation (Les facture sont basés sur la validation des livrable.Chaque livrable a une facture.)

Cette liste n'est pas exaustivve tu pourras après analyse professionnel proposé des aspects a rajouter


Tout les documents d'un projet seront stockés dans:
1. public/projet/titre projet/contrat/fichier pour le contrat du projet
2. public/projet/titre projet/contrat-expert/fichier pour les contrat des expert du projet
3. public/projet/titre projet/livrable/fichier pour les livrable du projet
4. public/projet/titre projet/avenant/fichier pour les avenant du projet
5. public/projet/titre projet/facture/fichier pour les facture du projet

Cette liste n'est pas exaustivve tu pourras après analyse professionnel proposé des aspects a rajouter
