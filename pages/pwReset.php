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
  $content.= "<h1><span class='fas icon'>&#xf084;</span>Passwort zurücksetzen</h1>";
  /**
   * Passwort zurücksetzen Formular
   */
  $tabindex = 1;
  $content.= "<form action='/pwReset' method='post'>";
  $content.= "<section>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'><label for='email'>E-Mail Adresse</label></div>".
    "<div class='col-s-12 col-l-9'><input type='email' name='email' id='email' placeholder='Name' autofocus required tabindex='".$tabindex++."'></div>".
  "</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-3'>Passwort zurücksetzen</div>".
    "<div class='col-s-12 col-l-9'><input type='submit' name='submit' value='Passwort zurücksetzen' tabindex='".$tabindex++."'></div>".
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
     * Wenn der User existiert, wird geprüft, ob er für das Zurücksetzen des Passworts gesperrt ist.
     */
    $row = mysqli_fetch_assoc($result);
    if($row['preventPasswordReset'] == 1) {
      /**
       * Der Nutzer ist für das Zurücksetzen des Passworts gesperrt.
       */
      http_response_code(403);
      $content.= "<h1><span class='fas icon'>&#xf071;</span>Passwort zurücksetzen gescheitert</h1>";
      $content.= "<div class='warnBox'>Dieses Nutzerkonto ist für die Passwort zurücksetzen Funktion gesperrt. Wenn du dein Kennwort ändern lassen möchtest, schreib eine E-Mail an die <a href='/imprint'>vorhandenen Kontaktmöglichkeiten</a>.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/start'><span class='fas icon'>&#xf015;</span>Startseite</a></div>".
      "</div>";
    } elseif(!empty($row['lastPwReset']) AND (time()-86400) < strtotime($row['lastPwReset'])) {
      /**
       * Der Nutzer hat zu viele Passwort Zurücksetzungen angefordert.
       */
      http_response_code(403);
      $content.= "<h1><span class='fas icon'>&#xf071;</span>Passwort zurücksetzen gescheitert</h1>";
      $content.= "<div class='warnBox'>Es wurde in zu kurzer Zeit zu oft versucht das Passwort zurückzusetzen. Wenn du dein Kennwort ändern lassen möchtest, schreib eine E-Mail an die <a href='/imprint'>vorhandenen Kontaktmöglichkeiten</a> oder warte einen Tag.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/start'><span class='fas icon'>&#xf015;</span>Startseite</a></div>".
      "</div>";
    } else {
      /**
       * Der Nutzer darf sein Passwort zurücksetzen.
       * Es wird ein neues Passwort generiert und dem Nutzer per Mail zugestellt.
       */
      $pw = hash('sha256', random_bytes(4096));
      $salt = hash('sha256', random_bytes(4096));
      $password = password_hash($pw.$salt, PASSWORD_DEFAULT);
      mysqli_query($dbl, "UPDATE `users` SET `password`='".$password."', `salt`='".$salt."', `lastPwReset`=NOW() WHERE `id`='".$row['id']."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "DELETE FROM `sessions` WHERE `userId`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      userLog($row['id'], 1, "Passwort zurückgesetzt (pwReset) und alle Sitzungen ausgeloggt");
      $content.= "<div class='successBox'>Passwort erfolgreich zurückgesetzt. Es wurde dir per E-Mail zugeschickt.</div>";
      /**
       * Mail
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
      $mail->addAddress($row['email']);
      $mail->Subject = $mailConfig['subject']['passwordResetted'];
      $mail->isHTML(FALSE);
      $mail->CharSet = "UTF-8";
      $mailBody = "Hallo!\n\n".
      "Du hast dein Passwort auf https://".$_SERVER['HTTP_HOST']." zurückgesetzt.\n\n".
      "Dein neues Passwort lautet:\n".$pw."\n\n".
      "Bitte achte beim Kopieren darauf, dass du keine Leerzeichen oder Zeilenumbrüche kopierst, sondern wirklich nur von ".substr($pw, 0, 3).".. bis ..".substr($pw, strlen($pw)-3, 3).".\n\n".
      "Solltest du das Passwort nicht angefordert haben, dann kannst du uns an die unten stehende E-Mail Adresse schreiben, damit wir dein Nutzerkonto für zukünftige Rücksetzungen sperren können.\n\n".
      $mailConfig['conf']['closingGreeting'];
      $mail->Body = $mailBody;
      if (!$mail->send()) {
        mysqli_query($dbl, "INSERT INTO `failedEmails` (`userId`, `to`, `subject`, `message`) VALUES ('".$row['id']."', '".defuse($row['email'])."', '".$mailConfig['subject']['passwordResetted']."', '".defuse($mailBody)."')") OR DIE(MYSQLI_ERROR($dbl));
        $content.= "<div class='infoBox'>Der Mailserver ist gerade ausgelastet. Es kann ein paar Minuten dauern, bis du die Passwort E-Mail bekommst.</div>";
      }
    }
  } else {
    /**
     * Wenn keine Übereinstimmung vorliegt, dann wird HTTP403 zurückgegeben und eine Fehlermeldung wird ausgegeben.
     */
    http_response_code(403);
    $content.= "<h1><span class='fas icon'>&#xf071;</span>Passwort zurücksetzen gescheitert</h1>";
    $content.= "<div class='warnBox'>Es existiert kein Nutzerkonto mit der E-Mail Adresse.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/pwReset'><span class='fas icon'>&#xf084;</span>Mit anderer E-Mail Adresse versuchen</a><br><a href='/register'><span class='far icon'>&#xf044;</span>Neues Nutzerkonto registrieren</a></div>".
    "</div>";
  }
} else {
  /**
   * Wenn bereits ein Cookie gesetzt ist wird auf die Eintragsseite weitergeleitet.
   */
  header("Location: /entry");
  die();
}

?>
