<?php
/**
 * editCar.php
 * 
 * Änderungsseite für KFZs
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Titel und Überschrift
 */
$title = "KFZ ändern";
$content.= "<h1><span class='far icon'>&#xf044;</span>KFZ ändern</h1>";

if(!empty($_GET['id']) AND !is_numeric($_GET['id'])) {
  /**
   * Wenn der id Parameter leer ist oder gar nicht gesendet wurde, wird der Nutzer
   * auf die KFZ Seite zurückgeleitet.
   */
  header("Location: /cars");
  die();
} else {
  /**
   * ID wurde übergeben
   */
  $id = intval(defuse($_GET['id']));
  $result = mysqli_query($dbl, "SELECT `cars`.* FROM `cars` WHERE `userId`=".$userId." AND `cars`.`id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * KFZ existiert nicht
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Es existiert kein Fahrzeug mit dieser ID oder es ist nicht deinem Nutzerkonto zugewiesen.</div>";
    $content.= "<p><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
  } else {
    /**
     * KFZ existiert
     */
    $row = mysqli_fetch_assoc($result);
    if(empty($_POST['submit'])) {
      /**
       * Formular wurde noch nicht abgesendet
       */
      $tabindex = 1;
      $content.= "<form method='post'>";
        $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
        $content.= "<section>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-3'><label for='name'>Bezeichnung</label></div>".
        "<div class='col-s-12 col-l-9'><input type='text' name='name' id='name' placeholder='Bezeichnung des Fahrzeugs' value='".output($row['name'])."' required tabindex='".$tabindex++."'></div>".
        "</div>";
        $fuelResult = mysqli_query($dbl, "SELECT * FROM `fuels` ORDER BY `name` ASC") OR DIE(MYSQLI_ERROR($dbl));
        $options = "";
        while($fuelRow = mysqli_fetch_assoc($fuelResult)) {
          $options.= "<option value='".output($fuelRow['id'])."'".($row['fuel'] == $fuelRow['id'] ? " selected" : NULL).">".output($fuelRow['name'])."</option>";
        }
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-3 breakWord'><label for='fuel'>getankter Kraftstoff</label></div>".
        "<div class='col-s-12 col-l-9'><select name='fuel' id='fuel' tabindex='".$tabindex++."' required>".$options."</select></div>".
        "</div>";
        $fuelResult = mysqli_query($dbl, "SELECT * FROM `fuelsCompare` ORDER BY `name` ASC") OR DIE(MYSQLI_ERROR($dbl));
        $options = "";
        while($fuelRow = mysqli_fetch_assoc($fuelResult)) {
          $options.= "<option value='".output($fuelRow['id'])."'".($row['fuelCompare'] == $fuelRow['id'] ? " selected" : NULL).">".output($fuelRow['name'])."</option>";
        }
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-3 breakWord'><label for='fuelCompare'>Vergleichskraftstoff</label></div>".
        "<div class='col-s-12 col-l-9'><select name='fuelCompare' id='fuelCompare' tabindex='".$tabindex++."' required>".$options."</select></div>".
        "</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-3'><label for='submit'>Ändern</label></div>".
        "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Ändern' tabindex='".$tabindex++."'></div>".
        "</div>";
        $content.= "</section>";
      $content.= "</form>";
      $content.= "<div class='spacer-m'></div>";
      $content.= "<p><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Zurück zur Übersicht</a></p>";
    } else {
      /**
       * Formular wurde abgesendet
       */
      if(empty($_POST['token'])) {
        /**
         * Es wurde kein Token übergeben.
         */
        http_response_code(403);
        $content.= "<div class='warnBox'>Es wurde kein Token übergeben.</div>";
        $content.= "<p><a href='/editCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
      } elseif($_POST['token'] != $sessionhash) {
        /**
         * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
         */
        http_response_code(403);
        $content.= "<div class='warnBox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
        $content.= "<p><a href='/editCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
      } elseif(empty($_POST['name']) OR empty($_POST['fuel']) OR empty($_POST['fuelCompare']) OR !is_numeric($_POST['fuel']) OR !is_numeric($_POST['fuelCompare'])) {
        /**
         * Wenigstens eins der übergebenen Felder ist leer oder die Kraftstoffarten sind keine IDs.
         */
        http_response_code(403);
        $content.= "<div class='warnBox'>Du musst eine Bezeichnung und beide Kraftstoffarten angeben.</div>";
        $content.= "<p><a href='/editCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
      } else {
        /**
         * Alle Felder wurden übergeben. Nun müssen die übergebenen Kraftstoffarten geprüft werden.
         */
        $name = defuse($_POST['name']);
        $fuel = defuse(intval($_POST['fuel']));
        $fuelCompare = defuse(intval($_POST['fuelCompare']));
        $ok = 1;
        $result = mysqli_query($dbl, "SELECT `id` FROM `fuels` WHERE `id`=".$fuel." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) != 1) {
          /**
           * Die übergebene Kraftstoffart ist ungültig.
           */
          $ok = 0;
          http_response_code(403);
          $content.= "<div class='warnBox'>Du musst eine gültige Kraftstoffart angeben.</div>";
          $content.= "<p><a href='/editCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
        }
        $result = mysqli_query($dbl, "SELECT `id` FROM `fuelsCompare` WHERE `id`=".$fuelCompare." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) != 1) {
          /**
           * Die übergebene Kraftstoffart ist ungültig.
           */
          $ok = 0;
          http_response_code(403);
          $content.= "<div class='warnBox'>Du musst eine gültige Kraftstoffart angeben.</div>";
          $content.= "<p><a href='/editCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
        }
        if($ok == 1) {
          mysqli_query($dbl, "UPDATE `cars` SET `name`='".$name."', `fuel`=".$fuel.", `fuelCompare`=".$fuelCompare." WHERE `userId`=".$userId." AND `id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
          if(mysqli_affected_rows($dbl) == 1) {
            userLog($userId, 7, "KFZ geändert: `".$name."`, `".$fuel."`, `".$fuelCompare."`");
            $content.= "<div class='successBox'>Das Fahrzeug wurde erfolgreich bearbeitet.</div>";
            $content.= "<p><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Zurück zur Fahrzeug Übersicht</a></p>";
          } else {
            http_response_code(403);
            $content.= "<div class='infoBox'>Es fand keine Änderung statt.</div>";
            $content.= "<p><a href='/editCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
          }
        }
      }
    }
  }
}
?>
