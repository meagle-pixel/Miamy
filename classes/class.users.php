<?php
/**
 * User : gestion des comptes utilisateurs (table `utilisateurs`) et des comptes
 * administrateurs (table `administrateurs`). Inclut l'authentification.
 *
 * Pour la journalisation, voir UserLog (class.userlogs.php).
 */
class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Cree un compte administrateur (fiche metier seule, sans compte de connexion).
     * Retourne l'ID insere.
     */
    public function insertAdmin(array $data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `administrateurs` (`nom`, `prenom`, `telephone`, `user_id`)
             VALUES (:nom, :prenom, :telephone, :user_id)"
        );
        $stmt->execute([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'telephone' => $data['telephone'] ?? '',
            'user_id'   => (int)$data['user_id'],
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Cree un compte utilisateur (mot de passe hashe avec sel global).
     * Loggue la creation. Retourne l'ID ou false.
     */
    public function insertUtilisateur(array $utilisateur)
    {
        $base_salt = BASE_SALT;
        $options   = ['cost' => 9];
        $pass      = password_hash($utilisateur['motdepasse'] . $utilisateur['email'] . $base_salt, PASSWORD_BCRYPT, $options);

        $stmt = $this->pdo->prepare(
            "INSERT INTO `utilisateurs`
                (`id`, `email`, `motdepasse`, `profil`, `profil_id`, `dateinscription`, `dateconnect`, `dateaction`, `token`, `actif`)
             VALUES
                (NULL, :email, :motdepasse, :profil, :profil_id, NOW(), NULL, NULL, '', '1')"
        );
        $stmt->execute([
            'email'      => $utilisateur['email'],
            'motdepasse' => $pass,
            'profil'     => $utilisateur['profil'],
            'profil_id'  => $utilisateur['profil_id'],
        ]);
        $idu = $this->pdo->lastInsertId();

        if ($idu) {
            $actorId = $_SESSION['user']['id'] ?? $idu;
            (new UserLog())->log((int)$actorId, 'create_user', "Création du compte pour : " . $utilisateur['email']);
            return $idu;
        }
        return false;
    }

    /**
     * Verifie si un email est deja enregistre.
     */
    public function isRegistered(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM `utilisateurs` WHERE `email` = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch() !== false;
    }

   
    public function tryToConnect(string $email, string $pass, bool $bypass = false): bool
    {
        $base_salt = BASE_SALT ?? "";
        $logger    = new UserLog();

        $stmt = $this->pdo->prepare("SELECT * FROM `utilisateurs` WHERE `email` = :email AND actif = '1'");
        $stmt->execute(['email' => $email]);
        $userFound = $stmt->fetch();

        if ($userFound) {
            if (!$bypass) {
                if (!password_verify($pass . $email . $base_salt, $userFound['motdepasse'])) {
                    $_SESSION['connected'] = false;
                    $_SESSION['user']      = false;
                    $logger->log(0, 'login_fail', "Echec connexion pour $email");
                    return false;
                }
            }

            $_SESSION['connected'] = true;
            $_SESSION['user']      = $userFound;

            $upd = $this->pdo->prepare("UPDATE utilisateurs SET `dateconnect` = NOW() WHERE id = :id");
            $upd->execute(['id' => (int)$userFound['id']]);

            $logger->log((int)$userFound['id'], 'login', "Connexion au site réussie");

            $tables = [1 => 'administrateurs', 2 => 'restaurateurs', 3 => 'clients'];
            $_SESSION['user-info'] = null;
            if (isset($tables[(int)$userFound['profil']])) {
                $table = $tables[(int)$userFound['profil']];
                $s = $this->pdo->prepare("SELECT * FROM `$table` WHERE `id` = :id");
                $s->execute(['id' => (int)$userFound['profil_id']]);
                $_SESSION['user-info'] = $s->fetch() ?: null;

                if ((int)$userFound['profil'] < 3) {
                    $_SESSION['admin'] = true;
                }
            }

            return true;
        }

        $_SESSION['connected'] = false;
        $_SESSION['user']      = false;
        return false;
    }

    /**
     * Suppression complete d'un utilisateur (fiche metier + compte utilisateur).
     * Atomique : transaction + rollback en cas d'erreur. Loggue la suppression.
     * Recupere dynamiquement le nom de la table metier depuis profils.type
     * (cascade ON DELETE geree cote MySQL).
     */
    public function deleteUser(int $id, int $profil): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Recupere l'id dans utilisateurs pour le log
            $s = $this->pdo->prepare("SELECT id FROM utilisateurs WHERE profil_id = :profil_id AND profil = :profil");
            $s->execute(['profil_id' => $id, 'profil' => $profil]);
            $row            = $s->fetch();
            $userIdToDelete = $row['id'] ?? 0;

            // Table metier selon le profil (1=admin, 2=restaurateur, 3=client)
            $tablesByProfil = [
                1 => 'administrateurs',
                2 => 'restaurateurs',
                3 => 'clients',
            ];
            $table = $tablesByProfil[$profil] ?? null;

            if ($table) {
                $this->pdo->prepare("DELETE FROM `$table` WHERE `id` = :id")
                    ->execute(['id' => $id]);
            }

            $this->pdo->prepare("DELETE FROM `utilisateurs` WHERE `profil_id` = :profil_id AND `profil` = :profil")
                ->execute(['profil_id' => $id, 'profil' => $profil]);

            $this->pdo->commit();

            $actorId = $_SESSION['user']['id'] ?? 1;
            (new UserLog())->log((int)$actorId, 'delete_user', "Suppression complète de l'utilisateur ID $userIdToDelete (Profil $profil / profil_id $id)");

            return true;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log('[deleteUser] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Migre un utilisateur d'un profil a un autre en garantissant la coherence
     * entre `utilisateurs` et les tables metier (`administrateurs`,
     * `restaurateurs`, `clients`).
     *
     * Tout est encapsule dans une transaction SQL :
     *   1. Lit la ligne actuelle dans la table metier d'origine
     *   2. Insere dans la table metier de destination en reprenant les champs
     *      compatibles (nom, prenom, telephone...) + $extraData pour les NOT NULL
     *      specifiques (ex. civilite, codepostal, ville pour `clients`)
     *   3. Met a jour `utilisateurs` (profil ET profil_id)
     *   4. Supprime l'ancienne ligne metier
     */
    public function changeUserProfile(int $userId, int $newProfil, array $extraData = []): bool
    {
        $tables = [
            1 => 'administrateurs',
            2 => 'restaurateurs',
            3 => 'clients',
        ];

        if (!isset($tables[$newProfil])) {
            error_log("[changeUserProfile] Profil cible inconnu : $newProfil");
            return false;
        }

        $newTable = $tables[$newProfil];

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT profil, profil_id FROM utilisateurs WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("Utilisateur ID $userId introuvable");
            }

            $oldProfil   = (int) $user['profil'];
            $oldProfilId = (int) $user['profil_id'];

            // Aucun changement reel : on sort proprement
            if ($oldProfil === $newProfil) {
                $this->pdo->commit();
                return true;
            }

            // Garde-fou : restaurateur encore proprietaire de restaurants
            if ($oldProfil === 2 && $oldProfilId) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM restaurants WHERE id_restaurateur = :id");
                $stmt->execute(['id' => $oldProfilId]);
                $nbRestos = (int) $stmt->fetchColumn();

                if ($nbRestos > 0) {
                    throw new Exception(
                        "Impossible de migrer ce restaurateur : il possède encore $nbRestos restaurant(s). "
                        . "Réassignez-les ou supprimez-les avant de changer son profil."
                    );
                }
            }

            // Lit l'ancienne ligne metier
            $oldTable = $tables[$oldProfil] ?? null;
            $oldData  = [];
            if ($oldTable && $oldProfilId) {
                $stmt = $this->pdo->prepare("SELECT * FROM `$oldTable` WHERE id = :id");
                $stmt->execute(['id' => $oldProfilId]);
                $oldData = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            }

            // Fusionne ancien + extra (extra prioritaire)
            $merged = array_merge($oldData, $extraData);

            // Filtre selon les colonnes reelles de la table cible
            $cols       = $this->pdo->query("DESCRIBE `$newTable`")->fetchAll(PDO::FETCH_COLUMN);
            $insertData = [];
            foreach ($cols as $col) {
                if ($col === 'id') continue;
                if (array_key_exists($col, $merged)) {
                    $insertData[$col] = $merged[$col];
                }
            }

            // INSERT dans la nouvelle table metier
            $colNames     = array_keys($insertData);
            $colsSql      = implode(',', array_map(fn($c) => "`$c`", $colNames));
            $placeholders = implode(',', array_map(fn($c) => ":$c", $colNames));
            $stmt = $this->pdo->prepare("INSERT INTO `$newTable` ($colsSql) VALUES ($placeholders)");
            $stmt->execute($insertData);
            $newProfilId = (int) $this->pdo->lastInsertId();

            // UPDATE utilisateurs
            $stmt = $this->pdo->prepare("UPDATE utilisateurs SET profil = :profil, profil_id = :profil_id WHERE id = :id");
            $stmt->execute([
                'profil'    => $newProfil,
                'profil_id' => $newProfilId,
                'id'        => $userId,
            ]);

            // DELETE de l'ancienne ligne metier
            if ($oldTable && $oldProfilId) {
                $stmt = $this->pdo->prepare("DELETE FROM `$oldTable` WHERE id = :id");
                $stmt->execute(['id' => $oldProfilId]);
            }

            $this->pdo->commit();

            $actorId = $_SESSION['user']['id'] ?? $userId;
            (new UserLog())->log((int)$actorId, 'update_role', "Migration de profil : utilisateur $userId, $oldProfil -> $newProfil");

            return true;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log('[changeUserProfile] ' . $e->getMessage());
            throw $e;
        }
    }
}
