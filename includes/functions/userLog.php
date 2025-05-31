<?php
/**
 * includes/functions/userLog.php
 * 
 * Function to log user activities.
 */

/**
 * userLog
 * Function to log user activities.
 * 
 * @param int    $userId      userId of the user
 * @param int    $logLevel    logLevel of the activity
 * @param string $text        Text (optional)
 */
function userLog($userId = NULL, int $logLevel, $text = NULL) {
  global $dbl;

  /**
   * Check whether the userId exists. If not, it is set to NULL.
   */
  if($userId !== NULL) {
    $userId = (int)defuse($userId);
    $result = mysqli_query($dbl, "SELECT `id` FROM `users` WHERE `id`='".$userId."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $userId = NULL;
    }
  } else {
    $userId = NULL;
  }

  /**
   * Check whether the logLevel exists. If not, it is set to “User/system action” (1).
   */
  if(is_int($logLevel)) {
    $logLevel = defuse($logLevel);
    $result = mysqli_query($dbl, "SELECT `id` FROM `logLevel` WHERE `id`='".$logLevel."' LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) != 1) {
      $logLevel = 1;
    }
  } else {
    $logLevel = 1;
  }

  /**
   * Defusing the text, if available.
   */
  if($text !== NULL) {
    $text = defuse($text);
  }

  /**
   * Enter into the log.
   */
  mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES (".($userId !== NULL ? "'".$userId."'" : "NULL").", '".$logLevel."', ".($text !== NULL ? "'".$text."'" : "NULL").")") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_affected_rows($dbl) != 0) {
    return true;
  } else {
    return false;
  }
}
?>
