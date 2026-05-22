<?php
class Horaires {
    private $pdo;

    // Labels des jours (0=Lundi … 6=Dimanche)
    public static $jours = [
        0 => 'Lundi',
        1 => 'Mardi',
        2 => 'Mercredi',
        3 => 'Jeudi',
        4 => 'Vendredi',
        5 => 'Samedi',
        6 => 'Dimanche',
    ];

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Retourne les horaires d'un restaurant, indexés par numéro de jour (0-6).
     * Si un jour n'existe pas en base, il est retourné avec des valeurs par défaut.
     */
    public function getByRestaurant(int $id_restaurant): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM horaires WHERE id_restaurant = :id ORDER BY jour ASC"
        );
        $stmt->execute(['id' => $id_restaurant]);
        $rows = $stmt->fetchAll();

        // Indexer par numéro de jour
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(int)$row['jour']] = $row;
        }

        // Compléter les jours manquants avec des valeurs par défaut
        $result = [];
        foreach (self::$jours as $num => $label) {
            $result[$num] = $indexed[$num] ?? [
                'id'            => null,
                'id_restaurant' => $id_restaurant,
                'jour'          => $num,
                'ouvert'        => 1,
                'debut'         => '09:00:00',
                'fin'           => '22:00:00',
            ];
        }

        return $result;
    }

    /**
     * Sauvegarde (INSERT ou UPDATE) les 7 jours d'un restaurant.
     * $data = tableau indexé par jour : [0 => ['ouvert'=>1, 'debut'=>'09:00', 'fin'=>'22:00'], ...]
     */
    public function save(int $id_restaurant, array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO horaires (id_restaurant, jour, ouvert, debut, fin)
             VALUES (:id_restaurant, :jour, :ouvert, :debut, :fin)
             ON DUPLICATE KEY UPDATE
                 ouvert = VALUES(ouvert),
                 debut  = VALUES(debut),
                 fin    = VALUES(fin)"
        );

        $this->pdo->beginTransaction();
        try {
            foreach (self::$jours as $num => $label) {
                $ouvert = isset($data[$num]['ouvert']) ? 1 : 0;
                $debut  = $ouvert ? ($data[$num]['debut'] ?? null) : null;
                $fin    = $ouvert ? ($data[$num]['fin']   ?? null) : null;

                $stmt->execute([
                    'id_restaurant' => $id_restaurant,
                    'jour'          => $num,
                    'ouvert'        => $ouvert,
                    'debut'         => $debut,
                    'fin'           => $fin,
                ]);
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * Retourne les horaires "du jour" pour une liste de restaurants.
     * Indexe par id_restaurant. Le numero de jour suit la convention
     * date('N') - 1 (0=Lundi).
     */
    public function getTodayForRestaurants(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $ids       = array_map('intval', $ids);
        $idsCsv    = implode(',', $ids);
        $jour      = (int)date('N') - 1;
        $stmt      = $this->pdo->query(
            "SELECT * FROM `horaires` WHERE `id_restaurant` IN ($idsCsv) AND `jour` = $jour"
        );
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $result[(int)$row['id_restaurant']] = $row;
        }
        return $result;
    }
}
