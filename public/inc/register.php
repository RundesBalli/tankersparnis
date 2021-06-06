<?php
/**
 * register.php
 * 
 * Registrierungsseite
 */
$title = "Registrieren";
$content.= "<h1><span class='far icon'>&#xf044;</span>Registrieren</h1>".PHP_EOL;

/**
 * Prüfen ob eingeloggt. Wenn ja, dann Umleitung auf Nutzerseite. Prüfung auf Validität erfolgt über die Userseite.
 */
if((isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  header("Location: /overview");
  die();
}

/**
 * Falls das Formular übergeben wurde, gehen wir davon aus, dass alles okay ist, demzufolge muss das Formular nicht mehr angezeigt werden.
 * Im Fehlerfall wird das Formular nochmals angezeigt.
 */
$form = 0;
if(isset($_POST['submit'])) {
  /**
   * Auswertung. Falls alles ok dann $form auf 0 lassen, sonst 1. Bei 0 am Ende wird der Query ausgeführt und die Email versendet.
   */
  /**
   * E-Mail
   */
  if(filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) !== FALSE) {
    $email = defuse(filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL));
  } else {
    $form = 1;
    $content.= "<div class='warnbox'>Die eingegebene E-Mail Adresse ist ungültig.</div>".PHP_EOL;
  }

  /**
   * Passwort
   */
  if(strlen($_POST['password']) >= 8) {
    $salt = hash('sha256', random_bytes(4096));
    $password = password_hash($_POST['password'].$salt, PASSWORD_DEFAULT);
  } else {
    $form = 1;
    $content.= "<div class='warnbox'>Das Passwort ist zu kurz.</div>".PHP_EOL;
  }

  /**
   * Passwort
   */
  if(empty($_POST['privacy']) OR $_POST['privacy'] != 1) {
    $form = 1;
    $content.= "<div class='warnbox'>Du musst die Datenschutzerklärung lesen, verstehen und akzeptieren um dir ein Nutzerkonto anzulegen.</div>".PHP_EOL;
  }
  
  /**
   * Wenn durch die Postdaten-Validierung die Inhalte geprüft und entschärft wurden, kann der Query erzeugt und ausgeführt werden.
   */
  if($form == 0) {
    if(mysqli_query($dbl, "INSERT INTO `users` (`email`, `password`, `salt`) VALUES ('".$email."', '".$password."', '".$salt."')")) {
      userLog(mysqli_insert_id($dbl), 1, "Registriert");
      $content.= "<div class='successbox'>Account erfolgreich angelegt.</div>".PHP_EOL;
      //
    } else {
      $form = 1;
      if(mysqli_errno($dbl) == 1062) {
        $content.= "<div class='warnbox'>Es existiert bereits ein Nutzerkonto unter dieser E-Mail Adresse.<br>Wenn du dein Passwort vergessen hast, kannst du es unter <a href='/pwforget'><span class='fas icon'>&#xf084;</span>Passwort vergessen</a> neu setzen.</div>".PHP_EOL;
      } else {
        $content.= "<div class='warnbox'>Unbekannter Fehler. Bitte wende dich an <a href='/imprint'>den Plattformbetreiber</a>.</div>".PHP_EOL;
      }
    }
  }
} else {
  /**
   * Erstaufruf = Formular wird angezeigt.
   */
  $form = 1;
}

if($form == 1) {
  $content.= "<form action='/register' method='post'>".PHP_EOL;
  $content.= "<section>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-s-12 col-l-3'><label for='email'><span class='fas icon'>&#xf1fa;</span>E-Mail Adresse</label></div>".PHP_EOL.
    "<div class='col-s-12 col-l-9'><input type='email' id='email' name='email' placeholder='john@example.com'".(!empty($email) ? " value='".output($email)."'" : NULL)." autofocus tabindex='1'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-s-12 col-l-3'><label for='password'><span class='fas icon'>&#xf084;</span>Passwort<br><span class='small'>min. 8 Zeichen</span></label></div>".PHP_EOL.
    "<div class='col-s-12 col-l-9'><input type='password' id='password' minlength='8' name='password' placeholder='Passwort' tabindex='2'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-s-12 col-l-3'>Datenschutz</div>".PHP_EOL.
    "<div class='col-s-12 col-l-9'><input type='checkbox' name='privacy' id='privacy' value='1'><label for='privacy'>Ich habe die <a href='/privacy' target='_blank'>Datenschutzerklärung</a> gelesen, verstanden und akzeptiere sie.</label></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "<div class='row'>".PHP_EOL.
    "<div class='col-s-12 col-l-3'><label for='submit'><span class='far icon'>&#xf044;</span>Registrieren</label></div>".PHP_EOL.
    "<div class='col-s-12 col-l-9'><input type='submit' id='submit' name='submit' value='Registrieren' tabindex='3'></div>".PHP_EOL.
  "</div>".PHP_EOL;
  $content.= "</section>".PHP_EOL;
  $content.= "</form>".PHP_EOL;
}
?>
