# Plan du PowerPoint — Oral Miamy (DWWM)

Cible : ~62 diapositives, 35 minutes, démo live comprise (~34 s/diapo en moyenne).
Conseil de rythme : garde les diapos « visuel + 1 idée » à 20-30 s, et concentre le temps sur le back-end/sécurité et la démo. Si tu parles beaucoup, fusionne pour descendre vers 50 diapos.

Pour chaque diapo : la capture à mettre + une description courte (ce que la diapo montre / ce que tu dis).

---

## Ouverture

**1. Page de titre**
Capture : logo Miamy + visuel d'un plat.
Description : titre « Miamy — la carte interactive de votre restaurant », ton nom, DWWM, date.

**2. Qui suis-je**
Capture : ta photo ou une icône neutre.
Description : ta formation DWWM et ton parcours en une phrase.

**3. Sommaire**
Capture : aucune (liste des parties).
Description : les grandes étapes : projet, conception, base de données, développement, qualité, déploiement.

## 1. Contexte & projet

**4. Le contexte**
Capture : logo de l'entreprise / du tuteur, ou photo poste de travail.
Description : reprise d'un projet existant, en stage, encadré par Yann.

**5. Le problème**
Capture : logos Uber Eats / Deliveroo avec une mention « 20 à 30 % de commission ».
Description : les plateformes ponctionnent la marge des petits restaurants.

**6. La solution Miamy**
Capture : page d'accueil de Miamy.
Description : une vitrine numérique que le restaurateur possède et contrôle.

**7. Cahier des charges**
Capture : schéma deux colonnes (objectif métier / objectif pédagogique).
Description : vendre sans intermédiaire + apprendre en développant sans framework.

**8. Périmètre fonctionnel**
Capture : schéma des 3 espaces (visiteur / restaurateur / admin).
Description : chaque rôle a ses droits et ses fonctionnalités.

**9. Contraintes techniques**
Capture : l'image « stack technique » (logos PHP, MySQL, Docker, etc.).
Description : développement à la main, environnement Docker, versioning Git/GitHub.

## 2. Conception & gestion de projet

**10. Gestion de projet**
Capture : capture de ta todolist.html.
Description : point quotidien avec le tuteur, todolist, approche user stories.

**11. Versioning Git/GitHub**
Capture : capture du dépôt GitHub (historique de commits).
Description : commits réguliers, sauvegarde distante, historique du projet.

**12. Public visé**
Capture : deux personas (restaurateur / client).
Description : restaurateurs indépendants et consommateurs de proximité.

**13. Charte graphique**
Capture : nuancier orange/vert + logo + typo.
Description : l'identité visuelle définie avant de coder.

**14. Wireframes desktop**
Capture : wireframe(s) version desktop.
Description : maquettage de l'ergonomie avant le code.

**15. Enchaînement des écrans**
Capture : schéma d'enchaînement des maquettes (Figma ou à la main).
Description : comment l'utilisateur navigue d'un écran à l'autre (CP2).

## 3. Base de données (Merise)

**16. La méthode Merise**
Capture : logo / schéma des 3 niveaux (MCD → MLD → MPD).
Description : conception du conceptuel au physique, avec l'outil Looping.

**17. MCD**
Capture : ton MCD (Looping).
Description : entités, propriétés et cardinalités du projet.

**18. MLD**
Capture : ton MLD.
Description : passage aux tables et introduction des clés étrangères.

**19. MPD**
Capture : ton MPD.
Description : types SQL, contraintes, et surtout les ON DELETE CASCADE.

**20. Le DDL**
Capture : capture d'un `CREATE TABLE` (ex. `plats` ou `utilisateurs`).
Description : traduction du modèle en SQL réel.

**21. Le modèle relationnel utilisateurs**
Capture : schéma utilisateurs ↔ administrateurs/restaurateurs/clients.
Description : un compte de connexion relié à une fiche métier (1:1).

## 4. Architecture

**22. Le choix sans framework**
Capture : schéma « à la main vs Laravel/Symfony ».
Description : choix pédagogique pour comprendre le cycle d'une requête.

**23. Le pattern MVC**
Capture : schéma Modèle / Vue / Contrôleur.
Description : séparation des responsabilités pour un code maintenable.

**24. Le Front Controller**
Capture : capture du haut de `index.php`.
Description : toutes les requêtes passent par un point d'entrée unique.

**25. La table de routage**
Capture : capture du `$dispatchMap`.
Description : chaque URL associée à un couple (contrôleur, méthode).

**26. Réécriture d'URL (.htaccess)**
Capture : capture du `.htaccess` + une URL propre dans la barre d'adresse.
Description : `/connexion` au lieu de `index.php?mod=connexion`, mieux pour le SEO.

**27. Bootstrap & layouts**
Capture : capture de `functions.php`.
Description : initialisation centralisée (session, config, classes) + layouts public/admin.

## 5. Front-end

**28. Intégration des templates**
Capture : aperçu Foodingly + SB Admin.
Description : mon travail = intégrer ces thèmes dans la logique PHP.

**29. Le head & APP_URL**
Capture : capture de `head.php` (base href + APP_URL).
Description : liens portables entre local et production sans modifier le code.

**30. Navbar responsive**
Capture : capture du menu burger mobile (meanmenu).
Description : le menu desktop se transforme en burger sous 1200px.

**31. Le responsive**
Capture : la même page en desktop / tablette / mobile côte à côte.
Description : Bootstrap + media queries + mes correctifs `responsive-fixes.css`.

**32. Footer & scripts**
Capture : capture du footer + `foot.php`.
Description : scripts chargés en fin de page pour ne pas bloquer l'affichage.

**33. Fonctionnalité : horaires**
Capture : capture de la page horaires (cases ouvert/fermé).
Description : JavaScript vanilla qui désactive les champs d'un jour fermé.

**34. Fonctionnalité : Drag & Drop**
Capture : capture de la gestion de carte avec un plat en cours de déplacement.
Description : réorganiser les plats par glisser-déposer avec SortableJS.

**35. Drag & Drop côté serveur**
Capture : capture du gestionnaire `onEnd` (fetch + async/await).
Description : persistance immédiate en base via AJAX, sans rechargement.

**36. Protection XSS**
Capture : capture d'une ligne `htmlspecialchars(...)`.
Description : neutraliser les caractères dangereux affichés à l'écran.

## 6. Back-end & sécurité

**37. PDO & requêtes préparées**
Capture : capture d'une requête préparée (`prepare` + `execute`).
Description : les valeurs sont traitées comme des données, pas comme du SQL.

**38. Le pattern Singleton**
Capture : capture de la classe `Database` (`getInstance`).
Description : une seule connexion partagée pour toute la requête.

**39. Authentification : vue d'ensemble**
Capture : schéma des 3 profils + sessions.
Description : qui peut accéder à quoi, persistance via `$_SESSION`.

**40. Hachage bcrypt**
Capture : capture de `password_hash(...)` dans `insertUtilisateur`.
Description : le mot de passe en clair n'est jamais stocké.

**41. Le sel automatique de bcrypt**
Capture : un hash bcrypt commenté (`$2y$09$...` : algorithme, cost, sel, empreinte).
Description : bcrypt génère un sel aléatoire unique par mot de passe ; pas de pepper.

**42. Inscription**
Capture : capture du formulaire + de la création en 2 tables.
Description : validation des champs puis création compte + fiche métier liée.

**43. Vérification au login**
Capture : capture de `tryToConnect` (bloc password_verify).
Description : même combinaison qu'à l'inscription, hash irréversible.

**44. Le contrôleur de connexion**
Capture : capture de `AuthController::login` (redirection par profil).
Description : message d'erreur générique = mesure de sécurité.

**45. Protection des pages privées**
Capture : capture d'une garde de contrôleur (redirection si non connecté).
Description : aucune action sensible sans session valide.

**46. La déconnexion**
Capture : capture de `logout()` (`session_destroy`).
Description : on vide et détruit la session, puis redirection.

**47. Le CRUD des plats**
Capture : capture de la gestion de carte (liste des plats).
Description : créer, lire, modifier, supprimer un plat.

**48. Contrôle d'ownership & CSRF**
Capture : capture de `getOwnedBy` (jointure SQL).
Description : un restaurateur n'agit que sur ses propres données ; CSRF en perspective.

## 7. Tests & qualité

**49. Jeux d'essai**
Capture : l'image du tableau des jeux d'essai.
Description : cas fonctionnels et de sécurité, entrée / attendu / obtenu.

**50. Tests de sécurité**
Capture : captures injection SQL refusée + accès admin bloqué.
Description : preuve que les protections fonctionnent (CP6/CP7).

**51. Audit Lighthouse**
Capture : capture du rapport Lighthouse (scores).
Description : performance, accessibilité, bonnes pratiques, SEO.

**52. Validation W3C**
Capture : capture du rapport « No errors or warnings ».
Description : conformité du HTML aux standards.

**53. Audit WAVE**
Capture : capture WAVE avant/après corrections.
Description : erreurs d'accessibilité identifiées et corrigées.

**54. Accessibilité : choix concrets**
Capture : capture montrant le lang, l'alt, un aria-label.
Description : sémantique, textes alternatifs, labels, contraste.

**55. Éco-conception**
Capture : capture d'une image WebP + du chargement JS ciblé.
Description : images optimisées, dimensions fixes, scripts là où ils servent.

## 8. Déploiement

**56. Environnement & config**
Capture : capture de `config.php` (détection local/prod).
Description : Docker en local, bascule automatique vers la production.

**57. Mise en ligne o2switch**
Capture : capture FileZilla + le site en ligne (URL o2switch).
Description : transfert FTP, base importée, `.env` de production.

## 9. Démonstration & clôture

**58. Démonstration live**
Capture : la page d'accueil en ligne (transition vers le navigateur).
Description : on bascule sur le site réel pour montrer un parcours complet.

**59. Compétences & veille**
Capture : l'image stack technique + logos MDN/PHP.net.
Description : compétences front/back consolidées, veille via la documentation.

**60. Difficultés rencontrées**
Capture : schéma ou icône « problème → solution ».
Description : MVC rigoureux et Drag & Drop multi-technologies.

**61. Axes d'évolution**
Capture : icônes (CSRF, paiement, e-mail, PWA).
Description : tokens CSRF, paiement en ligne, notifications, application mobile.

**62. Conclusion & remerciements**
Capture : logo Miamy + « Merci ».
Description : bilan, ce que le projet m'a apporté, remerciements à Yann.
