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
$content.= "<h1><span class='fas icon'>&#xf0cb;</span>Übersicht</h1>";

/**
 * Allgemeine Infos
 */
$content.= "<div class='row'>".
"<div class='col-x-12 col-s-12 col-m-12 col-l-12 col-xl-12'>Eingeloggt als: <span class='highlight'>".output($email)."</span> - (<a href='/logout'>Ausloggen</a>)</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

/**
 * Eintrag hinzufügen
 */
$tabindex = 1;
$content.= "<h2><span class='far icon'>&#xf044;</span>Eintrag hinzufügen</h2>";

$result = mysqli_query($dbl, "SELECT `cars`.*, `fuels`.`name` AS `fuel` FROM `cars` JOIN `fuels` ON `fuels`.`id`=`cars`.`fuel` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) > 0) {
  $content.= "<form action='/addEntry' method='post'>";
    $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
    if(mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_array($result);
      $content.= "<input type='hidden' name='car' value='".output($row['id'])."'>";
      $cars = output($row['name'])." - ".output($row['fuel']);
    } else {
      $cars = "<select name='car' id='car' tabindex='".$tabindex++."' required><option value='' selected disabled hidden>-- Bitte auswählen --</option>";
      while($row = mysqli_fetch_array($result)) {
        $cars.= "<option value='".output($row['id'])."'>".output($row['name'])." - ".output($row['fuel'])."</option>";
      }
      $cars.= "</select>";
    }
    $content.= "<section>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='car'>Fahrzeug</label></div>".
    "<div class='col-s-12 col-l-9'>".$cars."</div>".
    "</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='fuel'>Gas Liter/kg</label></div>".
    "<div class='col-s-12 col-l-9'><input type='number' name='fuel' id='fuel' step='0.01' min='0.01' tabindex='".$tabindex++."' required placeholder='Getankte Menge Gas'></div>".
    "</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='range'>Reichweite</label></div>".
    "<div class='col-s-12 col-l-9'><input type='number' name='range' id='range' step='0.1' min='0.1' tabindex='".$tabindex++."' required placeholder='Gefahrene Kilometer'></div>".
    "</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='cost'>Kosten in €</label></div>".
    "<div class='col-s-12 col-l-9'><input type='number' name='cost' id='cost' step='0.01' min='0.01' tabindex='".$tabindex++."' required placeholder='Für diesen Tankvorgang bezahlt'></div>".
    "</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='geoButton'>Standort<br><span class='small'>Optional</span></label></div>".
    "<div class='col-s-12 col-l-9'><input type='button' id='geoButton' value='Standort ermitteln' tabindex='".$tabindex++."'><br><span class='small'>Dein genauer Standort wird nicht gespeichert, sondern nur die Tankstelle im Umkreis von 15km, die am günstigsten ist.<br>Dein Standort wird ohne Zuweisung zu deiner Person an unseren Kraftstoffpreis Dienstleister <a href='https://creativecommons.tankerkoenig.de/' target='_blank' rel='noopener'>Tankerkönig</a> gesendet.</span></div>".
    "</div>";
    $content.= "<script type='text/javascript' src='/src/geolocation.js'></script>";
    $content.= "<input type='hidden' name='geo' id='geo' value=''>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='submit'>Hinzufügen</label></div>".
    "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Hinzufügen' tabindex='".$tabindex++."'></div>".
    "</div>";
    $content.= "</section>";
  $content.= "</form>";
} else {
  $content.= "<div class='infobox'>Du hast noch keine KFZs hinzugefügt.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>KFZ hinzufügen</a></div>".
  "</div>";
}
$content.= "<div class='spacer-m'></div>";
  "</div>";
  $content.= "</section>";
?>
