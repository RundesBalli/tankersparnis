<?php
/**
 * Tankersparnis.net
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2025 RundesBalli
 * @version   1.1
 * @see       https://github.com/RundesBalli/tankersparnis
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Initialize the output and the default title.
 */
$content = '';
$title = '';

/**
 * Including the configuration and function loader, the page generation elements, the router and the output generation.
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'loader.php');

/**
 * Output the generated and tidied output.
 */
echo $output;
?>
