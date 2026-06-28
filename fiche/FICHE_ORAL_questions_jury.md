# Fiche oral — Questions probables du jury + réponses modèles

> Réponses à la première personne, à apprendre puis à reformuler avec tes mots.
> Toutes vérifiées sur ton code réel. Reste honnête : assume tes choix, c'est ce qui plaît à un jury.

---

## 1. Architecture & routage

**Q — Que se passe-t-il entre le moment où on tape une URL et l'affichage de la page ?**
R — L'URL est d'abord réécrite par mon fichier .htaccess vers un point d'entrée unique, index.php : c'est le pattern Front Controller. Là, je récupère le nom de la page, et je l'utilise deux fois : une fois pour trouver le bon contrôleur dans ma table de routage, et une fois pour trouver le fichier de vue dans une table pages de ma base. Le contrôleur prépare les données en interrogeant les modèles, puis index.php affiche la vue correspondante dans le bon layout. Le tout repart en HTML vers le navigateur.

**Q — C'est quoi le Front Controller ?**
R — C'est le fait que toutes les requêtes passent par un seul fichier d'entrée, index.php, qui se charge ensuite d'aiguiller vers le bon traitement. Ça centralise tout au même endroit plutôt que d'avoir un fichier PHP par page.

**Q — C'est quoi votre table de routage ?**
R — C'est un tableau associatif dans index.php : à chaque nom de page, j'associe une paire contrôleur plus méthode. Pour ajouter une page, je n'ai qu'une ligne à écrire, au lieu d'une longue suite de conditions.

**Q — Comment trouvez-vous la bonne vue à afficher ?**
R — Le contrôleur ne choisit pas la vue lui-même. J'ai une table pages dans ma base qui associe chaque nom de page à un titre et à un fichier de vue. Je la lis avec getByMod, qui me renvoie le bon fichier. Je sais que c'est un choix un peu particulier ; une approche plus classique serait que le contrôleur retourne directement sa vue, ce que je ferais pour éviter une requête en base à chaque page.

**Q — À quoi sert le .htaccess ?**
R — À réécrire les URLs. Il transforme une adresse propre comme slash connexion en index.php avec un paramètre, sans que l'utilisateur le voie. Ça donne des URLs lisibles et meilleures pour le référencement.

---

## 2. MVC

**Q — Expliquez votre architecture MVC.**
R — Je sépare mon code en trois couches. Les modèles, dans le dossier classes, sont les seuls à parler à la base de données. Les vues, dans le dossier views, produisent le HTML. Et les contrôleurs font le lien : ils reçoivent la requête, appellent les modèles pour les données, et les transmettent à la vue.

**Q — Comment vos contrôleurs, modèles et vues communiquent-ils ?**
R — C'est le contrôleur qui relie tout. Il appelle le modèle pour récupérer les données, puis il les transmet à la vue qui les affiche. Le modèle et la vue ne communiquent jamais directement : le contrôleur est toujours l'intermédiaire. C'est tout l'intérêt du MVC, chaque couche a un seul rôle.

**Q — Pourquoi avoir développé sans framework ?**
R — C'était un objectif pédagogique. Un framework m'aurait fourni le routage, l'accès à la base et la sécurité tout faits, mais cachés. En codant à la main, j'ai vraiment compris ce qui se passe sous le capot : le cycle d'une requête, le MVC, la sécurité.

---

## 3. Base de données

**Q — Comment communiquez-vous avec la base ?**
R — Avec PDO, l'outil standard de PHP. Toute la connexion est centralisée dans une seule classe, Database, et partout où il y a une donnée utilisateur, je passe par des requêtes préparées.

**Q — Montrez-moi comment vous évitez les injections SQL.**
R — Avec les requêtes préparées. Je prépare d'abord la commande avec un marqueur à la place de la valeur, puis j'envoie la valeur séparément avec execute. MySQL la traite alors comme une simple donnée, jamais comme du code SQL, donc une saisie piégée ne peut pas modifier ma requête.

**Q — C'est quoi le pattern Singleton, et pourquoi l'utiliser ?**
R — C'est un pattern qui garantit qu'une classe n'a qu'une seule instance. Je l'utilise pour la connexion à la base : une seule connexion partagée pour toute la page, au lieu d'en ouvrir une nouvelle à chaque requête, ce qui gaspillerait les ressources du serveur.

**Q — Comment garantissez-vous une seule instance ?**
R — Le constructeur est privé, donc on ne peut pas faire new de l'extérieur. On passe forcément par getInstance, qui crée l'objet la première fois et renvoie toujours le même ensuite. Et la méthode __clone est privée aussi, pour empêcher de le dupliquer.

---

## 4. Sécurité

**Q — Comment stockez-vous les mots de passe ?**
R — Jamais en clair. À l'inscription, je les hache avec bcrypt via password_hash. Bcrypt génère automatiquement un sel aléatoire unique pour chaque mot de passe, donc deux personnes avec le même mot de passe obtiennent des hash différents. Au login, je refais le même hachage et je compare avec password_verify, sachant qu'un hash bcrypt est irréversible.

**Q — Je vois que vous concaténez l'email au mot de passe avant de hacher, pourquoi ?**
R — Honnêtement, ce n'est pas indispensable : c'est bcrypt qui fait le vrai travail avec son sel automatique. L'email n'est pas secret, donc il n'apporte pas de protection. La version standard serait de hacher uniquement le mot de passe ; je l'ai laissé ainsi par cohérence avec l'existant, mais je sais que je pourrais le simplifier.

**Q — Et comment renforceriez-vous encore la sécurité des mots de passe ?**
R — En ajoutant un pepper, c'est-à-dire un secret stocké en dehors de la base, dans un fichier de configuration. Comme ça, même si la base seule fuit, l'attaquant n'a pas ce secret et les hash restent inexploitables.

**Q — C'est quoi une faille XSS, et comment vous protégez-vous ?**
R — C'est quand un utilisateur saisit du code à la place du texte, par exemple une balise script, et que le navigateur l'exécute au lieu de l'afficher. Je m'en protège avec htmlspecialchars : avant d'afficher une donnée, je transforme les caractères dangereux en texte inoffensif. Je le fais partout où j'affiche une donnée utilisateur.

**Q — Comment empêchez-vous un restaurateur de modifier les données d'un autre ?**
R — Avec un contrôle d'ownership. En plus de vérifier que l'utilisateur est connecté avec le bon rôle, je vérifie avec une jointure SQL que la ressource lui appartient bien. Sans ça, il pourrait modifier le plat d'un autre en changeant l'id dans l'URL.

**Q — Comment protégez-vous les pages privées ?**
R — Avec une garde au début de chaque méthode protégée : je vérifie que l'utilisateur est connecté et qu'il a le bon profil. Sinon, je le redirige vers la connexion avec un exit qui stoppe le script avant tout traitement.

---

## 5. AJAX

**Q — Montrez-moi comment vous traitez l'AJAX.**
R — Toujours de la même façon, en JavaScript natif avec fetch, sans librairie. Je prépare les données dans un FormData, je les envoie en POST vers une route, j'attends la réponse au format JSON avec await, et j'agis selon le résultat. J'applique ce même schéma pour mon drag and drop et pour mon toggle de disponibilité.

**Q — SortableJS gère-t-il l'AJAX ?**
R — Non. SortableJS gère seulement la partie visuelle du glisser-déposer et me prévient quand un plat est lâché, via sa fonction onEnd. L'AJAX, c'est mon propre code que j'écris dans le onEnd : SortableJS ne touche jamais au serveur ni à la base.

**Q — Que se passe-t-il côté serveur quand on déplace un plat ?**
R — Ma méthode updateCategorie fait trois contrôles avant d'écrire : que l'utilisateur a le bon rôle, que la catégorie fait partie d'une liste autorisée, et que le plat lui appartient bien. Si tout est bon, je mets à jour la base avec une requête préparée, et je réponds en JSON.

---

## 6. Front-end

**Q — Avez-vous fait le design vous-même ?**
R — Non, je suis parti de deux templates, Foodingly pour la partie publique et SB Admin pour l'administration, choisis par mon tuteur. Mon travail a été de les intégrer dans ma logique PHP : structurer les vues, gérer les affichages selon le rôle, et corriger le CSS quand le thème ne suffisait pas.

**Q — Comment gérez-vous le responsive ?**
R — Sur trois couches. D'abord la grille Bootstrap qui gère la majorité des adaptations. Ensuite quelques media queries. Et quand le thème avait des bugs d'affichage, plutôt que de modifier ses fichiers, j'ai créé mon propre fichier responsive-fixes.css, chargé en dernier, qui surcharge le thème par la cascade sans toucher à ses fichiers d'origine.

**Q — Comment vos catégories s'empilent-elles sur mobile ?**
R — Grâce à la grille Bootstrap. Chaque carte porte des classes de colonnes qui définissent combien on en affiche par ligne selon l'écran. Sur petit écran, chaque carte passe en pleine largeur, donc elles se retrouvent les unes sous les autres. Bootstrap s'appuie sur Flexbox en interne, avec un retour à la ligne automatique.

---

## 7. Déploiement

**Q — Comment passez-vous du local à la production sans modifier le code ?**
R — Avec mon fichier config.php, qui détecte tout seul s'il tourne en local ou en production, et charge les bons identifiants. Les informations sensibles sont dans un fichier .env, non versionné, qui sépare les variables de développement et de production. Donc le même code fonctionne partout.

**Q — Comment avez-vous mis le site en ligne ?**
R — Sur l'hébergeur o2switch, via son panneau cPanel. J'ai créé un sous-domaine et une base de données, importé ma base avec phpMyAdmin, mis mes identifiants de production dans le .env, et transféré les fichiers en FTP avec FileZilla.

---

## 8. Méthodo & recul

**Q — Comment avez-vous géré le projet ?**
R — Sans méthode agile formelle, mais avec des rituels agiles : un point quotidien avec mon tuteur, une todolist pour prioriser, et des commits Git réguliers sur un dépôt privé. J'avançais fonctionnalité par fonctionnalité.

**Q — Quels sont les axes d'amélioration de votre projet ?**
R — J'en vois plusieurs : ajouter des tokens CSRF sur les formulaires sensibles, mettre en place un vrai système de paiement, et des notifications par email. Côté code, je pourrais aussi optimiser certaines requêtes qui chargent plus de données que la page n'en affiche.

**Q — Qu'est-ce qu'un token CSRF ?**
R — C'est une valeur secrète et unique que le serveur met dans un formulaire et revérifie à l'envoi, pour s'assurer que la requête vient bien de mon propre site et pas d'un site malveillant qui aurait piégé l'utilisateur.

**Q — Utilisez-vous les quatre piliers de la POO ?**
R — Je les connais tous, mais mon projet repose surtout sur l'encapsulation, avec mes propriétés privées et mon Singleton, et sur l'abstraction, avec mes modèles qui cachent les requêtes SQL. Je n'ai pas eu besoin d'héritage ni de polymorphisme ici, parce que mes classes ont chacune une responsabilité distincte.

---

## Questions pièges — à bien préparer

**Q — Pourquoi une table pages plutôt que de gérer la vue dans le contrôleur ?**
R — J'ai centralisé le titre et le fichier de vue de chaque page au même endroit. Je sais qu'une approche plus classique serait que le contrôleur retourne directement sa vue ; c'est ce que je ferais pour optimiser, car ça éviterait une requête en base par page.

**Q — Pourquoi le cost de bcrypt n'est pas précisé ?**
R — Je laisse le facteur de difficulté par défaut de PHP, qui est à 10. C'est une bonne valeur, et je pourrais l'augmenter pour renforcer encore la sécurité au prix d'un calcul un peu plus lent.

**Q — Pourquoi pas de requête préparée dans getTodayForRestaurants ?**
R — Parce que je travaille sur une liste d'identifiants. Mais je les force tous en entiers avant de les insérer, donc il n'y a pas de risque d'injection. Pour l'améliorer, je passerais par des marqueurs dynamiques.
