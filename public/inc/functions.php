<?php
/**
 * functions.php
 * 
 * Datei mit Funktionen für den Betrieb.
 */

/**
 * Entschärffunktion
 * 
 * @param  string $defuse_string String der "entschärft" werden soll, um ihn in einen DB-Query zu übergeben.
 * @param  bool   $trim          Gibt an ob Leerzeichen/-zeilen am Anfang und Ende entfernt werden sollen.
 * 
 * @return string Der vorbereitete, "entschärfte" String.
 */
function defuse($defuse_string, $trim = TRUE) {
  if($trim === TRUE) {
    $defuse_string = trim($defuse_string);
  }
  global $dbl;
  return mysqli_real_escape_string($dbl, strip_tags($defuse_string));
}

/**
 * Ausgabefunktion
 * 
 * @param  string $string String, der ausgegeben werden soll.
 * 
 * @return string Der vorbereitete String.
 */
function output($string) {
  return htmlentities($string, ENT_QUOTES);
}

/**
 * Userlog Funktion
 * Zum loggen aller User Handlungen
 * 
 * @param int    $userId      userId des Users
 * @param int    $logLevel    logLevel der Aktion
 * @param string $text        optionaler Text
 */
function userLog($userId = NULL, int $logLevel, $text = NULL) {
  global $dbl;

  /**
   * Prüfung, ob die userId existiert. Falls nicht wird sie genullt.
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
   * Prüfung ob das logLevel existiert. Falls nicht, wird es auf "User-/Systemaktion" (1) gesetzt.
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
   * Entschärfen des Textes, sofern vorhanden.
   */
  if($text !== NULL) {
    $text = defuse($text);
  }

  /**
   * Eintragen ins Log
   */
  mysqli_query($dbl, "INSERT INTO `log` (`userId`, `logLevel`, `text`) VALUES (".($userId !== NULL ? "'".$userId."'" : "NULL").", '".$logLevel."', ".($text !== NULL ? "'".$text."'" : "NULL").")") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_affected_rows($dbl) != 0) {
    return true;
  } else {
    return false;
  }
}
?>
