# Délégation ChangeLog

## 4.3.9 (14/11/2024)

- Correction d'un problème empêchant l'ajout de ligne dans les délégation de paiement fournisseur.
- Suppression de la partie Qonto de synchronisation avec Qonto car géré en direct par la banque.

## 4.3.8 (13/05/2024)

- Modifications pour compatibilité PHP 8

## 4.3.7 (26/04/2023)

- Ajout du modèle de document "contrat_lmdb" dans les propales.

## 4.3.6 (13/04/2022)

- Ajout de modèle de document pour INPOSE qui force l'affichage de la description produit dans les commandes fournisseurs.

## 4.3.5 (12/04/2022)

- Correction d'un problème de date initiale du document dans les DC4.
- Ajout d'un message d'erreur au cas où la commande fournisseurs ne serait pas au statut « Commandé » ou ultérieur et récupération de la date de procédure la plus récente à chaque mise à jour de champs.
- Correction de problème « d'inertie » d'affichage après la validation d'un formulaire DC4, DC1 ou DC2.

## 4.3.4 (07/09/2021)

- Correction d'un problème empêchant l'affichage des images lors de la génération d'une fiche produit

## 4.3.3 (23/11/2020)

- Correction d'un problème pouvant créer des doublons dans les écritures bancaires fournisseurs.

## 4.3.2 (20/10/2020)

- Compatibilité avec v12 des modèles de documents délégation, DC4, etc... dans les commandes fournisseurs.

## 4.3.1 (06/10/2020)

- Correction d'un problème pouvant empêcher l'apparition du formulaire dans l'onglet DC4 des commandes client.
- Compatibilité avec v12.

## 4.3.0 (09/06/2020)

- Ajout du support des Contrats de sous-traitance dans les commandes client.


## 4.2.1 (27/05/2020)

- Active les constantes BANK_ASK_PAYMENT_BANK_DURING_ORDER et BANK_ASK_PAYMENT_BANK_DURING_PROPOSAL à l'activation du module
- L'extrafield lmdb_commande_account n'est activé que si BANK_ASK_PAYMENT_BANK_DURING_ORDER n'existe pas.

## 4.2.0 (27/05/2020)

- Corrige des problèmes avec les données récupérées dans les tiers et les RIB.
- Les traductions des Extrafields sont désormais ajoutées à l'activation du module.

## 4.1.0 (15/05/2020)

Ajout du support des formulaires DC4 dans les commandes clients.

## 4.0.1 (27/04/2020)

Corrections diverses

## 4.0.0 (24/04/2020)

Ajout d'une fonction d'évaluation pour les produits.

## 3.3.1 (16/04/2020)

Correctif compatibilité du modèle « Les Métiers du Bâtiment » pour la génération de fiche produit/service avec v11 et Multicompany

## 3.3.0 (15/04/2020)

Dans l'onglet « Budget », les Postes ne comportant pas de sous-poste sont désormais masqués.

## 3.2.1 (14/04/2020)

Correction d'un problème pouvant empecher le passage d'une facture fournisseur à l'état payé dans les liste alors qu'elle l'est dans sa fiche.

## 3.2.0 (11/04/2020)

Refonte des pages de reglages.

## 3.1.0 (09/04/2020)

Ajout dans la balance Profits/Pertes basée sur les commandes clients/fournisseurs dans l'onglet « Budget ».

## 3.0.1 (09/04/2020)

Correction de problèmes d'interface dans l'onglet « Budget », et de sécurité dans les onglets « Budget », « Détails », « DC1 » et « DC2 ».

## 3.0.0 (08/04/2020)

Connexion API Banque :
	- Support de la néobanque pour les professionnels QONTO
	- Rapprochement automatique des Paiements Clients/Fournisseurs avec la banque
	- Création automatique des paiements Clients/Fournisseurs lorsqu'ils apparaissent sur le compte.
Suivi des Budgets :
	- Intégration de la possibilité de sélectionner une Tâche à lier à une commande fournisseur (S'effectue dans la commande)
	- Onglet "Budget" dans les projets pour synthétiser toutes les Tâches liées aux commandes fournisseurs dans un tableau de suivi de budget.

## 2.1.2 (25/03/2020)

Correction d'un problème provocant la disparition des lignes SIREN, SIRET, etc.. lors de l'activation du Module.
Ajout de la possibilité de désactiver l'intégration de l'autocomplétion du champs NAF/APE.

## 2.1.1

Intégration de la Bibliothèque de code APE/NAF via le "Hook" getInputIdProf. (Version 12 requise)

## 2.1.0

Démarrage connection aux API des Banques :
	- Connexion à API QONTO -> Solde et écritures consultables dans l'onglet "Compte".

## 2.0.0

Ajout d'une Bibliothèque de code APE/NAF

## 1.10.2
Corrections dans le caractère requis de certains Extrafields.


## 1.10.1

Mise à jour du Modèle de Délégation Clairsienne
Intégration automatique des Extrafields lors de l'activbation du module, et prise en charge des traduction.
Mise à jour des noms des Extrafields pour éviter interférences avec d'autres modules.
Intégration des nouveaux Extrafields dans les modèles de documents.

## 1.10

Ajout de Modèle de Délégation de Paielement Tripartite de SPIE BATIGNOLLES SUD OUEST

## 1.9.3

Correction d'un bug empêchant l'onglet "DC1" de s'ouvrir correctement dans une Proposition Commerciale.

## 1.9.2

Prise en charge des actes modificatifs dans les délégations de paiement + Corrections CSS dans le modèle.
Modification de la prise en charge de la date dans les DC4 ( par défaut date de la commande pour le champs dc4_date_initiale et dol_now() pour la date de remise du PPSPS) afin de ne pas avoir à remonter tout le calendrier lors de la saisie.

## 1.9.1

Correction d'un problème pouvant empecher la création des données de détail projet dans la base de données.

## 1.9

Refonte de la gestion des DC4, contrats de sous-traitance et Délégations de paiement dans les commandes fournisseurs et les projets.

## 1.8

Ajout du modèle de Formulaire DC2 pour la candidature au marché publics
Structure du formulaire DC1 améliorée pour plus de lisibilité et Traductibilité revue.

## 1.7.3

Correction d'une erreur d'affichage dans les colonnes des factures de Situation du Modèle BTP LMDB.

## 1.7.2

Correction d'un problème empêchant la mise à jour ou la suppression d'une ligne dans l'onglet Facture->Délégation.

## 1.7.1

Correction d'un bug d'affichage dans les factures de situation quand le paramètre "PDF-> Cacher les détails des lignes de produits " est activé.

## 1.7

Ajout du modèle de Formulaire DC1 pour la candidature au marché publics

## 1.6.1

Ajout du Poste et du Projet dans le modèle de demande de prix fournisseurs.

## 1.6.0

Ajout d'un modèle de demande de prix fournisseurs prenant en charge les liens vers les fichiers (ex : weTransfer, dropbox, etc.)

## 1.5.4

Corrections des situations de Travaux lors de la présence d'avoir sur situation

## 1.5.3

Ajout des LCR et des modèles de DC4 et contrats de sous-traitance

## 1.5.2

Corrections des Calculs et modifications de l'orientation des pages dans les situations

## 1.5.1

Ajout du Tableau Récapitulatif dans les Situations de Travaux

## 1.5.0

Ajout du Modèle de Situation de Travaux

## 1.4.0

Ajout du Modèle de Commande Client

## 1.3.0

Ajout du Modèle de Facture Client

## 1.2.0

Ajoput du Modèle de Devis

## 1.1.0

Ajout du Modèle de Commande Fournisseur

## 1.0.0

Création du Modulde de délégation de paiement