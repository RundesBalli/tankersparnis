<?php
/**
 * includes/routing/routes.php
 * 
 * Routes
 * 
 * @var array
 */
$routes = [
  /**
   * Pages
   */
  'start'          => 'start.php',
  'imprint'        => 'imprint.php',
  'info'           => 'info.php',
  'privacy'        => 'privacy.php',
  'changeStyle'    => 'changeStyle.php',
  'register'       => 'register.php',
  'activate'       => 'activate.php',
  'pwReset'        => 'pwReset.php',
  'confirmEmail'   => 'confirmEmail.php',
  
  /* User pages */
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
  'rawData'        => 'rawData.php',

  /* Error pages */
  '404'            => '404.php',
  '403'            => '403.php',
];
?>
