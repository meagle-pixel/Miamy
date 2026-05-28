<?php
/**
 * Restaurateur : gestion des profils de gerants (table `restaurateurs`).
 * Le compte de connexion associe vit dans `utilisateurs` (profil = 2).
 */
class Restaurateur
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Retourne un restaurateur par son id, ou null s'il n'existe pas.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `restaurateurs` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Cree un nouveau restaurateur. Retourne l'ID insere.
     */
    public function insert(array $data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `restaurateurs` (nom, prenom, email, telephone, user_id)
             VALUES (:nom, :prenom, :email, :telephone, :user_id)"
        );
        $stmt->execute([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'],
            'user_id'   => (int)$data['user_id'],
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * Met a jour les infos d'un restaurateur. $data['id'] doit etre present.
     */
    public function update(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `restaurateurs` SET
                nom       = :nom,
                prenom    = :prenom,
                email     = :email,
                telephone = :telephone
             WHERE id = :id"
        );
        return $stmt->execute([
            'nom'       => $data['nom'],
            'prenom'    => $data['prenom'],
            'email'     => $data['email'],
            'telephone' => $data['telephone'],
            'id'        => (int)$data['id'],
        ]);
    }
}
