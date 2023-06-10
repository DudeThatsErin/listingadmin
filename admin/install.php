<?php
declare(strict_types=1);
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <install.php>
 * @version          Robotess Fork
 */

if (!file_exists('rats.inc.php')) {
    ?>
    <section><span class="mysql">Notice:</span> there was an error while trying to find file rats.inc.php.
        Please make sure you have copied rats.sample.inc.php to rats.inc.php and added it to <?= __DIR__; ?>. The script stops executing.
    </section>
    <?php
    die;
}

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require('rats.inc.php');
require('inc/fun.inc.php');
require('inc/fun-admin.inc.php');
require('inc/fun-misc.inc.php');
require('inc/fun-utility.inc.php');
$getTitle = 'Install';
require('vars.inc.php');

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

    <section id="install">
        <?php
        //$steps = $tigers->cleanMys($_GET['step']);var_dump($_GET['step']);
        $steps = isset($_GET['step']) ? (int)$_GET['step'] : 0;
        switch ($steps) {
            case 1:
                echo $frogs->installMain();
                break;
            case 2:
                echo $frogs->installFeatures();
                break;
            case 3:
                echo $frogs->installAddons();
                break;
            case 4:
                echo $frogs->showFinished();
                break;
            default:
                if (isset($_POST['action'])) {
                    if (isset($_POST['action']) && $_POST['action'] == 'Install Main Functions') {
                        $collname = $tigers->cleanMys($_POST['cname']);
                        $adm_path = $tigers->cleanMys($_POST['adm_path']);
                        $adm_http = $tigers->cleanMys($_POST['adm_http']);
                        $my_name = $tigers->cleanMys($_POST['my_name']);
                        if (!empty($my_name) && !preg_match("/([A-Za-z\\-\s]+)/i", $my_name)) {
                            $tigers->displayError('Form Error', 'The name you provided can only contain' .
                                ' letters, spaces and dashes.', false);
                        }
                        $my_email = $tigers->cleanMys($_POST['my_email']);
                        if (!empty($my_email) && !preg_match("/([A-Za-z0-9-_\.]+)@(([A-Za-z0-9-_]+\.)+)([a-zA-Z]{2,4})$/i", $my_email)) {
                            $tigers->displayError('Form Error', 'The e-mail address you provided is' .
                                'not valid.', false);
                        }
                        $my_website = $tigers->cleanMys($_POST['my_website']);

                        $create = "CREATE TABLE `$_ST[categories]` (
     `catid` smallint(3) NOT NULL AUTO_INCREMENT,
     `catname` varchar(255) NOT NULL,
     `parent` smallint(3) UNSIGNED NOT NULL DEFAULT '0',
     PRIMARY KEY (`catid`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }
                        if (isset($_POST['installcats']) && $_POST['installcats'] == 'y') {
                            $frogs->installCategories();
                        }

                        $create = "CREATE TABLE `$_ST[options]` (
     `name` varchar(255) NOT NULL,
     `text` text NOT NULL,
     UNIQUE KEY `name` (`name`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $hashhash_input = sha1(date('YmdHis'));
                        $javascript_key_input = sha1((string)random_int(9999, 999999));
                        $insert = "INSERT INTO `$_ST[options]` (`name`, `text`) VALUES
    ('collective_name', '$collname'),
    ('per_joined', '25'), 
    ('per_members', '25'), 
    ('per_page', '15'), 
    ('adm_path', '$adm_path'), 
    ('adm_http', '$adm_http'), 
    ('aff_path', ''), 
    ('aff_http', ''), 
    ('img_path', ''), 
    ('img_http', ''), 
    ('jnd_path', ''), 
    ('jnd_http', ''), 
    ('wsh_path', ''), 
    ('wsh_http', ''),
    ('my_name', '$my_name'), 
    ('my_email', '$my_email'),
    ('my_website', '$my_website'),
    ('user_username', '$my_name'), 
    ('user_password', ''), 
    ('user_salthash', '$hashhash_input'),
    ('user_passhint', ''),
    ('user_passhinthash', ''),
    ('markup', 'xhtml'),
		('format_links', '&#187;'),
    ('kim_join', 'http://website.com/kim.php?join'),
    ('kim_list', 'http://website.com/kim.php?list'),
    ('kim_reset', 'http://website.com/kim.php?reset'),
    ('kim_update', 'http://website.com/kim.php?update'),
    ('notify_approval', 'y'),
    ('notify_update', 'y'),
    ('akismet_key', ''),
    ('akismet_opt', 'n'),
		(
		 'antispam_spam_words_required', 
		 'alert|bcc:|cc:|content-type|document.cookie|ejaculate|fag|javascript|jism|onclick|onload'
		),
		('antispam_spam_words', ''),
    ('antispam_opt', 'n'),
    ('captcha_opt', 'n'),
    ('javascript_opt', 'y'),
    ('javascript_key', '$javascript_key_input'),
    ('codes_opt', 'n'),
		('codes_filesize', '921600'),
		('codes_formurl', 'donate.php'),
    ('codes_img_path', ''),
    ('codes_img_http', ''),
    ('codes_order', 'DESC'),
    ('kim_opt', 'n'),
    ('lyrics_opt', 'n'),
    ('quotes_opt', 'n'),
    ('wishlist_opt', 'n'),
    ('updates_opt', 'n'),
    ('updates_akismet', 'n'),
    ('updates_akismet_key', ''),
    ('updates_antispam', 'y'),
    ('updates_captcha', 'n'),
    ('updates_comments', 'y'),
    ('updates_comments_header', ''),
    ('updates_comments_footer', ''),
    ('updates_comments_moderation', 'n'),
    ('updates_comments_notification', 'n'),
    ('updates_crosspost_dw', 'y'),
    ('updates_crosspost_dw_user', ''),
    ('updates_crosspost_dw_pass', ''),
    ('updates_crosspost_ij', 'y'),
    ('updates_crosspost_ij_user', ''),
    ('updates_crosspost_ij_pass', ''),
    ('updates_crosspost_lj', 'y'),
    ('updates_crosspost_lj_user', ''),
    ('updates_crosspost_lj_pass', ''),
    ('updates_crosspost_dw_link', 'y'),
    ('updates_crosspost_ij_link', 'y'),
    ('updates_crosspost_lj_link', 'y'),
    ('updates_gravatar', 'y'),
    ('updates_gravatar_size', '60'),
    ('updates_gravatar_rating', 'PG'),
    ('updates_prettyurls', 'y'),
    ('updates_url', ''),
    ('wishlist_granted', 'y')";
                        $scorpions->query("SET NAMES 'utf8';");
                        $true = $scorpions->query($insert);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[errors]` (
     `messID` mediumint(4) NOT NULL AUTO_INCREMENT,
     `messType` varchar(255) NOT NULL,
     `messURL` varchar(255) NOT NULL,
     `messText` text NOT NULL,
     `messInfo` text NOT NULL,
     `messAdded` datetime NOT NULL,
     PRIMARY KEY (`messID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[logs]` (
     `logID` int(10) NOT NULL AUTO_INCREMENT,
     `userNiq` mediumint(4) NOT NULL,
     `logUser` varchar(255) NOT NULL,
     `logInfo` text NOT NULL,
     `logLast` datetime NOT NULL,
     PRIMARY KEY (`logID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[main]` (
    `id` mediumint(6) NOT NULL AUTO_INCREMENT,
    `title` varchar(100) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `url` varchar(255) NOT NULL,
    `image` varchar(255) NOT NULL,
    `category` varchar(50) NOT NULL,
    `status` tinyint(1) NOT NULL DEFAULT '0',
    `show` tinyint(1) NOT NULL DEFAULT '0',
    `dbhost` varchar(255) NOT NULL DEFAULT 'localhost',
    `dbuser` varchar(255) NOT NULL,
    `dbpass` varchar(255) NOT NULL,
    `dbname` varchar(255) NOT NULL,
    `dbtype` enum('enth','fanbase', 'listingadmin') NOT NULL DEFAULT 'enth',
    `dbtabl` varchar(255) NOT NULL,
    `dblist` tinyint(1) NOT NULL DEFAULT '0',
    `dbaffs` varchar(255) NOT NULL,
    `dbflid` mediumint(6) NOT NULL,
    `dbhttp` varchar(255) NOT NULL,
    `dbpath` varchar(255) NOT NULL,
    `desc` text NOT NULL,
    `stats` text NOT NULL,
    `affiliates` text NOT NULL,
    `wishlist` text NOT NULL,
    `quotes` text NOT NULL,
    `members` text NOT NULL,
    `members_header` text NOT NULL,
    `members_footer` text NOT NULL,
    `updates` text NOT NULL,
    `form_delete` varchar(255) NOT NULL DEFAULT 'delete.php',
    `form_form` varchar(255) NOT NULL DEFAULT 'site.php',
    `form_join` varchar(255) NOT NULL DEFAULT 'join.php',
    `form_join_comments` tinyint(1) NOT NULL DEFAULT '0',
    `form_join_rules` text NOT NULL,
    `form_reset` varchar(255) NOT NULL DEFAULT 'reset.php',
    `form_update` varchar(255) NOT NULL DEFAULT 'update.php',
    `fave_fields` text NOT NULL,
		`previous` text NOT NULL,
    `date` varchar(255) NOT NULL,
    `since` date NOT NULL,
    `updated` date NOT NULL DEFAULT '1970-01-01',
    `granted` TINYINT(1) NOT NULL DEFAULT '0',
    `markup` enum('html', 'xhtml') NOT NULL DEFAULT 'xhtml',
     PRIMARY KEY (`id`),
     UNIQUE KEY (`subject`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[members]` (
    `mID` mediumint(6) NOT NULL AUTO_INCREMENT,
    `mEmail` varchar(255) NOT NULL,
    `fNiq` mediumint(6) UNSIGNED NOT NULL,
    `mName` varchar(25) NOT NULL,
    `mURL` varchar(255) NOT NULL,
    `mCountry` varchar(100) NOT NULL,
    `mPassword` varchar(255) NOT NULL,
    `mExtra` text NOT NULL,
    `mVisible` tinyint(1) NOT NULL DEFAULT '0',
    `mPending` tinyint(1) NOT NULL DEFAULT '0',
    `mUpdate` enum('y', 'n') NOT NULL DEFAULT 'n',
    `mEdit` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
    `mAdd` date NOT NULL,
     PRIMARY KEY (`mID`),
     UNIQUE KEY `mName` (`fNiq`, `mEmail`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[success]` (
    `messID` mediumint(4) NOT NULL AUTO_INCREMENT,
    `messType` varchar(255) NOT NULL,
    `messURL` varchar(255) NOT NULL,
    `messText` text NOT NULL,
    `messInfo` text NOT NULL,
    `messAdded` datetime NOT NULL,
     PRIMARY KEY (`messID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[templates]` (
    `name` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `template` text NOT NULL,
     UNIQUE KEY (`name`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $insert = "INSERT INTO `$_ST[templates]` (`name`, `title`, `template`)";
                        $insert .= " VALUES ('affiliates_template', 'Affiliates', ''),
    ('collective_stats_template', 'Collective Stats', ''),
    ('date_template', 'Date', 'F j, Y'),
    ('listings_template', 'Listings', ''), 
    ('joined_template', 'Joined', ''), 
    ('kim_stats_template', 'KIM Stats', ''),
    ('updates_template', 'Updates', ''),
    ('wishlist_top_template', 'Wishlist: Top', ''), 
    ('wishlist_granted_template', 'Wishlist: Granted', ''), 
    ('wishlist_query_template', 'Wishlist: Query', ''), 
    ('wishlist_template', 'Wishlist', '')";
                        $scorpions->query("SET NAMES 'utf8';");
                        $true = $scorpions->query($insert);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $insert . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[templates_emails]` (
    `name` varchar(255) NOT NULL,
    `title` varchar(255) NOT NULL,
    `template` text NOT NULL,
     UNIQUE KEY (`name`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        require('templates.inc.php');
                        $insert = "INSERT INTO `$_ST[templates_emails]` (`name`, `title`, `template`)";
                        $insert .= " VALUES ('affiliates_approve', 'Affiliates: Approve', '$affa'),
    ('affiliates_closed', 'Affiliates: Closed', '$affc'),
    ('affiliates_moved', 'Affiliates: Moved', '$affm'),
    ('kim_approve', 'KIM: Approve', '$kima'),
    ('kim_update', 'KIM: Update', '$kimu'),
    ('members_approve', 'Members: Approve', '$mema'), 
    ('members_closed', 'Members: Closed', '$memc'),
    ('members_delete', 'Members: Delete', '$memd'), 
    ('members_lostpass', 'Members: Reset Password', '$meml'),
    ('members_moved', 'Members: Moved', '$memm'),
    ('members_update', 'Members: Update', '$memu')";
                        $scorpions->query("SET NAMES 'utf8';");
                        $true = $scorpions->query($insert);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $insert . '</em></p>');
                        }

                        echo $tigers->displaySuccess('If you experienced zero errors, your main' .
                            ' functions were created, and the first part of the installation was' .
                            ' completed!');
                        echo "<p class=\"nextStep\"><a href=\"install.php?step=2\">Next Step</a></p>\n";
                    } /**
                     * Install Features
                     */
                    elseif (isset($_POST['action']) && $_POST['action'] == 'Install Features') {
                        $create = "CREATE TABLE `$_ST[affiliates]` (
    `aID` mediumint(6) NOT NULL AUTO_INCREMENT,
	  `fNiq` varchar(255) NOT NULL DEFAULT '!0!',
	  `aSubject` varchar(255) NOT NULL,
	  `aEmail` varchar(255) NOT NULL,
	  `aURL` varchar(255) NOT NULL,
	  `aImage` varchar(255) NOT NULL,
	  `aAdd` date NOT NULL,
	   PRIMARY KEY (`aID`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[joined]` (
    `jID` mediumint(5) NOT NULL AUTO_INCREMENT,
    `jSubject` varchar(150) NOT NULL,
    `jURL` varchar(255) NOT NULL,
    `jImage` varchar(255) NOT NULL,
    `jCategory` varchar(255) NOT NULL,
    `jMade` enum('y', 'n') NOT NULL DEFAULT 'n',
    `jStatus` tinyint(1) NOT NULL DEFAULT '0',
    `jAdd` date NOT NULL,
     PRIMARY KEY (`jID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        $create = "CREATE TABLE `$_ST[wishlist]` (
    `wID` smallint(3) NOT NULL AUTO_INCREMENT,
    `wSubject` varchar(255) NOT NULL,
    `wURL` varchar(255) NOT NULL,
    `wImage` varchar(255) NOT NULL,
    `wCategory` varchar(50) NOT NULL,
    `wDesc` text NOT NULL,
    `wType` enum('custom', 'granted', 'list', 'top') NOT NULL,
     PRIMARY KEY (`wID`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                        $true = $scorpions->query($create);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $create . '</em></p>');
                        }

                        echo $tigers->displaySuccess("If you haven't come across any errors, your" .
                            ' features were created, and the second part of the installation was' .
                            ' completed!');
                        echo "<p class=\"nextStep\"><a href=\"install.php?step=3\">Next Step</a></p>\n";
                    } /**
                     * Install Addons!
                     */
                    elseif (isset($_POST['action']) && $_POST['action'] == 'Install Addons') {
                        $get_addon_array_for_installation = array_diff_key($get_addon_array, array_flip($notSupportedAddons));
                        foreach ($get_addon_array_for_installation as $k => $v) {
                            if (isset($_POST[$k]) && $_POST[$k] == 'y') {
                                $result = $frogs->installAddon($k);
                                if ($result->status == true) {
                                    echo $tigers->displaySuccess("The <strong>$v</strong> addon was installed!");
                                    $seahorses->editOption($k . '_opt', 'y');
                                } else {
                                    exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                        "<br />\n<em>" . $result->message . '</em></p>');
                                }
                            }
                        }

                        echo $tigers->displaySuccess("A-OK, dudes! Now it's time to complete the" .
                            ' installation and get you logged in and clicking away (and probably cursing' .
                            ' me, whatevs)!');
                        echo "<p class=\"nextStep\"><a href=\"install.php?step=4\">Next Step</a></p>\n";
                    } /**
                     * Create password and login~!
                     */
                    elseif (isset($_POST['action']) && $_POST['action'] == 'Finish Installation') {
                        $password = isset($_POST['password']) && !empty($_POST['password']) ?
                            $tigers->cleanMys($_POST['password'], 'y', 'y', 'n') :
                            substr(random_int(99999, 888888), 0, 4) . substr(sha1(date('YmdHis')), 0, 11);
                        $update = "UPDATE `$_ST[options]` SET `text` = MD5('$password') WHERE `name`" .
                            " = 'user_password' LIMIT 1";
                        $true = $scorpions->query($update);
                        if ($true == false) {
                            exit('<p><span class="mysql">Error:</span> ' . $scorpions->error() .
                                "<br />\n<em>" . $update . '</em></p>');
                        }

                        echo $tigers->displaySuccess("Success! You've completed the installation." .
                            ' Below is your information; save it, keep it safe, and/or change it once' .
                            ' you login.');
                        echo '<code><samp>Username:</samp> ' . $seahorses->getOption('user_username') .
                            "<br>\n<samp>Password:</samp> $password</code>\n";
                        echo '<p class="nextStep"><a href="http://' . $_SERVER['SERVER_NAME'] .
                            str_replace('install.php', '', $_SERVER['PHP_SELF']) . "\">Login!</a></p>\n";
                    }
                } else {
                    echo $frogs->showWelcome();
                }
                break;
        }
        ?>
    </section>

</div>

</body>
</html>
