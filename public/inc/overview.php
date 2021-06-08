<?php
/**
 * overview.php
 * 
 * Übersichtsseite
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Übersicht";
$content.= "<h1><span class='fas icon'>&#xf0cb;</span>Übersicht</h1>".PHP_EOL;

/**
 * Allgemeine Infos
 */
$content.= "<div class='row'>".PHP_EOL.
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Eingeloggt als: <span class='highlight'>".output($email)."</span> - (<a href='/logout'>Ausloggen</a>)</div>".PHP_EOL.
"</div>".PHP_EOL;
$content.= "<div class='spacer-m'></div>".PHP_EOL;


?>
