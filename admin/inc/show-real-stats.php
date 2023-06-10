<?php
/** 
 * @copyright   2007 
 * @license     GPL Version 3; BSD Modified 
 * @author      Tess <theirrenegadexxx@gmail.com> 
 * @file        <show-real-stats.php> 
 * @since       September 2nd, 2010  
 * @version     2.2 
 */ 
require('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-affiliates.inc.php');
require_once('fun-categories.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-members.inc.php');
require_once('fun-misc.inc.php');
require(MAINDIR . 'vars.inc.php');

/** 
 * Get overall affiliates count, with collective count 
 */ 
$affiliates            = $seahorses->getCount('affiliates');
$affiliates_collective = $rabbits->countAffiliates('0');

/** 
 * Get categories! 
 */ 
$categories        = $seahorses->getCount('cat');
$categories_listed = $lions->countCategories();

/** 
 * The listings: the current, upcoming, pending and total \o/ 
 */ 
$current  = $seahorses->getCount('current', 'y');
$upcoming = $seahorses->getCount('upcoming', 'y');
$pending  = $seahorses->getCount('pending', 'y');
$listings = $current + $upcoming + $pending;

/** 
 * Joined listings 
 */ 
$joined = $seahorses->getCount('joined');

/** 
 * Members: approved and peeeending :D 
 */ 
$approved   = $seahorses->getCount('approved');
$unapproved = $seahorses->getCount('unapproved');
$members    = $approved + $unapproved;

/** 
 * And finally, the newest opened listing 
 */ 
$newest = $wolves->getNewest();
