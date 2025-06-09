<?php
/**
 * includes/config.php
 * 
 * Configuration file
 */

/**
 * MySQL-Credentials
 * 
 * @var array
 *   @var string host    MySQL connection host
 *   @var string user    Username for the MySQL connection
 *   @var string pass    Password for the MySQL connection
 *   @var string db      Database on the SQL server in which to work.
 *   @var string charset Charset of the connection. Default: utf8
 */
$mysqlCredentials = [
  'host' => 'localhost',
  'user' => '',
  'pass' => '',
  'db' => '',
  'charset' => 'utf8mb4'
];

/**
 * Display timezone
 * 
 * @var string
 * @see List of supported timezones: https://www.php.net/manual/en/timezones.php
 */
$displayTimezone = 'Europe/Berlin';

/**
 * Default color scheme
 * @var string Darkmode or Lightmode ('dark' / 'light')
 */
$defaultStyle = 'dark';

/**
 * Cookie names
 * @var string Login-Cookie name
 * @var string Style-Cookie name
 */
$cookieName = '';
$styleName = '';

/**
 * Tankerkönig API-Key
 * @var string API-Key, Format: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
 */
$tkApiKey = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';

/**
 * cURL Settings
 * @var string Interface with which cURL should establish the connection, e.g. eth0 (sudo ifconfig).
 * @var string The UserAgent with which the request is to be sent.
 */
$cURL = [
  'bindTo' => '',
  'userAgent' => '',
];

/**
 * Mail settings
 * @var array
 */
$mailConfig = [
  /**
   * Connection settings
   * @var string Hostname of the mailserver
   * @var int Port of the mailserver
   * @var string SMTP username
   * @var string SMTP password
   */
  'conn' => [
    'host' => 'mail.example.com',
    'port' => 587,
    'smtpUser' => 'noreply@example.com',
    'smtpPass' => '',
  ],

  /**
   * Header configuration and greeting
   * @var string "From:" e-mail address
   * @var string "From:" Name
   * @var string "Reply-to:" e-mail address
   * @var string "Reply-to:" Name
   * @var string Final greeting at the end of each e-mail
   */
  'conf' => [
    'fromEmail' => 'noreply@example.com',
    'fromName' => 'Example.com',
    'replyToEmail' => 'info@example.com',
    'replyToName' => 'Example.com',
    'closingGreeting' => "Viele Grüße\nDein Team von Example.com\nWeb: https://example.com - E-Mail: info@example.com",
  ],

  /**
   * @var string Hostname for the CLI mail transfer. Should match $_SERVER[‘HTTP_HOST’]. Example: example.com
   */
  'text' => [
    'HTTP_HOST' => 'example.com',
  ],

  /**
   * Subjects
   * @var string Registration mail
   * @var string Activation e-mail (after clicking the activation link in the registration e-mail)
   * @var string "Password has been reset" mail
   * @var string "E-mail address has been changed" Mail
   * @var string "Password has been changed" mail
   * @var string Inactivity reminder mail
   * @var string Inactivity account deletion mail
   */
  'subject' => [
    'register' => 'Deine Registrierung',
    'accountActivated' => 'Dein Account wurde aktiviert',
    'passwordResetted' => 'Passwort wurde zurückgesetzt',
    'emailChanged' => 'E-Mail Adresse wurde geändert',
    'passwordChanged' => 'Passwort wurde geändert',
    'reminder' => 'Account inaktiv',
    'inactiveDeletion' => 'Account gelöscht',
  ],
];

/**
 * Month names
 * @var array Month names
 */
$monthNames = [
  1 => 'Januar',
  2 => 'Februar',
  3 => 'März',
  4 => 'April',
  5 => 'Mai',
  6 => 'Juni',
  7 => 'Juli',
  8 => 'August',
  9 => 'September',
  10 => 'Oktober',
  11 => 'November',
  12 => 'Dezember'
];


/**
 * 
 * DO NOT CHANGE ANYTHING BELOW THIS LINE, EVEN IF YOU KNOW WHAT YOU ARE DOING!
 * 
 */

/**
 * Config version
 * 
 * @var int
 */
$configVersion = 1;
?>
