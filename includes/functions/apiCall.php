<?php
/**
 * includes/functions/apiCall.php
 * 
 * Function to handle Tankerkönig-API requests.
 */

/**
 * Tankerkönig apiCall
 * 
 * @param string The URL to be called.
 * @param array The optional GET parameters for the request.
 * 
 * @return array/bool Result from the API as an associative array or FALSE
 */
function apiCall(string $url, array $params = []) {
  /**
   * Including global variables from the configuration file in the function.
   */
  global $tkApiKey;
  global $cURL;
  
  /**
   * Initialize cURL
  */
  $ch = curl_init();

  /**
   * Set connection parameters
   * @see https://www.php.net/manual/en/function.curl-setopt.php
   */
  $options = array(
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_USERAGENT => $cURL['userAgent'],
    CURLOPT_INTERFACE => $cURL['bindTo'],
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 10
  );
  
  /**
   * Preparing the GET parameters.
   */
  if(!empty($params) AND is_array($params)) {
    $data = http_build_query($params, '', '&', PHP_QUERY_RFC1738);
    $options[CURLOPT_URL] = $url."?".$data."&apikey=".$tkApiKey;
  } else {
    $options[CURLOPT_URL] = $url."?apikey=".$tkApiKey;
  }

  /**
   * Inserting the option array into the cURL handle
   */
  curl_setopt_array($ch, $options);

  /**
   * Execute the cURL
   */
  $response = json_decode(curl_exec($ch), TRUE);
  $errno = curl_errno($ch);
  if($errno != 0) {
    return FALSE;
  }

  /**
   * Checking if the request was successful.
   */
  $http_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
  if($http_code != 200) {
    return FALSE;
  }

  /**
   * Close the cURL handle.
   */
  curl_close($ch);

  /**
   * Return the api response.
   */
  return $response;
}
