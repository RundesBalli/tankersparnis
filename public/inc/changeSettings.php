<?php
/**
 * changeSettings.php
 * 
 * Ausführungsseite für Einstellungen
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

if(empty($_POST['action'])) {
  /**
   * Wenn der action Parameter leer ist oder gar nicht gesendet wurde, wird der Nutzer
   * auf die Einstellungsseite zurückgeleitet.
   */
  header("Location: /settings");
  die();
} elseif($_POST['action'] == "changePassword") {
  /**
   * Passwort ändern
   */
  if(empty($_POST['password']) OR empty($_POST['passwordNew']) OR empty($_POST['passwordNew1'])) {
    /**
     * Wenigstens eins der übergebenen Felder ist leer.
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Du musst dein derzeitiges Passwort und zwei mal dein neues Passwort eingeben um es zu ändern.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></div>".
    "</div>";
  } elseif(!password_verify($_POST['password'].$userRow['salt'], $userRow['password'])) {
    /**
     * Das alte Passwort stimmt nicht mit dem eingegebenen Passwort überein.
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Das eingegebene Passwort stimmt nicht mit dem aktuell gültigen Passwort überein.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></div>".
    "</div>";
  } elseif($_POST['password'] == $_POST['passwordNew']) {
    /**
     * Das neue Passwort ist identisch mit dem derzeit gültigen.
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Das neue Passwort ist identisch zu dem aktuellen.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></div>".
    "</div>";
  } elseif($_POST['passwordNew'] != $_POST['passwordNew1']) {
    /**
     * Das neue Passwort ist nicht identisch mit der Zweiteingabe des neuen Passworts.
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Die neuen Passwörter stimmen nicht überein.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></div>".
    "</div>";
  } elseif(strlen($_POST['passwordNew']) < 8) {
    /**
     * Das neue Passwort ist zu kurz.
     */
    http_response_code(403);
    $content.= "<div class='warnbox'>Das neue Passwort ist zu kurz (min. 8 Zeichen).</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/settings'><span class='fas icon'>&#xf013;</span>Erneut versuchen</a></div>".
    "</div>";
  } else {
    /**
     * Das neue Passwort ist nicht identisch mit dem aktuellen, ist lang genug, stimmt mit der Zweiteingabe
     * überein und kann daher geändert werden.
     */
    $salt = hash('sha256', random_bytes(4096));
    $password = password_hash($_POST['passwordNew'].$salt, PASSWORD_DEFAULT);
    mysqli_query($dbl, "UPDATE `users` SET `password`='".$password."', `salt`='".$salt."' WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    mysqli_query($dbl, "DELETE FROM `sessions` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl));
    $content.= "<div class='successbox'>Dein Passwort wurde geändert. Du musst dich jetzt neu einloggen.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/login'><span class='fas icon'>&#xf2f6;</span>Zum Login</a></div>".
    "</div>";
    setcookie($cookieName, NULL, 0);
    unset($_COOKIE[$cookieName]);
    /**
     * E-Mail senden
     */
    require(__DIR__.DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."PHPMailer.php");
    require(__DIR__.DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."SMTP.php");
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = $mailConfig['conn']['host'];
    $mail->Port = $mailConfig['conn']['port'];
    $mail->SMTPAuth = TRUE;
    $mail->Username = $mailConfig['conn']['smtpUser'];
    $mail->Password = $mailConfig['conn']['smtpPass'];
    $mail->setFrom($mailConfig['conf']['fromEmail'], $mailConfig['conf']['fromName']);
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
} else {
  /**
   * Wenn der action Parameter nicht leer ist aber keine action zutrifft, wird der Nutzer
   * auf die Einstellungsseite zurückgeleitet.
   */
  header("Location: /settings");
  die();
}
?>
