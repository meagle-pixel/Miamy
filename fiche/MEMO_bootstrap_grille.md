# Mémo — Les bases de Bootstrap (la grille et les colonnes)

> Comment fonctionne la grille responsive de Bootstrap, expliquée avec les vrais exemples de Miamy.

---

## L'idée de base : une grille de 12 colonnes

Bootstrap découpe la largeur de la page en **12 colonnes** invisibles. Chaque bloc dit **combien de ces 12 colonnes** il occupe. Pour remplir une ligne, les blocs doivent totaliser **12**.

La structure est toujours la même, sur **3 niveaux** :

```html
<div class="container">      <!-- centre le contenu et fixe une largeur max -->
    <div class="row">        <!-- une ligne (c'est un conteneur flex) -->
        <div class="col-6">  <!-- une colonne : ici 6 sur 12, donc la moitié -->
        <div class="col-6">  <!-- 6 + 6 = 12, donc deux blocs côte à côte -->
    </div>
</div>
```

- **`container`** : centre le contenu et lui donne une largeur maximale.
- **`row`** : une ligne. Techniquement, c'est un conteneur Flexbox (`display: flex`).
- **`col-...`** : une colonne, qui occupe un certain nombre de douzièmes.

Quelques exemples de répartition :
- `col-6` + `col-6` → 2 blocs de 50 %.
- `col-4` + `col-4` + `col-4` → 3 blocs égaux (un tiers chacun).
- `col-3` x 4 → 4 blocs égaux (un quart chacun).

---

## Le responsive : les points de rupture (breakpoints)

C'est là que c'est puissant. On peut dire à un bloc d'occuper **un nombre de colonnes différent selon la taille de l'écran**, grâce à un suffixe :

| Suffixe | S'applique à partir de | Type d'écran |
|---|---|---|
| (aucun) | 0 px | mobile (très petit) |
| `sm` | ≥ 576 px | grand mobile |
| `md` | ≥ 768 px | tablette |
| `lg` | ≥ 992 px | ordinateur |
| `xl` | ≥ 1200 px | grand écran |

Règle importante : un suffixe s'applique **à cette taille ET au-dessus**. Par exemple `col-md-6` veut dire « 6 colonnes à partir de 768 px ». En dessous de 768 px, c'est une autre règle (plus petite) qui prend le relais ; et s'il n'y en a pas, le bloc passe en pleine largeur.

---

## Exemple Miamy n°1 — les cartes de restaurants

Dans `liste-restaurants.php` :

```html
<div class="col-lg-4 col-md-6 col-sm-6 col-12 mb-4">
```

On lit ça de droite à gauche (du plus petit écran au plus grand) :

| Classe | Écran | Colonnes | Cartes par ligne |
|---|---|---|---|
| `col-12` | mobile | 12/12 | **1** (pleine largeur, empilées) |
| `col-sm-6` | ≥ 576 px | 6/12 | **2** |
| `col-md-6` | ≥ 768 px | 6/12 | **2** |
| `col-lg-4` | ≥ 992 px | 4/12 | **3** |

Donc : 1 carte par ligne sur téléphone, 2 sur tablette, 3 sur ordinateur. C'est **ça** qui fait que tes restaurants s'empilent sur mobile et s'alignent sur 3 colonnes en grand écran. (`mb-4`, c'est juste une marge en bas, voir plus loin.)

---

## Exemple Miamy n°2 — les catégories de l'accueil

Dans `home.php` :

```html
<div class="col-lg-3 col-md-4 col-sm-6">
```

- `col-sm-6` → 2 par ligne sur petit écran.
- `col-md-4` → 3 par ligne sur tablette.
- `col-lg-3` → 4 par ligne sur ordinateur.
- En dessous de 576 px (aucune classe plus petite) → pleine largeur, donc 1 par ligne.

---

## Exemple Miamy n°3 — le footer (colonnes inégales)

Dans `footer.php` :

```html
<div class="col-lg-9 ...">   <!-- "Besoin d'aide ?" : 9 colonnes -->
<div class="col-lg-3 ...">   <!-- "Liens rapides" : 3 colonnes -->
```

`9 + 3 = 12`, donc sur grand écran les deux blocs se partagent la ligne, mais de façon **inégale** (un large, un étroit). Sur mobile, chacun passe en pleine largeur et ils s'empilent.

---

## Les classes utilitaires courantes (bonus)

Tu en utilises beaucoup dans Miamy. Les plus fréquentes :

| Classe | Effet |
|---|---|
| `mb-4`, `mt-3`… | marge basse / haute (le chiffre = la taille, 0 à 5) |
| `p-3`, `px-2`… | padding (espace intérieur) |
| `text-center` | texte centré |
| `d-flex` | active Flexbox sur l'élément |
| `text-muted` | texte grisé |

---

## Phrase pour l'oral

> Bootstrap découpe la page en 12 colonnes. Chaque bloc indique combien de colonnes il occupe, et la somme par ligne fait 12. Le système est responsive grâce aux suffixes comme `md` ou `lg` : un même bloc peut occuper un nombre de colonnes différent selon la taille de l'écran. Par exemple, mes cartes de restaurants sont en `col-lg-4 col-md-6 col-12` : 3 par ligne sur ordinateur, 2 sur tablette, et 1 sur mobile. Le tout sans écrire la moindre media query, parce que Bootstrap s'appuie sur Flexbox en interne.
