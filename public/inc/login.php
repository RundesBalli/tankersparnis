<?php
/**
 * login.php
 * 
 * Seite zum Einloggen in den Nutzerbereich.
 */
$title = "Login";

/**
 * Kein Cookie gesetzt oder Cookie leer und Formular nicht übergeben.
 */
if(empty($_COOKIE[$cookieName]) AND !isset($_POST['submit'])) {
  $content.= "<h1><span class='fas icon'>&#xf2f6;</span>Login</h1>".PHP_EOL;
  /**
   * Cookiewarnung
   */
  $content.= "<div class='infobox'>Ab diesem Punkt werden technisch notwendige Cookies verwendet! Mit dem Fortfahren stimmst du dem zu!</div>".PHP_EOL;
  /**
   * Loginformular
   */
  $content.= "<form action='/login' method='post'>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-s-12 col-l-3'><label for='email'>E-Mail Adresse</label></div>".PHP_EOL.
  "<div class='col-s-12 col-l-9'><input type='email' name='email' id='email' placeholder='E-Mail Adresse' autofocus required tabindex='1'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-s-12 col-l-3'><label for='password'>Passwort<br><span class='small'><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</a></span></label></div>".PHP_EOL.
  "<div class='col-s-12 col-l-9'><input type='password' name='password' id='password' minlength='8' placeholder='Passwort' required tabindex='2'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
  "<div class='col-s-12 col-l-3'>Einloggen</div>".PHP_EOL.
  "<div class='col-s-12 col-l-9'><input type='submit' name='submit' value='Einloggen' tabindex='3'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "</form>".PHP_EOL;
} elseif(empty($_COOKIE[$cookieName]) AND isset($_POST['submit'])) {
  /**
   * Kein Cookie gesetzt oder Cookie leer und Formular wurde übergeben.
   */
  /**
   * Entschärfen der Usereingaben.
   */
  $email = defuse($_POST['email']);
  /**
   * Abfragen ob eine Übereinstimmung in der Datenbank vorliegt.
   */
  $result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `email`='".$email."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) == 1) {
    /**
     * Wenn der User existiert, muss der Passworthash validiert werden.
     */
    $row = mysqli_fetch_array($result);
    if(password_verify($_POST['password'].$row['salt'], $row['password'])) {
      if($row['active'] == 1) {
        /**
         * Wenn das Passwort verifiziert werden konnte und der Account aktiv ist, wird eine Sitzung generiert und im Cookie gespeichert.
         * Danach erfolg eine Weiterleitung zur Übersichts-Seite.
         */
        $sessionhash = hash('sha256', random_bytes(4096));
        mysqli_query($dbl, "INSERT INTO `sessions` (`userId`, `hash`) VALUES ('".$row['id']."', '".$sessionhash."')") OR DIE(MYSQLI_ERROR($dbl));
        setcookie($cookieName, $sessionhash, time()+(6*7*86400));
        userLog($row['id'], 1, "Login");
        header("Location: /overview");
        die();
      } else {
        /**
         * Der Account ist noch nicht aktiviert. Es wird HTTP403 und eine Fehlermeldung ausgegeben.
         */
        http_response_code(403);
        $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>".PHP_EOL;
        $content.= "<div class='warnbox'>Der Account ist noch nicht aktiviert. Bitte klicke auf den Link in der Registrierungsmail.</div>".PHP_EOL;
        $content.= "<div class='row'>".PHP_EOL.
        "<div class='col-s-12 col-l-12'><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a></div>".PHP_EOL.
        "</div>".PHP_EOL;
      }
    } else {
      /**
       * Wenn das Passwort nicht verifiziert werden konnte wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
       */
      http_response_code(403);
      $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>".PHP_EOL;
      $content.= "<div class='warnbox'>Die Zugangsdaten sind falsch.</div>".PHP_EOL;
      $content.= "<div class='row'>".PHP_EOL.
      "<div class='col-s-12 col-l-12'><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a><br><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</a></div>".PHP_EOL.
      "</div>".PHP_EOL;
    }
  } else {
    /**
     * Wenn keine Übereinstimmung vorliegt, dann wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
     */
    http_response_code(403);
    $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>".PHP_EOL;
    $content.= "<div class='warnbox'>Die Zugangsdaten sind falsch.</div>".PHP_EOL;
    $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-s-12 col-l-12'><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a><br><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</a></div>".PHP_EOL.
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
