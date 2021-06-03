<?php
/**
 * config.php
 * 
 * Konfigurationsdatei
 */

/**
 * MySQL-Datenbank
 * @param string Hostname
 * @param string Username
 * @param string Passwort
 * @param string Datenbank
 */
$dbl = mysqli_connect(
  "localhost",
  "",
  "",
  ""
) OR DIE(MYSQLI_ERROR($dbl));
mysqli_set_charset($dbl, "utf8") OR DIE(MYSQLI_ERROR($dbl));

/**
 * Login Cookie
 * @var string Name des Cookies
 */
$cookieName = "";
?>
