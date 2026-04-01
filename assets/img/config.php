<?php
// On détecte si on est sur l'adresse IP locale
if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['HTTP_HOST'] == 'localhost') {

    // CONFIGURATION LOCALHOST (Docker)
    $GLOBALS["db_host"] = 'mysql-server';
    $GLOBALS["db_username"] = 'root';
    $GLOBALS["db_password"] = 'root';
    $GLOBALS["db_name"] = 'Miamy';
    $GLOBALS["url"] = 'http://localhost/Miamy';
    $GLOBALS["dev"] = true;
} else {
    // CONFIGURATION PRODUCTION (Le site en ligne)
    $GLOBALS["db_host"] = 'localhost';
    $GLOBALS["db_username"] = 'sc1feti6921_miamy';
    $GLOBALS["db_password"] = '*~{6bRl3OdLX';
    $GLOBALS["db_name"] = 'sc1feti6921_miamy';
    $GLOBALS["url"] = 'https://miamy.fr';
    $GLOBALS["dev"] = false;
}

$GLOBALS["base_salt"] = '6a6dd4a14f1a3d5943b41470c86752dc';
// ... reste de tes clés Stripe etc.
