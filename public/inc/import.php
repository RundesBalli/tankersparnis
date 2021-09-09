<?php
/**
 * import.php
 * 
 * Infoseite zum Datenimport von anderen Seiten.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Importieren";
$content.= "<h1><span class='fas icon'>&#xf56f;</span>Importieren</h1>";

$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>Es besteht die Möglichkeit, Daten von einer anderen Plattform hier her zu importieren.</div>".
  "<div class='col-s-12 col-l-12'>Solltest du die Möglichkeit haben einen Datendump/Export von der anderen Seite herunterzuladen, dann kannst du ihn an <a href='mailto:info@tankersparnis.net?subject=Datenimport'>info@tankersparnis.net</a> senden.</div>".
  "<div class='col-s-12 col-l-12'>Bitte achte darauf, dass du die E-Mail von der Mail-Adresse schickst, mit der du hier registriert bist.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

$content.= "<h2>Bisher unterstützte Imports:</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>CSV Export von Spritmonitor.de</div>".
  "<div class='col-s-12 col-l-12 small italic'><span class='highlight bold'>Hinweis:</span> Sollte deine Plattform nicht aufgeführt sein, dann macht das nichts. Schicke uns einfach den Export und wir versuchen es zu importieren.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

?>
