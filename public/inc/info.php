<?php
/**
 * info.php
 * 
 * Informationsseite
 */
$title = "Informationsseite";

$content.= "<h1><span class='fas icon'>&#xf128;</span>Informationsseite</h1>";

$content.= "<h2>Aufbau</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>Das Portal ist intuitiv aufgebaut und ermöglicht einfache KFZ Datenpflege und Eintragungen der Ersparnisse.</div>".
  "<div class='col-s-12 col-l-12 center'><a href='/img/entry.png' target='_blank'><img src='/img/entry.png' alt='Eintragen' class='bordered'></a></div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

$content.= "<h2>Was wird ausgerechnet?</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>Der Heizwert des getankten Gases wird auf den Heizwert von Benzin oder Diesel umgerechnet. Die Menge an fossilem Kraftstoff, wird dann mit aktuellen Spritpreisen (wahlweise standortabhängig oder deutschlandweit) gegengerechnet. Die aktuellen Spritpreise bekommen wir von der <a href='https://creativecommons.tankerkoenig.de/' target='_blank' rel='noopener'><span class='fas icon'>&#xf52f;</span>Tankerkönig-API</a>. Daraus ergibt sich dann die Ersparnis. Durch diese Berechnung werden Mehrverbräuche (z.B. durch Autobahnfahrten) berücksichtigt.</div>".
  "<div class='col-s-12 col-l-12'>Interessierte und Programmierer können sich die genaue Berechnung <a href='https://github.com/RundesBalli/tankersparnis/blob/74ba896fffcf6bc16b819a5c95e2b4c5d78ad3b6/public/inc/addEntry.php#L147' target='_blank' rel='noopener'>hier</a> ansehen.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

$content.= "<h2>Statistiken</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>Es gibt übersichtliche Statistiken, die einfach aufgerufen werden können:</div>".
  "<div class='col-s-12 col-l-12 center'><a href='/img/totalMonthly.png' target='_blank'><img src='/img/totalMonthly.png' alt='Monatssummen' class='bordered'></a><br><span class='small'>* Beispielwerte</span></div>".
  "<div class='col-s-12 col-l-12 center'><a href='/img/totalSavings.png' target='_blank'><img src='/img/totalSavings.png' alt='Gesamtübersicht' class='bordered'></a><br><span class='small'>* Beispielwerte</span></div>".
"</div>";

?>
