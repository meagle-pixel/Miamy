# Guide de mise en ligne — Miamy sur o2switch

Sous-domaine cible : **https://miamy.yaxo7081.odns.fr**
Hébergement : o2switch (compte existant, voie « sous-domaine » comme sherprof)

---

## Étape 1 — Sous-domaine ✅ (fait)

- [x] cPanel → Domaines → Sous-domaines
- [x] Sous-domaine `miamy` sur le domaine `yaxo7081.odns.fr`
- [x] Dossier racine créé : `Miamy.yaxo7081.odns.fr` (contient un `cgi-bin` vide = normal)

## Étape 2 — Base de données MySQL ✅ (fait)

- [x] cPanel → Bases de données → Assistant MySQL
- [x] Base + utilisateur créés, utilisateur associé avec TOUS les privilèges
- [x] Import de `Miamy.sql` via phpMyAdmin
- [ ] Noter les 3 infos (à reporter dans le `.env`) :
  - Nom de la base : `____________`
  - Utilisateur : `____________`
  - Mot de passe : `____________`
  - Hôte : `localhost` (toujours, sur o2switch)

## Étape 3 — Compte FTP

- [ ] cPanel → Fichiers → Comptes FTP
- [ ] Option A : utiliser le compte cPanel principal (accès à tout)
- [ ] Option B : créer un compte dédié `miamy`
  - ⚠️ Champ **Répertoire** = `Miamy.yaxo7081.odns.fr` (PAS la valeur par défaut)
- [ ] NE PAS utiliser le compte `sherprof` (restreint à son dossier)
- [ ] Récupérer hôte / utilisateur / port via « Configurer le client FTP »

## Étape 4 — Connexion FileZilla

- [ ] Fichier → Gestionnaire de sites → Nouveau site
- [ ] Protocole : FTP — Chiffrement : FTP explicite sur TLS
- [ ] Hôte : `xxxxx.o2switch.net` (donné par o2switch) — Port : `21`
- [ ] Authentification : Normale — identifiant + mot de passe FTP
- [ ] Connexion (accepter le certificat TLS au 1er essai)

## Étape 5 — Upload des fichiers

- [ ] À droite (serveur) : ouvrir le dossier `Miamy.yaxo7081.odns.fr`
- [ ] À gauche (PC) : sélectionner TOUT le contenu du dossier Miamy
  (pas le dossier lui-même)
- [ ] Glisser-déposer vers la droite
- [ ] Vérifier : `config.php` et `index.php` posés à côté du `cgi-bin`
- [ ] (Optionnel) ne pas envoyer `.git/` ni les fichiers de travail `.md`

## Étape 6 — Le fichier .env ⚠️ ÉTAPE CRITIQUE

- [ ] Créer un fichier `.env` DANS le dossier `Miamy.yaxo7081.odns.fr`,
      au même niveau que `config.php`
- [ ] Contenu :

```env
# Production (o2switch)
PROD_DB_HOST=localhost
PROD_DB_USER=ton_user_mysql
PROD_DB_PASS=ton_mdp_mysql
PROD_DB_NAME=ta_base
PROD_URL=https://miamy.yaxo7081.odns.fr

# Sécurité (IDENTIQUE au BASE_SALT du local !)
BASE_SALT=la_meme_chaine_que_ton_local
```

- [ ] ⚠️ Ce `.env` est DIFFÉRENT du `.env` Docker (MYSQL_* / PMA_*).
      `config.php` lit uniquement les clés `PROD_*` / `BASE_SALT`.

## Étape 7 — Dossiers d'images

- [ ] Vérifier / créer sur le serveur (exclus du Git) :
  - `assets/img/plats/`
  - `assets/img/restaurants/`
  - `assets/img/users/`

## Étape 8 — Certificat SSL (HTTPS)

- [ ] cPanel → Sécurité → Statut SSL/TLS
- [ ] Cocher `miamy.yaxo7081.odns.fr` → « Exécuter AutoSSL »
- [ ] Patienter quelques minutes → le cadenas passe au vert
- [ ] En attendant, tester en `http://` (sans le « s »)

## Étape 9 — Test final

- [ ] Ouvrir `https://miamy.yaxo7081.odns.fr`
- [ ] La page d'accueil s'affiche
- [ ] Connexion avec un compte existant fonctionne
- [ ] Ajout / modif / suppression d'un plat fonctionne (test du CRUD + images)

---

## En cas de problème

| Symptôme | Cause la plus probable |
|---|---|
| Erreur de connexion à la base | `.env` mal placé (pas à côté de `config.php`) ou valeurs `PROD_*` fausses |
| Page blanche / 500 | `.env` absent → valeurs par défaut vides |
| URL `/connexion` en 404 | `.htaccess` non uploadé (fichier caché — activer l'affichage dans FileZilla) |
| « Connexion non sécurisée » | Certificat SSL pas encore émis → lancer AutoSSL, patienter |
| Impossible de se connecter à un compte | `BASE_SALT` différent de celui du local |
| Pas d'images après upload | Dossiers `assets/img/...` manquants sur le serveur |

> Astuce FileZilla : pour voir les fichiers cachés comme `.env` et `.htaccess`,
> active **Serveur → Forcer l'affichage des fichiers cachés**.
