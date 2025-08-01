<?php
/**
 * Nextcloud - sessiontoken
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Benjamin Sonntag <benjamin@octopuce.fr>
 * @copyright Benjamin Sonntag 2021
 * @author Nicolas Varlot <nicolas.varlot@ac-versailles.fr>
 * @copyright Nicolas Varlot 2025
 */

echo "\nThis scripts returns a random API key for sessiontoken and the Hashed version of it, that you should put in your config.php\n\n";

function randomapi() {
    $str="abcdefghjkmnpqrstuvwxyz23456789";
    $rand="";
    for($i=0;$i<32;$i++) $rand.=substr($str,rand(0,strlen($str)-1),1);
    return $rand;
}

$api=randomapi();
echo "Your sessiontoken API key (that you can use in your apps) is\n     ".$api."\n\n";
echo "The hashed version that you should add to your config.php is : \n";
echo "     'sessiontoken_apikey_hash' => '".password_hash($api,PASSWORD_DEFAULT)."',\n\n";

