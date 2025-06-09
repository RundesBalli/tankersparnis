<?php
/**
 * utcConversion.php
 * 
 * Script to convert all timestamps to UTC.
 */

/**
 * Including the configuration and function loader.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Config
 */
date_default_timezone_set('Europe/Berlin');
$toTimezone = new DateTimeZone('UTC');

## log: timestamp
$result = mysqli_query($dbl, 'SELECT `id`, `timestamp` FROM `log`') OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $oldTime = new DateTime($row['timestamp']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `log` SET `timestamp`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
}

## sessions: lastActivity
$result = mysqli_query($dbl, 'SELECT `id`, `lastActivity` FROM `sessions`') OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $oldTime = new DateTime($row['lastActivity']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `sessions` SET `lastActivity`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
}

## users: registered, last activity, reminderDate, lastPwReset
$result = mysqli_query($dbl, 'SELECT `id`, `registered`, `lastActivity`, `reminderDate`, `lastPwReset` FROM `users`') OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  $oldTime = new DateTime($row['registered']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `users` SET `registered`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));

  $oldTime = new DateTime($row['lastActivity']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `users` SET `lastActivity`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));

  $oldTime = new DateTime($row['reminderDate']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `users` SET `reminderDate`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));

  $oldTime = new DateTime($row['lastPwReset']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `users` SET `lastPwReset`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
}

## entries: timestamp
$result = mysqli_query($dbl, 'SELECT `id`, `timestamp` FROM `entries`') OR DIE(MYSQLI_ERROR($dbl));
while($row = mysqli_fetch_assoc($result)) {
  if(str_ends_with($row['timestamp'], '00:00:00')) {
    continue;
  }
  $oldTime = new DateTime($row['timestamp']);
  $oldTime->setTimezone($toTimezone);
  $newTime = $oldTime->format('Y-m-d H:i:s');
  mysqli_query($dbl, 'UPDATE `entries` SET `timestamp`="'.$newTime.'" WHERE `id`='.$row['id'].' LIMIT 1') OR DIE(MYSQLI_ERROR($dbl));
}

echo 'Done.'."\n";
?>
