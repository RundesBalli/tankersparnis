<?php
/**
 * Tankersparnis.net
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2021 RundesBalli
 * @version   1.0
 * @see       https://github.com/RundesBalli/tankersparnis
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
  'changeStyle'    => 'changeStyle.php',
  'register'       => 'register.php',
  'activate'       => 'activate.php',
  'pwReset'        => 'pwReset.php',
  'confirmEmail'   => 'confirmEmail.php',
  
  /* Userseiten */
  'login'          => 'login.php',
  'logout'         => 'logout.php',
  'entry'          => 'entry.php',
  'addEntry'       => 'addEntry.php',
  'deleteEntry'    => 'deleteEntry.php',
  'cars'           => 'cars.php',
  'addCar'         => 'addCar.php',
  'editCar'        => 'editCar.php',
  'deleteCar'      => 'deleteCar.php',
  'settings'       => 'settings.php',
  'changeSettings' => 'changeSettings.php',
  'savings'        => 'savings.php',
  'import'         => 'import.php',
  'myData'         => 'myData.php',
  'statistics'     => 'statistics.php',

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
 * Stil Auswahl
 */
if(empty($_COOKIE[$styleName])) {
  $style = $defaultStyle;
} elseif($_COOKIE[$styleName] == "dark") {
  $style = "dark";
} elseif($_COOKIE[$styleName] == "light") {
  $style = "light";
} else {
  $style = $defaultStyle;
}
setcookie($styleName, $style, time()+(6*7*86400), NULL, NULL, TRUE, TRUE);

/**
 * Navigation
 * Hinweis: das Toggle-Element ist im Template enthalten.
 */
$a = " class='active'";
if(!empty($_COOKIE[$cookieName]) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  $nav = "<a href='/entry'".($getp == "entry" ? $a : NULL)."><span class='far icon'>&#xf044;</span>Eintrag hinzufügen</a>";
  $nav.= "<a href='/savings'".($getp == "savings" ? $a : NULL)."><span class='fas icon'>&#xf153;</span>Ersparnisse</a>";
  $nav.= "<a href='/cars'".($getp == "cars" ? $a : NULL)."><span class='fas icon'>&#xf1b9;</span>KFZs</a>";
  $nav.= "<a href='/settings'".($getp == "settings" ? $a : NULL)."><span class='fas icon'>&#xf013;</span>Einstellungen</a>";
  $nav.= "<a href='/import'".($getp == "import" ? $a : NULL)."><span class='fas icon'>&#xf56f;</span>Importieren</a>";
  $nav.= "<a href='/logout'".($getp == "logout" ? $a : NULL)."><span class='fas icon'>&#xf2f5;</span>Logout</a>";
  if(defined("perm-showStatistics")) {
    $nav.= "<a href='/statistics'".($getp == "statistics" ? $a : NULL)."><span class='fas icon highlight'>&#xf201;</span>Statistiken</a>";
  }
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
$footer.= "<a href='https://github.com/RundesBalli/tankersparnis' target='_blank' rel='noopener'><span class='fab icon'>&#xf09b;</span>GitHub</a>";
$footer.= "<a href='https://RundesBalli.com/' target='_blank' rel='noopener'>🎱 RundesBalli</a>";
$footer.= "<a href='https://creativecommons.tankerkoenig.de/' target='_blank' rel='noopener'><span class='fas icon'>&#xf52f;</span>Tankerkönig-API</a>";
$footer.= "<a href='/changeStyle?back=".$getp."'><span class='fas icon'>&#xf042;</span>Stil wechseln</a>";

/**
 * Templateeinbindung und Einsetzen der Variablen
 */
$templatefile = __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."template.tpl";
$fp = fopen($templatefile, "r");
$output = preg_replace(array("/{TITLE}/im", "/{STYLE}/im", "/{NAV}/im", "/{FOOTER}/im", "/{CONTENT}/im"), array((empty($title) ? "" : " - ".$title), $style, $nav, $footer, $content), fread($fp, filesize($templatefile)));
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
