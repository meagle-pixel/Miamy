<?php

// Chargement du fichier .env s'il existe
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Détection de l'environnement (local Docker vs production o2switch)
$isLocal = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost');

if ($isLocal) {
    // CONFIGURATION LOCALHOST (Docker)
    define('DB_HOST',     $_ENV['DEV_DB_HOST'] ?? 'mysql-server');
    define('DB_USERNAME', $_ENV['DEV_DB_USER'] ?? 'root');
    define('DB_PASSWORD', $_ENV['DEV_DB_PASS'] ?? 'root');
    define('DB_NAME',     $_ENV['DEV_DB_NAME'] ?? 'Miamy');
    define('APP_URL',     $_ENV['DEV_URL']     ?? 'http://localhost/Miamy');
    define('APP_DEV',     true);
} else {
    // CONFIGURATION PRODUCTION (o2switch)
    define('DB_HOST',     $_ENV['PROD_DB_HOST'] ?? 'localhost');
    define('DB_USERNAME', $_ENV['PROD_DB_USER'] ?? '');
    define('DB_PASSWORD', $_ENV['PROD_DB_PASS'] ?? '');
    define('DB_NAME',     $_ENV['PROD_DB_NAME'] ?? '');
    define('APP_URL',     $_ENV['PROD_URL']     ?? 'https://miamy.fr');
    define('APP_DEV',     false);
}

// Sel utilisé pour hasher les mots de passe (UserInsert + tryToConnect)
define('BASE_SALT', $_ENV['BASE_SALT'] ?? '');
