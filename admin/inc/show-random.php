<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <show-random.php>
 * @version          Robotess Fork
 */

require('b.inc.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-misc.inc.php');

$options = new stdClass();

if (isset($show_joined) && !empty($show_joined)) {
    if ($show_joined == 'y') {
        $options->showJoined = 'y';
    } elseif ($show_joined == 'n') {
        $options->showJoined = 'n';
    } else {
        $options->showJoined = 'y';
    }
} else {
    $options->showJoined = 'y';
}

if (isset($show_joined_number) && is_numeric($show_joined_number)) {
    $options->showJoinedNumber = $show_joined_number;
} else {
    $options->showJoinedNumber = 2;
}

if (isset($show_joined_rotate) && !empty($show_joined_rotate)) {
    if ($show_joined_rotate == 'y') {
        $options->showJoinedRotate = 'y';
    } elseif ($show_joined_rotate == 'n') {
        $options->showJoinedRotate = 'n';
    } else {
        $options->showJoinedRotate = 'n';
    }
} else {
    $options->showJoinedRotate = 'n';
}

if (isset($show_owned) && !empty($show_owned)) {
    if ($show_owned == 'y') {
        $options->showOwned = 'y';
    } elseif ($show_owned == 'n') {
        $options->showOwned = 'n';
    } else {
        $options->showOwned = 'n';
    }
} else {
    $options->showOwned = 'n';
}

if (isset($show_owned_number) && is_numeric($show_owned_number)) {
    $options->showOwnedNumber = $show_owned_number;
} else {
    $options->showOwnedNumber = 2;
}

if (isset($show_owned_rotate) && !empty($show_owned_rotate)) {
    if ($show_owned_rotate == 'y') {
        $options->showOwnedRotate = 'y';
    } elseif ($show_owned_rotate == 'n') {
        $options->showOwnedRotate = 'n';
    } else {
        $options->showOwnedRotate = 'n';
    }
} else {
    $options->showOwnedRotate = 'n';
}

$markup = $seahorses->getOption('markup');
if ($markup == 'xhtml') {
    $mark = ' /';
} else {
    $mark = '';
}

/**
 * Get the index: owned and joined listings \o/
 */
if ($options->showOwned == 'y') {
    if($options->showOwnedRotate == 'n') {
        $select = "SELECT * FROM `$_ST[main]` WHERE `show` = '0' AND `status` = '0'" .
            ' ORDER BY `since` DESC LIMIT ' . $options->showOwnedNumber;
    } else {
        $select = "SELECT * FROM `$_ST[main]` WHERE `show` = '0' AND `status` = '0'" .
            ' ORDER BY RAND() LIMIT ' . $options->showOwnedNumber;
    }

    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to select' .
            ' the newest listing(s)!', false);
    } else {
        while ($getItem = $scorpions->obj($true)) {
            echo '<a href="' . $getItem->url . '"><img src="' . $seahorses->getOption('img_http') . $getItem->image .
                '" alt="' . $getItem->subject . '" title="' . $getItem->subject . "\"$mark></a>\n";
        }
    }
}

if ($options->showJoined == 'y') {
    if($options->showJoinedRotate == 'n') {
        $select = "SELECT * FROM `$_ST[joined]` ORDER BY `jAdd` DESC LIMIT " . $options->showJoinedNumber;
    } else {
        $select = "SELECT * FROM `$_ST[joined]` ORDER BY RAND() LIMIT " . $options->showJoinedNumber;
    }

    $true = $scorpions->query($select);
    if ($true === false) {
        $tigers->displayError('Database Error', 'The script was unable to select' .
            ' the random joined listing(s)!', false);
    } else {
        while ($getItem = $scorpions->obj($true)) {
            if (!empty($getItem->jImage) && file_exists($seahorses->getOption('jnd_path') . $getItem->jImage)) {
                echo '<a href="' . $getItem->jURL . '"><img src="' . $seahorses->getOption('jnd_http') . $getItem->jImage .
                    '" alt="' . $getItem->jSubject . '" title="' . $getItem->jSubject . "\" class=\"joinedRandom\"$mark></a>\n";
            } else {
                echo '<a href="' . $getItem->jURL . '">' . $getItem->jSubject . "</a>\n";
            }
        }
    }
}
