<?php
/**
 * @copyright  2007
 * @license    GPL Version 3; BSD Modified
 * @author     Tess <treibend@gmail.com>
 * @file       <upgrade.php>
 * @since      November 19th, 2011
 * @version    2.3alpha
 */

require('rats.inc.php');

die('Upgrade script is turned off for version ' . $laoptions->version);

require('inc/fun.inc.php');
require('inc/fun-admin.inc.php');

$getTitle = 'Upgrade';
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Author" content="Tess">
    <title> <?php echo $laoptions->version; ?> &#8212; <?php echo $leopards->isTitle($getTitle); ?> </title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>

<body>

<div id="container">

    <header>
        <h1><?php echo $laoptions->version; ?></h1>
    </header>

    <section id="install"><span class="mysql">Notice:</span> please note that there's no active support for FRESH
        installations. You might try to install a script, but there's no guarantee it will be working.
        <ins>If you wish to install a script, you can use original Tess' version.</ins>
    </section>

    <section id="install">
        <?php
        $versionarray = array(
            '2.1.9' => '2.1.9',
            '2.2' => '2.2',
            '2.3beta' => '2.3 Beta',
            '2.3alpha' => '2.3 Alpha'
        );

        if (isset($_POST['action']) && $_POST['action'] == 'Upgrade') {
            $version = $tigers->cleanMys($_POST['version']);
            if (empty($version) || !array_key_exists($version, $versionarray)) {
                $tigers->displayError('Form Error', 'In order to upgrade, you must choose a' .
                    ' valid version to upgrade from, m\'love.', false);
            }

            if ($version == '2.3beta') {
                $alter = "ALTER TABLE `$_ST[codes_donors]` ADD `dEmail` VARCHAR(255)" .
                    " NOT NULL AFTER `dName`, ADD `dPending` VARCHAR(255) NOT NULL DEFAULT '0'" .
                    " AFTER `dURL`, ADD `dUpdated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'" .
                    ' AFTER `dPending`, ADD `dAdded` DATETIME NOT NULL AFTER `dUpdated`;';
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[kim]` ADD `mEdit` DATETIME NOT NULL DEFAULT" .
                    " '0000-00-00 00:00:00' AFTER `mUpdate`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[members]` ADD `mEdit` DATETIME NOT NULL DEFAULT" .
                    " '0000-00-00 00:00:00' AFTER `mUpdate`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[quotes]` ADD `qUpdated` DATETIME NOT NULL DEFAULT" .
                    " '0000-00-00 00:00:00' AFTER `qAuthor`, ADD `qAdded` DATETIME NOT NULL AFTER" .
                    ' `qUpdated`;';
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }
            } else if ($version == '2.3alpha') {
                $alter = "ALTER TABLE `$_ST[main]` ADD `previous` TEXT NOT NULL AFTER" .
                    " `fave_fields`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $insert = "INSERT INTO `$_ST[options]` (`name`, `text`) VALUES" .
                    " ('format_links', '&#187;')";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $insert . '</em></p>');
                }

                @unlink($adminpath . 'img/v5Sidebar.png');
                @unlink($adminpath . 'img/v5Bg.png');
                @unlink($adminpath . 'inc/show-details.php');
                unlink($adminpath . 'textMido.otf');
            } else {
                if ($version == '2.1.9') {
                    $alter = "ALTER TABLE `$_ST[affiliates]` CHANGE `fNiq` `fNiq` VARCHAR(255)" .
                        " NOT NULL DEFAULT '!0!';";
                    $true = $scorpions->query($alter);
                    if ($true == false) {
                        exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                            ' <em>' . $alter . '</em></p>');
                    }

                    $alter = "ALTER TABLE `$_ST[main]` ADD `dbaffs` VARCHAR(255) NOT NULL AFTER" .
                        " `dblist`;";
                    $true = $scorpions->query($alter);
                    if ($true == false) {
                        exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                            ' <em>' . $alter . '</em></p>');
                    }

                    $insert = "INSERT INTO `$_ST[options]` (`name`, `text`) VALUES ('user_passhint'," .
                        " '')";
                    $scorpions->query("SET NAMES 'utf8';");
                    $true = $scorpions->query($insert);
                    if ($true == false) {
                        exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                            ' <em>' . $insert . '</em></p>');
                    }
                }

                $alter = "ALTER TABLE `$_ST[codes_donors]` ADD `dEmail` VARCHAR(255)" .
                    " NOT NULL AFTER `dName`, ADD `dPending` VARCHAR(255) NOT NULL DEFAULT '0'" .
                    " AFTER `dURL`, ADD `dUpdated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'" .
                    " AFTER `dPending`, ADD `dAdded` DATETIME NOT NULL AFTER `dUpdated`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $query = "SELECT * FROM `$_ST[joined]` LIMIT 1";
                $result = $scorpions->query($query);
                $item = $scorpions->obj($result);
                if (!isset($item->jStatus)) {
                    $alter = "ALTER TABLE `$_ST[joined]` ADD `jStatus` TINYINT(1) NOT NULL" .
                        " DEFAULT '0' AFTER `jMade`;";
                    $true = $scorpions->query($alter);
                    if ($true == false) {
                        exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                            ' <em>' . $alter . '</em></p>');
                    }
                }

                $alter = "ALTER TABLE `$_ST[kim]` ADD `mEdit` DATETIME NOT NULL DEFAULT" .
                    " '0000-00-00 00:00:00' AFTER `mUpdate`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[main]`" .
                    " CHANGE `markup` `markup` ENUM('html5', 'html', 'xhtml') NOT NULL DEFAULT 'xhtml'," .
                    " CHANGE `dbtype` `dbtype` ENUM('enth', 'fanbase', 'listingadmin') NOT NULL DEFAULT 'enth'," .
                    " ADD `dbflid` MEDIUMINT(6) NOT NULL AFTER `dbaffs`," .
                    " ADD `dbhttp` VARCHAR(255) NOT NULL AFTER `dbflid`," .
                    " ADD `dbpath` VARCHAR(255) NOT NULL AFTER `dbhttp`," .
                    " ADD `form_join_comments` TEXT NOT NULL AFTER `form_join`," .
                    " ADD `form_join_rules` MEDIUMINT(6) NOT NULL AFTER `form_join_comments`," .
                    " ADD `previous` TEXT NOT NULL AFTER `fave_fields`;";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[members]` ADD `mEdit` DATETIME NOT NULL DEFAULT" .
                    " '0000-00-00 00:00:00' AFTER `mUpdate`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[quotes]` ADD `qUpdated` DATETIME NOT NULL DEFAULT" .
                    " '0000-00-00 00:00:00' AFTER `qAuthor`, ADD `qAdded` DATETIME NOT NULL AFTER" .
                    " `qUpdated`;";
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $alter = "ALTER TABLE `$_ST[wishlist]` CHANGE `wType` `wType` ENUM('custom'," .
                    " 'granted', 'list', 'top') NOT NULL;";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($alter);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $alter . '</em></p>');
                }

                $insert = "INSERT INTO `$_ST[options]` (`name`, `text`) VALUES" .
                    " ('antispam_spam_words_required'," .
                    " 'alert|bcc:|cc:|content-type|document.cookie|ejaculate|fag|javascript|jism|onclick|onload')," .
                    " ('antispam_spam_words', '')," .
                    " ('formats_links', '&#187;')";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    exit('<p class="mysqlButton"><span class="mysql">Error:</span> ' . $scorpions->error() .
                        ' <em>' . $insert . '</em></p>');
                }

                $adminpath = dirname(__FILE__) . '/';
                @unlink($adminpath . 'img/error.png');
                @unlink($adminpath . 'img/error_fatal.png');
                @unlink($adminpath . 'img/logo.png');
                @unlink($adminpath . 'img/logo_stats.png');
                @unlink($adminpath . 'img/nav.png');
                @unlink($adminpath . 'img/newestJoined.png');
                @unlink($adminpath . 'img/newestOwned.png');
                @unlink($adminpath . 'img/note.png');
                @unlink($adminpath . 'img/success.png');
                @unlink($adminpath . 'img/write.png');
                @unlink($adminpath . 'img/write_bw.png');
                unlink($adminpath . 'vendors/class-crosspost.inc.php');
                unlink($adminpath . 'vendors/class-ixr.inc.php');
                unlink($adminpath . 'fun-admin.inc.php');
                unlink($adminpath . 'fun-captcha.inc.php');
                unlink($adminpath . 'fun-categories.inc.php');
                unlink($adminpath . 'fun-check.inc.php');
                unlink($adminpath . 'fun-codes.inc.php');
                unlink($adminpath . 'fun-emails.inc.php');
                unlink($adminpath . 'fun-external.inc.php');
                unlink($adminpath . 'fun-internal.inc.php');
                unlink($adminpath . 'fun-joined.inc.php');
                unlink($adminpath . 'fun-kim.inc.php');
                unlink($adminpath . 'fun-listings.inc.php');
                unlink($adminpath . 'fun-lyrics.inc.php');
                unlink($adminpath . 'fun-mark.inc.php');
                unlink($adminpath . 'fun-members.inc.php');
                unlink($adminpath . 'fun-misc.inc.php');
                unlink($adminpath . 'fun-process.inc.php');
                unlink($adminpath . 'fun-quotes.inc.php');
                unlink($adminpath . 'fun-show.inc.php');
                unlink($adminpath . 'fun-updates.inc.php');
                unlink($adminpath . 'fun-wishlist.inc.php');
                unlink($adminpath . 'fun.inc.php');
                unlink($adminpath . 'func.microakismet.inc.php');
                unlink($adminpath . 'js.js');
                unlink($adminpath . 'js_updates.js');
                unlink($adminpath . 'show-affiliates.php');
                unlink($adminpath . 'show-c-stats.php');
                unlink($adminpath . 'show-codes.php');
                unlink($adminpath . 'show-delete.php');
                unlink($adminpath . 'show-details.php');
                unlink($adminpath . 'show-form.php');
                unlink($adminpath . 'show-join.php');
                unlink($adminpath . 'show-joined.php');
                unlink($adminpath . 'show-kim-join.php');
                unlink($adminpath . 'show-kim-members.php');
                unlink($adminpath . 'show-kim-reset.php');
                unlink($adminpath . 'show-kim-stats.php');
                unlink($adminpath . 'show-kim-update.php');
                unlink($adminpath . 'show-lyrics.php');
                unlink($adminpath . 'show-members.php');
                unlink($adminpath . 'show-owned.php');
                unlink($adminpath . 'show-quotes.php');
                unlink($adminpath . 'show-real-stats.php');
                unlink($adminpath . 'show-reset.php');
                unlink($adminpath . 'show-stats.php');
                unlink($adminpath . 'show-update.php');
                unlink($adminpath . 'show-updates.php');
                unlink($adminpath . 'show-wishlist.php');
                @unlink($adminpath . 'textMido.otf');
            }
            echo '<p class="successButton"><span class="success">Success!</span> If' .
                " you're seeing this message -- and have come across no errors -- you have" .
                ' successfully completed the upgrade! Go login now and enjoy the (kind of' .
                " shitty) new version of Listing Admin! :D</p>\n";
        } else {
            ?>
            <form action="upgrade.php" method="post">
                <fieldset>
                    <legend>Upgrade</legend>
                    <div id="upgrade">
                        <p style="float: left; margin: 0; padding: 0 1% 0 0; text-align: right; width: 19%;">
                            <strong>From Version:</strong>
                        </p>
                        <p style="float: left; margin: 0 1% 0 0; padding: 0; width: 59%;">
                            <select name="version" class="input4">
                                <?php
                                foreach ($versionarray as $k => $v) {
                                    echo '    <option';
                                    if ($k == '2.2') {
                                        echo ' selected="selected"';
                                    }
                                    echo " value=\"$k\">$v</option>\n";
                                }
                                ?>
                            </select>
                        </p>
                        <p style="float: right; margin: 0 1% 0 0; padding: 0; text-align: left; width: 19%;">
                            <input name="action" class="input2" type="submit" value="Upgrade">
                        </p>
                        <p class="clear"></p>
                    </div>
                </fieldset>
            </form>
            <?php
        }
        ?>
    </section>

</div>

</body>
</html>
