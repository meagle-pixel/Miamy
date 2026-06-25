<?php
/*
Cette table sert au Front Controller (index.php) pour résoudre
un slug d'URL ($_GET['mod']) vers le titre + le fichier de vue
correspondant. C'est ma "table de routage".
 */

class Page
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Cree une nouvelle entree de page. Retourne l'ID insere ou false.
     */
    public function insert(string $nom, string $mod, string $url)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `pages` (`id`, `nom`, `mod`, `url`) VALUES (NULL, :nom, :mod, :url)"
        );
        $stmt->execute(['nom' => $nom, 'mod' => $mod, 'url' => $url]);
        $idp = $this->pdo->lastInsertId();

        if ($idp) {
            $userId = $_SESSION['user']['id'] ?? 0;
            (new UserLog())->log((int)$userId, 'create_page', "Création de la page : $nom (Module: $mod)");
        }

        return $idp ?: false;
    }

    // Met a jour une entree de page existante.
    
    public function update(int $id, string $nom, string $mod, string $url): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE `pages` SET `nom` = :nom, `mod` = :mod, `url` = :url WHERE `id` = :id"
        );
        $res = $stmt->execute(['nom' => $nom, 'mod' => $mod, 'url' => $url, 'id' => $id]);

        if ($res) {
            $userId = $_SESSION['user']['id'] ?? 0;
            (new UserLog())->log((int)$userId, 'update_page', "Modification de la page ID $id : $nom");
        }

        return true;
    }

    // Retourne toutes les pages.
     
    public function listAll(bool $onlyCount = false)
    {
        $data = $this->pdo->query("SELECT * FROM `pages` ORDER BY `nom` ASC")->fetchAll();
        return $onlyCount ? count($data) : $data;
    }

    // Retourne une page par son id.

    public function getById(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `pages` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: [];
    }

    
    public function getByMod(string $mod): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `pages` WHERE `mod` = :mod");
        $stmt->execute(['mod' => $mod]);
        $url = $stmt->fetch() ?: [];

        $url['ok'] = isset($url['id']);
        return $url;
    }
}
