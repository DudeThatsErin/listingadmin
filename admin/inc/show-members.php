<div id="show-members">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-members.php>
     * @version          Robotess Fork
     */
    require('b.inc.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('fun.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');

    /**
     * Set variables for further use~!
     */
    $options = (object)array();

    if (
        !isset($fKey) ||
        ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
    ) {
        $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
    } else {
        $options->listingID = $tigers->cleanMys($fKey);
        $getItem = $wolves->getListings($options->listingID, 'object');
    }
    echo $checkCr;

    if (!isset($_GET['page']) || empty($_GET['page']) || !is_numeric($_GET['page'])) {
        $page = 1;
    } else {
        $page = $tigers->cleanMys((int)$_GET['page']);
    }
    $start = $scorpions->escape((($page * $per_page) - $per_page));

    if (!isset($pretty_urls) || !in_array($pretty_urls, array('y', 'n'))) {
        $options->prettyURL = false;
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            if (isset($_GET['page'])) {
                $options->url = '?' . str_replace('&', '&#38;', str_replace($_GET['page'], '',
                        str_replace('&page=', '', $_SERVER['QUERY_STRING']))) . '&#38;';
            } else {
                $options->url = '?' . str_replace('&', '&#38;', $_SERVER['QUERY_STRING']) . '&#38;';
            }
        } else {
            $options->url = '?';
        }
    } else {
        $options->prettyURL = true;
        if (isset($set_query) && preg_match("/^[A-Za-z0-9\/-_]+$/", $set_query)) {
            $options->url = $set_query;
            if (isset($_GET['name'])) {
                $queryname = str_replace(' ', '+', $_GET['name']);
                $options->url .= 'country/' . $tigers->cleanMys($queryname) . '/';
            }
        } else {
            $options->url = '';
        }
    }

    $sortbyArray = array('all', 'country', 'list', 'name');
    if (isset($sort_by) && in_array($sort_by, $sortbyArray)) {
        $options->sort = $sort_by;
    } else {
        $options->sort = 'country';
    }

    if ($getItem->markup == 'xhtml') {
        $options->markup = ' /';
    } else {
        $options->markup = '';
    }

    /**
     * Is the listing crosslisted? Check and perform necessary actions~
     */
    if ($getItem->dblist == 1) {
        if (!empty($getItem->dbhost) && !empty($getItem->dbuser) && !empty($getItem->dbname)) {
            $scorpions->breach(0);
            $scorpions->initDB(
                $getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname
            );
        }
        $dbtable = $getItem->dbtabl;
        if ($getItem->dbtype == 'enth') {
            $select = "SELECT * FROM `$dbtable` WHERE `pending` = '0'";
        } elseif ($getItem->dbtype == 'listingadmin') {
            $flid = $getItem->dbflid;
            $select = "SELECT * FROM `$dbtable` WHERE `fNiq` = '$flid' AND `mPending` = '0'";
        } else {
            $select = "SELECT * FROM `$dbtable` WHERE `apr` = 'y'";
        }
        $true = $scorpions->counts($select, 1);
        if ($true->status == false) {
            $tigers->displayError('Script Error', 'The script was unable to select the' .
                ' members from the specified listing.', false);
        }
        $count = $true->rows;

        if (!empty($getItem->dbhost) && !empty($getItem->dbuser) && !empty($getItem->dbname)) {
            $scorpions->breach(0);
            $scorpions->initDB(
                $getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname
            );
        }
    } else {
        $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '" . $options->listingID .
            "' AND `mPending` = '0'";
        $true = $scorpions->counts($select, 1);
        if ($true->status == false) {
            $tigers->displayError('Script Error', 'Unable to select the members' .
                ' from the specified listing.', false);
        }
        $count = $true->rows;
    }

    if ($count > 0) {
        if (isset($_GET['sort']) && $_GET['sort'] === 'all') {
            $snakes->membersSort('all', $getItem->dblist, $getItem->dbtype);
            $snakes->membersPagination('all');
        } elseif (isset($_GET['sort']) && $_GET['sort'] === 'name') {
            $snakes->membersSort('name', $getItem->dblist, $getItem->dbtype);
            $snakes->membersPagination('name');
        } elseif (isset($_GET['sort']) && $_GET['sort'] === 'country') {
            $snakes->membersSort('country', $getItem->dblist, $getItem->dbtype);
            if (isset($_GET['name'])) {
                $snakes->membersPagination('country', str_replace('+', ' ', $tigers->cleanMys($_GET['name'])));
            }
        } else {
            $snakes->membersDefault($options->sort);
            if ($options->sort === 'name') {
                $snakes->membersPagination($options->sort);
            }
        }
    } else {
        echo "<p class=\"tc\">Currently no members!</p>\n";
    }
    ?>

    <p class="showCredits-LA-RF" style="text-align: center;">
        Powered by <?php echo $octopus->formatCredit(); ?>
    </p>
</div>
