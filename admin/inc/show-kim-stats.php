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
require('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('class-kimadmin.inc.php');
require_once('fun.inc.php');
require_once('fun-misc.inc.php');
require(MAINDIR . 'vars.inc.php');

$format = html_entity_decode($seahorses->getTemplate('kim_stats_template'));
$format = str_replace('{members}', $kimadmin->kimCount(0), $format);
$format = str_replace('{pending}', $kimadmin->kimCount(1), $format);
$format = str_replace('{updated}', $kimadmin->kimUpdate(), $format);

echo $format;
