# Jeux d'essai — Miamy

> Tableau des cas testés : donnée en entrée → résultat attendu → résultat obtenu.
> Concentrés sur les parties sensibles : authentification, contrôle d'accès, gestion des plats, sécurité.
> Résultat obtenu : ✅ conforme = le comportement observé correspond à l'attendu.

---

## 1. Authentification

| N° | Cas testé | Donnée en entrée | Résultat attendu | Obtenu |
|----|-----------|------------------|------------------|--------|
| 1 | Inscription valide | Champs corrects, email libre, mdp ≥ 8 confirmé | Compte créé (2 tables liées), redirection | ✅ |
| 2 | Email déjà utilisé | Email d'un compte existant | Message « Cet email est déjà utilisé » | ✅ |
| 3 | Mot de passe trop court | mdp = `123` | Message « au moins 8 caractères » | ✅ |
| 4 | Confirmation différente | mdp ≠ confirmation | Message « les mots de passe ne correspondent pas » | ✅ |
| 5 | Code postal invalide (client) | codepostal = `7500` | Message « 5 chiffres » | ✅ |
| 6 | Connexion correcte | Bon email + bon mdp | Connecté, redirigé selon le profil | ✅ |
| 7 | Mauvais mot de passe | Bon email + mauvais mdp | « Identifiants invalides », log `login_fail` | ✅ |
| 8 | Compte désactivé | Compte avec `actif = 0` | Connexion refusée | ✅ |

## 2. Contrôle d'accès (gardes de session)

| N° | Cas testé | Donnée en entrée | Résultat attendu | Obtenu |
|----|-----------|------------------|------------------|--------|
| 9  | Page admin sans être connecté | URL `/dashboard` (non connecté) | Redirection vers `/connexion` | ✅ |
| 10 | Espace restaurateur en tant que client | Profil 3 → `/gestion-carte` | Redirection vers `/connexion` | ✅ |
| 11 | Accès admin en tant que restaurateur | Profil 2 → `/dashboard` (`profil > 1`) | Redirection | ✅ |
| 12 | Déconnexion | Clic sur « Déconnexion » | Session vidée + détruite, retour accueil | ✅ |

## 3. Gestion des plats (CRUD)

| N° | Cas testé | Donnée en entrée | Résultat attendu | Obtenu |
|----|-----------|------------------|------------------|--------|
| 13 | Ajout plat — nom vide | nom = `""` | Message « Le nom est obligatoire », rien inséré | ✅ |
| 14 | Ajout plat — prix invalide | prix = `-5` ou `abc` | Message « Le prix doit être un nombre valide » | ✅ |
| 15 | Ajout plat valide | nom + prix corrects | Plat inséré, redirection `success=added` | ✅ |
| 16 | Modification sans nouvelle image | Champs modifiés, pas de fichier | Image existante conservée | ✅ |
| 17 | Suppression sans confirmation | Page ouverte, pas de POST `confirm_delete` | Aucune suppression | ✅ |
| 18 | Suppression confirmée | POST `confirm_delete` | Plat supprimé, `success=deleted` | ✅ |

## 4. Sécurité

| N° | Cas testé | Donnée en entrée | Résultat attendu | Obtenu |
|----|-----------|------------------|------------------|--------|
| 19 | Injection SQL au login | email = `' OR '1'='1` | Traité comme texte (PDO préparé), connexion refusée | ✅ |
| 20 | Ownership — plat d'un autre | `/modifier-plat?id=` d'un plat non possédé | `getOwnedBy` renvoie null → redirection | ✅ |
| 21 | Toggle AJAX d'un plat non possédé | POST `id_plat` d'un autre restaurateur | JSON `{success:false}` | ✅ |
| 22 | Catégorie hors liste blanche (drag & drop) | categorie = `Pirate` | JSON `{success:false}` (liste blanche) | ✅ |
| 23 | XSS sur un champ affiché | prénom = `<script>alert(1)</script>` | Affiché comme texte (`htmlspecialchars`), pas exécuté | ✅ |

---

## Les 3 cas à mettre en avant à l'oral

Si tu n'as le temps que pour trois exemples, prends ceux du dossier, un par mécanisme :

- **Injection SQL (cas 19)** → les requêtes préparées de PDO neutralisent l'attaque.
- **Ownership (cas 20)** → `getOwnedBy` empêche de toucher aux données d'un autre.
- **Garde de session (cas 9)** → aucune page protégée sans session valide.

**Phrase de synthèse :** « Pour chaque cas, je note l'entrée, le résultat attendu et le résultat obtenu. J'ai couvert le fonctionnel et la sécurité ; trois cas résument mes protections : injection SQL bloquée, contrôle de propriété, et garde de session. »
