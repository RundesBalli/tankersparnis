<?php
/**
 * activate.php
 * 
 * Nutzerkonto aktivieren
 */
$title = "Nutzerkonto aktivieren";

$content.= "<h1><span class='fas icon'>&#xf058;</span>Nutzerkonto aktivieren</h1>";

if(!empty($_GET['hash'])) {
  if(preg_match('/[a-f0-9]{64}/i', defuse($_GET['hash']), $match) === 1) {
    $hash = defuse($match[0]);
    $result = mysqli_query($dbl, "SELECT * FROM `users` WHERE `registerHash`='".$hash."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      mysqli_query($dbl, "UPDATE `users` SET `registerHash`=NULL, `active`=1, `validEmail`=1 WHERE `registerHash`='".$hash."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      if(mysqli_affected_rows($dbl) == 1) {
        $row = mysqli_fetch_array($result);
        userLog($row['id'], 1, "Aktiviert");
        $content.= "<div class='successbox'>Nutzerkonto aktiviert.</div>";
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
        $mail->addAddress($row['email']);
        $mail->Subject = $mailConfig['subject']['accountActivated'];
        $mail->isHTML(FALSE);
        $mail->CharSet = "UTF-8";
        $mailBody = "Hallo!\n\n".
        "Dein Nutzerkonto auf https://".$_SERVER['HTTP_HOST']." wurde aktiviert.\n\n".
        "Du kannst dich nun über https://".$_SERVER['HTTP_HOST']."/login anmelden.\n\n".
        $mailConfig['conf']['closingGreeting'];
        $mail->Body = $mailBody;
        if (!$mail->send()) {
          mysqli_query($dbl, "INSERT INTO `failedEmails` (`userId`, `to`, `subject`, `message`) VALUES ('".$row['id']."', '".$row['email']."', '".$mailConfig['subject']['accountActivated']."', '".defuse($mailBody)."')") OR DIE(MYSQLI_ERROR($dbl));
        }
        $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/login'><span class='fas icon'>&#xf2f6;</span>Login</a></div>".
        "</div>";
      }
    } else {
      http_response_code(403);
      $content.= "<div class='warnbox'>Der übergebene Hash ist ungültig oder wurde bereits benutzt. Bitte klicke den Link in der Aktivierungsmail an oder probiere dich einzuloggen um fortzufahren.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/start'><span class='fas icon'>&#xf015;</span>Startseite</a></div>".
      "</div>";
    }
  } else {
    http_response_code(403);
    $content.= "<div class='warnbox'>Der übergebene Hash hat ein ungültiges Format. Bitte klicke den Link in der Aktivierungsmail an um fortzufahren.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/start'><span class='fas icon'>&#xf015;</span>Startseite</a></div>".
    "</div>";
  }
} else {
  http_response_code(403);
  $content.= "<div class='warnbox'>Es wurde kein Hash übergeben. Bitte klicke den Link in der Aktivierungsmail an um fortzufahren.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/start'><span class='fas icon'>&#xf015;</span>Startseite</a></div>".
  "</div>";
}
?>
