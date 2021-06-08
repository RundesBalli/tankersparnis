<?php
/**
 * pwforget.php
 * 
 * Passwort zurücksetzen Funktion
 */
$title = "Passwort zurücksetzen";

/**
 * Kein Cookie gesetzt oder Cookie leer und Formular nicht übergeben.
 */
if(empty($_COOKIE[$cookieName]) AND !isset($_POST['submit'])) {
  $content.= "<h1><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</h1>".PHP_EOL;
  /**
   * Passwort zurücksetzen Formular
   */
  $content.= "<form action='/pwReset' method='post'>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-s-12 col-l-3'><label for='email'>E-Mail Adresse</label></div>".PHP_EOL.
  "<div class='col-s-12 col-l-9'><input type='email' name='email' id='email' placeholder='Name' autofocus required tabindex='1'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-s-12 col-l-3'>Passwort zurücksetzen</div>".PHP_EOL.
  "<div class='col-s-12 col-l-9'><input type='submit' name='submit' value='Passwort zurücksetzen' tabindex='2'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "</form>".PHP_EOL;
} elseif(empty($_COOKIE[$cookieName]) AND isset($_POST['submit'])) {
  /**
   * Kein Cookie gesetzt oder Cookie leer und Formular wurde übergeben.
   */
  } else {
    /**
     * Wenn keine Übereinstimmung vorliegt, dann wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
     */
    http_response_code(403);
    $content.= "<h1><span class='fas icon'>&#xf071;</span>Passwort zurücksetzen gescheitert</h1>".PHP_EOL;
    $content.= "<div class='warnbox'>Es existiert kein Nutzerkonto mit der E-Mail Adresse.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-s-12 col-l-12'><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Mit anderer E-Mail Adresse versuchen</a><br><a href='/register'><span class='far icon'>&#xf044;</span>Neues Nutzerkonto registrieren</a></div>".PHP_EOL.
    "</div>".PHP_EOL;
  }
} else {
  /**
   * Wenn bereits ein Cookie gesetzt ist wird auf die Übersichts-Seite weitergeleitet.
   */
  header("Location: /overview");
  die();
}

?>
