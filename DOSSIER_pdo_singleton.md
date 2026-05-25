# Sécurité base de données : PDO, requêtes préparées et Singleton

## 1. Présentation

La sécurité de la communication avec la base de données est un point central du projet. Toutes les requêtes SQL passent par **PDO** (PHP Data Objects), l'extension officielle de PHP pour dialoguer avec les bases de données, et utilisent systématiquement des **requêtes préparées** pour empêcher les **injections SQL**.

L'accès à PDO est centralisé dans une classe `Database` qui implémente le **pattern Singleton** : il n'y a qu'une seule connexion à la base, partagée par tous les modèles, pour toute la durée de la requête HTTP.

---

## 2. La classe `Database` (pattern Singleton)

**Fichier :** `classes/class.database.php`

Le pattern **Singleton** garantit qu'une classe ne peut être instanciée qu'**une seule fois** dans toute l'application. On l'utilise ici pour la connexion à la base de données, parce qu'ouvrir plusieurs connexions serait inutile et coûteux.

```php
class Database
{
    private ?PDO $_connection = null;
    private static ?Database $_instance = null;

    public static function getInstance(): Database
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct()
    {
        try {
            $this->_connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USERNAME,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            trigger_error("Failed to connect to MySQL: " . $e->getMessage(), E_USER_ERROR);
        }
    }

    private function __clone() {}

    public function getConnection(): ?PDO
    {
        return $this->_connection;
    }
}
```

Les éléments importants :

- **`private static ?Database $_instance`** : la propriété statique qui stocke l'unique instance.
- **`getInstance()`** : la méthode publique qu'on appelle pour récupérer l'instance. Si elle n'existe pas encore, on la crée ; sinon, on retourne celle déjà créée.
- **`private function __construct()`** : le constructeur est **privé**, donc impossible de faire `new Database()` depuis l'extérieur. On est obligé de passer par `getInstance()`.
- **`private function __clone()`** : on empêche aussi le clonage de l'objet, qui contournerait le Singleton.

**Options PDO importantes :**
- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION` : les erreurs SQL deviennent des exceptions PHP qu'on peut attraper avec `try/catch`.
- `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC` : les résultats sont retournés sous forme de tableaux associatifs (`$row['email']` au lieu de `$row[2]`).
- `PDO::ATTR_EMULATE_PREPARES => false` : on force PDO à utiliser les **vraies** requêtes préparées de MySQL, pas une émulation côté PHP. C'est plus sûr.

---

## 3. Utilisation dans les modèles

Tous les modèles récupèrent la connexion via le Singleton dans leur constructeur, puis l'utilisent pour préparer et exécuter leurs requêtes :

```php
class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function isRegistered(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }
}
```

Deux étapes systématiques :

1. **`prepare()`** : on envoie à MySQL un **modèle** de requête, avec des **placeholders** (`:email`) à la place des valeurs variables. MySQL compile la requête une fois pour toutes.
2. **`execute([...])`** : on envoie ensuite **séparément** les valeurs à insérer dans les placeholders. MySQL traite ces valeurs comme des **données**, jamais comme du code SQL.

---

## 4. Pourquoi c'est sécurisé : l'injection SQL

L'**injection SQL** est la faille n°1 de l'OWASP Top 10 depuis 20 ans. Elle se produit quand un développeur **concatène** des données utilisateur dans une requête SQL.

### Exemple de code **vulnérable** (à ne JAMAIS faire)

```php
// ❌ DANGEREUX : concaténation directe
$email = $_POST['email'];
$query = "SELECT * FROM utilisateurs WHERE email = '" . $email . "'";
$result = $pdo->query($query);
```

Si un attaquant envoie comme email :
```
' OR '1'='1
```

La requête devient :
```sql
SELECT * FROM utilisateurs WHERE email = '' OR '1'='1'
```

`'1'='1'` est toujours vrai → l'attaquant récupère **toute la table des utilisateurs**.

### Mon code Miamy : la version sécurisée

```php
// ✅ SÉCURISÉ : requête préparée avec placeholder
$stmt = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
$stmt->execute(['email' => $_POST['email']]);
```

Même si l'attaquant envoie `' OR '1'='1`, MySQL traite cette chaîne comme une **valeur littérale à comparer**, pas comme du SQL. La requête cherchera littéralement un email égal à `' OR '1'='1`, ne trouvera rien, et retournera une chaîne vide.

**C'est pour ça que les requêtes préparées sont la défense standard** : la séparation stricte entre le **code SQL** (le `prepare`) et les **données** (l'`execute`) rend l'injection structurellement impossible.

---

## 5. Bilan

Cette approche combine trois bonnes pratiques :

- **PDO** comme couche d'abstraction officielle de PHP : portable, moderne, bien documentée.
- **Singleton** pour ne pas multiplier les connexions à la base, ce qui économise des ressources serveur.
- **Requêtes préparées systématiques** pour bloquer les injections SQL au niveau structurel, indépendamment de toute validation côté formulaire.

Aucune requête SQL du projet ne concatène de variable utilisateur directement dans la chaîne SQL. C'est une règle absolue que je me suis fixée dès le début et que j'ai respectée partout, dans tous les modèles (`User`, `Restaurant`, `Plat`, `Restaurateur`, `Horaire`, `UserLog`, etc.).
