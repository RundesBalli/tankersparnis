<?php
/**
 * pwforget.php
 * 
 * Passwort vergessen Funktion
 */
$title = "Passwort vergessen";

$content.= "<h1><span class='fas icon'>&#xf084;</span>Passwort vergessen</h1>".PHP_EOL;

/**
 * Prüfen ob eingeloggt. Wenn ja, dann Umleitung auf Nutzerseite. Prüfung auf Validität erfolgt über die Userseite.
 */
if(empty($_COOKIE[$cookieName]) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  header("Location: /overview");
  die();
}


?>
