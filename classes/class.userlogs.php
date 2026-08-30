<?php

class UserLog
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Enregistre une action utilisateur dans user_logs (avec IP).
     */
    public function log(?int $userId, string $actionType, string $message): void
    {
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = $this->pdo->prepare(
            "INSERT INTO user_logs (user_id, action_type, message, ip_address)
             VALUES (:user_id, :action_type, :message, :ip_address)"
        );
        $stmt->execute([
            'user_id'     => $userId,
            'action_type' => $actionType,
            'message'     => $message,
            'ip_address'  => $ip,
        ]);
    }
}
