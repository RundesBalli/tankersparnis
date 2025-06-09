<?php
/**
 * failedEmails.php
 * 
 * Script to re-send failed emails.
 * (e.g. when the mailserver was not reachable)
 * Cron: * * * * * /usr/bin/php /path/to/cliScripts/failedEmails.php > /dev/null
 */

/**
 * Including the configuration and function loader.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Selektieren aller gescheiterten Emails und abbruch, sofern es keine gibt.
 */
$result = mysqli_query($dbl, "SELECT * FROM `failedEmails` WHERE `retryCounter` < 5 AND `retryAt` < NOW()") OR DIE(MYSQLI_ERROR($dbl));
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
$mail->isHTML(FALSE);
$mail->CharSet = "UTF-8";

/**
 * Iterate all failed emails.
 */
while($row = mysqli_fetch_assoc($result)) {
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
