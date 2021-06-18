<?php
/**
 * logout.php
 * 
 * Seite zum Löschen der Sitzung und um den Cookie zu leeren.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel
 */
$title = "Logout";
$content.= "<h1>Logout</h1>";

if(!isset($_POST['submit'])) {
  /**
   * Formular wird angezeigt
   */
  $content.= "<form action='/logout' method='post'>";
    $content.= "<section>";
      /**
       * Sitzungstoken
       */
      $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
      /**
       * Auswahl
       */
      $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Möchtest du dich ausloggen?</div>".
      "<div class='col-s-12 col-l-12'><input type='radio' id='killAll-1' name='killAll' value='1'><label for='killAll-1'>Alle Sitzungen, auf allen Geräten beenden</label><br><input type='radio' id='killAll-0' name='killAll' value='0' checked><label for='killAll-0'>Nur diese Sitzung beenden</label></div>".
      "<div class='col-s-12 col-l-12'><input type='submit' name='submit' value='Ja'></div>".
      "</div>";
    $content.= "</section>";
  $content.= "</form>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Zurück zum Formular</a></div>".
  "</div>";
} else {
  /**
   * Formular abgesendet
   */
  /**
   * Sitzungstoken
   */
  if($_POST['token'] != $sessionhash) {
    http_response_code(403);
    $content.= "<div class='warnbox'>Ungültiges Token.</div>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Zurück zum Formular</a></div>".
    "</div>";
  } else {
    /**
     * Löschen der Sitzung.
     */
    if($_POST['killAll'] == 1) {
      $where = "`userId`=".$userId;
    } else {
      $where = "`hash`='".$sessionhash."'";
    }
    mysqli_query($dbl, "DELETE FROM `sessions` WHERE ".$where) OR DIE(MYSQLI_ERROR($dbl));
    userLog($userId, 1, "Logout, ".($_POST['killAll'] == 1 ? "alle Sitzungen" : "Einzelsitzung"));
    /**
     * Entfernen des Cookies und Umleitung zur Loginseite.
     */
    setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
    header("Location: /login");
    die();
  }
}
?>
