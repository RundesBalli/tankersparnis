<?php
/**
 * changeStyle.php
 * 
 * Seite zum Wechseln zwischen Dark-/Lightmode
 */
$title = "Stil ändern";

$content.= "<h1><span class='fas icon'>&#xf042;</span>Stil ändern</h1>";

$styleNames = array("dark" => "Darkmode", "light" => "Lightmode");

if(empty($_POST['submit'])) {
  /**
   * Es wurde kein Formular abgesendet
   */
  if(empty($_COOKIE[$styleName]) OR !array_key_exists($_COOKIE[$styleName], $styleNames)) {
    /**
     * Es existiert kein Stil Cookie oder der darin enthaltene Wert existiert nicht im Stil Array.
     */
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Du bist aktuell im <span class='bold highlight'>".$styleNames[$defaultStyle]."</span></div>".
    "</div>";
    $tabindex = 1;
    $content.= "<form action='/changeStyle' method='post'>";
    $content.= "<section>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><input type='submit' id='submit' name='submit' value='in den ".($defaultStyle == "dark" ? $styleNames['light'] : $styleNames['dark'])." wechseln' tabindex='".$tabindex++."'></div>".
    "</div>";
    $content.= "</section>";
    $content.= "<div class='infobox'>Wenn du den Stil wechselst, wird ein Cookie gesetzt. Mit dem Fortfahren erklärst du dich damit einverstanden.</div>";
    $content.= "</form>";
  } else {
    /**
     * Es existiert ein Stil Cookie und der darin enthaltene Wert existiert im Stil Array.
     */
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'>Du bist aktuell im <span class='bold highlight'>".$styleNames[$_COOKIE[$styleName]]."</span></div>".
    "</div>";
    $tabindex = 1;
    $content.= "<form action='/changeStyle' method='post'>";
    $content.= "<section>";
    $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><input type='submit' id='submit' name='submit' value='in den ".($_COOKIE[$styleName] == "dark" ? $styleNames['light'] : $styleNames['dark'])." wechseln' tabindex='".$tabindex++."'></div>".
    "</div>";
    $content.= "</section>";
    $content.= "<div class='infobox'>Wenn du den Stil wechselst, wird ein Cookie gesetzt. Mit dem Fortfahren erklärst du dich damit einverstanden.</div>";
    $content.= "</form>";
  }
} else {
  /**
   * Das Formular wurde abgesendet.
   */
  if(empty($_COOKIE[$styleName])) {
    $style = ($defaultStyle == "dark" ? "light" : "dark");
    setcookie($styleName, ($defaultStyle == "dark" ? "light" : "dark"), time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
  } else {
    $style = ($_COOKIE[$styleName] == "dark" ? "light" : "dark");
    setcookie($styleName, ($_COOKIE[$styleName] == "dark" ? "light" : "dark"), time()+(6*7*86400), NULL, NULL, TRUE, TRUE);
  }
  $content.= "<div class='successbox'>Stil wurde geändert!</div>";
}
?>
