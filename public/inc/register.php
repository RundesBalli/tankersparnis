<?php
/**
 * register.php
 * 
 * Registrierungsseite
 */
$title = "Registrieren";

if((isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  header("Location: /logout");
  die();
}

$content.= "<h1>Registrieren</h1>".PHP_EOL;

?>
