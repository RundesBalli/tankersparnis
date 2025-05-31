<?php
/**
 * myData.php
 * 
 * Datenexport der eigenen Daten.
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

$executionTime = microtime(true);

$export['__INFO']['_'] = "Datenexport von https://".$_SERVER['HTTP_HOST'];
$export['__INFO']['time'] = date("Y-m-d H:i:s");
$export['__INFO']['timestamp'] = time();

$queryCount = 0;

/**
 * Abfragen der Usertabelle
 */
$result = mysqli_query($dbl, "SELECT `id`, `email`, `registered`, `lastActivity`, `preventPasswordReset`, `lastPwReset` FROM `users` WHERE `id`=".$userId) OR DIE(MYSQLI_ERROR($dbl)); $queryCount++;
$export['user'] = mysqli_fetch_assoc($result);

/**
 * Abfragen der Autotabelle
 */
$result = mysqli_query($dbl, "SELECT `id`, `name`, `fuel`, `fuelCompare` FROM `cars` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl)); $queryCount++;
while($row = mysqli_fetch_assoc($result)) {
  $export['cars'][$row['id']] = $row;
}

/**
 * Abfragen der Einträge
 */
$result = mysqli_query($dbl, "SELECT `id`, `carId`, `timestamp`, `fuelQuantity`, `range`, `cost`, `moneySaved`, `raw` FROM `entries` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl)); $queryCount++;
while($row = mysqli_fetch_assoc($result)) {
  $export['entries'][$row['id']] = $row;
}

/**
 * Abfragen der fehlgeschlagenen Emails
 */
$result = mysqli_query($dbl, "SELECT `id`, `to`, `subject`, `message`, `timestamp` FROM `failedEmails` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl)); $queryCount++;
while($row = mysqli_fetch_assoc($result)) {
  $export['failedEmails'][$row['id']] = $row;
}

/**
 * Abfragen der Logeinträge
 */
$result = mysqli_query($dbl, "SELECT `id`, `timestamp`, `loglevel`, `text` FROM `log` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl)); $queryCount++;
while($row = mysqli_fetch_assoc($result)) {
  $export['log'][$row['id']] = $row;
}

/**
 * Abfragen der Sessions
 */
$result = mysqli_query($dbl, "SELECT `id`, `lastActivity` FROM `sessions` WHERE `userId`=".$userId) OR DIE(MYSQLI_ERROR($dbl)); $queryCount++;
while($row = mysqli_fetch_assoc($result)) {
  $export['sessions'][$row['id']] = $row;
}

/**
 * Statusvariablen ausgeben
 */
$export['__INFO']['queryCount'] = $queryCount;
$export['__INFO']['executionTime'] = round((microtime(true) - $executionTime)*1000);

/**
 * Ausgabe
 */
header('Content-Type: application/json; charset=utf-8');
echo json_encode($export);
die();
?>
