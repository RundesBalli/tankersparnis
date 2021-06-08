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
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
  /**
   * Auswahl
   */
  $content.= "<div class='row hover bordered'>".
  "<div class='col-s-12 col-l-12'>Möchtest du dich ausloggen?</div>".
  "<div class='col-s-12 col-l-12'><input type='submit' name='submit' value='Ja'></div>".
  "</div>";
  $content.= "</form>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/overview'><span class='fas icon'>&#xf0cb;</span>Zurück zur Übersicht</a></div>".
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
    "<div class='col-s-12 col-l-12'><a href='/overview'>Zurück zur Übersicht</a></div>".
    "</div>";
  } else {
    /**
     * Löschen der Sitzung.
     */
    mysqli_query($dbl, "DELETE FROM `sessions` WHERE `hash`='".$match[0]."'") OR DIE(MYSQLI_ERROR($dbl));
    userLog($userId, 1, "Logout");
    /**
     * Entfernen des Cookies und Umleitung zur Loginseite.
     */
    setcookie($cookieName, NULL, 0);
    header("Location: /login");
    die();
  }
}
?>
