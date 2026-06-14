# Fiche oral — Base de données : PDO, Singleton et requêtes préparées

> Format **Question (jury) / Réponse (ce que je dois dire)**.
> On complétera cette fiche au fur et à mesure.
> Toutes les réponses ci-dessous ont été vérifiées dans le code réel du projet.

---

## 1. La connexion à la base

**Q — Comment ton site se connecte-t-il à la base de données ?**

R — J'utilise **PDO**, l'outil standard de PHP pour dialoguer avec une base de données. Toute la connexion est centralisée dans une seule classe, `Database` (fichier `classes/class.database.php`). Aucune autre partie du code n'ouvre de connexion : tout le monde passe par cette classe.

---

**Q — Pourquoi PDO et pas autre chose (mysqli, etc.) ?**

R — PDO est l'extension officielle et moderne de PHP. Elle gère les **requêtes préparées**, qui protègent contre les injections SQL, et elle est portable : si je changeais de type de base, le code resterait quasiment le même. C'est aussi ce qui est recommandé aujourd'hui.

---

**Q — Où sont stockés tes identifiants de connexion (mot de passe de la base) ?**

R — Ils ne sont **pas écrits en dur** dans le code. Ils sont dans un fichier `.env` qui n'est pas envoyé sur Git, et c'est `config.php` qui les lit et les met dans des constantes (`DB_HOST`, `DB_USERNAME`, etc.). `config.php` détecte aussi automatiquement si je suis en local (Docker) ou en production (o2switch) et charge les bons identifiants selon le cas.

---

## 2. Le pattern Singleton

**Q — Qu'est-ce que le pattern Singleton ?**

R — C'est un patron de conception qui garantit qu'une classe ne peut avoir **qu'une seule instance** dans toute l'application. Chez moi, je l'utilise pour la connexion à la base : je veux **une seule connexion** partagée par toute la page, pas une nouvelle connexion à chaque fois que j'interroge la base.

---

**Q — Comment fonctionne ton Singleton, concrètement ?**

R — Il y a trois éléments clés dans `Database` :

1. Une propriété **statique** `$_instance` qui garde l'unique instance.
2. Une méthode `getInstance()` : la première fois qu'on l'appelle, elle crée l'objet ; ensuite, elle renvoie toujours le **même**.
3. Le **constructeur est privé**, donc on ne peut pas faire `new Database()` ailleurs dans le code. On est obligé de passer par `getInstance()`.

Donc partout dans mes modèles j'écris `Database::getInstance()->getConnection()`, et je récupère toujours la même connexion.

---

**Q — Pourquoi le constructeur est-il privé ?**

R — Pour empêcher qu'on crée un objet `Database` directement avec `new`. Si on pouvait le faire, on pourrait ouvrir plusieurs connexions par erreur, ce qui casserait justement le principe d'instance unique. En rendant le constructeur privé, le seul moyen d'obtenir l'objet est de passer par `getInstance()`, et là je contrôle qu'il n'y en a qu'un.

---

**Q — À quoi sert la méthode `__clone()` privée ?**

R — Elle empêche de **cloner** l'objet. Sans ça, quelqu'un pourrait faire une copie de l'instance avec `clone`, et on se retrouverait avec deux objets `Database`. La rendre privée bloque cette possibilité et protège l'instance unique.

---

**Q — Pourquoi vouloir une seule connexion ? Quel est l'intérêt ?**

R — Ouvrir une connexion à une base de données coûte des ressources (temps et mémoire côté serveur). Si chaque modèle ouvrait sa propre connexion, j'en aurais une dizaine pour une seule page. Avec le Singleton, j'ouvre **une seule fois** et tout le monde réutilise la même. C'est plus économe et plus simple à gérer.

---

**Q — Le Singleton est parfois critiqué. Tu en penses quoi ?**

R — Oui, c'est vrai. On lui reproche d'être une sorte de variable globale : comme on peut l'appeler de partout, les dépendances deviennent moins visibles, et c'est plus difficile à tester unitairement. L'alternative plus « propre » serait l'injection de dépendances : créer la connexion une fois et la passer en paramètre à chaque classe. Pour un projet de cette taille, j'ai choisi le Singleton parce qu'il répond bien au besoin (une seule connexion), qu'il reste simple à lire, et que c'est un pattern reconnu. Mais je sais que sur un plus gros projet, l'injection de dépendances serait préférable.

> *Astuce : ne dis ça que si tu te sens à l'aise. Sinon, tiens-toi en au « pourquoi une seule connexion » plus haut.*

---

## 3. La configuration de PDO

**Q — Tu as configuré des options sur PDO. Lesquelles et pourquoi ?**

R — J'ai mis trois options importantes à la création de la connexion :

- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` : si une requête SQL échoue, PHP lève une **exception** que je peux attraper avec `try/catch`. Sinon les erreurs passeraient inaperçues.
- `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` : les résultats reviennent sous forme de **tableau associatif**, donc j'écris `$row['email']` au lieu de `$row[0]`. C'est plus lisible.
- `PDO::ATTR_EMULATE_PREPARES => false` : je force les **vraies** requêtes préparées de MySQL au lieu d'une simulation faite par PHP. C'est plus sûr.

J'utilise aussi le jeu de caractères `utf8mb4` pour gérer correctement les accents et les emojis.

---

**Q — Tu fais quelque chose pour les dates/heures ?**

R — Oui, juste après la connexion je règle le fuseau horaire de MySQL sur celui de Paris. Comme ça, quand la base écrit une date automatiquement (par exemple la date d'une commande), c'est bien l'heure française et pas l'heure du serveur.

---

## 4. Les requêtes préparées et l'injection SQL

**Q — Comment protèges-tu ton site contre les injections SQL ?**

R — J'utilise des **requêtes préparées** partout où il y a une donnée venant de l'utilisateur. Le principe se fait en deux temps : d'abord `prepare()`, où j'envoie le modèle de requête avec des **marqueurs** (par exemple `:email`) à la place des valeurs ; puis `execute()`, où j'envoie les valeurs séparément. MySQL traite alors ces valeurs comme des **données**, jamais comme du code SQL. Du coup l'injection devient structurellement impossible.

---

**Q — Tu peux me donner un exemple concret ?**

R — Oui. Au lieu d'écrire (version dangereuse) :

```php
$query = "SELECT * FROM utilisateurs WHERE email = '" . $_POST['email'] . "'";
```

…ce qui permettrait à quelqu'un d'injecter du SQL via le champ email, j'écris :

```php
$stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_POST['email']]);
```

Même si l'attaquant tape `' OR '1'='1`, MySQL le cherche comme un email littéral, ne trouve rien, et ne renvoie rien.

---

**Q — Es-tu sûr que TOUTES tes requêtes sont protégées ?**

R — Oui, partout où il y a une donnée utilisateur. Dans le projet il y a environ **74 requêtes préparées**. Il me reste quelques `query()` simples, mais uniquement pour des requêtes **sans aucune donnée utilisateur** : par exemple compter le nombre de restaurants, ou lister toutes les catégories. Comme il n'y a rien de variable à l'intérieur, il n'y a pas de risque d'injection.

---

**Q — Et quand tu mets un nom de table directement dans la requête ? (question piège)**

R — Bonne remarque, parce qu'un nom de table ne peut pas être un marqueur préparé. Dans le seul endroit où je fais ça (le changement de profil d'un utilisateur), le nom de table ne vient **jamais** de l'utilisateur : il est choisi dans une **liste fermée codée en dur** dans mon code (`administrateurs`, `restaurateurs`, `clients`), et je vérifie qu'il en fait partie avant de l'utiliser. Donc l'utilisateur ne peut pas y glisser ce qu'il veut.

---

## 5. Récapitulatif express (à se répéter avant l'oral)

- **PDO** = la couche standard de PHP pour parler à la base.
- **Singleton** = une seule connexion pour toute la page, via `Database::getInstance()`.
- **Constructeur privé + `__clone()` privé** = on ne peut pas créer ou copier une 2ᵉ connexion.
- **Requêtes préparées** = `prepare()` (le modèle) + `execute()` (les données) → pas d'injection SQL.
- **Identifiants dans `.env`**, jamais en dur dans le code.

---

# Partie 2 — Architecture MVC et routage

## 6. L'architecture du projet

**Q — Comment ton projet est-il organisé ?**

R — J'ai suivi une architecture **MVC** (Modèle-Vue-Contrôleur), avec un dossier par rôle :

- `classes/` = les **modèles** : chaque classe gère une entité (utilisateurs, plats, restaurants…) et c'est la seule à parler à la base.
- `controllers/` = les **contrôleurs** : ils reçoivent la demande, appellent les modèles, et préparent les données.
- `views/` = les **vues** : uniquement l'affichage HTML, elles ne font pas de logique métier.

Tout passe par un **point d'entrée unique**, `index.php`, qui joue le rôle de routeur.

---

**Q — Pourquoi avoir séparé en MVC ? Quel intérêt ?**

R — Pour ne pas tout mélanger. Avant, j'avais du SQL, du PHP et du HTML dans le même fichier, c'était vite illisible. Avec le MVC, chaque partie a sa responsabilité : si je dois changer une requête, je vais dans le modèle ; si je change l'affichage, je touche la vue. C'est plus clair, plus facile à corriger et à faire évoluer.

---

**Q — Qu'est-ce qu'un modèle, concrètement, chez toi ?**

R — C'est une classe qui représente une entité de la base. Par exemple `User` gère la table `utilisateurs`. Le modèle contient les méthodes pour lire, créer, modifier ou supprimer ces données, toujours avec des requêtes préparées. C'est le **seul** endroit où on écrit du SQL : ni les contrôleurs ni les vues n'en contiennent (à part quelques requêtes de comptage dans le contrôleur admin, sans donnée utilisateur).

---

## 7. Le routage (index.php)

**Q — Comment fonctionne ton routeur ? Comment sais-tu quelle page afficher ?**

R — Tout passe par `index.php`. Je récupère le nom de la page demandée dans l'URL, dans le paramètre `mod` (par exemple `?mod=connexion`). S'il n'y a rien, j'affiche l'accueil par défaut.

Ensuite, j'ai un tableau `$dispatchMap` qui associe chaque nom de page à un **contrôleur et une méthode**. Si la page demandée est dans ce tableau, j'appelle la bonne méthode du bon contrôleur, et je récupère les données qu'elle renvoie. Enfin, j'inclus la vue correspondante.

---

**Q — Pourquoi un tableau de routage plutôt qu'une série de `if` ou un `switch` ?**

R — Parce que c'est plus propre et plus facile à maintenir. Pour ajouter une page, j'ajoute juste **une ligne** dans le tableau, je n'ai pas à toucher la logique. Le tableau sert de « carte » lisible de toutes les routes du site, d'un seul coup d'œil.

---

**Q — Comment les données du contrôleur arrivent-elles jusqu'à la vue ?**

R — Mes méthodes de contrôleur renvoient un **tableau associatif** (avec `compact()`). Dans `index.php`, j'utilise `extract()` sur ce tableau, ce qui transforme chaque clé en variable utilisable directement dans la vue. Par exemple le contrôleur renvoie `message_error`, et la vue peut afficher `$message_error`.

---

**Q — Et le titre de la page, l'URL du fichier de vue, d'où viennent-ils ?**

R — Ils sont stockés en base, dans une table `pages`. Le modèle `Page` va chercher le nom et le chemin du fichier de vue correspondant à la page demandée. Si la page n'existe pas, j'affiche une page **404**. C'est ce qui me permet de gérer les pages de façon centralisée plutôt qu'en dur dans le code.

---

**Q — Tu as parlé de deux mises en page (layouts). Explique.**

R — Oui. Selon la page, j'inclus un gabarit différent. Si le fichier de la vue est dans `views/admin/`, j'utilise le **layout administrateur** (en-tête et pied de page du back-office). Sinon, j'utilise le **layout public** classique du site (en-tête, menu, pied de page). Ça évite de réécrire l'en-tête et le pied sur chaque page : ce sont des fichiers `partials` que j'inclus.

---

# Partie 3 — Authentification et sécurité des comptes

## 8. Connexion et sessions

**Q — Comment se passe la connexion d'un utilisateur ?**

R — Quand l'utilisateur envoie le formulaire de connexion, le contrôleur `AuthController` récupère l'email et le mot de passe, puis appelle la méthode `tryToConnect()` du modèle `User`. Cette méthode cherche l'utilisateur par son email (et vérifie qu'il est actif), puis compare le mot de passe. Si tout est bon, je stocke ses infos en **session** et je le redirige selon son profil.

---

**Q — Comment gardes-tu un utilisateur connecté d'une page à l'autre ?**

R — J'utilise les **sessions PHP**. Je fais `session_start()` tout en haut, dans `functions.php`, qui est inclus au début de chaque page. Une fois connecté, je stocke dans `$_SESSION` les infos de l'utilisateur et un indicateur `connected`. À chaque page, je peux donc vérifier s'il est connecté et qui il est.

---

**Q — Comment se passe la déconnexion ?**

R — Je vide complètement `$_SESSION`, puis j'appelle `session_destroy()` pour détruire la session côté serveur, et je redirige vers l'accueil. J'enregistre aussi la déconnexion dans mes journaux (logs).

---

## 9. Mots de passe

**Q — Comment sont stockés les mots de passe ? (question quasi certaine)**

R — Ils ne sont **jamais** stockés en clair. J'utilise la fonction `password_hash()` de PHP avec l'algorithme **bcrypt**. En base, on ne trouve que l'empreinte (le hash), impossible à inverser. À la connexion, je ne compare pas les mots de passe directement : j'utilise `password_verify()`, qui re-hashe ce que l'utilisateur a tapé et compare avec l'empreinte stockée.

---

**Q — C'est quoi le "salt" / pourquoi concaténer l'email et une clé ?**

R — Avant de hasher, je concatène le mot de passe avec l'email de l'utilisateur et une clé secrète (`BASE_SALT`) stockée dans le `.env`. Cette clé secrète joue le rôle de **« poivre »** (pepper) : c'est un secret côté serveur qui rend les attaques par dictionnaire plus difficiles, même si la base fuite.

> **À savoir (honnêteté technique)** : bcrypt génère **déjà** automatiquement un sel aléatoire unique pour chaque mot de passe. Donc l'essentiel de la sécurité vient de bcrypt lui-même ; ma concaténation email + `BASE_SALT` est un **plus** (un pepper), pas une obligation. Si le jury demande « bcrypt ne sale-t-il pas déjà ? », la bonne réponse est : « si, bcrypt sale tout seul ; le `BASE_SALT` que j'ajoute est un secret serveur supplémentaire ». Ne prétends pas que sans cette concaténation ce serait non sécurisé — ce serait faux.

---

**Q — Quelles règles imposes-tu sur les mots de passe à l'inscription ?**

R — Le mot de passe doit faire **au moins 8 caractères**, et il doit être saisi deux fois à l'identique (confirmation). Si ces conditions ne sont pas remplies, l'inscription est refusée avec un message d'erreur.

---

## 10. Inscription et validation des données

**Q — Que vérifies-tu quand quelqu'un s'inscrit ?**

R — Je valide chaque champ côté serveur avant d'enregistrer : le nom et le prénom doivent faire entre 2 et 50 caractères, l'email doit être à un format valide (`FILTER_VALIDATE_EMAIL`) et ne pas déjà exister en base, le code postal doit faire exactement 5 chiffres, le mot de passe doit respecter ses règles. Je nettoie aussi les entrées texte avec une fonction `sanitizeString()` et l'email avec `FILTER_SANITIZE_EMAIL`. Tant qu'il reste une erreur, je n'enregistre rien.

---

**Q — Pourquoi valider côté serveur si le formulaire a déjà des contrôles ?**

R — Parce que les contrôles côté navigateur (HTML/JavaScript) peuvent être contournés très facilement : il suffit de désactiver le JavaScript ou d'envoyer la requête directement. La validation **côté serveur** est la seule sur laquelle je peux vraiment compter. Le côté client, c'est juste pour le confort de l'utilisateur.

---

**Q — Un compte est créé en deux tables. Pourquoi ? Et si ça échoue au milieu ?**

R — Oui : il y a une table `utilisateurs` (email, mot de passe, profil) commune à tout le monde, et une table « métier » selon le profil (`clients`, `restaurateurs`, `administrateurs`) pour les infos spécifiques. À l'inscription, je crée d'abord l'utilisateur, puis la fiche métier, puis je relie les deux. Pour les opérations sensibles comme la suppression ou le changement de profil, j'utilise des **transactions** : si une étape échoue, je fais un `rollBack()` et rien n'est enregistré, pour ne pas laisser la base dans un état incohérent.

---

## 11. Contrôle d'accès (qui a le droit de voir quoi)

**Q — Comment empêches-tu un simple client d'accéder aux pages d'administration ?**

R — Au **début de chaque méthode protégée** du contrôleur, je vérifie la session : l'utilisateur doit être connecté et avoir le bon profil. Par exemple, le tableau de bord admin vérifie que `profil <= 1` (administrateur). Si la condition n'est pas remplie, je le redirige immédiatement vers la page de connexion avec `header()` + `exit()`, donc le contenu protégé n'est jamais affiché.

---

**Q — Les profils, c'est quoi exactement ?**

R — J'ai trois profils, identifiés par un numéro : **1 = administrateur**, **2 = restaurateur**, **3 = client**. Après la connexion, je redirige chacun vers son espace (l'admin vers le dashboard, le restaurateur vers son compte pro, le client vers son compte). Et c'est ce profil qui détermine à quelles pages il a accès.

---

## 12. Récap express — auth & MVC

- **MVC** : modèles (`classes/`) = données + SQL ; contrôleurs (`controllers/`) = logique ; vues (`views/`) = affichage.
- **Routeur unique** : `index.php` + tableau `$dispatchMap` (page → contrôleur/méthode).
- **Données contrôleur → vue** : `compact()` puis `extract()`.
- **Sessions** : `session_start()` dans `functions.php`, infos dans `$_SESSION`.
- **Mots de passe** : `password_hash()` (bcrypt) + `password_verify()`, jamais en clair.
- **Validation systématique côté serveur** (longueurs, email, code postal, mot de passe).
- **Contrôle d'accès** en début de chaque méthode protégée, sinon redirection.

---

*(Sections suivantes à compléter : AJAX/toggle, horaires, upload d'images, déploiement o2switch, etc.)*
