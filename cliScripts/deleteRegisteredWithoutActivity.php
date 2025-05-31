<?php
/**
 * deleteRegisteredWithoutActivity.php
 * 
 * Script to remove users who have only registered out of interest and whose account has been unused for
 * a month without activity after registration.
 * Cron: 28 7-22 * * * /usr/bin/php /path/to/cliScripts/deleteRegisteredWithoutActivity.php > /dev/null
 */

/**
 * Including the configuration and function loader.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Selektieren aller User, die seit mindestens einem Monat registriert sind und die letzte Aktivität maximal eine Stunde nach Registrierung war.
 */
$result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `registered` < DATE_SUB(NOW(), INTERVAL 1 MONTH) AND `lastActivity` < DATE_ADD(`registered`, INTERVAL 1 HOUR) AND `reminderDate` IS NULL") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  die();
}

/**
 * Configuration of the email function.
 */
$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPDebug = SMTP::DEBUG_OFF;
$mail->Host = $mailConfig['conn']['host'];
$mail->Port = $mailConfig['conn']['port'];
$mail->SMTPAuth = TRUE;
$mail->SMTPKeepAlive = true;
$mail->Username = $mailConfig['conn']['smtpUser'];
$mail->Password = $mailConfig['conn']['smtpPass'];
$mail->setFrom($mailConfig['conf']['fromEmail'], $mailConfig['conf']['fromName']);
$mail->addReplyTo($mailConfig['conf']['replyToEmail'], $mailConfig['conf']['replyToName']);
$mail->Subject = $mailConfig['subject']['inactiveDeletionWithoutActivity'];
$mail->isHTML(FALSE);
$mail->CharSet = "UTF-8";
$mailBody = "Hallo!\n\n".
"Du warst auf https://".$mailConfig['text']['HTTP_HOST']." registriert. Dein Account wurde gelöscht, da er seit deiner Registrierung vor über einem Monat keine Aktivitäten aufweist, was vollkommen in Ordnung ist.\n\n".
"Da wir dir damit den größtmöglichen Datenschutz und beste Datensparsamkeit bieten möchten, haben wir uns für diesen Schritt entschieden. Nach Versand dieser E-Mail sind all deine Daten von unseren Servern gelöscht.\n\n".
"Solltest du irgendwann wieder einen Account anlegen wollen, bist du herzlich dazu eingeladen dir einen neuen anzulegen :-)\n\n".
"Bei Rückfragen oder für Feedback kannst du gerne auf diese E-Mail antworten. Wir bedanken uns für dein Interesse am Projekt!\n\n".
$mailConfig['conf']['closingGreeting'];
$mail->Body = $mailBody;

/**
 * Iterate all users who are inactive and send the email.
 */
while($row = mysqli_fetch_assoc($result)) {
  $mail->addAddress($row['email']);
  if (!$mail->send()) {
    $mail->getSMTPInstance()->reset();
  }
  $mail->clearAddresses();
  mysqli_query($dbl, "DELETE FROM `users` WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
}
unset($mail);
?>
