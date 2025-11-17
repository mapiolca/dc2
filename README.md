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

## English version

### Module overview

The DC2 form module adds a dedicated tab to commercial proposals so French public procurement candidates can generate and fill the DC2 declaration without leaving Dolibarr. It centralizes the drafting of tender responses and reuses existing Dolibarr data to speed up preparation.

### Prerequisites

- Dolibarr 19.0 or later
- Enabled Dolibarr modules: Projects, Third Parties, DC1
- PHP 8.0 or later

### Installation

1. Copy the `dc2` directory into the `custom` folder of your Dolibarr instance.
2. Check that file permissions allow execution by the web server user.
3. Activate the module from **Home > Setup > Modules/Applications**, category “Les Métiers du Bâtiment”.
4. Confirm the creation of working directories when Dolibarr prompts you to do so.

### Configuration

- The module automatically adds a “DC2” tab to commercial proposals.
- User rights are managed through the “Formulaire DC2” permission set: read, create, and delete lines in the form.
- The tab relies on existing third-party data and proposal details to prefill the DC2 form sections.

### Usage

1. Open an existing commercial proposal.
2. Access the “DC2” tab to enter or update the form information.
3. Select the “DC2” document template and click “Generate” to produce the PDF.
4. For joint bids, populate the grouping members section before generating the document.

### Multicompany

The module is compatible with Multicompany: data tables use the Dolibarr prefix and respect entity isolation. Ensure rights and constants are configured per entity as needed.

### Support and maintenance

- Publisher: Les Métiers du Bâtiment
- Contact: Pierre Ardoin <developpeur@lesmetiersdubatiment.fr>
- License: GPL v3 or later

For any issue or suggestion, please open a ticket and include the Dolibarr log along with the reproduction steps.
