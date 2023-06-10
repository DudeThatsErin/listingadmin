<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <show-c-stats.php>
 * @version          Robotess Fork
 */

require_once('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-members.inc.php');
require_once('fun-misc.inc.php');
require(MAINDIR . 'vars.inc.php');

$format = html_entity_decode($seahorses->getTemplate('collective_stats_template'));
$format = str_replace('{current}', $seahorses->getCount('current', 'y'), $format);
$format = str_replace('{upcoming}', $seahorses->getCount('upcoming', 'y'), $format);
$format = str_replace('{pending}', $seahorses->getCount('pending', 'y'), $format);
$format = str_replace('{mApproved}', $seahorses->memberCount(0), $format);
$format = str_replace('{mPending}', $seahorses->memberCount(1), $format);
$format = str_replace('{joined}', $seahorses->getCount('joined'), $format);
$format = str_replace('{newest}', $wolves->getNewest(), $format);

echo $format;
