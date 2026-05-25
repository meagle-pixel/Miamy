# Fiche oral — Le bouton "Dispo / Indispo" en AJAX

**Page concernée :** `views/gestion-carte.php` (tableau de bord du restaurateur)
**Fonctionnalité :** basculer un plat en "indisponible" sans recharger la page

---

## 1. Le contexte (à dire en intro)

> « Sur cette page, un restaurateur voit tous les plats de son restaurant. Pour signaler qu'un plat est en rupture, il clique sur un bouton. Comme un restaurateur peut avoir besoin de basculer plusieurs plats à la suite, j'ai voulu éviter de recharger la page à chaque clic. J'ai donc utilisé **AJAX**. »

---

## 2. C'est quoi AJAX ? (analogie restaurant)

> « AJAX, c'est une technique qui permet au navigateur de communiquer avec le serveur **en arrière-plan**, sans recharger la page. »

**Analogie à raconter au jury :**

> « Sans AJAX, c'est comme si à chaque commande au restaurant, on te demandait de sortir, de revenir et de te réinstaller à une nouvelle table. Avec AJAX, tu restes assis : le serveur va en cuisine, revient avec ton plat, et tu n'as rien eu à bouger. »

---

## 3. Les 4 étapes du toggle (à mémoriser)

| Étape | Qui agit | Ce qui se passe |
|------|---------|-----------------|
| 1 | L'utilisateur | Clique sur le bouton "Dispo" |
| 2 | Le JavaScript | Envoie une requête au serveur PHP avec `fetch()` |
| 3 | Le serveur PHP | Vérifie les droits, change la valeur en base, renvoie un JSON |
| 4 | Le JavaScript | Met à jour le bouton, le badge et le compteur **sans recharger** |

---

## 4. Le code en bref

**Côté JavaScript (dans `gestion-carte.php`) :**

```js
document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-toggle-dispo');
    if (!btn) return;

    btn.disabled = true;                      // anti double-clic

    const fd = new FormData();
    fd.append('id_plat', btn.dataset.platId);

    try {
        const response = await fetch(BASE_URL + '/toggle-disponible-plat', {
            method: 'POST',
            body: fd                          // contient l'ID du plat
        });
        const resp = await response.json();   // on lit la réponse JSON

        if (resp.success) {
            // on change le texte et la couleur du bouton
        } else {
            alert('Erreur');
        }
    } catch (err) {
        alert('Erreur réseau');
    } finally {
        btn.disabled = false;
    }
});
```

> **À retenir sur `async/await` :** `async` marque la fonction comme asynchrone, et `await` met le code en pause jusqu'à ce que la promesse soit tenue. Les erreurs sont attrapées avec un `try/catch` classique, et le `finally` s'exécute toujours (qu'il y ait une erreur ou non) — c'est là qu'on réactive le bouton.

**Côté PHP (dans `controllers/PlatController.php`, méthode `toggleDisponible()`) :**

```php
header('Content-Type: application/json');     // on annonce qu'on renvoie du JSON

if (!Auth::isRestaurateur()) {                // 1. l'utilisateur est-il connecté ?
    echo json_encode(['success' => false]);
    exit();
}

$plat = $platClass->getOwnedBy($id_plat, $id_restaurateur);
if (!$plat) {                                 // 2. ce plat lui appartient-il ?
    echo json_encode(['success' => false]);
    exit();
}

$ok = $platClass->toggleDisponible($id_plat); // 3. on bascule la valeur en base
echo json_encode(['success' => true, 'disponible' => ...]);
```

---

## 5. Questions pièges et réponses prêtes

**Q : Pourquoi AJAX et pas un formulaire classique ?**
R : Pour ne pas recharger toute la page. Un restaurateur qui bascule 10 plats perdrait son défilement et son contexte à chaque fois.

**Q : C'est quoi le JSON ?**
R : Un format de texte structuré, simple, qui ressemble à un objet JavaScript. Le serveur envoie du texte, le navigateur le transforme en objet utilisable avec `response.json()`.

**Q : Pourquoi tu vérifies les droits côté serveur si l'utilisateur est déjà sur la page ?**
R : Parce qu'on ne fait **jamais** confiance au navigateur. N'importe qui peut envoyer une requête à l'URL avec un outil comme Postman, en se faisant passer pour quelqu'un d'autre. Seul le serveur peut décider si l'action est autorisée.

**Q : Et si la requête échoue ?**
R : J'affiche une alerte JavaScript à l'utilisateur. C'est basique, on pourrait améliorer avec un message plus joli (un toast Bootstrap par exemple).

**Q : Pourquoi `fetch()` et pas `XMLHttpRequest` ?**
R : `fetch()` est la version moderne, plus simple à utiliser, basée sur les Promises. `XMLHttpRequest` est l'ancienne façon, qui marche encore mais qui est plus verbeuse.

**Q : C'est quoi `async` et `await` ?**
R : `async/await` est la syntaxe moderne pour gérer du code asynchrone. `async` marque une fonction comme asynchrone, `await` met le code en pause jusqu'à ce que la promesse soit tenue. C'est équivalent à utiliser `.then()`, mais plus lisible parce que le code se lit de haut en bas comme du code normal. Derrière, c'est toujours le mécanisme des Promises qui tourne.

**Q : Pourquoi `try/catch` ici ?**
R : Avec `async/await`, les erreurs (par exemple une erreur réseau quand `fetch` n'arrive pas à joindre le serveur) sont attrapées avec un `try/catch` classique, exactement comme du code synchrone. Sans `try/catch`, l'erreur "remonterait" sans qu'on puisse l'afficher proprement à l'utilisateur.

**Q : À quoi sert le `finally` ?**
R : Le bloc `finally` s'exécute **toujours**, qu'il y ait eu une erreur ou non. Ici je l'utilise pour réactiver le bouton (`btn.disabled = false`) dans tous les cas : si la requête a réussi, si elle a échoué, ou si le réseau est tombé. Sans ça, le bouton resterait grisé pour toujours en cas d'erreur.

**Q : Pourquoi tu désactives le bouton avec `btn.disabled = true` ?**
R : Pour empêcher l'utilisateur de cliquer plusieurs fois pendant que la requête est en cours, ce qui éviterait des requêtes en double.

---

## 6. Vocabulaire à connaître

- **AJAX** : Asynchronous JavaScript and XML — technique pour communiquer avec le serveur sans recharger la page.
- **`fetch()`** : la fonction JavaScript moderne pour envoyer une requête HTTP.
- **JSON** : format de texte structuré pour échanger des données entre le serveur et le navigateur.
- **Endpoint** : une URL côté serveur qui répond à une requête AJAX (ici : `/toggle-disponible-plat`).
- **Promise** : un objet JavaScript qui représente le résultat futur d'une opération asynchrone.
- **`async`** : mot-clé qui marque une fonction comme asynchrone, ce qui permet d'utiliser `await` à l'intérieur.
- **`await`** : met le code en pause jusqu'à ce que la promesse soit tenue, puis récupère le résultat. Équivalent moderne et plus lisible de `.then()`.
- **`try / catch / finally`** : structure pour gérer les erreurs. `try` contient le code à risque, `catch` attrape l'erreur, `finally` s'exécute toujours.
- **Controller** (MVC) : la classe PHP qui reçoit la requête, fait le travail, et renvoie la réponse.
- **Ownership check** : vérification que l'utilisateur connecté est bien propriétaire de la ressource qu'il essaie de modifier.

---

## 7. Phrase de conclusion

> « En résumé, le bouton Dispo/Indispo est un exemple typique d'AJAX : le navigateur envoie une mini-requête au serveur en arrière-plan, le serveur vérifie les droits et met à jour la base de données, puis renvoie une réponse JSON que le JavaScript utilise pour modifier l'affichage sans recharger la page. C'est ce qui rend l'interface fluide et réactive. »
