<?php
/**
 * Page : routage URL (table `pages`).
 *
 * Cette table sert au Front Controller (index.php) pour résoudre
 * un slug d'URL ($_GET['mod']) vers le titre + le fichier de vue
 * correspondant. C'est notre "table de routage".
 *
 * NOTE : seule la methode getByMod() est utilisee activement.
 * Les autres methodes (insert, update, listAll, getById) sont
 * conservees pour une future page admin de gestion des routes.
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

    /**
     * Met a jour une entree de page existante.
     */
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

    /**
     * Retourne toutes les pages.
     */
    public function listAll(bool $onlyCount = false)
    {
        $data = $this->pdo->query("SELECT * FROM `pages` ORDER BY `nom` ASC")->fetchAll();
        return $onlyCount ? count($data) : $data;
    }

    /**
     * Retourne une page par son id.
     */
    public function getById(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `pages` WHERE `id` = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: [];
    }

    /**
     * Retourne la page correspondant a un mod (slug d'URL).
     * Utilisee par le routeur index.php — DO NOT BREAK.
     */
    public function getByMod(string $mod): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `pages` WHERE `mod` = :mod");
        $stmt->execute(['mod' => $mod]);
        $url = $stmt->fetch() ?: [];

        $url['ok'] = isset($url['id']);
        return $url;
    }
}
