<?php
require_once("pro.php");
require("header.php");
?>
<h3>Buttons</h3>
<?php 
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
$fKey = 1;
$donor_link = 'y';
$show_all = 'n';
$show_number = 'y';
$sort_by = 'id';
# -----------------------------------------------------------
require(STPATH . "show-codes.php");
# -----------------------------------------------------------
require(STPATH . "show-codes-form.php");
# -----------------------------------------------------------
?>
<?php 
require("footer.php");
?>
