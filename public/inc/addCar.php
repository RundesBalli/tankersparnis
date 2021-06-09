<?php
/**
 * addCar.php
 * 
 * Seite zum Hinzufügen eines neuen Fahrzeuges
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "KFZ Hinzufügen";
$content.= "<h1><span class='far icon'>&#xf0fe;</span>KFZ hinzufügen</h1>";

if(empty($_POST['token'])) {
  /**
   * Es wurde kein Token übergeben.
   */
  http_response_code(403);
  $content.= "<div class='warnbox'>Es wurde kein Token übergeben.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></div>".
  "</div>";
} elseif($_POST['token'] != $sessionhash) {
  /**
   * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
   */
  http_response_code(403);
  $content.= "<div class='warnbox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></div>".
  "</div>";
} elseif(empty($_POST['name']) OR empty($_POST['fuel']) OR empty($_POST['fuelCompare']) OR !is_numeric($_POST['fuel']) OR !is_numeric($_POST['fuelCompare'])) {
  /**
   * Wenigstens eins der übergebenen Felder ist leer oder die Kraftstoffarten sind keine IDs.
   */
  http_response_code(403);
  $content.= "<div class='warnbox'>Du musst eine Bezeichnung und beide Kraftstoffarten angeben.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></div>".
  "</div>";
} else {
  /**
   * Alle Felder wurden übergeben. Nun müssen die übergebenen Kraftstoffarten geprüft werden.
   */
  $name = defuse($_POST['name']);
  $fuel = defuse(intval($_POST['fuel']));
  $fuelCompare = defuse(intval($_POST['fuelCompare']));
  $ok = 1;
  $result = mysqli_query($dbl, "SELECT `id` FROM `fuels` WHERE `id`=".$fuel." LIMIT 1") OR DIE(MYSQL_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    /**
     * Die übergebene Kraftstoffart ist ungültig.
     */
    $ok = 0;
    http_response_code(403);
    $content.= "<div class='warnbox'>Du musst eine gültige Kraftstoffart angeben.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></div>".
    "</div>";
  }
  $result = mysqli_query($dbl, "SELECT `id` FROM `fuelsCompare` WHERE `id`=".$fuelCompare." LIMIT 1") OR DIE(MYSQL_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    /**
     * Die übergebene Kraftstoffart ist ungültig.
     */
    $ok = 0;
    http_response_code(403);
    $content.= "<div class='warnbox'>Du musst eine gültige Kraftstoffart angeben.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></div>".
    "</div>";
  }
  if($ok == 1) {
    mysqli_query($dbl, "INSERT INTO `cars` (`userId`, `name`, `fuel`, `fuelCompare`) VALUES (".$userId.", '".$name."', ".$fuel.", ".$fuelCompare.")") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_affected_rows($dbl) == 1) {
      userLog($userId, 6, "KFZ angelegt: `".$name."`, `".$fuel."`, `".$fuelCompare."`");
      $content.= "<div class='successbox'>Das Fahrzeug wurde erfolgreich angelegt.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Zurück zur Fahrzeug Übersicht</a></div>".
      "</div>";
    } else {
      http_response_code(403);
      $content.= "<div class='warnbox'>Das Fahrzeug konnte nicht angelegt werden. Bitte wende dich an den <a href='/imprint'>Plattformbetreiber</a>.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></div>".
      "</div>";
    }
  }
}
?>
