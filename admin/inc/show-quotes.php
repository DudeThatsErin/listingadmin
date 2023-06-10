<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <show-quotes.php> 
 * @since      September 2nd, 2010 
 * @version    1.0   
 */ 
require('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-addons.inc.php');
require_once('fun-external.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-members.inc.php');
require_once('fun-misc.inc.php');

/** 
 * Get variables and options! 
 */ 
$options = false;

if(!isset($quote_number) || empty($quote_number)) {
 $options->quoteNumber = 2;
} else {
 $options->quoteNumber = $quote_number;
}

if(
 !isset($fKey) || 
 ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
) {
 $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
} else {
 $options->listingID = $tigers->cleanMys($fKey);
 $getItem            = $wolves->getListings($options->listingID, 'object');
}

/** 
 * Get quotes! 
 */ 
$select = "SELECT * FROM `$_ST[quotes]` WHERE `fNiq` = '" . $options->listingID . "'";
$true = $scorpions->query($select);
if($true == false) {
 $tigers->displayError('Database Error', 'The script was unable to select the' . 
 ' quotes from that specific listing.', false);
}
$count = $scorpions->total($true);

if($count > 0) {
 $str = isset($quote_random) && $quote_random == 0 ? 0 : 1;
 $b   = $cheetahs->pullQuotes($options->listingID, $str, $options->quoteNumber);
 echo str_replace('&lt;', '<', str_replace('&gt;', '>', $b));
}

else {
 echo '<p class="tc">No quotes appear to exist under that listing.</p>';
}
