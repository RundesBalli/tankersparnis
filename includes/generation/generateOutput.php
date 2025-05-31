<?php
/**
 * includes/generation/generateOutput.php
 * 
 * Generates the output with previous generated contents.
 */
$output = preg_replace(
  [
    '/{TITLE}/im',
    '/{STYLE}/im',
    '/{NAV}/im',
    '/{FOOTER}/im',
    '/{CONTENT}/im',
  ],
  [
    (!empty($title) ? ' - '.$title : NULL),
    $style,
    $nav,
    $footer,
    $content,
  ],
  $template
);
?>
