<?php

if (!isset($argv[1])) {
    echo "Usage: hash-apikey.php <api password (32 characters minimum)>\n";
    echo "Will return the hash to add to config.php as sessiontoken_apikey_hash\n";
    exit();
}

echo password_hash($argv[1],PASSWORD_DEFAULT)."\n";
