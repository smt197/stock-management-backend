# 📦 Guide d'Utilisation - Application de Gestion de Stock

## 👋 Bienvenue

Cette application vous permet de gérer efficacement le stock de votre boutique, suivre vos produits, gérer vos fournisseurs, et passer des commandes d'achat.

---

## 📱 Accès à l'Application

### URLs de l'Application
- **Application Web** : `https://stock-management-front-wvmn.onrender.com`
- **Connexion** : Utilisez vos identifiants fournis par l'administrateur

### Première Connexion
1. Ouvrez l'application dans votre navigateur
2. Entrez votre **email** et **mot de passe**
3. Cliquez sur **Se connecter**

> **Note** : Si l'application met du temps à charger, c'est normal ! Elle se réveille après une période d'inactivité (30-60 secondes).

---

## 🏪 Modules de l'Application

### 1. 📊 Tableau de Bord (Dashboard)
Le tableau de bord vous donne une vue d'ensemble de votre boutique :
- **Nombre total de produits** en stock
- **Valeur totale du stock** (en FCFA)
- **Produits en rupture** qui nécessitent une commande
- **Produits bientôt en rupture** (niveau de stock faible)
- **Graphiques** des mouvements de stock

### 2. 📦 Gestion des Produits
Module principal pour gérer tous vos articles en vente.

### 3. 🏢 Gestion des Fournisseurs
Liste de tous vos fournisseurs avec leurs coordonnées.

### 4. 🏷️ Gestion des Catégories
Organisation de vos produits par catégories (Électronique, Alimentaire, etc.).

### 5. 🛒 Commandes d'Achat
Gestion de vos commandes auprès des fournisseurs.

---

## 📝 Comprendre les Champs d'un Produit

Quand vous ajoutez ou modifiez un produit, voici ce que signifie chaque information :

### 1. **Nom du Produit** (`name`)
- **Définition** : Le nom commercial du produit tel qu'il apparaît sur l'étiquette
- **Exemple** : "Smartphone Samsung Galaxy A54", "Riz Uncle Ben's 5kg", "Lait Gloria 1L"
- **Utilité** : C'est ce que vos clients voient et cherchent
- **Obligatoire** : ✅ Oui

### 2. **Description** (`description`)
- **Définition** : Détails complets sur le produit (caractéristiques, composition, usage)
- **Exemple** :
  - "Smartphone avec écran 6.4 pouces, 128GB, caméra 50MP, 5G"
  - "Riz long grain de qualité supérieure, origine Thaïlande"
- **Utilité** : Aide à identifier précisément le produit et informer les clients
- **Obligatoire** : ❌ Non (mais recommandé)

### 3. **SKU (Stock Keeping Unit)** (`sku`)
- **Définition** : Code unique d'identification interne du produit
- **Format** : Lettres + chiffres, sans espaces
- **Exemples** :
  - `SAMS-A54-128-BLK` (Samsung A54, 128GB, Noir)
  - `RIZ-UB-5KG` (Riz Uncle Ben's 5kg)
  - `LAIT-GLO-1L` (Lait Gloria 1L)
- **Utilité** : Permet de retrouver rapidement un produit dans votre système
- **Obligatoire** : ✅ Oui
- **Important** : Doit être unique pour chaque produit

### 4. **Code-barres** (`barcode`)
- **Définition** : Le code-barres imprimé sur l'emballage du produit (EAN-13, UPC, etc.)
- **Format** : Généralement 13 chiffres
- **Exemple** : `3245414598571`, `8850389105702`
- **Utilité** : Permet de scanner le produit avec une douchette/scanner
- **Obligatoire** : ❌ Non
- **Conseil** : Utilisez un scanner pour éviter les erreurs de saisie

### 5. **Catégorie** (`category_id`)
- **Définition** : Le type/famille du produit
- **Exemples** :
  - Électronique
  - Alimentaire
  - Boissons
  - Hygiène & Beauté
  - Papeterie
- **Utilité** : Organiser vos produits et générer des rapports par catégorie
- **Obligatoire** : ✅ Oui

### 6. **Fournisseur** (`supplier_id`)
- **Définition** : L'entreprise qui vous vend ce produit
- **Exemple** : "Samsung Cameroun", "Distributeur CFAO", "Socapalm"
- **Utilité** : Savoir qui contacter pour commander ce produit
- **Obligatoire** : ❌ Non (mais fortement recommandé)

### 7. **Prix de Vente Unitaire** (`unit_price`)
- **Définition** : Le prix auquel VOUS VENDEZ le produit à vos clients (en FCFA)
- **Exemple** : `350 000 FCFA` pour un smartphone, `2 500 FCFA` pour un sac de riz
- **Utilité** : Calculer le chiffre d'affaires et la marge bénéficiaire
- **Obligatoire** : ✅ Oui
- **Format** : Nombre sans séparateurs (exemple : 350000)

### 8. **Prix d'Achat/Coût** (`cost_price`)
- **Définition** : Le prix auquel VOUS ACHETEZ le produit chez le fournisseur (en FCFA)
- **Exemple** : `280 000 FCFA` si vous vendez à `350 000 FCFA`
- **Utilité** :
  - Calculer votre **marge bénéficiaire** : 350 000 - 280 000 = **70 000 FCFA** de profit
  - Calculer la valeur totale de votre stock
- **Obligatoire** : ❌ Non (mais très important pour connaître vos profits)
- **Confidentialité** : Cette information reste dans votre système, vos clients ne la voient pas

### 9. **Quantité en Stock** (`quantity`)
- **Définition** : Combien d'unités de ce produit vous avez actuellement en magasin
- **Exemple** : `45` (vous avez 45 unités disponibles)
- **Utilité** :
  - Savoir si vous pouvez vendre le produit
  - Recevoir des alertes quand le stock est bas
  - Éviter les ruptures de stock
- **Obligatoire** : ✅ Oui
- **Mise à jour** : Se met à jour automatiquement quand vous recevez des commandes

### 10. **Quantité Minimale** (`min_quantity`)
- **Définition** : Le niveau de stock en dessous duquel vous devez recommander
- **Exemple** : `10` (alerte si moins de 10 unités)
- **Utilité** :
  - L'application vous **alerte automatiquement** quand le stock atteint ce niveau
  - Éviter les ruptures de stock
  - Planifier vos commandes à l'avance
- **Obligatoire** : ❌ Non (mais très utile)
- **Conseil** : Basez-vous sur votre rythme de vente hebdomadaire

### 11. **Quantité Maximale** (`max_quantity`)
- **Définition** : La quantité maximale que vous souhaitez stocker
- **Exemple** : `100` (ne jamais dépasser 100 unités)
- **Utilité** :
  - Éviter le surstockage (capital immobilisé)
  - Optimiser l'espace de stockage
  - Éviter les produits périmés
- **Obligatoire** : ❌ Non
- **Conseil** : Basez-vous sur votre capacité de stockage et rotation des produits

### 12. **Image** (`image`)
- **Définition** : Photo du produit uploadée depuis votre ordinateur
- **Format** : JPG, PNG (max 2MB recommandé)
- **Utilité** :
  - Identification visuelle rapide
  - Aide pour les inventaires
  - Peut être utilisée dans un futur catalogue
- **Obligatoire** : ❌ Non

### 13. **URL de l'Image** (`image_url`)
- **Définition** : Adresse web d'une image du produit (alternative à l'upload)
- **Exemple** : `https://example.com/produit.jpg`
- **Utilité** : Utiliser une image existante sur internet
- **Obligatoire** : ❌ Non
- **Note** : Utilisez soit l'upload, soit l'URL, pas les deux

### 14. **Statut** (`status`)
- **Définition** : État actuel du produit dans votre système
- **Valeurs possibles** :
  - ✅ **Actif** : Produit en vente, visible dans le système
  - ❌ **Inactif** : Produit temporairement non disponible (arrêt de vente, produit saisonnier)
  - 🗑️ **Archivé** : Produit que vous ne vendez plus (conservé pour l'historique)
- **Utilité** : Gérer votre catalogue sans supprimer les données
- **Obligatoire** : ✅ Oui (Actif par défaut)

---

## 🎯 Exemple Concret : Gestion Complète d'un Produit

### Scénario Réel

Vous êtes gérant d'une boutique de quartier à Yaoundé. Vous vendez des téléphones et de l'alimentation. Suivons le parcours complet d'un produit de A à Z.

---

### 📱 ÉTAPE 1 : Ajout d'un Nouveau Produit

**Situation** : Vous décidez de vendre des smartphones Samsung Galaxy A54.

#### Actions :

1. **Allez dans "Produits"** → Cliquez sur **"+ Nouveau Produit"**

2. **Remplissez les informations** :

| Champ | Valeur | Pourquoi ? |
|-------|--------|------------|
| **Nom** | `Smartphone Samsung Galaxy A54 5G 128GB` | Nom complet pour que vos employés sachent de quoi il s'agit |
| **Description** | `Écran 6.4" Super AMOLED, Caméra 50MP, 128GB de stockage, 5G, Batterie 5000mAh, Couleur Noir` | Caractéristiques complètes pour identifier la variante exacte |
| **SKU** | `SAMS-A54-128-BLK` | Code interne facile à retenir (SAMSUNG-A54-128GB-BLACK) |
| **Code-barres** | `8806094786866` | Scanné depuis la boîte du téléphone |
| **Catégorie** | `Électronique` | Pour les statistiques et organisation |
| **Fournisseur** | `Samsung Cameroun` | Vous savez qui contacter pour commander |
| **Prix de vente** | `350000` | Prix au client = 350 000 FCFA |
| **Prix d'achat** | `280000` | Vous l'achetez à 280 000 FCFA → **Marge = 70 000 FCFA** |
| **Quantité** | `0` | Vous n'en avez pas encore, vous allez commander |
| **Quantité min** | `3` | Alerte si moins de 3 unités (vous en vendez ~1 par semaine) |
| **Quantité max** | `15` | Maximum 15 unités (espace limité + capital) |
| **Image** | *Upload photo* | Photo du téléphone pour identification |
| **Statut** | `Actif` | Produit en vente |

3. **Cliquez sur "Enregistrer"**

✅ **Résultat** : Le produit est créé mais la quantité est à **0**. Il apparaît dans la liste des **"Produits en rupture"** sur le tableau de bord.

---

### 🛒 ÉTAPE 2 : Passer une Commande au Fournisseur

**Situation** : Vous devez commander 10 téléphones Samsung auprès de votre fournisseur.

#### Actions :

1. **Allez dans "Commandes d'Achat"** → Cliquez sur **"+ Nouvelle Commande"**

2. **Remplissez la commande** :

| Champ | Valeur | Explication |
|-------|--------|-------------|
| **Référence** | `CMD-2025-001` | Numéro de commande (généré automatiquement) |
| **Fournisseur** | `Samsung Cameroun` | Qui vous fournit les téléphones |
| **Date de commande** | `12/11/2025` | Aujourd'hui |
| **Date de livraison** | `19/11/2025` | Livraison prévue dans 7 jours |
| **Statut** | `En attente` | La commande est passée mais pas encore livrée |

3. **Ajoutez les articles** :

Cliquez sur **"+ Ajouter un article"** :
- **Produit** : `Smartphone Samsung Galaxy A54...`
- **Quantité commandée** : `10` unités
- **Prix unitaire** : `280 000 FCFA` (prix d'achat)
- **Total ligne** : `2 800 000 FCFA` (calculé automatiquement)

4. **Vérifiez le total** :
- **Total de la commande** : `2 800 000 FCFA`

5. **Cliquez sur "Enregistrer"**

✅ **Résultat** : La commande est enregistrée avec le statut **"En attente"**.

---

### 📦 ÉTAPE 3 : Réception de la Commande

**Situation** : 5 jours plus tard, le livreur de Samsung arrive avec votre commande. Vous avez commandé 10 téléphones, mais il n'en apporte que **8** (rupture de stock chez Samsung).

#### Actions :

1. **Allez dans "Commandes d'Achat"** → Cliquez sur votre commande **CMD-2025-001**

2. **Cliquez sur "Recevoir"**

3. **Indiquez les quantités réellement reçues** :

| Produit | Commandé | Reçu | Statut |
|---------|----------|------|--------|
| Samsung Galaxy A54 | 10 | `8` ⚠️ | Partiellement reçu |

4. **Raison de la différence** (optionnel) :
```
Rupture de stock chez le fournisseur.
Livraison complète prévue la semaine prochaine.
```

5. **Cliquez sur "Valider la Réception"**

✅ **Résultats automatiques** :
- ✅ Le stock du produit passe de **0** à **8 unités**
- ✅ Le statut de la commande passe à **"Partiellement reçue"**
- ✅ Le produit disparaît de la liste "Rupture de stock"
- ✅ La valeur de votre stock augmente de : `8 × 280 000 = 2 240 000 FCFA`

---

### 💰 ÉTAPE 4 : Vente d'un Produit

**Situation** : Un client entre dans votre boutique et achète 1 téléphone Samsung Galaxy A54.

#### Actions (Vente Manuelle) :

1. **Allez dans "Produits"** → Trouvez le `Samsung Galaxy A54`

2. **Cliquez sur "Modifier"**

3. **Modifiez la quantité** :
   - Ancienne quantité : `8`
   - Nouvelle quantité : `7` (vous en avez vendu 1)

4. **Cliquez sur "Enregistrer"**

✅ **Résultats automatiques** :
- ✅ Stock passe de **8** à **7 unités**
- ✅ Vous avez gagné : `350 000 - 280 000 = 70 000 FCFA` de profit sur cette vente

> **Note Importante** : Dans une version future, un module "Ventes" permettra d'enregistrer automatiquement les ventes avec factures et tickets de caisse.

---

### 🔔 ÉTAPE 5 : Alerte de Stock Faible

**Situation** : Vous avez vendu 5 téléphones Samsung. Il vous reste maintenant **2 unités**.

#### Ce qui se passe automatiquement :

1. **Sur le tableau de bord**, vous voyez une **alerte rouge** :
   ```
   ⚠️ Produits en stock faible : 1 produit
   ```

2. **Le produit apparaît dans "Stock Faible"** :
   ```
   📱 Samsung Galaxy A54
   Stock actuel : 2 unités
   Seuil minimum : 3 unités
   → Recommander 13 unités pour atteindre le max (15)
   ```

#### Actions recommandées :

1. **Passez une nouvelle commande** avant la rupture de stock
2. **Ajustez le seuil minimal** si besoin (par exemple, passer à 5 unités si les ventes ont augmenté)

---

### 📊 ÉTAPE 6 : Suivi et Analyse

#### Tableau de Bord - Ce que vous voyez :

**Statistiques Générales** :
- **Produits en stock** : 47 produits
- **Valeur totale du stock** : 18 450 000 FCFA
- **Produits en rupture** : 2 produits ⚠️
- **Stock faible** : 1 produit ⚠️

**Graphiques** :
- Évolution du stock par catégorie
- Produits les plus vendus
- Mouvements de stock (entrées/sorties)

**Actions Rapides** :
- 🔴 Cliquez sur "Produits en rupture" → Liste des produits à commander d'urgence
- 🟠 Cliquez sur "Stock faible" → Liste des produits à surveiller

---

## 📋 Exemple avec un Produit Alimentaire

### Cas : Riz Uncle Ben's 5kg

| Champ | Valeur | Notes |
|-------|--------|-------|
| **Nom** | `Riz Uncle Ben's Long Grain 5kg` | Nom exact du produit |
| **Description** | `Riz long grain de qualité supérieure, origine Thaïlande, sachet de 5kg` | Description complète |
| **SKU** | `RIZ-UB-5KG` | Code simplifié |
| **Code-barres** | `3245414598571` | Code EAN-13 sur le paquet |
| **Catégorie** | `Alimentaire` | Catégorie produit |
| **Fournisseur** | `CFAO Distribution` | Votre grossiste |
| **Prix de vente** | `4500` | Vous vendez à 4 500 FCFA |
| **Prix d'achat** | `3200` | Vous achetez à 3 200 FCFA → **Marge = 1 300 FCFA** |
| **Quantité** | `50` | 50 sacs en stock |
| **Quantité min** | `20` | Alerte si moins de 20 sacs |
| **Quantité max** | `100` | Maximum 100 sacs (espace de stockage) |
| **Statut** | `Actif` | En vente active |

**Calculs automatiques** :
- **Valeur en stock** : 50 × 3 200 = `160 000 FCFA` (ce que vous avez investi)
- **Valeur de vente potentielle** : 50 × 4 500 = `225 000 FCFA`
- **Profit potentiel** : 50 × 1 300 = `65 000 FCFA`

---

## 💡 Conseils Pratiques

### 1. Définir les Seuils de Stock

**Comment calculer le stock minimum ?**

Formule simple :
```
Stock minimum = (Ventes hebdomadaires moyennes) × (Délai de livraison en semaines) + Stock de sécurité
```

**Exemple** :
- Vous vendez **5 Samsung A54 par semaine**
- Le fournisseur livre en **2 semaines**
- Stock de sécurité : **2 unités**

```
Stock minimum = (5 × 2) + 2 = 12 unités
```

### 2. Organiser vos Produits

**Créez des catégories claires** :
- ✅ Électronique → Téléphones, Ordinateurs, Accessoires
- ✅ Alimentaire → Riz, Huile, Conserves, Épices
- ✅ Boissons → Eau, Sodas, Jus
- ✅ Hygiène → Savons, Shampoing, Dentifrice

### 3. Convention de Nommage SKU

**Utilisez un système cohérent** :
```
[MARQUE]-[MODÈLE]-[TAILLE/CAPACITÉ]-[COULEUR/VARIANTE]
```

**Exemples** :
- `SAMS-A54-128-BLK` → Samsung A54 128GB Noir
- `SAMS-A54-128-WHT` → Samsung A54 128GB Blanc
- `IPH-14-256-BLU` → iPhone 14 256GB Bleu

### 4. Gestion des Prix

**Calculer votre marge** :
```
Marge (%) = [(Prix de vente - Prix d'achat) / Prix d'achat] × 100
```

**Exemple** :
```
Prix d'achat : 280 000 FCFA
Prix de vente : 350 000 FCFA
Marge = [(350 000 - 280 000) / 280 000] × 100 = 25%
```

**Marges recommandées par secteur** :
- Électronique : 15-25%
- Alimentaire : 20-35%
- Cosmétiques : 30-50%
- Vêtements : 50-100%

### 5. Inventaire Régulier

**Planifiez des inventaires** :
- 📅 Inventaire complet : 1 fois par mois
- 📅 Inventaire des best-sellers : 1 fois par semaine
- 📅 Vérification des alertes : Tous les jours

---

## ❓ Questions Fréquentes

### Q1 : Que faire si je me trompe en entrant la quantité ?
**R** : Allez dans "Produits" → Cliquez sur le produit → "Modifier" → Corrigez la quantité → "Enregistrer"

### Q2 : Comment archiver un produit que je ne vends plus ?
**R** : Modifiez le produit → Changez le statut à "Archivé". Le produit reste dans le système mais n'apparaît plus dans les listes actives.

### Q3 : Puis-je avoir plusieurs fournisseurs pour un même produit ?
**R** : Actuellement, un produit = un fournisseur. Si vous avez plusieurs sources, créez des SKU différents (ex: `RIZ-UB-5KG-F1` et `RIZ-UB-5KG-F2`).

### Q4 : Comment savoir combien j'ai gagné sur un produit ?
**R** : Le profit = Prix de vente - Prix d'achat. L'application calcule automatiquement la valeur de votre stock basée sur les prix d'achat.

### Q5 : L'application fonctionne-t-elle hors ligne ?
**R** : Non, une connexion internet est nécessaire. Cependant, vous pouvez noter vos ventes sur papier et les saisir plus tard.

### Q6 : Comment imprimer la liste de mes produits ?
**R** : Utilisez la fonction "Exporter" pour télécharger un fichier Excel, puis imprimez-le.

### Q7 : Puis-je gérer plusieurs boutiques ?
**R** : Actuellement, l'application gère une seule boutique. Pour plusieurs boutiques, créez des catégories par emplacement.

---

## 🔐 Sécurité et Bonnes Pratiques

### Protégez vos Données

1. **Ne partagez jamais vos identifiants**
2. **Changez votre mot de passe régulièrement** (tous les 3 mois)
3. **Déconnectez-vous** quand vous quittez l'ordinateur
4. **Faites des sauvegardes** : Exportez vos données chaque semaine

### Vérifications Quotidiennes

Chaque matin, vérifiez :
- ✅ Produits en rupture de stock
- ✅ Produits en stock faible
- ✅ Commandes en attente de livraison
- ✅ Valeur totale du stock

---

## 📞 Support et Assistance

### Besoin d'aide ?

**Pour les problèmes techniques** :
- 📧 Email : support@votreentreprise.com
- 📱 WhatsApp : +237 6XX XXX XXX

**Pour la formation** :
Des sessions de formation sont disponibles pour vous et votre équipe.

---

## 📈 Évolutions Futures

L'application continuera d'évoluer avec de nouvelles fonctionnalités :
- 🎯 Module de ventes avec facturation automatique
- 📊 Rapports détaillés (best-sellers, marges, rotations)
- 📱 Application mobile
- 🖨️ Impression d'étiquettes avec codes-barres
- 📧 Notifications email automatiques pour les alertes de stock

---

**Dernière mise à jour** : 11 novembre 2025

**Version de l'application** : 1.0

---

*Ce guide est conçu pour vous aider à tirer le meilleur parti de votre application de gestion de stock. N'hésitez pas à le consulter régulièrement !* 📚
