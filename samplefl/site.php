<?php
require_once("pro.php");
require("header.php");
?>
<h3>Site</h3>
<p><strong>Example</strong> was opened in 2007, and was updated in February 
2012.</p>

<h4>Affiliate</h4>
<?php 
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
$fKey = 1;
$fWhich = 'list';
# -----------------------------------------------------------
require(STPATH . "show-form.php");
# -----------------------------------------------------------
?> 

<h4>Contact</h4>
<?php 
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
$fKey = 1;
$fClose = 'n';
$fWhich = 'form';
$turnAff = 1;
$turnCon = 1;
# -----------------------------------------------------------
require(STPATH . "show-form.php");
# -----------------------------------------------------------
?> 
<?php 
require("footer.php");
?>
