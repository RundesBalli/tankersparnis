<?php
/**
 * autoDeleter.php
 * 
 * Datei zum Entfernen der User, die 6 Monate + 1 Monat nach Benachrichtigung nicht online waren.
 * Cron: 25 7-22 * * * /usr/bin/php /path/to/cliScripts/autoDeleter.php > /dev/null
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Selektieren aller User, die seit 3 Monaten + 1 Monat nach Benachrichtigung nicht online waren. Abbruch wenn kein User gelöscht werden muss.
 */
$result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 3 MONTH) AND (`reminderDate` IS NOT NULL AND `reminderDate` < DATE_SUB(NOW(), INTERVAL 1 MONTH))") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  die();
}

/**
 * Einbinden des PHPMailers
 */
require(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."PHPMailer.php");
require(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."SMTP.php");

/**
 * Konfiguration der Mailfunktion
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
$mail->Subject = $mailConfig['subject']['inactiveDeletion'];
$mail->isHTML(FALSE);
$mail->CharSet = "UTF-8";
$mailBody = "Hallo!\n\n".
"Du warst auf https://".$mailConfig['text']['HTTP_HOST']." registriert. Dein Account wurde wegen drei Monaten Inaktivität und einem Monat nach Erinnerung gelöscht :-(\n\n".
"Da wir dir damit den größtmöglichen Datenschutz und beste Datensparsamkeit bieten möchten, haben wir uns für diesen Schritt entschieden. Nach Versand dieser E-Mail sind all deine Daten von unseren Servern gelöscht.\n\n".
"Solltest du irgendwann wieder einen Account anlegen wollen, bist du herzlich dazu eingeladen dir einen neuen anzulegen :-)\n\n".
"Bei Rückfragen oder für Feedback kannst du gerne auf diese E-Mail antworten. Wir bedanken uns für dein Interesse am Projekt!\n\n".
$mailConfig['conf']['closingGreeting'];
$mail->Body = $mailBody;

/**
 * Durchlaufen aller User, die inaktiv sind und Versenden der Email.
 */
while($row = mysqli_fetch_array($result)) {
  $mail->addAddress($row['email']);
  if (!$mail->send()) {
    $mail->getSMTPInstance()->reset();
  }
  $mail->clearAddresses();
  mysqli_query($dbl, "DELETE FROM `users` WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
}
unset($mail);
?>
