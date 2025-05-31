<?php
/**
 * changeStyle.php
 * 
 * Seite zum Wechseln zwischen Dark-/Lightmode
 */

if(empty($_COOKIE[$styleName])) {
  setcookie($styleName, ($defaultStyle == 'dark' ? 'light' : 'dark'), time()+COOKIE_DURATION, NULL, NULL, TRUE, TRUE);
} else {
  setcookie($styleName, ($_COOKIE[$styleName] == 'dark' ? 'light' : 'dark'), time()+COOKIE_DURATION, NULL, NULL, TRUE, TRUE);
}

header('Location: /'.$route);
die();
?>
