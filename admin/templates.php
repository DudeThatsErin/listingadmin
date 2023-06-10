<?php
/** 
 * @copyright 2007 
 * @license   GPL Version 3; BSD Modified 
 * @author    Tess <treibend@gmail.com> 
 * @file      <templates.php> 
 * @since     September 16th, 2010 
 * @version   1.0    
 */ 
$getTitle = 'Templates';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

/** 
 * Edit Template 
 */ 
if(isset($_GET['g']) && $_GET['g'] == 'old') {
 if(!isset($_GET['n']) || !in_array($_GET['n'], $seahorses->templatesList())) {
  $tigers->displayError('Script Error', 'The template you\'ve chosen to edit' . 
  ' is invalid. Go back and try again? :D', false);
 }

 $name    = $tigers->cleanMys($_GET['n']);
 $getItem = $seahorses->getTemplate($name, 'object');
?>
<h3><?php echo $getItem->title; ?></h3>
<?php  
 $seahorses->templates($getItem->name);
?>

<h3>Form</h3>
<form action="templates.php" method="post">
<p class="noMargin"><input name="name" type="hidden" value="<?php echo $getItem->name; ?>"></p>

<fieldset>
 <legend>Edit Template</legend>
 <p><label><strong>Template Name:</strong></label> 
 <span style="padding: 0 1%; width: 48%"><samp><?php echo $getItem->name; ?></samp></span></p>
 <p><label><strong>Template Title:</strong></label> 
 <input name="title" class="input1" type="text" value="<?php echo $getItem->title; ?>"></p>
 <p class="tc"><strong>Template</strong><br>
  <textarea name="template" cols="50" rows="16" style="height: 150px; width: 100%;">
<?php echo $getItem->template; ?>
  </textarea>
 </p>
 <p class="tc"><input name="action" class="input2" type="submit" value="Edit Template"></p>
</fieldset>
</form>
<?php 
}

elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Template') {
 $name = $tigers->cleanMys($_POST['name']);
 if(empty($name)) {
  $tigers->displayError('Form Error', 'Your template name is empty. This' . 
	' means you selected an incorrect template or you\'re trying to access something' . 
	' that doesn\'t exist. Go back and try again.', false);
 } 
 $title = $tigers->cleanMys($_POST['title']);
 if(empty($title)) {
  $tigers->displayError('Form Error', 'Your <samp>template title</samp> is empty.', false);
 } 
 $template = $tigers->cleanMys($_POST['template'], 'n', 'y', 'n');
 if(empty($template)) {
  $tigers->displayError('Form Error', 'Your <samp>template</samp> is empty.', false);
 }

 $update = "UPDATE `$_ST[templates]` SET `title` = '$title', `template` =" . 
 " '$template' WHERE `name` = '$name' LIMIT 1";
 $scorpions->query("SET NAMES 'utf8';");
 $true = $scorpions->query($update);

 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to edit the' . 
  ' template.', true, $update);
 } elseif ($true == true) {
  echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
  " template was edited!</p>\n";
  echo $tigers->backLink('temp');
 }
}

/* 
 *  @section   Index 
 */ 
else {
?>
<p>Welcome to <samp>templates.php</samp>, the outlet to editing your templates 
for various aspects of the script. Currently, you are only able to edit the 
current existing the templates.</p>
<p class="noteButton">You can view the template variables for each template when 
you edit a template. An example will be provided for each template as well.</p>

<table class="index">
<thead><tr>
 <th>Name</th>
 <th>Title</th>
 <th>Action</th>
</tr></thead>
<?php 
 $select = "SELECT * FROM `$_ST[templates]` ORDER BY `title` ASC";
 $true = $scorpions->query($select);
 if($true == false) {
  $tigers->displayError('Database Error', 'The script was unable to select the' . 
  ' templates from the database.', true, $select);
 }

 while($getItem = $scorpions->obj($true)) {
?>
<tbody><tr>
 <td class="tc"><?php echo $getItem->name; ?></td>
 <td class="tc"><?php echo $getItem->title; ?></td>
 <td class="floatIcons tc">
  <a href="templates.php?g=old&amp;n=<?php echo $getItem->name; ?>">
   <img src="img/icons/edit.png" alt="">
  </a> 
 </td>
</tr></tbody>
<?php 
}
?>
</table>
<?php 
}

require('footer.php');
