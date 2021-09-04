<?php
/**
 * savings.php
 * 
 * Detailansicht der Ersparnisse
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Ersparnisse";
$content.= "<h1><span class='fas icon'>&#xf153;</span>Ersparnisse</h1>";

/**
 * Anzeigevarianten
 */
$content.= "<h2>Anzeigevarianten, unterteilt nach KFZs</h2>";
$content.= "<div class='row'>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=total'>Gesamtansicht</a></div>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=monthly'>Monatswerte</a></div>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=maxSaved'>Die 10 größten Sparbeträge</a></div>".
  "<div class='col-s-12 col-l-12'><a href='/savings?view=minUsed'>Am wenigsten verbraucht (100km)</a></div>".
  "<div class='col-s-12 col-l-12 small'>Vorschläge für weitere Anzeigevarianten gerne per <a href='mailto:info@tankersparnis.net'>Mail</a>, <a href='https://github.com/RundesBalli/tankersparnis/issues' target='_blank' rel='noopener'>Issue</a> oder <a href='https://github.com/RundesBalli/tankersparnis/pulls' target='_blank' rel='noopener'>PR</a>.</div>".
"</div>";
$content.= "<div class='spacer-m'></div>";

$content.= "<div class='row breakWord'>".
  "<div class='col-s-12 col-l-0 bold highlight'>Hinweis!</div>".
  "<div class='col-s-12 col-l-0'>Für eine detailliertere Ansicht musst du diese Seite von einem Computer aus aufrufen oder in den \"Desktop-Modus\" in deinem Handybrowser wechseln!</div>".
  "<div class='col-s-12 col-l-0 spacer-m'></div>".
"</div>";

if(!empty($_GET['view'])) {
  if($_GET['view'] == 'total') {
    /**
     * Gesamtansicht, unterteilt nach KFZs
     */
    $content.= "<h2>Gesamtansicht</h2>";
    $carResult = mysqli_query($dbl, "SELECT `cars`.`id`, `cars`.`name` FROM `cars` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($carResult) == 0) {
      /**
       * Es wurden noch keine KFZs angelegt
       */
      http_response_code(404);
      $content.= "<div class='infobox'>Du hast noch keine KFZs angelegt.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>KFZ anlegen</a></div>".
      "</div>";
    } else {
      /**
       * Es existieren KFZs
       */
      while($carRow = mysqli_fetch_array($carResult)) {
        $content.= "<h3>".output($carRow['name'])."</h3>";
        $result = mysqli_query($dbl, "SELECT `entries`.* FROM `entries` WHERE `entries`.`userId`=".$userId." AND `carId`=".$carRow['id']." ORDER BY `entries`.`timestamp` DESC") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $content.= "<div class='infobox'>Für dieses KFZ gibt es noch keine Einträge.</div>";
        } else {
          $content.= "<section>";
          $content.= "<div class='row bold breakWord small'>".
            "<div class='col-s-12 col-l-3'>Zeitpunkt</div>".
            "<div class='col-s-0 col-l-1'>Getankt (l/kg)</div>".
            "<div class='col-s-0 col-l-1'>Reichweite</div>".
            "<div class='col-s-0 col-l-1'>Verbrauch auf 100km (l/kg)</div>".
            "<div class='col-s-0 col-l-1'>Preis</div>".
            "<div class='col-s-0 col-l-1'>Preis/100km</div>".
            "<div class='col-s-6 col-l-1'>eingespart</div>".
            "<div class='col-s-6 col-l-3'>Aktion</div>".
          "</div>";
          $totalFuel = 0;
          $totalRange = 0;
          $totalCost = 0;
          $totalSavings = 0;
          while($row = mysqli_fetch_array($result)) {
            $content.= "<div class='row hover breakWord small'>".
              "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i", strtotime($row['timestamp']))." Uhr</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['fuelQuantity'], 2, ",", ".")."</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['range'], 1, ",", ".")."km</div>".
              "<div class='col-s-0 col-l-1'>".number_format(($row['fuelQuantity']/$row['range']*100), 1, ",", ".")."</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['cost'], 2, ",", ".")."€</div>".
              "<div class='col-s-0 col-l-1'>".number_format(($row['cost']/$row['range']*100), 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-1 highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-3'><a class='noUnderline' href='/deleteEntry?id=".output($row['id'])."'><span class='far icon'>&#xf2ed;</span></a></div>".
            "</div>";
            $totalFuel+= $row['fuelQuantity'];
            $totalRange+= $row['range'];
            $totalCost+= $row['cost'];
            $totalSavings+= $row['moneySaved'];
          }
          $content.= "<div class='row hover breakWord small bold italic'>".
            "<div class='col-s-0 col-l-3'>Gesamtwerte:</div>".
            "<div class='col-s-12 col-l-0'>Gesamtersparnis:</div>".
            "<div class='col-s-0 col-l-1'>".number_format($totalFuel, 2, ",", ".")."</div>".
            "<div class='col-s-0 col-l-2'>".number_format($totalRange, 1, ",", ".")."km</div>".
            "<div class='col-s-0 col-l-2'>".number_format($totalCost, 2, ",", ".")."€</div>".
            "<div class='col-s-12 col-l-4 highlightPositive bold'>".number_format($totalSavings, 2, ",", ".")."€</div>".
          "</div>";
          $content.= "</section>";
        }
      }
    }
  } elseif($_GET['view'] == 'monthly') {
    /**
     * Monatswerte
     */
    $content.= "<h2>Monatswerte</h2>";
    $carResult = mysqli_query($dbl, "SELECT `cars`.`id`, `cars`.`name` FROM `cars` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($carResult) == 0) {
      /**
       * Es wurden noch keine KFZs angelegt
       */
      http_response_code(404);
      $content.= "<div class='infobox'>Du hast noch keine KFZs angelegt.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>KFZ anlegen</a></div>".
      "</div>";
    } else {
      /**
       * Es existieren KFZs
       */
      while($carRow = mysqli_fetch_array($carResult)) {
        $content.= "<h3>".output($carRow['name'])."</h3>";
        $result = mysqli_query($dbl, "SELECT YEAR(`timestamp`) AS `y`, MONTH(`timestamp`) AS `m`, SUM(`fuelQuantity`) AS `fuelQuantity`, SUM(`range`) AS `range`, SUM(`cost`) AS `cost`, SUM(`moneySaved`) AS `moneySaved` FROM `entries` WHERE `carId`=".$carRow['id']." AND `userId`=".$userId." GROUP BY `y`, `m` ORDER BY `y` DESC, `m` DESC") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $content.= "<div class='infobox'>Für dieses KFZ gibt es noch keine Einträge.</div>";
        } else {
          $content.= "<section>";
          $content.= "<div class='row bold breakWord small'>".
            "<div class='col-s-6 col-l-1'>Jahr</div>".
            "<div class='col-s-6 col-l-2'>Monat</div>".
            "<div class='col-s-0 col-l-2'>Getankt (l/kg)</div>".
            "<div class='col-s-0 col-l-2'>Reichweite</div>".
            "<div class='col-s-0 col-l-2'>Preis</div>".
            "<div class='col-s-0 col-l-2'>Preis/100km</div>".
            "<div class='col-s-6 col-l-1'>eingespart</div>".
          "</div>";
          $totalFuel = 0;
          $totalRange = 0;
          $totalCost = 0;
          $totalSavings = 0;
          while($row = mysqli_fetch_array($result)) {
            $content.= "<div class='row hover breakWord small'>".
              "<div class='col-s-6 col-l-1'>".output($row['y'])."</div>".
              "<div class='col-s-6 col-l-2'>".$monthNames[$row['m']]."</div>".
              "<div class='col-s-0 col-l-2'>".number_format($row['fuelQuantity'], 2, ",", ".")."</div>".
              "<div class='col-s-0 col-l-2'>".number_format($row['range'], 1, ",", ".")."km</div>".
              "<div class='col-s-0 col-l-2'>".number_format($row['cost'], 2, ",", ".")."€</div>".
              "<div class='col-s-0 col-l-2'>".number_format(($row['cost']/$row['range']*100), 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-1 highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€</div>".
            "</div>";
            $totalFuel+= $row['fuelQuantity'];
            $totalRange+= $row['range'];
            $totalCost+= $row['cost'];
            $totalSavings+= $row['moneySaved'];
          }
          $content.= "<div class='row hover breakWord small bold italic'>".
            "<div class='col-s-0 col-l-3'>Gesamtwerte:</div>".
            "<div class='col-s-12 col-l-0'>Gesamtersparnis:</div>".
            "<div class='col-s-0 col-l-2'>".number_format($totalFuel, 2, ",", ".")."</div>".
            "<div class='col-s-0 col-l-2'>".number_format($totalRange, 1, ",", ".")."km</div>".
            "<div class='col-s-0 col-l-2'>".number_format($totalCost, 2, ",", ".")."€</div>".
            "<div class='col-s-0 col-l-2'>".number_format(($totalCost/$totalRange*100), 2, ",", ".")."€</div>".
            "<div class='col-s-12 col-l-1 highlightPositive bold'>".number_format($totalSavings, 2, ",", ".")."€</div>".
          "</div>";
          $content.= "</section>";
        }
      }
    }
  } elseif($_GET['view'] == 'maxSaved') {
    /**
     * Die 10 größten Sparbeträge
     */
    $content.= "<h2>Die 10 größten Sparbeträge</h2>";
    $carResult = mysqli_query($dbl, "SELECT `cars`.`id`, `cars`.`name` FROM `cars` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($carResult) == 0) {
      /**
       * Es wurden noch keine KFZs angelegt
       */
      http_response_code(404);
      $content.= "<div class='infobox'>Du hast noch keine KFZs angelegt.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>KFZ anlegen</a></div>".
      "</div>";
    } else {
      /**
       * Es existieren KFZs
       */
      while($carRow = mysqli_fetch_array($carResult)) {
        $content.= "<h3>".output($carRow['name'])."</h3>";
        $result = mysqli_query($dbl, "SELECT `entries`.* FROM `entries` WHERE `entries`.`userId`=".$userId." AND `carId`=".$carRow['id']." ORDER BY `entries`.`moneySaved` DESC LIMIT 10") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $content.= "<div class='infobox'>Für dieses KFZ gibt es noch keine Einträge.</div>";
        } else {
          $content.= "<section>";
          $content.= "<div class='row bold breakWord small'>".
            "<div class='col-s-12 col-l-3'>Zeitpunkt</div>".
            "<div class='col-s-0 col-l-1'>Getankt (l/kg)</div>".
            "<div class='col-s-0 col-l-1'>Reichweite</div>".
            "<div class='col-s-0 col-l-1'>Verbrauch auf 100km (l/kg)</div>".
            "<div class='col-s-0 col-l-1'>Preis</div>".
            "<div class='col-s-0 col-l-1'>Preis/100km</div>".
            "<div class='col-s-6 col-l-1'>eingespart</div>".
            "<div class='col-s-6 col-l-3'>Aktion</div>".
          "</div>";
          while($row = mysqli_fetch_array($result)) {
            $content.= "<div class='row hover breakWord small'>".
              "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i", strtotime($row['timestamp']))." Uhr</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['fuelQuantity'], 2, ",", ".")."</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['range'], 1, ",", ".")."km</div>".
              "<div class='col-s-0 col-l-1'>".number_format(($row['fuelQuantity']/$row['range']*100), 1, ",", ".")."</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['cost'], 2, ",", ".")."€</div>".
              "<div class='col-s-0 col-l-1'>".number_format(($row['cost']/$row['range']*100), 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-1 highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-3'><a class='noUnderline' href='/deleteEntry?id=".output($row['id'])."'><span class='far icon'>&#xf2ed;</span></a></div>".
            "</div>";
          }
          $content.= "</section>";
        }
      }
    }
  } elseif($_GET['view'] == 'minUsed') {
    /**
     * Am wenigsten verbraucht (100km), unterteilt nach KFZs
     */
    $content.= "<h2>Am wenigsten verbraucht (100km)</h2>";
    $carResult = mysqli_query($dbl, "SELECT `cars`.`id`, `cars`.`name` FROM `cars` WHERE `userId`=".$userId." ORDER BY `cars`.`id` ASC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($carResult) == 0) {
      /**
       * Es wurden noch keine KFZs angelegt
       */
      http_response_code(404);
      $content.= "<div class='infobox'>Du hast noch keine KFZs angelegt.</div>";
      $content.= "<div class='row'>".
        "<div class='col-s-12 col-l-12'><a href='/cars'><span class='fas icon'>&#xf1b9;</span>KFZ anlegen</a></div>".
      "</div>";
    } else {
      /**
       * Es existieren KFZs
       */
      while($carRow = mysqli_fetch_array($carResult)) {
        $content.= "<h3>".output($carRow['name'])."</h3>";
        $result = mysqli_query($dbl, "SELECT `entries`.*, ROUND((`fuelQuantity`/`range`*100), 1) AS `consumption` FROM `entries` WHERE `userId`=".$userId." AND `carId`=".$carRow['id']." ORDER BY `consumption` ASC LIMIT 10") OR DIE(MYSQLI_ERROR($dbl));
        if(mysqli_num_rows($result) == 0) {
          $content.= "<div class='infobox'>Für dieses KFZ gibt es noch keine Einträge.</div>";
        } else {
          $content.= "<section>";
          $content.= "<div class='row bold breakWord small'>".
            "<div class='col-s-12 col-l-3'>Zeitpunkt</div>".
            "<div class='col-s-0 col-l-1'>Getankt (l/kg)</div>".
            "<div class='col-s-0 col-l-1'>Reichweite</div>".
            "<div class='col-s-0 col-l-1'>Verbrauch auf 100km (l/kg)</div>".
            "<div class='col-s-0 col-l-1'>Preis</div>".
            "<div class='col-s-0 col-l-1'>Preis/100km</div>".
            "<div class='col-s-6 col-l-1'>eingespart</div>".
            "<div class='col-s-6 col-l-3'>Aktion</div>".
          "</div>";
          while($row = mysqli_fetch_array($result)) {
            $content.= "<div class='row hover breakWord small'>".
              "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i", strtotime($row['timestamp']))." Uhr</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['fuelQuantity'], 2, ",", ".")."</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['range'], 1, ",", ".")."km</div>".
              "<div class='col-s-0 col-l-1'>".number_format(($row['fuelQuantity']/$row['range']*100), 1, ",", ".")."</div>".
              "<div class='col-s-0 col-l-1'>".number_format($row['cost'], 2, ",", ".")."€</div>".
              "<div class='col-s-0 col-l-1'>".number_format(($row['cost']/$row['range']*100), 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-1 highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€</div>".
              "<div class='col-s-6 col-l-3'><a class='noUnderline' href='/deleteEntry?id=".output($row['id'])."'><span class='far icon'>&#xf2ed;</span></a></div>".
            "</div>";
          }
          $content.= "</section>";
        }
      }
    }
  } else {
    http_response_code(404);
    $content.= "<h2>Nicht gefunden</h2>";
    $content.= "<div class='warnbox'>Diese Anzeigevariante existiert nicht.</div>";
    $content.= "<div class='row'>".
      "<div class='col-s-12 col-l-12 small'>Vorschläge für weitere Anzeigevarianten gerne per <a href='mailto:info@tankersparnis.net'>Mail</a>, <a href='https://github.com/RundesBalli/tankersparnis/issues' target='_blank' rel='noopener'>Issue</a> oder <a href='https://github.com/RundesBalli/tankersparnis/pulls' target='_blank' rel='noopener'>PR</a>.</div>".
    "</div>";
  }
}
?>
