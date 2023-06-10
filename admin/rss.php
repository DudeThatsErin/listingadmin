<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com> 
 * @file       <rss.php> 
 * @since      September 2nd, 2010 
 * @version    2.1.4  
 */ 
require('rats.inc.php');
require_once('inc/fun.inc.php');
require_once('inc/fun-listings.inc.php');
require_once('inc/fun-misc.inc.php');
require_once('inc/fun-updates.inc.php');
require('vars.inc.php');

$c = !isset($_GET['c']) ? '' : (in_array($_GET['c'], $wolves->listingsList())
? $tigers->cleanMys($_GET['c']) : '');

$turtles->getRSS($c);
