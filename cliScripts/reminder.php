<?php
/**
 * reminder.php
 * 
 * Datei zum Senden von Emails an User, die 6 Monate nicht online waren.
 * Cron: 20 7-22 * * * /usr/bin/php /path/to/cliScripts/reminder.php > /dev/null
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Selektieren aller User, die seit 6 Monaten nicht online waren und noch keine Erinnerungs-Email bekommen haben. Abbruch wenn kein User benachrichtigt werden muss.
 */
$result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 MONTH) AND `reminderDate` IS NULL") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  die();
}

/**
 * Einbinden des PHPMailers
 */
require(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."PHPMailer.php");
require(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."SMTP.php");

/**
 * Durchlaufen aller User, die inaktiv sind und Versenden der Email.
 */

while($row = mysqli_fetch_array($result)) {
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
  $mail->Subject = $mailConfig['subject']['reminder'];
  $mail->isHTML(FALSE);
  $mail->CharSet = "UTF-8";
  $mailBody = "Hallo!\n\n".
  "Du bist auf https://".$mailConfig['text']['HTTP_HOST']." registriert und warst ein halbes Jahr nicht mehr bei uns eingeloggt :-(\n\n".
  "Unter dem oben genannten Link kannst du dich wieder einloggen, wenn du möchtest. Solltest du keinen Bedarf mehr für deinen Account haben, wird dieser automatisch einen Monat nach Versand dieser E-Mail restlos gelöscht. Wir möchten dir damit den größtmöglichen Datenschutz und beste Datensparsamkeit bieten.\n\n".
  "Bei Rückfragen oder wenn du deinen Account sofort entfernen lassen möchtest, kannst du gerne auf diese E-Mail antworten.\n\n".
  $mailConfig['conf']['closingGreeting'];
  $mail->Body = $mailBody;
  mysqli_query($dbl, "UPDATE `users` SET `reminderDate`=NOW() WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  userLog($row['id'], 1, "Erinnerungsmail geschickt");
  if (!$mail->send()) {
    mysqli_query($dbl, "INSERT INTO `failedEmails` (`userId`, `to`, `subject`, `message`) VALUES ('".$row['id']."', '".$row['email']."', '".$mailConfig['subject']['reminder']."', '".defuse($mailBody)."')") OR DIE(MYSQLI_ERROR($dbl));
  }
  unset($mail);
}
?>
