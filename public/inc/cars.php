<?php
/**
 * cars.php
 * 
 * Seite zum Bearbeiten der eigenen Kraftfahrzeuge
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "KFZs";
$content.= "<h1><span class='fas icon'>&#xf1b9;</span>KFZs</h1>";
/**
 * Fahrzeug hinzufügen
 */
$content.= "<h2><span class='far icon'>&#xf0fe;</span>KFZ hinzufügen</h2>";

$tabindex = 1;
$content.= "<form action='/addCar' method='post'>";
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
  $content.= "<section>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3'><label for='name'>Bezeichnung</label></div>".
  "<div class='col-s-12 col-l-9'><input type='text' name='name' id='name' placeholder='Bezeichnung des Fahrzeugs' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $result = mysqli_query($dbl, "SELECT * FROM `fuels` ORDER BY `name` ASC") OR DIE(MYSQLI_ERROR($dbl));
  $options = "<option value='' selected disabled hidden>-- Bitte auswählen --</option>";
  while($row = mysqli_fetch_array($result)) {
    $options.= "<option value='".output($row['id'])."'>".output($row['name'])."</option>";
  }
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3 breakWord'><label for='fuel'>getankter Kraftstoff</label></div>".
  "<div class='col-s-12 col-l-9'><select name='fuel' id='fuel' tabindex='".$tabindex++."' required>".$options."</select></div>".
  "</div>";
  $result = mysqli_query($dbl, "SELECT * FROM `fuelsCompare` ORDER BY `name` ASC") OR DIE(MYSQLI_ERROR($dbl));
  $options = "<option value='' selected disabled hidden>-- Bitte auswählen --</option>";
  while($row = mysqli_fetch_array($result)) {
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
