<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <codes.php> 
 * @since      September 2nd, 2010 
 * @version    1.0  
 */ 
$getTitle = 'Quotes';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

?>
    <p class="scriptButton"><span class="script"><b>Notice:</b></span> This extension is simply legacy hence is not supported by current version of the LA script. I would recommend turning it off.
    </p>
   <?php
$sp = !isset($_GET['g']) ? '<span><a href="quotes.php?g=new">Add Quote</a>' .
 '</span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if($seahorses->getOption('quotes_opt') == 'y') {
 if(isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<form action="quotes.php" method="post">
<fieldset>
 <p><label><strong>Author:</strong></label> 
 <input name="author" class="input1" type="text"></p>
 <p><strong>Quote</strong><br>
 <textarea name="quote" cols="50" rows="8" style="height: 200px; margin: 0 1% 0 0; width: 99%;"></textarea></p>
</fieldset>

<fieldset>
<legend>Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true   = $scorpions->query($select);
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
  <input name="action" class="input2" type="submit" value="Add Quote"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
 }

 elseif (isset($_POST['action']) && $_POST['action'] == 'Add Quote') {
  $author = $tigers->cleanMys($_POST['author']);
  $quote  = $tigers->cleanMys($_POST['quote'], 'n');
  if(empty($quote)) {
   $tigers->displayError('Form Error', 'The <samp>quote</samp> field is empty.', 
   false);
  }
  $listing = $tigers->cleanMys($_POST['listing']);
  if(!in_array($listing, $wolves->listingsList())) {
   $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' . 
   ' invalid.', false);
  } 

  $insert = "INSERT INTO `$_ST[quotes]` (`fNiq`, `qAuthor`, `qQuote`, `qUpdated`," . 
  " `qAdded`) VALUES ('$listing', '$author', '$quote', '1970-01-01 00:00:00', NOW())";
  $scorpions->query("SET NAMES 'utf8';");
  $true = $scorpions->query($insert);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to add' . 
	 ' the quote to the database.|Make sure your quotes table exists.', 
	 true, $insert);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('Your quote was added to the database!');
   echo $tigers->backLink('quotes');
  }
 }

 /** 
  * Edit 
  */ 
 elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
  $id = $tigers->cleanMys($_GET['d']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect quote or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
 
  $select = "SELECT * FROM `$_ST[quotes]` WHERE `qID` = '$id' LIMIT 1";
  $true   = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to' . 
	 ' select that specific quote.|Make sure the ID is not empty and the quotes' . 
	 ' table exists.', true, $select);
  }
  $getItem = $scorpions->obj($true);
?>
<form action="quotes.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->qID; ?>">

<fieldset>
 <p><label><strong>Author:</strong></label> 
 <input name="author" class="input1" type="text" value="<?php echo $getItem->qAuthor; ?>"></p>
 <p>
  <strong>Quote</strong><br>
  <textarea name="quote" cols="50" rows="8" style="height: 200px; margin: 0 1% 0 0; width: 100%;">
<?php echo $getItem->qQuote; ?>
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
  echo "  <option value=\"0\">No Listings Available</option>\n";
 }
	
 else {
  while($getCat = $scorpions->obj($true)) {
   $cats = $tigers->emptyarray(explode('!', $getItem->fNiq));
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
  <input name="action" class="input2" type="submit" value="Edit Quote">
 </p>
</fieldset>
</form>
<?php 
 }

 elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Quote') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	 ' you selected an incorrect quote or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
  $author = $tigers->cleanMys($_POST['author']);
  $quote  = $tigers->cleanMys($_POST['quote'], 'n');
  if(empty($quote)) {
   $tigers->displayError('Form Error', 'The <samp>quote</samp> field is empty.', 
   false);
  }
  $listing = $tigers->cleanMys($_POST['listing']);
  if(!in_array($listing, $wolves->listingsList())) {
   $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' . 
   ' invalid.', false);
  } 

  $update = "UPDATE `$_ST[quotes]` SET `fNiq` = '$listing', `qAuthor` = '$author'," . 
  " `qQuote` = '$quote', `qUpdated` = NOW() WHERE `qID` = '$id' LIMIT 1";
  $scorpions->query("SET NAMES 'utf8';");
  $true = $scorpions->query($update);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to update' . 
	 ' the quote.', true, $update);
  } elseif ($true == true) {
   echo $tigers->displaySuccess('Your quote was updated! :D');
   echo $tigers->backLink('quotes');
  }
 }

 /** 
  * Delete 
  */ 
 elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
  $id = $tigers->cleanMys($_GET['d']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect quote or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 
 
  $select = "SELECT * FROM `$_ST[quotes]` WHERE `qID` = '$id' LIMIT 1";
  $true   = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select that' . 
   ' specific quote.', true, $select);
  }
  $getItem = $scorpions->obj($true);
?>
<p>You are about to delete the <strong><?php echo $getItem->qID; ?></strong> 
quote; please be aware that once you delete a quote, it is gone forever. <em>This 
cannot be undone!</em> To proceed, click the "Delete Quote" button.</p>

<form action="quotes.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->qID; ?>">

<fieldset>
 <legend>Delete Quote</legend>
 <p class="tc">
  Deleting <strong><?php echo $getItem->qQuote; ?></strong><br>
  <input name="action" class="input2" type="submit" value="Delete Quote">
 </p>
</fieldset>
</form>
<?php 
 }

 elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Quote') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect quote or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  } 

  $delete = "DELETE FROM `$_ST[quotes]` WHERE `qID` = '$id' LIMIT 1";
  $true   = $scorpions->query($delete);

  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to' . 
	 ' delete the quote.|Make sure your ID is not empty and your quotes table' . 
	 ' exists.', true, $delete);
  } elseif ($true == true) {
    echo $tigers->displaySuccess('Your quote was deleted!');
    echo $tigers->backLink('quotes');
   }
  }
 
 /** 
  * Delete 
  */ 
 else {
?>
<p>Welcome to <samp>listings.php</samp>, the page to add a listing and edit or 
delete current ones! Below is the list of your listings. To edit or delete, 
click "Edit" or "Delete" by the appropriate listing.</p>

<form action="quotes.php" method="get">
<input name="g" type="hidden" value="searchListings">

<fieldset>
 <legend>Search Listings</legend>
 <p><label><strong>Listing:</strong></label> <select name="listingid" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Listings Available</option>\n";
 }

 else {
  while($getTion = $scorpions->obj($true)) {
   echo '  <option value="' . $getTion->id . '">' . $getTion->subject . "</option>\n";
  }
 }
?>
</select></p>
<p class="tc"><input class="input2" type="submit" value="Search Listings"></p>
</fieldset>
</form>

<?php 
  $select = "SELECT * FROM `$_ST[quotes]`";
  if(
   (isset($_GET['g']) && $_GET['g'] == 'searchListings') &&
   (!in_array($_GET['listingid'], $wolves->listingsList()))
  ) {
   $listingid = $tigers->cleanMys($_GET['listingid']);
   $select   .= " WHERE `fNiq` = '$listingid'";
  }
  $select .= " ORDER BY `qUpdated` DESC, `qAdded` DESC, `qID` DESC LIMIT $start," . 
  " $per_page";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select the' . 
   ' quotes from the database.', true, $select);
  }
  $count = $scorpions->total($true);

  if($count > 0) {
?>

<table class="index">
<thead><tr>
 <th>ID</th>
 <th>Quote</th>
 <th>Listing</th>
 <th>Action</th>
</tr></thead>
<?php 
   while($getItem = $scorpions->obj($true)) {
	  $q = strlen($getItem->qQuote) > 50 ? substr(html_entity_decode($getItem->qQuote), 0, 50) . 
		'...' : html_entity_decode($getItem->qQuote); 
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->qID; ?></td>
 <td class="tc"><?php echo $q; ?></td>
 <td class="tc"><?php echo $wolves->getSubject($getItem->fNiq); ?></td>
 <td class="floatIcons tc">
  <a href="quotes.php?g=old&#38;d=<?php echo $getItem->qID; ?>">
   <img src="img/icons/edit.png" alt="">
  </a> 
  <a href="quotes.php?g=erase&#38;d=<?php echo $getItem->qID; ?>">
   <img src="img/icons/delete.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php 
   } 
   echo "</table>\n\n<p id=\"pagination\">Pages: ";

   $s     = $listingid ?? '';
   $total = is_countable($cheetahs->quotesList($s)) ? count($cheetahs->quotesList($s)) : 0;
   $pages = ceil($total / $per_page);
	 
   for($i = 1; $i <= $pages; $i++) {
    if($page == $i) {
     echo $i . ' ';
    } else { 
     $pg = '<a href="quotes.php?';
     if(isset($listingid)) {
      $pg .= 'g=searchListings&#38;listingid=' . $listingid . '&#38;';
     }
     $pg .= 'p=' . $i . '">' . $i . '</a> ';
     echo $pg;
    }
   }

   echo "</p>\n"; 
  } else {
   echo "<p class=\"tc\">Currently no quotes!</p>\n";
  }
 }
}

else {
?>
<p class="errorButton"><span class="error">ERROR:</span> You have turned off the 
<samp>quotes</samp> feature. To install and use this feature, visit the 
<a href="addons.php">&#171; addons page</a>!</p>
<?php 
}

require('footer.php');
