<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com> 
 * @file       <show-lyrics.php> 
 * @since      September 2nd, 2010   
 * @version    2.1.4     
 */ 
require('b.inc.php');
require_once(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-addons.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-external.inc.php');
require_once('fun-members.inc.php');

/** 
 * Get variables and options! 
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

$query = $tigers->cleanMys($_SERVER['QUERY_STRING']);
if(isset($query) && !empty($query)) {
 $options->url = '?' . str_replace('&', '&#38;', $query) . '&#38;';
} else {
 $options->url = '?';
}

$options->albumID = $album_id ?? 'n';

/** 
 * Get specific lyric \o/ 
 */ 
if(isset($_GET['ly']) && in_array($_GET['ly'], $cheetahs->lyricsList($_KY['listing_id']))) {
 $lyid   = $tigers->cleanMys((int)$_GET['ly']);
 $select = "SELECT * FROM `$_ST[lyrics]` WHERE `lyID` = '$lyid' LIMIT 1";
 $true   = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select the' . 
	' specified lyrics.', false);
 } else {
  while($getItem = $true->fetch_object()) {
	 echo '<h3>' . $getItem->lyName . "</h3>\n";
	 echo '<div id="lyrics" class="lyric' . $getItem->lyID . '">';
	 if($getItem->markup == 'xhtml') {
	  echo nl2br($getItem->lyText);
	 } else {
	  echo $octopus->lineBreak($getItem->lyText);
	 }
	 echo "</div>\n";
  }
 }
}

/** 
 * And le Index :D 
 */ 
else {
 if(isset($album_id)) {
  $select = "SELECT * FROM `$_ST[lyrics]` WHERE `fNiq` = '" . $options->listingID . 
	"' AND `aNiq` = '" . $options->albumID . "'";
 } else {
  $select = "SELECT * FROM `$_ST[lyrics_albums]` WHERE" . 
	" `fNiq` = '" . $options->listingID . "'";
 }
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select' . 
	' the lyrics from that specific listing.', false);
 }
 $count = $scorpions->total($true);

 if($count > 0) {
  $cheetahs->defaultLyrics($options->listingID, $options->albumID);
 } else {
  echo '<p class="tc">No lyrics appear to exist under that listing.</p>';
 }
}
