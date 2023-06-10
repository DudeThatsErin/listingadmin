<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <lyrics.php> 
 * @since      September 2nd, 2010 
 * @version    1.0    
 */
$s = isset($_GET['g']) && $_GET['g'] == 'albums' ? 'Albums' : 'Songs';
$w = isset($_GET['g']) ? $s : 'Lyrics';
$getTitle = $w;

require('pro.inc.php');
require('vars.inc.php');
require('header.php');

?>
    <p class="scriptButton"><span class="script"><b>Notice:</b></span> This extension is simply legacy hence is not supported by current version of the LA script. I would recommend turning it off.
    </p>
   <?php

$sp = isset($_GET['g']) ? ($_GET['g'] == 'songs' ? '<span><a href="lyrics.php?' .
'g=songs&#38;p=new">Add Song</a></span>' : '<span><a href="lyrics.php?g=albums' .
'&#38;p=new">Add Album</a></span>') : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !ctype_digit($_GET['p'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if($seahorses->getOption('lyrics_opt') == 'y') {

if(isset($_GET['g']) && $_GET['g'] == 'albums') {
 if(isset($_GET['p']) && $_GET['p'] == 'new') {
?>
<form action="lyrics.php" method="post">
<fieldset>
 <legend>Details</legend>
 <p><label><strong>Album Name:</strong></label> 
 <input name="album" class="input1" type="text"></p>
 <p><label><strong>Artist:</strong></label> 
 <input name="artist" class="input1" type="text"></p>
 <p><label><strong>Year:</strong></label> 
 <input name="year" class="input1" type="text"></p>
</fieldset>

<fieldset>
 <legend>Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Listings Available</option>\n";
 }
	
 else {
  while($getItem = $scorpions->obj($true)) {
   echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Album"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
 } 
 
 elseif (isset($_GET['p']) && $_GET['p'] == 'old') {
  $id = $tigers->cleanMys($_GET['d']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect album or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
 
  $select = "SELECT * FROM `$_ST[lyrics_albums]` WHERE `aID` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select that' . 
   ' specific quote.', true, $select);
  }
  $getItem = $scorpions->obj($true);
?>
<form action="lyrics.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->aID; ?>">

<fieldset>
 <legend>Details</legend>
 <p><label><strong>Album Name:</strong></label> 
 <input name="album" class="input1" type="text" value="<?php echo $getItem->aName; ?>"></p>
 <p><label><strong>Artist:</strong></label> 
 <input name="artist" class="input1" type="text" value="<?php echo $getItem->aArtist; ?>"></p>
 <p><label><strong>Year:</strong></label> 
 <input name="year" class="input1" type="text" value="<?php echo $getItem->aYear; ?>"></p>
</fieldset>

<fieldset>
 <legend>Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "<option>No Listings Available</option>\n";
 }
	
 else {
  while($getCat = $scorpions->obj($true)) {
   $cats = explode('!', $getItem->fNiq);
   echo '<option value="' . $getCat->id . '"'; 
	 if(in_array($getCat->id, $cats)) {
	  echo ' selected="selected"'; 
   }
   echo '>' . $getCat->subject . "</option>\n";
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Edit Album"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
 }
 
 elseif (isset($_GET['p']) && $_GET['p'] == 'erase') {
  $id = $tigers->cleanMys($_GET['id']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect album or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
 
  $select = "SELECT * FROM `$_ST[lyrics_albums]` WHERE `aID` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select that' . 
   ' specific album.', true, $select);
  }
  $getItem = $scorpions->obj($true);
?>
<p>You are about to delete the <strong><?php echo $getItem->aName; ?></strong> 
album; please be aware that once you delete a album, it is gone forever. <em>This 
cannot be undone!</em> To proceed, click the "Delete Album" button.</p>

<form action="lyrics.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->aID; ?>">

<fieldset>
 <legend>Delete Album</legend>
 <p class="tc">Deleting <strong><?php echo $getItem->aName; ?></strong></p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Delete Album"></p>
</fieldset>
</form>
<?php 
 }
 
 else {
  $select = "SELECT * FROM `$_ST[lyrics_albums]` ORDER BY `aID` DESC LIMIT" . 
  " $start, $per_page";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select the' . 
   ' lyrics from the database.', true, $select);
  }
  $count = $scorpions->total($true);

  if($count > 0) {
?>

<table class="index">
<thead><tr>
 <th>ID</th>
 <th>Album Name</th>
 <th>Artist</th>
 <th>Listing</th>
 <th>Action</th>
</tr></thead>
<?php 
   while($getItem = $scorpions->obj($true)) {
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->aID; ?></td>
 <td class="tc"><?php echo $getItem->aName; ?></td>
 <td class="tc"><?php echo $getItem->aArtist; ?></td>
 <td class="tc"><?php echo $wolves->getSubject($getItem->fNiq); ?></td>
 <td class="floatIcons tc">
  <a href="lyrics.php?g=albums&#38;p=old&#38;d=<?php echo $getItem->aID; ?>">
   <img src="img/icons/edit.png" alt="">
  </a> 
  <a href="lyrics.php?g=albums&#38;p=erase&#38;d=<?php echo $getItem->aID; ?>">
   <img src="img/icons/delete.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php 
   } 
   echo "</table>\n\n";
   echo '<p id="pagination">Pages: ';
   $total = is_countable($cheetahs->lyricsList('', 'albums')) ? count($cheetahs->lyricsList('', 'albums')) : 0;
   $pages = ceil($total / $per_page);

   for($i = 1; $i <= $pages; $i++) {
    if($page == $i) {
     echo $i . ' ';
    } else {
     echo '<a href="lyrics.php?g=albums&#38;p=' . $i . '">' . $i . '</a> ';
    }
   }
   echo "</p>\n";
  } else {
   echo "\n<p class=\"tc\">Currently no albums!</p>\n";
  }
 }
} 

elseif (isset($_GET['g']) && $_GET['g'] == 'songs') {
 if(isset($_GET['p']) && $_GET['p'] == 'new') {
?>
<form action="lyrics.php" method="post">
<fieldset>
 <legend>Details</legend>
 <p><label><strong>Song Name:</strong></label> 
 <input name="song" class="input1" type="text"></p>
 <p><label><strong>Album:</strong></label> 
 <select name="album" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[lyrics_albums]` ORDER BY `aName` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Albums Available</option>\n";
 }
	
 else {
  while($getItem = $scorpions->obj($true)) {
   echo '  <option value="' . $getItem->aID . '">' . $getItem->aName . "</option>\n";
  }
 }
?>
 </select></p>
 <p><strong><strong>Song Lyrics</strong></strong><br>
  <textarea name="lyrics" cols="50" rows="8" style="height: 300px; margin: 0 1% 0 0; width: 99%;"></textarea>
 </p>
</fieldset>

<fieldset>
 <legend>Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Listings Available</option>\n";
 }
	
 else {
  while($getItem = $scorpions->obj($true)) {
   echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Song"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
 } 
 
 elseif (isset($_GET['p']) && $_GET['p'] == 'old') {
  $id = $tigers->cleanMys($_GET['d']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect song or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
 
  $select = "SELECT * FROM `$_ST[lyrics]` WHERE `lyID` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select that' . 
   ' specific lyric.', true, $select);
  }
  $getItem = $scorpions->obj($true);
 ?>
<form action="lyrics.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->lyID; ?>">

<fieldset>
 <legend>Details</legend>
 <p><label><strong>Song Name:</strong></label> 
 <input name="song" class="input1" type="text" value="<?php echo $getItem->lyName; ?>"></p>
 <p><label><strong>Album:</strong></label> <select name="album" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[lyrics_albums]` ORDER BY `aName` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Albums Available</option>\n";
 }
	
 else {
  while($getCat = $scorpions->obj($true)) {
   $cats = explode('!', $getItem->aNiq);
   echo '  <option value="' . $getCat->aID . '"';
	 if(in_array($getCat->aID, $cats)) {
	  echo ' selected="selected"'; 
   }
   echo '>' . $getCat->aName . "</option>\n";
  }
 }
?>
 </select></p>
 <p><strong>Song Lyrics</strong><br>
  <textarea name="lyrics" cols="50" rows="8" style="height: 300px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->lyText; ?>
  </textarea>
 </p>
</fieldset>

<fieldset>
 <legend>Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Listings Available</option>\n";
 }
	
 else {
  while($getCat = $scorpions->obj($true)) {
   $cats = explode('!', $getItem->fNiq);
   echo '  <option value="' . $getCat->id . '"';
	 if(in_array($getCat->id, $cats)) {
	  echo ' selected="selected"'; 
   }
   echo '>' . $getCat->subject . "</option>\n";
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Edit Song"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
 }
 
 elseif (isset($_GET['p']) && $_GET['p'] == 'erase') {
  $id = $tigers->cleanMys($_GET['d']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	 ' you selected an incorrect song or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
 
  $select = "SELECT * FROM `$_ST[lyrics]` WHERE `lyID` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select that' . 
   ' specific song.', true, $select);
  }
  $getItem = $scorpions->obj($true);
?>
<p>You are about to delete the <strong><?php echo $getItem->lyName; ?></strong> 
song; please be aware that once you delete a song, it is gone forever. <em>This 
cannot be undone!</em> To proceed, click the "Delete Song" button.</p>

<form action="lyrics.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->lyID; ?>">

<fieldset>
 <legend>Delete Song</legend>
 <p class="tc">Deleting <strong><?php echo $getItem->lyName; ?></strong></p>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Delete Song">
 </p>
</fieldset>
</form>
<?php 
 }
 
 else {
  $select = "SELECT * FROM `$_ST[lyrics]` ORDER BY `lyID` DESC LIMIT $start," . 
  " $per_page";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select the' . 
   ' lyrics from the database.', true, $select);
  }
  $count = $scorpions->total($true);

  if($count > 0) {
?>

<table class="index">
<thead><tr>
 <th>ID</th>
 <th>Song Name</th>
 <th>Album</th>
 <th>Listing</th>
 <th>Action</th>
</tr></thead>
<?php 
   while($getItem = $scorpions->obj($true)) {
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->lyID; ?></td>
 <td class="tc"><?php echo $getItem->lyName; ?></td>
 <td class="tc"><?php echo $cheetahs->getAlbum($getItem->aNiq); ?></td>
 <td class="tc"><?php echo $wolves->getSubject($getItem->fNiq); ?></td>
 <td class="floatIcons tc">
  <a href="lyrics.php?g=songs&#38;p=old&#38;d=<?php echo $getItem->lyID; ?>">
   <img src="img/icons/edit.png" alt="">
  </a> 
  <a href="lyrics.php?g=songs&#38;p=erase&#38;d=<?php echo $getItem->lyID; ?>">
   <img src="img/icons/delete.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php 
   } 
   echo "</table>\n\n<p id=\"pagination\">Pages: ";
   $total = is_countable($cheetahs->lyricsList()) ? count($cheetahs->lyricsList()) : 0;
   $pages = ceil($total / $per_page);

   for($i = 1; $i <= $pages; $i++) {
    if($page == $i) {
     echo $i . ' ';
    } else {
     echo '<a href="lyrics.php?g=songs&#38;p=' . $i . '">' . $i . '</a> ';
    }
   }
   echo "</p>\n";
  } 

  else {
   echo "\n<p class=\"tc\">Currently no songs!</p>\n";
  }
 }
}

/** 
 * Forms have been set, let's process! 
 */ 
elseif (isset($_POST['action'])) {
 if($_POST['action'] == 'Add Song') {
  $song = $tigers->cleanMys($_POST['song']);
  if(empty($song)) {
   $tigers->displayError('Form Error', 'The <samp>song name</samp>' . 
	 ' field is empty.', false);
  }
	$album = $tigers->cleanMys($_POST['album']);
  if(!in_array($album, $cheetahs->lyricsList('', 'albums'))) {
   $tigers->displayError('Form Error', 'The <samp>album</samp> field' . 
	 ' is empty.', false);
  }
	$lyrics = $tigers->cleanMys($_POST['lyrics']);
  if(empty($lyrics)) {
   $tigers->displayError('Form Error', 'The <samp>lyrics</samp> field' . 
	 ' is empty.', false);
  }
	$listing = $tigers->cleanMys($_POST['listing']);
  if(!in_array($listing, $wolves->listingsList())) {
   $tigers->displayError('Form Error', 'The <samp>listing</samp> field' . 
	 ' is empty.', false);
  }
	
	$insert = "INSERT INTO `$_ST[lyrics]` (`fNiq`, `aNiq`, `lyName`, `lyText`)" . 
	" VALUES ('$listing', '$album', '$song', '$lyrics')";
	$scorpions->query("SET NAMES 'utf8';");
	$true = $scorpions->query($insert);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to add' . 
	 ' the song to the database.', true, $insert);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('The song was added to the database!</p>');
   echo $tigers->backLink('lyrics', 'songs');
	 echo $tigers->backLink('lyrics');
  }
 }
 
 elseif ($_POST['action'] == 'Edit Song') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id) || !ctype_digit($id)) {
   $tigers->displayError('Form Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect song or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
  $song = $tigers->cleanMys($_POST['song']);
  if(empty($song)) {
   $tigers->displayError('Form Error', 'The <samp>song name</samp> field' . 
	 ' is empty.', false);
  }
	$album = $tigers->cleanMys((int)$_POST['album']);
  if(!in_array($album, $cheetahs->lyricsList('', 'albums'))) {
   $tigers->displayError('Form Error', 'The <samp>album</samp> field is' . 
	 ' empty.', false);
  }
	$lyrics = $tigers->cleanMys($_POST['lyrics']);
  if(empty($lyrics)) {
   $tigers->displayError('Form Error', 'The <samp>lyrics</samp> field' . 
	 ' is empty.', false);
  }
	$listing = $tigers->cleanMys($_POST['listing']);
  if(!in_array($listing, $wolves->listingsList())) {
   $tigers->displayError('Form Error', 'The <samp>listing</samp> field' . 
	 ' is empty.', false);
  }
	
	$update = "UPDATE `$_ST[lyrics]` SET `fNiq` = '$listing', `aNiq` = '$album'," . 
	" `lyName` = '$song', `lyText` = '$lyrics' WHERE `lyID` = '$id' LIMIT 1";
	$scorpions->query("SET NAMES 'utf8';");
	$true = $scorpions->query($update);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to edit the' . 
   ' song.', true, $update);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('The song was edited!');
   echo $tigers->backLink('lyrics', 'songs');
	 echo $tigers->backLink('lyrics');
  }
 }
 
 elseif ($_POST['action'] == 'Delete Song') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Form Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect song or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
	
	$delete = "DELETE FROM `$_ST[lyrics]` WHERE `lyID` = '$id' LIMIT 1";
	$true = $scorpions->query($delete);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to delete' . 
	 ' the song from the database.', true, $delete);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('The song was deleted!');
   echo $tigers->backLink('lyrics', 'songs');
	 echo $tigers->backLink('lyrics');
  }
 }
 
 elseif ($_POST['action'] == 'Add Album') {
  $album = $tigers->cleanMys($_POST['album']);
  if(empty($album)) {
   $tigers->displayError('Form Error', 'The <samp>album name</samp> field' . 
	 ' is empty.', false);
  }
	$artist = $tigers->cleanMys($_POST['artist']);
	$listing = $tigers->cleanMys($_POST['listing']);
  if(!in_array($listing, $wolves->listingsList())) {
   $tigers->displayError('Form Error', 'The <samp>listing</samp> field' . 
	 ' is empty.', false);
  }
	$year = $tigers->cleanMys($_POST['year']);
  if(empty($year)) {
   $y = date('Y');
  } else {
	 $y = $year;
	}
	
	$insert = "INSERT INTO `$_ST[lyrics_albums]` (`fNiq`, `aArtist`, `aName`," . 
	" `aYear`) VALUES ('$listing', '$artist', '$album', '$y')";
	$scorpions->query("SET NAMES 'utf8';");
	$true = $scorpions->query($insert);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to add' . 
	 ' the album to the database.', true, $insert);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('The album was added to the database!');
   echo $tigers->backLink('lyrics', 'albums');
	 echo $tigers->backLink('lyrics');
  }
 }
 
 elseif ($_POST['action'] == 'Edit Album') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Form Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect album or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
  $album = $tigers->cleanMys($_POST['album']);
  if(empty($album)) {
   $tigers->displayError('Form Error', 'The <samp>album name</samp>' . 
	 ' field is empty.', false);
  }
	$artist = $tigers->cleanMys($_POST['artist']);
	$listing = $tigers->cleanMys($_POST['listing']);
  if(!in_array($listing, $wolves->listingsList())) {
   $tigers->displayError('Form Error', 'The <samp>listing</samp> field' . 
	 ' is empty.', false);
  }
	$year = $tigers->cleanMys($_POST['year']);
  if(empty($year)) {
   $y = date('Y');
  } else {
	 $y = $year;
	}
	
	$update = "UPDATE `$_ST[lyrics_albums]` SET `fNiq` = '$listing', `aArtist` =" . 
	" '$artist', `aName` = '$album', `aYear` = '$y' WHERE `aID` = '$id' LIMIT 1";
	$scorpions->query("SET NAMES 'utf8';");
	$true = $scorpions->query($update);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to edit' . 
	 ' the album.', true, $update);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('The album was edited!');
   echo $tigers->backLink('lyrics', 'albums');
	 echo $tigers->backLink('lyrics');
  }
 }
 
 elseif ($_POST['action'] == 'Delete Song') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Form Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect album or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
	
	$delete = "DELETE FROM `$_ST[lyrics_albums]` WHERE `aID` = '$id' LIMIT 1";
	$true = $scorpions->query($delete);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to' . 
	 ' delete the album from the database.', true, $delete);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('The album was deleted!');
   echo $tigers->backLink('lyrics', 'albums');
	 echo $tigers->backLink('lyrics');
  }
 }
}

/** 
 * Main index for <lyrics.php> :D 
 */ 
else {
?>
<p>Welcome to <samp>lyrics.php</samp>, the outlet to adding, editing and 
deleting your songs and albums. Albums and Songs have two different sections, 
which you can access below:</p>

<table class="index">
<tbody><tr>
 <td class="tc"><a href="lyrics.php?g=albums">Albums</a></td>
 <td class="tc"><a href="lyrics.php?g=songs">Songs</a></td>
</tr></tbody>
</table>
<?php 
 }
}

else {
?>
<p class="errorButton"><span class="error">ERROR:</span> You have turned off the 
<samp>lyrics</samp> feature. To turn it on this feature, visit the 
<a href="options.php">&#171; options page</a>!</p>
<?php 
}

require('footer.php');
