# 🚀 Déploiement Backend - Stock Management

Ce guide explique comment déployer le backend Laravel sur Render avec ServersideUp.

## 📋 Prérequis

- Compte Render.com
- Repository Git (GitHub, GitLab, etc.)
- PostgreSQL (fourni par Render)

## 🔧 Configuration

### 1. Fichiers de déploiement

Les fichiers suivants sont nécessaires pour le déploiement:

- `Dockerfile` - Configuration Docker avec ServersideUp PHP
- `render.yaml` - Configuration Render
- `scripts/docker-entrypoint.sh` - Script de démarrage
- `.dockerignore` - Fichiers à exclure du build
- `.env.production.example` - Variables d'environnement de production

### 2. Configuration Render

Le fichier `render.yaml` configure automatiquement:

- ✅ Service web Laravel (plan free)
- ✅ Base de données PostgreSQL (plan free)
- ✅ Variables d'environnement
- ✅ Health check endpoint

### 3. Variables d'environnement

Les variables suivantes sont configurées automatiquement:

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Clé d'application Laravel (format base64) configurée dans render.yaml |
| `DB_*` | Connexion PostgreSQL (depuis la base Render via `fromDatabase`) |
| `FRONTEND_URL` | URL du frontend Angular |
| `SANCTUM_STATEFUL_DOMAINS` | Domaines autorisés pour Sanctum |
| `SEED_DATABASE` | `true` pour seeding auto, `false` en production normale |

**Important**: L'APP_KEY doit être une clé Laravel valide générée avec `php artisan key:generate --show`.

### 4. Fonctionnalités

#### Health Check
Endpoint disponible sur `/api/health` pour le monitoring Render.

#### Migrations automatiques
Le script d'entrée exécute automatiquement:
- Migrations de la base de données
- Création du lien storage
- Cache des configurations, routes et vues

#### Seeding (optionnel)
Pour seed la base de données lors du premier déploiement:
```bash
# Dans Render, ajouter la variable d'environnement:
SEED_DATABASE=true
```

## 📦 Déploiement sur Render

### Méthode 1: Avec render.yaml (Recommandé)

1. **Connectez votre repository Git à Render**
   ```bash
   git push origin main
   ```

2. **Render détecte automatiquement `render.yaml`**
   - Crée le service web
   - Crée la base de données PostgreSQL
   - Configure les variables d'environnement

3. **Attendez le build** (environ 5-10 minutes pour le premier déploiement)

4. **Votre API est en ligne** 🎉
   ```
   https://stock-management-backend-j33r.onrender.com
   ```

### Méthode 2: Manuel

1. **Créez une nouvelle Web Service sur Render**
   - Runtime: Docker
   - Repository: Votre repo Git
   - Branch: main

2. **Créez une base de données PostgreSQL**
   - Database Name: `stock-management-db`
   - Region: Même région que le service web

3. **Configurez les variables d'environnement** (voir `.env.production.example`)

4. **Déployez**

## 🔍 Vérification du déploiement

### 1. Health Check
```bash
curl https://stock-management-backend-j33r.onrender.com/api/health
```

Réponse attendue:
```json
{
  "status": "ok",
  "timestamp": "2025-11-11T20:00:00.000000Z",
  "service": "Stock Management API"
}
```

### 2. Test de l'API
```bash
# Test de login
curl -X POST https://stock-management-backend-j33r.onrender.com/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'
```

## 🐛 Dépannage

### Problèmes courants et solutions

#### 1. "No open HTTP ports detected" dans les logs

**Symptôme**:
```
==> No open HTTP ports detected on 0.0.0.0, continuing to scan...
```

**Cause**: L'ENTRYPOINT Docker a été écrasé, empêchant ServersideUp de lancer Nginx.

**Solution**:
- Utiliser le système de hooks de ServersideUp via `/etc/entrypoint.d/`
- Ne PAS écraser l'ENTRYPOINT dans le Dockerfile
- Le script d'entrée doit être copié dans `/etc/entrypoint.d/50-laravel-setup.sh`

```dockerfile
# ✅ Correct
COPY --chmod=755 scripts/docker-entrypoint.sh /etc/entrypoint.d/50-laravel-setup.sh

# ❌ Incorrect - n'écrasez pas l'entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
```

#### 2. "Unsupported cipher or incorrect key length"

**Symptôme**:
```
RuntimeException: Unsupported cipher or incorrect key length
```

**Cause**: `APP_KEY` n'est pas au bon format Laravel.

**Solution**:
```bash
# Générer une clé valide
php artisan key:generate --show

# Ajouter dans render.yaml
- key: APP_KEY
  value: base64:VotreCléGénérée...
```

**Important**: N'utilisez PAS `generateValue: true` dans render.yaml pour APP_KEY.

#### 3. render.yaml invalide - "pserv service type cannot have an IP allow list"

**Symptôme**: Erreur lors de la création du Blueprint.

**Cause**: Base de données déclarée à la fois comme service ET dans la section databases.

**Solution**: Déclarez la base UNIQUEMENT dans la section `databases:`, pas dans `services:`.

```yaml
# ✅ Correct
databases:
  - name: stock-management-db
    plan: free

# ❌ Incorrect - ne pas ajouter dans services
services:
  - type: pserv  # ❌ À supprimer
```

#### 4. MySQL vs PostgreSQL en production

**Question**: Mon application locale utilise MySQL, est-ce compatible avec PostgreSQL en production ?

**Réponse**: ✅ Oui, Laravel Eloquent est database-agnostic.
- Aucune modification de code n'est nécessaire
- Les migrations, requêtes Eloquent et relations fonctionnent identiquement
- Évitez les requêtes SQL brutes spécifiques à MySQL (`DB::raw()` avec syntaxe MySQL)

### Autres erreurs

#### Erreur 502 Bad Gateway
- Vérifiez les logs Render
- Assurez-vous que les migrations sont réussies
- Vérifiez la connexion à la base de données

#### Base de données non accessible
- Vérifiez que la base PostgreSQL est bien créée
- Vérifiez les variables `DB_*` dans les variables d'environnement

#### CORS errors
- Vérifiez `FRONTEND_URL` dans les variables d'environnement
- Vérifiez `config/cors.php`
- Vérifiez `SANCTUM_STATEFUL_DOMAINS`

## 📊 Logs

Pour voir les logs de l'application:
```bash
# Dans le dashboard Render
Logs → Your Service → Logs
```

Ou via CLI:
```bash
render logs -s stock-management-backend
```

## 🔄 Mises à jour

Pour déployer une nouvelle version:
```bash
git add .
git commit -m "Update backend"
git push origin main
```

Render redéploie automatiquement après chaque push sur la branche principale.

## 🔒 Sécurité

- ✅ HTTPS activé automatiquement
- ✅ Variables d'environnement sécurisées
- ✅ CORS configuré pour le frontend
- ✅ Rate limiting sur les routes API
- ✅ Authentification Sanctum

## 📱 URLs de Production

- **API Backend**: `https://stock-management-backend-j33r.onrender.com`
- **Frontend**: `https://stock-management-front-wvmn.onrender.com`
- **Health Check**: `https://stock-management-backend-j33r.onrender.com/api/health`

## 🎨 Configuration du Frontend

Le frontend Angular doit être configuré pour utiliser le backend de production.

### 1. Fichier environment.prod.ts

```typescript
export const environment = {
  production: true,
  apiUrl: 'https://stock-management-backend-j33r.onrender.com/api/v1'
};
```

### 2. Configuration angular.json

Assurez-vous que le build de production utilise le bon fichier d'environnement :

```json
"configurations": {
  "production": {
    "fileReplacements": [
      {
        "replace": "src/environments/environment.ts",
        "with": "src/environments/environment.prod.ts"
      }
    ],
    ...
  }
}
```

### 3. Vérification

Après déploiement du frontend, ouvrez la console du navigateur et vérifiez que les requêtes pointent vers :
```
https://stock-management-backend-j33r.onrender.com/api/v1/...
```

Et NON vers `http://localhost:8000/api/v1/...`

## 🎯 Prochaines étapes

### Déjà configuré ✅

- [x] Backend Laravel déployé sur Render avec Docker
- [x] Base de données PostgreSQL configurée
- [x] Migrations automatiques au démarrage
- [x] Health check endpoint fonctionnel
- [x] CORS configuré pour le frontend
- [x] Frontend Angular déployé
- [x] Authentification Sanctum opérationnelle

### À faire

1. [ ] Configurer les backups automatiques de la base de données
2. [ ] Mettre en place un monitoring avec UptimeRobot
3. [ ] Configurer un domaine personnalisé (optionnel)
4. [ ] Mettre en place un CDN pour les assets (optionnel)
5. [ ] Configurer les logs persistants
6. [ ] Mettre en place un système de notification (email)

---

## 📝 Notes importantes

### Plan gratuit Render
- Les services se mettent en veille après **15 minutes d'inactivité**
- Le premier accès après la mise en veille prend **30-60 secondes** (cold start)
- La base de données PostgreSQL gratuite a une limite de **1 GB** de stockage

### Compatibilité bases de données
- ✅ Laravel Eloquent est compatible MySQL ↔ PostgreSQL sans modification de code
- ✅ Les migrations fonctionnent sur les deux systèmes
- ⚠️ Évitez les requêtes SQL brutes spécifiques à un SGBD

### ServersideUp
- L'image `serversideup/php:8.3-fpm-nginx` gère automatiquement Nginx et PHP-FPM
- Utilisez le système de hooks `/etc/entrypoint.d/` pour les scripts de démarrage
- Ne surchargez jamais l'ENTRYPOINT par défaut

---

**Dernière mise à jour**: 2025-11-11
