<?php
/**
 * deleteCar.php
 * 
 * Löschseite für KFZs
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Titel und Überschrift
 */
$title = "KFZ löschen";
$content.= "<h1><span class='far icon'>&#xf2ed;</span>KFZ löschen</h1>";

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
  $result = mysqli_query($dbl, "SELECT `cars`.`name`, `cars`.`fuel` AS `fuelId`, `cars`.`fuelCompare` AS `fuelCompareId`, `fuels`.`name` AS `fuel`, `fuelsCompare`.`name` AS `fuelCompare` FROM `cars` JOIN `fuels` ON `fuels`.`id`=`cars`.`fuel` JOIN `fuelsCompare` ON `fuelsCompare`.`id`=`cars`.`fuelCompare` WHERE `userId`=".$userId." AND `cars`.`id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
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
        "<div class='col-s-12 col-l-3'>Bezeichnung</div>".
        "<div class='col-s-12 col-l-9'>".output($row['name'])."</div>".
        "</div>";
        $content.= "<div class='row breakWord'>".
        "<div class='col-s-12 col-l-3'>getankter Kraftstoff</div>".
        "<div class='col-s-12 col-l-9'>".output($row['fuel'])."</div>".
        "</div>";
        $content.= "<div class='row breakWord'>".
        "<div class='col-s-12 col-l-3'>Vergleichskraftstoff</div>".
        "<div class='col-s-12 col-l-9'>".output($row['fuelCompare'])."</div>".
        "</div>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-3'>Löschen</div>".
        "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Ja, wirklich löschen' tabindex='".$tabindex++."'></div>".
        "</div>";
        $content.= "<div class='row small'>".
        "<div class='col-s-12 col-l-3 highlight'>Achtung</div>".
        "<div class='col-s-12 col-l-9'>Es werden alle Daten gelöscht! Dieser Vorgang kann nicht rückgängig gemacht werden.</div>".
        "</div>";
        $content.= "</section>";
      $content.= "</form>";
      $content.= "<div class='spacer-m'></div>";
      $content.= "<p><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Nein, nicht löschen</a></p>";
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
        $content.= "<p><a href='/deleteCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
      } elseif($_POST['token'] != $sessionhash) {
        /**
         * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
         */
        http_response_code(403);
        $content.= "<div class='warnBox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
        $content.= "<p><a href='/deleteCar?id=".output($id)."'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
      } else {
        /**
         * Das Formular wurde bestätigt.
         */
        mysqli_query($dbl, "DELETE FROM `cars` WHERE `userId`=".$userId." AND `id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_affected_rows($dbl) == 1) {
          userLog($userId, 8, "KFZ gelöscht: `".$row['name']."`, `".$row['fuelId']."`, `".$row['fuelCompareId']."`");
          $content.= "<div class='successBox'>Das Fahrzeug wurde erfolgreich gelöscht.</div>";
          $content.= "<p><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Zurück zur Fahrzeug Übersicht</a></p>";
        } else {
          http_response_code(403);
          $content.= "<div class='warnBox'>Das Fahrzeug konnte nicht gelöscht werden. Bitte wende dich an den <a href='/imprint'>Plattformbetreiber</a>.</div>";
          $content.= "<p><a href='/cars'><span class='fas icon'>&#xf1b9;</span>Erneut versuchen</a></p>";
        }
      }
    }
  }
}
?>
