<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com> 
 * @file       <updates.php> 
 * @since      September 2nd, 2010 
 * @version    2.1.4  
 */ 
$getTitle = 'Updates';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g']) || 
(isset($_GET['g']) && preg_match('/^(search)([A-Za-z]+)/', $_GET['g'])) ?
    '<span><a href="updates.php?g=new">Add Update</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if($seahorses->getOption('updates_opt') == 'y') {
 if(isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<form action="updates.php" enctype="multipart/form-data" method="post">

<div id="updatesFloatRight">
<h3>Listings</h3>
<menu class="categoriesList selectAll">
 <li class="select" id="select_all"><a href="#">Select All</a></li>
 <li class="select" id="select_none"><a href="#">Select None</a></li>
<?php 
$select = "SELECT * FROM `$_ST[main]` WHERE `status` = '0' ORDER BY `subject` ASC";
$true = $scorpions->query($select);
if($true == false) { 
 echo " <li>No Listings Available</li>\n";
}

else {
 echo " <li><input name=\"listing[]\" class=\"input3\" type=\"checkbox\" value=\"0\"> Whole Collective</li>\n";
 while($getListing = $scorpions->obj($true)) {
  echo ' <li><input name="listing[]" class="input3" type="checkbox"' .
      ' value="' . $getListing->id . '"> ' . $getListing->subject . "</li>\n";
 }
}
?>
</menu>
</div>

<div id="updatesFloatLeft">
<fieldset>
 <legend>Details</legend>
 <input name="numeric[]" type="hidden" value="<?php echo $i; ?>">
 <p><label><strong>Title:</strong></label> <input name="title" class="input1" type="text"></p>
 <p><label><strong>Entry Status:</strong></label> 
 <input name="status" checked="checked" class="input3" type="radio" value="0"> Published
 <input name="status" class="input3" type="radio" value="1"> Draft</p>
 <p><label><strong>Disabled comments on this entry?</strong></label> 
  <input name="disabled" checked="checked" class="input3" type="radio" value="0"> Yes
  <input name="disabled" class="input3" type="radio" value="1"> No
 </p>
</fieldset>

<fieldset>
 <legend>Entry</legend>
 <p class="tc"><textarea name="entry" cols="60" rows="13" style="height: 250px; margin: 0 1% 0 0; width: 99%;"></textarea></p>
 <fieldset class="crossposts"> 
  <legend>Cross-Post</legend>
	<p class="noteButton">Click the title of each journal you'd like to crosspost to for additional options. Also, please be aware they
	you <strong>must</strong> check the journal in order for the additional options to be saved. Additional options are optional.</p>
	<div id="cp"><label><strong>Journals:</strong></label> <div style="float: left; width: 49%;">
	<input name="crosspost[]" class="input3" id="dw" type="checkbox" value="dw"> 
	<span id="DW">Dreamwidth</span><br>
	<div class="dw" id="toggleDW" style="display: none;">
	 <p><label><strong>Community:</strong></label> 
   <input name="dw-community" class="input1" type="text"></p>
	 <p><label><strong>Tags:</strong></label> 
   <input name="dw-tags" class="input1" type="text"></p>
	 <p><label>
    <strong>Userpic:</strong><br>
    Type in the keyword of your preferred userpic
   </label> 
   <input name="dw-user" class="input1" type="text"></p>
	 <p style="clear: both; margin: 0 0 2% 0;"></p>
	</div>
	<input name="crosspost[]" class="input3" id="ij" type="checkbox" value="ij"> 
	<span id="IJ">InsaneJournal</span><br>
	<div class="ij" id="toggleIJ" style="display: none;">
	 <p><label><strong>Community:</strong></label> 
   <input name="ij-community" class="input1" type="text"></p>
	 <p><label><strong>Tags:</strong></label> 
   <input name="ij-tags" class="input1" type="text"></p>
	 <p><label>
     <strong>Userpic:</strong><br>Type in the keyword of your preferred userpic
    </label> <input name="ij-user" class="input1" type="text"></p>
	 <p style="clear: both; margin: 0 0 2% 0;"></p>
	</div>
	<input name="crosspost[]" class="LJ" id="lj" type="checkbox" value="lj"> 
	<span id="LJ">LiveJournal</span>
	<div class="lj" id="toggleLJ" style="display: none;">
	 <p><label><strong>Community:</strong></label> 
   <input name="lj-community" class="input1" type="text"></p>
	 <p><label><strong>Tags:</strong></label> 
   <input name="lj-tags" class="input1" type="text"></p>
	 <p><label>
    <strong>Userpic:</strong><br>
    Type in the keyword of your preferred userpic)
   </label> 
   <input name="lj-user" class="input1" type="text"></p>
	 <p style="clear: both; margin: 0 0 2% 0;"></p>
	</div>
	</div>
	</div>
 </fieldset>
</fieldset>

<fieldset>
 <legend>Date</legend>
 <p><label><strong>Month:</strong></label> <select name="month" class="input1">
<?php 
 $dateArray = $get_date_array;
 foreach($dateArray as $dA => $dA2) {
  echo '  <option value="' . $dA . '"';
  if($dA == date('m')) {
   echo ' selected="selected"';
  }
  echo '>' . $dA2 . "</option>\n";
 }
?>
 </select></p>
 <p><label><strong>Day:</strong></label> 
 <input name="day" class="input1" type="text" value="<?php echo date('d'); ?>"></p>
 <p><label><strong>Year:</strong></label> 
 <input name="year" class="input1" type="text" value="<?php echo date('Y'); ?>"></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Update"> 
  <input class="input2" type="reset" value="Reset">
 </p>
</fieldset>
</div>
</form>
<?php 
 }
 
 elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
  $id = $tigers->cleanMys($_GET['d']);
  if(empty($id) || !is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	 ' you selected an incorrect update or you\'re trying to access something' . 
	 ' that doesn\'t exist. Go back and try again.', false);
  }

  $select = "SELECT * FROM `$_ST[updates]` WHERE `uID` = '$id' LIMIT 1";
  $true   = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'Unable to select that' . 
	 ' specific listing.|Make sure the ID is not empty and the updates table' . 
	 ' exists.', true, $select);
  }
  $getItem = $scorpions->obj($true);

  $dw = $getItem->uDW == 'y' ? 'checked="checked"' : '';
  $dwc = explode('|', $getItem->uDWOpt);
  $ij = $getItem->uIJ == 'y' ? 'checked="checked"' : '';
  $ijc = explode('|', $getItem->uIJOpt);
  $lj = $getItem->uLJ == 'y' ? 'checked="checked"' : '';
  $ljc = explode('|', $getItem->uLJOpt);
?>
<form action="updates.php" enctype="multipart/form-data" method="post">
<input name="id" type="hidden" value="<?php echo $id; ?>">
<input name="dwitemid" type="hidden" value="<?php echo str_replace('itemid:', '', $dwc[1]); ?>">
<input name="ijitemid" type="hidden" value="<?php echo str_replace('itemid:', '', $ijc[1]); ?>">
<input name="ljitemid" type="hidden" value="<?php echo str_replace('itemid:', '', $ljc[1]); ?>">

<div id="updatesFloatRight">
<h3>Listings</h3>
<menu class="categoriesList selectAll">
 <li class="select" id="select_all"><a href="#">Select All</a></li>
 <li class="select" id="select_none"><a href="#">Select None</a></li>
<?php 
 $listings = explode('!', $getItem->uCategory);
 $select   = "SELECT * FROM `$_ST[main]` WHERE `status` = '0' ORDER BY `subject` ASC";
 $true     = $scorpions->query($select);
 if($true == false) { 
  echo "<li>No Listings Available</li>\n";
 }

 else {
  echo ' <li><input name="listing[]"';
  if(in_array('0', $listings)) {
	 echo ' checked="checked"';
  }
  echo " class=\"input3\" type=\"checkbox\" value=\"0\"> Whole Collective</li>\n";
  while($getListing = $scorpions->obj($true)) {
   echo ' <li><input name="listing[]"';
	 if(in_array($getListing->id, $listings)) {
	  echo ' checked="checked"';
	 }
	 echo ' class="input3" type="checkbox" value="' . $getListing->id .
         '"> ' . $getListing->subject . "</li>\n";
  }
 }
?>
</menu>
</div>

<div id="updatesFloatLeft">
<fieldset>
 <legend>Details</legend>
 <p><label><strong>Title:</strong></label> 
 <input name="title" class="input1" type="text" value="<?php echo $getItem->uTitle; ?>"></p>
 <p><label><strong>Entry Status:</strong></label> 
<?php 
 $estatus = explode('!', $getItem->uPending);
 $statuses = array('0' => 'Published', '1' => 'Draft');
 foreach($statuses as $s1 => $s2) {
  echo '  <input name="status"';
  if(in_array($s1, $estatus)) {
   echo ' checked="checked"';
  }
  echo ' class="input3" type="radio" value="' . $s1 . '"> ' . $s2 . "\n";
 }
?>
 </p>
 <p><label><strong>Disable comments on this entry?</strong></label> 
<?php 
 $edisable = explode('!', $getItem->uDisabled);
 $disabled = array('0' => 'Yes', '1' => 'No');
 foreach($disabled as $d1 => $d2) {
  echo '  <input name="disabled"';
  if(in_array($d1, $edisable)) {
   echo ' checked="checked"';
  }
  echo ' class="input3" type="radio" value="' . $d1 . '"> ' . $d2 . "\n";
 }
?>
 </p>
</fieldset>

<fieldset>
 <legend>Entry</legend>
 <p class="tc">
  <textarea name="entry" cols="70" rows="15" style="height: 250px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->uEntry; ?>
  </textarea>
 </p>
 <fieldset> 
<?php  
 $showjournal = (object) array(
  'dw' => ($getItem->uDW == 'y' ? ' style="display: block;"' : ' style="display: none;"'),
  'ij' => ($getItem->uIJ == 'y' ? ' style="display: block;"' : ' style="display: none;"'),
  'lj' => ($getItem->uLJ == 'y' ? ' style="display: block;"' : ' style="display: none;"')
 );
?>
  <legend>Cross-Post</legend>
	<p class="noteButton">Click the title of each journal you'd like to crosspost 
  to for additional options. Also, please be aware they you <strong>must</strong> 
  check the journal in order for the additional options to be saved. Additional 
  options are optional.</p>
	<div id="cp"><label><strong>Journals:</strong></label> <div style="float: left; width: 49%;">
	<input name="crosspost[]" <?php echo $dw; ?> class="input3" id="dw" type="checkbox" value="dw"> 
	<span class="DW">Dreamwidth</span><br>
	<div class="dw" id="toggleDW"<?php echo $showjournal->dw; ?>>
	 <p><label><strong>Community:</strong></label> 
   <input name="dw-community" class="input1" type="text" 
   value="<?php echo str_replace('community:', '', $dwc[2]); ?>"></p>
	 <p><label><strong>Tags:</strong></label> 
   <input name="dw-tags" class="input1" type="text" 
   value="<?php echo str_replace('tags:', '', $dwc[3]); ?>"></p>
	 <p><label><strong>Userpic:</strong><br>
   Type in the keyword of your preferred userpic</label> 
	 <input name="dw-user" class="input1" type="text" value="<?php echo str_replace('userpic:', '', $dwc[4]); ?>"></p>
	 <p style="clear: both; margin: 0 0 2% 0;"></p>
	</div>
	<input name="crosspost[]" <?php echo $ij; ?> class="input3" id="ij" type="checkbox" value="ij"> 
	<span class="IJ">InsaneJournal</span><br>
	<div class="ij" id="toggleIJ"<?php echo $showjournal->ij; ?>>
	 <p><label><strong>Community:</strong></label> 
   <input name="ij-community" class="input1" type="text" 
   value="<?php echo str_replace('community:', '', $ijc[2]); ?>"></p>
	 <p><label><strong>Tags:</strong></label> 
   <input name="ij-tags" class="input1" type="text" 
   value="<?php echo str_replace('tags:', '', $ijc[3]); ?>"></p>
	 <p><label>
    <strong>Userpic:</strong><br>
    Type in the keyword of your preferred userpic
   </label> 
	 <input name="ij-user" class="input1" type="text" 
   value="<?php echo str_replace('userpic:', '', $ijc[4]); ?>"></p>
	 <p style="clear: both; margin: 0 0 2% 0;"></p>
	</div>
	<input name="crosspost[]" <?php echo $lj; ?> class="input3" id="lj" type="checkbox" value="lj"> 
	<span class="LJ">LiveJournal</span>
	<div class="lj" id="toggleLJ"<?php echo $showjournal->lj; ?>>
	 <p><label><strong>Community:</strong></label> 
   <input name="lj-community" class="input1" type="text" 
   value="<?php echo str_replace('community:', '', $ljc[2]); ?>"></p>
	 <p><label><strong>Tags:</strong></label> <input name="lj-tags" class="input1" type="text" 
   value="<?php echo str_replace('tags:', '', $ljc[3]); ?>"></p>
	 <p>
    <label>
     <strong>Userpic:</strong><br>
     Type in the keyword of your preferred userpic)
    </label> 
	  <input name="lj-user" class="input1" type="text" value="<?php echo str_replace('userpic:', '', $ljc[4]); ?>">
   </p>
	 <p style="clear: both; margin: 0 0 2% 0;"></p>
	</div>
	</div>
	</div>
 </fieldset>
</fieldset>

<fieldset>
 <legend>Date</legend>
 <p><label><strong>Month:</strong></label> <select name="month" class="input1">
<?php 
 $dateArray = $get_date_array;
 $dateNow1 = date('m', strtotime($getItem->uAdded));
 $dateNow2 = explode('!', $dateNow1);
 foreach($dateArray as $dA => $dA2) {
  echo '<option value="' . $dA . '"';
  if(in_array($dA, $dateNow2)) { 
   echo ' selected="selected"';
  }
  echo '>' . $dA2 . "</option>\n";
 }
?>
 </select></p>
 <p><label><strong>Day:</strong></label> 
 <input name="day" class="input1" type="text" value="<?php echo date('d', strtotime($getItem->uAdded)); ?>"></p>
 <p><label><strong>Year:</strong></label> 
 <input name="year" class="input1" type="text" value="<?php echo date('Y', strtotime($getItem->uAdded)); ?>"></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Edit Update"> 
  <input name="action" class="input2" type="submit" value="Delete Update"> 
  <input class="input2" type="reset" value="Reset">
 </p>
</fieldset>
</div>
</form>
<?php 
 }

 elseif (isset($_POST['action'])) {
  if(isset($_POST['action']) && $_POST['action'] == 'Add Update') {
   $grabid = $turtles->grabID() + 1;
   $title = $tigers->cleanMys($_POST['title']);
   if(empty($title)) {
    $tigers->displayError('Form Error', 'The <samp>title</samp> is empty.', false);
   }
   $disable = $tigers->cleanMys((int)$_POST['disabled']);
   if(!is_numeric($disable)) {
    $tigers->displayError('Form Error', 'The <samp>disable comments</samp> field' . 
    ' is not a number.', false);
   } elseif (strlen($disable) > 1) {
    $tigers->displayError('Form Error', 'The <samp>disable comments</samp> field' . 
    ' must not exceed 1.', false);
   }
   $status = $tigers->cleanMys($_POST['status']);
   if(!is_numeric($status)) {
    $tigers->displayError('Form Error', 'The <samp>status</samp> field is not a' . 
    ' number.', false);
   } elseif (strlen($status) > 1) {
    $tigers->displayError('Form Error', 'The <samp>status</samp> field must not' . 
    ' exceed 1.', false);
   }
   if($_POST['listing'] != 0) {
    $listing = $_POST['listing'];
	  $listing = array_map(array($tigers, 'cleanMys'), $listing);
	  $list = implode('!', $listing);
    $list = '!' . trim($list, '!') . '!';
   } else {
	  $list = '!0!';
	 }
   $entry = $tigers->cleanMys($_POST['entry'], 'n');
   if(empty($entry)) {
    $tigers->displayError('Form Error', 'The <samp>entry</samp> field isempty.', 
    false);
   }
   $year = $tigers->cleanMys($_POST['year'], 'y', 'n', 'n');
   $month = $tigers->cleanMys($_POST['month'], 'y', 'n', 'n');
   $day = $tigers->cleanMys($_POST['day'], 'y', 'n', 'n');
   if(empty($year) || empty($month) || empty($day)) {
    $tigers->displayError('Form Error', 'The <samp>date</samp> field is' . 
		' empty.', false);
   } elseif (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
    $tigers->displayError('Form Error', 'The <samp>date</samp> field is' . 
		' not digits.', false);
   } elseif (strlen($year) > 4) {
    $tigers->displayError('Form Error', 'The <samp>year</samp> field' . 
		' needs to be the length of 4 digits.', false);
   } elseif (strlen($month) > 2 || strlen($day) > 2) {
    $tigers->displayError('Form Error', 'The <samp>month or day</samp>' . 
		' field needs to be the length of 2 digits.', false);
   }
   $date = $tigers->cleanMys($year . '-' . $month . '-' . $day) . ' ' . date('H:i:s');
	
   /** 
    * Start crosslisting stuff! 
    */  
   $dw_post = 'n';
   $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
   $ij_post = 'n';
   $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
   $lj_post = 'n';
   $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
   if(isset($_POST['crosspost']) && !empty($_POST['crosspost']) && (is_countable($_POST['crosspost']) ? count($_POST['crosspost']) : 0) > 0) {
    $crosspost = $_POST['crosspost'];
	  $crosspost = array_map(array($tigers, 'cleanMys'), $crosspost);
    if(in_array('dw', $crosspost)) {
	   $dw_post = 'y';
	   $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	   $dw_comm = $tigers->cleanMys($_POST['dw-community']);
	   $dw_comm_if = !empty($dw_comm) ? $dw_comm : '';
	   $dw_priv = $tigers->cleanMys($_POST['dw-priv']);
	   $dw_tags = $tigers->cleanMys($_POST['dw-tags']);
	   $dw_user = $tigers->cleanMys($_POST['dw-user']);
	   $dw = new crosspost(
		  $seahorses->getOption('updates_crosspost_dw_user'), 
		  $seahorses->getOption('updates_crosspost_dw_pass'), 
			$journals->dw, 
			$dw_comm_if
		 );
	   $data = array();
	   $data['event'] = $turtles->formatEntry('dw', $turtles->cleanLJ($_POST['entry'], 'n', 'n'), $grabid, $disable);
	   $data['subject'] = $title;
	   $data['year'] = $year;
	   $data['month'] = $month;
	   $data['day'] = $day;
	   $data['hour'] = date('H');
	   $data['min'] = date('i');
	   if(isset($dw_priv) && $dw_priv == 'y') {
	    $data['security'] = 'usemask';
	   } else {
	    $data['security'] = 'public';
	   }
     $comment = $disable == 0 ? 1 : 0;
	   $meta = array( 
	    'opt_nocomments' => $comment,
		  'opt_preformatted' => true,
	   );
	   if(!empty($dw_tags)) {
	    $meta['taglist'] = $dw_tags;
	   }
	   if(!empty($dw_user)) {
	    $meta['picture_keyword'] = $dw_user;
	   }
	   if(date('Y') != $year || date('m') != $month || date('d') != $day) {
	    $meta['opt_backdated'] = true;
	   } 
	   $w = $dw->postevent($data, $meta);
	   if($w[0] == TRUE) {
	    $dw_post = 'y';
		  $dw_post_opt = '|itemid:' . $w[1]['itemid'] . "|community:{$dw_comm_if}|tags:{$dw_tags}|userpic:{$dw_user}|";
	   } else {
	    $dw_post = 'n';
	    echo '<p class="errorButton"><span class="error">Script Error:</span> The' . 
      " script was unable to add the <strong>$title</strong> entry to Dreamwidth!" . 
      "</p>\n";
	   }
	  } elseif (!in_array('dw', $crosspost)) {
	   $dw_post = 'n';
	   $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	  }
    if(in_array('ij', $crosspost)) {
	   $ij_post = 'y';
	   $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	   $ij_comm = $tigers->cleanMys($_POST['ij-community']);
	   $ij_comm_if = !empty($ij_comm) ? $ij_comm : '';
	   $ij_priv = $tigers->cleanMys($_POST['ij-priv']);
	   $ij_tags = $tigers->cleanMys($_POST['ij-tags']);
	   $ij_user = $tigers->cleanMys($_POST['ij-user']);
	   $ij = new crosspost(
		  $seahorses->getOption('updates_crosspost_ij_user'), 
			$seahorses->getOption('updates_crosspost_ij_pass'), 
			$journals->ij, 
			$ij_comm_if
		 );
	   $data = array();
	   $data['event'] = $turtles->formatEntry('ij', $turtles->cleanLJ($_POST['entry'], 'n', 'n'), $grabid, $disable);
	   $data['subject'] = $title;
	   $data['year'] = $year;
	   $data['month'] = $month;
	   $data['day'] = $day;
	   $data['hour'] = date('H');
	   $data['min'] = date('i');
	   if(isset($ij_priv) && $ij_priv == 'y') {
	    $data['security'] = 'usemask';
	   } else {
	    $data['security'] = 'public';
	   }
     $comment = $disable == 0 ? 1 : 0;
	   $meta = array( 
	    'opt_nocomments' => $comment,
		  'opt_preformatted' => true,
	   );
	   if(!empty($ij_tags)) {
	    $meta['taglist'] = $ij_tags;
	   }
	   if(!empty($ij_user)) {
	    $meta['picture_keyword'] = $ij_user;
	   } 
	   if(date('Y') != $year || date('m') != $month || date('d') != $day) {
	    $meta['opt_backdated'] = true;
	   } 
	   $w = $ij->postevent($data, $meta);
	   if($w[0] == TRUE) {
	    $ij_post = 'y';
		  $ij_post_opt = '|itemid:' . $w[1]['itemid'] . "|community:{$ij_comm_if}|tags:{$ij_tags}|userpic:{$ij_user}|";
	   } else {
	    $ij_post = 'n';
		  $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	    echo '<p class="errorButton"><span class="error">Script Error:</span> The' . 
      " script was unable to add the <strong>$title</strong> entry to InsaneJou" . 
      "rnal!</p>\n";
	   }
	  } elseif (!in_array('ij', $crosspost)) {
	   $ij_post = 'n';
	   $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	  }
	  if(in_array('lj', $crosspost)) {
	   $lj_post = 'y';
	   $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
	   $lj_comm = $tigers->cleanMys($_POST['lj-community']);
	   $lj_comm_if = !empty($lj_comm) ? $lj_comm : '';
	   $lj_priv = $tigers->cleanMys($_POST['lj-priv']);
	   $lj_tags = $tigers->cleanMys($_POST['lj-tags']);
	   $lj_user = $tigers->cleanMys($_POST['lj-user']);
	   $lj = new crosspost(
		  $seahorses->getOption('updates_crosspost_lj_user'), 
			$seahorses->getOption('updates_crosspost_lj_pass'), 
			$journals->lj, 
			$lj_comm_if
		 );
	   $data = array();
	   $data['event'] = $turtles->formatEntry('lj', $turtles->cleanLJ($_POST['entry'], 'n', 'n'), $grabid, $disable);
	   $data['subject'] = $title;
	   $data['year'] = $year;
	   $data['month'] = $month;
	   $data['day'] = $day;
	   $data['hour'] = date('H');
	   $data['min'] = date('i');
	   if(isset($lj_priv) && $lj_priv == 'y') {
	    $data['security'] = 'usemask';
	   } else {
	    $data['security'] = 'public';
	   }
     $comment = $disable == 0 ? 1 : 0;
	   $meta = array( 
	    'opt_nocomments' => $comment,
		  'opt_preformatted' => true,
	   );
	   if(!empty($lj_tags)) {
	    $meta['taglist'] = $lj_tags;
	   }
	   if(!empty($lj_user)) {
	    $meta['picture_keyword'] = $lj_user;
	   } 
	   if(date('Y') != $year || date('m') != $month || date('d') != $day) {
	    $meta['opt_backdated'] = true;
	   } 
	   $w = $lj->postevent($data, $meta);
	   if($w[0] == TRUE) {
	    $lj_post = 'y';
		  $lj_post_opt = '|itemid:' . $w[1]['itemid'] . "|community:{$lj_comm_if}|tags:{$lj_tags}|userpic:{$lj_user}|";
	   } else {
	    $lj_post = 'n';
		  $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
	    echo '<p class="errorButton"><span class="error">Script Error:</span> The' . 
      " script was unable to add the <strong>$title</strong> entry to LiveJourn" . 
      "al!</p>\n";
      echo '<p>Server Error: ' . $w[1] . ' (' . $w[2] . ")</p>\n";
	   }
	  } elseif (!in_array('lj', $crosspost)) {
	   $lj_post = 'n';
	   $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
	  }
   } elseif (!isset($_POST['crosspost']) || empty($_POST['crosspost']) && (is_countable($_POST['crosspost']) ? count($_POST['crosspost']) : 0) < 0) {
    $dw_post = 'n';
	  $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $ij_post = 'n';
	  $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $lj_post = 'n';
	  $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
   } else {
    $dw_post = 'n';
	  $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $ij_post = 'n';
	  $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $lj_post = 'n';
	  $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
   }
  
   /** 
    * Add entry \o/ 
    */ 
   $insert = "INSERT INTO `$_ST[updates]` (`uTitle`, `uCategory`, `uEntry`," .
       ' `uDW`, `uDWOpt`, `uIJ`, `uIJOpt`, `uLJ`, `uLJOpt`, `uPending`, `uDisabled`,' .
	 " `uAdded`) VALUES ('$title', '$list', '$entry', '$dw_post', '$dw_post_opt'," . 
	 " '$ij_post', '$ij_post_opt', '$lj_post', '$lj_post_opt', '$status'," . 
	 " '$disable', '$date')";
   $scorpions->query("SET NAMES 'utf8';");
   $true = $scorpions->query($insert);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to add' . 
		' the <strong>' . $title . '</strong> entry to the database.|Make sure your' . 
		' table exists.', true, $insert);
   } elseif ($true == true) {
    echo '<p class="successButton"><span class="success">Success!</span> Your' . 
		' <samp>' . $title . "</samp> entry was added to the database! :D</p>\n";
	  if($dw_post == 'y') {
	   echo '<p class="successButton"><span class="success">Success!</span> Your' . 
		 ' <samp>' . $title . '</samp> entry was crossposted to <strong>Dreamwidth' .
		 "</strong>! :D</p>\n";
	  } if($ij_post == 'y') {
	   echo '<p class="successButton"><span class="success">Success!</span> Your' . 
		 ' <samp>' . $title . '</samp> entry was crossposted to <strong>Insane' .
		 "Journal</strong>! :D</p>\n";
	  } if($lj_post == 'y') {
	   echo '<p class="successButton"><span class="success">Success!</span> Your' . 
		 ' <samp>' . $title . '</samp> entry was crossposted to <strong>Live' .
		 "Journal</strong>! :D</p>\n";
	  }
   }
   echo $tigers->backLink('updates');
  }
 
  /** 
   * Edit or delete an update :D 
   */ 
  elseif (
	 (isset($_POST['action']) && $_POST['action'] == 'Edit Update') || 
	 (isset($_POST['action']) && $_POST['action'] == 'Delete Update')
	) { 
   $id = $tigers->cleanMys($_POST['id']);
   if(empty($id) || !is_numeric($id)) {
    $tigers->displayError('Form Error', 'Your ID is empty. This means' . 
		' you selected an incorrect update or you\'re trying to access something' . 
		' that doesn\'t exist. Go back and try again.', false);
   }
   $dwitemid = $tigers->cleanMys((int)$_POST['dwitemid']);
   $ijitemid = $tigers->cleanMys((int)$_POST['ijitemid']);
   $ljitemid = $tigers->cleanMys((int)$_POST['ljitemid']);
   $title = $tigers->cleanMys($_POST['title']);
   if(empty($title)) {
    $tigers->displayError('Form Error', 'The <samp>title</samp> is empty.', false);
   }
   $disable = $tigers->cleanMys($_POST['disabled']);
   if(!is_numeric($disable)) {
    $tigers->displayError('Form Error', 'The <samp>disable comments' . 
		'</samp> field is not a number.', false);
   } elseif (strlen($disable) > 1) {
    $tigers->displayError('Form Error', 'The <samp>disable comments' . 
		'</samp> field must not exceed 1.', false);
   }
   $status = $tigers->cleanMys($_POST['status']);
   if(!is_numeric($status)) {
    $tigers->displayError('Form Error', 'The <samp>status</samp> field' . 
		' is not a number.', false);
   } elseif (strlen($status) > 1) {
    $tigers->displayError('Form Error', 'The <samp>status</samp> field' . 
		' must not exceed 1.', false);
   }
   if($_POST['listing'] != 0) {
    $listing = $_POST['listing'];
	  $listing = array_map(array($tigers, 'cleanMys'), $listing);
	  $list = implode('!', $listing);
    $list = '!' . trim($list, '!') . '!';
   } else {
	  $list = '!0!';
   }
   $entry = $tigers->cleanMys($_POST['entry'], 'n');
   if(empty($entry)) {
    $tigers->displayError('Form Error', 'The <samp>entry</samp> is empty.', false);
   }
   $year = $tigers->cleanMys($_POST['year'], 'y', 'n', 'n');
   $month = $tigers->cleanMys($_POST['month'], 'y', 'n', 'n');
   $day = $tigers->cleanMys($_POST['day'], 'y', 'n', 'n');
   if(empty($year) || empty($month) || empty($day)) {
    $tigers->displayError('Form Error', 'The <samp>date</samp> field is' . 
		' empty.', false);
   } elseif (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
    $tigers->displayError('Form Error', 'The <samp>date</samp> field is' . 
		' not digits.', false);
   } elseif (strlen($year) > 4) {
    $tigers->displayError('Form Error', 'The <samp>year</samp> field' . 
		' needs to be the length of 4 digits.', false);
   } elseif (strlen($month) > 2 || strlen($day) > 2) {
    $tigers->displayError('Form Error', 'The <samp>month or day</samp>' . 
		' field needs to be the length of 2 digits.', false);
   }
   $date = $tigers->cleanMys($year . '-' . $month . '-' . $day) . ' ' . date('H:i:s');
	
   /** 
    * Get crosslisting shit~ 
    */ 
   $dw_post = 'n';
   $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
   $ij_post = 'n';
   $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
   $lj_post = 'n';
   $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
   if(isset($_POST['crosspost']) && !empty($_POST['crosspost']) && (is_countable($_POST['crosspost']) ? count($_POST['crosspost']) : 0) > 0) {
       $crosspost = $_POST['crosspost'];
	  $crosspost = array_map(array($tigers, 'cleanMys'), $crosspost);
	  if(in_array('dw', $crosspost)) {
	   $dw_post = 'y';
	   $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	   $dw_comm = $tigers->cleanMys($_POST['dw-community']);
	   $dw_comm_if = !empty($dw_comm) ? $dw_comm : '';
	   $dw_priv = $tigers->cleanMys($_POST['dw-priv']);
	   $dw_tags = $tigers->cleanMys($_POST['dw-tags']);
	   $dw_user = $tigers->cleanMys($_POST['dw-user']);
	   $dw = new crosspost(
		  $seahorses->getOption('updates_crosspost_dw_user'), 
			$seahorses->getOption('updates_crosspost_dw_pass'), 
			$journals->dw, 
			$dw_comm_if
		 );
	   $data = array();
	   if(!empty($dwitemid)) {
	    $data['itemid'] = $dwitemid;
	   }
	   $data['event'] = $turtles->formatEntry('dw', $turtles->cleanLJ($_POST['entry'], 'n', 'n'), $id, $disable);
	   $data['subject'] = $title;
	   $data['year'] = $year;
	   $data['month'] = $month;
	   $data['day'] = $day;
	   $data['hour'] = date('H');
	   $data['min'] = date('i');
	   if(isset($dw_priv) && $dw_priv == 'y') {
	    $data['security'] = 'usemask';
	   } else {
	    $data['security'] = 'public';
	   }
     $comment = $disable == 0 ? 1 : 0;
	   $meta = array( 
	    'opt_nocomments' => $comment,
		  'opt_preformatted' => true,
	   );
	   if(!empty($dw_tags)) {
	    $meta['taglist'] = $dw_tags;
	   }
	   if(!empty($dw_user)) {
	    $meta['picture_keyword'] = $dw_user;
	   }
	   # -- Decide what we're doing, yah? --
	   if($_POST['action'] == 'Edit Update') {
	    if(empty($dwitemid)) {
		   $w = $dw->postevent($data, $meta);
	    } else {
		   $w = $dw->editevent($data, $meta);
	    }
	   } elseif ($_POST['action'] == 'Delete Update' && !empty($dwitemid)) {
	    $w = $dw->deleteevent($data);
	   }
	   if($w[0] == TRUE) {
	    $dw_post = 'y';
		  if(empty($dwitemid)) {
		   $dw_post_opt = '|itemid:' . $w[1]['itemid'] . "|community:{$dw_comm_if}|tags:{$dw_tags}|userpic:{$dw_user}|";
		  } else {
	     $dw_post_opt = "|itemid:{$dwitemid}|community:{$dw_comm_if}|tags:{$dw_tags}|userpic:{$dw_user}|";
		  }
	   } else {
	    $dw_post = 'n';
	    echo '<p class="errorButton"><span class="error">Script Error:</span>' . 
			" The script was unable to edit the <strong>$title</strong> entry on" . 
      " Dreamwidth!</p>\n";
	   }
	  } elseif (!in_array('dw', $crosspost)) {
	   $dw_post = 'n';
	   $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	  }
	  if(in_array('ij', $crosspost)) {
	   $ij_post = 'y';
	   $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	   $ij_comm = $tigers->cleanMys($_POST['ij-community']);
	   $ij_comm_if = !empty($ij_comm) ? $ij_comm : '';
	   $ij_priv = $tigers->cleanMys($_POST['ij-priv']);
	   $ij_tags = $tigers->cleanMys($_POST['ij-tags']);
	   $ij_user = $tigers->cleanMys($_POST['ij-user']);
	   $ij = new crosspost(
		  $seahorses->getOption('updates_crosspost_ij_user'), 
			$seahorses->getOption('updates_crosspost_ij_pass'), 
			$journals->ij, 
			$ij_comm_if
		 );
	   $data = array();
	   if(!empty($ijitemid)) {
	    $data['itemid'] = $ijitemid;
	   }
	   $data['event'] = $turtles->formatEntry('ij', $turtles->cleanLJ($_POST['entry'], 'n', 'n'), $id, $disable);
	   $data['subject'] = $title;
	   $data['year'] = $year;
	   $data['month'] = $month;
	   $data['day'] = $day;
	   $data['hour'] = date('H');
	   $data['min'] = date('i');
	   if(isset($ij_priv) && $ij_priv == 'y') {
	    $data['security'] = 'usemask';
	   } else {
	    $data['security'] = 'public';
	   }
     $comment = $disable == 0 ? 1 : 0;
	   $meta = array( 
	    'opt_nocomments' => $comment,
		  'opt_preformatted' => true,
	   );
	   if(!empty($ij_tags)) {
	    $meta['taglist'] = $ij_tags;
	   }
	   if(!empty($ij_user)) {
	    $meta['picture_keyword'] = $ij_user;
	   } 
	   if($_POST['action'] == 'Edit Update') {
	    if(empty($ijitemid)) {
		   $w = $ij->postevent($data, $meta);
	    } else {
		   $w = $ij->editevent($data, $meta);
	    }
	   } elseif ($_POST['action'] == 'Delete Update' && !empty($ijitemid)) {
	    $w = $ij->deleteevent($data);
	   }
	   if($w[0] == TRUE) {
	    $ij_post = 'y';
		  if(empty($ijitemid)) {
		   $ij_post_opt = '|itemid:' . $w[1]['itemid'] . "|community:{$ij_comm_if}|tags:{$ij_tags}|userpic:{$ij_user}|";
		  } else {
	     $ij_post_opt = "|itemid:{$ijitemid}|community:{$ij_comm_if}|tags:{$ij_tags}|userpic:{$ij_user}|";
		  }
	   } else {
	    $ij_post = 'n';
		  $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	    echo '<p class="errorButton"><span class="error">Script Error:</span>' . 
			' The script was unable to edit the <strong>' . $title . 
			"</strong> entry on InsaneJournal!</p>\n";
	   }
	  } elseif (!in_array('ij', $crosspost)) {
	   $ij_post = 'n';
	   $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	  }
	  if(in_array('lj', $crosspost)) {
	   $lj_post = 'y';
	   $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
	   $lj_comm = $tigers->cleanMys($_POST['lj-community']);
	   $lj_comm_if = !empty($lj_comm) ? $lj_comm : '';
	   $lj_priv = $tigers->cleanMys($_POST['lj-priv']);
	   $lj_tags = $tigers->cleanMys($_POST['lj-tags']);
	   $lj_user = $tigers->cleanMys($_POST['lj-user']);
	   $lj = new crosspost(
		  $seahorses->getOption('updates_crosspost_lj_user'), 
			$seahorses->getOption('updates_crosspost_lj_pass'), 
			$journals->lj, 
			$lj_comm_if
		 );
	   $data = array();
	   if(!empty($ljitemid)) {
	    $data['itemid'] = $ljitemid;
	   }
	   $data['event'] = $turtles->formatEntry('lj', $turtles->cleanLJ($_POST['entry'], 'n', 'n'), $id, $disable);
	   $data['subject'] = $title;
	   $data['year'] = $year;
	   $data['month'] = $month;
	   $data['day'] = $day;
	   $data['hour'] = date('H');
	   $data['min'] = date('i');
	   if(isset($lj_priv) && $lj_priv == 'y') {
	    $data['security'] = 'usemask';
	   } else {
	    $data['security'] = 'public';
	   }
     $comment = $disable == 0 ? 1 : 0;
	   $meta = array( 
	    'opt_nocomments' => $comment,
		  'opt_preformatted' => true,
	   );
	   if(!empty($lj_tags)) {
	    $meta['taglist'] = $lj_tags;
	   }
	   if(!empty($lj_user)) {
	    $meta['picture_keyword'] = $lj_user;
	   } 
	   # -- Decide what we're doing, yah? --
	   if($_POST['action'] == 'Edit Update') {
	    if(empty($ljitemid)) {
		   $w = $lj->postevent($data, $meta);
	    } else {
		   $w = $lj->editevent($data, $meta);
	    }
	   } elseif ($_POST['action'] == 'Delete Update' && !empty($ljitemid)) {
	    $w = $lj->deleteevent($data);
	   }
	   if($w[0] == TRUE) {
	    $lj_post = 'y';
		  if(empty($ljitemid)) {
		   $lj_post_opt = '|itemid:' . $w[1]['itemid'] . "|community:{$lj_comm_if}|tags:{$lj_tags}|userpic:{$lj_user}|";
		  } else {
	   	 $lj_post_opt = "|itemid:{$ljitemid}|community:{$lj_comm_if}|tags:{$lj_tags}|userpic:{$lj_user}|";
		  }
	   } else {
	    $lj_post = 'n';
		  $lj_post_opt = '|community:|tags:|userpic:|';
	    echo '<p class="errorButton"><span class="error">Script Error:</span>' . 
			' Unable to add the <strong>' . $title . 
			"</strong> entry to LiveJournal!</p>\n";
	   }
	  } elseif (!in_array('lj', $crosspost)) {
	   $lj_post = 'n';
	   $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
	  }
   } elseif (!isset($_POST['crosspost']) || empty($_POST['crosspost']) && (is_countable($_POST['crosspost']) ? count($_POST['crosspost']) : 0) < 0) {
    $dw_post = 'n';
	  $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $ij_post = 'n';
	  $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $lj_post = 'n';
	  $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
   } else {
    $dw_post = 'n';
	  $dw_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $ij_post = 'n';
	  $ij_post_opt = '|itemid:|community:|tags:|userpic:|';
	  $lj_post = 'n';
	  $lj_post_opt = '|itemid:|community:|tags:|userpic:|';
   }
 
   /** 
    * Format the edit and delete query and, depending on the user's choice, 
    * perform the action 
    */ 
   $update = "UPDATE `$_ST[updates]` SET `uTitle` = '$title', `uCategory` =" . 
	 " '$list', `uEntry` = '$entry', `uDW` = '$dw_post', `uDWOpt` = '$dw_post_opt'," . 
	 " `uIJ` = '$ij_post', `uIJOpt` = '$ij_post_opt', `uLJ` = '$lj_post', `uLJOpt`" . 
	 " = '$lj_post_opt', `uPending` = '$status', `uDisabled` = '$disable', `uAdded`" . 
	 " = '$date' WHERE `uID` = '$id' LIMIT 1";
   $delete = "DELETE FROM `$_ST[updates]` WHERE `uID` = '$id' LIMIT 1";
   if($_POST['action'] == 'Edit Update') {
	  $scorpions->query("SET NAMES 'utf8';");
	  $true = $scorpions->query($update);
    if($true == false) {
     $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' edit the <strong>' . $title . '</strong> entry.|Make sure your table' . 
		 ' exists.', true, $update);
    } elseif ($true == true) {
     echo $tigers->displaySuccess('Your <samp>' . $title . '</samp> entry was' .
         ' added to the database! :D');
	   if($dw_post == 'y') {
	    echo $tigers->displaySuccess('Your <samp>' . $title . '</samp> entry was' .
            ' crossposted to <strong>Dreamwidth</strong>!');
	   } if($ij_post == 'y') {
	    echo $tigers->displaySuccess('Your <samp>' . $title . '</samp> entry was' .
            ' crossposted to <strong>InsaneJournal</strong>! :D');
	   } if($lj_post == 'y') {
	    echo $tigers->displaySuccess('Your <samp>' . $title . '</samp> entry was' .
            ' crossposted to <strong>LiveJournal</strong>! :D');
	   }
    }
	 } elseif ($_POST['action'] == 'Delete Update') {
	  $true = $scorpions->query($delete);
    if($true == false) {
     $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' delete the <strong>' . $title . '</strong> entry.|Make sure your table' . 
		 ' exists.', true, $delete);
    } elseif ($true == true) {
     $tigers->displayError('Database Error', 'Your <samp>' . $title . '</samp> entry was' .
         ' deleted from the database! :D');
	   if($dw_post == 'y') {
	    $tigers->displayError('Database Error', 'Your <samp>' . $title . '</samp> entry was' .
            ' deleted from <strong>Dreamwidth</strong>! :D');
	   } if($ij_post == 'y') {
	    echo $tigers->displaySuccess('Your <samp>' . $title . '</samp> entry was' .
            ' deleted from <strong>InsaneJournal</strong>! :D');
	   } if($lj_post == 'y') {
	    echo $tigers->displaySuccess('Your <samp>' . $title . '</samp> entry was' .
            ' deleted from <strong>LiveJournal</strong>! :D');
	   }
    }
   }
	 if($_POST['action'] == 'Edit Update') {
	  echo $tigers->backLink('updates', $id);
	 }
   echo $tigers->backLink('updates');
  }

  /** 
   * Mass-delete comments 
   */ 
  elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
   if(empty($_POST['update'])) {
    $tigers->displayError('Script Error', 'You need to select a update (or' . 
	  ' two, etc.) in order to delete them.', false);
   }
 
   foreach($_POST['update'] as $pm) {
    $delete = "DELETE FROM `$_ST[updates]` WHERE `uID` = '$pm' LIMIT 1";
	  $true = $scorpions->query($delete);
	  if($true == true) {
	   echo $tigers->displaySuccess('The update was deleted!');
	  } 
   }
   echo $tigers->backLink('updates');
  }
 }
 
 else {
  $journalArray = array('dw', 'ij', 'lj');
  $journalArray2 = array('dw' => 'Dreamwidth', 'ij' => 'InsaneJournal', 'lj' => 'LiveJournal');
?>
<p>Welcome to <samp>updates.php</samp>, the page to add updates and edit or 
delete your current ones! Below is your list of updates. To edit or delete a 
current one, click "Edit" or "Delete" by the appropriate update.</p>
<?php 
  $selectList = "SELECT * FROM `$_ST[updates]`";
  if(isset($_GET['g']) && $_GET['g'] == 'searchListings') {
   $listingid   = $tigers->cleanMys($_GET['listing_id']);
   $selectList .= " `uCategory` LIKE '%!$listingid!%'";
  }
  $selectList .= " ORDER BY `uAdded` DESC LIMIT $start, $per_page";
  $trueList    = $scorpions->query($selectList);
  $count       = $scorpions->total($trueList);

  if($count > 0) {
?>
<form action="updates.php" method="get">
<input name="g" type="hidden" value="searchListings">

<fieldset>
 <legend>Search Listings</legend>
 <p><label><strong>Listing:</strong></label> <select name="listingid" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true   = $scorpions->query($select);
 if($true == false || $scorpions->total($true) == 0) {
  echo "  <option>No Listings Found</option>\n";
 }

 else {
  echo "  <option selected=\"selected\" value=\"0\">Whole Collective</option>\n";
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
  if(isset($listingid)) {
   echo '<h4>Searching the <em>' . $wolves->getSubject($listingid) . "</em> listing...</h4>\n";
  }
?>
<form action="updates.php" method="post">
<table class="index">
<thead><tr>
 <th>&#160;</th>
 <th>Title</th>
 <th>Listing(s)</th>
 <th>Date</th>
 <th>Action</th>
</tr></thead>
<tfoot><tr>
 <td class="tc" colspan="5">With Checked: <input name="action" class="input2" type="submit" value="Delete"></td>
</tr></tfoot>
<?php 
   while($getItem = $scorpions->obj($trueList)) {
	  $q = isset($_GET['listingid']) ? 'listing=' . $listingid . '&#38;' : '';
?>
<tbody><tr>
 <td class="tc"><input name="update[]" type="checkbox" value="<?php echo $getItem->uID; ?>"></td>
 <td class="tc"><?php echo $getItem->uTitle; ?></td>
 <td class="tc"><?php echo $wolves->pullSubjects($getItem->uCategory, '!'); ?></td>
 <td class="tc"><?php echo date($seahorses->getTemplate('date_template'), strtotime($getItem->uAdded)); ?></td>
 <td class="floatIcons tc">
  <a href="updates.php?g=old&#38;<?php echo $q; ?>d=<?php echo $getItem->uID; ?>">
   <img src="img/icons/edit.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php 
   } 
?>
</table>
</form>
<?php 
   echo "\n<p id=\"pagination\">Pages: ";
   $select = "SELECT * FROM `$_ST[updates]`";
   if(isset($_GET['g']) && $_GET['g'] == 'searchListings') {
    $select .= " WHERE `uCategory` LIKE '%!$listingid!%'";
   }
   $true  = $scorpions->query($select);
   $total = $scorpions->total($true);
   $pages = ceil($total / $per_page);
   
   for($i = 1; $i <= $pages; $i++) {
    if($page == $i) {
     echo $i . ' ';
    } else {
     $pg = '<a href="updates.php?';
     if(isset($_GET['g']) && $_GET['g'] == 'searchListings') {
      $pg .= 'g=searchListings&#38;listingid=' . $listingid . '&#38;';
     }
     $pg .= 'p=' . $i . '">' . $i . '</a> ';
     echo $pg;
    }
   }

   echo "</p>\n"; 
  } else {
   echo "<p class=\"tc\">Currently no updates!</p>\n";
  }
 }
} 

else {
?>
<p class="errorButton"><span class="error">ERROR:</span> You have turned off the 
<samp>updates</samp> feature. To install and activate this feature, visit the 
<a href="addons.php">&#171; addons page</a>!</p>
<?php 
}

require('footer.php');
