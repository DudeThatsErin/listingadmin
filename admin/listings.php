<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <listings.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;
use Robotess\Templates;

$getTitle = 'Listings';

require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$templatesExamples = new Templates();

$sp = !isset($_GET['g']) 
|| (isset($_GET['g']) && preg_match('/^(search)([A-Za-z]+)/', $_GET['g'])) ?
'<span><a href="listings.php?g=new">Add Listing</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if(!isset($_GET['p']) || empty($_GET['p']) || !ctype_digit($_GET['p'])) {
 $page = 1;
} else {
 $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

/**  
 * Manage listing! 
 */ 
if(isset($_GET['g']) && $_GET['g'] == 'manage') {
 $listingArray = $wolves->listingsList();
 if(isset($_GET['d']) && is_numeric($_GET['d']) && in_array($_GET['d'], $listingArray)) { 
  $id = $tigers->cleanMys((int)$_GET['d']);
  $select = "SELECT * FROM `$_ST[main]` WHERE `id` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to' . 
	 ' select the specified listing.|Make sure the ID is not empty and the' . 
	 ' listings table exists.', true, $select);
  }
  $getItem = $scorpions->obj($true, 0);
?>
<div id="menuRight">
<h3 class="tr">Menu</h3>
<?php
 $if1 = !isset($_GET['o']) ? '&#187; ' : '';
 $if2 = isset($_GET['o']) && $_GET['o'] == 'crosslist' ? '&#187; ' : '';
 $if3 = isset($_GET['o']) && $_GET['o'] == 'options' ? '&#187; ' : '';
 $if4 = isset($_GET['o']) && $_GET['o'] == 'templates' ? '&#187; ' : '';
?>
<menu>
 <li><?php echo $if1; ?><a href="listings.php?g=manage&#38;d=<?php echo $id; ?>">Details</a></li>
 <li><?php echo $if2; ?><a href="listings.php?g=manage&#38;d=<?php echo $id; ?>&#38;o=crosslist">Crosslist</a></li>
 <li><?php echo $if3; ?><a href="listings.php?g=manage&#38;d=<?php echo $id; ?>&#38;o=options">Options</a></li>
 <li><?php echo $if4; ?><a href="listings.php?g=manage&#38;d=<?php echo $id; ?>&#38;o=templates">Templates</a></li>
</menu>
</div>

<div id="mainContent">
<?php 
  if(isset($_GET['o']) && $_GET['o'] == 'crosslist') {
?>
<h3>Crosslist</h3>
<p class="scriptButton"><span class="script"><b>Notice:</b></span> This functionality has not been properly
                tested in <?= $laoptions->version ?>. Please report any issues if such occur.
            </p>
<p>Crosslisting should only be used if you are crosslisting a fanlisting with
another person, or are connecting another domain you own. <ins>This should only
be used on a as-needed basis!</ins></p>

<form action="listings.php" enctype="multipart/form-data" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->id; ?>">
 <input name="opt" type="hidden" value="2">
</p>

<fieldset>
 <legend>Database Variables</legend>
 <p><label><strong>Database Host:</strong></label> 
 <input name="dbhost" class="input1" type="text" value="<?php echo $getItem->dbhost; ?>"></p>
 <p><label><strong>Database Username:</strong></label>
 <input name="dbuser" class="input1" type="text" value="<?php echo $getItem->dbuser; ?>"></p>
 <p><label><strong>Database Password:</strong></label>
 <input name="dbpass" class="input1" type="password" value="<?php echo $getItem->dbpass; ?>"></p>
 <p><label><strong>Database Name:</strong></label>
 <input name="dbname" class="input1" type="text" value="<?php echo $getItem->dbname; ?>"></p>
</fieldset>

<fieldset>
<legend>Miscellany</legend>
 <p><label><strong>Crosslisting?</strong><br> 
 Are we actually crosslisting to another script? (This includes other Listing 
 Admin installs!)</label>
 <select name="crosslist" id="crosslist" class="input1">
 <option <?php if($getItem->dblist == '0' || $getItem->dblist == 0) { echo ' selected="selected"'; } ?> value="1">No</option>
 <option <?php if($getItem->dblist == 1) { echo ' selected="selected"'; } ?> value="2">Yes</option>
 </select></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><label><strong>Script:</strong><br>Which script are we crosslisting to?</label> 
 <select name="crosslist_script" class="input1">
  <option<?php if($getItem->dbtype == 'enth') { echo ' selected="selected"'; } ?> value="enth">Enthusiast</option>
	<option<?php if($getItem->dbtype == 'fanbase') { echo ' selected="selected"'; } ?> value="fanbase">phpFanBase</option>
	<option<?php if($getItem->dbtype == 'listingadmin') { echo ' selected="selected"'; } ?> value="listingadmin">Listing Admin</option>
 </select></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><label><strong>Table Name:</strong></label>
 <input name="table" class="input1" type="text" value="<?php echo $getItem->dbtabl; ?>"></p>
 <p><label><strong>Affiliates Table Name:</strong></label>
 <input name="afftable" class="input1" type="text" value="<?php echo $getItem->dbaffs; ?>"></p>
 <p><label><strong>Affiliates Images URL:</strong><br>
 The URL to the affiliates image folder (e.g. <samp>http://website.com/fanlisting/affiliates/</samp>).</label>
 <input name="affhttps" class="input1" type="text" value="<?php echo $getItem->dbhttp; ?>"></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><label><strong>Affiliates Images Path:</strong><br>
 The path to the affiliates image folder (e.g. <samp>/home/username/website/fanlisting/affiliates/</samp>)</label>
 <input name="affpaths" class="input1" type="text" value="<?php echo $getItem->dbpath; ?>"></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
<?php 
if($getItem->dblist == 1 && $getItem->dbtype != 'listingadmin') {
?>
<div id="noCrosslistBlock" style="display: none;">
<?php 
} elseif ($getItem->dblist == 1 && $getItem->dbtype == 'listingadmin') {
?>
<div id="noCrosslistBlock" style="display: block;">
<?php 
}
?>
 <p><label><strong>Listing ID:</strong><br>
 The ID to the listing you're crosslisting to in another Listing Admin install.</label>
 <input name="flid" class="input1" type="text" value="<?php echo $getItem->dbflid; ?>"></p>
</div>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Manage Listing">
 </p>
</fieldset>
</form>
<?php 
  }
	
  elseif (isset($_GET['o']) && $_GET['o'] == 'options') {
?>
<h3>Options</h3>
<form action="listings.php" enctype="multipart/form-data" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->id; ?>">
 <input name="opt" type="hidden" value="3">
</p>

<fieldset>
 <legend>Options</legend>
 <p><label>Status:</label>
<?php
 $dstatus = explode('!', $getItem->status);
 $statuses = array('0' => 'Current', '1' => 'Upcoming', '2' => 'Pending');
 foreach($statuses as $s1 => $s2) {
  echo '  <input name="status"';
  if(in_array($s1, $dstatus)) {
   echo ' checked="checked"';
  }
  echo " class=\"input3\" type=\"radio\" value=\"$s1\"> $s2\n";
 }
?>
 </p>
 <p><label><strong>Granted Wish?</strong></label> <select name="granted" class="input1">
  <option <?php if($getItem->granted == 1) { echo ' selected="selected"'; } ?> value="1">Yes</option>
  <option <?php if($getItem->granted == '0') { echo ' selected="selected"'; } ?> value="0">No</option>
 </select></p>
 <p><label><strong>HTML Markup</strong><br>
 This option chooses what you want HTML mark-up you want for each listing.
 </label> <select name="markup" class="input1">
<?php 
 $markuparray = $get_markup_array;
 foreach($markuparray as $m => $a) {
  echo '  <option';
	if($m == $getItem->markup) {
	 echo ' selected="selected"';
	}
	echo " value=\"$m\">$a</option>\n";
 }
?>
 </select></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><label><strong>Show on</strong> <samp>show-owned.php</samp><strong>?</strong><br>
 This option chooses whether you'd like this particular fanlisting displayed on 
 your owned listings (for all status'). Please be aware if it's <em>not</em> 
 displayed this will <ins>NOT</ins> be included in your initial listing count for
 your collective statistics.
 </label> 
 <select name="show_owned" class="input1">
  <option<?php if($getItem->show == '0') { echo ' selected="selected"'; } ?> value="0">Yes</option>
  <option<?php if($getItem->show == 1) { echo ' selected="selected"'; } ?> value="1">No</option>
 </select></p>
</fieldset>

<fieldset>
 <legend>Image</legend>
<?php 
 $img = $seahorses->getOption('img_path') . $getItem->image;
 if(!empty($getItem->image) && file_exists($img)) { 
?>
 <p class="tc"><img src="<?php echo $seahorses->getOption('img_http') . $getItem->image; ?>" alt=""></p>
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
 <legend>Favourite Fields</legend>
 <p>Favourite fields &#8211; &aacute; la additional or optional fields &#8211; are 
 fields that can be applied to the join form and members list for members to fill out.
 They are by no means required, and the number of favourite fields allowed are
 unlimited.</p>
 <p class="noteButton">You can add, edit and delete fields, but you cannot <em>move</em>
 favourite fields up or down. <strong>Also</strong>, be aware that everytime you add
 a new favourite field, any unsaved changes made to the fields on this page will
 <ins>not</ins> be saved. To erase a field, simply erase the text from the field you
 would no longer wish to have.</p>
<?php 
if(!empty($getItem->fave_fields)) {
 $fave_fields = explode('|', $getItem->fave_fields);
 $fave_fields = $tigers->emptyarray($fave_fields);
 $q3 = basename($_SERVER['PHP_SELF']) . '?g=manage&#38;d=' . $tigers->cleanMys($_GET['d']);
 if(isset($_GET['opt'])) { 
  $q3 .= '&#38;opt=' . $tigers->cleanMys($_GET['opt']) . '&#38;extend=1&' .
  '#38;count=' . ((is_countable($fave_fields) ? count($fave_fields) : 0) + 1) . '#fave';
 } 

 echo "<div id=\"fave\">\n";
 for($i = 0,$iMax = is_countable($fave_fields) ? count($fave_fields) : 0; $i < $iMax; $i++) {
?>
 <p><input name="numero[]" type="hidden" value="<?php echo $i; ?>">
 <label><strong>Favourite Field <?php echo $i; ?></strong></label>
 <input name="favefield[]" class="input1" type="text" 
 value="<?php echo $snakes->additional($fave_fields[$i], 'decode'); ?>"></p>
<?php 
 }
 if(!isset($_GET['extend'])) {
  echo '<p class="noteButton tc"><span class="note">Note:</span> Adding' .
  ' another fave field will erase any data you may have entered in the field(s)' .
  ".<br><a href=\"$q3\">Add Another Field?</a></p>\n";
 }
 echo "</div>\n";
}

else {if(!isset($_GET['extend']) && !isset($_GET['count']) && !isset($_POST['favefield'])) {
 $q = basename($tigers->cleanMys($_SERVER['PHP_SELF'])) .
   '?g=manage&#38;d=' . $tigers->cleanMys($_GET['d']) .
   '&#38;o=' . $tigers->cleanMys($_GET['o']) . '&#38;extend=1&#38;count=1#fave';
 echo "<p class=\"tc\"><a href=\"$q\">Add Favourite Field(s)?</a></p>\n";
}}

if(isset($_GET['extend']) && is_numeric($_GET['extend'])) {
 $c1 = (int)$tigers->cleanMys($_GET['count']) + 1;
 $q2 = basename($_SERVER['PHP_SELF']) . 
 '?g=manage&#38;d=' . $tigers->cleanMys($_GET['d']) .
 '&#38;opt=' . $tigers->cleanMys($_GET['opt'] ?? "") .
 '&#38;extend=1&#38;count=' . $c1 . '#fave';
 $countQuery = $tigers->cleanMys((int)$_GET['count']);
 echo "<div id=\"fave\">\n";
 $vk = isset($fave_fields) && !empty($getItem->fave_fields) ? (is_countable($fave_fields) ? count($fave_fields) : 0) + 1 : 0;
 for($n = $vk; $n <= $countQuery; $n++) {
?>
 <p><input name="numero[]" type="hidden" value="<?php echo $n - 1; ?>">
 <label><strong>Favourite Field <?php echo $n; ?></strong></label>
 <input name="favefield[]" class="input1" type="text"></p>
<?php 
 }
 echo '<p class="noteButton tc"><span class="note">Note:</span> Adding' .
 ' another fave field will erase any data you may have entered in the field(s)' .
 ".<br><a href=\"$q2\">Add Another Field?</a></p>\n";
 echo "</div>\n";
}

if(!empty($getItem->fave_fields) && (is_countable($fave_fields) ? count($fave_fields) : 0) != 0 && !empty($fave_fields)) {
?>
<p><label><strong>Erase Record?</strong></label>
<input name="record" checked="checked" class="input3" type="radio" value="no"> No
<input name="record" class="input3" type="radio" value="yes"> Yes</p>
<?php
}
?>
</fieldset>

<fieldset>
 <legend>Previous Owners</legend>
 <p class="noteButton">You only should fill out these fields if your listing has 
 previous owners. :D</p>
 <p class="noteButton">If the URL field is left empty (with a name filled out), 
 the URL field will be filled with the current fanlisting's URL. <ins>Do not 
 change this.</ins> The script will read this as a no-link previous owner, and 
 will only display the previous owner's name as text, instead of a link.</p>
 <div class="previous">
<?php 
 if(empty($getItem->previous)) {
  $a = [];
 } else {
  $a = unserialize($getItem->previous, ['allowed_classes' => true]);
 }

 if((is_countable($a) ? count($a) : 0) > 0) {
  $pn   = 1;
	$text = '';
  foreach($a as $k => $v) {
	 $num   = ((is_countable($a) ? count($a) : 0) + 1) == $pn ? ' <span class="add">[+]</span>' : '';
	 $class = 'p' . $pn;
	 $text .= "  <div class=\"owner\" id=\"$class\">\n   "  . 
	 "<input name=\"pnumeric[]\" type=\"hidden\" value=\"$pn\">\n   " . 
	 '<p><label><strong>Name:</strong></label> <input name="pname[]"' .
	 " class=\"input1\" type=\"text\" value=\"$v\"></p>\n   " . 
	 '<p><label><strong>URL:</strong></label> <input name="purl[]"' .
	 " class=\"input6\" type=\"url\" value=\"$k\">$num</ap>\n";
	 $pn++;
	}
	echo str_replace('</ap>', " <span class=\"add\">[+]</span></p>\n  </div>", $text);
 } else {
  echo "  <div class=\"owner\" id=\"p1\">\n   "  . 
	"<input name=\"pnumeric[]\" type=\"hidden\" value=\"1\">\n   " . 
	'<p><label><strong>Name:</strong></label> <input name="pname[]"' .
	" class=\"input1\" type=\"text\"></p>\n   " . 
	'<p><label><strong>URL:</strong></label> <input name="purl[]"' .
	" class=\"input6\" type=\"url\"> <span class=\"add\">[+]</span></p>\n  </div>\n";
 }
?>
 </div>
</fieldset>

<fieldset>
 <legend>Forms</legend>
 <p class="tc">These can be optional, as these forms can be set by variables at the fanlisting 
 (see <a href="display_codes.php">Display Codes</a>).</p>
 <p><label><strong>Affiliates/Contact Form:</strong></label> 
 <input name="form-form" class="input1" type="text" value="<?php echo $getItem->form_form; ?>"></p>
 <p><label><strong>Delete Form:</strong><br>
 Deletion forms can be used for members to delete themselves from any listing they are listed at
 (password and e-mail address required).</label> 
 <input name="form-delete" class="input1" type="text" value="<?php echo $getItem->form_delete; ?>"></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><label><strong>Join Form:</strong></label> 
 <input name="form-join" class="input1" type="text" value="<?php echo $getItem->form_join; ?>"></p>
 <p><label><strong>Join Form: Rules:</strong></label>
 <textarea name="form-join_rules" cols="50" class="input1" rows="10" style="height: 200px; margin-right: 1%; width: 99%;">
<?php echo $getItem->form_join_rules; ?>
 </textarea></p>
 <p><label><strong>Reset Form:</strong><br>
 Reset forms are for members to reset their passwords.</label> 
 <input name="form-reset" class="input1" type="text" value="<?php echo $getItem->form_reset; ?>"></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><label><strong>Update Form:</strong></label> 
 <input name="form-update" class="input1" type="text" value="<?php echo $getItem->form_update; ?>"></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Manage Listing"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
  }
 
  elseif (isset($_GET['o']) && $_GET['o'] == 'templates') {
?>
<p>Although each template has a table of variables, you can also find a comprehensive sheet 
on the <a href="templates.php?g=templates">Templates page</a>.</p>

<form action="listings.php" enctype="multipart/form-data" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->id; ?>">
 <input name="opt" type="hidden" value="4">
</p>

<fieldset>
 <legend>Date and Description</legend>
 <p><label><strong>Date Format:</strong><br>Check out
 <a href="http://php.net/" title="External Link: 'date' at php.net">the Date manual &#187;</a> 
 for more formats.</label>
 <input name="dateformat" class="input1" type="text" value="<?php echo $getItem->date; ?>"></p>
 <p style="clear: both; margin: 0 0 1% 0;"></p>
 <p><strong>Description:</strong><br>
 <textarea name="desc" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->desc; ?></textarea></p>
</fieldset>

<fieldset> 
 <legend>Member Templates</legend>
 <table class="stats">
 <tbody><tr>
  <td class="t">{name}</td>
  <td class="d">Member name</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{email}</td>
  <td class="d">E-Mail address of member; will display if the member chose so, and will not if not</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{fave_field}</td>
  <td class="d">Will display favourite fields; please be aware if the variable
  is not set at the fanlisting, the fave field(s) will not display</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{url}</td>
  <td class="d">Member URL</td>
 </tr></tbody>
 </table>
 <p><strong>Members Template:</strong></p>
 <p class="noteButton"><span class="note">Note:</span> The members template for <samp>show-members.php</samp>.</p>
 <p class="tc"><textarea name="mem" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo str_replace('&', '&#38;', $getItem->members); ?></textarea></p>
 <p class="noteButton"><span class="note">Note:</span> The header and footer templates for the <samp>members template</samp>
 (above) are for <samp>show-members.php</samp>.</p>
 <p><strong>Members Template: Header:</strong></p>
 <p class="tc"><textarea name="mem-head" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->members_header; ?></textarea></p>
 <p><strong>Members Template: Footer:</strong></p>
 <p class="tc"><textarea name="mem-foot" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->members_footer; ?></textarea></p>
</fieldset>

<fieldset>
 <legend>Statistics Template</legend>
 <table class="stats">
 <tbody><tr>
  <td class="t">{affiliates}</td>
  <td class="d">Number of affiliates listed under the current listing</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{members}</td>
  <td class="d">Number of approved members</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{pending}</td>
  <td class="d">Number of pending members</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{newest}</td>
  <td class="d">Newest members to have joined the listing (returns names in
	the form of links (if a URL was provided))</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{since}</td>
  <td class="d">Date the listing was opened</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{updated}</td>
  <td class="d">"Last Updated" date</td>
 </tr></tbody>
 </table>
 <p class="nb"><strong>Stats Template:</strong><br>
 <textarea name="stats" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->stats; ?></textarea></p>
</fieldset>

<fieldset>
 <legend>Affiliates Template</legend>
 <table class="stats">
 <tbody><tr>
  <td class="t">{image}</td>
  <td class="d">Returns image location and <em>name</em>, not a 
	<samp>&#60;img&#62;</samp> tag</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{subject}</td>
  <td class="d">Subject name of the affiliate</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{url}</td>
  <td class="d">Affiliate URL</td>
 </tr></tbody>
 </table>
 <p class="nb"><strong>Affiliates Template:</strong><br>
 <textarea name="affiliates" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->affiliates; ?></textarea></p>
</fieldset>

<fieldset>
 <legend>Quotes Templates</legend>
 <table class="stats">
 <tbody><tr>
  <td class="t">{author}</td>
  <td class="d">Author name/title used</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{quote}</td>
  <td class="d">Quote</td>
 </tr></tbody>
 </table>
 <p class="nb"><strong>Quotes Template:</strong><br>
 <textarea name="quotes" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->quotes; ?></textarea></p>
</fieldset>

<fieldset>
 <legend>Wishlist: Granted</legend>
 <p class="noteButton"><span class="note">Note:</span> This description will 
 only be displayed if "Granted Wish?" is set to <em>Yes</em> in the 
 <a href="listings.php?g=manage&#38;d=2&#38;opt=options">Options</a> section and 
 you are displaying your granted wishes at your collective.</p>
 <p class="nb tc">
  <textarea name="wishlist" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->wishlist; ?>
  </textarea>
 </p>
</fieldset>

<fieldset>
 <legend>Updates Template</legend>
 <table class="stats">
 <tbody><tr>
  <td class="t">{categories}</td>
  <td class="d">Comma-separated category names are returned</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{comments}</td>
  <td class="d">Returns the number of comments and "Add Comment" link, if enabled; if comments are 
  disabled, it will return "Comments Disabled". Also returns journal links if crossposting was enabled 
  with the entry.</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{date}</td>
  <td class="d">Date of entry</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{entry}</td>
  <td class="d">Entry</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{permalink}</td>
  <td class="d">The link to the entry</td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{permalink_raw}</td>
  <td class="d">Returns the link <em>location</em> of the entry, e.g. <samp>http://mywebsite.com/?e=4</samp></td>
 </tr></tbody>
 <tbody><tr>
  <td class="t">{title}</td>
  <td class="d">Entry title</td>
 </tr></tbody>
 </table>
 <p class="noteButton"><span class="note">Note:</span> This template is used for 
 the updates feature &#8211; this template is not required to be edited unless 
 your updates feature is turned on.</p>
 <p class="nb">
  <textarea name="updates" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->updates; ?>
  </textarea>
 </p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Manage Listing">
 </p>
</fieldset>
</form>
<?php 
  }
	
  else { 
?>
<h3>Details</h3>
<form action="listings.php" enctype="multipart/form-data" method="post">
<p class="noMargin">
 <input name="id" type="hidden" value="<?php echo $getItem->id; ?>">
 <input name="opt" type="hidden" value="1">
</p>

<fieldset>
 <legend>Details</legend>
 <p><label><strong>Title:</strong></label> 
 <input name="title" class="input1" type="text" value="<?php echo $getItem->title; ?>"></p>
 <p><label><strong>Subject:</strong></label> 
 <input name="subject" class="input1" type="text" value="<?php echo $getItem->subject; ?>"></p>
 <p><label><strong>URI:</strong></label> 
 <input name="url" class="input1" type="url" value="<?php echo $getItem->url; ?>"></p>
</fieldset>

<fieldset>
 <legend>Categories and Opened Date</legend>
 <p><label><strong>Categories:</strong></label> 
 <select name="category[]" class="input1" multiple="multiple" size="10">
<?php 
 $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY `catname` ASC";
 $true = $scorpions->query($select);
 if($true == false) { 
  echo "<option value=\"0\">No Categories Available</option>\n";
 }

 else {
  while($getCat = $scorpions->obj($true, 0)) {
   $catid = $getCat->catid;
   $cats = explode('!', $getItem->category);
   echo '<option value="' . $getCat->catid . '"'; 
	 if(in_array($getCat->catid, $cats)) {
	  echo ' selected="selected"'; 
   }
	 echo '>' . $getCat->catname . "</option>\n";
	 $q2 = $scorpions->query("SELECT * FROM `$_ST[categories]` WHERE `parent` =" . 
   " '$catid' ORDER BY `catname` ASC");
	 while($getCat2 = $scorpions->obj($q2, 0)) {
    echo '<option value="' . $getCat2->catid . '"'; 
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
 <p><label><strong>Date Opened:</strong></label> <select name="month" class="input1" size="4">
<?php
 $dateArray = $get_date_array;
 $dateNow1 = date('m', strtotime($getItem->since));
 foreach($dateArray as $dA => $dA2) {
  echo '  <option value="' . $dA . '"';
  if($dA == $dateNow1) { 
   echo ' selected="selected"';
  }
  echo '>' . $dA2 . "</option>\n";
 }
?>
 </select></p>
 <p><label><strong>Day:</strong></label> 
 <input name="day" class="input1" type="number" value="<?php echo date('d', strtotime($getItem->since)); ?>" min="1" max="31"></p>
 <p><label><strong>Year:</strong></label> 
 <input name="year" class="input1" type="number" value="<?php echo date('Y', strtotime($getItem->since)); ?>"></p>
</fieldset>

<fieldset>
<legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Manage Listing"> 
  <input class="input2" type="reset" value="Reset Form">
 </p>
</fieldset>
</form>
<?php 
  }
?>
</div>
<?php 
 } 
 
 else {
?>
<p>Use the form below to choose a listing to manage. You can only manage 
<strong>one</strong> listing at a time.</p>

<form action="listings.php" method="get">
<p class="noMargin"><input name="g" type="hidden" value="manage"></p>

<fieldset> 
 <legend>Choose Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="d" class="input1">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $count = $scorpions->counts($select, 1);
 $true = $scorpions->query($select);
 if($true == false || $count->rows == 0) { 
  echo "  <option>Listings Unavailable</option>\n";
 }

 else {
  while($getItem = $scorpions->obj($true, 0)) {
   echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc">
  <input class="input2" type="submit" value="Manage Listing">
 </p>
</fieldset>
</form>
<?php 
 }
}

elseif (isset($_POST['action']) && $_POST['action'] === 'Manage Listing') {
 $id = $tigers->cleanMys($_POST['id']);
 $idArray = $wolves->listingsList();
 if(empty($id) || !is_numeric($id) || !in_array($id, $idArray)) {
  $tigers->displayError('Script Error', 'Your ID is empty, is not a number' . 
	' or is not a listing. This means you selected an incorrect listing or you\'re' . 
	' trying to access something that doesn\'t exist. Go back and try again.', false);
 }
 $opt = $tigers->cleanMys($_POST['opt']);
 $optArray = ['1', '2', '3', '4'];
 $optValue = ['2' => 'crosslist', '3' => 'options', '4' => 'templates'];
 if(empty($opt) || !is_numeric($id) || !in_array($opt, $optArray)) {
  $tigers->displayError('Script Error', 'You can only edit listing details,' . 
	' crosslisting, options and templates!', false);
 }
 $getlisting = $wolves->getListings($id, 'object');
 
 /* 
  *  Start editing our listing options right harrrr~ The first will be our 
  *  details (subject, title, et al.) 
  */  
 if($opt == '1') {
  $title = $tigers->cleanMys($_POST['title']);
  if($seahorses->getVar($id, 'title') != $title) {
   $seahorses->editListing($id, 'title', $title);
  }
  $subject = $tigers->cleanMys($_POST['subject']);
  if(empty($subject)) {
   $tigers->displayError('Form Error', 'Your <samp>subject</samp> field is' . 
   ' empty.', false);
  }
  if($seahorses->getVar($id, 'subject') != $subject) {
   $seahorses->editListing($id, 'subject', $subject);
  }
  $url = $tigers->cleanMys($_POST['url']);
if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
    $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
        ' not valid. Please supply a valid site URL or empty the field.', false);
}
	
	/** 
	 * If a previous owner has been supplied for this fanlisting, change the 
	 * previous owners with links to the fanlisting URL 
	 */ 
	$previousnow = $seahorses->getVar($id, 'previous');
	$previous    = unserialize($previousnow, ['allowed_classes' => true]);
	$newprevious = new stdClass();
	if(
	 (!empty($url) && $url != '' && $url != '0') && 
	 ($seahorses->getVar($id, 'url') != $url)
	) {
	 foreach($previous as $pk => $pv) {
	  $pkn = $pk == $seahorses->getVar($id, 'url') ? $url : $pk;
	  $newprevious->$pkn = $pv;
	 }
	 $newp = serialize($newprevious);
	 $seahorses->editListing($id, 'previous', $newp);
	}

	if(!empty($url) && $seahorses->getVar($id, 'url') != $url) {
   $seahorses->editListing($id, 'url', $url);
  }

  $category = $_POST['category'];
  if(empty($category)) {
   $tigers->displayError('Form Error', 'Your <samp>category</samp> field' . 
	 ' is empty.', false);
  }
  $category = array_map([$tigers, 'cleanMys'], $category);
  $cat = implode('!', $category);
  $cat = '!' . trim($cat, '!') . '!';
  if($seahorses->getVar($id, 'category') != $cat) {
   $seahorses->editListing($id, 'category', $cat);
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
  } elseif(!checkdate($month, $day, $year)) {
  $tigers->displayError('Form Error', 'The combination of day, month and year is incorrect, please check the date you entered.', false);
 }
  $date = $tigers->cleanMys($year . '-' . $month . '-' . $day);
  if($seahorses->getVar($id, 'since') != $date) {
   $seahorses->editListing($id, 'since', $date);
  }
 }

 /* 
  *  Now we're editing the crosslisting options :D 
  */ 
 elseif ($opt == '2') {
  $dbhost = $tigers->cleanMys($_POST['dbhost']);
  if($seahorses->getVar($id, 'dbhost') != $dbhost) {
   $seahorses->editListing($id, 'dbhost', $dbhost);
  }
  $dbuser = $tigers->cleanMys($_POST['dbuser']);
  if($seahorses->getVar($id, 'dbuser') != $dbuser) {
   $seahorses->editListing($id, 'dbuser', $dbuser);
  }
  $dbpass = $tigers->cleanMys($_POST['dbpass']);
  if($seahorses->getVar($id, 'dbpass') != $dbpass) {
   $seahorses->editListing($id, 'dbpass', $dbpass);
  }
  $dbname = $tigers->cleanMys($_POST['dbname']);
  if($seahorses->getVar($id, 'dbname') != $dbname) {
   $seahorses->editListing($id, 'dbname', $dbname);
  }
	$crosslist = $tigers->cleanMys($_POST['crosslist']);
  if(empty($crosslist) || !in_array($crosslist, array(1, 2))) {
   $tigers->displayError('Form Error', 'The <samp>crosslist</samp> field' . 
	 ' is invalid. Please enter "Yes" or "No".', false);
  }
	$clist = $crosslist == 1 || $crosslist == '1' ? '0' : 1;
  if($seahorses->getVar($id, 'dblist') != $clist) {
   $seahorses->editListing($id, 'dblist', $clist);
  }
  $crosslist_script = $tigers->cleanMys($_POST['crosslist_script']);
  $crosslist_script_array = array('n', 'enth', 'fabbase', 'listingadmin');
  if(empty($crosslist_script) || !in_array($crosslist_script, $crosslist_script_array)) {
   $tigers->displayError('Form Error', 'The <samp>crosslist script</samp>' . 
	 ' field is invalid. If you are not crosslisting, simply leave it at default' . 
	 ' (N/A).', false);
  }
  if($seahorses->getVar($id, 'dbtype') != $crosslist_script) {
   $seahorses->editListing($id, 'dbtype', $crosslist_script);
  }
  $table = $tigers->cleanMys($_POST['table']);
  if(empty($table) && $crosslist == 2) {
   $tigers->displayError('Form Error', 'If you are crosslisting to a' . 
	 ' script, please supply a table name.', false);
  }
  if($seahorses->getVar($id, 'dbtabl') != $table) {
   $seahorses->editListing($id, 'dbtabl', $table);
  }
  $afftable = $tigers->cleanMys($_POST['afftable']);
  if($seahorses->getVar($id, 'dbaffs') != $afftable) {
   $seahorses->editListing($id, 'dbaffs', $afftable);
  }
	$affhttp = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['affhttps']));
	$affpath = $tigers->cleanMys($_POST['affpaths']);
	if($crosslist == 2) {
	 if (!empty($affhttp) && !StringUtils::instance()->isUrlValid($affhttp)) {
	  $tigers->displayError('Form Error', 'The affiliates image URL you' .
	  ' provided is invalid!', false);
	 } elseif (substr($affhttp, -1) != '/' || substr($affhttp, -1) != '/') {
	  $tigers->displayError('Form Error', 'The affiliates images URL and/or' . 
	  ' affiliates images path do not have trailing slashes.', false);
	 }
	}
	if($seahorses->getVar($id, 'dbhttp') != $affhttp) {
   $seahorses->editListing($id, 'dbhttp', $affhttp);
  }
  if($seahorses->getVar($id, 'dbpath') != $affpath) {
   $seahorses->editListing($id, 'dbpath', $affpath);
  }
	$flid = $tigers->cleanMys($_POST['flid']);
  if(empty($flid) && $crosslist == 2) {
   $tigers->displayError('Form Error', 'If you are not crosslisting to a' . 
	 ' script, please supply a listing ID.', false);
  }
  if($seahorses->getVar($id, 'dbflid') != $flid) {
   $seahorses->editListing($id, 'dbflid', $flid);
  }
 }

 /** 
  * Regular ole options for Listings 8D 
  */ 
 elseif ($opt == '3') {
  $status = $tigers->cleanMys($_POST['status']);
  if(!ctype_digit($status) || strlen($status) > 1 || $status > 3) {
   $tigers->displayError('Form Error', 'Your <samp>status</samp> is' . 
   ' invalid.', false);
  }
  if($seahorses->getVar($id, 'status') != $status) {
   $seahorses->editListing($id, 'status', $status);
  }
  $granted = $tigers->cleanMys($_POST['granted']);
  if(!ctype_digit($granted)) {
   $tigers->displayError('Form Error', 'Your <samp>granted</samp>' . 
	 ' field is not a number.', false);
  } elseif (strlen($granted) > 1) {
   $tigers->displayError('Form Error', 'Your <samp>granted</samp>' . 
	 ' field must not exceed 1.', false);
  }
  if($seahorses->getVar($id, 'granted') != $granted) {
   $seahorses->editListing($id, 'granted', $granted);
  }
  $show_owned = $tigers->cleanMys($_POST['show_owned']);
  if(!ctype_digit($granted) || strlen($granted) > 1) {
   $tigers->displayError('Form Error', 'Your <samp>show on owned' . 
	 ' list</samp> field is invalid.', false);
  } 
  if($seahorses->getVar($id, 'show') != $show_owned) {
   $seahorses->editListing($id, 'show', $show_owned);
  }
  $markup    = $tigers->cleanMys($_POST['markup']);
  $markupArr = array('html', 'html5', 'xhtml');
  if(empty($markup)) {
   $tigers->displayError('Form Error', 'Your <samp>markup</samp>' . 
	 ' field is empty.', false);
  } elseif (!in_array($markup, $markupArr)) {
   $tigers->displayError('Form Error', 'Your <samp>markup</samp>' . 
	 ' field needs to be XHTML, HTML5 or HTML.', false);
  } 
  if($seahorses->getVar($id, 'markup') != $markup) {
   $seahorses->editListing($id, 'markup', $markup);
  }
  $change = $tigers->cleanMys($_POST['change']);
  $changeArray = array('add', 'edit', 'delete', 'none');
  if(!in_array($change, $changeArray)) {
   $tigers->displayError('Form Error', 'You can only add, edit and delete' . 
	 ' an image.', false);
  }
  $image = $_FILES['image'];
  $image_tag = substr(sha1(date('YmdHis')), 0, 12);
  if($change == 'add' || $change == 'edit') {
	 $imageinfo = getimagesize($_FILES['image']['tmp_name']);
	 $imagetype = $imageinfo[2];
   if($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
    $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' . 
		' <samp>.jpg</samp> and <samp>.png</samp> extensions are allowed when' . 
		' uploading an image.', false);
   }
  }
  if($change != 'none' && $change != 'add') {
   $sImage = $wolves->pullImage($id);
   $dImage = $seahorses->getOption('img_path') . $sImage;
   if($change == 'edit' || $change == 'delete') {
    if(!empty($sImage) && file_exists($dImage)) {
	   $delete = @unlink($dImage);
	  }
   }
  }
  $img_path = $seahorses->getOption('img_path');
  if(!empty($img_path)) {
   $path = $seahorses->getOption('img_path');
  } else {
   $path = str_replace('listings.php', '', $_SERVER['SCRIPT_FILENAME']);
  }
	$e = file_exists($path . $image['name']) ? $image_tag . '_' : '';
  $file = $scorpions->escape($e . $image['name']);
  if($change == 'add' || $change == 'edit') {
   if($change != 'delete' && $change != 'none') {
    $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
   }
  }
  if($change == 'add' || $change == 'edit') {
   $seahorses->editListing($id, 'image', $file);
  }
  if(isset($delete, $success)) {
	 if($delete && $success) {
	  echo '<p class="successButton"><span class="success">SUCCESS!</span>' . 
	  " Your old image was deleted and replaced with a new one!</p>\n";
	 }
  }
  elseif (isset($delete) && !isset($success)) {
	 if($delete) {
    echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
		" old image was deleted!</p>\n";
	 }
  }
  elseif (!isset($delete) && isset($success)) { 
	 if($success) {
	  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' . 
		" image was uploaded!</p>\n";
   }
  }
  $additional = array();
  if(isset($_POST['numero'])) {
   foreach($_POST['numero'] as $field => $value) {
    if(empty($_POST['favefield'][$field])) {
	   $members = $snakes->membersList($id);
	   foreach($members as $mid) {
        $member = $snakes->getMembers($mid, 'id', 'object', $id);
	    $erase  = $snakes->eraseFave($member->mExtra, $value);
		  if(isset($_POST['record']) && $_POST['record'] == 'yes') {
	     $update = "UPDATE `$_ST[members]` SET `mExtra` = '' WHERE `mID` = '$mid' LIMIT 1";
		  } else {
		   $update = "UPDATE `$_ST[members]` SET `mExtra` = '$erase' WHERE `mID` = '$mid' LIMIT 1";
		  }
		  $true = $scorpions->query($update);
	   }
	  } 
	  else {
     $additional[] = $snakes->additional($_POST['favefield'][$field], 'encode');
	  }
   }
  }
  /**   
   * This adds a favourite field to each member if adding a new field(s). :D 
   */ 
  $listingnow = $wolves->getListings($id, 'object');
  $favesfields = explode('|', $listingnow->fave_fields);
  $favesfields = $tigers->emptyarray($favesfields);
  if((count($additional) > (is_countable($favesfields) ? count($favesfields) : 0)) && ((is_countable($favesfields) ? count($favesfields) : 0) > 0)) {
   $changemem = $snakes->membersList($id);
   foreach($changemem as $cm) {
	  $getmem = $snakes->getMembers($cm, 'object');
	  $added = $snakes->addtoFave($additional, $getmem->mExtra);
	  if($added != false) {
	   $update = "UPDATE `$_ST[members]` SET `mExtra` = '$added' WHERE `mID` = '$cm' LIMIT 1";
	   $true = $scorpions->query($update);
	  }
	 }
  }
  if(isset($_POST['record']) && $_POST['record'] == 'yes') {
   $favouritefields = '';
  } else {if(count($additional) > 0 && !empty($additional)) {
$favouritefields = implode('|', $additional);
$favouritefields = '|' . trim($favouritefields, '|') . '|';
  } else {
   $favouritefields = '';
  }}
  if($seahorses->getVar($id, 'fave_fields') != $favouritefields) {
   $seahorses->editListing($id, 'fave_fields', $favouritefields);
  }
	
	/** 
	 * Previous owners si-tu-a-tion! 
	 */ 
	$powners = array();
	if(isset($_POST['pnumeric'])) {
   foreach($_POST['pnumeric'] as $field => $value) {
	  $pname = trim($_POST['pname'][$field]);
		$purl  = !empty($pname) && $pname != '' && $pname != '0' ? 
		(
		 empty($_POST['purl'][$field]) || $_POST['purl'][$field] == '' ? 
		 $getlisting->url : $tigers->cleanMys($_POST['purl'][$field])
		) : '';
	  if(
		 (isset($_POST['pname'][$field])) &&
		 (!empty($_POST['pname'][$field])) &&
		 ($_POST['pname'][$field] != '' && $_POST['pname'][$field] != ' ')
		) {
	   $pname          = $tigers->cleanMys($pname);
	   $powners[$purl] = $pname;
		}
	 }
	}
	if(count($powners) > 0 && !empty($powners)) {
	 $powners  = $tigers->emptymulti($powners);
	 $previous = serialize($powners);
	 if($seahorses->getVar($id, 'previous') != $previous) {
    $seahorses->editListing($id, 'previous', $previous);
   }
	}
	
	/** 
	 * Form URLs 
	 */ 
  $form_delete = $tigers->cleanMys($_POST['form-delete']);
  $form_delete = $tigers->formDefault('delete', $form_delete);
  if($seahorses->getVar($id, 'form_delete') != $form_delete) {
   $seahorses->editListing($id, 'form_delete', $form_delete);
  }
  $form_form = $tigers->cleanMys($_POST['form-form']);
  $form_form = $tigers->formDefault('form', $form_form);
  if($seahorses->getVar($id, 'form_form') != $form_form) {
   $seahorses->editListing($id, 'form_form', $form_form);
  }
  $form_join = $tigers->cleanMys($_POST['form-join']);
  $form_join = $tigers->formDefault('join', $form_join);
  if($seahorses->getVar($id, 'form_join') != $form_join) { 
   $seahorses->editListing($id, 'form_join', $form_join);
  }
	$form_join_rules = $tigers->cleanMys($_POST['form-join_rules'], 'n');
	if($seahorses->getVar($id, 'form_join_rules') != $form_join_rules) {
	 $seahorses->editListing($id, 'form_join_rules', $form_join_rules);
	}
  $form_reset = $tigers->cleanMys($_POST['form-reset']);
  $form_reset = $tigers->formDefault('reset', $form_reset);
  if($seahorses->getVar($id, 'form_reset') != $form_reset) {
   $seahorses->editListing($id, 'form_reset', $form_reset);
  }
  $form_update = $tigers->cleanMys($_POST['form-update']);
  $form_update = $tigers->formDefault('update', $form_update);
  if($seahorses->getVar($id, 'form_update') != $form_update) {
   $seahorses->editListing($id, 'form_update', $form_update);
  }
 }

 /**  
  * Aaaaaaaa-aaa-and templates! :D 
  */ 
 elseif ($opt == '4') {
  $dateformat = $tigers->cleanMys($_POST['dateformat']);
  if(empty($dateformat)) {
   $df = $tigers->cleanMys('F j, Y');
  } else {
   $df = $dateformat;
  }
  if($seahorses->getVar($id, 'date') != $df) {
   $seahorses->editListing($id, 'date', $df);
  }
  $desc = trim(htmlentities($_POST['desc'], ENT_QUOTES));
  if($seahorses->getVar($id, 'desc') != $desc) { 
   $seahorses->editListing($id, 'desc', $desc);
  }
  $stats = trim(htmlentities($_POST['stats'], ENT_QUOTES));
  if(empty($stats)) {
   $stats = trim($templatesExamples->stats);
  }
  if($seahorses->getVar($id, 'stats') != $stats) {
   $seahorses->editListing($id, 'stats', $stats);
  }
  $quotes = trim(htmlentities($_POST['quotes'], ENT_QUOTES));
  if($seahorses->getVar($id, 'quotes') != $quotes) {
   $seahorses->editListing($id, 'quotes', $quotes);
  }
  $affiliates = trim(htmlentities($_POST['affiliates'], ENT_QUOTES));
  if(empty($affiliates)) {
   $affiliates = trim($templatesExamples->affiliates);
  }
  if($seahorses->getVar($id, 'affiliates') != $affiliates) { 
   $seahorses->editListing($id, 'affiliates', $affiliates);
  }
  $wishlist = trim(htmlentities($_POST['wishlist'], ENT_QUOTES));
  if(empty($wishlist)) {
   $wishlist = trim($templatesExamples->wishlist);
  }
  if($seahorses->getVar($id, 'wishlist') != $wishlist) {
   $seahorses->editListing($id, 'wishlist', $wishlist);
  }
  $updates = trim(htmlentities($_POST['updates'], ENT_QUOTES));
  if(empty($updates)) {
   $updates = trim($templatesExamples->updates);
  }
  if($seahorses->getVar($id, 'updates') != $updates) {
   $seahorses->editListing($id, 'updates', $updates);
  }
  $member = trim($_POST['mem']);
  if(empty($member)) {
   $member = trim($templatesExamples->members);
  }
  if($seahorses->getVar($id, 'members') != $member) {
   $seahorses->editListing($id, 'members', $member);
  }
  $member_header = trim(htmlentities($_POST['mem-head'], ENT_QUOTES));
  if(empty($member_header)) {
   $member_header = trim($templatesExamples->members_header);
  } 
  if($seahorses->getVar($id, 'members_header') != $member_header) {
   $seahorses->editListing($id, 'members_header', $member_header);
  }
  $member_footer = trim(htmlentities($_POST['mem-foot'], ENT_QUOTES));
  if(empty($member_footer)) {
   $member_footer = trim($templatesExamples->members_footer);
  }
  if($seahorses->getVar($id, 'members_footer') != $member_footer) {
   $seahorses->editListing($id, 'members_footer', $member_footer);
  }
 }

 if($opt != '1') {
  echo $tigers->backLink('listings', $id, '', '&#38;o=' . $optValue[$opt]);
 }
 echo $tigers->backLink('listings', $id);
 echo $tigers->backLink('listings');
}

# -- Get New Listing ----------------------------------------------------------- 

elseif (isset($_GET['g']) && $_GET['g'] == 'new') {
?>
<form action="listings.php" enctype="multipart/form-data" method="post">
<fieldset>
 <legend>Details</legend>
 <p><label><strong>Title:</strong></label> <input name="title" class="input1" type="text"></p>
 <p><label><strong>Subject:</strong></label> <input name="subject" class="input1" type="text" required></p>
 <p><label><strong><abbr title="Uniform Resource Indentifier">URI</abbr>:</strong>
 </label> <input name="url" class="input1" type="url"></p>
 <p><label><strong>Status:</strong></label> 
 <input name="status" checked="checked" class="input3" type="radio" value="0"> Current
 <input name="status" class="input3" type="radio" value="1"> Upcoming 
 <input name="status" class="input3" type="radio" value="2"> Pending</p>
</fieldset>

<fieldset>
 <legend>Options</legend>
 <p><label><strong>Image:</strong></label> <input name="image" class="input1" type="file"></p>
 <p><label><strong>Granted Wish?</strong></label> <select name="granted" class="input1">
  <option value="1">Yes</option>
  <option selected="selected" value="0">No</option>
 </select></p>
 <p><label><strong>HTML Markup</strong><br>
 This option chooses what you want HTML mark-up you want for each listing.</label> 
 <select name="markup" class="input1">
  <option value="html">HTML</option>
  <option value="html5">HTML5</option>
  <option selected="selected" value="xhtml">XHTML</option>
 </select></p>
</fieldset>

<fieldset>
 <legend>Description and Templates</legend>
 <p><label><strong>Date Format:</strong><br>The date format is 
 <samp>F j, Y</samp> by default; check out 
 <a href="http://php.net/" title="External Link: 'date' at php.net">the Date manual &#187;</a>
 on the PHP.net website for more formats.</label>
 <input name="dateformat" class="input1" type="text" value="F j, Y"></p>
 <p class="clear"></p>
 <p><strong>Description:</strong><br>
 <textarea name="desc" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;"></textarea></p>
 <p><strong>Stats Template:</strong><br>
 <textarea name="stats" cols="50" rows="8" style="height: 150px; margin: 0 1% 0 0; width: 99%;"><?=$templatesExamples->stats;?></textarea></p>
</fieldset>

<fieldset>
 <legend>Categories and Opened Date</legend>
 <p><label><strong>Categor(y|ies):</strong></label> 
 <select name="category[]" class="input1" multiple="multiple" size="10" required>
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
	  $lions->getCatName($getItem2->parent) . ' &#187; ' . $getItem2->catname .
    "</option>\n";
	 }
  }
 }
?>
 </select></p>
 <p><label><strong>Date Opened *:</strong></label> <select name="month" class="input1" required>
<?php 
 $dateArray = $get_date_array;
 $dateNow1 = date('m');
 foreach($dateArray as $dA => $dA2) {
  echo '  <option value="' . $dA . '"';
  if($dA == $dateNow1) { 
   echo ' selected="selected"';
  }
  echo '>' . $dA2 . "</option>\n";
 }
?>
 </select></p>
 <p class="tc"><label><strong>Day *:</strong></label>
 <input name="day" class="input1" type="number" value="<?php echo date('d'); ?>" min="1" max="31" required></p>
 <p class="tc"><label><strong>Year *:</strong></label>
 <input name="year" class="input1" type="number" value="<?php echo date('Y'); ?>" required></p>
</fieldset>

<fieldset>
 <legend>Submit</legend>
 <p class="tc">
  <input name="action" class="input2" type="submit" value="Add Listing">
 </p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Add Listing') {
 $title = $tigers->cleanMys($_POST['title']);
 $subject = $tigers->cleanMys($_POST['subject']);
 if(empty($subject)) {
  $tigers->displayError('Form Error', 'Your <samp>subject</samp> field is empty.', false);
 }
$url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
    $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
        ' not valid. Please supply a valid site URL or empty the field.', false);
}
 $image = $_FILES['image'];
 $image_tag = substr(sha1(date('YmdHis')), random_int(0, 15), 12);
 if(!empty($_FILES['image']['name'])) {
	$imageinfo = getimagesize($_FILES['image']['tmp_name']);
	$imagetype = $imageinfo[2];
  if($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
   $tigers->displayError('FORM Error', 'Only <samp>.gif</samp>, <samp>.jpg' . 
   '</samp> and <samp>.png</samp> extensions allowed.', false);
  }
 }

 $category = $_POST['category'] ?? null;
 if(empty($category)) {
  $tigers->displayError('Form Error', 'Your <samp>category</samp> field' . 
	' is empty.', false);
 }
 $category = array_map(array($tigers, 'cleanMys'), $category);
 $status = $tigers->cleanMys($_POST['status']);
 if(!is_numeric($status) || strlen($status) > 1 || $status > 3) {
  $tigers->displayError('Form Error', 'Your <samp>status</samp> field' . 
	' is invalid.', false);
 } 
 $dateformat = $tigers->cleanMys($_POST['dateformat']);
 if(empty($dateformat)) {
  $df = 'F j, Y';
 } else {
  $df = $dateformat;
 }
 $desc = $tigers->cleanMys($_POST['desc'], 'n');
 $stats = $tigers->cleanMys($_POST['stats'], 'n');
 $year = $tigers->cleanMys($_POST['year'], 'n', 'y', 'n');
 $month = $tigers->cleanMys($_POST['month'], 'n', 'y', 'n');
 $day = $tigers->cleanMys($_POST['day'], 'n', 'y', 'n');
 if(empty($year) || empty($month) || empty($day)) {
  $tigers->displayError('Form Error', 'The <samp>date</samp> field is empty.', false);
 } elseif (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
  $tigers->displayError('Form Error', 'The <samp>date</samp> field is not digits.', false);
 } elseif (strlen($year) > 4) {
  $tigers->displayError('Form Error', 'The <samp>year</samp> field needs' . 
	' to be the length of 4 digits.', false);
 } elseif (strlen($month) > 2 || strlen($day) > 2) {
  $tigers->displayError('Form Error', 'The <samp>month or day</samp> field' . 
	' needs to be the length of 2 digits.', false);
 } elseif(!checkdate($month, $day, $year)) {
  $tigers->displayError('Form Error', 'The combination of day, month and year is incorrect, please check the date you entered.', false);
 }
 $date = $tigers->cleanMys($year . '-' . $month . '-' . $day, 'y', 'n');
 $granted = $tigers->cleanMys($_POST['granted']);
 if(!is_numeric($granted) || strlen($granted) > 1) {
  $tigers->displayError('Script Error', 'Your <samp>granted</samp> field' . 
	' is invalid.', false);
 } 
 $markup = $tigers->cleanMys($_POST['markup']);
 if(empty($markup) || !array_key_exists($markup, $get_markup_array)) {
  $tigers->displayError('Script Error', 'Your <samp>markup</samp> field is' . 
	' invalid.', false);
 }
 
 $img_path = $seahorses->getOption('img_path');
 if(!empty($img_path) && is_dir($img_path)) {
  $path = $seahorses->getOption('img_path');
 } else {
  $path = str_replace('listings.php', '', $_SERVER['SCRIPT_FILENAME']);
 }

 $e = file_exists($path . $image['name']) ? $image_tag . '_' : '';
 $file = $scorpions->escape($e . $image['name']);
 if(!empty($image)) {
  $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
 }

 $cat = implode('!', $category);
 $cat = '!' . trim($cat, '!') . '!';

 $insert = "INSERT INTO `$_ST[main]` (`title`, `subject`, `url`, `image`," .
 ' `category`, `status`, `show`, `dbhost`, `dbuser`, `dbpass`, `dbname`,' .
 ' `dbtype`, `dbtabl`, `dblist`, `dbaffs`, `dbflid`, `dbhttp`, `dbpath`,' .
 ' `desc`, `stats`, `affiliates`, `wishlist`, `quotes`, `members`,' .
 ' `members_header`, `members_footer`, `updates`, `form_delete`, `form_form`,' .
 ' `form_join`, `form_join_rules`, `form_reset`, `form_update`, `fave_fields`,' .
 " `date`, `since`, `updated`, `granted`, `markup`, `previous`) VALUES ('$title', '$subject'," .
 " '$url', '$file', '$cat', '$status', 0, '', '', '', '', 'enth', '', 0, '', 0," .
 " '', '', '$desc', '$stats', '$templatesExamples->affiliates', '$templatesExamples->wishlist', '', '$templatesExamples->members', '$templatesExamples->members_header', '$templatesExamples->members_footer', '$templatesExamples->updates', '', '', '', '', '', ''," .
 " '', '$df', '$date', '1970-01-01', '$granted', '$markup', '')";
 $true = $scorpions->insert($insert);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to add' . 
	' the listing to the database.|Make sure your listings table exists.', 
	true, $insert);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">Success!</span> Your' .
  " listing was added to the database!</p>\n";
	if(isset($success) && $success) {
     echo '<p class="successButton"><span class="success">Success!</span> Your' .
   " listing image was uploaded!</p>\n";
    }
  echo $tigers->backLink('listings');
 }
}

# -- Now let's delete a listing, yeah~? ---------------------------------------- 

elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
 if(empty($_GET['d']) || !isset($_GET['d'])) {
?>
<form action="listings.php" method="get">
<p class="noMargin"><input name="g" type="hidden" value="erase"></p>

<fieldset> 
 <legend>Choose Listing</legend>
 <p><label><strong>Listing:</strong></label> <select name="d" class="input1" size="15">
<?php 
 $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
 $count = $scorpions->counts($select, 1);
 $true = $scorpions->query($select);
 if($true == false || $count->rows == 0) { 
  echo "  <option value=\"0\">No Listings Available</option>\n";
 }

 else {
  while($getItem = $scorpions->obj($true, 0)) {
   echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc"><input class="input2" type="submit" value="Delete Listing"></p>
</fieldset>
</form>
<?php 
 }

 if(!empty($_GET['d']) && is_numeric($_GET['d'])) {
  $id = $tigers->cleanMys((int)$_GET['d']);

  $select = "SELECT * FROM `$_ST[main]` WHERE `id` = '$id' LIMIT 1";
  $true = $scorpions->query($select);
  if($true == false) {
   $tigers->displayError('Database Error', 'The script was unable to select' . 
	 ' that specific listing.|Make sure the ID is not empty and the listings table' . 
	 ' exists.', true, $select);
  }
  $getItem = $scorpions->obj($true, 0);
?>
<p>You are about to delete the <strong><?php echo $getItem->subject; ?></strong> 
listing; please be aware that once you delete a listing, it is gone forever. 
<em>This cannot be undone!</em> To proceed, click the "Delete Listing" button.</p>

<form action="listings.php" method="post">
<p class="noMargin"><input name="id" type="hidden" value="<?php echo $getItem->id; ?>"></p>

<fieldset>
 <legend>Delete Listing</legend>
 <p class="tc">Deleting the <strong><?php echo $getItem->subject; ?></strong> 
 fanlisting &#8211;</p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Delete Listing"></p>
</fieldset>
</form>
<?php 
 } 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Listing') {
 $id = $tigers->cleanMys($_POST['id']);
 if(empty($id) || !is_numeric($id)) {
  $tigers->displayError('Script Error', 'Your ID is empty. This means' . 
	' you selected an incorrect listing or you\'re trying to access something' . 
	' that doesn\'t exist. Go back and try again.', false);
 }
 
 $sImage = $wolves->pullImage($id);
 $dImage = $seahorses->getOption('img_path') . $sImage;
 if(file_exists($dImage) && !empty($sImage)) {
  $eraser = @unlink($dImage);
 }

 $cMembers = $snakes->countMembers($id);
 $query = "DELETE FROM `$_ST[members]` WHERE `fNiq` = '$id' LIMIT $cMembers";
 if($cMembers > 0) {
  $result = $scorpions->query($query);
 }

 $delete = "DELETE FROM `$_ST[main]` WHERE `id` = '$id' LIMIT 1";
 $true = $scorpions->query($delete);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to delete' . 
  ' the listing.|Make sure your ID is not empty and your listings table exists.', 
	true, $delete);
 }  

 if($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
	" listing was deleted!</p>\n";
 }
 if(isset($result) && $result === true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> The' .
	" members from the deleted listing were deleted!</p>\n";
 }
 
 echo $tigers->backLink('listings');
}

/** 
 * Index 
 */ 
else {
?>
<p>Welcome to <samp>listings.php</samp>, where you'll be able to add a listing, 
and edit and/or delete your current ones! Below is the list of your listings. 
To edit or delete, click the "Edit" or "Delete" buttons by the appropriate 
listing.</p>

<h3>Search Listings</h3>
<div class="height">
<form action="listings.php" method="get">
<p class="noMargin"><input name="g" type="hidden" value="searchCategories"></p>

<fieldset class="lap-two">
 <legend>Search Categories</legend>
 <p class="tc"><select name="c" class="input1">
<?php
 $select = "SELECT * FROM `$_ST[categories]` ORDER BY `catname` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  echo "  <option>No Category Available</option>\n";
 }

 else {
  while($getTion = $scorpions->obj($true, 0)) {
   echo '  <option value="' . $getTion->catid . '">' . $getTion->catname . "</option>\n";
  }
 }
?>
 </select></p>
 <p class="tc"><input class="input2" type="submit" value="Search Category"></p>
</fieldset>
</form>

<form action="listings.php" method="get">
<p class="noMargin"><input name="g" type="hidden" value="searchStatus"></p>

<fieldset class="lap-two">
 <legend>Search Status</legend>
 <p class="tc"><select name="s" class="input1">
  <option value="1">Current</option>
  <option value="2">Upcoming</option>
  <option value="3">Pending</option>
 </select></p>
 <p class="tc"><input class="input2" type="submit" value="Search Status"></p>
</fieldset>
</form>
</div>
<?php 
 $select = "SELECT * FROM `$_ST[main]`";
 if(isset($_GET['g'])) {
	if(isset($_GET['g']) && $_GET['g'] == 'searchCategories') {
	 $catid   = $tigers->cleanMys($_GET['c']);
	 $select .= " WHERE `category` LIKE '%!$catid!%'";
	} elseif (isset($_GET['g']) && $_GET['g'] == 'searchStatus') {
	 $statusid = $tigers->cleanMys($_GET['s']) - 1;
	 $select  .= " WHERE `status` = '$statusid'";
	}
 } 
 $select .= " ORDER BY `subject` ASC LIMIT $start, $per_page";
 $true    = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select' . 
	' the listings from the database.|Make sure your table exists.', true, $select);
 }
 $count = $scorpions->total($true);

 if($count > 0) {
?>

<table class="index">
<thead><tr>
 <th>ID</th>
 <th>Subject</th>
 <th>Image</th>
 <th>Category</th>
 <th>Affiliate/Member Counts</th>
 <th>Action</th>
</tr></thead>
<?php 
  while($getItem = $scorpions->obj($true, 0)) {
   $imp = strpos($my_imagesh, '/') !== false ? $seahorses->getOption('img_http') . 
   $getItem->image : $seahorses->getOption('img_http') . '/' . $getItem->image;
   $status  = $getItem->status;
   $subject = $getItem->subject;
   $url     = $getItem->url;

   if($status == 0 || $status == '0') {
    $g = "<a href=\"$url\">$subject</a>";
   } elseif ($status == 1 || $status == '1') {
    $g = "<strong>$subject</strong>";
   } else {
	  $g = "<em>$subject</em>";
	 }

   if(
	  !empty($getItem->image) && 
	  file_exists($seahorses->getOption('img_path') . $getItem->image)
	 ) {
    $imgNow = '<img src="' . $imp . "\" alt=\"\">\n";
   } else {
    $imgNow = '';
   }
 
   if($status == '0') {
    $statusSt = 'Current';
   } elseif ($status == 1) {
    $statusSt = 'Upcoming';
   } elseif ($status == 2) {
    $statusSt = 'Pending';
   } else {
    $statusSt = 'Status Not Set';
   }
	 
	 $qw = isset($_GET['g']) ? ($_GET['g'] == 'searchCategories' ? '&#38;c=' . 
	 $catid : '&#38;s=' . $statusid) : '';
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->id; ?></td>
 <td class="tc"><?php echo $g; ?></td>
 <td class="tc"><?php echo $imgNow; ?></td>
 <td class="tc"><?php echo $lions->pullCatNames($getItem->category, '!'); ?></td>
 <td class="tc">
<?php
   $affcount = $rabbits->countAffiliates($getItem->id);
   $memcount = $snakes->getMemberCount($getItem->id);
?>
<?php echo $affcount; ?> Affiliate<?php echo $affcount == 1 ? '' : 's'; ?><br />
<?php echo $memcount; ?> Total Member<?php echo $memcount == 1 ? '' : 's'; ?>
 </td>
 <td class="floatIcons tc">
  <a href="listings.php?g=manage&#38;d=<?php echo $getItem->id . $qw; ?>">
	 <img src="img/icons/edit.png" alt="">
	</a> 
  <a href="listings.php?g=erase&#38;d=<?php echo $getItem->id; ?>">
	 <img src="img/icons/delete.png" alt="">
	</a>
 </td>
</tr></tbody>
<?php 
  } 
  echo "</table>\n\n";

  echo '<p id="pagination">';
	if(isset($_GET['g'])) {
	 if(isset($_GET['g']) && $_GET['g'] == 'searchCategories') {
	  $p = 'categories';
		$s = $catid;
	 } elseif (isset($_GET['g']) && $_GET['g'] == 'searchStatus') {
	  $p = 'status';
		$s = $statusid - 1;
	 }
	} else {
	 $p = '';
	 $s = '';
	}
	$total = is_countable($wolves->listingsList('subject', $s, $p)) ? count($wolves->listingsList('subject', $s, $p)) : 0;
  $pages = ceil($total/$per_page);
	
	$q = 'listings.php?';
	if(isset($_GET['g'])) {
	 if(isset($_GET['g']) && $_GET['g'] == 'searchCategories') {
	  $q .= 'g=searchCategories&#38;c=' . $catid . '&#38;';
	 } elseif (isset($_GET['g']) && $_GET['g'] == 'searchStatus') {
	  $q .= 'g=searchStatus&#38;s=' . $statusid . '&#38;';
	 }
	}

  if($page > 1) {
   echo '<a href="' . $q . 'p=' . ($page - 1) . '">&#171; Previous</a> ';
  } else {
   echo '&#171; Previous ';
  }

  for($i = 1; $i <= $pages; $i++) {
   if($page == $i) {
    echo $i . ' ';
   } else {
    echo '<a href="' . $q . 'p=' . $i . '">' . $i . '</a> ';
   }
  }

  if($page < $pages) {
   echo '<a href="' . $q . 'p=' . ($page + 1) . '">Next &#187;</a>';
  } else {
   echo 'Next &#187;';
  }
  echo "</p>\n";
 } 

 else {
  echo "\n<p class=\"tc\">Currently no listings!</p>\n";
 }
}


require('footer.php');
