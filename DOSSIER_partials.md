# Les partials de Miamy : head, header, footer, foot

## Pourquoi des "partials" ?

Quand j'ai commencé à coder Miamy, j'avais une question évidente à régler : comment je fais pour que **chaque page ait le même menu, le même footer, les mêmes scripts**, sans recopier 200 lignes de HTML à chaque fois ? La réponse classique en PHP, c'est le **système de partials**.

Un partial, c'est un bout de vue qu'on isole dans son propre fichier (`head.php`, `header.php`, etc.) et qu'on inclut dans toutes les pages qui en ont besoin avec `include()`. L'idée tient en une phrase : ce qui se répète, on l'écrit une seule fois.

Concrètement, dans Miamy, j'ai créé un dossier `views/partials/` qui contient tout le squelette HTML commun :

```
views/partials/
├── head.php          ← <head> HTML : meta + tous les CSS
├── header.php        ← topbar + navbar + logo + menu burger
├── footer.php        ← footer visible (liens, copyright, panier)
├── foot.php          ← scripts JS + </body></html>
├── admin_head.php    ← variante back-office (SB Admin)
├── admin_foot.php    ← variante back-office (scripts + </body>)
└── panier.php        ← modal de panier, inclus depuis footer.php
```

L'orchestration se fait dans `index.php`, le seul point d'entrée du site grâce au front controller. Je vais détailler tout ça.

## index.php : le chef d'orchestre

Avant même de parler des partials, il faut comprendre **qui décide d'inclure quoi**. C'est `index.php` qui prend cette décision, à la fin de son traitement, juste avant de rendre la page :

```php
// Une page est admin si son fichier est dans views/admin/
$is_admin_page = strpos($page_url, 'views/admin/') === 0;

if ($is_admin_page) {
    // Layout administrateur (template SB Admin)
    include('views/partials/admin_head.php');
    include($page_url);
    include('views/partials/admin_foot.php');
} else {
    // Layout public classique
    include('views/partials/head.php');
    include('views/partials/header.php');
    include($page_url);
    include('views/partials/footer.php');
    include('views/partials/foot.php');
}
```

C'est ce que j'appelle un **double layout** : une page publique passe par 5 includes (head + header + vue + footer + foot), une page admin passe par 3 includes seulement (admin_head + vue + admin_foot). Le choix se fait sur un simple `strpos()` : si le chemin de la vue commence par `views/admin/`, c'est de l'admin, sinon c'est public.

Pourquoi cette séparation ? Parce que le design d'un dashboard admin n'a rien à voir avec celui d'un site public. L'admin utilise le template **SB Admin** (sidebar fixe, topbar sombre), le public utilise **Foodingly** (banner, menu horizontal, topbar orange). Les deux ne partagent pratiquement aucun asset.

## head.php : le `<head>` du HTML public

Le fichier `head.php` contient tout ce qui se trouve dans la balise `<head>` HTML : meta, titre, et la liste de tous les CSS chargés. C'est lui qui prépare le navigateur avant qu'il affiche quoi que ce soit.

Voici ce que j'y mets, dans l'ordre d'apparition :

**La déclaration HTML5 et la langue**
```html
<!DOCTYPE html>
<html lang="fr">
```
J'ai mis `lang="fr"` parce que tout le contenu du site est en français. Ça aide les lecteurs d'écran à choisir la bonne voix et Google à indexer correctement la page.

**La `<base href>` dynamique**
```php
<base href="<?= APP_URL ?>/">
```
C'est une ligne discrète mais cruciale. La balise `<base>` indique au navigateur la racine à partir de laquelle interpréter tous les liens relatifs. Sans elle, un lien `<a href="connexion">` serait résolu différemment selon la page courante. Avec `<base href="http://localhost/Miamy/">`, le lien `connexion` devient toujours `http://localhost/Miamy/connexion`, peu importe d'où on vient. C'est ce qui me permet d'écrire tous mes liens sans préfixe partout dans le code.

**Le viewport mobile**
```html
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
```
Cette meta est obligatoire pour que le responsive fonctionne sur mobile. Sans elle, le navigateur mobile zoome automatiquement et le design éclate.

**Le `<title>` dynamique**
```php
<title><?php if(isset($page_title)){ echo $page_title; } else { ?> Miamy - Le menu interactif de votre restaurant<?php } ?></title>
```
Le titre change selon la page : "Inscription", "Connexion", "Mon compte"... Le contrôleur passe cette valeur via `extract($viewData)` dans `index.php`, et si elle existe, on l'affiche. Sinon, on tombe sur le titre générique du site. C'est la base du SEO : chaque page a son propre titre.

**Les CSS, dans l'ordre qui compte**

Je charge 12 feuilles de style, dans un ordre précis :

```
bootstrap.min.css        ← framework de base (grille + composants)
animate.min.css          ← animations CSS prêtes à l'emploi
fontawesome.all.min.css  ← icônes (FontAwesome 5)
bootstrap-icons.css      ← icônes (Bootstrap Icons, CDN)
owl.carousel.min.css     ← slider d'images
nouislider.css           ← curseurs de plage
owl.theme.default.min.css
navber.css               ← styles spécifiques à la navbar du template
meanmenu.css             ← styles du burger mobile
style.css                ← style général du template Foodingly
responsive.css           ← overrides pour mobile/tablette
responsive-fixes.css     ← MES corrections au template
```

L'ordre est essentiel : **la dernière feuille chargée gagne**. C'est pour ça que `responsive-fixes.css`, mon fichier de corrections, est tout à la fin : il peut overrider n'importe quelle règle du template sans devoir modifier les fichiers originaux. C'est exactement la philosophie d'un "patch CSS" en théming WordPress.

## header.php : la zone de navigation

Le fichier `header.php` est le plus dense des partials. Il contient quatre blocs visuels distincts :

**1. Le preloader**
```html
<div class="preloader">
    <div class="lds-spinner">...</div>
</div>
```
Une animation de chargement qui s'affiche le temps que la page charge. JavaScript la fait disparaître en fondu à la fin du chargement avec `jQuery(window).on('load', function() { jQuery(".preloader").fadeOut(500); })` (dans `custom.js`).

**2. La topbar (barre du haut, orange)**
C'est la fine bande en haut de l'écran. Elle est divisée en deux colonnes Bootstrap `col-lg-6 col-md-6` :

- **À gauche** : icônes réseaux sociaux + email de contact
- **À droite** : liens en fonction de l'état de connexion

C'est ici que se joue toute la logique d'**authentification visuelle** :

```php
<?php if (isset($_SESSION['connected']) && $_SESSION['connected'] == true): ?>
    <?php $profil = $_SESSION['user']['profil'] ?? 3; ?>
    <li class="topbar-username"><span><?= htmlspecialchars(...) ?></span></li>
    <?php if ($profil === 1): ?>
        <li><a href="dashboard">Dashboard Admin</a></li>
    <?php endif; ?>
    <li><a href="<?= $profil <= 2 ? 'mon-compte-restaurateur' : 'mon-compte' ?>">Mon compte</a></li>
    <li><a href="deconnexion">Déconnexion</a></li>
<?php else: ?>
    <li><a href="connexion">Connexion</a></li>
    <li><a href="inscription-client">Inscription</a></li>
<?php endif; ?>
```

Trois choses se passent ici :

D'abord, je teste si la session contient `connected = true`. Si oui, l'utilisateur est authentifié, je lui montre son prénom et ses options ; sinon, je lui propose Connexion / Inscription.

Ensuite, je récupère son profil (1 = admin, 2 = restaurateur, 3 = client). Si c'est un admin, je rajoute un lien supplémentaire "Dashboard Admin" pour qu'il puisse rejoindre rapidement le back-office.

Enfin, le lien "Mon compte" pointe vers une page différente selon le profil : `mon-compte-restaurateur` pour les pros (profil ≤ 2), `mon-compte` pour les clients. Une seule ligne PHP, deux comportements.

Le `htmlspecialchars()` autour du prénom n'est pas décoratif : c'est une **protection contre les attaques XSS**. Si un utilisateur arrivait à enregistrer un prénom comme `<script>alert('xss')</script>`, sans cet échappement il serait exécuté dès qu'on l'afficherait. Avec `htmlspecialchars`, il s'affiche tel quel, sous forme de texte.

**3. La navbar (logo + menu principal)**
Le bloc `<div class="navbar-area">` contient le logo Miamy à gauche et le menu horizontal au centre. La navbar a une structure spéciale parce qu'elle doit fonctionner **à la fois en desktop et en mobile** :

- `<div class="main-responsive-nav">` : version mobile, n'apparaît qu'en dessous de 1200px
- `<div class="main-navbar">` : version desktop, n'apparaît qu'à partir de 1200px

C'est le plugin **meanmenu** (chargé dans `foot.php`) qui transforme la `<ul class="navbar-nav">` en menu burger ☰ sur mobile, en clonant le contenu dans la `.main-responsive-nav`.

J'ai ajouté un détail subtil : pour les liens compte (Mon compte / Déconnexion / Connexion), j'ai mis la classe `d-xl-none` (Bootstrap : "display none à partir de xl = 1200px"). Comme ça :
- En desktop ≥ 1200px : ces liens sont cachés dans la navbar, parce que la topbar les affiche déjà
- En mobile < 1200px : ces liens sont visibles dans le burger menu, parce que la topbar les cache (ou est cachée)

Une seule source HTML, deux comportements visuels selon la largeur d'écran. C'est l'idée centrale du responsive design.

**4. La barre de recherche overlay**
Tout en bas du header, j'ai un `<div class="search-overlay">` caché par défaut. Quand l'utilisateur clique sur la loupe, JavaScript ajoute la classe `search-overlay-active` qui le fait apparaître en plein écran. Pas encore branché côté logique, c'est un placeholder pour une future fonctionnalité de recherche de plats.

## footer.php : le bas de page visible

Le footer est beaucoup plus simple que le header. Il contient :

- Une zone "Besoin d'aide ?" avec un lien vers la page contact et des icônes réseaux sociaux
- Une zone "Liens rapides" avec A propos, Testimonials, FAQ, CGV
- Une bande copyright avec l'année (`<?= date('Y') ?>`) et une image des cartes bancaires acceptées
- Un bouton "go-top" qui scrolle vers le haut au clic (géré par JavaScript)

À noter : `footer.php` commence par `<?php include('panier.php'); ?>`. Le partial `panier.php` contient un modal Bootstrap (HTML caché par défaut) qui s'ouvre quand on clique sur l'icône panier de la navbar. C'est inclus dans toutes les pages publiques pour que le panier soit accessible depuis n'importe où sur le site.

## foot.php : les scripts et la fermeture

Le fichier `foot.php` ne fait qu'une chose : charger les JavaScript dans l'ordre, puis fermer le `<body>` et le `<html>` :

```html
<script src="...jquery-3.6.0.min.js"></script>
<script src="...bootstrap.bundle.js"></script>
<script src="...jquery.meanmenu.js"></script>
<script src="...nouislider.min.js"></script>
<script src="...wNumb.js"></script>
<script src="...owl.carousel.min.js"></script>
<script src="...wow.min.js"></script>
<script src="...custom.js"></script>
<?php if(isset($custom_js)) { echo $custom_js; } ?>
</body>
</html>
```

L'ordre suit la règle "des dépendances avant les dépendants" :

1. **jQuery** d'abord, parce que tous les plugins qui suivent en ont besoin
2. **Bootstrap bundle** ensuite (inclut Popper.js pour les tooltips et dropdowns)
3. **meanmenu** (burger menu mobile), qui dépend de jQuery
4. **noUiSlider + wNumb** pour les curseurs de prix
5. **Owl Carousel** pour les sliders
6. **wow.js** pour les animations au scroll
7. **custom.js** en dernier : c'est mon fichier d'initialisation où j'appelle `jQuery('.mean-menu').meanmenu({ meanScreenWidth: "1199" })` et où je configure les autres comportements

Le `<?php if(isset($custom_js)) { echo $custom_js; } ?>` à la fin est une **trappe d'extension** : si une vue veut injecter un script supplémentaire (par exemple le drag & drop SortableJS de `gestion-carte`), elle peut le faire en définissant `$custom_js` dans son contrôleur, et il sera échappé en fin de page sans que j'ai à modifier `foot.php`.

**Pourquoi tous les scripts à la fin du `<body>` et pas dans le `<head>` ?** Parce que les scripts bloquent le rendu HTML. En les mettant tout à la fin, on garantit que la page s'affiche **avant** que le navigateur télécharge et exécute le JavaScript. L'utilisateur voit le contenu plus vite, même sur une connexion lente.

## admin_head.php et admin_foot.php : la variante back-office

Pour le dashboard administrateur et restaurateur, j'utilise un layout complètement différent basé sur le template **SB Admin v7** (gratuit, Start Bootstrap). La structure est plus dense parce que `admin_head.php` contient à la fois le `<head>` HTML **et** la topbar admin **et** la sidebar de navigation.

Les différences principales avec le layout public :

- **Pas de séparation head/header** : tout est dans `admin_head.php`, qui s'arrête au milieu du `<main>` pour laisser la vue se rendre
- **Beaucoup moins de CSS chargés** : juste `styles.css` de SB Admin (qui contient déjà Bootstrap 5.2.3 packagé), `fontawesome.all.min.css`, le CSS des datatables, et mon `responsive-fixes.css`
- **Une sidebar** au lieu d'une navbar horizontale, avec des liens conditionnels selon la page courante grâce à `$current_mod = $_GET['mod'] ?? ''` détecté en haut du fichier
- **Un footer minimaliste** dans `admin_foot.php` : juste une mention copyright et deux liens, suivi des scripts JS (Bootstrap bundle depuis CDN, scripts.js de SB Admin, simple-datatables)

Cette approche en double layout me permet de garder chaque template isolé : si je veux changer le design admin, je n'ai pas à craindre de casser le front public, et vice versa.

## En résumé

Mon système de partials repose sur **trois principes** :

**1. Une seule source pour ce qui se répète.** Quand je change le menu, je le change dans `header.php`, et ça se propage à toutes les 20 et quelques pages publiques. Pareil pour le footer, les scripts, les CSS.

**2. Un contrôle centralisé dans `index.php`.** C'est le front controller qui décide quels partials inclure, en fonction du type de page (publique ou admin). Les vues n'ont aucun `require_once` ni `include` — elles sont du HTML pur, ce qui les rend lisibles et faciles à maintenir.

**3. Des trappes d'extension pour les cas particuliers.** Les variables `$page_title` et `$custom_js` permettent à n'importe quelle vue d'injecter du contenu dans le head ou en fin de body sans toucher aux partials. C'est ce qui me permet, par exemple, de charger SortableJS uniquement sur la page `gestion-carte`, sans le coller à toutes les pages du site.

C'est une architecture simple, sans framework, mais qui suit la même philosophie que les layouts d'un Laravel ou d'un Symfony : isoler ce qui se répète, centraliser la composition, et permettre l'extension là où c'est nécessaire.
