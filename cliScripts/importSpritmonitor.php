<?php
/**
 * importSpritmonitor.php
 * 
 * Script zum Importieren eines Datendumps von Spritmonitor.de
 */

/**
 * Einbinden der Konfigurationsdatei sowie der Funktionsdatei
 */
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."config.php");
require_once(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR."inc".DIRECTORY_SEPARATOR."functions.php");

/**
 * Eckdaten User
 */
$userId = 0;
$carId = 0;

/**
 * Prüfung der User/Auto Kombination
 */
$result = mysqli_query($dbl, "SELECT `cars`.*, `fuels`.`energy` AS `energy`, `fuelsCompare`.`energy` AS `energyCompare`, `fuelsCompare`.`symbol` AS `symbol` FROM `cars` JOIN `fuels` ON `fuels`.`id`=`cars`.`fuel` JOIN `fuelsCompare` ON `fuelsCompare`.`id`=`cars`.`fuelCompare` WHERE `cars`.`userId`=".intval(defuse($userId))." AND `cars`.`id`=".intval(defuse($carId))." LIMIT 1") OR DIE(MYSQLI_ERROR($dbl));
if(mysqli_num_rows($result) == 0) {
  die("User/Auto Kombination ungültig.");
} else {
  $row = mysqli_fetch_assoc($result);
}

/**
 * Eckdaten Input
 */
$inputfile = "./input.csv";
$fp = fopen($inputfile, "r");
$input = [];
while($input[] = fgetcsv(
  $fp,
  0, // Length, 0=unlimited
  ";", // Delimeter
  '"', // Enclosure
  "\\" // Escape
)) {
}

/**
 * Ausgabe des Inputs zur Kontrolle.
 */
//echo var_export($input, TRUE)."\n";

/**
 * Wenn wirklich ein Input stattfinden soll, dann das die(); auskommentieren.
 */
//die();

/**
 * Durchschnittspreise
 * @see https://www.mwv.de/statistiken/verbraucherpreise/
 * @see https://en2x.de/service/statistiken/verbraucherpreise/
 */
$averages['e5'][2010] = [NULL, 1.3695, 1.3495, 1.4255, 1.4465, 1.4435, 1.4365, 1.4145, 1.4015, 1.4035, 1.4025, 1.4125, 1.4705];
$averages['e5'][2011] = [NULL, 1.4915, 1.4765, 1.5555, 1.6005, 1.6055, 1.5625, 1.5745, 1.5455, 1.5882, 1.5662, 1.5515, 1.5295];
$averages['e5'][2012] = [NULL, 1.5760, 1.6156, 1.6820, 1.7110, 1.6523, 1.6058, 1.6254, 1.6939, 1.7367, 1.6584, 1.6021, 1.5850];
$averages['e5'][2013] = [NULL, 1.5863, 1.6302, 1.5750, 1.6118, 1.5948, 1.5991, 1.6274, 1.6217, 1.6146, 1.5623, 1.5340, 1.5495];
$averages['e5'][2014] = [NULL, 1.5170, 1.5269, 1.5241, 1.5580, 1.5637, 1.5878, 1.5863, 1.5510, 1.5595, 1.5241, 1.4731, 1.3684];
$averages['e5'][2015] = [NULL, 1.2920, 1.3415, 1.4052, 1.4462, 1.4802, 1.4859, 1.5028, 1.4406, 1.3599, 1.3330, 1.3444, 1.2920];
$averages['e5'][2016] = [NULL, 1.2495, 1.2170, 1.2227, 1.2736, 1.3104, 1.3444, 1.3146, 1.2948, 1.3132, 1.3415, 1.3132, 1.3571];
$averages['e5'][2017] = [NULL, 1.3882, 1.3939, 1.3628, 1.3939, 1.3599, 1.3444, 1.3302, 1.3401, 1.3712, 1.3472, 1.3854, 1.3684];
$averages['e5'][2018] = [NULL, 1.3684, 1.3727, 1.3415, 1.3896, 1.4562, 1.4760, 1.4745, 1.4944, 1.5326, 1.5340, 1.5679, 1.4689];
$averages['e5'][2019] = [NULL, 1.3574, 1.3477, 1.3700, 1.4633, 1.5316, 1.5093, 1.4884, 1.4480, 1.4257, 1.4173, 1.4159, 1.4076];
$averages['e5'][2020] = [NULL, 1.4257, 1.4076, 1.3435, 1.2194, 1.1971, 1.2668, 1.2933, 1.2822, 1.2794, 1.2891, 1.2417, 1.2654];
$averages['e5'][2021] = [NULL, 1.3964, 1.4396, 1.5149, 1.5233, 1.5358, 1.5595, 1.6027, 1.6222, 1.6278, 1.7058, 1.7560, 1.6696];
$averages['e5'][2022] = [NULL, 1.7212, 1.7881, 2.1546, 2.0319, 2.1002, 1.9943, 1.8689, 1.7699, 2.0166, 1.9846, 1.9260, 1.7546];
$averages['e5'][2023] = [NULL, 1.7941, 1.8200];
// letzter Wert ist eine ungefähre Schätzung.

// Keine belastbaren Vergleichswerte für E10, daher Kopie von E5.
$averages['e10'][2010] = [NULL, 1.3695, 1.3495, 1.4255, 1.4465, 1.4435, 1.4365, 1.4145, 1.4015, 1.4035, 1.4025, 1.4125, 1.4705];
$averages['e10'][2011] = [NULL, 1.4915, 1.4765, 1.5555, 1.6005, 1.6055, 1.5625, 1.5745, 1.5455, 1.5882, 1.5662, 1.5515, 1.5295];
$averages['e10'][2012] = [NULL, 1.5760, 1.6156, 1.6820, 1.7110, 1.6523, 1.6058, 1.6254, 1.6939, 1.7367, 1.6584, 1.6021, 1.5850];
$averages['e10'][2013] = [NULL, 1.5863, 1.6302, 1.5750, 1.6118, 1.5948, 1.5991, 1.6274, 1.6217, 1.6146, 1.5623, 1.5340, 1.5495];
$averages['e10'][2014] = [NULL, 1.5170, 1.5269, 1.5241, 1.5580, 1.5637, 1.5878, 1.5863, 1.5510, 1.5595, 1.5241, 1.4731, 1.3684];
$averages['e10'][2015] = [NULL, 1.2920, 1.3415, 1.4052, 1.4462, 1.4802, 1.4859, 1.5028, 1.4406, 1.3599, 1.3330, 1.3444, 1.2920];
$averages['e10'][2016] = [NULL, 1.2495, 1.2170, 1.2227, 1.2736, 1.3104, 1.3444, 1.3146, 1.2948, 1.3132, 1.3415, 1.3132, 1.3571];
$averages['e10'][2017] = [NULL, 1.3882, 1.3939, 1.3628, 1.3939, 1.3599, 1.3444, 1.3302, 1.3401, 1.3712, 1.3472, 1.3854, 1.3684];
$averages['e10'][2018] = [NULL, 1.3684, 1.3727, 1.3415, 1.3896, 1.4562, 1.4760, 1.4745, 1.4944, 1.5326, 1.5340, 1.5679, 1.4689];
$averages['e10'][2019] = [NULL, 1.3574, 1.3477, 1.3700, 1.4633, 1.5316, 1.5093, 1.4884, 1.4480, 1.4257, 1.4173, 1.4159, 1.4076];
$averages['e10'][2020] = [NULL, 1.4257, 1.4076, 1.3435, 1.2194, 1.1971, 1.2668, 1.2933, 1.2822, 1.2794, 1.2891, 1.2417, 1.2654];
$averages['e10'][2021] = [NULL, 1.3964, 1.4396, 1.5149, 1.5233, 1.5358, 1.5595, 1.6027, 1.6222, 1.6278, 1.7058, 1.7560, 1.6696];
$averages['e10'][2022] = [NULL, 1.7212, 1.7881, 2.1546, 2.0319, 2.1002, 1.9943, 1.8689, 1.7699, 2.0166, 1.9846, 1.9260, 1.7546];
$averages['e10'][2023] = [NULL, 1.7941, 1.8200];
// letzter Wert ist eine ungefähre Schätzung.

$averages['diesel'][2010] = [NULL, 1.1704, 1.1344, 1.2064, 1.2394, 1.2454, 1.2444, 1.2214, 1.2104, 1.2264, 1.2394, 1.2534, 1.3074];
$averages['diesel'][2011] = [NULL, 1.3334, 1.3694, 1.4374, 1.4604, 1.4094, 1.4234, 1.4234, 1.3924, 1.4343, 1.4439, 1.4674, 1.4324];
$averages['diesel'][2012] = [NULL, 1.4550, 1.4809, 1.5300, 1.5250, 1.4766, 1.4264, 1.4467, 1.5161, 1.5427, 1.5065, 1.4990, 1.4627];
$averages['diesel'][2013] = [NULL, 1.4496, 1.4668, 1.4068, 1.4252, 1.4154, 1.4092, 1.4411, 1.4349, 1.4558, 1.4227, 1.3958, 1.4129];
$averages['diesel'][2014] = [NULL, 1.3737, 1.3786, 1.3713, 1.3750, 1.3725, 1.3799, 1.3676, 1.3652, 1.3652, 1.3309, 1.3088, 1.2170];
$averages['diesel'][2015] = [NULL, 1.1338, 1.1778, 1.2182, 1.2231, 1.2525, 1.2268, 1.2023, 1.1484, 1.1374, 1.1313, 1.1423, 1.0591];
$averages['diesel'][2016] = [NULL, 0.9905, 0.9807, 1.0199, 1.0187, 1.0713, 1.1117, 1.0982, 1.0725, 1.0921, 1.1313, 1.1142, 1.1644];
$averages['diesel'][2017] = [NULL, 1.1876, 1.1827, 1.1656, 1.1729, 1.1374, 1.1105, 1.1080, 1.1240, 1.1436, 1.1619, 1.1852, 1.1901];
$averages['diesel'][2018] = [NULL, 1.2048, 1.1950, 1.1803, 1.2109, 1.2721, 1.2941, 1.2892, 1.3003, 1.3456, 1.3799, 1.4509, 1.3407];
$averages['diesel'][2019] = [NULL, 1.2425, 1.2542, 1.2706, 1.2812, 1.3116, 1.2695, 1.2648, 1.2460, 1.2613, 1.2718, 1.2683, 1.2648];
$averages['diesel'][2020] = [NULL, 1.3187, 1.2496, 1.1805, 1.1067, 1.0575, 1.0926, 1.0950, 1.0961, 1.0634, 1.0634, 1.0610, 1.1067];
$averages['diesel'][2021] = [NULL, 1.2437, 1.2812, 1.3351, 1.3257, 1.3444, 1.3737, 1.4030, 1.4030, 1.4159, 1.5377, 1.5845, 1.5400];
$averages['diesel'][2022] = [NULL, 1.6009, 1.6641, 2.1841, 2.0237, 2.0529, 2.0354, 1.9745, 1.9194, 2.1068, 2.1267, 2.0155, 1.8211];
$averages['diesel'][2023] = [NULL, 1.8404, 1.7730];
// letzten beiden Werte sind eine ungefähre Schätzung.

foreach($input AS $key => $val) {
  /**
   * Datum
   */
  if(!empty($val[0]) AND preg_match("/^(?'d'\d{1,2})\.(?'m'\d{1,2})\.(?'y'\d{4})$/", defuse($val[0]), $matches) === 1) {
    $date = $matches['y']."-".str_pad($matches['m'], 2, "0", STR_PAD_LEFT)."-".str_pad($matches['d'], 2, "0", STR_PAD_LEFT)." 00:00:00";
    if(!empty($averages[$row['symbol']][intval($matches['y'])][intval($matches['m'])])) {
      /**
       * Setzen des Vergleichspreises für diesen Vorgang
       */
      $priceCompare = floatval($averages[$row['symbol']][intval($matches['y'])][intval($matches['m'])]);
    } else {
      continue;
    }
  } else {
    continue;
  }

  /**
   * Reichweite
   */
  if(!empty($val[2])) {
    $range = round(floatval(str_replace(",", ".", defuse($val[2]))), 2);
    if($range == 0) {
      continue;
    }
  } else {
    continue;
  }

  /**
   * Liter/kg
   */
  if(!empty($val[3])) {
    $fuel = round(floatval(str_replace(",", ".", defuse($val[3]))), 2);
    if($fuel == 0) {
      continue;
    }
  } else {
    continue;
  }

  /**
   * Kosten
   */
  if(!empty($val[4])) {
    $cost = round(floatval(str_replace(",", ".", defuse($val[4]))), 2);
    if($cost == 0) {
      continue;
    }
  } else {
    continue;
  }

  /**
   * Ausrechnen
   */
  $energyUsed = $fuel*$row['energy']; // Energie des Getankten Kraftstoffes in kW
  $fuelCompare = $energyUsed/$row['energyCompare']; // Benötigte Menge des Vergleichskraftstoffes
  $costCompare = $fuelCompare*$priceCompare; // Gesamtpreis des Vergleichskraftstoffes
  $moneySaved = $costCompare-$cost; // Ersparnis gegenüber dem Vergleichskraftstoff
  //mysqli_query($dbl, "INSERT INTO `entries` (`userId`, `carId`, `timestamp`, `fuelQuantity`, `range`, `cost`, `moneySaved`, `raw`) VALUES (".defuse($userId).", ".defuse($carId).", '".defuse($date)."', ".$fuel.", ".$range.", ".$cost.", ".$moneySaved.", 'Spritmonitor-Input, Durchschnittspreis: ".defuse($priceCompare)."')") OR DIE(MYSQLI_ERROR($dbl));
  echo "INSERT INTO `entries` (`userId`, `carId`, `timestamp`, `fuelQuantity`, `range`, `cost`, `moneySaved`, `raw`) VALUES (".defuse($userId).", ".defuse($carId).", '".defuse($date)."', ".$fuel.", ".$range.", ".$cost.", ".$moneySaved.", 'Spritmonitor-Input, Durchschnittspreis: ".defuse($priceCompare)."')\n";
  //userLog($userId, 2, "Spritmonitor-Import Eintrag hinzugefügt. ".number_format($moneySaved, 2, ",", ".")."€ gespart");
}
echo "Fertig.\n";
?>
