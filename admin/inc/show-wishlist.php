<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <show-wishlist.php>
 * @version          Robotess Fork
 */

require('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-categories.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-members.inc.php');
require_once('fun-misc.inc.php');
require_once('fun-wishlist.inc.php');

/**
 * Get variables and options!
 */
$options = (object)array();

if (!isset($status) || empty($status)) {
    $tigers->displayError('Script Error', 'It appears your status is not set.|' .
        'In order to include your wishlist, you must set a status.', false);
}

$query = $tigers->cleanMys($_SERVER['QUERY_STRING']);
if (isset($query) && !empty($query)) {
    $options->url = '?' . str_replace('&', '&amp;', $query) . '&amp;';
} else {
    $options->url = '?';
}

if (isset($show_desc)) {
    if (empty($show_desc)) {
        $options->desc = 'no_desc';
    } elseif ($show_desc == 'y') {
        $options->desc = 'desc';
    } elseif ($show_desc == 'n') {
        $options->desc = 'no_desc';
    }
} else {
    $options->desc = 'no_desc';
}

/**
 * If a query has been access, assess that shit and display it \o/
 */
if (isset($_GET['q']) && in_array($_GET['q'], $mermaids->wishlistList())) {
    $id = $tigers->cleanMys((int)$_GET['q']);
    $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wID` = '$id' LIMIT 1";
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to pull the' .
            ' wishlist from the database!', false);
    } else {
        echo "<div id=\"sep\">\n";
        while ($getItem = $scorpions->obj($true)) {
            echo $mermaids->getTemplate_Wishlist($getItem->wID, 'wishlist_query_template');
        }
        echo "</div>\n";
    }
} /**
 * Default: get the top, custom, list, or granted wishlist :D
 */
else if ($status == 'top') {
    $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wType` = 'top' ORDER BY `wSubject` ASC";
    $count = $scorpions->counts($select);
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script unable to select your top' .
            ' wishes.|Make sure your wishlist table exists.', false);
    }
    if ($count->rows > 0) {
        while ($getItem = $scorpions->obj($true)) {
            echo $mermaids->getTemplate_Wishlist($getItem->wID, 'wishlist_top_template') . "\n";
        }
    } else {
        echo "<p class=\"tc\">Currently no top wishes!</p>\n";
    }
} elseif ($status == 'granted') {
    if ($seahorses->getOption('wishlist_granted') == 'y') {
        $select = "SELECT * FROM `$_ST[main]` WHERE `granted` = '1' AND `status`" .
            " = '0' ORDER BY `subject` ASC";
    } elseif ($seahorses->getOption('wishlist_granted') == 'n') {
        $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wType` = 'granted' ORDER BY" .
            ' `wSubject` ASC';
    }
    $count = $scorpions->counts($select);
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script unable to select your' .
            ' granted wishes.', false);
    }
    if ($count->rows > 0) {
        while ($getItem = $scorpions->obj($true)) {
            if ($seahorses->getOption('wishlist_granted') == 'y') {
                echo $wolves->getTemplate_Listings($getItem->id, 'wishlist_granted_template') . "\n";
            } elseif ($seahorses->getOption('wishlist_granted') == 'n') {
                echo $mermaids->getTemplate_Wishlist($getItem->wID, 'wishlist_granted_template') . "\n";
            }
        }
    } else {
        echo "<p class=\"tc\">Currently no granted wishes!</p>\n";
    }
} elseif ($status == 'list') {
    $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wType` = 'list' ORDER BY `wSubject` ASC";
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script unable to select' .
            ' your wishlist.', false);
    }
    $count = $scorpions->total($true);
    if ($count > 0) {
        while ($getItem = $scorpions->obj($true)) {
            echo $mermaids->getTemplate_Wishlist($getItem->wID, 'wishlist_template') . "\n";
        }
    } else {
        echo "<p class=\"tc\">Currently no wishes!</p>\n";
    }
} elseif ($status == 'custom') {
    $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wType` = 'custom' ORDER BY `wSubject` ASC";
    $count = $scorpions->counts($select);
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'Unable to select your custom' .
            ' wishes.|Make sure your wishlist table exists.', false);
    }
    if ($count->rows > 0) {
        while ($getItem = $scorpions->obj($true)) {
            echo $mermaids->getTemplate_Wishlist($getItem->wID, 'wishlist_custom_template') . "\n";
        }
    } else {
        echo "<p class=\"tc\">Currently no wishes!</p>\n";
    }
}
