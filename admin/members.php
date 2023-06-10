<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <members.php>
 * @version          Robotess Fork
 */
use Robotess\StringUtils;

$getTitle = 'Members';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = isset($_GET['listing']) ? (!isset($_GET['g']) 
|| (isset($_GET['g']) && preg_match('/^(search)([A-Za-z]+)/', $_GET['g'])) ?
'<span><a href="members.php?listing=' . $tigers->cleanMys($_GET['listing']) .
'&#38;g=new">Add Member</a></span>' : '') : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) { 
 $start = 0;
} else {
 $start = $per_members * ($tigers->cleanMys($_GET['p']) - 1);
}
$ender = $start + $per_members;

if(isset($_GET['listing']) && is_numeric($_GET['listing'])) {
 $getlistingid = $tigers->cleanMys($_GET['listing']);
 echo '<h3>Members &#187; ' . $wolves->getSubject($getlistingid) . "</h3>\n";
 
 if(isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<p class="noteButton"><span class="note">Note:</span> This is only for adding a 
member without sending over information e-mails, such as the listing information 
and approval e-mails. If you'd like to send out these e-mails, use the join form 
of the listing you'd like the member added to.</p>

<form action="members.php?listing=<?php echo $getlistingid; ?>" enctype="multipart/form-data" method="post">
<fieldset>
 <legend>Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "<option value=\"0\">No Listings Available</option>\n";
 }

 else {
  while($getItem = $scorpions->obj($true, 0)) {
   echo '  <option value="' . $getItem->id . '"';
	 if($getItem->id == $getlistingid) {
	  echo ' selected="selecgted"';
	 }
	 echo '>' . $getItem->subject . "</option>\n"; 
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Member Details</legend>
 <p><label>* <strong>Name:</strong></label> <input name="name" class="input1" type="text" required="required"></p>
 <p><label>* <strong>E-Mail:</strong></label> <input name="email" class="input1" type="email" required="required"></p>
 <p><label><strong>URI:</strong></label> <input name="url" class="input1" type="url"></p>
 <p><label>* <strong>Country:</strong></label> <select name="country" class="input1" required="required">
<?php require('inc/countries.inc.php'); ?>
 </select></p>
 <p><label><strong>Show E-Mail:</strong></label> 
  <input name="visible" class="input3" type="radio" value="0"> Yes
  <input name="visible" checked="checked" class="input3" type="radio" value="1"> No
 </p>
</fieldset>

<fieldset>
 <legend>Password</legend>
 <p class="noteButton"><span class="note">Note:</span> Passwords are required to 
 update a member's information (if wanted); however, you can leave the fields 
 blank and have the script generate a 16 alphanumeric password for you.</p>
 <p><label style="float: left; padding: 0 1%; width: 48%;"><strong>Password</strong><br>
 Type twice for verification:</label> 
  <input name="password" class="input1" style="width: 48%;" type="password"><br>
  <input name="passwordv" class="input1" style="width: 48%;" type="password">
 </p>
</fieldset>

<fieldset>
 <legend>Fave Field Options</legend>
<?php
if(!isset($_GET['extend']) && !isset($_GET['count']) && !isset($_POST['fave'])) {
 $q = basename($_SERVER['PHP_SELF']) . '?g=new&#38;listing=' . $getlistingid .'&#38;extend=1&#38;count=1#fave';
 echo "<p class=\"tc\"><a href=\"$q\">Add Fave Field(s)?</a></p>\n";
}

if(isset($_GET['extend']) && is_numeric($_GET['extend'])) {
 $c1 = (int)$tigers->cleanMys($_GET['count']) + 1;
 $q2 = basename($_SERVER['PHP_SELF']) . '?g=new&#38;listing=' . $getlistingid .
 '&#38;extend=1&#38;count=' . $c1 . '#fave'; 
 $countQuery = $tigers->cleanMys((int)$_GET['count'], 'y', 'y', 'n');
 echo "<div id=\"fave\">\n";
 for($i = 0; $i < $countQuery; $i++) {
?>
 <p><label><strong>Fave Field <?php echo $i; ?>:</strong></label> 
 <input name="fave[]" class="input1" type="text"></p>
<?php
 }
 echo '<p class="noteButton tc"><span class="note">Note:</span> Adding' .
 ' another fave field will erase any data you may have entered in the field(s).' .
 "<br><a href=\"$q2\">Add Another Field?</a></p>\n";
 echo "</div>\n";
}
?>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc"><input name="action" class="input2" type="submit" value="Add Member"></p>
</fieldset>
</form>
<?php 
 }

 elseif (isset($_POST['action']) && $_POST['action'] == 'Add Member') {
  $listing = $tigers->cleanMys($_POST['listing']);
  $listingArray = $wolves->listingsList();
  if(empty($listing) || !is_numeric($listing) || !in_array($listing, $listingArray)) {
   $tigers->displayError('Form Error', 'In order to add the member to the' . 
	 ' members list, you need to choose a listing.', false);
  } 
  $name = $tigers->cleanMys($_POST['name']);
  if(empty($name)) { 
   $tigers->displayError('Form Error', 'The <samp>name</samp> field is' . 
  ' empty.', false);
  } elseif (strlen($name) > 20) {
   $tigers->displayError('Form Error', 'The <samp>name</samp> is too' . 
	 ' long. Go back and shorten it.', false);
  } 
$email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'Your <samp>e-mail</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>e-mail</samp> field are not allowed.', false);
        }
  $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
    $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
        ' not valid. Please supply a valid site URL or empty the field.', false);
}

  $country = $tigers->cleanMys($_POST['country']);
  if(empty($country)) { 
   $tigers->displayError('Form Error', 'The <samp>country</samp> field is' . 
   ' empty.', false);
  }
  $visible = $tigers->cleanMys($_POST['visible']);
  if(!is_numeric($visible) || $visible > 1 || strlen($visible) > 1) { 
   $tigers->displayError('Form Error', 'The <samp>visible</samp> field is' . 
   ' invalid.', false);
  }
  $password1 = $tigers->cleanMys($_POST['password']);
  $password2 = $tigers->cleanMys($_POST['passwordv']);
  if(!empty($password1)) {
   if(empty($password2)) {
    $tigers->displayError('Form Error', 'In order to verify the password,' . 
    ' you need to fill out both password fields or leave both empty.', false);
   } elseif ($password1 !== $password2) {
	  $tigers->displayError('Form Error', 'The passwords do not match.', false);
	 }
  } 
  if(empty($password1) && empty($password2)) {
   $hashy1 = substr(sha1(random_int(1990, 16770)), 0, 8);
	 $hashy2 = substr(sha1(date('YmdHis')), 0, 8);
	 $pass = $hashy1 . $hashy2;
  } else {
   $pass = $password1;
  }
  if(isset($_POST['fave'])) {
   $fields = $_POST['fave'];
   $fields = array_map(array($tigers, 'replaceArray'), $fields);
   if(!empty($fields)) { 
    $ff = implode('|', $fields);
    $ff = '|' . trim($ff, '|') . '|';
   } else {
    $dbentry = $wolves->getListings($listing, 'object'); 
	  if(empty($dbentry->fave_fields)) {
	   $ff = '';
	  } else {
     $dbadditional = explode('|', $dbentry->fave_fields);
     $dbfields = $tigers->emptyarray($dbadditional);
	   for($i = 0,$iMax = is_countable($dbfields) ? count($dbfields) : 0; $i < $iMax; $i++) {
	    $fave[] = 'NONE';
	   }
	   $ff = implode('|', $fave);
	   $ff = '|' . trim($ff, '|') . '|';
	  }
   }
  } else {
   $dbentry = $wolves->getListings($listing, 'object'); 
	 if(empty($dbentry->fave_fields)) {
	  $ff = '';
	 } else {
    $dbadditional = explode('|', $dbentry->fave_fields);
    $dbfields = $tigers->emptyarray($dbadditional);
	  for($i = 0,$iMax = is_countable($dbfields) ? count($dbfields) : 0; $i < $iMax; $i++) {
	   $fave[] = 'NONE';
	  }
	  $ff = implode('|', $fave);
	  $ff = '|' . trim($ff, '|') . '|';
	 }
  }

  $insert = "INSERT INTO `$_ST[members]` (`mEmail`, `fNiq`, `mName`, `mURL`," . 
	' `mCountry`, `mPassword`, `mExtra`, `mVisible`, `mPending`, `mUpdate`,' .
  " `mEdit`, `mAdd`) VALUES ('$email', '$listing', '$name', '$url', '$country'," . 
	" MD5('$pass'), '$ff', '$visible', '0', 'n', '1970-01-01 00:00:00', CURDATE())";
  $true = $scorpions->insert($insert);

  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to insert' . 
   ' the member.', true, $insert);
  } elseif ($true == true) {
   echo '<p class="successButton"><span class="success">SUCCESS!</span> The' .
   " member was added! :D</p>\n";
	 echo $tigers->backLink('mem', $getlistingid);
   echo $tigers->backLink('mem');
  }
 }

 /** 
  * Edit the member! 
  */ 
 elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
  $listing = $wolves->getListings($getlistingid, 'object');
  $id = $tigers->cleanMys($_GET['d']);
	$idtype = is_numeric($id) ? 'id' : 'email';
	if(is_numeric($id)) {
	 $getItem = $snakes->getMembers($id, 'id', 'object', $getlistingid);
	} else {
	 $getItem = $snakes->getMembers($id, 'email', 'object', $getlistingid);
	}
?>
<form action="members.php?listing=<?php echo $getlistingid; ?>" enctype="multipart/form-data" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->mID; ?>">
 <input name="idtype" type="hidden" value="<?php echo $idtype; ?>">
</p>

<fieldset>
 <legend>Changes/Actions</legend>
 <p>
  <label><strong>Update member date?</strong><br>
  Check the box next to "Yes" if you'd like the added date for the member 
	changed to the current date.
  </label> 
	<input name="update_date" checked="checked" class="input3" type="radio" value="y"> Yes
  <input name="update_date" class="input3" type="radio" value="n"> No
 </p>
 <p class="clear"></p>
 <p>
  <label><strong>Change member status?</strong><br>
  Check the box next to "Yes" if you'd the status changed (if the member is 
  pending, the member will be approved; if the member is approved, the member 
  will be set to pending).
  </label> 
  <input name="change_status" checked="checked" class="input3" type="radio" value="n"> No
  <input name="change_status" class="input3" type="radio" value="y"> Yes
 </p>
 <p class="clear"></p>
 <p>
  <label><strong>E-mail the recipient?</strong><br>
  Check the box next to "Yes" if you want to send the member an update notice.
  </label> 
  <input name="email_now" checked="checked" class="input3" type="radio" value="n"> No
  <input name="email_now" class="input3" type="radio" value="y"> Yes
 </p>
</fieldset>

<fieldset>
 <legend>Member Details</legend>
 <p><label><strong>Name:</strong></label> 
 <input name="name" class="input1" type="text" value="<?php echo $getItem->mName; ?>"></p>
 <p><label><strong>E-mail:</strong></label> 
 <input name="email" class="input1" type="email" value="<?php echo $getItem->mEmail; ?>"></p>
 <p><label><strong>URI:</strong></label> 
 <input name="url" class="input1" type="url" value="<?php echo $getItem->mURL; ?>"></p>
 <p><label><strong>Country:</strong></label> 
 <input name="country" class="input1" type="text" value="<?php echo $getItem->mCountry; ?>"></p>
 <p><label><strong>Show E-Mail:</strong></label> 
<?php 
 $status = $listing->dblist == 1 && $listing->dbtype != 'listingadmin' ? ($listing->dbtype == 
 'enth' ? ($getItem->mVisible == 1 ? 0 : 1) : $getItem->mVisible) : $getItem->mVisible;
 if($status == 0) {
  echo " <input name=\"visible\" checked=\"checked\" type=\"radio\" value=\"0\"> Leave (Show)\n";
  echo " <input name=\"visible\" type=\"radio\" value=\"1\"> Hide\n";
 } elseif($status == 1) {
  echo " <input name=\"visible\" checked=\"checked\" type=\"radio\" value=\"1\"> Leave (Hide)\n";
  echo " <input name=\"visible\" type=\"radio\" value=\"0\"> Show\n";
 }
?>
 </p>
</fieldset>

<?php  
 if($listing->dblist == 0) {
?>
<fieldset>
 <legend>Fave Field Options</legend>
<?php
  $fave = $getItem->mExtra;
  $favs = explode('|', $fave);
  $favs = $tigers->emptyarray($favs);
  $num = 0;
  if(!empty($fave) && is_countable($favs) && count($favs) > 0) {
  foreach($favs as $f) {
   if(strcasecmp($f, '') !== 0) {
?>
 <p><label><strong>Fave Field <?= $num; ?>:</strong></label>
 <input name="fave[]" class="input1" type="text" value="<?= $f; ?>"></p>
<?php 
   }
 	 $num++;
  }
}

elseif(!isset($_GET['extend']) && !isset($_GET['count']) && !isset($_POST['fave'])) {
 $q = basename($_SERVER['PHP_SELF']) . '?listing=' . $getlistingid .
 '&#38;g=old&#38;d=' . $id . '&#38;extend=1&#38;count=1#fave';
 echo "<p class=\"tc\"><a href=\"$q\">Add Fave Field(s)?</a></p>\n";
}

if(isset($_GET['extend']) && is_numeric($_GET['extend']) && is_numeric($_GET['count'])) {
 $c1 = (int)$tigers->cleanMys($_GET['count']) + 1;
 $q2 = basename($_SERVER['PHP_SELF']) . '?listing=' . $getlistingid . '&#38;' . 
 'g=old&#38;d=' . $id . "&#38;extend=1&#38;count=$c1#fave"; 
 $countQuery = $tigers->cleanMys((int)$_GET['count']);
 echo " <div id=\"fave\">\n";
 for($i = 0; $i < $countQuery; $i++) {
?>
 <p><label><strong>Fave Field <?= $i; ?>:</strong></label>
 <input name="fave[]" class="input1" type="text"></p>
<?php 
 }
 echo ' <p class="noteButton tc"><span class="note">Note:</span> Adding' .
 ' another fave field will erase any data you may have entered in the field(s)' .
 ".<br><a href=\"$q2\">Add Another Field?</a></p>\n";
 echo " </div>\n";
}

if((is_countable($favs) ? count($favs) : 0) != 0 && !empty($fave)) {
?>
 <p><label><strong>Erase Record?</strong></label> 
  <input name="record" class="input3" type="radio" value="yes"> Yes 
  <input name="record" checked="checked" class="input3" type="radio" value="no"> No
 </p>
<?php 
}
?>
</fieldset>
<?php  
}
?>

<fieldset>
 <legend>Submit</legend>
 <p class="tc"><input name="action" class="input2" type="submit" value="Edit Member"></p>
</fieldset>
</form>
<?php 
 }

 elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Member') {
  $id = $tigers->cleanMys($_POST['id']);
  if(empty($id)) {
   $tigers->displayError('Form Error', 'Your ID is empty. This means' . 
	 ' you selected an incorrect listing or you\'re trying to access something' . 
	 ' that doesn\'t exist. Go back and try again.', false);
  } 
	$idtype    = $tigers->cleanMys($_POST['idtype']);
  $members   = $snakes->getMembers($id, $idtype, 'object', $getlistingid);
  $status    = $tigers->cleanMys($_POST['change_status']);
  $email_now = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email_now']));
  $name      = $tigers->cleanMys($_POST['name']);

  if(empty($name)) {
   $tigers->displayError('Form Error', 'Your <samp>name</samp> field is' . 
   ' empty.', false);
  } elseif (strlen($name) > 20) {
   $tigers->displayError('Form Error', 'Your <samp>name</samp> is too' . 
   ' long. Go back and shorten it.', false);
  }
$email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'Your <samp>e-mail</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>e-mail</samp> field are not allowed.', false);
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
    $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
        ' not valid. Please supply a valid site URL or empty the field.', false);
}
  $country = $tigers->cleanMys($_POST['country']);
  if(empty($country)) { 
   $tigers->displayError('Form Error', 'Your <samp>country</samp> field is' . 
   ' empty.', false);
  }
  $visible = $tigers->cleanMys((int)$_POST['visible']);
  if(!is_numeric($visible) || $visible > 1 || strlen($visible) > 1) { 
   $tigers->displayError('Form Error', 'Your <samp>visible</samp> field is' . 
   ' invalid.', false);
  }
  if(isset($_POST['fave'])) {
   $fields = $_POST['fave'];
   $fields = array_map(array($tigers, 'replaceArray'), $fields);
   if(!empty($fields)) { 
    $ff = implode('|', $fields);
    $ff = '|' . trim($ff, '|') . '|';
   } else {
    $ff = ''; 
   }
  } else {
   $ff = '';
  }
 
  $listing = $wolves->getListings($getlistingid, 'object');
	if($listing->dblist == 1) {
   $scorpions->initDB($listing->dbhost, $listing->dbuser, $listing->dbpass, $listing->dbname);
	 if($email_now == 'y') {
    $mail = $jaguars->updateMember($id);
   }
	 $dbtable = $listing->dbtabl;
	 if($listing->dbtype == 'enth') {
	  $v = $visible == 1 ? '0' : 1;
	  $update = "UPDATE `$dbtable` SET `email` = '$email', `name` = '$name'," . 
		" `url` = '$url', `country` = '$country', `showemail` = '$v'"; 
		if($status == 'y') {
     $update .= ", `pending` = '0'"; 
    } 
    if(isset($_POST['update_date']) && $_POST['update_date'] == 'y') {
		 $update .= ', `added` = CURDATE()';
		}
		$update .= " WHERE LOWER(`email`) = '$id' LIMIT 1";
	 } elseif ($listing->dbtype == 'fanbase') {
	  $v = $visible == 1 ? 'y' : 'n';
	  $update = "UPDATE `$dbtable` SET `email` = '$email', `name` = '$name'," . 
		" `url` = '$url', `country` = '$country', `apr` = '$v'"; 
		if($status == 'y') {
     $update .= ", `apr` = 'y'"; 
    } 
		$update .= " WHERE `id` = '$id' LIMIT 1";
	 } elseif ($listing->dbtype == 'listingadmin') {
	  $v = $visible == 1 ? 'y' : 'n';
	  $update = "UPDATE `$dbtable` SET `mEmail` = '$email', `mName` = '$name'," . 
		" `mURL` = '$url', `mCountry` = '$country', `mVisible` = '$v'"; 
		if($status == 'y') {
     $update .= ", `pending` = '0'"; 
    } 
    if(isset($_POST['update_date']) && $_POST['update_date'] == 'y') {
		 $update .= ', `mAdd` = CURDATE()';
		}
		$update .= " WHERE `mID` = '$id' LIMIT 1";
		$scorpions->query("SET NAMES 'utf8';");
	 }
   $true = $scorpions->query($update);

   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to' . 
		' update the member you selected.', true, $update);
   } elseif ($true == true) {
    echo $tigers->displaySuccess('The member was updated! :D');
	  if(isset($mail) && $mail) {
       echo $tigers->displaySuccess('The member was notified of their update!');
      }
	 }
	 $scorpions->breach(0);
	 $scorpions->breach(1);
	}
	
	else {
   if($email_now == 'y') {
    $mail = $jaguars->updateMember($id);
   }
   $update = "UPDATE `$_ST[members]` SET `mEmail` = '$email', `mName` = '$name'," . 
	 " `mURL` = '$url', `mCountry` = '$country', `mExtra` = '$ff', `mVisible` =" . 
	 " '$visible',"; 
   if($status == 'y') {
    $update .= " `mPending` = '0',"; 
   } 
	 if(isset($_POST['update_date']) && $_POST['update_date'] == 'y') {
		$update .= ' `mAdd` = CURDATE(),';
	 }
   $update .= ' `mEdit` = NOW()';
   $update .= " WHERE `mID` = '$id' LIMIT 1";
   $scorpions->query("SET NAMES 'utf8';");
   $true = $scorpions->query($update);

   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to' . 
    ' update the members.', true, $update);
   } elseif ($true == true) {
    echo $tigers->displaySuccess('The member was updated! :D');
	  if(isset($mail) && $mail) {
       echo $tigers->displaySuccess('The member was notified of their update!');
      }
	 }
	}
	$d = $idtype == 'email' && $id != $email ? $email : $id;
	echo $tigers->backLink('mem', $getlistingid, '', $d);
	echo $tigers->backLink('mem', $getlistingid);
  echo $tigers->backLink('mem');
 }
 
 /** 
  * Update listing, if not crosslisted~ 
  */ 
 elseif (isset($_GET['action']) && $_GET['action'] == 'update') {
  $listing = $tigers->cleanMys($_GET['listing']);
	$getItem = $wolves->getListings($listing, 'object');
	if(!isset($listing) || empty($listing) || !ctype_digit($listing)) {
	 $tigers->displayError('Script Error', 'In order to update a listing, a' . 
	 ' listing ID must be provided.', false);
	}
	
	/** 
	 * Is this a crosslisting listing? If so, it has to be Listing Admin :x 
	 */  
	if($getItem->dblist == 1 && $getItem->dbtype == 'listingadmin') {
	 $scorpions->breach(0);
   $scorpions->initDB($getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname);
   $dbplod = explode('_', $getItem->dbtabl);
	 $dbtabl = $dbplod[0];
	 $dbflid = $getItem->dbflid;
	 $update = "UPDATE `$dbtabl` SET `updated` = CURDATE() WHERE `id` = '$dbflid'" . 
   ' LIMIT 1';
   $scorpions->query("SET NAMES 'utf8';");
	 $true = $scorpions->query($update);
	 if($true == false) {
	  $scorpions->breach(0);
	  $tigers->displayError('Database Error', 'The script was unable to edit' . 
	  ' the listing.', true, $update);
	 } else {
	  $scorpions->breach(0);
		$scorpions->breach(1);
	  echo $tigers->displaySuccess('The <em>' . $tigers->replaceSpec($getItem->subject) .
    '</em> listing has been updated! :D');
	  echo $tigers->backLink('mem');
	  echo $tigers->backLink('index');
	 } 
	} else {if($getItem->dblist == 1 && $getItem->dbtype != 'listingadmin') {
     echo $tigers->displaySuccess('The <em>' . $getItem->subject . '</em> listing' .
   ' is crosslisted to a non-Listing Admin script, and therefore cannot be' .
   " updated this way. Sorry, m'love! :(");
       echo $tigers->backLink('mem');
     echo $tigers->backLink('index');
    }

    /**
     * Non-crosslisted listings are updated right harrrr :D
     */
    elseif ($getItem->dblist == 0) {
     $update = "UPDATE `$_ST[main]` SET `updated` = CURDATE() WHERE `id` =" .
   " '$listing' LIMIT 1";
   $scorpions->query("SET NAMES 'utf8';");
     $true = $scorpions->query($update);
     if($true == false) {
      $tigers->displayError('Database Error', 'The script was unable to edit' .
      ' the listing.', true, $update);
     } else {
      echo $tigers->displaySuccess('The <em>' . $getItem->subject . '</em>' .
    ' listing has been updated! :D');
      echo $tigers->backLink('mem');
      echo $tigers->backLink('index');
     }
    }}
 }

 /** 
  * Mass-approve members 
  */ 
 elseif (isset($_POST['action']) && $_POST['action'] == 'Approve') {
	$getItem = $wolves->getListings($getlistingid, 'object');
	if(empty($_POST['member'])) {
	 $tigers->displayError('Form Error', 'In order to approve a member,' . 
	 ' you must select a member (or more) to approve them. :P', false);
	}
	foreach($_POST['member'] as $pm) {
	 if($getItem->dblist == 1) {
	  $scorpions->breach(0);
    $scorpions->initDB($getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname);
		
	  $dbtable = $getItem->dbtabl;
	  $dbflid  = $getItem->dbflid;
	  if($getItem->dbtype == 'enth') {
	   $update = "UPDATE `$dbtable` SET `pending` = '0', `added` = CURDATE()" . 
		 " WHERE LOWER(`email`) = '$pm' LIMIT 1";
		} elseif ($getItem->dbtype == 'fanupdate') {
		 $update = "UPDATE `$dbtable` SET `apr` = 'y' WHERE `id` = '$pm' LIMIT 1";
    } elseif ($getItem->dbtype == 'listingadmin') {
		 $update = "UPDATE `$dbtable` SET `mPending` = '0', `mAdd` = CURDATE()" . 
		 " WHERE `mID` = '$pm' LIMIT 1";
     $scorpions->query("SET NAMES 'utf8';");
	  } 
		$true = $scorpions->query($update);
	  if($true == false) {
	   $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' approve the member.', true, $update);
	  } elseif ($true == true) {
	   echo '<p class="successButton"><span class="success">SUCCESS!</span> The' . 
	   " member has been approved! :D</p>\n";
	  } 
		$scorpions->breach(0);
		$scorpions->breach(1);
	  $mailNow = $jaguars->approveMember($pm, $getlistingid, $getItem->dblist, $getItem->dbtype);
	  if($mailNow) {
	   echo '<p class="successButton"><span class="success">SUCCESS!</span> The' .
		 " member was notified of their approval!</p>\n";
	  }
	 } else {
		$update = "UPDATE `$_ST[members]` SET `mPending` = '0', `mAdd` = CURDATE()" . 
		" WHERE `mID` = '$pm' LIMIT 1";
    $scorpions->query("SET NAMES 'utf8';");
		$true = $scorpions->query($update);
	  if($true == false) {
	   $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' approve the member.', true, $update);
	  } elseif ($true == true) {
	   echo $tigers->displaySuccess('The <samp>' . $snakes->memberName($pm) . 
     '</samp> member (from the <em>' . $getItem->subject . '</em> listing) has' .
     ' been approved! :D');
	  } 
	  $mailNow = $jaguars->approveMember($pm);
	  if($mailNow) {
	   echo $tigers->displaySuccess('The member was notified of their approval!');
	  }
	 }
	}
	echo $tigers->backLink('mem', $getlistingid);
  echo $tigers->backLink('mem');
 }

 /** 
  * Mass-delete members 
  */ 
 elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
  if(empty($_POST['member'])) {
   $tigers->displayError('Form Error', 'You need to select a member' . 
	 ' (or two, etc.) in order to delete them.', false);
  }
	$getItem = $wolves->getListings($getlistingid, 'object');
 
  foreach($_POST['member'] as $pm) {
   if($getItem->dblist == 1) {
	  $scorpions->breach(0);
    $scorpions->initDB(
     $getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname
    );
		
	  $dbtable = $getItem->dbtabl;
	  if($getItem->dbtype == 'enth') {
     $select = "DELETE FROM `$dbtable` WHERE LOWER(`email`) = '$pm' LIMIT 1";
    } elseif ($getItem->dbtype == 'fanbase') {
     $select = "DELETE FROM `$dbtable` WHERE `id` = '$pm' LIMIT 1";
    } elseif ($getItem->dbtype == 'listingadmin') {
     $delete = "DELETE FROM `$dbtable` WHERE `mID` = '$pm' LIMIT 1";
    }
    $true = $scorpions->query($delete);
	  if($true == false) {
	   $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' delete the member.', true, $delete);
	  } elseif ($true == true) {
	   echo $tigers->displaySuccess('The member has been deleted! :D');
	  } 
		$scorpions->breach(0);
		$scorpions->breach(1);
   } else {
    $delete = "DELETE FROM `$_ST[members]` WHERE `mID` = '$pm' LIMIT 1";
	  $true   = $scorpions->query($delete);
	  if($true == true) {
	   echo $tigers->displaySuccess('The member was deleted!');
	  } 
   }
  }
	echo $tigers->backLink('mem', $getlistingid);
  echo $tigers->backLink('mem');
 }

 /** 
  * Mass-update members 
  */ 
 elseif (isset($_POST['action']) && $_POST['action'] == 'Update') {
  $getItem = $wolves->getListings($getlistingid, 'object');
  if(empty($_POST['member'])) {
   $tigers->displayError('Form Error', 'You need to select a member' . 
	 ' (or two, etc.) in order to update them.', false);
  }
 
  foreach($_POST['member'] as $pm) {
	 if($getItem->dblist == 1) {
	  $scorpions->breach(0);
    $scorpions->initDB($getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname);
		
	  $dbtable = $getItem->dbtabl;
	  $dbflid  = $getItem->dbflid;
	  if($getItem->dbtype == 'enth') {
	   $update = "UPDATE `$dbtable` SET `pending` = '0', `added` = CURDATE()" . 
		 " WHERE LOWER(`email`) = '$pm' LIMIT 1";
		} elseif ($getItem->dbtype == 'listingadmin') {
		 $update = "UPDATE `$dbtable` SET `mPending` = '0', `mUpdate` = 'n'," . 
     " `mEdit` = NOW() WHERE `mID` = '$pm' LIMIT 1";
     $scorpions->query("SET NAMES 'utf8';");
	  } 
		$true = $scorpions->query($update);
	  if($true == false) {
	   $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' update the member.', true, $update);
	  } elseif ($true == true) {
	   echo '<p class="successButton"><span class="success">SUCCESS!</span> The' . 
	   " member has been updated! :D</p>\n";
	  } 
		$scorpions->breach(0);
		$scorpions->breach(1);
		$mailNow = $jaguars->updateMember($pm, $getlistingid);
	  if($mailNow) {
	   echo $tigers->displaySuccess('The member was notified of their update!');
	  }
	 } else {
	  $member = $snakes->getMembers($pm, 'object');
		$update = "UPDATE `$_ST[members]` SET `mPending` = '0', `mUpdate` = 'n'," . 
    " `mEdit` = NOW() WHERE `mID` = '$pm' LIMIT 1";
    $scorpions->query("SET NAMES 'utf8';");
		$true = $scorpions->query($update);
	  if($true == false) {
	   $tigers->displayError('Database Error', 'The script was unable to' . 
		 ' approve the member.', true, $update);
	  } elseif ($true == true) {
	   echo $tigers->displaySuccess('The <samp>' . $member->mName . '</samp> member' . 
		 ' (from the <em>' . $getItem->subject . '</em> listing) has been updated! :D');
	  } 
	  $mailNow = $jaguars->updateMember($pm);
	  if($mailNow) {
	   echo $tigers->displaySuccess('The member was notified of their update!');
	  }
	 }
	}
	echo $tigers->backLink('mem', $getlistingid);
  echo $tigers->backLink('mem');
 }

 /** 
  * @section Index 
  */ 
 else {
?>
<p>Welcome to <samp>members.php?listing=<?php echo $getlistingid; ?></samp>, the 
page to edit and/or delete your current members from the 
<strong><?php echo $wolves->getSubject($getlistingid); ?></strong>! 
Below is the list of your members; to edit or delete a member, click "Edit" or 
"Delete" by the appropriate member.</p>

<?php  
  $snakes->membersPage($getlistingid, 'form');
  if(isset($_GET['g']) && $_GET['g'] == 'searchMembers') {
   $a = array(
	  'searchType' => $tigers->cleanMys($_GET['s']),
    'searchText' => $tigers->cleanMys($_GET['q'])
	 );
   $s = 'members';
	 $b = $a;
  } else {
	 $s = '';
	 $b = '';
	}
  $select = $snakes->sortMembers($getlistingid, $s, $b);
  $count  = is_countable($select) ? count($select) : 0;
	
  if($count > 0) {
	 if($ender > $count) {
	  $ender = $count;
	 }
	 
	 $snakes->membersPage($getlistingid, 'head');
   while($start < $ender) {
	  $u = $select[$start];
		$i = is_numeric($u['mID']) ? 'id' : 'email';
	  $getItem = $snakes->getMembers($u['mID'], $i, 'object', $getlistingid);
		
		$qw = !isset($_GET['listingid']) ? '' : '&#38;listingid=' . $tigers->cleanMys($_GET['listingid']);
    $c = $getItem->mPending == '1' ? ($getItem->mUpdate == 'y' ? ' class="update"' :
		' class="approve"') : '';
    $n = $getItem->mPending == '1' ? ($getItem->mUpdate == 'y' ? '<strong>' . $getItem->mName .
		'</strong>' : '<em>' . $getItem->mName . '</em>') : $getItem->mName;
?>
<tbody<?php echo $c; ?>><tr>
 <td class="tc"><input name="member[]" type="checkbox" value="<?php echo $getItem->mID; ?>"></td>
 <td class="tc">
<?php 
 if($getItem->mPending == 1) {
  if($getItem->mUpdate == 'y') {
	 echo '<strong>Pending Update</strong>';
	} else {
	 echo '<em>Pending Approval</em>';
	}
 } else {
  echo 'Approved';
 }
?>
 </td>
 <td class="tc"><?php echo $n; ?></td>
 <td class="tc"><?php echo $getItem->mEmail; ?></td>
 <td class="floatIcons tc">
<?php  
    $edit   = 'members.php?listing=' . $getlistingid . '&#38;g=old' . $qw . '&#38;d=' . $getItem->mID;
		$email  = 'emails.php?p=mem&#38;d=' . $getlistingid . '&#38;m=' . $getItem->mID;
		$delete = 'members.php?listing=' . $getlistingid . '&#38;g=erase&#38;d=' . $getItem->mID;
?>
  <a href="<?php echo $edit; ?>">
	 <img src="img/icons/edit.png" alt="">
	</a> <a href="<?php echo $email; ?>">
	 <img src="img/icons/email.png" alt=""> 
	</a> <a href="<?php echo $delete; ?>">
	 <img src="img/icons/delete.png" alt="">
	</a>
 </td>
</tr></tbody>
<?php 
    $start++;
   } 
?>
</table>
</form>
<?php 
   /**  
    * Build a search array if the search is valid and exists~ 
    */ 
   $ma = array();
   $sa = $get_type_id_array['listingadmin']; 
   if(
    isset($_GET['g'], $_GET['s']) && $_GET['g'] == 'searchMembers' && array_key_exists($_GET['s'], $sa)
   ) {
    $members_pagination = is_countable($snakes->membersList(
     $getlistingid, 1, $tigers->cleanMys($_GET['s']), $tigers->cleanMys($_GET['q'])
    )) ? count(
     $snakes->membersList(
      $getlistingid, 1, $tigers->cleanMys($_GET['s']), $tigers->cleanMys($_GET['q'])
     )
    ) : 0;
   } else {
    $members_pagination = is_countable($snakes->membersList($getlistingid)) ? count($snakes->membersList($getlistingid)) : 0;
   }
 
   echo '<p id="pagination">';
	 $pages = ceil($members_pagination / $per_members);
   $snakes->paginate($pages);
   echo "</p>\n";
	 
	 $dbcheck = $wolves->getListings($getlistingid, 'object');
	 if($dbcheck->dblist == 1) {
	  $scorpions->breach(1);
	 }
  } 

  else {
   echo "<p class=\"tc\">Currently no members!</p>\n";
  }
 }
}

/** 
 * Show index of listings~ 
 */ 
else {
 $counter = is_countable($wolves->listingsList()) ? count($wolves->listingsList()) : 0;
 $select  = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC LIMIT $counter";
 $true    = $scorpions->query($select);
 $count   = $scorpions->total($true);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select' . 
  ' the listings from the database.|Make sure your table exists.', true, $select);
 }

 if($count > 0) {
?>

<table class="index">
<thead><tr>
 <th>ID</th>
 <th>Subject</th>
 <th>Members (Pending)</th>
 <th>Action</th>
</tr></thead>
<?php 
  while($getItem = $scorpions->obj($true, 0)) {
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->id; ?></td>
 <td class="tc">
<?php 
   if($getItem->status == 1 || $getItem->status == '1') {
	  echo $getItem->subject;
	 } else {
	  echo '<a href="' . $getItem->url . '">' . $getItem->subject . '</a>';
	 }
?>
 </td>
 <td class="tc">
  <?php echo $snakes->getMemberCount($getItem->id, '0'); ?> 
  (<?php echo $snakes->getMemberCount($getItem->id, 1); ?> Pending)
 </td>
 <td class="tc"><a href="members.php?listing=<?php echo $getItem->id; ?>">Manage Members</a></td>
</tr></tbody>
<?php 
  } 
  echo "</table>\n\n";
 } else {
  echo "\n<p class=\"tc\">There are currently no listings to list!</p>\n";
 }
}

require('footer.php');
