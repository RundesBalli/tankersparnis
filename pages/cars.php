<?php
/**
 * cars.php
 * 
 * Seite zum Bearbeiten der eigenen Kraftfahrzeuge
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Titel und Überschrift
 */
$title = "KFZs";
$content.= "<h1><span class='fas icon'>&#xf1b9;</span>KFZs</h1>";

/**
 * Fahrzeug Übersicht
 */
$content.= "<h2><span class='fas icon'>&#xf5e4;</span>Deine KFZs</h2>";
$result = mysqli_query($dbl, "SELECT `cars`.*, `fuels`.`name` AS `fuel`, `fuelsCompare`.`name` AS `fuelCompare` FROM `cars` JOIN `fuels` ON `fuels`.`id`=`cars`.`fuel` JOIN `fuelsCompare` ON `fuelsCompare`.`id`=`cars`.`fuelCompare` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) > 0) {
  $content.= "<section>";
  $content.= "<div class='row bold breakWord'>".
    "<div class='col-s-12 col-l-4'>Bezeichnung</div>".
    "<div class='col-s-6 col-l-3'>getankter Kraftstoff</div>".
    "<div class='col-s-6 col-l-3'>Vergleichskraftstoff</div>".
    "<div class='col-s-12 col-l-2'>Aktion</div>".
  "</div>";
  while($row = mysqli_fetch_assoc($result)) {
    $content.= "<div class='row hover'>".
      "<div class='col-s-12 col-l-4'>".output($row['name'])."</div>".
      "<div class='col-s-6 col-l-3'>".output($row['fuel'])."</div>".
      "<div class='col-s-6 col-l-3'>".output($row['fuelCompare'])."</div>".
      "<div class='col-s-12 col-l-2'><a class='noUnderline' href='/deleteCar?id=".output($row['id'])."'><span class='far icon'>&#xf2ed;</span></a> <a class='noUnderline' href='/editCar?id=".output($row['id'])."'><span class='far icon'>&#xf044;</span></a></div>".
    "</div>";
  }
  $content.= "</section>";
} else {
  $content.= "<div class='infoBox'>Du hast noch keine KFZs hinzugefügt.</div>";
}
$content.= "<div class='spacer-m'></div>";
$content.= "<hr>";

/**
 * Fahrzeug hinzufügen
 */
$content.= "<h2><span class='far icon'>&#xf0fe;</span>KFZ hinzufügen</h2>";

$tabindex = 1;
$content.= "<form action='/addCar' method='post' autocomplete='off'>";
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
  $content.= "<section>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3'><label for='name'>Bezeichnung</label></div>".
  "<div class='col-s-12 col-l-9'><input type='text' name='name' id='name' placeholder='Bezeichnung des Fahrzeugs' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $result = mysqli_query($dbl, "SELECT * FROM `fuels` ORDER BY `name` ASC") OR DIE(MYSQLI_ERROR($dbl));
  $options = "<option value='' selected disabled hidden>-- Bitte auswählen --</option>";
  while($row = mysqli_fetch_assoc($result)) {
    $options.= "<option value='".output($row['id'])."'>".output($row['name'])."</option>";
  }
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3 breakWord'><label for='fuel'>getankter Kraftstoff</label></div>".
  "<div class='col-s-12 col-l-9'><select name='fuel' id='fuel' tabindex='".$tabindex++."' required>".$options."</select></div>".
  "</div>";
  $result = mysqli_query($dbl, "SELECT * FROM `fuelsCompare` ORDER BY `sortIndex` ASC") OR DIE(MYSQLI_ERROR($dbl));
  $options = "<option value='' selected disabled hidden>-- Bitte auswählen --</option>";
  while($row = mysqli_fetch_assoc($result)) {
    $options.= "<option value='".output($row['id'])."'>".output($row['name'])."</option>";
  }
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3 breakWord'><label for='fuelCompare'>Vergleichskraftstoff</label></div>".
  "<div class='col-s-12 col-l-9'><select name='fuelCompare' id='fuelCompare' tabindex='".$tabindex++."' required>".$options."</select></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3'><label for='submit'>Hinzufügen</label></div>".
  "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Hinzufügen' tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "</section>";
$content.= "</form>";
?>
