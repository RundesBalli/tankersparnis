<?php
/**
 * failedEmails.php
 * 
 * Datei zum Nachsenden gescheiterter E-Mails.
 * (bspw. wenn der Mailserver nicht erreichbar war)
 * Cron: * * * * * /usr/bin/php /path/to/cliScripts/failedEmails.php > /dev/null
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Selektieren aller gescheiterten Emails und abbruch, sofern es keine gibt.
 */
$result = mysqli_query($dbl, "SELECT * FROM `failedEmails` WHERE `retryCounter` < 5 AND `retryAt` < NOW()") OR DIE(MYSQLI_ERROR($dbl));
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
$mail->isHTML(FALSE);
$mail->CharSet = "UTF-8";

/**
 * Durchlaufen aller gescheiterten Mails.
 */
while($row = mysqli_fetch_array($result)) {
  $mail->addAddress($row['email']);
  $mail->Subject = $row['subject'];
  $mailBody = $row['message'];
  $mail->Body = $mailBody;

  if($mail->send()) {
    mysqli_query($dbl, "DELETE FROM `failedEmails` WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  } else {
    if(stripos($mail->ErrorInfo, "Recipient address rejected") !== FALSE) {
      mysqli_query($dbl, "DELETE FROM `failedEmails` WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      mysqli_query($dbl, "UPDATE `users` SET `active`=0, `validEmail`=0 WHERE `id`=".$row['userId']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      userLog($row['userId'], 1, "Mail-Adresse fehlerhaft. User inaktiv gesetzt.");
    } else {
      mysqli_query($dbl, "UPDATE `failedEmails` SET `retryCounter` = `retryCounter` + 1, `retryAt` = DATE_ADD(`retryAt`, INTERVAL 1 HOUR) WHERE `id`=".$row['id']." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    }
  }
  $mail->clearAddresses();
}
unset($mail);
?>
