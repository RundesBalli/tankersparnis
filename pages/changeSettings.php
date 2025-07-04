<?php
/**
 * changeSettings.php
 * 
 * Ausführungsseite für Einstellungen
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once(INCLUDE_DIR.'cookieCheck.php');

/**
 * Titel und Überschrift
 */
$title = "Einstellungen";
$content.= "<h1><span class='fas icon'>&#xf013;</span>Einstellungen</h1>";

if(empty($_POST['action'])) {
  /**
   * Wenn der action Parameter leer ist oder gar nicht gesendet wurde, wird der Nutzer
   * auf die Einstellungsseite zurückgeleitet.
   */
  header("Location: /settings");
  die();
} elseif($_POST['action'] == "changeEmail") {
  /**
   * E-Mail Adresse ändern
   */
  if($_POST['token'] != $sessionhash) {
    /**
     * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(empty($_POST['password']) OR empty($_POST['email']) OR empty($_POST['email1'])) {
    /**
     * Wenigstens eins der übergebenen Felder ist leer.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Du musst dein derzeitiges Passwort und deine neue E-Mail Adresse doppelt eingeben.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(!password_verify($_POST['password'].$userRow['salt'], $userRow['password'])) {
    /**
     * Das Passwort stimmt nicht mit dem eingegebenen Passwort überein.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das eingegebene Passwort stimmt nicht mit dem aktuell gültigen Passwort überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif($email == $_POST['email']) {
    /**
     * Die neue E-Mail Adresse ist identisch mit der aktuellen.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Die neue E-Mail Adresse ist identisch zur aktuellen E-Mail Adresse.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif($_POST['email'] != $_POST['email1']) {
    /**
     * Die neue E-Mail Adresse ist nicht identisch mit der Zweiteingabe.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Die neuen E-Mail Adressen stimmen nicht überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) === FALSE) {
    /**
     * Die neue E-Mail Adresse ist ungültig.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Die neue E-Mail Adresse ist ungültig.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } else {
    /**
     * Das E-Mail Adresse ist nicht identisch mit der aktuellen, ist gültig, stimmt mit der Zweiteingabe
     * überein. Nun muss geprüft werden, ob bereits ein Nutzerkonto mit der E-Mail Adresse existiert.
     */
    $emailNew = defuse(filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL));
    $result = mysqli_query($dbl, "SELECT `id` FROM `users` WHERE `email`='".$emailNew."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Es existiert bereits ein Nutzerkonto mit dieser E-Mail Adresse
       */
      http_response_code(403);
      $content.= "<div class='warnBox'>Es existiert bereits ein Nutzerkonto mit der eingegebenen E-Mail Adresse.</div>";
      $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
    } else {
      /**
       * Es existiert noch kein Nutzerkonto mit dieser E-Mail Adresse. Daher kann die neue E-Mail Adresse gesetzt werden.
       */
      $emailHash = hash('sha256', random_bytes(4096));
      mysqli_query($dbl, "UPDATE `users` SET `email`='".$emailNew."', `validEmail`=0, `emailHash`='".$emailHash."' WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      userLog($userId, 5, "E-Mail Adresse geändert. Alt: ".defuse($email));
      mysqli_query($dbl, "DELETE FROM `sessions` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl));
      userLog($userId, 1, "Alle Sitzungen beendet.");
      $content.= "<div class='successBox'>Deine E-Mail Adresse wurde geändert. Du musst dich jetzt neu einloggen.</div>";
      $content.= "<p><a href='/login'><span class='fas icon'>&#xf2f6;</span>Zum Login</a></p>";
      setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
      unset($_COOKIE[$cookieName]);
      /**
       * E-Mail senden (neuer Empfänger)
       */
      $mail = new PHPMailer();
      $mail->isSMTP();
      $mail->SMTPDebug = SMTP::DEBUG_OFF;
      $mail->Host = $mailConfig['conn']['host'];
      $mail->Port = $mailConfig['conn']['port'];
      $mail->SMTPAuth = TRUE;
      $mail->Username = $mailConfig['conn']['smtpUser'];
      $mail->Password = $mailConfig['conn']['smtpPass'];
      $mail->setFrom($mailConfig['conf']['fromEmail'], $mailConfig['conf']['fromName']);
      $mail->addReplyTo($mailConfig['conf']['replyToEmail'], $mailConfig['conf']['replyToName']);
      $mail->addAddress($emailNew);
      $mail->Subject = $mailConfig['subject']['emailChanged'];
      $mail->isHTML(FALSE);
      $mail->CharSet = "UTF-8";
      $mailBody = "Hallo!\n\n".
      "Deine E-Mail Adresse auf https://".$_SERVER['HTTP_HOST']." wurde in diese E-Mail Adresse geändert.\n\n".
      "Bitte bestätige, dass es sich um deine E-Mail Adresse handelt indem du dazu auf den folgenden Link klickst:\nhttps://".$_SERVER['HTTP_HOST']."/confirmEmail?hash=".$emailHash."\n\n".
      "Solltest du diese Änderung nicht veranlasst haben, setze dich bitte mit uns über die unten stehende E-Mail Adresse in Verbindung.\n\n".
      $mailConfig['conf']['closingGreeting'];
      $mail->Body = $mailBody;
      if (!$mail->send()) {
        mysqli_query($dbl, "INSERT INTO `failedEmails` (`userId`, `to`, `subject`, `message`) VALUES ('".$userId."', '".$emailNew."', '".$mailConfig['subject']['emailChanged']."', '".defuse($mailBody)."')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='infoBox'>Der Mailserver ist gerade ausgelastet. Es kann ein paar Minuten dauern, bis du die Aktivierungsmail bekommst.</div>";
      }
      /**
       * E-Mail senden (alter Empfänger)
       */
      $mail1 = new PHPMailer();
      $mail1->isSMTP();
      $mail1->SMTPDebug = SMTP::DEBUG_OFF;
      $mail1->Host = $mailConfig['conn']['host'];
      $mail1->Port = $mailConfig['conn']['port'];
      $mail1->SMTPAuth = TRUE;
      $mail1->Username = $mailConfig['conn']['smtpUser'];
      $mail1->Password = $mailConfig['conn']['smtpPass'];
      $mail1->setFrom($mailConfig['conf']['fromEmail'], $mailConfig['conf']['fromName']);
      $mail1->addReplyTo($mailConfig['conf']['replyToEmail'], $mailConfig['conf']['replyToName']);
      $mail1->addAddress($email);
      $mail1->Subject = $mailConfig['subject']['emailChanged'];
      $mail1->isHTML(FALSE);
      $mail1->CharSet = "UTF-8";
      $mailBody = "Hallo!\n\n".
      "Deine E-Mail Adresse auf https://".$_SERVER['HTTP_HOST']." wurde in ".$emailNew." geändert.\n\n".
      "Solltest du diese Änderung nicht veranlasst haben, setze dich bitte mit uns über die unten stehende E-Mail Adresse in Verbindung.\n\n".
      $mailConfig['conf']['closingGreeting'];
      $mail1->Body = $mailBody;
      if (!$mail1->send()) {
        mysqli_query($dbl, "INSERT INTO `failedEmails` (`userId`, `to`, `subject`, `message`) VALUES ('".$userId."', '".$email."', '".$mailConfig['subject']['emailChanged']."', '".defuse($mailBody)."')") OR DIE(MYSQLI_ERROR($dbl));
      }
    }
  }
} elseif($_POST['action'] == "changePassword") {
  /**
   * Passwort ändern
   */
  if($_POST['token'] != $sessionhash) {
    /**
     * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(empty($_POST['password']) OR empty($_POST['passwordNew']) OR empty($_POST['passwordNew1'])) {
    /**
     * Wenigstens eins der übergebenen Felder ist leer.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Du musst dein derzeitiges Passwort und zwei mal dein neues Passwort eingeben um es zu ändern.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(!password_verify($_POST['password'].$userRow['salt'], $userRow['password'])) {
    /**
     * Das alte Passwort stimmt nicht mit dem eingegebenen Passwort überein.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das eingegebene Passwort stimmt nicht mit dem aktuell gültigen Passwort überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif($_POST['password'] == $_POST['passwordNew']) {
    /**
     * Das neue Passwort ist identisch mit dem derzeit gültigen.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das neue Passwort ist identisch zu dem aktuellen.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif($_POST['passwordNew'] != $_POST['passwordNew1']) {
    /**
     * Das neue Passwort ist nicht identisch mit der Zweiteingabe des neuen Passworts.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Die neuen Passwörter stimmen nicht überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(strlen($_POST['passwordNew']) < 8) {
    /**
     * Das neue Passwort ist zu kurz.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das neue Passwort ist zu kurz (min. 8 Zeichen).</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } else {
    /**
     * Das neue Passwort ist nicht identisch mit dem aktuellen, ist lang genug, stimmt mit der Zweiteingabe
     * überein und kann daher geändert werden.
     */
    $salt = hash('sha256', random_bytes(4096));
    $password = password_hash($_POST['passwordNew'].$salt, PASSWORD_DEFAULT);
    mysqli_query($dbl, "UPDATE `users` SET `password`='".$password."', `salt`='".$salt."' WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "DELETE FROM `sessions` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl));
    userLog($userId, 5, "Passwort geändert (settings) und alle Sitzungen ausgeloggt");
    $content.= "<div class='successBox'>Dein Passwort wurde geändert. Du musst dich jetzt neu einloggen.</div>";
    $content.= "<p><a href='/login'><span class='fas icon'>&#xf2f6;</span>Zum Login</a></p>";
    setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
    unset($_COOKIE[$cookieName]);
    /**
     * E-Mail senden
     */
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = $mailConfig['conn']['host'];
    $mail->Port = $mailConfig['conn']['port'];
    $mail->SMTPAuth = TRUE;
    $mail->Username = $mailConfig['conn']['smtpUser'];
    $mail->Password = $mailConfig['conn']['smtpPass'];
    $mail->setFrom($mailConfig['conf']['fromEmail'], $mailConfig['conf']['fromName']);
    $mail->addReplyTo($mailConfig['conf']['replyToEmail'], $mailConfig['conf']['replyToName']);
    $mail->addAddress($email);
    $mail->Subject = $mailConfig['subject']['passwordChanged'];
    $mail->isHTML(FALSE);
    $mail->CharSet = "UTF-8";
    $mailBody = "Hallo!\n\n".
    "Dein Passwort auf https://".$_SERVER['HTTP_HOST']." wurde geändert (wird aus Sicherheitsgründen nicht gesendet).\n\n".
    "Solltest du diese Änderung nicht veranlasst haben, setze dich bitte mit uns über die unten stehende E-Mail Adresse in Verbindung.\n\n".
    $mailConfig['conf']['closingGreeting'];
    $mail->Body = $mailBody;
    if (!$mail->send()) {
      mysqli_query($dbl, "INSERT INTO `failedEmails` (`userId`, `to`, `subject`, `message`) VALUES ('".$userId."', '".$email."', '".$mailConfig['subject']['passwordChanged']."', '".defuse($mailBody)."')") OR DIE(MYSQLI_ERROR($dbl));
    }
  }
} elseif($_POST['action'] == "radius") {
  /**
   * Radius ändern
   */
  if($_POST['token'] != $sessionhash) {
    /**
     * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(empty($_POST['radius']) OR !is_numeric($_POST['radius'])) {
    /**
     * Radius ist leer.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Du musst einen Radius eingeben.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } elseif(intval($_POST['radius']) < 1 OR intval($_POST['radius']) > 25) {
    /**
     * Der Radius ist zu klein oder zu groß.
     */
    http_response_code(403);
    $content.= "<div class='warnBox'>Der Radius muss zwischen 1 und 25km liegen.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></p>";
  } else {
    /**
     * Änderung kann durchgeführt werden.
     */
    mysqli_query($dbl, "UPDATE `users` SET `radius`='".defuse(intval($_POST['radius']))."' WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    userLog($userId, 5, "Radius geändert von `".$userRow['radius']."` auf `".intval($_POST['radius'])."`");
    $content.= "<div class='successBox'>Der Radius wurde geändert.</div>";
    $content.= "<p><a href='/settings'><span class='fas icon'>&#xf013;</span>Zurück zu den Einstellungen</a></p>";
  }
} else {
  /**
   * Wenn der action Parameter nicht leer ist aber keine action zutrifft, wird der Nutzer
   * auf die Einstellungsseite zurückgeleitet.
   */
  header("Location: /settings");
  die();
}
?>
