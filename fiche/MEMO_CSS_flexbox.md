# Mémo technique — CSS Flexbox

> Les propriétés clés pour aligner et répartir des éléments avec Flexbox.
> À savoir : Bootstrap repose entièrement sur Flexbox (la `.row` est un conteneur flex).

---

## Le principe de base

Flexbox fonctionne avec **deux niveaux** :
- **Le conteneur** (le parent) : on y met `display: flex;`. C'est lui qui porte les propriétés ci-dessous.
- **Les éléments** (les enfants directs) : ils se placent automatiquement selon les règles du conteneur.

Et **deux axes**, c'est LA notion à comprendre :
- **L'axe principal** (main axis) : sa direction est donnée par `flex-direction`.
- **L'axe secondaire** (cross axis) : il est **perpendiculaire** à l'axe principal.

Tout le reste dépend de ces deux axes.

```
flex-direction: row  →  axe principal = horizontal →,  axe secondaire = vertical ↓
flex-direction: column → axe principal = vertical ↓,  axe secondaire = horizontal →
```

---

## 1. `flex-direction` — la direction de l'axe principal

Sur le conteneur. Définit dans quel sens les éléments s'alignent.

| Valeur | Effet |
|---|---|
| `row` (défaut) | en ligne, de gauche à droite |
| `column` | en colonne, de haut en bas |
| `row-reverse` | en ligne, de droite à gauche |
| `column-reverse` | en colonne, de bas en haut |

```css
.conteneur { display: flex; flex-direction: row; }
```

C'est cette propriété qui **fixe les deux axes**. Tout le reste en découle.

---

## 2. `justify-content` — alignement sur l'AXE PRINCIPAL

Sur le conteneur. Répartit les éléments **le long de l'axe principal**.

| Valeur | Effet |
|---|---|
| `flex-start` (défaut) | collés au début |
| `flex-end` | collés à la fin |
| `center` | centrés |
| `space-between` | écartés au maximum, premier et dernier aux bords |
| `space-around` | espace égal autour de chaque élément |
| `space-evenly` | espace égal partout, y compris aux bords |

⚠️ Si `flex-direction: row`, `justify-content` agit **à l'horizontale**. Si `flex-direction: column`, il agit **à la verticale**. Ça suit toujours l'axe principal.

---

## 3. `align-items` — alignement sur l'AXE SECONDAIRE (une seule ligne)

Sur le conteneur. Aligne les éléments **perpendiculairement** à l'axe principal, à l'intérieur d'**une même ligne**.

| Valeur | Effet |
|---|---|
| `stretch` (défaut) | les éléments s'étirent pour remplir la hauteur |
| `flex-start` | alignés au début de l'axe secondaire |
| `flex-end` | alignés à la fin |
| `center` | centrés sur l'axe secondaire |
| `baseline` | alignés sur la ligne de base du texte |

Avec `flex-direction: row`, `align-items` agit **à la verticale** (centrer en hauteur, par exemple).

---

## 4. `align-content` — alignement de PLUSIEURS LIGNES sur l'axe secondaire

Sur le conteneur. Ne sert **que** quand il y a **plusieurs lignes**, donc avec `flex-wrap: wrap`. Elle répartit **les lignes entre elles** sur l'axe secondaire.

| Valeur | Effet |
|---|---|
| `flex-start` | lignes groupées au début |
| `flex-end` | lignes groupées à la fin |
| `center` | lignes groupées au centre |
| `space-between` | lignes écartées au maximum |
| `space-around` | espace égal autour de chaque ligne |
| `stretch` (défaut) | les lignes s'étirent pour remplir |

⚠️ **`align-content` n'a AUCUN effet s'il n'y a qu'une seule ligne.** Il faut `flex-wrap: wrap` et assez d'éléments pour qu'ils passent sur plusieurs lignes.

---

## La propriété qui débloque align-content : `flex-wrap`

| Valeur | Effet |
|---|---|
| `nowrap` (défaut) | tout sur une seule ligne (ça déborde ou ça compresse) |
| `wrap` | les éléments passent à la ligne suivante quand il manque de place |

---

## Le tableau récapitulatif (à retenir)

| Propriété | Agit sur | Concerne |
|---|---|---|
| `justify-content` | l'axe **principal** | la répartition des éléments dans le sens de `flex-direction` |
| `align-items` | l'axe **secondaire** | l'alignement des éléments **dans une ligne** |
| `align-content` | l'axe **secondaire** | l'alignement **entre plusieurs lignes** (avec `wrap`) |

**Moyen mnémotechnique :**
- **justify** = le sens de la direction (axe principal).
- **align-items** = perpendiculaire, une ligne.
- **align-content** = perpendiculaire, plusieurs lignes.

---

## Exemple complet

```css
.conteneur {
    display: flex;
    flex-direction: row;        /* éléments en ligne */
    flex-wrap: wrap;            /* autorise le passage à la ligne */
    justify-content: center;    /* centrés horizontalement */
    align-items: center;        /* centrés verticalement dans chaque ligne */
    align-content: space-between;/* lignes écartées si plusieurs lignes */
}
```

---

## Lien avec ton projet Miamy

- Bootstrap utilise Flexbox : la `.row` est `display: flex; flex-wrap: wrap;`. C'est ce qui fait que tes cartes (restaurants, catégories) passent à la ligne et s'empilent sur mobile.
- Dans ton `responsive-fixes.css` (BUG 8), tu utilises `flex-direction: column` pour empiler verticalement les liens de ton menu burger.

**Phrase pour l'oral, si on te questionne sur Flexbox :**
> Flexbox repose sur un conteneur et deux axes. `flex-direction` définit l'axe principal. `justify-content` répartit les éléments le long de cet axe principal, `align-items` les aligne sur l'axe perpendiculaire dans une ligne, et `align-content` gère l'alignement entre plusieurs lignes quand il y a un retour à la ligne.
