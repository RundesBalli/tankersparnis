<?php
/**
 * includes/functions/showLog.php
 * 
 * Function to show log texts.
 */

/**
 * showLog
 * Function to show log texts.
 * 
 * @param string $text The log text
 */
function showLog(string $text){
  return nl2br(preg_replace("/`(.*?)`/", "<code>$1</code>", output($text)));
}
?>
