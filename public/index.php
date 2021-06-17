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
  'privacy'        => 'privacy.php',

  /* Userseiten */
  'register'       => 'register.php',
  'activate'       => 'activate.php',
  'login'          => 'login.php',
  'pwReset'        => 'pwReset.php',
  'logout'         => 'logout.php',
  'overview'       => 'overview.php',
  'addEntry'       => 'addEntry.php',
  'cars'           => 'cars.php',
  'addCar'         => 'addCar.php',
  'editCar'        => 'editCar.php',
  'deleteCar'      => 'deleteCar.php',
  'settings'       => 'settings.php',
  'changeSettings' => 'changeSettings.php',
  'confirmEmail'   => 'confirmEmail.php',

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
if(!empty($_COOKIE[$cookieName]) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  $nav = "<a href='/overview'".($getp == "overview" ? $a : NULL)."><span class='fas icon'>&#xf0cb;</span>Übersicht</a>";
  $nav.= "<a href='/cars'".($getp == "cars" ? $a : NULL)."><span class='fas icon'>&#xf1b9;</span>KFZs</a>";
  $nav.= "<a href='/settings'".($getp == "settings" ? $a : NULL)."><span class='fas icon'>&#xf013;</span>Einstellungen</a>";
  $nav.= "<a href='/logout'".($getp == "logout" ? $a : NULL)."><span class='fas icon'>&#xf2f5;</span>Logout</a>";
} else {
  $nav = "<a href='/'".($getp == "start" ? $a : NULL)."><span class='fas icon'>&#xf015;</span>Startseite</a>";
  $nav.= "<a href='/login'".($getp == "login" ? $a : NULL)."><span class='fas icon'>&#xf2f6;</span>Login</a>";
  $nav.= "<a href='/register'".($getp == "register" ? $a : NULL)."><span class='far icon'>&#xf044;</span>Registrieren</a>";
  $nav.= "<a href='/info'".($getp == "info" ? $a : NULL)."><span class='fas icon'>&#xf128;</span>Info</a>";
}

/**
 * Footer
 */
$footer = "<a href='/imprint'".($getp == "imprint" ? $a : NULL)."><span class='fas icon'>&#xf21b;</span>Impressum</a>";
$footer.= "<a href='/privacy'".($getp == "privacy" ? $a : NULL)."><span class='fas icon'>&#xf1c0;</span>Datenschutz</a>";
$footer.= "<a href='https://github.com/RundesBalli/tankersparnis.net' target='_blank' rel='noopener'><span class='fab icon'>&#xf09b;</span>GitHub</a>";
$footer.= "<a href='https://RundesBalli.com/' target='_blank' rel='noopener'>🎱 RundesBalli</a>";
$footer.= "<a href='https://creativecommons.tankerkoenig.de/' target='_blank' rel='noopener'><span class='fas icon'>&#xf52f;</span>Tankerkönig-API</a>";

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
