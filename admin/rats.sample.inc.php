<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <rats.sample.inc.php>
 * @version          Robotess Fork
 */

if (basename($_SERVER['PHP_SELF']) === 'rats.inc.php') {
    die('<p>ERROR: <em>Nobody ever told [her] it was the wrong way</em>...<br>' .
        "Sorry m'dear, you cannot access this file directly!</p>\n");
}
require('v.inc.php');

# ----------------------------------------------------------- 
/* 
 * Database Variables  
 * 
 * $database_host - MySQL Host (usually localhost) 
 * $database_user - MySQL username  
 * $database_pass - MySQL password  
 * $database_name - MySQL name 
 */
# ----------------------------------------------------------- 
$database_host = 'localhost';
$database_user = 'root';
$database_pass = 'root';
$database_name = 'bambino';

# ----------------------------------------------------------- 
/* 
 * Table Prefix 
 * 
 * Table prefix before your table names. Beware that if you 
 * have installed Listing Admin prior to version 2.1.9, 
 * your prefix is most likely "trex_" 
 */
# ----------------------------------------------------------- 
$_ST['prefix'] = 'trex_';

# -----------------------------------------------------------
/*
 *  Database Engine
 *
 *  You can choose between using MySQL or Mysqli for your
 *  method of database usage. You shouldn't need to change
 *  this unless your server doesn't support Mysqli.
 *
 *  Enter in "mysqli" or "pdo_mysql". For example:
 *
 *  $_ST['dbengine'] = "pdo_mysql";
 *  $_ST['dbengine'] = "mysqli";
 */
# -----------------------------------------------------------
$_ST['dbengine'] = 'mysqli';

# -----------------------------------------------------------
/* 
 * STOP EDITING HERE! 
 * 
 * Below this line are sensitive lines that should NOT be 
 * messed with unless you want the script to blow up, and 
 * an ugly "WARNING: SYNTAX ERROR, LOL YOU'RE AN IDIOT" 
 * error to appear (but no, really, don't do it, dudes). 
 */
# ----------------------------------------------------------- 
if ((!isset($_ST['prefix']) || empty($_ST['prefix']))
    || (strpos($_ST['prefix'], '_') === false)) {
    $prefix = 'listingadmin_';
} else {
    $prefix = $_ST['prefix'];
}

/* 
 *  Table varrrrriables 
 */
$_ST['affiliates'] = $prefix . 'affiliates';
$_ST['categories'] = $prefix . 'categories';
$_ST['codes'] = $prefix . 'codes';
$_ST['codes_categories'] = $prefix . 'codes_categories';
$_ST['codes_donors'] = $prefix . 'codes_donors';
$_ST['codes_sizes'] = $prefix . 'codes_sizes';
$_ST['joined'] = $prefix . 'joined';
$_ST['main'] = str_replace('_', '', $prefix);
$_ST['kim'] = $prefix . 'kim';
$_ST['lyrics'] = $prefix . 'lyrics';
$_ST['lyrics_albums'] = $prefix . 'lyrics_albums';
$_ST['members'] = $prefix . 'members';
$_ST['options'] = $prefix . 'options';
$_ST['quotes'] = $prefix . 'quotes';
$_ST['templates'] = $prefix . 'templates';
$_ST['templates_emails'] = $prefix . 'templates_emails';
$_ST['updates'] = $prefix . 'updates';
$_ST['updates_comments'] = $prefix . 'updates_comments';
$_ST['users'] = $prefix . 'users';
$_ST['wishlist'] = $prefix . 'wishlist';

/**
 * And our "security" tables! (This was created with version uno
 * of the script, and I just never got around to removing the
 * proof of what an anal retentive loser I am, so. 8D)
 */
$_ST['errors'] = $prefix . 'errors';
$_ST['logs'] = $prefix . 'logs';
$_ST['success'] = $prefix . 'success';

/**
 * And fi-na-lly, let's grab our info object~
 */
$laoptions = (object)[
    'dbEngine' => $_ST['dbengine'],
    'saltPass' => date('F'),
    'version' => 'Listing Admin ' . LAVERSION,
    'versionURI' => 'http://scripts.wyngs.net/scripts/listingadmin/',
];

/**
 * Now we initialise our database jaaaaazzz :D
 */
require('inc/fun-db.inc.php');
$scorpions = new scorpions(
    $database_host,
    $database_user,
    $database_pass,
    $database_name
);
