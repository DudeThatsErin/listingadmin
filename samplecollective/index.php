<?php
require("header.php");
?>
<h3>Welcome!</h3>
<p>This is Erin's <a href="https://thefanlistings.org/" target="_blank">Fanlisting</a> Collective. This is where she lists fanlistings she owns as well as fanlistings she has joined. Have fun browsing. If you run into any errors, please let her know!</p>
<?php
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
require(STPATH . "show-c-stats.php");
# -----------------------------------------------------------
$show_joined = 'y';
$show_joined_number = 1;
$show_joined_rotate = 'y';
$show_owned = 'y';
$show_owned_number = 1;
$show_owned_rotate = 'y';
# -----------------------------------------------------------
require(STPATH . "show-random.php");
# -----------------------------------------------------------
?>
<?php
# -----------------------------------------------------------
require("footer.php");
