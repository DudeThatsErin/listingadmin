<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <wishlist.php> 
 * @since      September 2nd, 2010 
 * @version    1.0    
 */ 
$getTitle = 'Categories';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g']) ? '<span><a href="categories.php?g=new">Add Category</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !ctype_digit($_GET['p'])) { 
 $start = 0;
} else {
 $start = $tigers->cleanMys($per_page * ($tigers->cleanMys($_GET['p']) - 1));
}
$ender = $start + $per_page;

if(isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<form action="categories.php" method="post">
<fieldset>
 <legend>Add Category</legend>
 <p><label><strong>Category:</strong></label> 
 <input name="category" class="input1" type="text"></p>
 <p><label><strong>Parent Category:</strong></label> 
 <select name="parent" class="input1" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY" . 
 ' `catname` ASC';
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "  <option value=\"0\">No Categories Available</option>\n";
 }

 else {
  while($getItem = $scorpions->obj($true)) {
   echo '  <option value="' . $getItem->catid . '">' . $getItem->catname .
   "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Category"> 
  <input class="input2" type="reset" value="Reset">
 </p>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Add Category') {
 $name = $tigers->cleanMys($_POST['category']);
 if(empty($name)) {
  $tigers->displayError('Script Error', 'In order to create a category,' . 
	' you must enter a name first. :P', false);
 }
 $p = !isset($_POST['parent']) || empty($_POST['parent']) ? 
 '0' : $tigers->cleanMys($_POST['parent']);

 $insert = "INSERT INTO `$_ST[categories]` (`catname`, `parent`) VALUES" . 
 " ('$name', '$p')";
 $scorpions->query("SET NAMES 'utf8';");
 $true = $scorpions->query($insert);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to add' . 
	' the category to the database.|Make sure your category table exists.'. 
	true, $insert);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
	" category was added to the database!</p>\n";
  echo $tigers->backLink('cat');
 }
}

/** 
 *  @section   Edit Category 
 */ 
elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
 $id = $tigers->cleanMys($_GET['d']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	' you selected an incorrect category or you\'re trying to access something' . 
	' that doesn\'t exist. Go back and try again.', false);
 } 

 $select = "SELECT * FROM `$_ST[categories]` WHERE `catid` = '$id' LIMIT 1";
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'Unable to select that specific' . 
	' category.|Make sure the ID is not empty and the category table exists.', 
	true, $select);
 }
 $getItem = $scorpions->obj($true);
?>
<form action="categories.php" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->catid; ?>">
</p>

<fieldset>
 <legend>Edit Category</legend>
 <p><label><strong>Category:</strong></label> 
 <input name="category" class="input1" type="text" value="<?php echo $getItem->catname; ?>"></p>
 <p><label><strong>Parent:</strong></label> 
 <select name="parent" class="input1" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY" . 
 ' `catname` ASC';
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "  <option value=\"0\">No Categories Available</option>\n";
 }

 else {
  while($getCat = $scorpions->obj($true)) {
   echo '  <option value="' . $getCat->catid . '"';
	 if($getCat->catid == $getItem->parent) {
	  echo ' selected="selected"'; 
	 }
	 echo '>' . $getCat->catname . "</option>\n";
  }
 } 
?>
 </select></p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Edit Category"></p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Category') {
 $id = $tigers->cleanMys($_POST['id']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	' you selected an incorrect category or you\'re trying to access something' . 
	' that doesn\'t exist. Go back and try again.', false);
 } 
 $name = $tigers->cleanMys($_POST['category']);
 if(empty($name)) {
  $tigers->displayError('Script Error', 'In order to edit a category,' . 
	' you must enter a name first. :P', false);
 }
 $p = !isset($_POST['parent']) || empty($_POST['parent']) ? 
 '0' : $tigers->cleanMys($_POST['parent']);

 $update = "UPDATE `$_ST[categories]` SET `catname` = '$name', `parent` = '$p'" . 
 " WHERE `catid` = '$id' LIMIT 1";
 $scorpions->query("SET NAMES 'utf8';");
 $true = $scorpions->query($update);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to edit' . 
	' the category.|Make sure your category table exists.', true, $update);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
	" category was edited!</p>\n";
  echo $tigers->backLink('cat');
 }
}

/** 
 *  @action   Delete Category 
 */ 
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
 $id = $tigers->cleanMys($_GET['d']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	' you selected an incorrect category or you\'re trying to access something' . 
	' that doesn\'t exist. Go back and try again.', false);
 }
 
 $select = "SELECT * FROM `$_ST[categories]` WHERE `catid` = '$id' LIMIT 1";
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select' . 
	' that specific category.|Make sure the ID is not empty and the listings table' . 
	' exists.', true, $select);
 }
 $getItem = $scorpions->obj($true);
?>
<p>You are about to delete the <strong><?php echo $getItem->catname; ?></strong> 
category; please be aware that once you delete a category, it is gone forever. 
<em>This cannot be undone!</em> To proceed, click the "Delete Category" button.</p>

<form action="categories.php" method="post">
<p class="noMargin"><input name="id" type="hidden" value="<?php echo $getItem->catid; ?>"></p>

<fieldset>
 <legend>Delete Category</legend>
 <p class="tc">Deleting <strong><?php echo $getItem->catname; ?></strong><br>
 <input name="action" class="input2" type="submit" value="Delete Category"></p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Category') {
 $id = $tigers->cleanMys((int)$_POST['id']);

 $delete = "DELETE FROM `$_ST[categories]` WHERE `catid` = '$id' LIMIT 1";
 $true = $scorpions->query($delete);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to delete' . 
	' the category.|Make sure your category table exists.', false);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
	" category was deleted!</p>\n";
  echo $tigers->backLink('cat');
 }
}

/** 
 *  @section   Index 
 */ 
else {
?>
<p>Welcome to <samp>categories.php</samp>, the page to add categories and edit 
or delete your current ones! Below is your list of categories. To edit or delete 
a current one, click "Edit" or "Delete" by the appropriate category.</p>
<?php 
 $select = $lions->categorySort();
 $count = is_countable($select) ? count($select) : 0;
 
 if($count > 0) {
  if((int)$ender > $count) {
	 $ender = $count;
	}
?>
<table class="index">
<thead><tr>
 <th>Category ID</th>
 <th>Category Name</th>
 <th>Action</th>
</tr></thead>
<?php 
  while($start < $ender) {
   $u = $select[$start];
	 $getItem = $lions->getCategory($u['catID']);
	 $c = $getItem->parent == 0 ? '' : ' class="subcategory"';
	 $a = $getItem->parent == 0 ? $getItem->catname : 
	 $lions->getCatName($getItem->parent) . ' &#187; ' . $getItem->catname;
?>
<tbody<?php echo $c; ?>><tr>
 <td class="tc"><?php echo $getItem->catid; ?></td>
 <td class="tc"><?php echo $a; ?></td>
 <td class="floatIcons tc">
  <a href="categories.php?g=old&#38;d=<?php echo $getItem->catid; ?>">
	 <img src="img/icons/edit.png" alt="">
	</a> 
  <a href="categories.php?g=erase&#38;d=<?php echo $getItem->catid; ?>">
	 <img src="img/icons/delete.png" alt="">
	</a>
 </td>
</tr></tbody>
<?php 
   $start++;
  } 
  echo "</table>\n";
 
  $p = !isset($_GET['p']) || !is_numeric($_GET['p']) ? 1 : $tigers->cleanMys($_GET['p']);
  $pages = ceil($count/$per_page);
	
  echo '<p id="pagination">Pages: ';
  for($i = 1; $i <= $pages; $i++) {
   if($p == $i) {
    echo $i . ' ';
   } else { 
    echo '<a href="categories.php?p=' . $i . '">' . $i . '</a> ';
   }
  }
  echo "</p>\n";
 } else {
  echo "<p class=\"tc\">Currently no categories!</p>\n";
 }
}

require('footer.php');
