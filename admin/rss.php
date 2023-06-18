<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @contributor      Erin <dudethatserin@outlook.com> https://github.com/DudeThatsErin/listingadmin
 * @version          Erin's Fork
 */
require('rats.inc.php');
require_once('inc/fun.inc.php');
require_once('inc/fun-listings.inc.php');
require_once('inc/fun-misc.inc.php');
require_once('inc/fun-updates.inc.php');
require('vars.inc.php');

$c = !isset($_GET['c']) ? '' : (in_array($_GET['c'], $wolves->listingsList())
? $tigers->cleanMys($_GET['c']) : '');

$turtles->getRSS($c);
