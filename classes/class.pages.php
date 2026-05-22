<?php
/**
 * Page : routage URL et droits ACL (tables `pages`, `profils`, `autorisations`).
 *
 * NOTE : seule la methode getByMod() (anciennement getPage) est utilisee
 * activement, par le routeur index.php. Les autres methodes sont conservees
 * pour la future page admin de gestion des droits, mais ne sont pas branchees.
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

        if ($idp && function_exists('logUserAction')) {
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

        if ($res && function_exists('logUserAction')) {
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
     * Si $profil est passe, applique le filtre ACL : la page devient
     * "views/acces.php" si le profil n'a pas le droit.
     *
     * Utilisee par le routeur index.php — DO NOT BREAK.
     */
    public function getByMod(string $mod, $profil = false): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `pages` WHERE `mod` = :mod");
        $stmt->execute(['mod' => $mod]);
        $url = $stmt->fetch() ?: [];

        $url['ok'] = true;

        if ($profil) {
            if (!isset($url['id'])) {
                $url['url'] = 'views/acces.php';
                $url['nom'] = 'Page inaccessible';
                $url['ok']  = false;
            } else {
                // isClear() est encore une fonction globale (class.users.php).
                if (!$this->hasAccess((int)$url["id"], (int)$profil)) {
                    $url['url'] = 'views/acces.php';
                    $url['nom'] = 'Page inaccessible';
                    $url['ok']  = false;
                }
            }
        }
        return $url;
    }

    /**
     * Liste tous les profils.
     */
    public function listProfils(bool $onlyCount = false)
    {
        $data = $this->pdo->query("SELECT * FROM `profils`")->fetchAll();
        return $onlyCount ? count($data) : $data;
    }

    /**
     * Liste toutes les autorisations.
     */
    public function listAuthorizations(bool $onlyCount = false)
    {
        $data = $this->pdo->query("SELECT * FROM `autorisations`")->fetchAll();
        return $onlyCount ? count($data) : $data;
    }

    /**
     * Verifie qu'un profil a acces a une page donnee.
     */
    public function hasAccess(int $page, int $profil): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `autorisations` WHERE `page` = :page AND `profil` = :profil"
        );
        $stmt->execute(['page' => $page, 'profil' => $profil]);
        $rows = $stmt->fetchAll();

        return count($rows) && $rows[0]['etat'] == "1";
    }

    /**
     * Bascule l'autorisation d'un profil sur une page (toggle 0/1, cree si absent).
     */
    public function toggleAccess(int $page, int $profil): void
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM `autorisations` WHERE `page` = :page AND `profil` = :profil"
        );
        $stmt->execute(['page' => $page, 'profil' => $profil]);
        $rows      = $stmt->fetchAll();
        $actionLog = "";

        if (count($rows) && $rows[0]['etat'] == "1") {
            $upd = $this->pdo->prepare(
                "UPDATE `autorisations` SET `etat` = '0' WHERE `page` = :page AND `profil` = :profil"
            );
            $upd->execute(['page' => $page, 'profil' => $profil]);
            $actionLog = "Retrait accès";
        } elseif (count($rows) && $rows[0]['etat'] == "0") {
            $upd = $this->pdo->prepare(
                "UPDATE `autorisations` SET `etat` = '1' WHERE `page` = :page AND `profil` = :profil"
            );
            $upd->execute(['page' => $page, 'profil' => $profil]);
            $actionLog = "Ajout accès";
        } else {
            $ins = $this->pdo->prepare(
                "INSERT INTO `autorisations` (`id`, `page`, `profil`, `etat`) VALUES (NULL, :page, :profil, '1')"
            );
            $ins->execute(['page' => $page, 'profil' => $profil]);
            $actionLog = "Ajout accès (Initial)";
        }

        if ($actionLog !== "" && function_exists('logUserAction')) {
            $userId = $_SESSION['user']['id'] ?? 0;
            (new UserLog())->log((int)$userId, 'update_permission', "$actionLog pour Page ID $page / Profil ID $profil");
        }
    }
}
