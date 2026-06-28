# Fiche oral — La classe Database (PDO + Singleton), bloc par bloc

> Explication de `classes/class.database.php`, morceau par morceau, comme devant un jury.
> Vérifié sur le code réel.

## Le code complet

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

---

## Bloc 1 — Les deux propriétés privées

```php
private ?PDO $_connection = null;
private static ?Database $_instance = null;
```

`$_connection` contiendra la **connexion PDO**. `$_instance` contiendra l'**unique objet Database**. Le mot **`static`** sur `$_instance` est essentiel : la propriété appartient à la **classe** entière, pas à un objet précis. C'est elle qui porte la logique du Singleton. Le `?` signifie qu'elles peuvent valoir `null` au départ.

## Bloc 2 — getInstance() : le cœur du Singleton

```php
public static function getInstance(): Database
{
    if (!self::$_instance) {
        self::$_instance = new self();
    }
    return self::$_instance;
}
```

La porte d'entrée de la classe. La première fois, `$_instance` est vide, donc on crée l'objet. Ensuite, on renvoie toujours **le même**. Résultat : une seule instance dans toute la page. C'est `static`, donc on l'appelle avec `Database::getInstance()`, sans objet préalable.

## Bloc 3 — __construct() : la création de la connexion

```php
private function __construct()
{
    $this->_connection = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USERNAME, DB_PASSWORD, [ ...options... ]
    );
}
```

C'est ici qu'on ouvre réellement la connexion à MySQL avec `new PDO(...)`. Les identifiants (host, nom, utilisateur, mot de passe) viennent de `config.php`. Le constructeur est **`private`** : impossible de faire `new Database()` de l'extérieur, on est obligé de passer par `getInstance()`. C'est ça qui garantit l'instance unique.

## Bloc 4 — Les options PDO

```php
PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES   => false,
```

- **ERRMODE_EXCEPTION** : les erreurs SQL deviennent des exceptions, attrapables avec try/catch.
- **FETCH_ASSOC** : les résultats reviennent en tableaux associatifs (`$row['email']` au lieu d'un numéro).
- **EMULATE_PREPARES = false** : force les vraies requêtes préparées de MySQL, un cran de sécurité en plus.

## Bloc 5 — Le catch

```php
} catch (PDOException $e) {
    trigger_error("Failed to connect to MySQL: " . $e->getMessage(), E_USER_ERROR);
}
```

Si la connexion échoue, on attrape l'exception et on déclenche une erreur propre, au lieu de laisser le site planter brutalement.

## Bloc 6 — __clone() privé

```php
private function __clone() {}
```

Empêche de **cloner** l'objet. Sans ça, un `clone` créerait un deuxième objet Database. La méthode privée bloque cette possibilité et protège l'instance unique.

## Bloc 7 — getConnection()

```php
public function getConnection(): ?PDO
{
    return $this->_connection;
}
```

La méthode que mes modèles appellent pour récupérer la connexion. Partout dans le code, j'écris `Database::getInstance()->getConnection()` : `getInstance()` me donne l'objet unique, `getConnection()` me rend la connexion PDO pour mes requêtes.

---

## ❓ Questions / Réponses jury

**Q — C'est quoi le pattern Singleton ?**
R — Un pattern qui garantit qu'une classe n'a qu'une seule instance. Je l'utilise pour avoir une seule connexion à la base, partagée par toute la page.

**Q — Pourquoi une seule connexion ?**
R — Ouvrir une connexion coûte des ressources au serveur. Avec une seule connexion partagée, j'évite d'en ouvrir une nouvelle à chaque requête.

**Q — Pourquoi le constructeur est-il privé ?**
R — Pour empêcher de faire `new Database()` de l'extérieur et d'ouvrir plusieurs connexions par erreur. Le seul accès est `getInstance()`, qui garantit l'instance unique.

**Q — À quoi sert `__clone()` privé ?**
R — À empêcher de dupliquer l'objet avec `clone`, ce qui créerait une deuxième instance.

**Q — Pourquoi `static` sur `$_instance` et `getInstance()` ?**
R — Parce qu'ils doivent exister au niveau de la classe, avant qu'aucun objet ne soit créé. C'est ce qui me permet d'appeler `Database::getInstance()` sans avoir déjà un objet.

**Q — Que fait `EMULATE_PREPARES => false` ?**
R — Ça force les vraies requêtes préparées côté MySQL, au lieu d'une émulation côté PHP. C'est un peu plus sûr et plus propre sur le typage des paramètres.

## Phrase de synthèse

> Ma classe Database applique le Singleton : une propriété statique garde l'unique instance, `getInstance()` la crée une fois puis la réutilise, et le constructeur et `__clone` sont privés pour empêcher d'en créer une deuxième. La connexion PDO est ouverte une seule fois avec les options de sécurité, et mes modèles la récupèrent partout via `getInstance()->getConnection()`.
