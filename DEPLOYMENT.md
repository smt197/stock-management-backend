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
| `APP_KEY` | Clé d'application (générée automatiquement) |
| `DB_*` | Connexion PostgreSQL (depuis la base Render) |
| `FRONTEND_URL` | URL du frontend Angular |
| `SANCTUM_STATEFUL_DOMAINS` | Domaines autorisés pour Sanctum |

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
   https://stock-management-backend.onrender.com
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
curl https://stock-management-backend.onrender.com/api/health
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
curl -X POST https://stock-management-backend.onrender.com/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'
```

## 🐛 Dépannage

### Erreur 502 Bad Gateway
- Vérifiez les logs Render
- Assurez-vous que les migrations sont réussies
- Vérifiez la connexion à la base de données

### Base de données non accessible
- Vérifiez que la base PostgreSQL est bien créée
- Vérifiez les variables `DB_*` dans les variables d'environnement

### CORS errors
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

- **API Backend**: `https://stock-management-backend.onrender.com`
- **Frontend**: `https://stock-management-front-wvmn.onrender.com`
- **Health Check**: `https://stock-management-backend.onrender.com/api/health`

## 🎯 Prochaines étapes

1. [ ] Configurer les backups automatiques de la base de données
2. [ ] Mettre en place un monitoring avec UptimeRobot
3. [ ] Configurer un domaine personnalisé (optionnel)
4. [ ] Mettre en place un CDN pour les assets (optionnel)

---

**Note**: Le plan gratuit de Render met en veille les services après 15 minutes d'inactivité. Le premier accès après la mise en veille peut prendre 30-60 secondes.
