# Authentification et sécurité des mots de passe

## 1. Présentation

L'application Miamy gère trois types d'utilisateurs (administrateurs, restaurateurs, clients) qui ont chacun leur propre espace authentifié. La sécurité des comptes repose sur deux piliers :

- **Le hachage du mot de passe** avec l'algorithme **bcrypt** (`password_hash`) : le mot de passe en clair n'est jamais stocké en base.
- **La vérification au moment du login** avec `password_verify`, qui compare le mot de passe saisi avec le hash stocké, sans jamais le déchiffrer.

Aucune action sensible ne peut être effectuée sans être connecté, et la gestion des sessions PHP (`$_SESSION`) garantit la persistance du contexte utilisateur entre les requêtes.

---

## 2. Le hachage du mot de passe (inscription)

**Fichier :** `classes/class.users.php`, méthode `insertUtilisateur()`

Quand un nouvel utilisateur s'inscrit, son mot de passe est immédiatement **haché avec bcrypt** avant d'être inséré en base. Il n'est jamais stocké en clair.

```php
public function insertUtilisateur(array $utilisateur)
{
    $base_salt = BASE_SALT;
    $options   = ['cost' => 9];

    $pass = password_hash(
        $utilisateur['motdepasse'] . $utilisateur['email'] . $base_salt,
        PASSWORD_BCRYPT,
        $options
    );

    $stmt = $this->pdo->prepare(
        "INSERT INTO utilisateurs (email, motdepasse, profil, profil_id, dateinscription, actif)
         VALUES (:email, :motdepasse, :profil, :profil_id, NOW(), '1')"
    );
    $stmt->execute([
        'email'      => $utilisateur['email'],
        'motdepasse' => $pass,
        'profil'     => $utilisateur['profil'],
        'profil_id'  => $utilisateur['profil_id'],
    ]);

    return $this->pdo->lastInsertId();
}
```

**Pourquoi bcrypt ?**
Bcrypt est un algorithme de hachage **lent par conception** : il est volontairement coûteux en calcul pour rendre les attaques par force brute impraticables. Le paramètre `cost` (ici 9) contrôle cette lenteur : chaque incrément double le temps de calcul. Pour un attaquant qui essaye des milliards de mots de passe, c'est une barrière énorme. Pour l'utilisateur légitime qui ne se connecte qu'une fois, c'est imperceptible.

### Le sel : géré automatiquement par `password_hash`

`password_hash` **génère lui-même un sel aléatoire** unique pour chaque appel, et l'inclut directement dans la chaîne retournée. Un hash bcrypt stocké en base ressemble à :

```
$2y$09$abcdef0123456789xxxxxxOoR3M5VBJ8YT0nU8eC1V2WLcQE6vC5K2
  │   │  └──── sel aléatoire ────┘└──────── hash final ────────┘
  │   └─ cost (lenteur du calcul)
  └─ algorithme bcrypt
```

Conséquence : deux utilisateurs avec le même mot de passe produisent des hashs **complètement différents** en base, sans aucune action de ma part. Je n'ai donc **pas besoin d'ajouter mon propre sel aléatoire** — PHP s'en charge.

### Le poivre (`BASE_SALT`) : une couche de défense supplémentaire

En plus du sel automatique, je concatène une chaîne secrète globale `BASE_SALT` (stockée dans `.env`, jamais versionnée) au mot de passe avant le hachage. Techniquement, ce n'est pas un **sel** mais un **poivre** (pepper en anglais) : c'est un concept distinct.

| Concept | Sel (salt) | Poivre (pepper) |
|---|---|---|
| **Aléatoire ?** | Oui, unique par mot de passe | Non, identique pour tous |
| **Stocké où ?** | En base, inclus dans le hash | Dans le `.env`, **jamais en base** |
| **Géré par qui ?** | `password_hash` automatiquement | Moi, manuellement |
| **Protège contre** | Tables arc-en-ciel | Vol de la base seule |

**Le rôle du poivre :** si un attaquant arrivait à voler uniquement ma base de données (par exemple via une faille sur un autre composant), il aurait les hashs et leurs sels — mais **pas le poivre**, qui est dans le code/`.env`. Sans cette chaîne secrète, aucune attaque par force brute ne peut reproduire le bon hash, parce que la formule complète est :

```
bcrypt( motdepasse + email + PEPPER )
```

C'est ce qu'on appelle de la **défense en profondeur** : même en cas de compromission partielle, les mots de passe restent protégés.

**Remarque sur le nommage :** la constante s'appelle historiquement `BASE_SALT` dans le projet, mais le terme techniquement correct serait `PEPPER` ou `PASSWORD_PEPPER`. C'est un héritage de nommage que je laisse en l'état pour ne pas casser la compatibilité, mais que je documenterais comme axe de refactorisation.

**Remarque sur l'email concaténé :** comme bcrypt génère déjà un sel aléatoire unique, ajouter l'email avant le hachage n'apporte pas de protection cryptographique supplémentaire. C'est sans risque, mais redondant.

---

## 3. La vérification (login)

**Fichier :** `classes/class.users.php`, méthode `tryToConnect()`

Quand l'utilisateur saisit son email et son mot de passe pour se connecter, on récupère son hash stocké en base et on utilise `password_verify` pour comparer.

```php
public function tryToConnect(string $email, string $pass): bool
{
    $base_salt = BASE_SALT ?? "";

    $stmt = $this->pdo->prepare(
        "SELECT * FROM utilisateurs WHERE email = :email AND actif = '1'"
    );
    $stmt->execute(['email' => $email]);
    $userFound = $stmt->fetch();

    if ($userFound) {
        if (!password_verify($pass . $email . $base_salt, $userFound['motdepasse'])) {
            $_SESSION['connected'] = false;
            (new UserLog())->log(0, 'login_fail', "Echec connexion pour $email");
            return false;
        }

        // Connexion réussie : on hydrate la session
        $_SESSION['connected'] = true;
        $_SESSION['user']      = $userFound;
        // ... mise à jour de dateconnect, log de la connexion, etc.

        return true;
    }

    return false;
}
```

Le point essentiel : on **ne déchiffre jamais** le hash stocké. `password_verify` se contente de re-hacher le mot de passe saisi (avec le même sel et le même algorithme) et de comparer **deux hashs entre eux**. Si les deux correspondent, le mot de passe est correct.

---

## 4. Le contrôleur de connexion

**Fichier :** `controllers/AuthController.php`, méthode `login()`

Le contrôleur fait le pont entre le formulaire HTML et le modèle. Il récupère l'email et le mot de passe envoyés en POST, appelle `User::tryToConnect()`, et redirige selon le profil de l'utilisateur.

```php
public function login()
{
    $message_error = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if (!empty($email) && !empty($pass)) {
            if ((new User())->tryToConnect($email, $pass)) {

                // Redirection selon le profil
                $profil = $_SESSION['user']['profil'];

                if ($profil == 1) {
                    $redirect_url = 'dashboard';                   // admin
                } elseif ($profil == 2) {
                    $redirect_url = 'mon-compte-restaurateur';     // restaurateur
                } else {
                    $redirect_url = 'mon-compte';                  // client
                }

                header('Location: ' . APP_URL . '/' . $redirect_url);
                exit();
            } else {
                $message_error = "Identifiants invalides ou compte non activé.";
            }
        }
    }

    return compact('message_error');
}
```

Le **message d'erreur** est volontairement générique (`"Identifiants invalides"`) et ne distingue pas le cas "email inexistant" du cas "mot de passe incorrect". C'est une mesure de sécurité supplémentaire : un attaquant ne doit pas pouvoir deviner si un email est enregistré ou non sur la plateforme.

---

## 5. La déconnexion

**Fichier :** `controllers/AuthController.php`, méthode `logout()`

```php
public function logout()
{
    if (isset($_SESSION['user']['id'])) {
        (new UserLog())->log((int)$_SESSION["user"]["id"], "logout", 'Déconnexion du site');
    }
    $_SESSION = [];
    session_destroy();
    header('Location: ' . APP_URL . '/accueil');
    exit();
}
```

La déconnexion fait trois choses :
1. **Loggue l'événement** dans la table d'audit `userlogs`.
2. **Vide** complètement le tableau `$_SESSION`.
3. **Détruit** la session côté serveur avec `session_destroy()`.

---

## 6. Le flux complet d'une connexion

```
1. L'utilisateur saisit email + mot de passe → soumet le formulaire
                          ↓
2. POST /connexion → AuthController::login()
                          ↓
3. login() appelle User::tryToConnect(email, pass)
                          ↓
4. tryToConnect() cherche l'utilisateur en base par email (requête préparée)
                          ↓
5. Si trouvé : password_verify(pass + email + BASE_SALT, hashStocké)
                          ↓
6. Si OK : hydrate $_SESSION (connected, user, user-info)
   ET met à jour dateconnect en base
   ET loggue 'login' dans userlogs
                          ↓
7. login() lit $_SESSION['user']['profil'] et redirige selon le profil :
   - profil 1 (admin)        → /dashboard
   - profil 2 (restaurateur) → /mon-compte-restaurateur
   - profil 3 (client)       → /mon-compte
```

---

## 7. Bilan et sécurité

Cette implémentation combine plusieurs bonnes pratiques :

- **Hachage bcrypt** avec sel aléatoire généré automatiquement par `password_hash` : protection contre les attaques par dictionnaire et par tables arc-en-ciel.
- **Poivre global (`BASE_SALT`)** concaténé au mot de passe avant hachage : couche de défense en profondeur qui rend les hashs inutilisables sans accès au code source/`.env`.
- **Message d'erreur générique** côté login : ne révèle pas si un email est enregistré ou non.
- **Vérification du flag `actif`** : un compte désactivé ne peut pas se connecter, même avec les bons identifiants.
- **Logging systématique** des connexions réussies et échouées dans la table `userlogs`, pour pouvoir détecter des tentatives d'intrusion.

**Axes d'amélioration possibles :**

- Renommer la constante `BASE_SALT` en `PASSWORD_PEPPER` pour refléter sa nature réelle.
- Retirer la concaténation de l'email avant le hachage : elle est techniquement redondante (le sel aléatoire de bcrypt rend déjà chaque hash unique).
- Mettre en place un **token CSRF** sur le formulaire de connexion pour empêcher la soumission depuis un autre site.
- Ajouter une **limitation du nombre de tentatives** (rate limiting) par adresse IP pour ralentir les attaques par force brute.
- Proposer une **double authentification** (2FA) par email ou code TOTP pour les comptes administrateurs.
- Migrer vers `PASSWORD_ARGON2ID` (algorithme plus récent que bcrypt, recommandé par l'OWASP depuis 2017).
