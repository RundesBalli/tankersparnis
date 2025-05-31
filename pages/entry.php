<?php
/**
 * entry.php
 * 
 * Eintrag hinzufügen
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Eintrag hinzufügen";
$content.= "<h1><span class='far icon'>&#xf044;</span>Eintrag hinzufügen</h1>";

$tabindex = 1;

$result = mysqli_query($dbl, "SELECT `cars`.*, `fuels`.`name` AS `fuel` FROM `cars` JOIN `fuels` ON `fuels`.`id`=`cars`.`fuel` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) > 0) {
  $content.= "<form action='/addEntry' method='post' autocomplete='off'>";
    $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
    if(mysqli_num_rows($result) == 1) {
      $row = mysqli_fetch_assoc($result);
      $content.= "<input type='hidden' name='car' value='".output($row['id'])."'>";
      $cars = output($row['name'])." - ".output($row['fuel']);
    } else {
      $cars = "<select name='car' id='car' tabindex='".$tabindex++."' required><option value='' selected disabled hidden>-- Bitte auswählen --</option>";
      while($row = mysqli_fetch_assoc($result)) {
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
    $content.= "<script type='text/javascript' src='/assets/js/geolocation.js'></script>";
    $content.= "<input type='hidden' name='geo' id='geo' value=''>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='submit'>Hinzufügen</label></div>".
    "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Hinzufügen' tabindex='".$tabindex++."'></div>".
    "</div>";
    $content.= "</section>";
  $content.= "</form>";
} else {
  $content.= "<div class='infoBox'>Du hast noch keine KFZs hinzugefügt.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>KFZ hinzufügen</a></div>".
  "</div>";
}
$content.= "<div class='spacer-m'></div>";

/**
 * Einträge
 */
$result = mysqli_query($dbl, "SELECT `entries`.*, `cars`.`name` FROM `entries` JOIN `cars` ON `cars`.`id`=`entries`.`carId` WHERE `entries`.`userId`=".$userId." ORDER BY `entries`.`timestamp` DESC LIMIT 15") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) > 0) {
  $content.= "<hr>";
  $content.= "<h2><span class='fas icon'>&#xf0cb;</span>Die letzten 15 Einträge</h2>";
  $content.= "<div class='row breakWord small'>".
    "<div class='col-s-12 col-l-0 bold highlight'>Hinweis!</div>".
    "<div class='col-s-12 col-l-0'>Für eine detailliertere Ansicht musst du diese Seite von einem Computer aus aufrufen!</div>".
    "<div class='col-s-12 col-l-0 spacer-m'></div>".
  "</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'>Oder schau dir deine <a href='/savings'>ausführlichen Statistiken</a> an.</div>".
  "</div>";
  $content.= "<div class='spacer-m'></div>";
  $content.= "<section>";
  $content.= "<div class='row bold breakWord small'>".
    "<div class='col-s-6 col-l-2'>KFZ</div>".
    "<div class='col-s-6 col-l-2'>Zeitpunkt</div>".
    "<div class='col-s-0 col-l-1'>Getankt (l/kg)</div>".
    "<div class='col-s-0 col-l-1'>Reichweite</div>".
    "<div class='col-s-0 col-l-1'>Verbrauch auf 100km (l/kg)</div>".
    "<div class='col-s-0 col-l-1'>Preis</div>".
    "<div class='col-s-0 col-l-1'>Preis/100km</div>".
    "<div class='col-s-6 col-l-1'>eingespart</div>".
    "<div class='col-s-6 col-l-2'>Aktion</div>".
  "</div>";
  while($row = mysqli_fetch_assoc($result)) {
    $content.= "<div class='row hover breakWord small'>".
      "<div class='col-s-6 col-l-2'>".output($row['name'])."</div>".
      "<div class='col-s-6 col-l-2'>".date("d.m.Y, H:i", strtotime($row['timestamp']))." Uhr</div>".
      "<div class='col-s-0 col-l-1'>".number_format($row['fuelQuantity'], 2, ",", ".")."</div>".
      "<div class='col-s-0 col-l-1'>".number_format($row['range'], 1, ",", ".")."km</div>".
      "<div class='col-s-0 col-l-1'>".number_format(($row['fuelQuantity']/$row['range']*100), 1, ",", ".")."</div>".
      "<div class='col-s-0 col-l-1'>".number_format($row['cost'], 2, ",", ".")."€</div>".
      "<div class='col-s-0 col-l-1'>".number_format(($row['cost']/$row['range']*100), 2, ",", ".")."€</div>".
      "<div class='col-s-6 col-l-1 highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€</div>".
      "<div class='col-s-6 col-l-2'><a class='noUnderline' href='/deleteEntry?id=".output($row['id'])."'><span class='far icon'>&#xf2ed;</span></a></div>".
    "</div>";
  }
  $content.= "</section>";
}
?>
