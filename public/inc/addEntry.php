<?php
/**
 * addEntry.php
 * 
 * Seite zum Hinzufügen eines neuen Eintrages
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Eintrag hinzufügen";
$content.= "<h1><span class='far icon'>&#xf044;</span>Eintrag hinzufügen</h1>";

if(empty($_POST['token'])) {
  /**
   * Es wurde kein Token übergeben.
   */
  http_response_code(403);
  $content.= "<div class='warnbox'>Es wurde kein Token übergeben.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</div>".
  "</div>";
} elseif($_POST['token'] != $sessionhash) {
  /**
   * Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.
   */
  http_response_code(403);
  $content.= "<div class='warnbox'>Das übergebene Token stimmt nicht mit dem Sitzungstoken überein.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</div>".
  "</div>";
} elseif(empty($_POST['car']) OR empty($_POST['fuel']) OR empty($_POST['range']) OR empty($_POST['cost'])) {
  /**
   * Wenigstens eins der übergebenen Felder ist leer.
   */
  http_response_code(403);
  $content.= "<div class='warnbox'>Du musst alle Felder ausfüllen.</div>";
  $content.= "<div class='row'>".
    "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</div>".
  "</div>";
} else {
  /**
   * Alle Felder wurden übergeben. Nun muss geprüft werden, ob das KFZ korrekt ist.
   */
  $car = intval(defuse($_POST['car']));
  $result = mysqli_query($dbl, "SELECT `cars`.*, `fuels`.`energy` AS `energy`, `fuelsCompare`.`energy` AS `energyCompare`, `fuelsCompare`.`symbol` AS `symbol` FROM `cars` JOIN `fuels` ON `fuels`.`id`=`cars`.`fuel` JOIN `fuelsCompare` ON `fuelsCompare`.`id`=`cars`.`fuelCompare` WHERE `cars`.`userId`=".$userId." AND `cars`.`id`=".$car." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
  if(mysqli_num_rows($result) != 1) {
    http_response_code(403);
    $content.= "<div class='warnbox'>Das übergebene Fahrzeug ist ungültig.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</div>".
    "</div>";
  } else {
    /**
     * Das KFZ ist gültig. Nun werden die restlichen übergebenen Werte entschärft und aufgewertet.
     */
    $row = mysqli_fetch_array($result);
    $fuel = round(floatval(str_replace(",", ".", defuse($_POST['fuel']))), 2);
    $range = round(floatval(str_replace(",", ".", defuse($_POST['range']))), 1);
    $cost = round(floatval(str_replace(",", ".", defuse($_POST['cost']))), 2);
    if($fuel <= 0 OR $range <= 0 OR $cost <= 0) {
      http_response_code(403);
      $content.= "<div class='warnbox'>Die Eingaben sind ungültig.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</div>".
      "</div>";
    } else {
      /**
       * Alle Eingaben sind im positiven Wertebereich und größer als Null.
       * Nun erfolgt die Geo-Auswertung
       */
      $ok = 1;
      if(!empty($_POST['geo']) AND preg_match('/^(-?\d{1,3}\.\d+);(-?\d{1,3}\.\d+)$/', defuse($_POST['geo']), $matches) === 1) {
        /**
         * Wenn ein Geo Punkt übergeben wurde, wird dieser an die API übergeben und die Tankstellen im Umkreis werden abgerufen.
         */
        $response = apiCall(
          "https://creativecommons.tankerkoenig.de/json/list.php",
          array(
            'lat' => floatval($matches[1]),
            'lng' => floatval($matches[2]),
            'rad' => 10,
            'type' => $row['symbol'],
            'sort' => 'price'
          )
        );
        /**
         * Preisfindung anhand der nächstgelegenen Tankstelle. Sofern keine Tankstelle geöffnet hat oder keine Tankstelle in der Umgebung
         * den Vergleichskraftstoff führt, wird kein Vergleichspreis gespeichert und der bundesweite Median wird genommen.
         */
        if(!empty($response) AND is_array($response['stations']) AND $response['ok'] === TRUE) {
          foreach($response['stations'] AS $key => $val) {
            $content.= "<div class='row'>".
              "<div class='col-s-0 col-l-0'>".var_export($val, TRUE)."</div>".
            "</div>";
            if($val['isOpen'] === TRUE AND $val['price'] !== NULL) {
              $priceCompare = $val['price'];
              $pricing = $val;
              break;
            }
          }
        }
      }
      
      if(empty($priceCompare)) {
        /**
         * Geo Auswertung wurde nicht übergeben oder konnte nicht erfolgen. Nehme bundesweiten Durchschnittspreis.
         */
        $response = apiCall(
          "https://creativecommons.tankerkoenig.de/api/v4/stats",
          NULL
        );
        if($response !== FALSE) {
          $response = array_change_key_case($response, CASE_LOWER);
          $priceCompare = $response[$row['symbol']]['median'];
          $pricing = $response[$row['symbol']];
        } else {
          $ok = 0;
        }
      }
      
      if($ok == 0) {
        http_response_code(403);
        $content.= "<div class='warnbox'>Es konnte kein Vergleichspreis ermittelt werden.</div>";
        $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Erneut versuchen</div>".
        "</div>";
      } else {
        /**
         * Eintragen
         */
        $energyUsed = $fuel*$row['energy']; // Energie des Getankten Kraftstoffes in kW
        $fuelCompare = $energyUsed/$row['energyCompare']; // Benötigte Menge des Vergleichskraftstoffes
        $costCompare = $fuelCompare*$priceCompare; // Gesamtpreis des Vergleichskraftstoffes
        $moneySaved = $costCompare-$cost; // Ersparnis gegenüber dem Vergleichskraftstoff
        mysqli_query($dbl, "INSERT INTO `entries` (`userId`, `carId`, `fuelQuantity`, `range`, `cost`, `moneySaved`, `raw`) VALUES (".$userId.", ".$car.", ".$fuel.", ".$range.", ".$cost.", ".$moneySaved.", '".defuse(json_encode($pricing))."')") OR DIE(MYSQLI_ERROR($dbl));
        userLog($userId, 2, "Eintrag hinzugefügt. ".number_format($moneySaved, 2, ",", ".")."€ gespart");
        $content.= "<div class='successbox'>Eintrag hinzugefügt. Du hast ".number_format($moneySaved, 2, ",", ".")."€ gespart!</div>";
        $content.= "<div class='row'>".
          "<div class='col-s-12 col-l-12'><a href='/entry'><span class='far icon'>&#xf044;</span>Zurück zum Formular</div>".
          "<div class='col-s-12 col-l-12'><a href='/savings'><span class='fas icon'>&#xf153;</span>Ersparnisse ansehen</div>".
        "</div>";
      }
    }
  }
}
?>
