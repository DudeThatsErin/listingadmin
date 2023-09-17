<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @contributor      Erin <dudethatserin@outlook.com> https://github.com/DudeThatsErin/listingadmin
 * @file             <install.php>
 * @version          Erin's Fork
 */

$_ST = [];

if (!defined('LAVERSION')) {
    define('LAVERSION', '[Erin\'s Fork] v2.4');
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
