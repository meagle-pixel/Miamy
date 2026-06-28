# Fiche oral — Les horaires d'ouverture (front-end + back-end)

> Présentation A à Z, comme devant un jury. D'abord le front-end, puis le back-end.
> Vérifié dans le code réel : `views/details.php`, `views/liste-restaurants.php`, `controllers/RestaurantController.php`, `classes/class.horaires.php`.

## Vue d'ensemble

La fonctionnalité a **deux côtés** :
- **La gestion** (page `details.php`, côté restaurateur) : il renseigne ses horaires sur 7 jours et les enregistre.
- **L'affichage** (page `liste-restaurants.php`, côté visiteur) : un badge « Ouvert · 09:00 – 22:00 » ou « Fermé aujourd'hui » s'affiche sur chaque restaurant.

```
GESTION  : formulaire 7 jours (details.php) + JS → save-horaires → saveHoraires() → Horaires::save() → base
AFFICHAGE: liste() → getTodayForRestaurants() → la vue calcule le badge "Ouvert / Fermé"
```

---
---

# PARTIE 1 — LE FRONT-END

## 1. Le formulaire de gestion des horaires
**Fichier : `views/details.php`**

```php
<form method="POST" action="save-horaires">
    <input type="hidden" name="id_restaurant" value="<?= $id_restaurant ?>">

    <?php foreach (Horaires::$jours as $num => $label): ?>
        <?php $h = $horaires[$num]; ?>
        <tr class="horaire-row" data-jour="<?= $num ?>">
            <td><?= $label ?></td>

            <!-- case Ouvert/Fermé -->
            <input class="toggle-ouvert" type="checkbox"
                   name="horaires[<?= $num ?>][ouvert]"
                   <?= $h['ouvert'] ? 'checked' : '' ?>>

            <!-- heure d'ouverture -->
            <input type="time" class="heures-input"
                   name="horaires[<?= $num ?>][debut]"
                   value="<?= $h['debut'] ? substr($h['debut'], 0, 5) : '09:00' ?>"
                   <?= !$h['ouvert'] ? 'disabled' : '' ?>>

            <!-- heure de fermeture -->
            <input type="time" class="heures-input"
                   name="horaires[<?= $num ?>][fin]"
                   value="<?= $h['fin'] ? substr($h['fin'], 0, 5) : '22:00' ?>"
                   <?= !$h['ouvert'] ? 'disabled' : '' ?>>
        </tr>
    <?php endforeach; ?>

    <button type="submit">Enregistrer les horaires</button>
</form>
```

**Ce que je dirais au jury :**
> C'est un formulaire classique en POST, qui envoie vers la route save-horaires. Je boucle sur les 7 jours de la semaine, et pour chaque jour j'affiche une case « Ouvert » et deux champs heure, l'ouverture et la fermeture.
>
> Le point intéressant, c'est le nom des champs : `name="horaires[0][ouvert]"`, `horaires[0][debut]`, etc. Cette écriture avec des crochets fait qu'en PHP, je vais récupérer un **tableau organisé par jour**, et pas 21 champs séparés. Le champ caché en haut transporte l'id du restaurant.
>
> Les champs sont aussi **pré-remplis** : les valeurs viennent de la variable `$horaires` que mon contrôleur a préparée. Et si un jour est fermé, ses deux champs heure sont `disabled`, donc grisés.

## 2. Le petit JavaScript : désactiver les heures d'un jour fermé
**Fichier : `views/details.php`**

```js
document.querySelectorAll('.toggle-ouvert').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const row    = this.closest('.horaire-row');
        const inputs = row.querySelectorAll('.heures-input');
        inputs.forEach(function (input) {
            input.disabled = !checkbox.checked;
        });
    });
});
```

**Ce que je dirais au jury :**
> Pour éviter qu'on saisisse des heures sur un jour fermé, j'ai ajouté une dizaine de lignes de JavaScript. Je pose un écouteur sur chaque case « Ouvert ». Quand on coche ou décoche une case, je remonte à la ligne du jour concerné, et je désactive ou réactive les deux champs heure de cette ligne.
>
> La ligne clé, c'est `input.disabled = !checkbox.checked` : elle gère les deux sens d'un coup, en mettant l'état « désactivé » à l'inverse de l'état de la case. Si la case est décochée, les champs se grisent ; si on la recoche, ils redeviennent actifs.

## 3. L'affichage du statut côté visiteur
**Fichier : `views/liste-restaurants.php`**

```php
$horaire = $horairesAujourdhui[$resto['id_restaurant']] ?? null;

if ($horaire === null) {
    $statutHtml = '';                                  // pas d'info → rien
} elseif (!$horaire['ouvert']) {
    $statutHtml = '<span class="badge bg-danger">Fermé aujourd\'hui</span>';
} else {
    $debut = substr($horaire['debut'], 0, 5);          // "09:00:00" → "09:00"
    $fin   = substr($horaire['fin'],   0, 5);
    $statutHtml = '<span class="badge bg-success">Ouvert · ' . $debut . ' – ' . $fin . '</span>';
}
```
puis plus bas :
```php
<?php if ($statutHtml): ?>
    <p class="mb-1"><?= $statutHtml ?></p>
<?php endif; ?>
```

**Ce que je dirais au jury :**
> Sur la liste des restaurants, j'affiche pour chacun un badge avec son statut du jour. Pour chaque restaurant, je récupère ses horaires d'aujourd'hui, préparés par le contrôleur. Ensuite je distingue trois cas : si je n'ai pas d'information, je n'affiche rien ; si le restaurant est fermé aujourd'hui, j'affiche un badge rouge « Fermé » ; sinon, un badge vert « Ouvert » avec les heures. Le `substr` sert juste à raccourcir l'heure, de 09:00:00 à 09:00.

---
---

# PARTIE 2 — LE BACK-END

## 4. Charger les horaires du jour pour l'affichage
**Fichier : `controllers/RestaurantController.php` (méthode `liste`)**

```php
public function liste()
{
    $restoClass  = new Restaurant();
    $restaurants = $restoClass->listRestaurants(activeOnly: false);

    $horairesAujourdhui = [];
    if (!empty($restaurants)) {
        $ids                = array_map(fn($r) => (int)$r['id_restaurant'], $restaurants);
        $horairesClass      = new Horaires();
        $horairesAujourdhui = $horairesClass->getTodayForRestaurants($ids);
    }

    return compact('restaurants', 'horairesAujourdhui');
}
```

**Ce que je dirais au jury :**
> Pour la liste, je récupère d'abord tous les restaurants. Puis je récupère, en une seule fois, les horaires du jour de tous ces restaurants, avec `getTodayForRestaurants`. Je passe la liste des id, et je récupère un tableau indexé par restaurant. Je renvoie le tout à la vue.

**La méthode dans `classes/class.horaires.php` :**
```php
public function getTodayForRestaurants(array $ids): array
{
    $ids    = array_map('intval', $ids);
    $idsCsv = implode(',', $ids);
    $jour   = (int)date('N') - 1;       // numéro du jour d'aujourd'hui (0=Lundi)
    $stmt   = $this->pdo->query(
        "SELECT * FROM `horaires` WHERE `id_restaurant` IN ($idsCsv) AND `jour` = $jour"
    );
    $result = [];
    foreach ($stmt->fetchAll() as $row) {
        $result[(int)$row['id_restaurant']] = $row;
    }
    return $result;
}
```
> Ici je calcule le numéro du jour d'aujourd'hui, et je vais chercher en une requête les horaires de ce jour pour tous les restaurants. Je sécurise les id en les forçant en entiers avant de les mettre dans la requête.

## 5. Charger les 7 jours pour la page de gestion
**Fichier : `controllers/RestaurantController.php` (méthode `details`)**

```php
$horairesClass = new Horaires();
$horaires      = $horairesClass->getByRestaurant($id_restaurant);

// messages après redirection depuis save-horaires
$horaires_success = isset($_GET['horaires']) && $_GET['horaires'] === 'ok';
$horaires_error   = isset($_GET['horaires']) && $_GET['horaires'] === 'error';
```

**La méthode `getByRestaurant` :**
```php
public function getByRestaurant(int $id_restaurant): array {
    $stmt = $this->pdo->prepare("SELECT * FROM horaires WHERE id_restaurant = :id ORDER BY jour ASC");
    $stmt->execute(['id' => $id_restaurant]);
    // ... on indexe par jour, et on complète les jours manquants avec des valeurs par défaut
}
```

**Ce que je dirais au jury :**
> Pour la page de gestion, je charge les 7 jours du restaurant avec `getByRestaurant`. Et si un jour n'existe pas encore en base, je le complète avec des valeurs par défaut, ouvert de 9h à 22h, pour que le formulaire ait toujours ses 7 lignes. Je lis aussi un paramètre dans l'URL pour afficher un message de succès ou d'erreur après l'enregistrement.

## 6. Enregistrer les horaires (le contrôleur)
**Fichier : `controllers/RestaurantController.php` (méthode `saveHoraires`)**

```php
public function saveHoraires()
{
    // CONTRÔLE 1 : connecté + bon rôle
    if (!isset($_SESSION['connected']) || $_SESSION['connected'] !== true || $_SESSION['user']['profil'] > 2) {
        header('Location: ' . APP_URL . '/connexion'); exit();
    }

    $id_restaurant   = isset($_POST['id_restaurant']) ? (int)$_POST['id_restaurant'] : 0;
    $id_restaurateur = (int)$_SESSION['user']['profil_id'];

    // CONTRÔLE 2 : id valide
    if (!$id_restaurant) {
        header('Location: ' . APP_URL . '/mon-compte-restaurateur'); exit();
    }

    // CONTRÔLE 3 : ownership (le restaurant appartient au restaurateur)
    $restoClass = new Restaurant();
    if (!$restoClass->isOwnedBy($id_restaurant, $id_restaurateur)) {
        header('Location: ' . APP_URL . '/mon-compte-restaurateur'); exit();
    }

    // Enregistrement
    $data          = $_POST['horaires'] ?? [];
    $horairesClass = new Horaires();
    $ok            = $horairesClass->save($id_restaurant, $data);

    $status = $ok ? 'ok' : 'error';
    header('Location: ' . APP_URL . '/details?id=' . $id_restaurant . '&horaires=' . $status);
    exit();
}
```

**Ce que je dirais au jury :**
> Quand le formulaire est soumis, ma méthode `saveHoraires` fait trois contrôles avant d'écrire : que l'utilisateur est connecté avec le bon rôle, que l'id du restaurant est valide, et surtout qu'il en est bien le propriétaire, avec le contrôle d'ownership. Je récupère ensuite le tableau `horaires` envoyé en POST, je le passe à ma classe Horaires, et je redirige vers la page de détails avec un indicateur de succès ou d'erreur.

## 7. L'écriture en base (la classe Horaires)
**Fichier : `classes/class.horaires.php` (méthode `save`)**

```php
public function save(int $id_restaurant, array $data): bool {
    $stmt = $this->pdo->prepare(
        "INSERT INTO horaires (id_restaurant, jour, ouvert, debut, fin)
         VALUES (:id_restaurant, :jour, :ouvert, :debut, :fin)
         ON DUPLICATE KEY UPDATE
             ouvert = VALUES(ouvert), debut = VALUES(debut), fin = VALUES(fin)"
    );

    $this->pdo->beginTransaction();
    try {
        foreach (self::$jours as $num => $label) {
            $ouvert = isset($data[$num]['ouvert']) ? 1 : 0;     // case cochée ou non
            $debut  = $ouvert ? ($data[$num]['debut'] ?? null) : null;
            $fin    = $ouvert ? ($data[$num]['fin']   ?? null) : null;

            $stmt->execute([
                'id_restaurant' => $id_restaurant,
                'jour'          => $num,
                'ouvert'        => $ouvert,
                'debut'         => $debut,
                'fin'           => $fin,
            ]);
        }
        $this->pdo->commit();
        return true;
    } catch (Exception $e) {
        $this->pdo->rollBack();
        return false;
    }
}
```

**Ce que je dirais au jury :**
> La méthode `save` enregistre les 7 jours d'un coup. J'utilise une requête préparée avec `INSERT ... ON DUPLICATE KEY UPDATE` : si la ligne du jour existe déjà, elle est mise à jour, sinon elle est créée. Ça m'évite de gérer séparément la création et la modification.
>
> Pour chaque jour, je regarde si la case « ouvert » a été cochée : si oui je mets 1 et je garde les heures, sinon je mets 0 et les heures à null. Détail important : si une case n'est pas cochée, elle n'est pas envoyée par le navigateur, donc je teste sa présence avec `isset`.
>
> Enfin, tout est dans une **transaction** : soit les 7 jours sont enregistrés ensemble, soit, en cas d'erreur, j'annule tout avec un rollback. Ça garantit que je ne me retrouve jamais avec des horaires à moitié enregistrés.

---
---

# ❓ Questions / Réponses jury

**Q — Comment récupères-tu les horaires côté serveur, vu qu'il y a 7 jours et plein de champs ?**
R — Grâce au nom des champs en `horaires[jour][ouvert]`. PHP reconstruit automatiquement un tableau organisé par jour dans `$_POST['horaires']`, que je parcours ensuite.

**Q — Que se passe-t-il si un jour est fermé ?**
R — La case n'est pas cochée, donc le navigateur ne l'envoie pas. Je le détecte avec `isset`, je mets ouvert à 0 et les heures à null.

**Q — Pourquoi une transaction dans save ?**
R — Pour que les 7 jours soient enregistrés tout ou rien. En cas d'erreur, je fais un rollback, ce qui évite des horaires incohérents.

**Q — INSERT ... ON DUPLICATE KEY UPDATE, ça sert à quoi ?**
R — À gérer création et mise à jour avec une seule requête : si le jour existe déjà pour ce restaurant, il est mis à jour, sinon il est créé.

**Q — Comment empêches-tu un restaurateur de modifier les horaires d'un autre ?**
R — Avec le contrôle d'ownership dans saveHoraires : je vérifie que le restaurant appartient bien au restaurateur connecté avant d'enregistrer.

# Phrase de synthèse

> Côté gestion, le restaurateur remplit un formulaire de 7 jours, avec un petit JavaScript qui désactive les heures d'un jour fermé. À l'envoi, mon contrôleur vérifie les droits et l'ownership, puis ma classe Horaires enregistre les 7 jours dans une transaction, avec un INSERT ON DUPLICATE KEY UPDATE. Côté visiteur, je charge les horaires du jour de tous les restaurants en une requête, et j'affiche un badge Ouvert ou Fermé sur chacun.
