<?php
/** 
 * @copyright 2007 
 * @license   GPL Version 3; BSD Modified 
 * @author    Tess <treibend@gmail.com> 
 * @file      <templates.php> 
 * @since     September 2nd, 2010 
 * @version   2.1+     
 */ 
$getTitle = 'Templates';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if(isset($_GET['name']) && !empty($_GET['name'])) {
 $nameid = $tigers->cleanMys($_GET['name']);
 if(empty($nameid) && !in_array($nameid, $jaguars->emailList())) {
  $tigers->displayError('Script Error', 'The <samp>template</samp>' . 
	' field is empty.', false);
 }
?>
<p>Do not wrap your template in bigger block elements. If you're using lists 
(<samp>&lt;li&gt;</samp>), do <em>not</em> wrap your template in (un)ordered 
lists (<samp>&lt;ol&gt;/&lt;ul&gt;</samp>); if you're using paragraphs 
(<samp>&lt;p&gt;</samp>), do <em>not</em> wrap your template in a DIV tag 
(<samp>&lt;div&gt;</samp>)! The script will do this for you.</p>
<p>Below are variables for your template. To use them is simple enough: how you 
want your affiliates displayed, replace the titles and URLs with the following 
variables. You can <em>not</em> use variables that are not listed here.</p>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{name}</td>
 <td class="d">Member name</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{collective_name}</td>
 <td class="d">Collective name</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{collective_url}</td>
 <td class="d">Collective <abbr title="Uniform Resource Indentifier">URI</abbr></td>
</tr></tbody>
<tbody><tr>
 <td class="t">{listing}</td>
 <td class="d">Listing (by subject)</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{listing_url}</td>
 <td class="d">Listing's URL</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{owner}</td>
 <td class="d">Owner name</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{subject}</td>
 <td class="d">Subject of the affiliate/member</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{title}</td>
 <td class="d">Title of the affiliate/member</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{url}</td>
 <td class="d"><abbr title="Uniform Resource Indentifier">URI</abbr> of the affiliate/member</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{image}</td>
 <td class="d">Image (Affiliates only!)</td>
</tr></tbody>
</table>

<form action="templates_emails.php" method="post">
<p class="noMargin">
 <input name="name" type="hidden" value="<?php echo $nameid; ?>">
</p>

<fieldset>
 <legend>Edit '<?php echo $jaguars->getEmailTitle($nameid); ?>' Template</legend>
 <p class="tc">
  <textarea name="template" cols="50" rows="8" style="height: 300px; margin: 0 1% 0 0; width: 99%;">
<?php echo $jaguars->getEmailTemplate($nameid); ?></textarea>
 </p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Edit Template"></p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action'])) {
 $name = $tigers->cleanMys($_POST['name']);
 if(empty($name) && !in_array($name, $jaguars->emailList())) {
  $tigers->displayError('Script Error', 'The <samp>template name</samp>' . 
	' field is empty.', false);
 }
 $template = $tigers->cleanMys($_POST['template'], 'n', 'n');
 if(empty($template)) {
  $tigers->displayError('Script Error', 'The <samp>template</samp> field' . 
	' is empty.', false);
 }

 $update = "UPDATE `$_ST[templates_emails]` SET `template` = '$template' WHERE" . 
 " `name` = '$name' LIMIT 1";
 $scorpions->query("SET NAMES 'utf8';");
 $true = $scorpions->query($update);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to edit the' . 
  ' template.', true, $update);
 } else {
  echo $tigers->displaySuccess("The <samp>$name</samp> template was updated! :D");
 }

 echo $tigers->backLink('temp_e');
}

/** 
 * Index 
 */ 
else {
?>
<p>Welcome to <samp>templates-email.php</samp>, the outlet to editing your email templates for various aspects 
of the script. Currently, you are only able to edit the current existing the templates.</p>
<?php 
 $select = "SELECT * FROM `$_ST[templates_emails]` ORDER BY `title` ASC";
 $true   = $scorpions->query($select);
 $count  = $scorpions->total($true);

 if($count > 0) {
?>
<table class="index">
<thead><tr>
 <th>Title</th>
 <th>Title Slug</th>
 <th>Action</th>
</tr></thead>
<?php 
  while($getItem = $scorpions->obj($true)) {
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->title; ?></td>
 <td class="tc"><?php echo $getItem->name; ?></td>
 <td class="floatIcons tc">
  <a href="templates_emails.php?name=<?php echo $getItem->name; ?>">
   <img src="img/icons/edit.png" alt="">
  </a>
 </td>
</tr></tbody>
<?php 
  }
?>
</table>
<?php 
 } else {
  echo "<p class=\"tc\">Currently no e-mail templates!</p>\n";
 }
}

require('footer.php');
