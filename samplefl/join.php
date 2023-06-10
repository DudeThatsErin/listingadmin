<?php
require_once("pro.php");
require("header.php");
?>
<h3>Join the Listing</h3>
<?php 
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
$fKey = 1;
$fave_field = "Fave Field 0|Fave Field 1";
# -----------------------------------------------------------
require(STPATH . "show-join.php");
# -----------------------------------------------------------
?> 
<?php 
require("footer.php");
?>
