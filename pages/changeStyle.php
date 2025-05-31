<?php
/**
 * changeStyle.php
 * 
 * Seite zum Wechseln zwischen Dark-/Lightmode
 */

if(empty($_COOKIE[$styleName])) {
  setcookie($styleName, ($defaultStyle == "dark" ? "light" : "dark"), time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
} else {
  setcookie($styleName, ($_COOKIE[$styleName] == "dark" ? "light" : "dark"), time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
}

if(!empty($_GET['back'])) {
  preg_match("/([\d\w-]+)/i", $_GET['back'], $match);
  $redirect = $match[1];
  if(isset($pageArray[$redirect])) {
    header("Location: /".$redirect);
    die();
  } else {
    header("Location: /start");
    die();
  }
} else {
  header("Location: /start");
  die();
}
?>
