<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <codes-sizes.php> 
 * @since      September 2nd, 2010 
 * @version    2.1.4 
 */ 
$getTitle = 'Codes: Sizes';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

$main    = true;
$success = array();
$errors  = array();

if(isset($_POST['action'])) {
 if(isset($_POST['action']) && $_POST['action'] == 'Add Size') {
  $main = false;
  $name = $tigers->cleanMys($_POST['name']);
  if(empty($name)) {
   $error['add'] = '<p class="errorButton"><span class="error">ERROR:</span> The <samp>name</samp> is empty!</p>';
  } 
  $order = isset($_POST['order']) && is_numeric($_POST['order']) ? 
  $tigers->cleanMys((int)$_POST['order']) : 0;

  $insert = "INSERT INTO `$_ST[codes_sizes]` (`sName`, `sOrder`) VALUES ('$name'," . 
  " '$order')";
  $scorpions->query("SET NAMES 'utf8';");
  $true = $scorpions->query($insert);

  if($true == false) {
   $errors['add'] = '<p class="errorButton">The script was unable to add the' .
   " size to the database.</p>\n";
   if(isset($_COOKIE['lalog'])) {
		$errors['add'] .= "<h4>Debug</h4>\n";
		$errors['add'] .= '<p class="mysqlButton"><span class="mysql">MySQL' .
            ' Errors:</span> ' . $scorpions->database->error() . "<br>\n<em>$insert</em></p>\n";
	 }
  } elseif ($true == true) {
   $success['add'] = $tigers->displaySuccess('The size was added to the database!');
	 $main = true;
  }
 }
 
 elseif (isset($_POST['action']) && $_POST['action'] == 'Update') {
  $count = is_countable($_POST['id']) ? count($_POST['id']) : 0;
	for($i = 0; $i < $count; $i++) {
	 $id    = $tigers->cleanMys($_POST['id'][$i]);
	 $order = $tigers->cleanMys($_POST['order'][$i]);
	 $name  = $tigers->cleanMys($_POST['name'][$i]);
	 
	 $update = "UPDATE `$_ST[codes_sizes]` SET `sOrder` = '$order', `sName` =" . 
   " '$name' WHERE `sID` = '$id' LIMIT 1";
	 $true = $scorpions->query($update);
	 if($true == true) {
	  $success['update'] = $tigers->displaySuccess('The size(s) were edited!'); 
	 } else {
	  $errors['add'] = '<p class="errorButton">The script was unable to edit the' .
    " code size.</p>\n";
    if(isset($_COOKIE['lalog'])) {
		 $errors['add'] .= "<h4>Debug</h4>\n";
		 $errors['add'] .= '<p class="mysqlButton"><span class="mysql">MySQL' .
             ' Errors:</span> ' . $scorpions->database->error() . "<br>\n<em>$update</em></p>\n";
	  }
	 }
	}
 }
 
 elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
  $count = is_countable($_POST['delete']) ? count($_POST['delete']) : 0;
	for($i = 0; $i < $count; $i++) {
	 $id = $tigers->cleanMys($_POST['delete'][$i]);
	
	 $delete = "DELETE FROM `$_ST[codes_sizes]` WHERE `sID` = '$id' LIMIT 1";
	 $true = $scorpions->query($delete);
	 if($true == true) {
	  $success['update'] = $tigers->displaySuccess('The size was deleted!'); 
	 } else {
	  $errors['add'] = '<p class="errorButton">The script was unable to delete' .
    " the code size from the database.</p>\n";
    if(isset($_COOKIE['lalog'])) {
		 $errors['add'] .= "<h4>Debug</h4>\n";
		 $errors['add'] .= '<p class="mysqlButton"><span class="mysql">MySQL' .
             ' Errors:</span> ' . $scorpions->database->error() . "<br>\n<em>$delete</em></p>\n";
	  }
	 }
	}
 }
}
?>

<div id="sizesFloatLeft">
<?php 
if($main) {
?>
<p>Welcome to <samp>codes-sizes.php</samp>, the page to add code sizes and edit or delete your current ones! Below
is your list of sizes. To edit a size, edit the value in the input field; to delete a size, check the checkbox by the appropriate size.</p>
<?php 
 if(isset($success['update']) || isset($error['update'])) {
  if(isset($success['update'])) {
   echo "<h3>Success!</h3>\n";
	 echo $success['update'] . "\n";
  } elseif (isset($errors['update'])) {
   echo "<h3>Error!</h3>\n";
	 echo $errors['update'] . "\n";
  }
 }

 $select = "SELECT * FROM `$_ST[codes_sizes]` ORDER BY `sOrder`, `sName` ASC";
 $true   = $scorpions->query($select);
 $count  = $scorpions->total($true);

 if($count > 0) {
?>
<form action="codes-sizes.php" method="post">
<table class="index">
<thead><tr>
 <th>Order</th>
 <th>Name</th>
 <th>Action</th>
</tr></thead>
<tfoot><tr>
 <td class="tc" colspan="3">
  <input name="action" class="input2" type="submit" value="Update"> 
  <input name="action" class="input2" type="submit" value="Delete">
 </td>
</tr></tfoot>
<?php 
  while($getItem = $scorpions->obj($true)) {
?>
<tbody><tr>
 <td class="tc">
  <input name="id[]" type="hidden" value="<?php echo $getItem->sID; ?>">
  <input name="order[]" class="input1" type="text" value="<?php echo $getItem->sOrder; ?>">
 </td>
 <td class="tc"><input name="name[]" class="input1" type="text" value="<?php echo $getItem->sName; ?>"></td>
 <td class="tc"><input name="delete[]" type="checkbox" value="<?php echo $getItem->sID; ?>"></td>
</tr></tbody>
<?php
  } 
  echo "</table>\n</form>\n";
 } else {
  echo "<p class=\"tc\">Currently no sizes!</p>\n";
 }
}
?>
</div>

<div id="sizesFloatRight">
<h3>Add Size</h3>
<form action="codes-sizes.php" method="post">

<fieldset>
 <legend>Add Size</legend>
<?php
 if(isset($success['add']) || isset($error['add'])) {
  if(isset($success['add'])) {
   echo "<h3>Success!</h3>\n";
   echo $success['add'] . "\n";
  } elseif (isset($errors['add'])) {
   echo "<h3>Error!</h3>\n";
	 echo $errors['add'] . "\n";
  }
 }
?>
 <p><label><strong>Name:</strong></label> 
 <input name="name" class="input1" type="text"></p>
 <p><label><strong>Order:</strong></label> 
 <input name="order" class="input1" type="text"></p>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Size"> 
  <input class="input2" type="reset" value="Reset">
 </p>
</fieldset>
</form>
</div>
<?php 
require('footer.php');
