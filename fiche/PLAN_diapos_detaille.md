# Plan détaillé diapo par diapo — Oral Miamy (DWWM)

> Pour chaque diapo : **À l'écran** (ce qui s'affiche) · **Capture** (le visuel à insérer) · **Script** (ce que tu dis, ~20-40 s).
> Tout est ancré sur ton code réel et ton dossier projet. Les extraits de code sont fidèles aux fichiers du projet.
> Rythme cible : ~35 min, démo comprise. Reste à ~25-30 s sur les diapos « visuel + 1 idée », prends ton temps sur la BDD, le back-end/sécurité et la démo.

---

## Ouverture

### Diapo 1 — Page de titre
- **À l'écran :** « Miamy — la carte interactive de votre restaurant » · Maxime Paulin · Titre professionnel DWWM · date.
- **Capture :** logo Miamy + photo d'un plat (entrecôte ou tarte tatin de `assets/img/plats/`).
- **Script :** « Bonjour, je m'appelle Maxime Paulin. Je vais vous présenter Miamy, une application web qui permet à des restaurants de présenter leur carte en ligne et de prendre des commandes en direct. C'est le projet que j'ai développé dans le cadre du titre Développeur Web et Web Mobile. »

### Diapo 2 — Qui suis-je ?
- **À l'écran :** Présentation · Reconversion professionnelle · Mes objectifs · Le projet.
- **Capture :** photo ou icône neutre.
- **Script :** « En quelques mots sur mon parcours : je suis en reconversion professionnelle vers le développement web. La formation DWWM m'a permis d'acquérir les compétences front-end et back-end, et ce projet est l'aboutissement où je les mets toutes en œuvre, de la conception au déploiement. »

### Diapo 3 — Sommaire
- **À l'écran :** les 9 parties : Contexte, Conception, Base de données, Architecture, Front-end, Back-end & sécurité, Tests & qualité, Déploiement, Démonstration.
- **Capture :** aucune (liste claire).
- **Script :** « Je vais suivre le cycle complet du projet : d'abord le contexte et la conception, puis la base de données, l'architecture du code, le front-end, le back-end et la sécurité, les tests, le déploiement, et je terminerai par une démonstration en direct. »

---

## 1. Contexte & projet

### Diapo 4 — Le contexte
- **À l'écran :** Reprise d'un projet existant · Stage · Encadré par Yann Goguet-Galli.
- **Capture :** logo entreprise/tuteur ou photo poste de travail.
- **Script :** « Ce projet s'est déroulé en stage. J'ai repris une base de projet existante, que j'ai fait évoluer et largement complétée, encadré par mon tuteur Yann Goguet-Galli avec qui je faisais un point quotidien. »

### Diapo 5 — Le problème
- **À l'écran :** logos Uber Eats / Deliveroo + mention « 20 à 30 % de commission ».
- **Capture :** visuel des plateformes de livraison.
- **Script :** « Le constat de départ : les grandes plateformes de livraison prélèvent une commission importante, souvent 20 à 30 % par commande. Pour un petit restaurant indépendant, ça ampute fortement la marge. Beaucoup aimeraient vendre en ligne sans dépendre de ces intermédiaires. »

### Diapo 6 — La solution Miamy
- **À l'écran :** « Vitrine numérique » que le restaurateur possède et contrôle.
- **Capture :** page d'accueil de Miamy en ligne.
- **Script :** « Miamy répond à ce besoin : c'est une vitrine numérique que le restaurateur possède et contrôle entièrement. Il gère sa fiche, sa carte, ses photos, ses horaires, et le client commande directement, sans intermédiaire qui prend une commission. »

### Diapo 7 — Cahier des charges
- **À l'écran :** deux colonnes : Objectif métier / Objectif pédagogique.
- **Capture :** schéma deux colonnes.
- **Script :** « Le cahier des charges avait un double objectif. Côté métier : permettre à un restaurant de vendre sans intermédiaire. Côté pédagogique : tout développer à la main, sans framework, pour vraiment comprendre les mécanismes internes — le routage, la sécurité, le cycle d'une requête. »

### Diapo 8 — Périmètre fonctionnel
- **À l'écran :** 3 espaces — Visiteur / Restaurateur / Admin, avec leurs droits.
  - Visiteur : voir les restaurants, fiche restaurant, créer un compte client, se connecter.
  - Restaurateur : gérer sa fiche, gérer sa carte, photos de plats, réorganiser par glisser-déposer.
  - Admin : gérer les comptes utilisateurs, gérer les établissements, superviser la BDD.
- **Capture :** schéma des 3 espaces.
- **Script :** « L'application est structurée autour de trois espaces avec des droits distincts. Le visiteur consulte les restaurants et peut créer un compte. Le restaurateur gère sa fiche, sa carte et ses plats, avec une réorganisation par glisser-déposer. L'administrateur supervise les comptes, les établissements et la base. Ces trois rôles correspondent aux trois profils que je gère dans le code. »

### Diapo 9 — Contraintes techniques
- **À l'écran :** logos PHP, MySQL, Docker, Git/GitHub, Apache.
- **Capture :** image « stack technique ».
- **Script :** « Côté technique, les contraintes étaient : du PHP pur orienté objet sans framework, une base MySQL, un environnement de développement Docker pour reproduire la production, le serveur Apache avec réécriture d'URL, et un versioning Git poussé sur GitHub. »

---

## 2. Conception & gestion de projet

### Diapo 10 — Gestion de projet
- **À l'écran :** Point quotidien · Todolist · approche par user stories.
- **Capture :** capture de `todolist.html`.
- **Script :** « Pour organiser le travail, je partais des besoins utilisateur formulés en user stories, je les découpais en tâches dans une todolist, et je faisais un point quotidien avec mon tuteur pour valider les priorités et débloquer les difficultés. »

### Diapo 11 — Versioning Git / GitHub
- **À l'écran :** Commits réguliers · Sauvegarde distante · Historique du projet.
- **Capture :** capture du dépôt GitHub (historique des commits).
- **Script :** « J'ai utilisé Git tout au long du projet : des commits réguliers et explicites, poussés sur GitHub. Ça me donne une sauvegarde distante, un historique complet, et la possibilité de revenir en arrière si une modification casse quelque chose. »

### Diapo 12 — Public visé
- **À l'écran :** deux personas — le restaurateur indépendant / le client de proximité.
- **Capture :** deux personas.
- **Script :** « Deux publics : d'un côté les restaurateurs indépendants qui veulent une présence en ligne sans commission ; de l'autre les clients de proximité qui veulent commander simplement auprès de leurs restaurants locaux. »

### Diapo 13 — Charte graphique
- **À l'écran :** nuancier orange/vert + logo + typographie.
- **Capture :** la charte (couleurs, logo, polices).
- **Script :** « Avant de coder, j'ai défini l'identité visuelle : une palette orange et vert, le logo Miamy, et les typographies. Ça garantit une cohérence sur tout le site et ça guide ensuite l'intégration CSS. »

### Diapo 14 — Wireframes (accueil)
- **À l'écran :** wireframe de la page d'accueil.
- **Capture :** wireframe desktop accueil.
- **Script :** « J'ai maquetté les écrans clés avant de développer. Voici le wireframe de la page d'accueil : il fixe la structure — bannière, catégories, restaurants mis en avant — avant d'écrire la moindre ligne de code. »

### Diapo 15 — Wireframes (gestion de carte)
- **À l'écran :** wireframe de la page gestion de la carte (restaurateur).
- **Capture :** wireframe gestion-carte.
- **Script :** « Et le wireframe de l'espace restaurateur, la gestion de la carte : les plats regroupés par catégorie, avec les actions sur chaque plat. C'est l'écran le plus riche, et celui où j'ai implémenté le glisser-déposer que je montrerai plus loin. »

### Diapo 16 — Enchaînement des écrans
- **À l'écran :** schéma de navigation entre les écrans (CP2).
- **Capture :** schéma d'enchaînement (flèches entre maquettes).
- **Script :** « Ce schéma montre comment l'utilisateur navigue d'un écran à l'autre : de l'accueil vers la liste des restaurants, vers une fiche, vers la connexion, puis selon le profil vers son espace. Ça m'a servi de fil conducteur pour le routage. »

---

## 3. Base de données (Merise)

### Diapo 17 — La méthode Merise
- **À l'écran :** les 3 niveaux MCD → MLD → MPD · outil Looping.
- **Capture :** schéma des 3 niveaux.
- **Script :** « Pour la base de données, j'ai suivi la méthode Merise, qui va du conceptuel au physique en trois étapes : le MCD décrit les entités et leurs relations, le MLD les traduit en tables, et le MPD ajoute les types et contraintes SQL. J'ai modélisé avec l'outil Looping. »

### Diapo 18 — MCD
- **À l'écran :** le Modèle Conceptuel de Données.
- **Capture :** ton MCD (Looping).
- **Script :** « Voici le MCD. On y retrouve les entités métier : utilisateurs, restaurateurs, clients, administrateurs, restaurants, plats, catégories, horaires. Les cardinalités traduisent les règles de gestion : par exemple, un restaurateur peut posséder plusieurs restaurants, et un restaurant propose plusieurs plats. »

### Diapo 19 — MLD
- **À l'écran :** le Modèle Logique de Données (tables + clés étrangères).
- **Capture :** ton MLD.
- **Script :** « Le MLD traduit ces entités en tables et fait apparaître les clés étrangères. Par exemple, la table plats porte une clé étrangère id_restaurant, et la liaison restaurant–catégorie passe par une table d'association, restaurant_categories, pour gérer le plusieurs-à-plusieurs. »

### Diapo 20 — MPD
- **À l'écran :** le Modèle Physique : types SQL, contraintes, ON DELETE CASCADE.
- **Capture :** ton MPD.
- **Script :** « Le MPD précise les types SQL et les contraintes. Point important : les clés étrangères sont en ON DELETE CASCADE. Concrètement, si je supprime un restaurant, ses plats et ses horaires sont supprimés automatiquement par MySQL — ça garantit que la base reste cohérente, sans lignes orphelines. »

### Diapo 21 — Le DDL
- **À l'écran :** un `CREATE TABLE` réel (ex. `horaires` ou `utilisateurs`).
- **Capture :** extrait de `Miamy.sql`.
- **Script :** « Enfin, le DDL, c'est la traduction du modèle en SQL exécutable. Sur la table horaires par exemple, on voit la contrainte d'unicité sur le couple (id_restaurant, jour) : elle empêche d'avoir deux fois le même jour pour un restaurant, et c'est elle qui rend possible mon enregistrement en "INSERT ... ON DUPLICATE KEY UPDATE". »
- **Code à montrer :**
  ```sql
  CREATE TABLE `horaires` (
    `jour` tinyint(1) NOT NULL COMMENT '0=Lundi, 6=Dimanche',
    `ouvert` tinyint(1) NOT NULL DEFAULT 1,
    `debut` time DEFAULT NULL,
    `fin` time DEFAULT NULL,
    UNIQUE KEY `uq_resto_jour` (`id_restaurant`, `jour`)
  );
  ```

### Diapo 22 — Le modèle relationnel utilisateurs
- **À l'écran :** schéma `utilisateurs` ↔ `administrateurs` / `restaurateurs` / `clients` (relation 1:1).
- **Capture :** schéma de la séparation compte / fiche métier.
- **Script :** « J'ai fait un choix de conception sur les utilisateurs : une table utilisateurs commune qui porte l'email, le mot de passe et le profil ; et une table métier séparée selon le rôle — administrateurs, restaurateurs ou clients — pour les informations spécifiques. Le lien se fait par un champ profil (1, 2 ou 3) et un profil_id qui pointe vers la fiche métier. Ça évite d'avoir une table utilisateurs avec plein de colonnes vides selon le rôle. »

---

## 4. Architecture

### Diapo 23 — Le choix sans framework
- **À l'écran :** « à la main » vs Laravel/Symfony.
- **Capture :** schéma comparatif.
- **Script :** « J'ai développé sans framework, volontairement. Un Laravel ou un Symfony aurait masqué le routage et la sécurité derrière de la magie. En le faisant à la main, j'ai vraiment compris le cycle d'une requête — et c'est cette compréhension qu'on attend d'un développeur. L'architecture que j'ai construite reprend d'ailleurs les mêmes principes que ces frameworks. »

### Diapo 24 — Le pattern MVC
- **À l'écran :** Modèle (`classes/`) · Vue (`views/`) · Contrôleur (`controllers/`).
- **Capture :** schéma MVC + arborescence des dossiers.
- **Script :** « Le code suit le pattern MVC, avec un dossier par responsabilité. Les modèles, dans classes/, sont les seuls à contenir du SQL. Les vues, dans views/, ne font que de l'affichage HTML. Les contrôleurs, dans controllers/, font le lien : ils reçoivent la requête, appellent les modèles, et préparent les données pour la vue. Si je dois changer une requête, je sais que c'est dans un modèle ; un affichage, dans une vue. »

### Diapo 25 — Le Front Controller
- **À l'écran :** toutes les requêtes → `index.php` (point d'entrée unique).
- **Capture :** haut de `index.php`.
- **Script :** « Toutes les requêtes passent par un seul fichier, index.php : c'est le pattern Front Controller. Plutôt qu'un fichier PHP par page, j'ai un point d'entrée unique qui centralise tout ce qui est commun — le démarrage de session, le chargement de la config et des classes, le choix du layout — écrit une seule fois. »

### Diapo 26 — La table de routage
- **À l'écran :** le tableau `$dispatchMap` : URL → [contrôleur, méthode].
- **Capture :** capture du `$dispatchMap` dans `index.php`.
- **Script :** « Le cœur du routeur, c'est ce tableau associatif, le dispatchMap. Chaque nom de page est associé à une paire : le contrôleur et la méthode à appeler. Je récupère la page demandée, je déballe la paire dans deux variables, et j'appelle dynamiquement la méthode. L'avantage : pour ajouter une page, j'ajoute une seule ligne, au lieu d'une longue suite de if/else. »
- **Code à montrer :**
  ```php
  $dispatchMap = [
      'accueil'           => [new HomeController(),       'index'],
      'connexion'         => [new AuthController(),       'login'],
      'gestion-carte'     => [new PlatController(),       'gestionCarte'],
      // ... une ligne par page
  ];
  $page = isset($_GET['mod']) ? $_GET['mod'] : 'accueil';
  if (isset($dispatchMap[$page])) {
      [$controller, $method] = $dispatchMap[$page];
      $viewData = $controller->$method();
      if (is_array($viewData)) { extract($viewData); }
  }
  ```

### Diapo 27 — Réécriture d'URL (.htaccess)
- **À l'écran :** `/connexion` au lieu de `index.php?mod=connexion`.
- **Capture :** extrait du `.htaccess` + URL propre dans la barre d'adresse.
- **Script :** « Pour avoir des URLs propres, j'utilise un .htaccess. La règle dit : si l'adresse demandée n'est pas un vrai fichier ni un vrai dossier, redirige tout vers index.php en mettant le nom de la page dans le paramètre mod. Donc /connexion devient en interne index.php?mod=connexion, sans que l'utilisateur le voie. C'est plus lisible et meilleur pour le référencement. »
- **Code à montrer :**
  ```apache
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^([a-zA-Z0-9\-_]+)/?$ index.php?mod=$1 [L,QSA]
  ```

### Diapo 28 — Bootstrap & layouts
- **À l'écran :** `functions.php` (session, config, classes) + le double layout public/admin.
- **Capture :** capture de `functions.php` et du bloc de layout d'`index.php`.
- **Script :** « Avant tout routage, un fichier d'amorçage, functions.php, prépare le terrain : il démarre la session, fixe le fuseau horaire de Paris, charge la config et toutes les classes. Ensuite, une fois la vue déterminée, index.php l'enveloppe dans le bon layout : si le fichier est dans views/admin/, c'est le gabarit administration ; sinon, le gabarit public. L'en-tête et le pied de page ne sont donc écrits qu'une seule fois. »
- **Code à montrer :**
  ```php
  $is_admin_page = strpos($page_url, 'views/admin/') === 0;
  if ($is_admin_page) {
      include('views/partials/admin_head.php');
      include($page_url);
      include('views/partials/admin_foot.php');
  } else {
      include('views/partials/head.php');
      include('views/partials/header.php');
      include($page_url);
      include('views/partials/footer.php');
      include('views/partials/foot.php');
  }
  ```

---

## 5. Front-end

### Diapo 29 — Intégration des templates
- **À l'écran :** Foodingly (public) + SB Admin (back-office).
- **Capture :** aperçu des deux thèmes.
- **Script :** « Pour le front, je suis parti de deux thèmes existants : Foodingly pour le site public, et SB Admin pour le back-office. Mon travail n'a pas été de les recréer, mais de les intégrer dans ma logique PHP : les découper en partials, les rendre dynamiques avec les données de la base, et les adapter à mes besoins. »

### Diapo 30 — Le head & APP_URL
- **À l'écran :** `<base href>` dynamique alimenté par `APP_URL`.
- **Capture :** capture de `head.php`.
- **Script :** « Un point clé du head, c'est la balise base href, alimentée par la constante APP_URL. Elle définit la racine de tous mes liens relatifs. Comme APP_URL est calculée automatiquement selon l'environnement, le même code marche en local et en production sans rien changer : j'écris simplement href="connexion" partout, et le lien est toujours résolu correctement. »
- **Code à montrer :**
  ```php
  <base href="<?= APP_URL ?>/">
  ```

### Diapo 31 — Navbar responsive
- **À l'écran :** menu desktop → menu burger sous 1200px (plugin meanmenu).
- **Capture :** capture du menu burger mobile.
- **Script :** « La navbar fonctionne en desktop et en mobile à partir d'une seule source HTML. En dessous de 1200 pixels, le plugin meanmenu transforme le menu horizontal en menu burger. J'utilise aussi des classes Bootstrap comme d-xl-none pour afficher certains liens uniquement en mobile, là où la barre du haut ne les montre plus. »

### Diapo 32 — Le responsive
- **À l'écran :** même page en desktop / tablette / mobile.
- **Capture :** 3 captures côte à côte.
- **Script :** « Le responsive repose sur la grille Bootstrap et des media queries. Quand le thème ne suffisait pas, je n'ai pas modifié ses fichiers : j'ai créé mon propre fichier responsive-fixes.css, chargé en dernier, qui surcharge proprement les règles. C'est la bonne pratique : on garde le thème d'origine intact et on applique ses correctifs par-dessus. »

### Diapo 33 — Footer & scripts
- **À l'écran :** scripts JS chargés en fin de `<body>` + hook `$custom_js`.
- **Capture :** capture de `foot.php`.
- **Script :** « Les scripts JavaScript sont chargés tout en bas du body, et pas dans le head. La raison : les scripts bloquent l'affichage. En les mettant à la fin, la page s'affiche d'abord, puis le JS se charge. J'ai aussi prévu une trappe d'extension, la variable custom_js, qui permet à une page précise d'injecter un script — par exemple la librairie de glisser-déposer uniquement sur la gestion de carte, sans l'imposer à tout le site. »

### Diapo 34 — Fonctionnalité : horaires
- **À l'écran :** tableau des 7 jours, case « Ouvert » qui grise les champs heure.
- **Capture :** capture de la page horaires de `details.php`.
- **Script :** « Première fonctionnalité front : la saisie des horaires. Quand le restaurateur décoche "Ouvert" pour un jour, les deux champs heure de cette ligne se grisent automatiquement. C'est du JavaScript vanilla, sans librairie : à chaque case je remonte à la ligne avec closest, je récupère ses deux inputs heure, et je synchronise leur état désactivé avec l'inverse de la case. Aucun appel serveur — c'est juste de l'aide à la saisie ; l'enregistrement se fait au clic sur Enregistrer. »
- **Code à montrer :**
  ```js
  document.querySelectorAll('.toggle-ouvert').forEach(function (checkbox) {
      checkbox.addEventListener('change', function () {
          const row    = this.closest('.horaire-row');
          const inputs = row.querySelectorAll('.heures-input');
          inputs.forEach(input => input.disabled = !checkbox.checked);
      });
  });
  ```

### Diapo 35 — Fonctionnalité : Drag & Drop
- **À l'écran :** un plat en cours de déplacement entre deux catégories (SortableJS).
- **Capture :** capture de la gestion de carte avec un plat « fantôme » en déplacement.
- **Script :** « La fonctionnalité dont je suis le plus fier : réorganiser sa carte au glisser-déposer. J'ai utilisé SortableJS, une librairie spécialisée, plutôt que de coder à la main la gestion souris et tactile — ç'aurait été des centaines de lignes et un nid à bugs. SortableJS gère toute la partie visuelle ; moi, je gère la persistance en base après chaque déplacement. »

### Diapo 36 — Drag & Drop côté serveur
- **À l'écran :** le gestionnaire `onEnd` (fetch + async/await) → endpoint PHP.
- **Capture :** capture du `onEnd` et de `PlatController::updateCategorie`.
- **Script :** « Quand l'utilisateur lâche un plat, le callback onEnd se déclenche. Je récupère l'id du plat et la catégorie de destination, et j'envoie une requête AJAX avec fetch vers mon endpoint update-plat-categorie. Côté serveur, le contrôleur fait trois vérifications avant d'écrire : l'utilisateur est-il un restaurateur connecté, la catégorie est-elle dans ma liste blanche, et le plat appartient-il bien à ce restaurateur. Puis il renvoie un JSON, sans rechargement de la page. »
- **Code à montrer :**
  ```js
  onEnd: async function (evt) {
      const platId   = evt.item.dataset.platId;
      const newCateg = evt.to.dataset.categorie;
      if (newCateg === evt.from.dataset.categorie) return;
      const fd = new FormData();
      fd.append('id_plat', platId);
      fd.append('categorie', newCateg);
      const r = await fetch(BASE_URL + '/update-plat-categorie', { method:'POST', body:fd });
      const resp = await r.json();
      if (!resp.success) alert('Erreur lors du changement de catégorie.');
  }
  ```

### Diapo 37 — Protection XSS
- **À l'écran :** `htmlspecialchars(...)` autour des données affichées.
- **Capture :** une ligne `htmlspecialchars` dans `header.php` (le prénom).
- **Script :** « Dès qu'une donnée saisie par un utilisateur est affichée, je l'échappe avec htmlspecialchars. Par exemple le prénom dans la barre du haut : si quelqu'un enregistrait un prénom contenant une balise script, sans échappement elle s'exécuterait à l'affichage — c'est une attaque XSS. Avec htmlspecialchars, les caractères dangereux sont convertis en texte et la balise s'affiche au lieu de s'exécuter. »
- **Code à montrer :**
  ```php
  <?= htmlspecialchars($_SESSION['user-info']['prenom'] ?? 'Mon compte') ?>
  ```

---

## 6. Back-end & sécurité

### Diapo 38 — PDO & requêtes préparées
- **À l'écran :** `prepare()` + `execute()` : la valeur traitée comme donnée, pas comme du SQL.
- **Capture :** une requête préparée d'un modèle (ex. `isRegistered`).
- **Script :** « Toutes mes requêtes avec une donnée utilisateur sont préparées, en deux temps. D'abord prepare : j'envoie la structure de la requête avec un marqueur, par exemple deux-points email, à la place de la valeur. Puis execute : j'envoie la valeur séparément. MySQL traite alors cette valeur comme une simple donnée, jamais comme du code SQL. C'est ce qui rend l'injection SQL structurellement impossible. J'ai environ 74 requêtes préparées dans le projet. »
- **Code à montrer :**
  ```php
  $stmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
  $stmt->execute(['email' => $email]);
  ```

### Diapo 39 — Le pattern Singleton
- **À l'écran :** classe `Database`, `getInstance()`, constructeur privé, `__clone` privé.
- **Capture :** capture de `class.database.php`.
- **Script :** « La connexion à la base est centralisée dans une classe Database qui suit le pattern Singleton : une seule connexion partagée pour toute la requête. Ouvrir une connexion coûte des ressources ; si chaque modèle ouvrait la sienne, j'en aurais une dizaine pour une seule page. getInstance crée la connexion la première fois, puis renvoie toujours la même. Le constructeur est privé et le clonage est bloqué, pour garantir qu'il ne peut y en avoir qu'une. »
- **Code à montrer :**
  ```php
  public static function getInstance(): Database {
      if (!self::$_instance) { self::$_instance = new self(); }
      return self::$_instance;
  }
  private function __construct() { /* new PDO(... EMULATE_PREPARES => false ...) */ }
  private function __clone() {}
  ```

### Diapo 40 — Authentification : vue d'ensemble
- **À l'écran :** 3 profils (1 admin, 2 restaurateur, 3 client) + sessions `$_SESSION`.
- **Capture :** schéma des profils et de la session.
- **Script :** « L'authentification repose sur trois profils, identifiés par un numéro : 1 administrateur, 2 restaurateur, 3 client. Une fois connecté, je stocke l'utilisateur dans la session PHP, et c'est ce profil qui détermine à quoi il a accès et vers quel espace il est redirigé. La session, démarrée dans functions.php, garde l'utilisateur connecté d'une page à l'autre. »

### Diapo 41 — Hachage bcrypt
- **À l'écran :** `password_hash(...)` à l'inscription.
- **Capture :** capture de `insertUtilisateur` dans `class.users.php`.
- **Script :** « Le mot de passe n'est jamais stocké en clair. À l'inscription, je le hache avec password_hash et l'algorithme bcrypt. Bcrypt est lent par conception : c'est volontaire, ça rend la force brute impraticable. Le paramètre cost contrôle cette lenteur. En base, on ne trouve que l'empreinte, impossible à inverser. »
- **Code à montrer :**
  ```php
  $pass = password_hash(
      $utilisateur['motdepasse'] . $utilisateur['email'] . BASE_SALT,
      PASSWORD_BCRYPT, ['cost' => 9]
  );
  ```

### Diapo 42 — Le pepper (BASE_SALT)
- **À l'écran :** concaténation mot de passe + email + `BASE_SALT` (hors base).
- **Capture :** la ligne de concaténation + le `.env`.
- **Script :** « En plus du sel aléatoire que bcrypt génère déjà tout seul, j'ajoute un secret global, BASE_SALT, stocké dans le fichier .env, jamais en base et jamais versionné. Techniquement c'est un "poivre". Son intérêt : si un attaquant volait uniquement la base, il aurait les hashs et les sels, mais pas ce secret qui est dans le code — sans lui, il ne peut pas reconstituer les mots de passe. C'est de la défense en profondeur. Pour être honnête, l'essentiel de la sécurité vient de bcrypt lui-même ; le pepper est une couche supplémentaire. »

### Diapo 43 — Inscription
- **À l'écran :** validation serveur → création en 2 tables liées.
- **Capture :** formulaire + extrait de `AuthController::register`.
- **Script :** « À l'inscription, je valide chaque champ côté serveur : longueur du nom et prénom, format de l'email et unicité, code postal à 5 chiffres, mot de passe d'au moins 8 caractères confirmé deux fois. Je valide côté serveur parce que les contrôles du navigateur peuvent être contournés. Si tout est bon, je crée d'abord le compte dans utilisateurs, puis la fiche métier, puis je relie les deux par le profil_id. »

### Diapo 44 — Vérification au login
- **À l'écran :** `tryToConnect` → `password_verify`.
- **Capture :** capture du bloc `password_verify` dans `class.users.php`.
- **Script :** « À la connexion, je ne déchiffre jamais le hash — c'est impossible. J'utilise password_verify, qui re-hache le mot de passe saisi avec la même combinaison — mot de passe, email, pepper — et compare les deux empreintes. Je vérifie aussi que le compte est actif. Si tout correspond, j'ouvre la session et je charge la fiche métier de l'utilisateur. »
- **Code à montrer :**
  ```php
  if (!password_verify($pass . $email . BASE_SALT, $user['motdepasse'])) {
      (new UserLog())->log(0, 'login_fail', "Echec connexion pour $email");
      return false;
  }
  ```

### Diapo 45 — Le contrôleur de connexion
- **À l'écran :** redirection selon le profil + message d'erreur générique.
- **Capture :** capture de `AuthController::login`.
- **Script :** « Le contrôleur fait le pont entre le formulaire et le modèle. Si la connexion réussit, il redirige selon le profil : l'admin vers le dashboard, le restaurateur vers son espace pro, le client vers son compte. Détail de sécurité : en cas d'échec, le message est volontairement générique — "identifiants invalides" — sans dire si c'est l'email ou le mot de passe qui est faux, pour ne pas révéler quels emails sont enregistrés. »
- **Code à montrer :**
  ```php
  if ($profil == 1)      $redirect_url = 'dashboard';
  elseif ($profil == 2)  $redirect_url = 'mon-compte-restaurateur';
  else                   $redirect_url = 'mon-compte';
  ```

### Diapo 46 — Protection des pages privées
- **À l'écran :** garde en début de méthode → redirection si non autorisé.
- **Capture :** capture d'une garde (ex. `gestionCarte` ou `dashboard`).
- **Script :** « Chaque méthode protégée commence par une vérification de session : l'utilisateur doit être connecté et avoir le bon profil. Par exemple, l'espace restaurateur refuse tout profil supérieur à 2, et le dashboard admin refuse tout profil supérieur à 1. Si la condition n'est pas remplie, je redirige immédiatement avec header puis exit — le contenu protégé n'est jamais envoyé. Et cette vérification est aussi refaite côté serveur sur les appels AJAX, parce qu'on ne fait jamais confiance au navigateur. »
- **Code à montrer :**
  ```php
  if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true
      || $_SESSION['user']['profil'] > 2) {
      header('Location: ' . APP_URL . '/connexion');
      exit();
  }
  ```

### Diapo 47 — La déconnexion
- **À l'écran :** vider `$_SESSION` → `session_destroy()` → redirection.
- **Capture :** capture de `AuthController::logout`.
- **Script :** « La déconnexion fait trois choses : elle enregistre l'événement dans mes journaux, elle vide complètement le tableau de session, puis elle détruit la session côté serveur avec session_destroy, et enfin elle redirige vers l'accueil. Après ça, plus aucune information de l'utilisateur ne subsiste. »
- **Code à montrer :**
  ```php
  $_SESSION = [];
  session_destroy();
  header('Location: ' . APP_URL . '/accueil');
  exit();
  ```

### Diapo 48 — Le CRUD des plats
- **À l'écran :** Create / Read / Update / Delete sur les plats.
- **Capture :** capture de la gestion de carte (liste des plats par catégorie).
- **Script :** « Le cœur fonctionnel pour le restaurateur, c'est le CRUD des plats : créer, lire, modifier, supprimer. Chaque opération passe par le modèle Plat, qui est le seul à parler à la base. La création insère le plat avec sa catégorie, son prix et son image ; la lecture les récupère triés par catégorie ; la modification met à jour la ligne ; la suppression la retire. À la création et à la modification, le prix est validé et l'image passe par mon uploader centralisé. »
- **Code à montrer :**
  ```php
  // Lecture, triée par catégorie puis par nom
  $stmt = $this->pdo->prepare(
      "SELECT * FROM `plats` WHERE `id_restaurant` = :id_restaurant
       ORDER BY FIELD(`categorie`,'Entrées','Plats','Desserts','Boissons','Snacks'), `nom` ASC"
  );
  ```

### Diapo 49 — Contrôle d'ownership & CSRF
- **À l'écran :** jointure SQL qui vérifie le propriétaire · CSRF en perspective.
- **Capture :** capture de `Plat::getOwnedBy` (jointure plats ↔ restaurants).
- **Script :** « Une vérification de session ne suffit pas : un restaurateur connecté ne doit pouvoir modifier que SES plats. Je le garantis par une jointure SQL — le contrôle d'ownership : je ne récupère le plat que s'il appartient à un restaurant du restaurateur connecté. Si ça ne correspond pas, l'action est refusée. C'est ce qui empêche un restaurateur de modifier la carte d'un autre en changeant simplement un id dans l'URL. En perspective d'amélioration, j'ajouterais des tokens CSRF sur les formulaires et les appels AJAX. »
- **Code à montrer :**
  ```php
  $stmt = $this->pdo->prepare(
      "SELECT p.id, p.disponible FROM `plats` p
       JOIN `restaurants` r ON r.id_restaurant = p.id_restaurant
       WHERE p.id = :id_plat AND r.id_restaurateur = :id_restaurateur"
  );
  ```

---

## 7. Tests & qualité

### Diapo 50 — Jeux d'essai
- **À l'écran :** tableau de cas : action / résultat attendu / résultat obtenu.
- **Capture :** ton tableau de jeux d'essai.
- **Script :** « Pour valider l'application, j'ai défini des jeux d'essai : pour chaque cas, l'action testée, le résultat attendu, et le résultat obtenu. J'ai couvert à la fois des cas fonctionnels — créer un plat, se connecter — et des cas d'erreur — mauvais mot de passe, champ vide, prix invalide — pour vérifier que l'application réagit correctement dans tous les cas. »

### Diapo 51 — Tests de sécurité
- **À l'écran :** injection SQL refusée · accès admin bloqué.
- **Capture :** capture d'une tentative d'injection sans effet + redirection d'un accès non autorisé.
- **Script :** « J'ai aussi testé les protections. Pour l'injection SQL : en tapant une charge classique comme apostrophe OR 1 égal 1 dans le champ email, la requête préparée la traite comme du texte et la connexion échoue normalement. Pour le contrôle d'accès : en essayant d'atteindre une page admin sans être connecté, ou avec un profil client, je suis bien redirigé vers la connexion. Ce sont les preuves que mes protections fonctionnent. »

### Diapo 52 — Audit Lighthouse
- **À l'écran :** scores performance / accessibilité / bonnes pratiques / SEO.
- **Capture :** capture du rapport Lighthouse.
- **Script :** « J'ai mesuré la qualité avec Lighthouse, l'outil de Google, sur quatre axes : performance, accessibilité, bonnes pratiques et SEO. Le rapport m'a guidé sur les points à améliorer, comme l'optimisation des images et les attributs manquants, que j'ai ensuite corrigés. »

### Diapo 53 — Validation W3C
- **À l'écran :** « No errors or warnings ».
- **Capture :** capture du validateur W3C.
- **Script :** « J'ai validé mon HTML avec le validateur du W3C pour vérifier qu'il respecte les standards. Un HTML conforme, c'est l'assurance d'un affichage cohérent entre les navigateurs et d'une meilleure accessibilité. »

### Diapo 54 — Audit WAVE
- **À l'écran :** WAVE avant / après corrections.
- **Capture :** captures WAVE.
- **Script :** « Avec l'outil WAVE, j'ai analysé l'accessibilité plus en détail. Il a remonté des erreurs — contrastes insuffisants, images sans texte alternatif, labels manquants — que j'ai corrigées. L'écran avant/après montre la réduction des erreurs. »

### Diapo 55 — Accessibilité : choix concrets
- **À l'écran :** `lang="fr"`, attributs `alt`, `aria-label`, contraste.
- **Capture :** captures montrant ces éléments dans le code.
- **Script :** « Concrètement, mes choix d'accessibilité : la langue déclarée sur la page avec lang égale fr, des textes alternatifs sur les images, des labels sur les champs de formulaire, des aria-label sur les boutons d'icône, et des contrastes suffisants. L'objectif, c'est que le site soit utilisable y compris avec un lecteur d'écran. »

### Diapo 56 — Éco-conception
- **À l'écran :** images WebP · dimensions fixes · JS chargé là où il sert.
- **Capture :** une image WebP + le hook `$custom_js`.
- **Script :** « J'ai pris en compte l'éco-conception : des images au format WebP, plus léger, avec des dimensions fixes pour éviter les recalculs d'affichage, et un JavaScript chargé seulement là où il est utile — par exemple la librairie de glisser-déposer uniquement sur la page de gestion. Moins de données transférées, c'est plus rapide et plus sobre. »

---

## 8. Déploiement

### Diapo 57 — Environnement & configuration
- **À l'écran :** `config.php` détecte local (Docker) vs production (o2switch).
- **Capture :** capture de `config.php`.
- **Script :** « Le déploiement repose sur une configuration qui s'adapte toute seule. config.php détecte s'il tourne en local ou en production, et charge les bons identifiants de base et la bonne URL. Les secrets sont dans un fichier .env non versionné, pas en dur dans le code. Résultat : je n'ai jamais à modifier le code entre mon poste et le serveur. »
- **Code à montrer :**
  ```php
  $isLocal = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost');
  if ($isLocal) { /* identifiants Docker + APP_URL locale */ }
  else          { /* identifiants o2switch + APP_URL prod */ }
  ```

### Diapo 58 — Mise en ligne o2switch
- **À l'écran :** transfert FTP · base importée · `.env` de production.
- **Capture :** capture FileZilla + le site en ligne (URL o2switch).
- **Script :** « J'ai mis le site en ligne chez l'hébergeur o2switch : transfert des fichiers par FTP avec FileZilla, import de la base de données via le script SQL, et création du fichier .env de production avec les identifiants du serveur. Grâce à la détection automatique de config.php, le site a fonctionné directement en ligne. »

---

## 9. Démonstration & clôture

### Diapo 59 — Démonstration live
- **À l'écran :** transition vers le navigateur (site en ligne).
- **Capture :** page d'accueil en ligne.
- **Script :** « Je vous propose maintenant une démonstration en direct. Je vais parcourir un scénario complet : consulter un restaurant en visiteur, me connecter en restaurateur, ajouter un plat, le réorganiser par glisser-déposer, et basculer sa disponibilité — pour montrer les fonctionnalités en conditions réelles. »
- **Note :** garde un parcours répété d'avance ; en secours, une vidéo ou des captures si le réseau lâche.

### Diapo 60 — Compétences & veille
- **À l'écran :** compétences front/back consolidées · veille (MDN, PHP.net).
- **Capture :** image stack technique + logos MDN / PHP.net.
- **Script :** « Ce projet m'a permis de consolider les compétences front-end et back-end du référentiel : conception, sécurité, base de données, déploiement. Pour me tenir à jour, je fais une veille régulière sur des sources fiables comme la documentation MDN et PHP.net, que j'ai beaucoup utilisées pour résoudre mes problèmes. »

### Diapo 61 — Difficultés rencontrées
- **À l'écran :** problème → solution.
- **Capture :** icône ou schéma « problème → solution ».
- **Script :** « Deux difficultés marquantes. D'abord, garder une architecture MVC rigoureuse : au début, j'avais tendance à mélanger SQL et HTML ; j'ai dû discipliner la séparation des responsabilités. Ensuite, le glisser-déposer, qui combine trois technologies — la librairie front, l'AJAX, et le PHP côté serveur avec la sécurité ; faire dialoguer les trois proprement a demandé du travail. »

### Diapo 62 — Axes d'évolution
- **À l'écran :** CSRF · paiement en ligne · e-mails · application mobile/PWA.
- **Capture :** icônes des évolutions.
- **Script :** « Si je continuais le projet : ajouter des tokens CSRF pour renforcer les formulaires, intégrer un vrai paiement en ligne, envoyer des e-mails de confirmation, et à terme une application mobile ou une version PWA. Le tunnel de commande complet est la suite logique de ce que j'ai construit. »

### Diapo 63 — Conclusion & remerciements
- **À l'écran :** logo Miamy + « Merci ».
- **Capture :** logo Miamy.
- **Script :** « Pour conclure : Miamy m'a fait parcourir tout le cycle d'un projet web, de la conception au déploiement, en développant tout à la main pour vraiment comprendre les mécanismes. Je remercie mon tuteur Yann pour son accompagnement, et vous pour votre attention. Je suis à votre disposition pour vos questions. »

---

> **Note de numérotation :** ce plan détaillé compte 63 diapos (une de plus que le plan d'origine), car les wireframes sont scindés en deux (accueil + gestion de carte), conformément à ta présentation actuelle. Tu peux fusionner les diapos 14 et 15 si tu veux revenir à 62.
