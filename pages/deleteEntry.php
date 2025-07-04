<?php
/**
 * deleteCar.php
 * 
 * Löschseite für Einträge
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Titel und Überschrift
 */
$title = "Eintrag löschen";
$content.= "<h1><span class='far icon'>&#xf2ed;</span>Eintrag löschen</h1>";

if(!empty($_GET['id']) AND !is_numeric($_GET['id'])) {
  /**
   * Wenn der id Parameter leer ist oder gar nicht gesendet wurde, wird der Nutzer
   * auf die Eintragsseite zurückgeleitet.
   */
  header("Location: /entry");
  die();
} else {
  /**
   * ID wurde übergeben
   */
  $id = intval(defuse($_GET['id']));
  $result = mysqli_query($dbl, "SELECT `entries`.* FROM `entries` WHERE `entries`.`userId`=".$userId." AND `entries`.`id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 0) {
    /**
     * Eintrag existiert nicht
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Es existiert kein Eintrag mit dieser ID oder er ist nicht deinem Nutzerkonto zugewiesen.</div>";
    $content.= "<p><a href='/entry'><span class='far icon'>&#xf044;</span>Zurück zum Formular</a></p>";
  } else {
    /**
     * Eintrag existiert
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
        $content.= "<p>Möchtest du die Ersparnis <span class='highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€ vom ".date("d.m.Y, H:i", strtotime($row['timestamp']))." wirklich löschen?</span></p>";
        $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-3'>Löschen</div>".
        "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Ja, wirklich löschen' tabindex='".$tabindex++."'></div>".
        "</div>";
        $content.= "<div class='row small'>".
        "<div class='col-s-12 col-l-3 highlight'>Achtung</div>".
        "<div class='col-s-12 col-l-9'>Dieser Vorgang kann nicht rückgängig gemacht werden.</div>".
        "</div>";
        $content.= "</section>";
      $content.= "</form>";
      $content.= "<div class='spacer-m'></div>";
      $content.= "<p><a href='/entry'><span class='far icon'>&#xf044;</span>Nein, nicht löschen</a></p>";
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
        $content.= "<p><a href='/deleteEntry?id=".output($id)."'><span class='far icon'>&#xf2ed;</span>Erneut versuchen</a></p>";
      } elseif($_POST['token'] != $sessionhash) {
        /**
         * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
         */
        http_response_code(403);
        $content.= "<div class='warnBox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
        $content.= "<p><a href='/deleteEntry?id=".output($id)."'><span class='far icon'>&#xf2ed;</span>Erneut versuchen</a></p>";
      } else {
        /**
         * Das Formular wurde bestätigt.
         */
        mysqli_query($dbl, "DELETE FROM `entries` WHERE `userId`=".$userId." AND `id`=".$id." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_affected_rows($dbl) == 1) {
          userLog($userId, 4, "Eintrag gelöscht: `".number_format($row['moneySaved'], 2, ",", ".")."€`, `".date("d.m.Y, H:i", strtotime($row['timestamp']))."`");
          $content.= "<div class='successBox'>Der Eintrag wurde erfolgreich gelöscht.</div>";
          $content.= "<p><a href='/entry'><span class='far icon'>&#xf044;</span>Zurück zum Formular</a></p>";
        } else {
          http_response_code(403);
          $content.= "<div class='warnBox'>Das Fahrzeug konnte nicht gelöscht werden. Bitte wende dich an den <a href='/imprint'>Plattformbetreiber</a>.</div>";
          $content.= "<p><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</a></p>";
        }
      }
    }
  }
}
?>
