<?php
/**
 * includes/generation/navigation.php
 * 
 * Navigation generation
 */
if(!empty($_COOKIE[$cookieName]) AND preg_match('/[a-f0-9]{64}/i', defuse($_COOKIE[$cookieName]), $match) === 1) {
  /**
   * Navigation when logged in.
   */
  $navElements = [
    [
      'faSet' => 'far',
      'faIcon' => 'f044',
      'url' => '/entry',
      'route' => 'entry',
      'text' => 'Eintrag hinzufügen',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f153',
      'url' => '/savings',
      'route' => 'savings',
      'text' => 'Ersparnisse',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f1b9',
      'url' => '/cars',
      'route' => 'cars',
      'text' => 'KFZs',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f013',
      'url' => '/settings',
      'route' => 'settings',
      'text' => 'Einstellungen',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f56f',
      'url' => '/import',
      'route' => 'import',
      'text' => 'Importieren',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f2f5',
      'url' => '/logout',
      'route' => 'logout',
      'text' => 'Logout',
      'newTab' => FALSE,
    ],
  ];
} else {
  /**
   * Navigation when not logged in.
   */
  $navElements = [
    [
      'faSet' => 'fas',
      'faIcon' => 'f015',
      'url' => '/',
      'route' => 'start',
      'text' => 'Startseite',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f2f6',
      'url' => '/login',
      'route' => 'login',
      'text' => 'Login',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'far',
      'faIcon' => 'f044',
      'url' => '/register',
      'route' => 'register',
      'text' => 'Registrieren',
      'newTab' => FALSE,
    ],
    [
      'faSet' => 'fas',
      'faIcon' => 'f128',
      'url' => '/info',
      'route' => 'info',
      'text' => 'Info',
      'newTab' => FALSE,
    ],
  ];
}

$nav = '<a id="toggleElement"></a>';
foreach($navElements as $element) {
  $nav.= '<a href="'.$element['url'].'"'.($route == $element['route'] ? ' class="active"' : NULL).($element['newTab'] ? ' target="_blank" rel="noopener"' : NULL).'><span class="icon '.$element['faSet'].'">&#x'.$element['faIcon'].';</span>'.$element['text'].'</a>';
}
?>
