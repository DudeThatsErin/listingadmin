<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com> 
 * @file       <show-stats.php> 
 * @since      September 2nd, 2010 
 * @version    1.0   
 */ 
require('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-affiliates.inc.php');
require_once('fun-external.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-members.inc.php');

/** 
 * Get variables and listing object before we start \o/ 
 */ 
$options = (object) array();

if(
 !isset($fKey) || 
 ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
) {
 $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
} else {
 $options->listingID = $tigers->cleanMys($fKey);
 $getItem            = $wolves->getListings($options->listingID, 'object');
}

if(isset($use_template) && in_array($use_template, array('y', 'n'))) {
 $options->useTemplate = $use_template == 'y';
} else {
 $options->useTemplate = true;
}

/** 
 * Get statistics depending on user's choice 
 */ 
if($options->useTemplate) {
 $format = html_entity_decode($getItem->stats);
 $format = str_replace('{affiliates}', $rabbits->countAffiliates($options->listingID), $format);
 $format = str_replace('{members}', $snakes->getMemberCount($options->listingID, '0'), $format);
 $format = str_replace('{newest}', $snakes->newestFans($options->listingID), $format);
 $format = str_replace('{pending}', $snakes->getMemberCount($options->listingID, 1), $format);
 $format = str_replace('{previous}', $snakes->formatPrevious($options->listingID), $format);
 $format = str_replace('{since}', date($getItem->date, strtotime($getItem->since)), $format);
 $format = str_replace('{updated}', $snakes->getUpdated($options->listingID), $format);
 echo $format;
} else {
 $la_aff_count  = $rabbits->countAffiliates($options->listingID);
 $la_newest     = $snakes->newestFans($options->listingID);
 $la_approved   = $snakes->getMemberCount($options->listingID, '0');
 $la_pending    = $snakes->getMemberCount($options->listingID, 1);
 $la_previous   = $snakes->formatPrevious($options->listingID);
 $la_fl_opened  = date($getItem->date, strtotime($getItem->since));
 $la_fl_updated = $snakes->getUpdated($options->listingID);
}
