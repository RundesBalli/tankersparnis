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
 * E-Mail Adresse
 */
$content.= "<h2>E-Mail Adresse</h2>";
  $content.= "<form action='/changeSettings' method='post'>";
  /**
   * Sitzungstoken
   */
  $content.= "<input type='hidden' name='token' value='".$sessionhash."'>";
  $content.= "<input type='hidden' name='action' value='changeEmail'>";
  $content.= "<section>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'>Derzeitige E-Mail Adresse</div>".
  "<div class='col-s-12 col-l-8'>".output($email)."</div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'><label for='email'>neue E-Mail Adresse</label></div>".
  "<div class='col-s-12 col-l-8'><input type='email' name='email' id='email' placeholder='E-Mail Adresse' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'><label for='email1'>neue E-Mail Adresse bestätigen</label></div>".
  "<div class='col-s-12 col-l-8'><input type='email' name='email1' id='email' placeholder='E-Mail Adresse' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'><label for='password'>derzeitiges Passwort</label></div>".
  "<div class='col-s-12 col-l-8'><input type='password' name='password' id='password' minlength='8' placeholder='Passwort' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-4'>Ändern</div>".
  "<div class='col-s-12 col-l-8'><input type='submit' name='submit' value='Ändern' tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row small'>".
  "<div class='col-s-12 col-l-4 highlight'>Achtung</div>".
  "<div class='col-s-12 col-l-8'>Du wirst auf allen Geräten ausgeloggt und deine neue E-Mail Adresse muss zuerst bestätigt werden, bevor du dich erneut einloggen kannst.</div>".
  "</div>";
  $content.= "</section>";
  $content.= "</form>";
$content.= "<div class='spacer-m'></div>";

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
