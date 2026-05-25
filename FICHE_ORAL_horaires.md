# Fiche oral — Le toggle des horaires d'ouverture

**Page concernée :** `views/details.php` (page Détails d'un restaurant, vue restaurateur)
**Fonctionnalité :** désactiver automatiquement les champs heure quand on décoche "Ouvert" pour un jour

---

## 1. Le contexte (à dire en intro)

> « Sur la page Détails d'un restaurant, le restaurateur peut renseigner ses horaires d'ouverture pour les 7 jours de la semaine. Chaque ligne du tableau a une case "Ouvert", et deux champs heure (début et fin). Pour éviter à l'utilisateur de saisir des horaires sur un jour où le restaurant est fermé, j'ai utilisé une dizaine de lignes de **JavaScript vanilla** qui désactivent automatiquement les inputs heure quand on décoche la case. »

---

## 2. C'est quoi le principe ? (analogie)

> « C'est comme un interrupteur de lumière dans une pièce : si l'interrupteur est éteint, ça ne sert à rien d'avoir une variateur d'intensité réglable. Le JavaScript ici fait exactement ça : quand on éteint l'interrupteur (la case "Ouvert"), il bloque automatiquement les boutons de réglage (les champs heure). »

**À retenir :** ici, **aucun appel serveur**, aucune base de données. C'est purement du JavaScript qui réagit aux clics, dans le navigateur, pour améliorer le confort de l'utilisateur.

---

## 3. Les 4 étapes du toggle (à mémoriser)

| Étape | Qui agit | Ce qui se passe |
|------|---------|-----------------|
| 1 | Au chargement de la page | Le JS pose un écouteur sur chacune des 7 cases "Ouvert" |
| 2 | L'utilisateur | Décoche (ou recoche) la case d'un jour |
| 3 | L'événement `change` | Se déclenche, le JS remonte au `<tr>` de cette ligne |
| 4 | Le JS | Active ou désactive les 2 champs heure de cette ligne uniquement |

---

## 4. Le code en bref

```js
<script>
document.querySelectorAll('.toggle-ouvert').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const row    = this.closest('.horaire-row');           // remonte au <tr>
        const inputs = row.querySelectorAll('.heures-input');  // les 2 inputs heure
        inputs.forEach(function (input) {
            input.disabled = !checkbox.checked;                // active OU désactive
        });
    });
});
</script>
```

> **La ligne clé :** `input.disabled = !checkbox.checked`
> Une seule ligne gère les **deux sens** du toggle, parce qu'on synchronise directement la propriété `disabled` avec l'inverse de l'état de la case. Si la case est cochée → `disabled = false` (input actif). Si elle est décochée → `disabled = true` (input grisé).

**Côté HTML (préparé par PHP) :**
Le tableau a 7 lignes, chacune avec :
- Une case `<input type="checkbox" class="toggle-ouvert">`
- Deux `<input type="time" class="heures-input">` (début et fin)
- Toute la ligne est dans un `<tr class="horaire-row">`

Les 3 classes (`toggle-ouvert`, `horaire-row`, `heures-input`) sont les **points d'accroche** que le JavaScript utilise pour naviguer dans le DOM.

---

## 5. Questions pièges et réponses prêtes

**Q : Pourquoi pas un appel AJAX comme dans le toggle Dispo ?**
R : Parce qu'ici on ne modifie aucune donnée tant que l'utilisateur n'a pas cliqué sur "Enregistrer". C'est juste de l'aide à la saisie. Tout est sauvegardé d'un coup à la soumission du formulaire, en POST classique, vers le contrôleur `RestaurantController::saveHoraires()`.

**Q : Pourquoi utiliser l'événement `change` et pas `click` ?**
R : Parce que `change` se déclenche aussi quand l'utilisateur coche la case au clavier (avec la barre espace). C'est plus universel et plus accessible.

**Q : C'est quoi `closest()` ?**
R : `closest()` part d'un élément HTML et **remonte** dans ses parents jusqu'à trouver le premier qui correspond au sélecteur passé. Ici, à partir d'une case, je remonte jusqu'au `<tr>` qui contient toute la ligne, pour pouvoir ensuite chercher les inputs heure de cette ligne uniquement.

**Q : Pourquoi pas `document.querySelectorAll('.heures-input')` ?**
R : Parce que ça sélectionnerait **les 14 inputs de tout le tableau** (2 par ligne × 7 lignes), et je désactiverais tout le tableau d'un coup. En partant de la ligne courante avec `row.querySelectorAll(...)`, je limite la recherche à 2 inputs.

**Q : C'est quoi le `!` dans `!checkbox.checked` ?**
R : C'est l'opérateur logique **NON** (négation booléenne). Si `checked` vaut `true`, alors `!checked` vaut `false`, et vice-versa. Ça me permet d'inverser : case cochée → input actif, case décochée → input désactivé, en une seule ligne.

**Q : Et au chargement de la page, comment ça marche ?**
R : Au chargement, ce n'est pas le JS qui désactive les inputs : c'est PHP qui imprime directement l'attribut `disabled` dans le HTML si le jour est marqué fermé en base. Le JS ne fait que **maintenir cet état** quand l'utilisateur change la case ensuite.

**Q : Pourquoi avoir mis des classes sur les éléments (`toggle-ouvert`, `horaire-row`, `heures-input`) ?**
R : Pour servir de **points d'accroche au JavaScript**. Ces classes ne servent pas au style ici, elles servent uniquement à ce que le JS puisse retrouver les bons éléments dans le DOM. C'est une convention courante : préfixer ces classes par `js-` (`js-toggle-ouvert`) est même une bonne pratique pour bien distinguer style et comportement.

---

## 6. Vocabulaire à connaître

- **DOM** : Document Object Model — la représentation en mémoire du HTML que JavaScript peut manipuler.
- **`querySelectorAll`** : sélectionne **tous** les éléments correspondant à un sélecteur CSS. Retourne une `NodeList` (liste).
- **`addEventListener`** : attache une fonction à un événement (clic, changement, scroll, etc.) sur un élément.
- **Événement `change`** : se déclenche quand la valeur d'un champ (input, checkbox, select) change.
- **`this`** dans un écouteur : fait référence à l'élément qui a déclenché l'événement.
- **`closest(selector)`** : remonte dans les parents jusqu'à trouver le premier qui correspond au sélecteur.
- **`disabled`** : attribut HTML qui rend un input non cliquable et non saisissable (et grisé visuellement).
- **`!` (négation)** : opérateur qui inverse un booléen — `!true = false`, `!false = true`.
- **JavaScript vanilla** : du JavaScript pur, **sans** librairie (pas de jQuery, pas de React).

---

## 7. Phrase de conclusion

> « En résumé, le toggle des horaires d'ouverture est un exemple typique d'**amélioration UX en JavaScript vanilla** : une dizaine de lignes suffisent pour empêcher l'utilisateur de saisir des données incohérentes. La logique est simple : au chargement, le JS pose un écouteur sur chaque case. Quand l'utilisateur coche ou décoche, le JS remonte au `<tr>` parent, récupère les inputs heure de cette ligne, et synchronise leur état `disabled` avec l'inverse de la case. C'est du DOM pur, sans aucune librairie, et sans aucun appel serveur. »
