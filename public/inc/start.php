<?php
/**
 * start.php
 * 
 * Startseite
 */
$title = "Startseite";

/**
 * Prüfen ob eingeloggt. Wenn ja, dann Umleitung auf Nutzerseite. Prüfung auf Validität erfolgt über die Userseite.
 */
if(!empty($_COOKIE[$cookieName]) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  header("Location: /entry");
  die();
}

$content.= "<h1><span class='fas icon'>&#xf52f;</span>Herzlich willkommen</h1>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>Herzlich willkommen auf tankersparnis.net</div>".
"</div>";

$content.= "<h2>Was ist das hier?</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>Wenn du ein Auto mit LPG, LNG oder CNG fährst, dann kannst du auf tankersparnis.net deine Verbräuche eintragen um zu erfahren, wie viel Geld du gegenüber herkömmlichem Kraftstoff eingespart hast.</div>".
  "<div class='col-s-12 col-l-12'>Außerdem hast du die Möglichkeit verschiedene Auswertungen anzusehen um z.B. deine monatlichen Verbräuche und Kosten im Auge zu behalten.</div>".
"</div>";

$content.= "<h2>Was kostet das?</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'>tankersparnis.net ist natürlich komplett kostenfrei und kommt ohne Werbung, Premium Funktionen oder versteckte Kosten aus. Das wird auch in Zukunft so bleiben, versprochen!</div>".
  "<div class='col-s-12 col-l-12'>Deine Daten werden nicht verkauft und die Aussage <span class='italic'>«Wenn es kostenlos ist, bist du die Ware»</span> gilt hier nicht, da es sich um ein reines Hobbyprojekt handelt, welches der Autor auch für sich selbst nutzt.</div>".
"</div>";
?>
