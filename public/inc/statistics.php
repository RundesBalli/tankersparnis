<?php
/**
 * statistics.php
 * 
 * Seite mit Statistiken
 */

/**
 * Einbinden der Cookieüberprüfung.
 */
require_once('cookiecheck.php');

/**
 * Titel und Überschrift
 */
$title = "Statistiken";
$content.= "<h1><span class='fas icon'>&#xf201;</span>Statistiken</h1>";

/**
 * Prüfung ob diese Seite überhaupt aufgerufen werden darf.
 */
if(!defined("perm-showStatistics")) {
  $content.= "<div class='warnbox'>Du hast keine Berechtigung um auf diese Seite zuzugreifen.</div>";
} else {
  $perm = 0;

  /**
   * Anzeige der Registrierungsstatistiken
   */
  if(defined("perm-showRegisterStatistics")) {
    if($perm != 0) {
      $content.= "<div class='spacer-m'></div><hr>";
    } else {
      $perm = 1;
    }
    $content.= "<h2>Registrierungen, 4 Wochen</h2>";
    $result = mysqli_query($dbl, "SELECT COUNT(`id`) AS `c`, DATE(`registered`) AS `d` FROM `users` WHERE `registered` > DATE_SUB(NOW(), INTERVAL 4 WEEK) GROUP BY `d` ORDER BY `d` DESC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Keine Neuregistrierungen in den letzten 4 Wochen.</div>";
    } else {
      $content.= "<section>";
      $content.= "<div class='row bold breakWord'>".
        "<div class='col-s-6 col-l-3'>Datum</div>".
        "<div class='col-s-6 col-l-9'>Anzahl Registrierungen</div>".
      "</div>";
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover breakWord'>".
          "<div class='col-s-6 col-l-3'>".date("d.m.Y", strtotime($row['d']))."</div>".
          "<div class='col-s-6 col-l-3'>".output($row['c'])."</div>".
          "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['c'])."</div>".
        "</div>";
      }
      $content.= "</section>";
    }
  }


  /**
   * Anzeige der Userstatistiken
   */
  if(defined("perm-showUserStatistics")) {
    if($perm != 0) {
      $content.= "<div class='spacer-m'></div><hr>";
    } else {
      $perm = 1;
    }
    $content.= "<h2>Userstatistiken</h2>";
    $result = mysqli_query($dbl, "SELECT (SELECT count(`id`) FROM `users`) AS `totalUsers`, (SELECT count(`id`) FROM `users` WHERE `registerHash` IS NOT NULL) AS `totalUsersNotActivated`, (SELECT count(`id`) FROM `users` WHERE `lastActivity` > DATE_SUB(NOW(), INTERVAL 10 DAY)) AS `totalUsersActive`, (SELECT count(`id`) FROM `users` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 MONTH)) AS `totalUsersBeforeDeletion`, (SELECT count(`id`) FROM `sessions`) AS `totalSessions`") OR DIE(MYSQLI_ERROR($dbl));
    $row = mysqli_fetch_array($result);
    $content.= "<section>";
      $content.= "<div class='row bold breakWord'>".
        "<div class='col-s-6 col-l-3'>Bezeichnung</div>".
        "<div class='col-s-6 col-l-9'>Wert</div>".
      "</div>";
      $content.= "<div class='row hover breakWord'>".
        "<div class='col-s-6 col-l-3'>User (gesamt)</div>".
        "<div class='col-s-6 col-l-3'>".output($row['totalUsers'])."</div>".
        "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['totalUsers'])."</div>".
      "</div>";
      $content.= "<div class='row hover breakWord'>".
        "<div class='col-s-6 col-l-3'>User (noch nicht Aktiviert)</div>".
        "<div class='col-s-6 col-l-3'>".output($row['totalUsersNotActivated'])."</div>".
        "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['totalUsersNotActivated'])."</div>".
      "</div>";
      $content.= "<div class='row hover breakWord'>".
        "<div class='col-s-6 col-l-3'>User aktiv (10 Tage)</div>".
        "<div class='col-s-6 col-l-3'>".output($row['totalUsersActive'])."</div>".
        "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['totalUsersActive'])."</div>".
      "</div>";
      $content.= "<div class='row hover breakWord'>".
        "<div class='col-s-6 col-l-3'>User länger als 6 Monate inaktiv*</div>".
        "<div class='col-s-6 col-l-3'>".output($row['totalUsersBeforeDeletion'])."</div>".
        "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['totalUsersBeforeDeletion'])."</div>".
      "</div>";
      $content.= "<div class='row hover breakWord'>".
        "<div class='col-s-6 col-l-3'>Anzahl eingeloggter Sitzungen**</div>".
        "<div class='col-s-6 col-l-3'>".output($row['totalSessions'])."</div>".
        "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['totalSessions'])."</div>".
      "</div>";
      $content.= "<div class='row small breakWord'>".
        "<div class='col-s-12 col-l-12'>* nach 7 Monaten erfolgt die Löschung.</div>".
        "<div class='col-s-12 col-l-12'>** ein User kann auch mehrere Sitzungen einloggen.</div>".
      "</div>";
    $content.= "</section>";
  }

  /**
   * Anzeige der Eintragsstatistik
   */
  if(defined("perm-showEntriesStatistics")) {
    if($perm != 0) {
      $content.= "<div class='spacer-m'></div><hr>";
    } else {
      $perm = 1;
    }

    $content.= "<h2>Einträge, 4 Wochen</h2>";
    $result = mysqli_query($dbl, "SELECT COUNT(`id`) AS `c`, DATE(`timestamp`) AS `t` FROM `entries` WHERE `timestamp` > DATE_SUB(NOW(), INTERVAL 4 WEEK) GROUP BY `t` ORDER BY `t` DESC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Keine Einträge in den letzten 4 Wochen.</div>";
    } else {
      $content.= "<section>";
      $content.= "<div class='row bold breakWord'>".
        "<div class='col-s-6 col-l-3'>Datum</div>".
        "<div class='col-s-6 col-l-9'>Anzahl Einträge</div>".
      "</div>";
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover breakWord'>".
          "<div class='col-s-6 col-l-3'>".date("d.m.Y", strtotime($row['t']))."</div>".
          "<div class='col-s-6 col-l-3'>".output($row['c'])."</div>".
          "<div class='col-s-0 col-l-6'>".str_repeat("#", $row['c'])."</div>".
        "</div>";
      }
      $content.= "</section>";
    }
  }

  /**
   * Anzeige der letzten Usereinträge
   */
  if(defined("perm-showLastEntries")) {
    if($perm != 0) {
      $content.= "<div class='spacer-m'></div><hr>";
    } else {
      $perm = 1;
    }
    $content.= "<h2>die Einträge der letzten 2 Wochen</h2>";
    $result = mysqli_query($dbl, "SELECT * FROM `entries` WHERE `timestamp` > DATE_SUB(NOW(), INTERVAL 2 WEEK) ORDER BY `timestamp` DESC") OR DIE(MYSQLI_ERROR($dbl));
    if(mysqli_num_rows($result) == 0) {
      $content.= "<div class='infobox'>Keine Einträge in den letzten 2 Wochen.</div>";
    } else {
      $content.= "<section>";
      $content.= "<div class='row bold breakWord small'>".
        "<div class='col-s-12 col-l-3'>Zeitpunkt</div>".
        "<div class='col-s-0 col-l-1'>ID</div>".
        "<div class='col-s-0 col-l-1'>Getankt (l/kg)</div>".
        "<div class='col-s-0 col-l-1'>Reichweite</div>".
        "<div class='col-s-0 col-l-1'>Verbrauch auf 100km (l/kg)</div>".
        "<div class='col-s-0 col-l-1'>Preis</div>".
        "<div class='col-s-0 col-l-1'>Preis/100km</div>".
        "<div class='col-s-6 col-l-3'>eingespart</div>".
      "</div>";
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row hover breakWord small'>".
          "<div class='col-s-12 col-l-3'>".date("d.m.Y, H:i", strtotime($row['timestamp']))." Uhr</div>".
          "<div class='col-s-0 col-l-1'>".output($row['id'])."</div>".
          "<div class='col-s-0 col-l-1'>".number_format($row['fuelQuantity'], 2, ",", ".")."</div>".
          "<div class='col-s-0 col-l-1'>".number_format($row['range'], 1, ",", ".")."km</div>".
          "<div class='col-s-0 col-l-1'>".number_format(($row['fuelQuantity']/$row['range']*100), 1, ",", ".")."</div>".
          "<div class='col-s-0 col-l-1'>".number_format($row['cost'], 2, ",", ".")."€</div>".
          "<div class='col-s-0 col-l-1'>".number_format(($row['cost']/$row['range']*100), 2, ",", ".")."€</div>".
          "<div class='col-s-6 col-l-3 highlightPositive'>".number_format($row['moneySaved'], 2, ",", ".")."€</div>".
        "</div>";
      }
      $content.= "</section>";
    }
  }

  /**
   * Anzeige der letzten Userlogeinträge
   */
  if(defined("perm-showLastLogEntries")) {
    if($perm != 0) {
      $content.= "<div class='spacer-m'></div><hr>";
    } else {
      $perm = 1;
    }
    $content.= "<h2>Logeinträge, letzten 2 Wochen</h2>";
    $content.= "<section>";
      $content.= "<div class='row bold breakWord' style='border-left: 6px solid #808080;'>".
        "<div class='col-s-12 col-l-3'>Zeitpunkt</div>".
        "<div class='col-s-12 col-l-3'>User</div>".
        "<div class='col-s-12 col-l-6'>Text</div>".
      "</div>";
      $result = mysqli_query($dbl, "SELECT `log`.*, `users`.`email`, `logLevel`.`title` AS `logTitle`, `logLevel`.`color` FROM `log` JOIN `users` ON `log`.`userId`=`users`.`id` JOIN `logLevel` ON `log`.`logLevel`=`logLevel`.`id` WHERE `log`.`timestamp` > DATE_SUB(NOW(), INTERVAL 2 WEEK) ORDER BY `log`.`timestamp` DESC") OR DIE(MYSQLI_ERROR($dbl));
      while($row = mysqli_fetch_array($result)) {
        $content.= "<div class='row breakWord hover' style='border-left: 6px solid #".output($row['color']).";' title='".output($row['logTitle'])."'>".
          "<div class='col-s-12 col-l-3 help'>".date("d.m.Y, H:i:s", strtotime($row['timestamp']))."</div>".
          "<div class='col-s-12 col-l-3'>".(defined("perm-showEmails") ? output($row['email']) : "<span class='italic'>REDACTED</span>")."</div>".
          "<div class='col-s-12 col-l-6'>".showLog($row['text'])."</div>".
        "</div>";
      }
    $content.= "</section>";
  }

  if($perm == 0) {
    $content.= "<div class='warnbox'>Du hast zwar die Berechtigung um auf diese Seite zuzugreifen, aber keine Berechtigung um einzelne Elemente dieser Seite anzeigen zu lassen.</div>";
  }
}
?>
