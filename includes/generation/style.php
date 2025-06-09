<?php
/**
 * includes/generation/style.php
 * 
 * Style selection
 */
if(empty($_COOKIE[$styleName])) {
  $style = $defaultStyle;
} elseif($_COOKIE[$styleName] == 'dark') {
  $style = 'dark';
} elseif($_COOKIE[$styleName] == 'light') {
  $style = 'light';
} else {
  $style = $defaultStyle;
}
setcookie($styleName, $style, time()+COOKIE_DURATION, NULL, NULL, TRUE, TRUE);
?>
