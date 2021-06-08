<?php
/**
 * settings.php
 * 
 * Einstellungsseite
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Einstellungen";
$content.= "<h1><span class='fas icon'>&#xf013;</span>Einstellungen</h1>";

$tabindex = 1;

/**
 * Passwort
 */
$content.= "<h2>Passwort</h2>";
  $content.= "<form action='/changeSettings' method='post'>";
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
  $content.= "<input type='hidden' name='action' value='changePassword'>";
  $content.= "<section>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'><label for='password'>derzeitiges Passwort</label></div>".
  "<div class='col-s-12 col-l-8'><input type='password' name='password' id='password' minlength='8' placeholder='Passwort' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'><label for='passwordNew'>neues Passwort<br><span class='small'>min. 8 Zeichen</span></label></div>".
  "<div class='col-s-12 col-l-8'><input type='password' name='passwordNew' id='passwordNew' minlength='8' placeholder='Passwort' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'><label for='passwordNew1'>neues Passwort bestätigen</label></div>".
  "<div class='col-s-12 col-l-8'><input type='password' name='passwordNew1' id='passwordNew1' minlength='8' placeholder='Passwort' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'>Ändern</div>".
  "<div class='col-s-12 col-l-8'><input type='submit' name='submit' value='Ändern' tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row small'>".
  "<div class='col-s-12 col-l-4 highlight'>Hinweis</div>".
  "<div class='col-s-12 col-l-8'>Du wirst auf allen Geräten ausgeloggt.</div>".
  "</div>";
  $content.= "</section>";
  $content.= "</form>";
?>
