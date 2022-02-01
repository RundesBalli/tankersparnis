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
 * Standardstil
 * @var string Darkmode oder Lightmode ('dark' / 'light')
 */
$defaultStyle = "dark";

/**
 * Cookie Bezeichnungen
 * @var string Name des Login-Cookies
 * @var string Name des Stil-Cookies
 */
$cookieName = "";
$styleName = "";

/**
 * Tankerkönig API-Key
 * @var string API-Key, Format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
 */
$tkApiKey = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";

/**
 * cURL Einstellungen
 * @var string Interface mit welchem cURL die Verbindung aufbauen soll, z.B. eth0 (sudo ifconfig)
 * @var string Der UserAgent mit dem die Anfrage gesendet werden soll
 */
$cURL['bindTo'] = "";
$cURL['userAgent'] = "";

/**
 * Mail Einstellungen
 * @var string Hostname des Mailservers
 * @var int Port des Mailservers
 * @var string SMTP Username
 * @var string SMTP Passwort
 * 
 * @var string Angezeigte "Von:" E-Mail Adresse
 * @var string Angezeigter "Von:" Name
 * @var string "Antworten an:" E-Mail Adresse
 * @var string Angezeigter "Antworten an:" Name
 * @var string Abschließende Grußformel am Ende jeder E-Mail
 */
$mailConfig['conn']['host'] = "mail.example.com";
$mailConfig['conn']['port'] = 587;
$mailConfig['conn']['smtpUser'] = "noreply@example.com";
$mailConfig['conn']['smtpPass'] = "";

$mailConfig['conf']['fromEmail'] = "noreply@example.com";
$mailConfig['conf']['fromName'] = "Example.com";
$mailConfig['conf']['replyToEmail'] = "info@example.com";
$mailConfig['conf']['replyToName'] = "Example.com";
$mailConfig['conf']['closingGreeting'] = "Viele Grüße\nDein Team von Example.com\nWeb: https://example.com - E-Mail: info@example.com";

/**
 * Mail Betreffe
 * @var string Betreff der Registrierungsmail
 * @var string Betreff der Aktivierungsmail (nach Klicken des Aktivierungslinks in der Registrierungsmail)
 * @var string Betreff der "Passwort wurde zurückgesetzt" Mail
 * @var string Betreff der "E-Mail Adresse wurde geändert" Mail
 * @var string Betreff der "Passwort wurde geänder" Mail
 */
$mailConfig['subject']['register'] = "Deine Registrierung";
$mailConfig['subject']['accountActivated'] = "Dein Account wurde aktiviert";
$mailConfig['subject']['passwordResetted'] = "Passwort wurde zurückgesetzt";
$mailConfig['subject']['emailChanged'] = "E-Mail Adresse wurde geändert";
$mailConfig['subject']['passwordChanged'] = "Passwort wurde geändert";

/**
 * Monatsnamen
 * @var array Monatsnamen
 */
$monthNames = array(
  1 => "Januar",
  2 => "Februar",
  3 => "März",
  4 => "April",
  5 => "Mai",
  6 => "Juni",
  7 => "Juli",
  8 => "August",
  9 => "September",
  10 => "Oktober",
  11 => "November",
  12 => "Dezember"
);
?>
