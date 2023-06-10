<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <jac.sample.inc.php>
 * @version          Robotess Fork
 */

if (basename($_SERVER['PHP_SELF']) === 'jac.inc.php') {
    die('<p>ERROR: <em>Nobody ever told [her] it was the wrong way</em>...<br>' .
        "Sorry m'dear, you cannot access this file directly!</p>\n");
}

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

/**
 * Table variable!
 */
$_ST['options'] = $prefix . 'options';

# -- STOP EDITING HERE! -------------------------------------
# -----------------------------------------------------------
#
#  Below this line are sensitive lines that should NOT be
#  messed with unless you know exactly what you're doing.
#
# -----------------------------------------------------------
# -----------------------------------------------------------

require_once ('db-connection.inc.php');
