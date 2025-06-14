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
  $content.= "<h1><span class='fas icon'>&#xf2f6;</span>Login</h1>";
  /**
   * Loginformular
   */
  $tabindex = 1;
  $content.= "<form action='/login' method='post'>";
  $content.= "<section>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3'><label for='email'>E-Mail Adresse</label></div>".
  "<div class='col-s-12 col-l-9'><input type='email' name='email' id='email' placeholder='E-Mail Adresse' autofocus required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3'><label for='password'>Passwort<br><span class='small'><span class='fas icon'>&#xf084;</span><a href='/pwReset'>Passwort zurücksetzen</a></span></label></div>".
  "<div class='col-s-12 col-l-9'><input type='password' name='password' id='password' minlength='8' placeholder='Passwort' required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
  "<div class='col-s-12 col-l-3'><label for='submit'>Einloggen</label></div>".
  "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Einloggen' tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "</section>";
  $content.= "</form>";
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
    $row = mysqli_fetch_assoc($result);
    if(password_verify($_POST['password'].$row['salt'], $row['password'])) {
      if($row['active'] == 1) {
        if($row['validEmail'] == 1) {
          /**
           * Wenn das Passwort verifiziert werden konnte und der Account aktiv und die E-Mail Adresse bestätigt ist,
           * wird eine Sitzung generiert und im Cookie gespeichert. Danach erfolg eine Weiterleitung zur Eintragsseite.
           */
          $sessionhash = hash('sha256', random_bytes(4096));
          mysqli_query($dbl, "INSERT INTO `sessions` (`userId`, `hash`) VALUES ('".$row['id']."', '".$sessionhash."')") OR DIE(MYSQLI_ERROR($dbl));
          setcookie($cookieName, $sessionhash, time()+COOKIE_DURATION, NULL, NULL, TRUE, TRUE);
          userLog($row['id'], 1, "Login");
          header("Location: /entry");
          die();
        } else {
          /**
           * Die E-Mail wurde noch nicht bestätigt. Es wird HTTP403 und eine Fehlermeldung ausgegeben.
           */
          http_response_code(403);
          $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>";
          $content.= "<div class='warnBox'>Du musst deine E-Mail Adresse bestätigen indem du auf den Link in der E-Mail klickst.</div>";
          $content.= "<p><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a></p>";
        }
      } else {
        /**
         * Der Account ist noch nicht aktiviert. Es wird HTTP403 und eine Fehlermeldung ausgegeben.
         */
        http_response_code(403);
        $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>";
        $content.= "<div class='warnBox'>Der Account ist noch nicht aktiviert. Bitte klicke auf den Link in der Registrierungsmail.</div>";
        $content.= "<p><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a></p>";
      }
    } else {
      /**
       * Wenn das Passwort nicht verifiziert werden konnte wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
       */
      http_response_code(403);
      $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>";
      $content.= "<div class='warnBox'>Die Zugangsdaten sind falsch.</div>";
      $content.= "<p><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a><br><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</a></p>";
    }
  } else {
    /**
     * Wenn keine Übereinstimmung vorliegt, dann wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
     */
    http_response_code(403);
    $content.= "<h1><span class='fas icon'>&#xf071;</span>Login gescheitert</h1>";
    $content.= "<div class='warnBox'>Die Zugangsdaten sind falsch.</div>";
    $content.= "<p><a href='/login'><span class='fas icon'>&#xf2f6;</span>Erneut versuchen</a><br><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</a></p>";
  }
} else {
  /**
   * Wenn bereits ein Cookie gesetzt ist wird auf die Eintragsseite weitergeleitet.
   */
  header("Location: /entry");
  die();
}
?>
