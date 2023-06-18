<?php
require("header.php");
# -----------------------------------------------------------
require("jac.inc.php");
# -----------------------------------------------------------
$fKey = 0;
$donor_link = 'y';
// $pretty_urls = 'y';
// $set_query = 'images/';
$show_all = 'y';
$show_number = 'y';
$sort_by = 'id';
# -----------------------------------------------------------
require(STPATH . "show-codes.php");
# -----------------------------------------------------------
require(STPATH . "show-codes-form.php");
# -----------------------------------------------------------
require("footer.php");
