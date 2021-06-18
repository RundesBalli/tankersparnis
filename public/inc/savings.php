<?php
/**
 * savings.php
 * 
 * Detailansicht der Ersparnisse
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Ersparnisse";
$content.= "<h1><span class='fas icon'>&#xf153;</span>Ersparnisse</h1>";

$content.= "<div class='row breakWord small'>".
  "<div class='col-s-12 col-l-0 bold highlight'>Hinweis!</div>".
  "<div class='col-s-12 col-l-0'>Für eine detailliertere Ansicht musst du diese Seite von einem Computer aus aufrufen oder in den \"Desktop-Modus\" in deinem Handybrowser wechseln!</div>".
  "<div class='col-s-12 col-l-0 spacer-m'></div>".
"</div>";

/**
 * Anzeigevarianten
 */
$content.= "<h2>Anzeigevarianten, unterteilt nach KFZs</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=total'>Gesamtansicht</a></div>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=monthly'>Monatswerte</a></div>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=maxSaved'>Am meisten gespart (Betrag)</a></div>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=minUsed'>Am wenigsten verbraucht (100km)</a></div>".
  "<div class='col-s-12 col-l-12 small'>Vorschläge für weitere Anzeigevarianten gerne per <a href='mailto:info@tankersparnis.net'>Mail</a> oder <a href='https://github.com/RundesBalli/tankersparnis/issues' target='_blank' rel='noopener'>Issue</a>.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

