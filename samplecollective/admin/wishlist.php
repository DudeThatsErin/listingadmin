<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <wishlist.php> 
 * @since      September 2nd, 2010 
 * @version    1.0    
 */ 
use Robotess\StringUtils;$getTitle = 'Wishlist';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g']) ? ' <span><a href="wishlist.php?g=new">Add Wish</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['page']) || empty($_GET['page']) || !ctype_digit($_GET['page'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['page']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if(isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<form action="wishlist.php" enctype="multipart/form-data" method="post">
<fieldset>
 <legend>Details</legend>
 <p><label><strong>Subject:</strong></label> <input name="subject" class="input1" type="text"></p>
 <p><label><strong>URL:</strong></label> <input name="url" class="input1" type="url"></p>
 <p><label><strong>Image:</strong></label> <input name="image" class="input1" type="file"></p>
 <p class="tc"><strong>Description:</strong><br>
 <textarea name="desc" cols="50" rows="8" style="width: 100%;"></textarea></p>
 <p><label><strong>Type:</strong><br>
 A <strong>wishlist type</strong> stands for the type of wish it is. It can be 
 in the top (i.e.: 'Top 5 Wishes'), the regular list, or even custom &#8211; it's  
 up to you!</label>
<?php  
 $typearray = $get_wishlist_array;
 foreach($typearray as $tA => $wA) {
  $c = $tA == 'list' ? ' checked=""' : '';
  echo "<input name=\"type\"{$c} type=\"radio\" value=\"$tA\"> $wA";
  if($tA == 'granted') {
   echo '<a href="#note-1"><sup>1</sup></a>';
  } 
  echo " \n";
 }
?>
</fieldset>

<fieldset>
 <legend>Categories</legend>
 <p><label><strong>Categories:</strong></label> 
 <select name="category[]" class="input1" multiple="multiple" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY `catname` ASC";
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "  <option value=\"0\">No Category Available</option>\n";
 }
	
 else {
  while($getItem = $scorpions->obj($true, 0)) {
   $catid = $getItem->catid;
   echo '  <option value="' . $getItem->catid . '">' . $getItem->catname . "</option>\n";
	 $q2 = $scorpions->query("SELECT * FROM `$_ST[categories]` WHERE `parent` =" . 
   " '$catid' ORDER BY `catname` ASC");
	 while($getItem2 = $scorpions->obj($q2, 0)) {
    echo '  <option value="' . $getItem2->catid . '">' .
	  $lions->getCatName($getItem2->parent) . ' &#187; ' . $getItem2->catname . "</option>\n";
	 }
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Wish"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
 <menu class="smallNote">
  <li id="note-1">While it is suggested you mark your appropriate listings as a 
	granted wish if applicable, you can still upload a new image here, if you 
  prefer to use a different image.</li>
 </menu>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Add Wish') {
 $subject = $tigers->cleanMys($_POST['subject']);
 if(empty($subject)) { 
  $tigers->displayError('Form Error', 'Your <samp>subject</samp> field is' . 
  ' empty.', false);
 } 
$url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (empty($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> does' .
                ' not start with http:// and therefore is not valid. Try again.', false);
        }
 $desc = $tigers->cleanMys($_POST['desc'], 'n');
 $image = $_FILES['image'];
 $image_tag = substr(sha1(date('YmdHis')), 0, 15);
 if(!empty($_FILES['image']['name'])) {
	$imageinfo = getimagesize($_FILES['image']['tmp_name']);
	$imagetype = $imageinfo[2];
  if($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
   $tigers->displayError('Form Error', 'Only <samp>.gif</samp>, <samp>' . 
	 '.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
  }
 }
 $category = array();
 $category = $_POST['category'];
 if(empty($category)) {
  $tigers->displayError('Form Error', 'Your <samp>category</samp> field is' . 
  ' empty.', false);
 }
 $category = array_map(array($tigers, 'cleanMys'), $category);
 $type = $tigers->cleanMys($_POST['type']);
 if(empty($type) || !array_key_exists($type, $get_wishlist_array)) {
  $tigers->displayError('Form Error', 'Your <samp>wish type</samp> is invalid. ', false);
 }

 $wsh_path = $seahorses->getOption('wsh_path');
 if(!empty($wsh_path) && is_dir($wsh_path)) {
  $path = $wsh_path;
 } else {
  $path = str_replace('wishlist.php', '', $_SERVER['SCRIPT_FILENAME']);
 }

 $e = file_exists($path . 'LAdminWish_' . $image['name']) ? $image_tag . '_' : '';
 if(!empty($image)) {
  $file = $scorpions->escape('LAdminWish_' . $e . $image['name']);
  $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
 } else {
  $file = '';
 }

 $cat = implode('!', $category);
 $cat = '!' . trim($cat, '!') . '!';

 $insert = "INSERT INTO `$_ST[wishlist]` (`wSubject`, `wURL`, `wImage`," . 
 " `wCategory`, `wDesc`, `wType`) VALUES ('$subject', '$url', '$file', '$cat'," . 
 " '$desc', '$type')";
 $true = $scorpions->insert($insert, 0);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to add' . 
	' the wish to the database.|Make sure your wishlist table exists.', 
	true, $insert);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
	" wish was added to the database!</p>\n";
	if(isset($success) && $success) {
     echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
       " wishlist image was uploaded!</p>\n";
    }
	echo $tigers->backLink('wishlist', 'new');
  echo $tigers->backLink('wishlist');
 }
}

/* 
 *  @section   Edit Wishlist 
 */ 
elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
 if(empty($_GET['d']) || !isset($_GET['d'])) {
?>
<form action="wishlist.php" method="get">
<p class="noMargin"><input name="g" type="hidden" value="old"></p>

<fieldset> 
 <legend>Choose Wishlist Item</legend>
 <p><label><strong>Wishlist Item:</strong></label> <select name="d" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[wishlist]` ORDER BY `wSubject` ASC";
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "  <option>No Wishlist Items Available</option>\n";
 }

 else {
  while($getItem = $scorpions->obj($true, 0)) {
   echo '  <option value="' . $getItem->wID . '">' . $getItem->wSubject . "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc"><input class="input2" type="submit" value="Edit Wishlist Item"></p>
</fieldset>
</form>
<?php
 }

 if(!empty($_GET['d'])) {
  $id = $tigers->cleanMys($_GET['d']);
  if(!is_numeric($id)) {
   $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	 ' selected an incorrect wish or you\'re trying to access something that' . 
	 ' doesn\'t exist. Go back and try again.', false);
  }

  $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wID` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to' . 
	 ' select that specific listing.|Make sure the ID is not empty and the' . 
	 ' listings table exists.', true, $select);
  }
  $getItem = $scorpions->obj($true, 0);
?>
<form action="wishlist.php" enctype="multipart/form-data" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->wID; ?>">
<?php  
  if(isset($_GET['c']) && !empty($_GET['c']) && is_numeric($_GET['c'])) {
?>
 <input name="catid" type="hidden" value="<?php echo $tigers->cleanMys($_GET['c']); ?>">
<?php  
  }
?>
</p>

<fieldset>
 <legend>Details</legend>
 <p><label><strong>Subject:</strong></label> 
 <input name="subject" class="input1" type="text" value="<?php echo $getItem->wSubject; ?>"></p>
 <p><label><strong>URL:</strong></label> 
 <input name="url" class="input1" type="url" value="<?php echo $getItem->wURL; ?>"></p>
 <p class="tc"><strong>Description:</strong><br>
 <textarea name="desc" cols="50" rows="8" style="width: 100%;"><?php echo $getItem->wDesc; ?></textarea></p>
 <p><label><strong>Type:</strong><br>
 A <strong>wishlist type</strong> stands for the type of wish it is. It can be 
 in the top (i.e.: 'Top 5 Wishes'), the regular list, or even custom &#8211; it's  
 up to you!</label>
<?php 
 $type1 = explode('!', $getItem->wType);
 $types = $get_wishlist_array;
 foreach($types as $t1 => $t2) {
  echo '<input name="type"';
  if(in_array($t1, $type1)) {
   echo ' checked="checked"';
  }
  echo ' class="input3" type="radio" value="' . $t1 . '"> ' . $t2 . "\n";
 }
?>
 </p>
</fieldset>

<fieldset>
 <legend>Image</legend>
<?php 
  $img = $seahorses->getOption('wsh_path') . $getItem->wImage;
  if(!empty($getItem->wImage) && file_exists($img)) { 
?>
 <p class="tc"><img src="<?php echo $seahorses->getOption('wsh_http') . $getItem->wImage; ?>" alt=""></p>
<?php 
  } 
?>
 <p><label><strong>Changes:</strong></label> 
 <input name="change" class="input3" type="radio" value="add"> Add
 <input name="change" class="input3" type="radio" value="edit"> Edit
 <input name="change" class="input3" type="radio" value="delete"> Delete
 <input name="change" checked="checked" class="input3" type="radio" value="none"> No Change</p>
 <p><label>New Image:</label> <input name="image" class="input1" type="file"></p>
</fieldset>

<fieldset>
 <legend>Categories</legend>
 <p><label><strong>Categories:</strong></label> 
 <select name="category[]" class="input1" multiple="multiple" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY `catname`";
 $true = $scorpions->query($select); 
 if($true == false) { 
  echo "  <option>No Category Available</option>\n";
 }

 else {
  while($getCat = $scorpions->obj($true, 0)) {
   $catid = $getCat->catid;
   $cats = explode('!', $getItem->wCategory);
   echo '  <option value="' . $getCat->catid . '"';
	 if(in_array($getCat->catid, $cats)) {
	  echo ' selected="selected"'; 
   }
	 echo '>' . $getCat->catname . "</option>\n";
	 $q2 = $scorpions->query("SELECT * FROM `$_ST[categories]` WHERE `parent` = '$catid' ORDER BY `catname` ASC");
	 while($getCat2 = $scorpions->obj($q2, 0)) {
    echo '  <option value="' . $getCat2->catid . '"';
	  if(in_array($getCat2->catid, $cats)) {
	   echo ' selected="selected"'; 
    }
	  echo '>' . $lions->getCatName($getCat2->parent) . ' &#187; ' . $getCat2->catname .
    "</option>\n";
	 }
  }
 }
?>
 </select></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Edit Wish"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
 }
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Wish') {
 $id = $tigers->cleanMys($_POST['id']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Form Error', 'Your ID is empty. This means you' . 
	' selected an incorrect wish or you\'re trying to access something that' . 
	' doesn\'t exist. Go back and try again.', false);
 } 
 if(isset($_POST['catid'])) {
  $catid = $tigers->cleanMys($_POST['catid']);
  if(is_numeric($catid) && in_array($catid, $lions->categoryList())) {
   $c = $catid;
  } else {
   $c = '';
  }
 }
 $subject = $tigers->cleanMys($_POST['subject']);
 if(empty($subject)) { 
  $tigers->displayError('Form Error', 'Your <samp>subject</samp> field is' . 
  ' empty.', false);
 }
 $url = StringUtils::instance()->normalizeUrl($_POST['url']);
        if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp>' .
                ' appears to be invalid; make sure you haven\'t included any invalid characters' .
                ' and you prepended your URL with <samp>http</samp>.', false);
        }
 $desc = $tigers->cleanMys($_POST['desc'], 'nom');
 $change = $tigers->cleanMys($_POST['change']);
 $changeArray = array('add', 'edit', 'delete', 'none');
 if(!in_array($change, $changeArray)) {
  $tigers->displayError('Form Error', 'You can only add, edit and delete' . 
	' an image.', false);
 }
 $image = $_FILES['image'];
 $image_tag = substr(sha1(date('YmdHis')), random_int(0, 8), 15);
 if($change == 'add' || $change == 'edit') {
	$imageinfo = getimagesize($_FILES['image']['tmp_name']);
	$imagetype = $imageinfo[2];
  if($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
   $tigers->displayError('Form Error', 'Only <samp>.gif</samp>, <samp>.jpg' . 
   '</samp> and <samp>.png</samp> extensions allowed.', false);
  }
 }
 $category = $_POST['category'];
 if(empty($category)) {
  $tigers->displayError('Form Error', 'Your <samp>category</samp> field is' . 
  ' empty.', false);
 }
 $category = array_map(array($tigers, 'cleanMys'), $category);
 $type = $tigers->cleanMys($_POST['type']);
 if(empty($type) || !array_key_exists($type, $get_wishlist_array)) {
  $tigers->displayError('Form Error', 'Your <samp>wish type</samp> is invalid. ', false);
 }
 
 if($change != 'none' && $change != 'add') {
  $sImage = $mermaids->pullImage_Wishlist($id);
  $dImage = $seahorses->getOption('wsh_path') . $sImage;

  if($change == 'delete' || $change == 'edit') {
   if(!empty($sImage) && file_exists($dImage)) {
	  $delete = @unlink($dImage);
	 }
  }
 } 
 
 $wsh_path = $seahorses->getOption('wsh_path');
 if(!empty($wsh_path) && is_dir($wsh_path)) {
  $path = $wsh_path;
 } else {
  $path = str_replace('wishlist.php', '', $_SERVER['SCRIPT_FILENAME']);
 }

 $e = file_exists($path . 'LAdminWish_' . $image['name']) ? $image_tag . '_' : '';
 $file = $scorpions->escape('LAdminWish_' . $e . $image['name']);
 if($change == 'add' || $change == 'edit') {
  if($change != 'delete' && $change != 'none') {
   $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
  }
 }

 $cat = implode('!', $category);
 $cat = '!' . trim($cat, '!') . '!';

 $update = "UPDATE `$_ST[wishlist]` SET `wSubject` = '$subject', `wURL` = '$url',";
 if($change == 'add' || $change == 'edit') { 
  $update .= " `wImage` = '$file',";
 } elseif ($change == 'delete') {
  $update .= " `wImage` = '',";
 }
 $update .= " `wCategory` = '$cat', `wDesc` = '$desc', `wType` = '$type' WHERE" . 
 " `wID` = '$id' LIMIT 1";
 $true = $scorpions->update($update);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to update' . 
	' the wish.|Make sure your ID is not empty and your wishlist table exists.', 
	true, $update);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
	" wishlist was updated!</p>\n";
	if(isset($delete, $success)) {
   if($delete && $success) {	
	  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
		" old image was deleted and replaced with a new one!</p>\n";
	 }
	}
  elseif (isset($delete) && !isset($success)) {
	 if($delete) { 
	  echo '<p class="successButton"><span class="success">SUCCESS!</span>' .
		" Your old image was deleted!</p>\n";
   }
	}
	elseif (!isset($delete) && isset($success)) {
	 if($success) { 
	  echo '<p class="successButton"><span class="success">SUCCESS!</span>' .
		" Your image was uploaded!</p>\n";
   }
	}
	echo $tigers->backLink('wishlist', 'n', $catid);
	echo $tigers->backLink('wishlist', $id);
	echo $tigers->backLink('wishlist');
 }
}

/*  
 *  @section   Delete Wishlist 
 */ 
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
 $id = $tigers->cleanMys($_GET['d']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	' selected an incorrect wish or you\'re trying to access something that' . 
	' doesn\'t exist. Go back and try again.', false);
 }

 $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wID` = '$id' LIMIT 1";
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select' . 
	' that specific wish.|Make sure the ID is not empty and the wishlist table' . 
	' exists.', true, $select);
 }
 $getItem = $scorpions->obj($true);
?>
<p>You are about to delete the <strong><?php echo $getItem->wSubject; ?></strong> 
wish; please be aware that once you delete a wish, it is gone forever. <em>This 
cannot be undone!</em> To proceed, click the "Delete Wish" button. :)</p>

<form action="wishlist.php" method="post">
<p class="noMargin"><input name="id" type="hidden" value="<?php echo $getItem->wID; ?>"></p>

<fieldset>
 <legend>Delete Wish</legend>
 <p class="tc">Deleting <strong><?php echo $getItem->wSubject; ?></strong></p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Delete Wish"></p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Wish') {
 $id = $tigers->cleanMys($_POST['id']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means you' . 
	' selected an incorrect wish or you\'re trying to access something that doesn\'t' . 
	' exist. Go back and try again.', false);
 }
 
 $sImage = $mermaids->pullImage_Wishlist($id);
 $dImage = $seahorses->getOption('wsh_path') . $sImage; 
 if(file_exists($dImage) && !empty($sImage)) {
  $delete = @unlink($dImage);
 }

 $delete = "DELETE FROM `$_ST[wishlist]` WHERE `wID` = '$id' LIMIT 1";
 $true = $scorpions->query($delete);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to delete' . 
	' the wish.|Make sure your ID is not empty and your wishlist table exists.', 
	true, $delete);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
	" wish was deleted!</p>\n";
  echo $tigers->backLink('wishlist');
 }
}

/** 
 * Index 
 */ 
else {
?>
<p>Welcome to <samp>wishlist.php</samp>, the page to add a wish and edit or 
delete current ones! Below is the list of your wishes. To edit or delete a wish, 
click "Edit" or "Delete" by the appropriate wish.</p>

<form action="wishlist.php" method="get">
<p class="noMargin"><input name="g" type="hidden" value="searchCategories"></p>

<fieldset>
 <legend>Search Categories</legend>
 <p><label><strong>Category:</strong></label> <select name="c" class="input1">
<?php
 $query = "SELECT * FROM `$_ST[categories]` ORDER BY `catname` ASC";
 $result = $scorpions->query($query);
 if($result == false) {
  echo "  <option>No Category Available</option>\n";
 }

 else {
  while($getTion = $scorpions->obj($result, 0)) {
   echo '  <option value="' . $getTion->catid . '"';
	 if(isset($_GET['g']) && $_GET['g'] == 'searchCategories' && $cat_id == $getTion->catid) {
      echo " selected=\"selected\"";
     }
	 echo '>' . $getTion->catname . "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Search Category"></p>
</fieldset>
</form>
<?php 
 $select = "SELECT * FROM `$_ST[wishlist]`";
 if((isset($_GET['g']) && $_GET['g'] == 'searchCategories') && (is_numeric($_GET['c']))) {
  $c = $tigers->cleanMys($_GET['c']);
	$select .= " WHERE `wCategory` LIKE '%!$c!%'";
 }
 $select .= " ORDER BY `wSubject` ASC LIMIT $start, $per_page";
 $true = $scorpions->query($select);
 $count = $scorpions->counts($select, 1);
 
 if($count->rows > 0) {
  if(isset($c)) {
     $catid=$lions->getCatName($c);
	 echo '<p style="font-size: 12pt; margin: 0;">Currently searching the <strong>' .
	 $catid . "</strong> category...</p>\n";
	}
?>
<table class="index">
<thead><tr>
 <th>ID</th>
 <th>Subject</th>
 <th>Image</th>
 <th>Type</th>
 <th>Category</th>
 <th>Action</th>
</tr></thead>
<?php 
  while($getItem = $scorpions->obj($true, 0)) {
   $imp = strpos($my_wishess, '/') !== false ? $seahorses->getOption('wsh_http') . $getItem->wImage 
   : $seahorses->getOption('wsh_http') . '/' . $getItem->wImage;
   if(file_exists($seahorses->getOption('wsh_path') . $getItem->wImage)) {
    $imgNow = '<img src="' . $imp . "\" alt=\"\">\n";
   } else {
    $imgNow = '';
   }
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->wID; ?></td>
 <td class="tc"><?php echo $getItem->wSubject; ?></td>
 <td class="tc"><?php echo $imgNow; ?></td>
 <td class="tc"><?php echo $get_wishlist_array[$getItem->wType]; ?></td>
 <td class="tc"><?php echo $lions->pullCatNames($getItem->wCategory, '!'); ?></td>
 <td class="floatIcons tc">
  <a href="wishlist.php?g=old&#38;d=<?php echo $getItem->wID; ?>">
	 <img src="img/icons/edit.png" alt="">
	</a> 
  <a href="wishlist.php?g=erase&#38;d=<?php echo $getItem->wID; ?>">
	 <img src="img/icons/delete.png" alt="">
	</a>
 </td>
</tr></tbody>
<?php 
  } 
  echo "</table>\n"; 
  echo "\n<p id=\"pagination\">Pages: ";

  $cs = isset($_GET['g']) && $_GET['g'] == 'searchCategories' ? 'category' : 'id';
  $ss = isset($_GET['g']) && $_GET['g'] == 'searchCategories' ? $catid : '';
  $results = $mermaids->wishlistList($cs, $ss);
  $pages = ceil((is_countable($results) ? count($results) : 0) / $per_page);

  for($i = 1; $i <= $pages; $i++) {
   if($page == $i) {
    echo $i . ' ';
   } elseif (isset($_GET['g']) && $_GET['get'] == 'searchCategories') {
    echo '<a href="wishlist.php?g=searchCategories&amp;c=' . $catid . '&#38;page=' . $i . '">' . $i . '</a> ';
   } else {
    echo '<a href="wishlist.php?page=' . $i . '">' . $i . '</a> ';
   }
  }

  echo "</p>\n";
 } 

 else {
  echo "<p class=\"tc\">Currently no wishes!</p>\n";
 } 
}

require('footer.php');
