<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <codes-categories.php> 
 * @since      September 2nd, 2010 
 * @version    2.1.4 
 */ 
$getTitle = 'Codes: Categories';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g']) ? '<span><a href="codes-categories.php?g=new">Add' .
    ' Code Category</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !ctype_digit($_GET['p'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if(isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<form action="codes-categories.php" method="post">

<fieldset>
 <legend>Add Category</legend>
 <p><label><strong>Listing:</strong></label> 
 <select name="listing[]" class="input1" multiple="multiple" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Listings Available</option>\n";
 }
	
 else {
  echo "  <option value=\"0\">&#187; Collective</option>\n";
  while($getItem = $scorpions->obj($true)) {
   echo '  <option value="' . $getItem->id . '">' . $getItem->subject .
   "</option>\n";
  }
 }
?>
 </select></p>
 <p><label><strong>Category:</strong></label> 
 <input name="category" class="input1" type="text"></p>
 <p><label><strong>Parent Category:</strong></label> 
 <select name="parent" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `catParent` = '0'" .
     ' ORDER BY `catName` ASC';
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "  <option>No Categories Available</option>\n";
 }

 else {
  echo "  <option selected=\"selected\" value=\"0\">No Parent</option>\n";
  while($getItem = $scorpions->obj($true)) {
   echo '<option value="' . $getItem->catID . '">' . $getItem->catName .
   "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Category"> 
  <input class="input2" type="reset" value="Reset">
 </p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Add Category') {
 if($_POST['listing'] != 0) {
  $listing = $_POST['listing'];
  $listing = array_map(array($tigers, 'cleanMys'), $listing);
  $list = implode('!', $listing);
  $list = '!' . trim($list, '!') . '!';
 } else {
  $list = '!0!';
 }
 $name = $tigers->cleanMys($_POST['category']);
 if(empty($name)) {
  $tigers->displayError('Form Error', 'In order to create a category, you must' . 
  ' enter a name first. :P', false);
 }
 if(isset($_POST['parent']) && in_array($_POST['parent'], $cheetahs->categoryCodes())) {
  $p = $tigers->cleanMys($_POST['parent']);
 } else {
  $p = '0';
 }

 $insert = "INSERT INTO `$_ST[codes_categories]` (`fNiq`, `catName`, `catParent`)" . 
 " VALUES ('$list', '$name', '$p')";
 $scorpions->query("SET NAMES 'utf8';");
 $true = $scorpions->query($insert);

 if($true == false) {
  $tigers->displayError('Database Error', 'Unable to add the category to' . 
	' the database.|Make sure your category table exists.'. true, $insert);
 } elseif ($true == true) {
  echo $tigers->displaySuccess('Your codes category was added to the database!');
  echo $tigers->backLink('codes_categories');
 }
}

/** 
 * Edit 
 */ 
elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
 $id = $tigers->cleanMys($_GET['d']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	' selected an incorrect code category or you\'re trying to access something' . 
  ' that doesn\'t exist. Go back and try again.', false);
 } 

 $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `catID` = '$id' LIMIT 1";
 $true   = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select that' . 
  ' specific code category.', true, $select);
 }
 $getItem = $scorpions->obj($true);
?>
<form action="codes-categories.php" method="post">
<input name="id" type="hidden" value="<?php echo $getItem->catID; ?>">

<fieldset>
 <legend>Edit Category</legend>
 <p><label><strong>Listing:</strong></label> 
 <select name="listing[]" class="input1" multiple="multiple" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Listings Available</option>\n";
 }

 else {
  echo "  <option value=\"0\">&#187; Collective</option>\n";
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
 <p><label><strong>Category:</strong></label> 
 <input name="category" class="input1" type="text" value="<?php echo $getItem->catName; ?>"></p>
 <p><label><strong>Parent Category:</strong></label> 
 <select name="parent" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `catParent` = '0'" .
     ' ORDER BY `catName` ASC';
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "  <option>No Categories Available</option>\n";
 }

 else {
  echo "  <option value=\"0\">No Parent</option>\n";
  while($getCat = $scorpions->obj($true)) {
   echo '  <option value="' . $getCat->catID . '"';
	 if($getCat->catID == $getItem->catParent) {
	  echo ' selected="selected"'; 
	 }
	 echo '>' . $getCat->catName . "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Edit Category">
 </p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Category') {
 $id = $tigers->cleanMys($_POST['id']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	' selected an incorrect code category or you\'re trying to access something' . 
  ' that doesn\'t exist. Go back and try again.', false);
 } 
 if($_POST['listing'] != 0) {
  $listing = $_POST['listing'];
  $listing = array_map(array($tigers, 'cleanMys'), $listing);
  $list = implode('!', $listing);
  $list = '!' . trim($list, '!') . '!';
 } else {
  $list = '!0!';
 }
 $name = $tigers->cleanMys($_POST['category']);
 if(empty($name)) {
  $tigers->displayError('Form Error', 'In order to edit a category, you must' . 
  ' enter a name first. :P', false);
 }
 if(isset($_POST['parent']) && in_array($_POST['parent'], $cheetahs->categoryCodes())) {
  $p = $tigers->cleanMys($_POST['parent']);
 } else {
  $p = '0';
 }

 $update = "UPDATE `$_ST[codes_categories]` SET `fNiq` = '$list', `catName` =" . 
 " '$name', `catParent` = '$p' WHERE `catID` = '$id' LIMIT 1";
 $scorpions->query("SET NAMES 'utf8';");
 $true = $scorpions->query($update);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to edit the' . 
  ' code category.', true, $update);
 } elseif ($true == true) {
  echo $tigers->displaySuccess('Your codes category was edited!');
  echo $tigers->backLink('codes_categories', $id);
  echo $tigers->backLink('codes_categories');
 }
}

/** 
 * Delete 
 */ 
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
 $id = $tigers->cleanMys($_GET['d']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	' selected an incorrect category or you\'re trying to access something that' . 
	' doesn\'t exist. Go back and try again.', false);
 }
 
 $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `catID` = '$id' LIMIT 1";
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select that' . 
  ' specific code category.', true, $select);
 }
 $getItem = $scorpions->obj($true);
?>
<p>You are about to delete the <strong><?php echo $getItem->catName; ?></strong> 
category; please be aware that once you delete a category, it is gone forever. 
<em>This cannot be undone!</em> To proceed, click the "Delete Category" button.</p>

<form action="codes-categories.php" method="post">
<p class="noMargin"><input name="id" type="hidden" value="<?php echo $getItem->catID; ?>"></p>

<fieldset>
 <legend>Delete Category</legend>
 <p class="tc">
  Deleting <strong><?php echo $getItem->catName; ?></strong><br>
  <input name="action" class="input2" type="submit" value="Delete Category">
 </p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Category') {
 $id = $tigers->cleanMys($_POST['id']);

 $delete = "DELETE FROM `$_ST[codes_categories]` WHERE `catID` = '$id' LIMIT 1";
 $true = $scorpions->query($delete);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to delete the' . 
  ' code category.', true, $delete);
 } elseif ($true == true) {
  echo $tigers->displaySuccess('Your category was deleted!');
  echo $tigers->backLink('codes_categories');
 }
}

/** 
 * Index 
 */ 
else {
?>
<p>Welcome to <samp>codes-categories.php</samp>, the page to add codes 
categories and edit or delete your current ones! Below is your list of 
categories. To edit or delete a current one, click "Edit" or "Delete" by the 
appropriate category.</p>
<?php 
 $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `catParent` = '0'" . 
 " ORDER BY `catName` ASC LIMIT $start, $per_page";
 $true  = $scorpions->query($select);
 $count = $scorpions->total($true);

 if($count > 0) {
?>
<table class="index">
<thead><tr>
 <th>Category ID</th>
 <th>Listing</th>
 <th>Category Name</th>
 <th>Action</th>
</tr></thead>
<?php
  while($getItem = $scorpions->obj($true)) {
   $catid = $getItem->catID;
	 $listingnow = $getItem->fNiq == '!0!' || $getItem->fNiq == '0' ?
         'Collective' : $wolves->pullSubjects($getItem->fNiq, '!');
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->catID; ?></td>
 <td class="tc"><?php echo $listingnow; ?></td>
 <td class="tc"><?php echo $getItem->catName; ?></td>
 <td class="floatIcons tc">
  <a href="codes-categories.php?g=old&#38;d=<?php echo $getItem->catID; ?>">
   <img src="img/icons/edit.png" alt="">
  </a> 
  <a href="codes-categories.php?g=erase&#38;d=<?php echo $getItem->catID; ?>">
   <img src="img/icons/delete.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php
   $pull = $scorpions->query("SELECT * FROM `$_ST[codes_categories]` WHERE" . 
   " `catParent` = '$catid' ORDER BY `catName`");
   while($items = $scorpions->obj($pull)) {
	  $pulllistingnow = $items->fNiq == '!0!' || $items->fNiq == '0' ?
          'Collective' : $wolves->pullSubjects($items->fNiq, '!');
    $n = $cheetahs->getCodesCatName($items->catParent) . ' &#187; ' . $items->catName;
?>
<tbody class="subcategory"><tr>
 <td class="tc"><?php echo $items->catID; ?></td>
 <td class="tc"><?php echo $pulllistingnow; ?></td>
 <td class="tc"><?php echo $n; ?></td>
 <td class="floatIcons tc">
  <a href="codes-categories.php?g=old&#38;d=<?php echo $items->catID; ?>">
   <img src="img/icons/edit.png" alt="">
  </a> 
  <a href="codes-categories.php?g=erase&#38;d=<?php echo $items->catID; ?>">
   <img src="img/icons/delete.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php 
   }
  } 
  echo "</table>\n";
  echo "\n<p id=\"pagination\">Pages: ";

  $total = is_countable($cheetahs->categoryCodes()) ? count($cheetahs->categoryCodes()) : 0;
  $pages = ceil($total / $per_page);

  for($i = 1; $i <= $pages; $i++) {
   if($page == $i) {
    echo $i . ' ';
   } else { 
    echo '<a href="codes-categories.php?p=' . $i . '">' . $i . '</a> ';
   }
  }

  echo "</p>\n";
 } 

 else {
  echo "<p class=\"tc\">Currently no categories!</p>\n";
 }
}

require('footer.php');
