<?php
/**
 * Client : gestion des comptes clients (table `clients`).
 * Le compte de connexion associe vit dans `utilisateurs` (profil = 3).
 *
 * NOTE : seule la methode insert() a un caller actuellement (AuthController::registerClient).
 * Les autres methodes sont conservees pour les fonctionnalites futures (panier, commandes,
 * espace client) mais ne sont actuellement appelees nulle part.
 */
class Client
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function listAll(bool $onlyCount = false)
    {
        $data = $this->pdo->query("SELECT * FROM `clients`")->fetchAll();
        return $onlyCount ? count($data) : $data;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM `clients` WHERE `id` = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `clients` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: [];
    }

    public function getByEmail(string $email): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `clients` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() ?: [];
    }

    /**
     * Cree un nouveau client. Requiert un user_id (FK vers utilisateurs).
     * Retourne l'ID insere.
     */
    public function insert(array $client)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `clients`
            (`civilite`, `nom`, `prenom`, `telephone`,
             `adresse`, `adresse_comp`, `codepostal`, `ville`, `user_id`)
            VALUES
            (:civilite, :nom, :prenom, :telephone,
             :adresse, :adresse_comp, :codepostal, :ville, :user_id)"
        );
        $stmt->execute([
            'civilite'     => $client['civilite'],
            'nom'          => $client['nom'],
            'prenom'       => $client['prenom'],
            'telephone'    => $client['telephone'],
            'adresse'      => $client['adresse'],
            'adresse_comp' => $client['adresse_comp'],
            'codepostal'   => $client['codepostal'],
            'ville'        => $client['ville'],
            'user_id'      => (int)$client['user_id'],
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Charge un client dans la session et met a jour sa date d'action.
     * Utilise par le flux de connexion client (pas encore branche).
     */
    public function refreshSession(int $id): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `clients` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        if ($result) {
            $_SESSION['connected'] = true;
            $_SESSION['user']      = $result;
        } else {
            $_SESSION['connected'] = false;
            $_SESSION['user']      = false;
        }

        $upd = $this->pdo->prepare("UPDATE `clients` SET `dateaction` = NOW() WHERE `id` = :id");
        $upd->execute(['id' => $id]);

        // insertIP est encore une fonction globale (class.users.php pas encore converti).
        insertIP($id, 2);
    }

    /**
     * Tentative de connexion directe client (table `clients`).
     * Non utilise actuellement : le projet passe par trytoconnect() de class.users.php.
     */
    public function tryToConnect(string $email, string $pass): bool
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `clients` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['motdepasse'])) {
            $_SESSION['connected'] = true;
            $_SESSION['user']      = $user;
            return true;
        }

        $_SESSION['connected'] = false;
        $_SESSION['user']      = false;
        return false;
    }

    /**
     * Met a jour les donnees d'un client. Si $client['motdepasse'] est present,
     * le mot de passe est aussi mis a jour (hashe).
     */
    public function update(array $client): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `clients` SET
                `email`        = :email,
                `civilite`     = :civilite,
                `nom`          = :nom,
                `prenom`       = :prenom,
                `telephone`    = :telephone,
                `adresse`      = :adresse,
                `adresse_comp` = :adresse_comp,
                `codepostal`   = :codepostal,
                `ville`        = :ville
             WHERE `id` = :id"
        );
        $stmt->execute([
            'email'        => $client['email'],
            'civilite'     => $client['civilite'],
            'nom'          => $client['nom'],
            'prenom'       => $client['prenom'],
            'telephone'    => $client['telephone'],
            'adresse'      => $client['adresse'],
            'adresse_comp' => $client['adresse_comp'],
            'codepostal'   => $client['codepostal'],
            'ville'        => $client['ville'],
            'id'           => (int)$client['id'],
        ]);

        if (isset($client['motdepasse'])) {
            $this->changePassword((int)$client['id'], $client['motdepasse']);
        }
    }

    /**
     * Change le mot de passe d'un client (hash bcrypt).
     */
    public function changePassword(int $id, string $pass): void
    {
        $stmt = $this->pdo->prepare("UPDATE `clients` SET `motdepasse` = :motdepasse WHERE `id` = :id");
        $stmt->execute([
            'motdepasse' => password_hash($pass, PASSWORD_DEFAULT),
            'id'         => $id,
        ]);
    }

    /**
     * Verifie si un email est deja utilise par un client.
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM `clients` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }
}
