<?php
/**
 * includes/loader.php
 * 
 * Configuration and function loader
 */

/**
 * Basic configuration and functions
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'config.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'constants.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'configCheck.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'timezone.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'apiCall.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'output.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'showLog.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'userLog.php');

/**
 * Database connection and functions
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'sql.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'defuse.php');

/**
 * Email stuff
 */
require_once(__DIR__.DIRECTORY_SEPARATOR.'email'.DIRECTORY_SEPARATOR.'PHPMailer.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'email'.DIRECTORY_SEPARATOR.'SMTP.php');

/**
 * If the loader has been called from a cli script or the API, the content/page generation is not needed.
 */
if(php_sapi_name() != 'cli') {
  /**
   * Content generation and router
   */
  require_once(__DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'readTemplate.php');
  require_once(__DIR__.DIRECTORY_SEPARATOR.'generation'.DIRECTORY_SEPARATOR.'navigation.php');
  require_once(__DIR__.DIRECTORY_SEPARATOR.'generation'.DIRECTORY_SEPARATOR.'footer.php');
  require_once(__DIR__.DIRECTORY_SEPARATOR.'generation'.DIRECTORY_SEPARATOR.'style.php');

  /**
   * Page generation
   */
  require_once(__DIR__.DIRECTORY_SEPARATOR.'generation'.DIRECTORY_SEPARATOR.'generateOutput.php');
  require_once(__DIR__.DIRECTORY_SEPARATOR.'generation'.DIRECTORY_SEPARATOR.'tidyOutput.php');
}
?>
