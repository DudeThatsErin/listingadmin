<?php
require("header.php");
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
require(STPATH . "show-c-stats.php");
# -----------------------------------------------------------
$show_joined = 'y';
$show_joined_number = 1;
$show_joined_rotate = 'y';
$show_owned = 'n';
$show_owned_number = 1;
$show_owned_rotate = 'y';
# -----------------------------------------------------------
require(STPATH . "show-random.php");
# -----------------------------------------------------------
# -----------------------------------------------------------
require("footer.php");
