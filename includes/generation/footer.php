<?php
/**
 * includes/generation/footer.php
 * 
 * Footer generation
 */
$footerElements = [
  [
    'faSet' => 'fas',
    'faIcon' => 'f21b',
    'url' => '/imprint',
    'route' => 'imprint',
    'text' => 'Impressum',
    'newTab' => FALSE,
  ],
  [
    'faSet' => 'fas',
    'faIcon' => 'f1c0',
    'url' => '/privacy',
    'route' => 'privacy',
    'text' => 'Datenschutz',
    'newTab' => FALSE,
  ],
  [
    'faSet' => 'fab',
    'faIcon' => 'f09b',
    'url' => 'https://github.com/RundesBalli/tankersparnis',
    'route' => NULL,
    'text' => 'GitHub',
    'newTab' => TRUE,
  ],
  [
    'faSet' => NULL,
    'faIcon' => NULL,
    'url' => 'https://RundesBalli.com',
    'route' => NULL,
    'text' => '🎱 RundesBalli',
    'newTab' => TRUE,
  ],
  [
    'faSet' => 'fas',
    'faIcon' => 'f233',
    'url' => 'https://www.netcup.de/?ref=213946',
    'route' => NULL,
    'text' => 'hosted by Netcup',
    'newTab' => TRUE,
  ],
  [
    'faSet' => 'fas',
    'faIcon' => 'f52f',
    'url' => 'https://creativecommons.tankerkoenig.de/',
    'route' => NULL,
    'text' => 'Tankerkönig-API',
    'newTab' => TRUE,
  ],
  [
    'faSet' => 'fas',
    'faIcon' => 'f042',
    'url' => '/changeStyle?back='.$route,
    'route' => NULL,
    'text' => 'Stil wechseln',
    'newTab' => FALSE,
  ],
];

$footer = '';
foreach($footerElements as $element) {
  $footer.= '<a href="'.$element['url'].'"'.($route == $element['route'] ? ' class="active"' : NULL).($element['newTab'] ? ' target="_blank" rel="noopener"' : NULL).'>'.((!empty($element['faSet']) AND !empty($element['faIcon'])) ? '<span class="icon '.$element['faSet'].'">&#x'.$element['faIcon'].';</span>' : NULL).$element['text'].'</a>';
}
?>
