<?php
/**
 * includes/routing/router.php
 * 
 * Router for the requested page.
 */
if(!empty($_GET['page']) AND preg_match('/([a-z-\d]+)/i', $_GET['page'], $pageMatch) === 1) {
  /**
   * Check if the requested page exist in the routes.
   */
  if(array_key_exists($pageMatch[1], $routes)) {
    /**
     * The route exists. Include the file.
     */
    $route = $pageMatch[1];
    $fileToInclude = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.$routes[$route];
    if(!file_exists($fileToInclude)) {
      /**
       * File doesn't exist.
       */
      header('Location: /'); die();
    }
    /**
     * File exists
     */
    require_once($fileToInclude);
  } else {
    /**
     * The requested page doesn't exist in the routes.
     */
    header('Location: /'); die();
  }
} else {
  /**
   * No page was requested or the requested page doesn't match the pattern.
   * So the default page is requested.
   */
  $route = 'start';
  $fileToInclude = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.$routes[$route];
  if(!file_exists($fileToInclude)) {
    /**
     * File doesn't exist.
     */
    die('No default page found.');
  }
  /**
   * File exists
   */
  require_once($fileToInclude);
}
?>
