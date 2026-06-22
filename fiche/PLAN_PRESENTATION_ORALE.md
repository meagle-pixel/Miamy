# Plan détaillé — Présentation orale Miamy (DWWM)

> Cible : **~65 diapos pour 35 minutes** (~30 s par diapo en moyenne).
> Le plan suit l'ordre de ton dossier. Pour chaque diapo : **[Sur la diapo]** = ce qu'on voit, **[À dire]** = ce que tu racontes.
> Tes 17 diapos actuelles sont réutilisées (marquées « EXISTANTE »). Le reste est à créer.
> Repère de temps indiqué à chaque section.

---

## BLOC 1 — OUVERTURE  (≈ 3 min · diapos 1-5)

**Diapo 1 — Titre** *(EXISTANTE)*
[Sur la diapo] « Miamy » + sous-titre « Plateforme de commande pour restaurants indépendants », Titre professionnel DWWM, ton nom, année 2026.
[À dire] Te présenter en une phrase, annoncer le projet et le cadre (dossier de projet DWWM).

**Diapo 2 — Qui suis-je ?** *(EXISTANTE)*
[Sur la diapo] Ton parcours, ta formation DWWM, le cadre du stage.
[À dire] Qui tu es, pourquoi cette formation, où s'est déroulé le stage.

**Diapo 3 — Sommaire** *(EXISTANTE)*
[Sur la diapo] Les grandes parties : Projet → Base de données → Développement → Finalisation → Compétences → Conclusion.
[À dire] Annoncer le déroulé pour que le jury ait la carte du voyage.

**Diapo 4 — Contexte & projet** *(EXISTANTE)*
[Sur la diapo] Reprise d'un projet existant en stage : analyse du code, modernisation, nouvelles fonctionnalités.
[À dire] Insister sur le point fort : tu as repris et fait évoluer un projet réel, conditions proches d'une vraie mission.

**Diapo 5 — L'équipe** *(EXISTANTE)*
[Sur la diapo] Yann Goguet-Galli (tuteur, concepteur initial, référent technique) + toi (développeur en charge de la reprise).
[À dire] Rôles respectifs ; tu valides tes choix d'archi avec lui chaque jour.

---

## BLOC 2 — LE PROJET  (≈ 5 min · diapos 6-12)

**Diapo 6 — Le problème** *(EXISTANTE)*
[Sur la diapo] Les commissions de 20-30 % prises par Uber Eats / Deliveroo aux petits restaurateurs.
[À dire] Le constat de départ : un petit resto perd une grosse part de sa marge et ne contrôle pas sa clientèle.

**Diapo 7 — La solution Miamy** *(EXISTANTE)*
[Sur la diapo] Espace de gestion pour le resto (carte, horaires, photos) + annuaire commun de proximité pour les clients.
[À dire] Miamy redonne le contrôle au restaurateur et offre une alternative locale aux consommateurs.

**Diapo 8 — Public visé** *(EXISTANTE)*
[Sur la diapo] 2 cibles : restaurateurs/food-trucks indépendants ; consommateurs locaux.
[À dire] Décrire brièvement les besoins de chaque cible.

**Diapo 9 — Cahier des charges : 2 objectifs**
[Sur la diapo] Objectif **métier** (alternative aux grosses plateformes) + objectif **pédagogique** (tout coder à la main, sans framework).
[À dire] Pourquoi sans Laravel/Symfony : pour comprendre en profondeur le cycle d'une requête, le MVC, la sécurité, PDO.

**Diapo 10 — Périmètre fonctionnel : 3 espaces** *(EXISTANTE, à compléter)*
[Sur la diapo] Visiteur (consulter, s'inscrire) · Restaurateur (gérer sa fiche, sa carte, ses horaires) · Admin (modérer comptes & établissements).
[À dire] Chaque espace a ses droits et ses fonctionnalités.

**Diapo 11 — Contraintes techniques** *(EXISTANTE)*
[Sur la diapo] PHP sans framework · PDO/MySQL · architecture MVC · Git.
[À dire] Les choix imposés par l'objectif pédagogique.

**Diapo 12 — Environnement technique**
[Sur la diapo] Docker (même environnement sur 2 machines) · phpMyAdmin · GitHub privé.
[À dire] Docker te permet de retrouver le même environnement entre le portable du stage et ton PC fixe.

---

## BLOC 3 — GESTION DE PROJET  (≈ 3 min · diapos 13-16)

**Diapo 13 — Conception & gestion de projet** *(EXISTANTE)*
[Sur la diapo] Projet personnel du tuteur, sans deadline → avancement fonctionnalité par fonctionnalité, validé au fil de l'eau.
[À dire] Pas de méthode agile formelle, mais des rituels agiles dans la pratique.

**Diapo 14 — Le point quotidien (daily)**
[Sur la diapo] Chaque matin : présentation de la veille à Yann, validation des choix, priorités du jour. Schéma « daily stand-up ».
[À dire] Exemple concret : c'est lui qui a demandé le Drag & Drop, que tu as implémenté avec SortableJS.

**Diapo 15 — Backlog & user stories**
[Sur la diapo] Todolist HTML (todolist.html) classée par espace et priorité + approche « user stories » (se mettre à la place de l'utilisateur).
[À dire] Comment tu décidais quoi développer sans cahier des charges détaillé.

**Diapo 16 — Versioning Git & GitHub** *(EXISTANTE)*
[Sur la diapo] Dépôt privé, branche master, `git push` à chaque fonctionnalité terminée.
[À dire] Sauvegarde, historique, retour arrière possible, trace réutilisée pour rédiger le dossier.

---

## BLOC 4 — MAQUETTAGE  (≈ 2 min · diapos 17-19)

**Diapo 17 — Charte graphique** *(EXISTANTE)*
[Sur la diapo] Logo, palette orange & vert anis, typographies Poppins / Roboto.
[À dire] **Honnêteté du périmètre** : la charte a été définie par le concepteur ; toi tu as réalisé les wireframes et maquettes.

**Diapo 18 — Wireframes desktop** *(EXISTANTE)*
[Sur la diapo] Maquette page d'accueil version desktop.
[À dire] Le wireframe a servi à organiser l'agencement avant d'intégrer.

**Diapo 19 — Wireframes mobile** *(EXISTANTE)*
[Sur la diapo] Maquette page d'accueil / gestion de carte version mobile.
[À dire] La logique responsive pensée dès la maquette.

---

## BLOC 5 — BASE DE DONNÉES  (≈ 4 min · diapos 20-26)

**Diapo 20 — La méthode Merise**
[Sur la diapo] Définition courte + outil Looping. Schéma MCD → MLD → MPD.
[À dire] La conception part du conceptuel et descend progressivement vers le physique (MySQL).

**Diapo 21 — MCD (Modèle Conceptuel)**
[Sur la diapo] Entités (Utilisateur, Restaurant, Plat, Catégorie, Horaire), propriétés, associations + cardinalités.
[À dire] Quelles infos on stocke et comment elles se relient. Exemple de cardinalité : un restaurant propose 1..n plats, un plat appartient à 1 seul restaurant.

**Diapo 22 — Schéma du MCD**
[Sur la diapo] L'image du MCD (Looping).
[À dire] Commenter 2-3 relations clés.

**Diapo 23 — MLD (Modèle Logique)**
[Sur la diapo] Entités → tables, propriétés → colonnes, clés étrangères. Les 3 règles (1:n, 1:1, n:n + table de jointure).
[À dire] Exemple : la table `plat` contient `id_restaurant` ; la relation resto/catégories passe par `restaurant_categories`.

**Diapo 24 — MPD (Modèle Physique)**
[Sur la diapo] Types SQL (INT, VARCHAR, TEXT, DATETIME, ENUM), contraintes (NOT NULL, UNIQUE, PK, AUTO_INCREMENT), cascades ON DELETE/UPDATE.
[À dire] Importance des cascades : sans ON DELETE CASCADE, supprimer un resto laisserait des plats orphelins ; mal placé, ça supprime trop.

**Diapo 25 — Le DDL (langage de définition)**
[Sur la diapo] Exemple de `CREATE TABLE` d'une de tes tables (ex. `plats`).
[À dire] Comment le MPD devient une vraie base via le SQL de définition.

**Diapo 26 — Vue d'ensemble de la base**
[Sur la diapo] Schéma global des tables (utilisateurs, restaurateurs, clients, administrateurs, restaurants, plats, horaires, user_logs…).
[À dire] La structure finale et la spécialisation utilisateurs → 3 sous-types.

---

## BLOC 6 — DÉVELOPPEMENT : ARCHITECTURE  (≈ 4 min · diapos 27-32)

**Diapo 27 — Architecture MVC**
[Sur la diapo] 3 couches : Modèles (classes/, SQL) · Vues (views/, HTML) · Contrôleurs (controllers/, coordination).
[À dire] Le contrôleur fait le lien : il appelle le modèle pour les données et les transmet à la vue. Modèle et vue ne se parlent jamais directement.

**Diapo 28 — Front Controller (index.php)**
[Sur la diapo] Schéma : toutes les requêtes → index.php (point d'entrée unique).
[À dire] Tout passe par un seul accueil qui aiguille. C'est ce qui te fait comprendre le cycle d'une requête HTTP.

**Diapo 29 — La table de routage ($dispatchMap)**
[Sur la diapo] Extrait du tableau : `'gestion-carte' => [new PlatController(), 'gestionCarte']`.
[À dire] Un annuaire page → (contrôleur, méthode). Pour ajouter une page, j'ajoute une ligne.

**Diapo 30 — Réécriture d'URL (.htaccess)**
[Sur la diapo] La règle RewriteRule + exemple `/liste-restaurants` → `index.php?mod=liste-restaurants`.
[À dire] URLs propres, invisibles pour l'utilisateur, meilleures pour le SEO.

**Diapo 31 — Fichier d'amorçage (functions.php)**
[Sur la diapo] 4 étapes : session_start, fuseau horaire, config.php, require des classes.
[À dire] Tout est initialisé une fois ; `require` charge les classes (≠ `new` qui crée les objets).

**Diapo 32 — Le système de layouts**
[Sur la diapo] Schéma : layout public (head/header/vue/footer/foot) vs layout admin (SB Admin).
[À dire] En-tête et pied de page écrits une seule fois ; toutes les pages en héritent.

---

## BLOC 7 — DÉVELOPPEMENT : FRONT-END  (≈ 3 min · diapos 33-37)

**Diapo 33 — Le front-end**
[Sur la diapo] Template Foodingly (public) + SB Admin (back-office). Choix imposé par le tuteur.
[À dire] Ton travail : intégrer ces templates dans la logique PHP, pas les dessiner.

**Diapo 34 — CSS & responsive**
[Sur la diapo] 3 couches : Bootstrap (grille) + feuilles du template + responsive.css (media queries) + menu burger meanmenu.
[À dire] Le socle responsive venait du template.

**Diapo 35 — Une surcharge propre**
[Sur la diapo] responsive-fixes.css appelé en dernier, exemples de bugs corrigés (titre masqué, liens en double).
[À dire] Tu corriges sans modifier le thème (cascade CSS), pour garder les mises à jour possibles.

**Diapo 36 — Les partials (head, header, footer, foot)**
[Sur la diapo] Rôle de chaque partial ; balise `<base>` ; titre de page dynamique.
[À dire] Éléments communs factorisés ; foot.php charge le JS en fin de page pour ne pas bloquer l'affichage.

**Diapo 37 — Header dynamique + protection XSS**
[Sur la diapo] Topbar qui change selon le rôle (admin/restaurateur/client) + `htmlspecialchars` sur les données affichées.
[À dire] L'échappement XSS convertit les caractères dangereux pour empêcher l'exécution de scripts.

---

## BLOC 8 — DÉVELOPPEMENT : FONCTIONNALITÉS JS  (≈ 3 min · diapos 38-41)

**Diapo 38 — Fonctionnalité 1 : page horaires**
[Sur la diapo] 7 jours, case « Ouvert » + 2 champs heure ; JS qui désactive les champs si la case est décochée.
[À dire] Côté serveur, `saveHoraires()` vérifie auth + propriété du restaurant avant d'enregistrer.

**Diapo 39 — Fonctionnalité 2 : Drag & Drop (principe)**
[Sur la diapo] Plats regroupés par catégorie ; déplacer un plat entre catégories ; librairie SortableJS.
[À dire] **Honnêteté du périmètre** : toi = le drag & drop + l'enregistrement ; Yann = les compteurs UX dynamiques.

**Diapo 40 — Drag & Drop : le gestionnaire onEnd + AJAX**
[Sur la diapo] Schéma : onEnd récupère id + catégorie origine/destination → requête fetch (async/await) vers update-plat-categorie.
[À dire] Persistance immédiate sans rechargement de page.

**Diapo 41 — Drag & Drop : persistance & sécurité serveur**
[Sur la diapo] `updateCategorie()` : 3 contrôles (connecté + rôle, liste blanche des catégories, propriété du plat) → réponse JSON.
[À dire] Les données du navigateur ne sont jamais considérées comme fiables : tout est revérifié côté serveur.

---

## BLOC 9 — DÉVELOPPEMENT : SÉCURITÉ BASE DE DONNÉES  (≈ 3 min · diapos 42-44)

**Diapo 42 — PDO + requêtes préparées**
[Sur la diapo] Exemple `prepare(... :email ...)` puis `execute([...])`. Schéma « commande + valeur séparées ».
[À dire] La valeur est traitée comme du texte, jamais comme du code SQL → injections impossibles.

**Diapo 43 — Le pattern Singleton**
[Sur la diapo] `getInstance()`, propriété statique, constructeur privé, `__clone` privé.
[À dire] Une seule connexion partagée par toute la page, pour économiser les ressources serveur.

**Diapo 44 — Options PDO importantes**
[Sur la diapo] ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES = false.
[À dire] Erreurs en exceptions, résultats en tableaux lisibles, vraies requêtes préparées MySQL.

---

## BLOC 10 — DÉVELOPPEMENT : AUTHENTIFICATION  (≈ 4 min · diapos 45-50)

**Diapo 45 — Authentification : vue d'ensemble**
[Sur la diapo] 3 profils (admin=1, restaurateur=2, client=3), chacun son espace ; sessions PHP.
[À dire] L'authentification est le socle de toute la partie privée.

**Diapo 46 — Hachage du mot de passe**
[Sur la diapo] `password_hash` + bcrypt ; sel automatique ; pepper (BASE_SALT) hors base (.env).
[À dire] Jamais de mot de passe en clair ; même si la base fuit, le pepper rend les hash inexploitables. Image : purée de fruit irréversible.

**Diapo 47 — Le contrôleur d'inscription**
[Sur la diapo] `register()` / `registerClient()` : validation des champs → création compte → fiche métier liée.
[À dire] Validation avant toute écriture ; cohérence entre table `utilisateurs` et table métier.

**Diapo 48 — La vérification au login (tryToConnect)**
[Sur la diapo] 5 étapes : chercher compte actif → vérifier mot de passe → ouvrir session → charger fiche métier.
[À dire] `password_verify` refait le même hachage et compare ; on ne déchiffre jamais. Image : cachet de cire.

**Diapo 49 — La protection des pages privées (gardes)**
[Sur la diapo] Garde en début de méthode : connecté ? bon profil ? sinon redirection + exit().
[À dire] Aucune action sensible sans être connecté ; sur l'admin, seuls les profils ≤ 1.

**Diapo 50 — La déconnexion**
[Sur la diapo] `logout()` : journalisation → vider $_SESSION → session_destroy → redirection.
[À dire] Aucune trace de session ne subsiste côté serveur.

---

## BLOC 11 — DÉVELOPPEMENT : CRUD DES PLATS  (≈ 3 min · diapos 51-53)

**Diapo 51 — CRUD des plats : principe**
[Sur la diapo] Créer / Lire / Modifier / Supprimer un plat. 2 niveaux de sécurité : garde + contrôle de propriété.
[À dire] Partout le même schéma : le contrôleur valide et contrôle les droits, le modèle exécute une requête préparée.

**Diapo 52 — Les 4 opérations**
[Sur la diapo] ajouter() / liste() & gestionCarte() / modifier() / supprimer(). Suppression seulement en POST + confirmation.
[À dire] Un simple clic (GET) ne supprime jamais : protection contre les suppressions accidentelles.

**Diapo 53 — Le contrôle de propriété (getOwnedBy)**
[Sur la diapo] Vérifier que le plat/resto appartient bien au restaurateur connecté.
[À dire] Empêche un restaurateur de modifier les plats d'un autre en changeant l'id dans l'URL.

---

## BLOC 12 — FINALISATION  (≈ 4 min · diapos 54-59)

**Diapo 54 — Déploiement sur o2switch**
[Sur la diapo] Sous-domaine, cPanel, base MySQL, import du .sql via phpMyAdmin, transfert FTP (FileZilla).
[À dire] Les étapes de mise en ligne, de la création du sous-domaine au site accessible.

**Diapo 55 — Config & variables d'environnement**
[Sur la diapo] config.php détecte local/prod ; secrets dans .env (non versionné, .gitignore) ; .env.example.
[À dire] Même code en local et en prod, sans modification : config.php choisit les bons identifiants.

**Diapo 56 — Audit Lighthouse**
[Sur la diapo] 4 axes (performance, accessibilité, bonnes pratiques, SEO) ; résultats desktop/mobile.
[À dire] Points relevés : image de bannière trop lourde, cache et compression à améliorer.

**Diapo 57 — Validation W3C**
[Sur la diapo] Validateur HTML du W3C ; erreurs de hiérarchie de titres corrigées.
[À dire] Code conforme = affichage homogène et meilleure accessibilité.

**Diapo 58 — Accessibilité**
[Sur la diapo] lang="fr", balises sémantiques, hiérarchie des titres, outil WAVE, alt/aria-label, contrastes.
[À dire] Rendre le site utilisable par le plus grand nombre, y compris au lecteur d'écran.

**Diapo 59 — Jeux d'essai & tests de sécurité**
[Sur la diapo] Tableau : 3 cas (injection SQL neutralisée, contrôle de propriété, garde de session) avec résultat attendu/obtenu.
[À dire] Tu as testé les parties sensibles ; tes protections fonctionnent.

---

## BLOC 13 — DÉMONSTRATION  (≈ 5 min · diapo 60) — *optionnel*

**Diapo 60 — Démonstration du site**
[Sur la diapo] Soit bascule vers le site en ligne (démo live), soit captures d'écran commentées.
[À dire] Montrer : page d'accueil → annuaire → connexion restaurateur → gestion de la carte → drag & drop en direct. *(Si pas de démo live, supprime cette diapo et illustre par des captures dans les blocs concernés.)*

---

## BLOC 14 — CLÔTURE  (≈ 3 min · diapos 61-65)

**Diapo 61 — Développement des compétences**
[Sur la diapo] Veille (doc officielle PHP/MySQL/Bootstrap/SortableJS, MDN), montée en compétence front/back/BDD/méthodo.
[À dire] Ce que le projet t'a appris et comment tu as appris en autonomie.

**Diapo 62 — Difficultés rencontrées**
[Sur la diapo] Mise en place rigoureuse du MVC ; combiner SortableJS + AJAX + PHP/PDO en sécurité.
[À dire] Comment tu les as surmontées.

**Diapo 63 — Axes d'évolution**
[Sur la diapo] Tokens CSRF, paiement en ligne, notifications e-mail.
[À dire] Tu sais ce qu'il manque et ce que tu ferais ensuite (montre du recul).

**Diapo 64 — Conclusion / bilan**
[Sur la diapo] Bilan technique (front→back sans framework) + méthodo (Git, échanges tuteur) + l'intérêt des conditions réelles.
[À dire] Ce que tu retiens de plus précieux pour tes futures missions.

**Diapo 65 — Merci + questions** *(EXISTANTE, à déplacer en fin)*
[Sur la diapo] « Merci de votre attention » + tes coordonnées / lien du site.
[À dire] Remercier (dont Yann) et inviter aux questions.

---

## Récapitulatif du minutage (≈ 35 min)

| Bloc | Diapos | Temps |
|------|--------|-------|
| 1. Ouverture | 1-5 | 3 min |
| 2. Le projet | 6-12 | 5 min |
| 3. Gestion de projet | 13-16 | 3 min |
| 4. Maquettage | 17-19 | 2 min |
| 5. Base de données | 20-26 | 4 min |
| 6. Archi (MVC/Front Controller) | 27-32 | 4 min |
| 7. Front-end | 33-37 | 3 min |
| 8. Fonctionnalités JS | 38-41 | 3 min |
| 9. Sécurité BDD | 42-44 | 3 min |
| 10. Authentification | 45-50 | 4 min |
| 11. CRUD | 51-53 | 3 min |
| 12. Finalisation | 54-59 | 4 min |
| 13. Démo (optionnel) | 60 | 5 min |
| 14. Clôture | 61-65 | 3 min |

> Sans la démo : ~30 min de diapos, ce qui te laisse de la marge pour respirer et pour les questions. Avec la démo : ~35 min pile.

## Conseils de rythme

- **~30 s par diapo** en moyenne : ne lis pas tes diapos, illustre-les. Les diapos de code, tu les commentes 2-3 phrases max.
- **Une idée par diapo.** Si une diapo a 3 idées, coupe-la en 3.
- **Les diapos de code** : ne mets que l'extrait essentiel (5-10 lignes), pas un fichier entier.
- **Entraîne-toi à voix haute** une fois en entier pour caler le temps — c'est le seul moyen fiable de savoir si tu tiens dans 35 min.
- Prépare tes **phrases de synthèse** (celles des fiches) pour les diapos clés : routage, MVC, PDO/Singleton, requêtes préparées, hachage, tryToConnect.
