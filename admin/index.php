<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <index.php>
 * @version          Robotess Fork
 */

$getTitle = 'Control Panel';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

$get_random_http = $seahorses->getOption('adm_http');
$get_random_path = $seahorses->getOption('adm_path');
if (strpos($get_random_http, 'inc') === false || strpos($get_random_path, 'inc') === false) {
    if (strpos($get_random_http, 'inc') === false) {
        $seahorses->editOption('adm_http', $get_random_http . 'inc/');
    }
    if (strpos($get_random_path, 'inc') === false) {
        $seahorses->editOption('adm_path', $get_random_path . 'inc/');
    }
}

if (is_file('install.php') || is_file('upgrade.php')) {
    ?>
    <p class="mysqlButton"><span class="mysql">Error:</span> It appears you still
        have the <samp>install.php</samp> or <samp>upgrade.php</samp> files on your
        server. Both the local and online documentation clearly states to delete this
        as soon as you are done installing the script, as it's a security hazard.
        <ins>DO NOT DISAPPOINT AND POSSIBLY DAMAGE YOUR SERVER</ins>
        .
    </p>
    <?php
}
?>
    <p>Welcome to <samp><?php echo $laoptions->version; ?></samp>, the script for
        managing your fanlisting collective and it's many, many addons! The default
        features include full control of your listings, joined listings, wishlist and
        members (with full intergration of crosslisting to Enthusiast phpFanUpdate and
        other Listing Admin installations); the addons include &#8211; but probably
        won't be limited to, I'm a hoarder &#8211; <abbr title="Keep In Mind List">KIM</abbr>
        list, updates and quotes. The control panel of your script is where you'll be
        able to view statistics (mostly of listings, joined listings and some of
        your (installed) addons), overdue and "neglected" notices, and use this as a
        refresh stop. Navigation is simple, and is as the top of the every page. Have
        fun displaying fanlistings! :D</p>

    <h2>Server info (useful for debugging and reporting issues)</h2>
    <p>When you're asking for help with the script, please share the following information:</p>
    <p class="la-version">Listing Admin: <?= $laoptions->version ?></p>
    <p>PHP: <?= PHP_VERSION ?></p>

    <h3>Statistics</h3>
<?php
$categories = $seahorses->getCount('cat');
$categories_used = $lions->countCategories(1);
$current = $seahorses->getCount('current', 'y');
$upcoming = $seahorses->getCount('upcoming', 'y');
$pending = $seahorses->getCount('pending', 'y');
$listings = $current + $upcoming + $pending;
$listings = $listings < 10 && $listings != 0 ? '0' . $listings : $listings;
$approved = $seahorses->memberCount(0);
$unapproved = $seahorses->memberCount(1);
$members = $approved + $unapproved;
$affiliates = $seahorses->getCount('affiliates');
$affiliates_collective = $seahorses->getCount('affiliates', 'y');

/**
 * Get collective features~
 */
$joinedapp = $seahorses->getCount('joined', 'm');
$joinedpen = $seahorses->getCount('joined', 'y');
$wishlist = $seahorses->getCount('wishlist');
$wishlist_granted = $seahorses->getCount('wishlist', 'y');

/**
 * Get addon statistics! :D
 */
if ($cheetahs->isInstalled('codes') == true) {
    $codes = $cheetahs->codeCount();
    $codes_collective = $cheetahs->codeCount('0');
} else {
    $codes = '0';
    $codes_collective = '0';
}
if ($cheetahs->isInstalled('kim') == true) {
    $kim = $seahorses->getCount('kim', 'n', 1);
    $kimpending = $seahorses->getCount('kim', 'y');
} else {
    $kim = '0';
    $kimpending = '0';
}
if ($cheetahs->isInstalled('lyrics') == true) {
    $lyrics = $seahorses->getCount('lyrics');
    $lyricsalbums = $seahorses->getCount('lyrics', 'a');
} else {
    $lyrics = '0';
    $lyricsalbums = '0';
}
if ($cheetahs->isInstalled('quotes') == true) {
    $quotes = $seahorses->getCount('quotes');
} else {
    $quotes = '0';
}
if ($cheetahs->isInstalled('updates') == true) {
    $updates = $seahorses->getCount('updates');
    $updates_collective = $seahorses->getCount('updates', 'c');
} else {
    $updates = '0';
    $updates_collective = '0';
}
?>
    <div id="statistics">
        <section id="stTop">
            <table>
                <tbody>
                <tr>
                    <td><strong><?php echo $listings; ?></strong> Listings
                        (<strong><?php echo $current; ?></strong> Current,
                        <strong><?php echo $upcoming; ?></strong> Upcoming and
                        <strong><?php echo $pending; ?></strong> Pending)
                    </td>
                </tr>
                </tbody>
            </table>
        </section>
        <section id="stLeft">
            <table>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $categories; ?></strong></td>
                    <td>Categories (<strong><?php echo $categories_used; ?></strong> in Use)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $joinedapp; ?></strong></td>
                    <td>Joined Listings (<strong><?php echo $joinedpen; ?></strong> Pending)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $approved; ?></strong></td>
                    <td>Members (<strong><?php echo $unapproved; ?></strong> Pending)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $affiliates; ?></strong></td>
                    <td>Affiliates (<strong><?php echo $affiliates_collective; ?></strong> Collective Affiliates)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $wishlist; ?></strong></td>
                    <td>Wishlist Items (<strong><?php echo $wishlist_granted; ?></strong> Granted Wishes)</td>
                </tr>
                </tbody>
            </table>
        </section>
        <section id="stRight">
            <table>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $codes; ?></strong></td>
                    <td>Code Buttons (<strong><?php echo $codes_collective; ?></strong> Collective Code Buttons)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $kim; ?></strong></td>
                    <td>KIM Members (<strong><?php echo $kimpending; ?></strong> Pending)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $lyrics; ?></strong></td>
                    <td>Lyrics (<strong><?php echo $lyricsalbums; ?></strong> Albums)</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $quotes; ?></strong></td>
                    <td>Quotes</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td class="left"><strong><?php echo $updates; ?></strong></td>
                    <td>Updates (<strong><?php echo $updates_collective; ?></strong> Collective Updates)</td>
                </tr>
                </tbody>
            </table>
        </section>
        <section class="clear"></section>
    </div>

    <h3>Listings Notification</h3>
    <div class="floatLeft floater">
        <h4>2 Months Inactivity</h4>
        <menu>
            <?php
            $no1 = array();
            $no11 = 0;
            $there = $wolves->indexListings(1);
            foreach ($there as $a) {
                $c11 = $no11 == ((is_countable($there) ? count($there) : 0) - 1) ? ' class="last"' : '';
                $g = $wolves->getListings($a, 'object');
                echo " <li{$c11}><strong>" . $g->subject . '</strong> &#8211; <a href="members' .
                    '.php?listing=' . $g->id . "&#38;action=update\">Update Listing</a><br>\n";
                echo '<strong>Last Updated:</strong> ' . $snakes->getUpdated($g->id) . "<br>\n";
                echo '<strong>' . $snakes->countMembers($g->id, '0') . '</strong> Members (<strong>' .
                    $snakes->countMembers($g->id, 1) . '</strong> Pending) &middot; ' .
                    $rabbits->countAffiliates($g->id) . " Affiliates</li>\n";
                $no11++;
            }

            if ((is_countable($there) ? count($there) : 0) == 0) {
                echo " <li class=\"last\">None Found!</li>\n";
            }
            ?>
        </menu>
    </div>

    <div class="floatLeft floater">
        <h4>Overdue Listings</h4>
        <menu>
            <?php
            $no2 = [];
            $coming = $wolves->indexListings(0);
            foreach ($coming as $b) {
                $h = $wolves->getListings($b, 'object');
                echo ' <li><strong>' . $h->subject . "</strong></li>\n";
            }

            if ((is_countable($coming) ? count($coming) : 0) == 0) {
                echo " <li class=\"last\">None Found!</li>\n";
            }
            ?>
        </menu>
    </div>
    <section class="clear"></section>

    <h3>Recent</h3>
    <div class="floatLeft floater listings">
        <h4>Recent Opened Listings</h4>
        <menu>
            <?php
            $select = "SELECT * FROM `$_ST[main]` WHERE `show` = '0' AND `status` = '0'" .
                ' ORDER BY `since` DESC LIMIT 5';
            $true = $scorpions->query($select);
            $counter = $scorpions->counts($select, 1);
            if ($true == false || $counter->rows == 0) {
                echo " <li class=\"last\">No Listings Available</li>\n";
            } else {
                $i = 0;
                while ($getItem = $scorpions->obj($true, 0)) {
                    $c = $i == ($counter->rows - 1) ? ' class="last"' : '';
                    echo " <li$c><a href=\"" . $getItem->url . '">' . $getItem->subject . "</a></li>\n";
                    $i++;
                }
            }
            ?>
        </menu>
    </div>

    <div class="floatLeft floater joined">
        <h4>Recent Joined Listings</h4>
        <menu>
            <?php
            $select = "SELECT * FROM `$_ST[joined]` ORDER BY `jAdd` DESC LIMIT 5";
            $true = $scorpions->query($select);
            $numbers = $scorpions->counts($select, 1);
            if ($true == false || $numbers->rows == 0) {
                echo " <li>No Joined Listings Available</li>\n";
            } else {
                $n = 0;
                while ($getItem = $scorpions->obj($true, 0)) {
                    $a = $n == ($numbers->rows - 1) ? ' class="last"' : '';
                    echo " <li$a><a href=\"" . $getItem->jURL . '">' . $getItem->jSubject . "</a></li>\n";
                    $n++;
                }
            }
            ?>
        </menu>
    </div>
<?php

require('footer.php');
