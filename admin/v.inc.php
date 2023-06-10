<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <v.inc.php>
 * @version          Robotess Fork
 */

$_ST = [];

if (!defined('LAVERSION')) {
    define('LAVERSION', '[Robotess Fork] 1.0.2');
}

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'inc' . DIRECTORY_SEPARATOR . 'Robotess' . DIRECTORY_SEPARATOR . 'autoloader.php');

/**
 * Grab login object!
 */
$loginInfo = (object)[
    'logBots' => '/(Indy|Blaiz|Java|libwww-perl|Python|OutfoxBot|User-Agent|PycURL|' .
        'AlphaServer|T8Abot|Syntryx|WinHttp|WebBandit|nicebot|Jakarta|curl|Snoopy|' .
        'PHPcrawl|id-search)/i',
    'logInfo' => '|' . $_SERVER['REMOTE_ADDR'] . '|' . $_SERVER['HTTP_USER_AGENT'] . '|',
    'logNots' => [
        'alert',
        'bcc:',
        'content-type',
        'document.cookie',
        'javascript',
        'onclick',
        'onload'
    ]
];
