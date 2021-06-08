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
 * Zeitzoneneinstellung
 * @param string Zeitzone
 */
date_default_timezone_set("Europe/Berlin");

/**
 * Login Cookie
 * @var string Name des Cookies
 */
$cookieName = "";

/**
 * Mail Einstellungen
 * @var string Hostname des Mailservers
 * @var int Port des Mailservers
 * @var string SMTP Username
 * @var string SMTP Passwort
 * 
 * @var string Angezeigte "Von:" E-Mail Adresse
 * @var string Angezeigter "Von:" Name
 * @var string Abschließende Grußformel am Ende jeder E-Mail
 */
$mailConfig['conn']['host'] = "mail.example.com";
$mailConfig['conn']['port'] = 587;
$mailConfig['conn']['smtpUser'] = "noreply@example.com";
$mailConfig['conn']['smtpPass'] = "";

$mailConfig['conf']['fromEmail'] = "noreply@example.com";
$mailConfig['conf']['fromName'] = "Example.com";
$mailConfig['conf']['closingGreeting'] = "Viele Grüße\nDein Team von Example.com\nWeb: https://example.com - E-Mail: info@example.com";

/**
 * Mail Betreffe
 * @var string Betreff der Registrierungsmail
 * @var string Betreff der Aktivierungsmail (nach Klicken des Aktivierungslinks in der Registrierungsmail)
 * @var string Betreff der "Passwort wurde zurückgesetzt" Mail
 * @var string Betreff der "Passwort wurde geänder" Mail
 */
$mailConfig['subject']['register'] = "Deine Registrierung";
$mailConfig['subject']['accountActivated'] = "Dein Account wurde aktiviert";
$mailConfig['subject']['passwordResetted'] = "Passwort wurde zurückgesetzt";
$mailConfig['subject']['passwordChanged'] = "Passwort wurde geändert";

?>
