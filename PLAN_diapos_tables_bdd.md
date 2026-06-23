# Les tables de la base — 4 exemples

> Pour chaque table : **À afficher** (le `CREATE TABLE`), **Points clés** et **Commentaire** (à dire à l'oral).
> Ces 4 tables couvrent les 3 types de relations : 1:1, 1:n et n:m.

---

## 1. `utilisateurs` — la table centrale des comptes

**À afficher :**

```sql
CREATE TABLE `utilisateurs` (
    `id`              int AUTO_INCREMENT,
    `email`           varchar(250) NOT NULL,
    `motdepasse`      varchar(100) NOT NULL,   -- le hash bcrypt
    `profil`          int NOT NULL DEFAULT 3,  -- 1=admin, 2=resto, 3=client
    `profil_id`       int DEFAULT NULL,        -- → id dans la table métier
    `dateinscription` datetime, `dateconnect` datetime,
    `actif`           tinyint(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
);
```

**Points clés :** clé primaire `id` · `email` en **UNIQUE** · `profil` + `profil_id` qui pointent vers la fiche métier.

**Commentaire :**

La table utilisateurs est le cœur de l'authentification : elle stocke ce qui est commun à tout le monde, l'email, le mot de passe haché, et le profil. La clé primaire est un identifiant auto-incrémenté. L'email est en contrainte d'unicité, ce qui garantit au niveau de la base qu'on ne peut pas avoir deux comptes avec le même email. Le champ profil dit quel type d'utilisateur c'est, 1 pour admin, 2 pour restaurateur, 3 pour client, et le champ profil_id pointe vers la ligne correspondante dans la table métier. Le champ actif permet d'activer ou de désactiver un compte sans le supprimer.

---

## 2. `administrateurs` — une relation 1:1

**À afficher :**

```sql
CREATE TABLE `administrateurs` (
    `id`        int AUTO_INCREMENT,
    `nom`       varchar(150) NOT NULL,
    `prenom`    varchar(150) NOT NULL,
    `telephone` varchar(20)  NOT NULL,
    `user_id`   int NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_user` (`user_id`),          -- ← garantit le 1:1
    CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`)
        REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
);
```

**Points clés :** clé étrangère `user_id` vers `utilisateurs` · **UNIQUE sur `user_id`** = relation 1:1 · `ON DELETE CASCADE`.

**Commentaire :**

La table administrateurs contient les informations propres à un admin, son nom, prénom, téléphone. Elle est reliée à la table utilisateurs par une clé étrangère, user_id. Le point important, c'est la contrainte d'unicité sur user_id : elle garantit qu'un compte utilisateur ne peut être lié qu'à une seule fiche admin, c'est ce qui matérialise une relation un à un. Et le ON DELETE CASCADE fait que si je supprime le compte utilisateur, sa fiche admin est supprimée automatiquement. J'ai exactement la même structure pour les restaurateurs et les clients : un compte commun, plus une fiche métier séparée selon le rôle. Ça m'évite d'avoir une seule grosse table utilisateurs avec plein de colonnes vides selon le type de compte.

---

## 3. `horaires` — une relation 1:n (+ contrainte d'unicité métier)

**À afficher :**

```sql
CREATE TABLE `horaires` (
    `id`            int AUTO_INCREMENT,
    `id_restaurant` int NOT NULL,
    `jour`          tinyint(1) NOT NULL,   -- 0=Lundi … 6=Dimanche
    `ouvert`        tinyint(1) NOT NULL DEFAULT 1,
    `debut`         time, `fin` time,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_resto_jour` (`id_restaurant`, `jour`),   -- ← 1 ligne par jour
    CONSTRAINT `fk_horaires_restaurant` FOREIGN KEY (`id_restaurant`)
        REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
);
```

**Points clés :** clé étrangère `id_restaurant` (1 restaurant → plusieurs horaires) · **UNIQUE `(id_restaurant, jour)`** · `ON DELETE CASCADE`.

**Commentaire :**

La table horaires stocke les horaires d'ouverture, à raison d'une ligne par jour et par restaurant. Elle est reliée aux restaurants par une clé étrangère : un restaurant a plusieurs horaires, c'est une relation un à plusieurs. Le point intéressant, c'est la contrainte d'unicité sur le couple identifiant de restaurant et jour : elle empêche d'enregistrer deux fois le même jour pour un même restaurant. Et c'est elle qui me permet, côté code, d'enregistrer les sept jours d'un coup avec un INSERT ON DUPLICATE KEY UPDATE : si la ligne du jour existe déjà, elle est mise à jour, sinon elle est créée. Là encore, le ON DELETE CASCADE supprime les horaires si le restaurant est supprimé.

---

## 4. `restaurant_categories` — une relation n:m (table de liaison)

**À afficher :**

```sql
CREATE TABLE `restaurant_categories` (
    `id_categorie`  int NOT NULL,
    `id_restaurant` int NOT NULL,
    PRIMARY KEY (`id_categorie`, `id_restaurant`),   -- ← clé primaire composite
    CONSTRAINT `fk_cle_categorie`  FOREIGN KEY (`id_categorie`)
        REFERENCES `categories`  (`id_categorie`)  ON DELETE CASCADE,
    CONSTRAINT `fk_cle_restaurant` FOREIGN KEY (`id_restaurant`)
        REFERENCES `restaurants` (`id_restaurant`) ON DELETE CASCADE
);
```

**Points clés :** **table de liaison** · clé primaire **composite** (les 2 clés étrangères) · 2 `FOREIGN KEY` · `ON DELETE CASCADE` des deux côtés.

**Commentaire :**

La table restaurant_categories est une table de liaison : elle gère une relation plusieurs à plusieurs, parce qu'un restaurant peut avoir plusieurs catégories et qu'une catégorie concerne plusieurs restaurants. Une simple clé étrangère ne suffit pas pour ce cas, il faut une table intermédiaire. Elle ne contient que deux colonnes, l'identifiant de la catégorie et celui du restaurant, et sa clé primaire est composite : c'est le couple des deux qui est unique, ce qui empêche d'associer deux fois la même catégorie au même restaurant. Les deux colonnes sont aussi des clés étrangères, vers categories et vers restaurants, toutes les deux en cascade.

---

## Récap — les 3 types de relations en une diapo

| Table | Relation | Mécanisme SQL |
|---|---|---|
| `administrateurs` → `utilisateurs` | **1:1** | FK `user_id` + **UNIQUE** dessus |
| `horaires` → `restaurants` | **1:n** | FK `id_restaurant` (côté « n ») |
| `restaurant_categories` | **n:m** | table de liaison + PK composite |

**Phrase de synthèse :** « Mes tables illustrent les trois relations classiques : le un à un entre un compte et sa fiche métier, le un à plusieurs entre un restaurant et ses horaires, et le plusieurs à plusieurs entre restaurants et catégories via une table de liaison. Et toutes les clés étrangères sont en suppression en cascade, pour garder une base cohérente. »
