<?php
/**
 * cookiecheck.php
 * 
 * Prüft ob ein gültiger Cookie gesetzt ist.
 */

if(!empty($_COOKIE[$cookieName])) {
  /**
   * Cookieinhalt entschärfen und prüfen ob Inhalt ein sha256-Hash ist.
   */
  $sessionhash = defuse($_COOKIE[$cookieName]);
  if(preg_match('/[a-f0-9]{64}/i', $sessionhash, $match) === 1) {
    /**
     * Abfrage in der Datenbank, ob eine Sitzung mit diesem Hash existiert.
     */
    $result = mysqli_query($dbl, "SELECT `users`.`id`, `users`.`email`, `users`.`password`, `users`.`salt` FROM `sessions` JOIN `users` ON `users`.`id`=`sessions`.`userId` WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 1) {
      /**
       * Wenn eine Sitzung existiert wird der letzte Nutzungszeitpunkt aktualisiert und die E-Mail Adresse in die Variable $email geladen.
       */
      mysqli_query($dbl, "UPDATE `sessions` SET `lastActivity`=CURRENT_TIMESTAMP WHERE `hash`='".$match[0]."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
      setcookie($cookieName, $match[0], time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
      $userRow = mysqli_fetch_array($result);
      $email = $userRow['email'];
      $userId = $userRow['id'];
      $sessionhash = $match[0];
      mysqli_query($dbl, "UPDATE `users` SET `lastActivity`=CURRENT_TIMESTAMP, `reminderDate`=NULL WHERE `id`=".$userId." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    } else {
      /**
       * Wenn keine Sitzung mit dem übergebenen Hash existiert wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
       */
      setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
      header("Location: /login");
      die();
    }
  } else {
    /**
     * Wenn kein gültiger sha256 Hash übergeben wurde wird der User durch Entfernen des Cookies und Umleitung zur Loginseite ausgeloggt.
     */
    setcookie($cookieName, NULL, 0, NULL, NULL, TRUE, TRUE);
    header("Location: /login");
    die();
  }
} else {
  /**
   * Wenn kein oder ein leerer Cookie übergeben wurde wird auf die Loginseite weitergeleitet.
   */
  header("Location: /login");
  die();
}
?>
