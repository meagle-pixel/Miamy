## Le routing en base de données : la table `pages`

Au moment où j'ai mis en place le Front Controller, je me suis posé une question pratique : où stocker la **liste des routes** de mon application ? Autrement dit, comment faire le lien entre une URL comme `/Miamy/accueil` et le fichier de vue à inclure (`views/accueil.php`) ainsi que le titre à afficher dans l'onglet du navigateur ?

La solution la plus courante en PHP, c'est de **hardcoder** cette table dans un tableau associatif au début de `index.php`. Quelque chose comme :

```php
$routes = [
    'accueil'   => ['file' => 'views/accueil.php',   'title' => 'Accueil'],
    'connexion' => ['file' => 'views/login.php',     'title' => 'Connexion'],
    'dashboard' => ['file' => 'views/admin/dashboard.php', 'title' => 'Dashboard - Miamy'],
    // ... etc pour chaque page
];
```

Ça fonctionne, mais ça veut dire que **chaque ajout, suppression ou renommage de page nécessite de modifier le code**. Pour un site avec 20+ pages comme Miamy, ça devient vite encombrant et casse le principe "code = comportement, données = contenu".

J'ai choisi une approche différente : **stocker la table de routage en base de données**, dans une table `pages`. Cette table a trois colonnes utiles :

| Colonne | Type | Rôle |
|---|---|---|
| `mod` | VARCHAR | Le slug de l'URL (le `?mod=xxx` du paramètre, ou la partie après `/Miamy/` grâce au `.htaccess`) |
| `nom` | VARCHAR | Le titre de la page, affiché dans `<title>` |
| `url` | VARCHAR | Le chemin du fichier de vue à inclure (`views/accueil.php`, `views/admin/dashboard.php`, etc.) |

Concrètement, voici un extrait de ce qu'elle contient :

| id | mod | nom | url |
|---|---|---|---|
| 1 | accueil | Accueil | views/accueil.php |
| 2 | connexion | Connexion | views/login.php |
| 3 | dashboard | Dashboard - Miamy | views/admin/dashboard.php |
| 4 | gestion-carte | Gestion de la carte | views/gestion-carte.php |

Et voici comment `index.php` l'utilise, juste après avoir résolu le contrôleur et préparé les variables de la vue :

```php
$pageModel = new Page();
$page_content = $pageModel->getByMod($page);

if (!empty($page_content['nom']) && !empty($page_content['url'])) {
    $page_title = $page_content['nom'];
    $page_url   = $page_content['url'];
} else {
    $page_title = 'Page introuvable';
    $page_url   = 'views/404.php';
}
```

Le `Page` est un petit modèle PHP qui contient une méthode `getByMod($mod)` faisant une requête PDO préparée :

```sql
SELECT * FROM pages WHERE mod = :mod
```

Si le slug existe en base, on récupère le titre et le chemin de vue. Sinon, on tombe automatiquement sur la page 404. Le titre `$page_title` est ensuite injecté dans le `<head>` HTML par le partial `head.php`, et `$page_url` est utilisé pour faire un `include($page_url)` qui rend la bonne vue.

### Pourquoi ce choix

Trois avantages concrets justifient d'avoir mis le routing en base plutôt que dans le code :

D'abord, **ajouter une nouvelle page se fait sans toucher au code PHP**. Si demain je veux ajouter une page `/Miamy/a-propos-du-fondateur`, je fais juste un `INSERT INTO pages` avec les bonnes valeurs et la page est instantanément routée. Pas besoin de redéployer le code.

Ensuite, **les titres SEO sont gérés au même endroit**. Au lieu d'avoir le titre `<title>` codé en dur dans chaque vue, il vient de la base. C'est cohérent, modifiable depuis n'importe quel client SQL, et ça centralise une donnée importante pour le référencement.

Enfin, **ça décorrèle complètement le slug d'URL du chemin de fichier**. Le slug `gestion-carte` pointe vers `views/gestion-carte.php`, mais demain je peux changer le chemin du fichier sans casser le lien public. C'est un niveau d'indirection utile.

### Pourquoi cette table n'apparaît pas dans le MCD métier

Je n'ai volontairement pas mis la table `pages` dans le Modèle Conceptuel de Données. Le MCD modélise le **domaine métier** de Miamy — les restaurants, les plats, les clients, les commandes... — pas les détails techniques de l'implémentation. La table `pages` est un **paramétrage du Front Controller**, pas une entité métier qui a une réalité dans le monde réel. C'est la même logique pour laquelle on ne modélise pas non plus les tables de sessions PHP ou les caches : ce sont des outils techniques, pas des données métier.

Je la mentionne donc ici, dans la partie architecture, parce que c'est le bon endroit pour parler de routing et d'infrastructure applicative.
