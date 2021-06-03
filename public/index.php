<?php
/**
 * Tankersparnis.net
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2021 RundesBalli
 * @version   1.0
 * @see       https://github.com/RundesBalli/tankersparnis.net
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Initialisieren des Outputs und des Standardtitels
 */
$content = "";
$title = "";

/**
 * Herausfinden welche Seite angefordert wurde
 */
if(!isset($_GET['p']) OR empty($_GET['p'])) {
  $getp = "start";
} else {
  preg_match("/([\d\w-]+)/i", $_GET['p'], $match);
  $getp = $match[1];
}

/**
 * Das Seitenarray für die Seitenzuordnung
 */
$pageArray = array(
  /* Standardseiten */
  'start'          => 'start.php',
  'imprint'        => 'imprint.php',
  'info'           => 'info.php',
  'datasafety'     => 'datasafety.php',

  /* Userseiten */
  'login'          => 'login.php',
  'logout'         => 'logout.php',
  'overview'       => 'overview.php',

  /* Fehlerseiten */
  '404'            => '404.php',
  '403'            => '403.php'
);

/**
 * Prüfung ob die Unterseite im Array existiert, falls nicht 404
 */
if(isset($pageArray[$getp])) {
  require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR.$pageArray[$getp]);
} else {
  require_once(__DIR__.DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."404.php");
}

/**
 * Navigation
 * Hinweis: das Toggle-Element ist im Template enthalten.
 */
$a = " class='active'";
if((isset($_COOKIE[$cookieName]) AND !empty($_COOKIE[$cookieName])) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {

} else {
  $nav = "<a href='/'".($getp == "start" ? $a : NULL)."><span class='fas icon'>&#xf015;</span>Startseite</a>";
  $nav.= "<a href='/login'".($getp == "login" ? $a : NULL)."><span class='fas icon'>&#xf2f6;</span>Login</a>";
  $nav.= "<a href='/info'".($getp == "info" ? $a : NULL)."><span class='fas icon'>&#xf128;</span>Info</a>";
}

/**
 * Footer
 */
$footer = "<a href='/imprint'".($getp == "imprint" ? $a : NULL)."><span class='fas icon'>&#xf21b;</span>Impressum</a>";
$footer.= "<a href='/datasafety'".($getp == "datasafety" ? $a : NULL)."><span class='fas icon'>&#xf1c0;</span>Datenschutz</a>";
$footer.= "<a href='https://github.com/RundesBalli/tankersparnis.net' target='_blank' rel='noopener'><span class='fab icon'>&#xf09b;</span>GitHub</a>";
$footer.= "<a href='https://RundesBalli.com/' target='_blank' rel='noopener'>🎱 RundesBalli</a>";

/**
 * Templateeinbindung und Einsetzen der Variablen
 */
$templatefile = __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."template.tpl";
$fp = fopen($templatefile, "r");
$output = preg_replace(array("/{TITLE}/im", "/{NAV}/im", "/{FOOTER}/im", "/{CONTENT}/im"), array((empty($title) ? "" : " - ".$title), $nav, $footer, $content), fread($fp, filesize($templatefile)));
fclose($fp);

/**
 * Tidy HTML Output
 * @see https://gist.github.com/RundesBalli/a5d20a8c92a9a004803980654e638cbb
 * @see https://api.html-tidy.org/tidy/quickref_5.6.0.html
 */

$tidyOptions = array(
  'indent' => TRUE,
  'output-xhtml' => TRUE,
  'wrap' => 200,
  'newline' => 'LF', /* LF = \n */
  'output-encoding' => 'utf8',
  'drop-empty-elements' => FALSE /* e.g. for placeholders */
);

$tidy = tidy_parse_string($output, $tidyOptions, 'UTF8');
tidy_clean_repair($tidy);
echo $tidy;

?>