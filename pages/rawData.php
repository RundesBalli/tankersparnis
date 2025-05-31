<?php
/**
 * rawData.php
 * 
 * Infoseite für Einträge. Anzeige der Rohdaten.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Titel und Überschrift
 */
$title = "Rohdaten";
$content.= "<h1><span class='far icon'>&#xf2ed;</span>Rohdaten</h1>";

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
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Zurück zum Formular</a></div>".
    "</div>";
  } else {
    /**
     * Eintrag existiert
     */
    $row = mysqli_fetch_assoc($result);
    $raw = json_decode($row['raw'], TRUE);
    if(json_last_error() == JSON_ERROR_NONE) {
      unset($row['raw']);
    }
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Die Rohdaten des Eintrages sind:</div>".
      "<div class='col-s-12 col-l-12'><pre>".var_export($row, true)."</pre></div>".
      (!empty($raw) ? "<div class='col-s-12 col-l-12'><pre>".var_export($raw, true)."</pre></div>" : NULL).
    "</div>";
    $content.= "<div class='spacer-m'></div>";
    $content.= "<h2>Hinweis</h2>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Sollten noch Daten der Tankstelle gespeichert sein, dient dies ausschließlich für eventuelle Fehlersuche. Die Daten werden nach einiger Zeit gelöscht und nur der Preis wird beibehalten.</div>".
    "</div>";
  }
}
?>
