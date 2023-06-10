<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com> 
 * @file       <wishlist.php> 
 * @since      March 13th, 2011  
 * @version    2.3beta     
 */ 
$getTitle = 'Error Log';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if(isset($_POST['action']) && $_POST['action'] == 'View') {
 if(!isset($_POST['log']) || empty($_POST['log'])) {
  $tigers->displayError('Script Error', 'You must select a log (or two,' . 
	' three, etc.) to edit. :P', false);
 }
 
 foreach($_POST['log'] as $log) {
  $o = $tigers->cleanMys($log);
	if(is_numeric($o)) {
?>
<table id="noeditStatistics">
 <?php $seahorses->errorView($log); ?>
</table>
<?php 
	}
 }
}

/** 
 * Mass-delete error logs \o/ 
 */ 
elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
 if(empty($_POST['log'])) {
  $tigers->displayError('Form Error', 'You need to select a log' . 
	' (or two, etc.) in order to delete them.', false);
 }
 
 foreach($_POST['log'] as $pm) {
  $errorlog = $tigers->cleanMys($pm);
	$delete   = "DELETE FROM `$_ST[errors]` WHERE `messID` = '$errorlog' LIMIT 1";
  $true     = $scorpions->query($delete);
	if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to delete' . 
	 ' the error log.', true, $delete);
  } elseif ($true == true) {
	 echo $tigers->displaySuccess('The error log was deleted!');
  }
  echo $tigers->backLink('errors');
 }
}

/** 
 * Index 
 */ 
else {
?>
<p>Welcome to <samp>errors.php</samp>, your error log! Here you'll be able to see
the errors that Listing Admin logs, and view/delete those logs.</p>
<?php  
 $select = "SELECT * FROM `$_ST[errors]` ORDER BY `messAdded` DESC LIMIT $start," . 
 " $per_page";
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select the' . 
  ' error logs from the database.', true, $select);
 }
 $count = $scorpions->counts($select, 1);
 
 if($count->rows > 0) {
?>
<form action="errors.php" method="post">
<table id="index">
<thead><tr>
 <th>&#160;</th>
 <th>ID</th>
 <th>Category</th>
 <th>Details</th>
 <th>Added</th>
</tr></thead>
<tfoot>
 <tr>
  <td class="tr" style="width: 100px;">Browse by</td>
  <td class="categories" colspan="4">
   <?php echo $leopards->buildErrorLog(); ?>
  </td>
 </tr>
 <tr>
  <td class="tr" style="width: 100px;">Action</td>
  <td class="categories" colspan="4">
   <input name="action" class="input5" type="submit" value="View"> 
   <input name="action" class="input5" type="submit" value="Delete">
  </td>
 </tr>
</tfoot>
<?php  
  while($getItem = $scorpions->obj($true, 0)) {
	 $class = '';
	 $details = '';
	 if($getItem->messType == 'Join Error' || $getItem->messType == 'Update Error') {
	  $class = 'forms';
		$text = explode("\n", $getItem->messText);
		$details = $getItem->messType . ': ' . str_replace('Name: ', '', $text[0]) .
            ' (' . str_replace('E-Mail Address: ', '', $text[1]) . ')';
	 } elseif (strpos($getItem->messType, 'SPAM') !== false) {
	  $class = 'spam';
		$text = explode("\n", $getItem->messText);
		$subj = explode('(', $getItem->messType);
		$details = str_replace('SPAM Error: ', '', trim($subj[0])) .
            ': ' . str_replace('Name: ', '', $text[0]) .
            ' (' . str_replace('E-Mail Address: ', '', trim($text[1])) . ')';
	 } elseif (strpos($getItem->messType, 'User Log-In') !== false) {
	  $class = 'user';
		$text = explode("\n", $getItem->messText);
		$details = str_replace('SPAM Error: ', '', str_replace('Failed ', '', $getItem->messType)) .
            ': ' . str_replace('Username: ', '', $text[0]);
	 }
?>
<tbody class="<?php echo $class; ?>"><tr>
 <td class="tc"><input name="log[]" class="input3" type="checkbox" value="<?php echo $getItem->messID; ?>"></td>
 <td class="tc"><?php echo $getItem->messID; ?></td>
 <td class="tc"><?php echo $get_errors_array[$class]; ?></td>
 <td class="tc"><?php echo $details; ?></td>
 <td class="tc"><?php echo date($seahorses->getTemplate('date_template'), strtotime($getItem->messAdded)); ?></td>
</tr></tbody>
<?php 
	}
?>
</table>
</form>
<?php 
 } 
}

require('footer.php');
