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

// On détecte si on est sur l'adresse IP locale
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost') {
    // CONFIGURATION LOCALHOST (Docker)
    $GLOBALS["db_host"]     = $_ENV['DEV_DB_HOST'] ?? 'mysql-server';
    $GLOBALS["db_username"] = $_ENV['DEV_DB_USER'] ?? 'root';
    $GLOBALS["db_password"] = $_ENV['DEV_DB_PASS'] ?? 'root';
    $GLOBALS["db_name"]     = $_ENV['DEV_DB_NAME'] ?? 'Miamy';
    $GLOBALS["url"]         = $_ENV['DEV_URL']     ?? 'http://localhost/Miamy';
    $GLOBALS["dev"]         = true;
} else {
    // CONFIGURATION PRODUCTION (o2switch)
    $GLOBALS["db_host"]     = $_ENV['PROD_DB_HOST'] ?? 'localhost';
    $GLOBALS["db_username"] = $_ENV['PROD_DB_USER'] ?? '';
    $GLOBALS["db_password"] = $_ENV['PROD_DB_PASS'] ?? '';
    $GLOBALS["db_name"]     = $_ENV['PROD_DB_NAME'] ?? '';
    $GLOBALS["url"]         = $_ENV['PROD_URL']     ?? 'https://miamy.fr';
    $GLOBALS["dev"]         = false;
}

$GLOBALS["base_salt"] = $_ENV['BASE_SALT'] ?? '';
// ... reste de tes clés Stripe etc.
