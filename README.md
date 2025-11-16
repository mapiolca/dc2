# Module Formulaire DC2 pour Dolibarr

Ce module ajoute un onglet "DC2" aux propositions commerciales afin de générer et remplir le formulaire DC2 directement depuis Dolibarr. Il s'adresse aux structures qui répondent à des marchés publics et souhaitent centraliser la préparation du dossier de candidature.

## Prérequis

- Dolibarr 19.0 ou version supérieure
- Modules Dolibarr activés : Projets, Tiers, DC1
- PHP 8.0 ou version supérieure

## Installation

1. Copier le répertoire `dc2` dans le dossier `custom` de votre instance Dolibarr.
2. Vérifier que les permissions des fichiers permettent l'exécution par le serveur web.
3. Activer le module depuis **Accueil > Configuration > Modules/Applications**, catégorie « Les Métiers du Bâtiment ».
4. Confirmer la création des répertoires de travail lorsque Dolibarr le propose.

## Configuration

- Le module ajoute automatiquement un nouvel onglet « DC2 » sur les propositions commerciales.
- Les droits utilisateurs sont gérés via le jeu de permissions « Formulaire DC2 » : lecture, création et suppression des lignes du formulaire.
- L'onglet utilise les extrafields et données existantes des tiers et des propositions pour préremplir les sections du formulaire DC2.

## Utilisation

1. Ouvrir une proposition commerciale existante.
2. Accéder à l'onglet « DC2 » pour saisir ou mettre à jour les informations du formulaire.
3. Sélectionner le modèle de document « DC2 » puis cliquer sur « Générer » pour produire le PDF.
4. En cas de candidature groupée, renseigner les membres du groupement dans la section dédiée avant génération.

## Multientité (Multicompany)

Le module est compatible avec Multicompany : les tables de données utilisent le préfixe Dolibarr et respectent l'isolation par entité. Vérifier que les droits et constantes sont configurés par entité selon vos besoins.

## Support et maintenance

- Éditeur : Les Métiers du Bâtiment
- Contact : Pierre Ardoin <developpeur@lesmetiersdubatiment.fr>
- Licence : GPL v3 ou ultérieure

Pour toute anomalie ou suggestion, merci d'ouvrir un ticket en fournissant le journal Dolibarr et les étapes de reproduction.
