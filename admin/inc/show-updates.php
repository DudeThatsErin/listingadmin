<div id="show-updates">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <fun-updates.inc.php>
     * @version          Robotess Fork
     */

    require('b.inc.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('fun.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require_once('fun-updates.inc.php');
    require(MAINDIR . 'vars.inc.php');

    /**
     * Get variables and options!
     */
    $options = new stdClass();

    if (
        !isset($fKey) ||
        ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
    ) {
        $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
    } else {
        $options->listingID = $tigers->cleanMys($fKey);
        $getItem = $wolves->getListings($options->listingID, 'object');
    }

    $query = $tigers->cleanMys($_SERVER['QUERY_STRING']);
    if (isset($query) && !empty($query)) {
        $options->url = '?' . str_replace('&', '&#38;', $query) . '&#38;';
    } else {
        $options->url = '?';
    }

    if (!isset($pagination) || empty($pagination) || !is_numeric($pagination)) {
        $options->pagination = 5;
    } else {
        $options->pagination = $tigers->cleanMys($pagination);
    }

    if (isset($show) && in_array($show, array('archives', 'blog'))) {
        $options->show = $show;
    } else {
        $options->show = 'blog';
    }

    if (
        isset($show_all) &&
        !empty($show_all) &&
        ($options->listingID == 0 || $options->listingID == '0') &&
        in_array($show_all, array('y', 'n'))
    ) {
        $options->showAll = $show_all;
    } else if ($fKey == 0 || $fKey == '0') {
        $options->showAll = 'y';
    } else {
        $options->showAll = 'n';
    }

    if (
        isset($show_cp) &&
        !empty($show_cp) &&
        in_array($show_cp, array('y', 'n'))
    ) {
        $options->crosspost = $show_cp;
    } else {
        $options->crosspost = 'n';
    }

    if (!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
        $page = 1;
    } else {
        $page = $tigers->cleanMys($_GET['p']);
    }
    $start_entry_lone = $scorpions->escape(
        (($page * $options->pagination) - $options->pagination)
    );

    if ($options->listingID == 0 || $options->listingID == '0') {
        if ($seahorses->getOption('markup') == 'xhtml') {
            $mark = ' /';
        } else {
            $mark = '';
        }
    } else if ($getItem->markup === 'xhtml') {
        $mark = ' /';
    } else {
        $mark = '';
    }

    $options->markup = $mark;

    /**
     * Get entry P:
     */
    if (isset($_GET['e']) && is_numeric($_GET['e'])) {
        $e = $tigers->cleanMys((int)$_GET['e']);
        if (!empty($e) || in_array($e, $turtles->updatesList())) {
            $getItem = $turtles->getEntry($e);
            echo $turtles->templateEntries(str_replace('{MORE}', '', $e), 'single');
            if ($getItem->uDisabled === '1') {
                echo "<h3 id=\"comments\">Comments</h3>\n";
                $turtles->comments($e);
            }
        } else {
            $tigers->displayError('Script Error', 'Invalid entry!', false);
        }
    } /**
     * Search by listing~
     */
    elseif (isset($_GET['s']) && is_numeric($_GET['s'])) {
        $s = $tigers->cleanMys($_GET['s']);
        if (($s == 0) || ($s != '0' && $s != 0 && in_array($s, $wolves->listingsList()))) {
            $select = "SELECT * FROM `$_ST[updates]` WHERE `uCategory` LIKE '%!$s!%' AND" .
                " `uPending` = '0' ORDER BY `uAdded` DESC";
            $true = $scorpions->query($select);
            if ($scorpions->total($true) == 0) {
                echo "<p class=\"tc\">No entries have been posted under this listing.</p>\n";
            } else {
                echo $octopus->alternate('menu', $seahorses->getOption('markup'), 0, 'archives');
                while ($getItem = $scorpions->obj($true)) {
                    if ($seahorses->getOption('updates_prettyurls') == 'y') {
                        echo ' <li><a href="' . $turtles->blogURL() . 'e/' . $getItem->uID . '">' .
                            $getItem->uTitle . '</a> (' . date($seahorses->getTemplate('date_template'),
                                strtotime($getItem->uAdded)) . ")</li>\n";
                    } else {
                        echo ' <li><a href="' . $turtles->blogURL() . '?e=' . $getItem->uID . '">' .
                            $getItem->uTitle . '</a> (' . date($seahorses->getTemplate('date_template'),
                                strtotime($getItem->uAdded)) . ")</li>\n";
                    }
                }
                echo $octopus->alternate('menu', $seahorses->getOption('markup'), 1, 'archives');
            }
        } else {
            $tigers->displayError('Script Error', 'Invalid listing ID!', false);
        }
    } /**
     * Sort by month and year 8D
     */
    elseif (isset($_GET['m']) && is_numeric($_GET['m'])) {
        $digits = $tigers->cleanMys($_GET['m']);
        if (strlen($digits) > 6 || strlen($digits) < 6 || !is_numeric($digits)) {
            $tigers->displayError('Script Error', 'Invalid year/month combination.' .
                ' Go back and try again.', false);
        }
        $select = "SELECT * FROM `$_ST[updates]` WHERE `uPending` = '0' AND" .
            " DATE_FORMAT(`uAdded`, '%Y%m') = '$digits' ORDER BY `uAdded` DESC";
        $true = $scorpions->query($select);
        if ($scorpions->total($true) == 0) {
            echo '<p class="tc">No entries have been posted in this month/year' .
                " combination.</p>\n";
        } else {
            $db = substr($digits, -2);
            echo '<h3>' . $get_date_array[$db] . ' ' . substr($digits, 0, -2) . "</h3>\n";
            echo $octopus->alternate('menu', $seahorses->getOption('markup'), 0, 'menu');
            while ($getItem = $scorpions->obj($true)) {
                echo '<li class="indi"><a href="' . $turtles->makeEntryLink($getItem->uID) .
                    '">' . $getItem->uTitle . '</a> (' . date($seahorses->getTemplate('date_template'),
                        strtotime($getItem->uAdded)) . ")</li>\n";
            }
            echo $octopus->alternate('menu', $seahorses->getOption('markup'), 1, 'menu');
        }
    } /**
     * Index
     */
    else {
        $select = "SELECT * FROM `$_ST[updates]`";
        if ($options->showAll == 'n') {
            $select .= " WHERE `uCategory` LIKE '%!" . $options->listingID . "!%'";
        }
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to select' .
                ' the updates from the specified listing.', false);
        }
        $count = $scorpions->total($true);
        if ($count > 0) {
            $turtles->updatesDefault($options->show, $options->showAll);
        } else {
            $b = $options->listingID == '0' ? 'the collective' : 'this listing';
            echo "<p style=\"text-align: center;\">There are currently no updates for $b!</p>\n";
        }
    }
    ?>

    <p class="showCredits-LA-RF" style="text-align: center;">
        Powered by <?php echo $octopus->formatCredit(); ?>
        </a>
    </p>
</div>
