<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <options.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;

$getTitle = 'Options';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if (isset($_POST['action']) && $_POST['action'] == 'Edit Options') {
    $opt = $tigers->cleanMys($_POST['opt']);
    $optArray = array('1', '2', '3', '4', '5', '6');
    $optValue = $get_option_array;
    if (empty($opt) || !is_numeric($opt) || !in_array($opt, $optArray)) {
        $tigers->displayError('Form Error', 'You can only edit listing details,' .
            ' crosslisting, options and templates!', false);
    }
    $ynArray = array('y', 'n');

    /**
     * Start editing variables here! First, we get our details, such as
     * the username, password, site name, et al. 8D
     */
    if ($opt == '1') {
        $collective_name = $tigers->cleanMys($_POST['cname']);
        if (empty($collective_name)) {
            $tigers->displayError('Form Error', 'Your <samp>collective name</samp>' .
                ' is empty. Go back and enter a name.', false);
        }
        if ($seahorses->getOption('collective_name') != $collective_name) {
            $seahorses->editOption('collective_name', $collective_name);
        }
        $my_nameNow = $tigers->cleanMys($_POST['my_name']);
        if (empty($my_nameNow)) {
            $tigers->displayError('Form Error', 'Your <samp>name</samp> is empty.' .
                ' Go back and enter a name.', false);
        } elseif (strlen($my_nameNow) > 20) {
            $tigers->displayError('Form Error', 'Your <samp>name</samp> is too' .
                ' long. Go back and shorten it.</p>', false);
        }
        if ($seahorses->getOption('my_name') != $my_nameNow) {
            $seahorses->editOption('my_name', $my_nameNow);
        }
        $my_emailNow = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['my_email']));
        if (empty($my_emailNow)) {
            $tigers->displayError('Form Error', 'Your <samp>e-mail address</samp>' .
                ' is empty. Go back and enter an valid e-mail address.', false);
        } elseif (!StringUtils::instance()->isEmailValid($my_emailNow)) {
            $tigers->displayError('Form Error', 'The <samp>e-mail address</samp>' .
                ' you supplied appears to be invalid. Go back and try again.', false);
        } elseif ($my_emailNow == 'you@yourdomain.com') {
            $tigers->displayError('Form Error', '<samp>you@yourdomain.com</samp>' .
                ' is not a valid e-mail address. Go back and enter a valid email address.',
                false);
        }
        if ($seahorses->getOption('my_email') != $my_emailNow) {
            $seahorses->editOption('my_email', $my_emailNow);
        }
        $my_url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['my_website']));
        if (empty($my_url)) {
            $tigers->displayError('Form Error', 'Your <samp>website</samp>' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isUrlValid($my_url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
                ' not valid. Please supply a valid site URL or empty the field.', false);
        }
        if ($seahorses->getOption('my_website') != $my_url) {
            $seahorses->editOption('my_website', $my_url);
        }
        $my_username = $tigers->cleanMys($_POST['myusername']);
        if (empty($my_username)) {
            $tigers->displayError('Form Error', 'Your <samp>username</samp>' .
                ' is empty.', false);
        }
        if ($seahorses->getOption('user_username') != $my_username) {
            $seahorses->editOption('user_username', $my_username);
        }
        $my_password_curren = $tigers->cleanMys($_POST['mypasswordc']);
        $my_password_valid1 = $tigers->cleanMys($_POST['mypasswordn']);
        $my_password_valid2 = $tigers->cleanMys($_POST['mypasswordv']);
        if (($my_password_valid1 !== $my_password_valid2)) {
            $tigers->displayError('Form Error', 'If you\'re changing your' .
                ' password, your passwords need to match, and cannot be under twelve' .
                ' characters.', false);
        }
        if (!empty($my_password_valid1) && strlen($my_password_valid1) < 8) {
            $tigers->displayError('Form Error', 'If you\'re changing your' .
                ' password, your passwords need to match, and cannot be under eight' .
                ' characters. (<a href="http://ow.ly/c57V7">This article &#187;</a> explains' .
                ' more about why this is important. ;))', false);
        }
        if (
            ((($my_password_valid1 == $my_password_valid2) &&
                    (!empty($my_password_valid1) && !empty($my_password_valid2))
                ) &&
                ($leopards->checkUser('', md5($_POST['mypasswordc'])) == 0)) &&
            ($seahorses->getOption('user_password') != md5($_POST['mypasswordn']))
        ) {
            $ps = $tigers->cleanMys($_POST['mypasswordn'], 'y', 'y', 'n');
            $update = "UPDATE $_ST[options] SET `text` = MD5('$ps') WHERE `name` =" .
                " 'user_password' LIMIT 1";
            $true = $scorpions->query($update);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to edit' .
                    ' your password.', false);
            } else {
                echo '<p class="successButton"><span class="success">Success!</span> Your' .
                    " password has been updated!</p>\n";
                $tigers->logout();
            }
        }
        $mypasshint = $tigers->cleanMys($_POST['mypasshint']);
        if (empty($mypasshint)) {
            $tigers->displayError('Form Error', 'Your <samp>password hint</samp>' .
                ' is empty.', false);
        }
        if ($seahorses->getOption('user_passhint') != $mypasshint) {
            $seahorses->editOption('user_passhint', $mypasshint);
        }
    } elseif ($opt == '2') {
        $per_joined = $tigers->cleanMys($_POST['per_joined']);
        if (!is_numeric($per_joined) || strlen($per_joined) > 2) {
            $tigers->displayError('Form Error', 'Your <samp>joined pagination</samp>' .
                ' field is invalid; make sure the field is a number, and is no longer than' .
                ' two digits (e.g., greated than 99).', false);
        }
        if ($seahorses->getOption('per_joined') != $per_joined) {
            $seahorses->editOption('per_joined', $per_joined);
        }
        $jnd_http = $tigers->cleanMys($_POST['jnd_http']);
        $jnd_path = $tigers->cleanMys($_POST['jnd_path']);
        if (substr($jnd_http, -1) != '/' || substr($jnd_path, -1) != '/') {
            $tigers->displayError('Form Error', 'The joined images URL and/or' .
                ' joined images path do not have trailing slashes.', false);
        }
        if ($seahorses->getOption('jnd_path') != $jnd_path) {
            $seahorses->editOption('jnd_path', $jnd_path);
        }
        if ($seahorses->getOption('jnd_http') != $jnd_http) {
            $seahorses->editOption('jnd_http', $jnd_http);
        }
        $wishlist_opt_input = $tigers->cleanMys($_POST['wishlist_opt_input']);
        $wishlist_granted_opt = $tigers->cleanMys($_POST['wishlist_granted_opt']);
        if (in_array($wishlist_opt_input, $ynArray) && in_array($wishlist_granted_opt, $ynArray)) {
            if ($seahorses->getOption('wishlist_opt') != $wishlist_opt_input) {
                $seahorses->editOption('wishlist_opt', $wishlist_opt_input);
            }
            if ($seahorses->getOption('wishlist_granted') != $wishlist_granted_opt) {
                $seahorses->editOption('wishlist_granted', $wishlist_granted_opt);
            }
        }
        $wsh_path = $tigers->cleanMys($_POST['wsh_path']);
        $wsh_http = $tigers->cleanMys($_POST['wsh_http']);
        if (substr($wsh_http, -1) != '/' || substr($wsh_path, -1) != '/') {
            $tigers->displayError('Form Error', 'The wishlist images URL and/or' .
                ' wishlist images path do not have trailing slashes.', false);
        }
        if ($seahorses->getOption('wsh_path') != $wsh_path) {
            $seahorses->editOption('wsh_path', $wsh_path);
        }
        if ($seahorses->getOption('wsh_http') != $wsh_http) {
            $seahorses->editOption('wsh_http', $wsh_http);
        }
    } elseif ($opt == '3') {
        // nothing
    } elseif ($opt == '4') {
        $notify = $tigers->cleanMys($_POST['approval']);
        if (empty($notify) || !in_array($notify, $ynArray)) {
            $notify = 'y';
        }
        if ($seahorses->getOption('notify_approval') != $notify) {
            $seahorses->editOption('notify_approval', $notify);
        }
        $markupopt = $tigers->cleanMys($_POST['markupopt']);
        if (empty($markupopt) || !array_key_exists($markupopt, $get_markup_array)) {
            $tigers->displayError('Form Error', 'Your <samp>markup</samp> option' .
                ' is invalid.', false);
        }
        if ($seahorses->getOption('markup') != $markupopt) {
            $seahorses->editOption('markup', $markupopt);
        }
        $linksopt = $tigers->cleanMys($_POST['formatlinks']);
        if ($seahorses->getOption('format_links') != $linksopt) {
            $seahorses->editOption('format_links', $linksopt);
        }
        $per_members = $tigers->cleanMys($_POST['per_members']);
        $per_page = $tigers->cleanMys($_POST['per_page']);
        if (
            (!is_numeric($per_members) || !is_numeric($per_page)) ||
            (strlen($per_members) > 2 || strlen($per_page) > 2)
        ) {
            $tigers->displayError('Form Error', 'Your pagination fields are invalid;' .
                ' make sure the fields are numbers, and are no longer than two digits (e.g.,' .
                ' greated than 99).', false);
        }
        if ($seahorses->getOption('per_members') != $per_members) {
            $seahorses->editOption('per_members', $per_members);
        }
        if ($seahorses->getOption('per_page') != $per_page) {
            $seahorses->editOption('per_page', $per_page);
        }
        $adm_path = $tigers->cleanMys($_POST['adm_path']);
        $adm_http = $tigers->cleanMys($_POST['adm_http']);
        $aff_path = $tigers->cleanMys($_POST['aff_path']);
        $aff_http = $tigers->cleanMys($_POST['aff_http']);
        $img_path = $tigers->cleanMys($_POST['img_path']);
        $img_http = $tigers->cleanMys($_POST['img_http']);
        if (
            (substr($adm_http, -1) != '/' || substr($adm_path, -1) != '/') ||
            (substr($aff_http, -1) != '/' || substr($aff_path, -1) != '/') ||
            (substr($img_http, -1) != '/' || substr($img_path, -1) != '/')
        ) {
            $tigers->displayError('Form Error', 'One or more of your paths need' .
                ' trailing slashes; make sure to go back and add them.', false);
        } elseif (empty($adm_path) || empty($adm_http)) {
            $tigers->displayError('Form Error', 'In order for certain features of' .
                ' Listing Admin to work, the admin paths must be filled out, love! ;;', false);
        }
        if ($seahorses->getOption('adm_path') != $adm_path) {
            $seahorses->editOption('adm_path', $adm_path);
        }
        if ($seahorses->getOption('adm_http') != $adm_http) {
            $seahorses->editOption('adm_http', $adm_http);
        }
        if ($seahorses->getOption('aff_path') != $aff_path) {
            $seahorses->editOption('aff_path', $aff_path);
        }
        if ($seahorses->getOption('aff_http') != $aff_http) {
            $seahorses->editOption('aff_http', $aff_http);
        }
        if ($seahorses->getOption('img_path') != $img_path) {
            $seahorses->editOption('img_path', $img_path);
        }
        if ($seahorses->getOption('img_http') != $img_http) {
            $seahorses->editOption('img_http', $img_http);
        }
    } elseif ($opt == '5') {
        $akismet_opt_input = $tigers->cleanMys($_POST['akismet_opt_input']);
        $akismet_key_input = $tigers->cleanMys($_POST['akismet_key_input']);
        $captcha_opt_input = $tigers->cleanMys($_POST['captcha_opt_input']);
        $javascript_opt_input = $tigers->cleanMys($_POST['javascript_opt_input']);
        $javascript_key_input = $tigers->cleanMys($_POST['javascript_key_input']);
        if (
            !in_array($akismet_opt_input, $ynArray) ||
            !in_array($captcha_opt_input, $ynArray) ||
            !in_array($javascript_opt_input, $ynArray)
        ) {
            $tigers->displayError('Form Error', 'One or more of the plugins need to' .
                ' be set to "On" or "Off".', false);
        }
        if ($akismet_opt_input == 'y' && empty($akismet_key_input)) {
            $tigers->displayError('Form Error', 'If the Akismet plugin is set to' .
                ' "On", you need to fill out your Akismet key.', false);
        }
        if (isset($_POST['generate_js']) && $_POST['generate_js'] == 'y') {
            $javascript_key_input2 = substr(sha1(date('YmdHis')), 0, 15) . substr(sha1(random_int(9000, 985999)), 0, 10);
        } else {
            $javascript_key_input2 = $javascript_key_input;
        }
        if ($seahorses->getOption('akismet_opt') != $akismet_opt_input) {
            $seahorses->editOption('akismet_opt', $akismet_opt_input);
        }
        if ($seahorses->getOption('akismet_key') != $akismet_key_input) {
            $seahorses->editOption('akismet_key', $akismet_key_input);
        }
        if ($seahorses->getOption('captcha_opt') != $captcha_opt_input) {
            $seahorses->editOption('captcha_opt', $captcha_opt_input);
        }
        if ($seahorses->getOption('javascript_opt') != $javascript_opt_input) {
            $seahorses->editOption('javascript_opt', $javascript_opt_input);
        }
        if ($seahorses->getOption('javascript_key') != $javascript_key_input2) {
            $seahorses->editOption('javascript_key', $javascript_key_input2);
        }
    } elseif ($opt == '6') {
        $antispam_opt_input = $tigers->cleanMys($_POST['antispam_opt_input']);
        if (!in_array($antispam_opt_input, $ynArray)) {
            $tigers->displayError('Form Error', 'The anti-SPAM field is invalid.', false);
        }
        if ($seahorses->getOption('antispam_opt') != $antispam_opt_input) {
            $seahorses->editOption('antispam_opt', $antispam_opt_input);
        }
        if (isset($_POST['spamword'])) {
            $spam_words_input = $_POST['spamword'];
            $spam_words_input = array_map(array($tigers, 'cleanMys'), $spam_words_input);
            natcasesort($spam_words_input);
            $spam_words_opt = implode('|', $spam_words_input);
            $spam_words_opt = trim($spam_words_opt, '|');
            if ($seahorses->getOption('antispam_spam_words') != $spam_words_opt) {
                $seahorses->editOption('antispam_spam_words', $spam_words_opt);
            }
        }
    }

    echo '<p class="successButton"><span class="success">SUCCESS!</span> Your options have been updated!</p>';
    if ($opt != 1 && $opt != '1') {
        echo $tigers->backLink('options', $optValue[$opt]);
    }
    echo $tigers->backLink('options');
} else {
    $optionstitle = isset($_GET['g']) && in_array($_GET['g'],
        array_values($get_option_array)) ? $get_option_nav_array[$_GET['g']] : 'Details';
    ?>
    <div id="mainContent">
        <h3><?php echo $optionstitle; ?></h3>
        <?php
        if (isset($_GET['g']) && $_GET['g'] == 'antispam') {
            ?>
            <p>Anti-SPAM is a plugin I wrote exclusively for Listing Admin. It's features
                include a point system, a mathematical problem and SPAM words. You can turn
                anti-SPAM for the join and contact forms on and off, and add your own SPAM
                words.</p>

            <form action="options.php" method="post">
                <p class="noMargin"><input name="opt" type="hidden" value="6"></p>

                <fieldset>
                    <legend>Anti-SPAM</legend>
                    <p><label>On or Off:</label> <select name="antispam_opt_input" class="input1">
                            <?php
                            $anArray = array('y' => 'On', 'n' => 'Off');
                            foreach ($anArray as $anKey => $anVal) {
                                echo '<option value="' . $anKey . '"';
                                if ($anKey == $seahorses->getOption('antispam_opt')) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $anVal . "</option>\n";
                            }
                            ?>
                        </select></p>
                </fieldset>

                <fieldset>
                    <legend>Anti-SPAM: SPAM Words</legend>
                    <p class="noteButton"><span class="note">Note:</span> The disabled fields are
                        SPAM words you
                        <ins>can't</ins>
                        delete, and are there to give an idea of what
                        to add. You are free to add, edit and delete any other SPAM words.
                    </p>
                    <p class="spam_words_req tc">
                        <?php
                        $_spam_words_req = explode('|', $seahorses->getOption('antispam_spam_words_required'));
                        $_spam_words_req = $tigers->emptyarray($_spam_words_req);
                        foreach ($_spam_words_req as $sw) {
                            $rq = substr(md5($sw), 0, 8);
                            echo '  <input name="' . $rq . '" class="input1" disabled="disabled" type="text" value="' . $sw . "\">\n";
                        }
                        ?>
                    </p>
                    <hr>
                    <div class="spam_words">
                        <?php
                        $_spam_words = explode('|', $seahorses->getOption('antispam_spam_words'));
                        $_spam_words = $tigers->emptyarray($_spam_words);
                        if ((is_countable($_spam_words) ? count($_spam_words) : 0) > 0) {
                            foreach ($_spam_words as $sr) {
                                $rq = substr(md5($sw), 0, 8);
                                echo '  <input name="spamword[]" class="input1" type="text" value="' . $sr . "\" />\n";
                            }
                        } else {
                            echo "  <p class=\"tc\">No custom SPAM words have been added!</p>\n";
                        }

                        $ur = 'options.php?g=antispam';
                        $ur .= !isset($_GET['c']) ? '&#38;c=2' : '&#38;c=' . ($_GET['c'] + 1);
                        $ur .= '#add';
                        ?>
                    </div>
                    <div id="add">
                        <p class="tc"><input name="spamword[]" class="input1" type="text">
                            <?php
                            if (!isset($_GET['c'])) {
                                echo '   <span class="add"><a href="' . $ur . '">[+]</a></span>';
                            } else {
                                echo '   <span class="add">[+]</span>';
                            }
                            ?>
                        </p>
                        <?php
                        if (isset($_GET['c'])) {
                            for ($i = 1; $i < $_GET['c']; $i++) {
                                echo '  <p class="tc"><input name="spamword[]" class="input1" type="text" />';
                                if ($i == ($_GET['c'] - 1)) {
                                    echo ' <span class="add"><a href="' . $ur . '">[+]</a></span>';
                                } else {
                                    echo ' <span class="add">[+]</span>';
                                }
                                echo "</p>\n";
                            }
                        }
                        ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Submit</legend>
                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>
            <?php
        } elseif (isset($_GET['g']) && $_GET['g'] == 'features') {
            ?>
            <form action="options.php" method="post">
                <p class="noMargin"><input name="opt" type="hidden" value="2"></p>

                <fieldset>
                    <legend>Joined</legend>
                    <p><label><strong>Joined Pagination:</strong><br>
                            You can set the number of MySQL results for <samp>joined.php</samp> in the
                            admin panel. By default, the joined pagination is set to 30.
                        </label> <input name="per_joined" class="input1" type="text"
                                        value="<?php echo $seahorses->getOption('per_joined'); ?>"></p>
                    <p class="clearBottom"></p>
                    <p><label><strong>Joined Paths:</strong><br>
                            Joined paths are for your joined listings. The paths are automatically set for
                            you in the event they were not created during installation; of course, they
                            can changed to the desired path(s).</label>
                        <input name="jnd_path" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('path', '', $seahorses->getOption('jnd_path'),
                                   'images/joined/'); ?>"><br>
                        <input name="jnd_http" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('url', $seahorses->getOption('jnd_http'), '',
                                   'images/joined/'); ?>">
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Wishlist</legend>
                    <p><label><strong>Wishlist Paths:</strong><br>
                            Wishlist paths are for your wishlist images. The paths are automatically set
                            for you in the event they were not added during installation; you can, of
                            course, changed them to your desired paths.</label>
                        <input name="wsh_path" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('path', '', $seahorses->getOption('wsh_path'),
                                   'images/wishlist/'); ?>"><br>
                        <input name="wsh_http" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('url', $seahorses->getOption('wsh_http'), '',
                                   'images/wishlist/'); ?>">
                    </p>
                    <p class="clearBottom"></p>
                    <p><label><strong>Use Listing Images for Granted Wishlist?</strong><br>
                            If "On" is chosen from the drop down menu, the script will use your listing
                            images for your granted wishlist; of course, setting it to "Off" will use the
                            granted images you upload via the <a href="wishlist.php">Wishlist</a> page.
                        </label> <select name="wishlist_granted_opt" class="input1">
                            <?php
                            $ws2Array = array('y' => 'On', 'n' => 'Off');
                            foreach ($ws2Array as $ws2Key => $ws2Val) {
                                echo '  <option value="' . $ws2Key . '"';
                                if ($ws2Key == $seahorses->getOption('wishlist_granted')) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $ws2Val . "</option>\n";
                            }
                            ?>
                        </select></p>
                </fieldset>

                <fieldset>
                    <legend>Submit</legend>
                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>
            <?php
        } elseif (isset($_GET['g']) && $_GET['g'] == 'import') {
            ?>
            <p class="scriptButton"><span class="script"><b>Notice:</b></span> This functionality has not been properly
                tested in <?= $laoptions->version ?>. Please report any issues if such occur.
            </p>
            <p class="noteButton">You can use the <samp>Import/Export</samp> section to
                import affiliates and members from other scripts, as well as categories and
                joined from Enthusiast, updates and codes from FanUpdate and CodeSort
                respectively, and export your affiliates and members to other scripts! :D</p>

            <form action="convert.php" enctype="multipart/form-data" method="post">
                <p class="noMargin">
                    <input name="opttype" type="hidden" value="import">
                </p>

                <fieldset>
                    <legend>Import</legend>
                    <p><label><strong>Script:</strong></label> <select name="script" class="input1">
                            <?php
                            $scriptarray = $get_script_array;
                            foreach ($scriptarray as $sk => $sv) {
                                echo ' <option';
                                if ($sk == 'enthusiast') {
                                    echo ' selected="selected"';
                                }
                                echo " value=\"$sk\">$sv</option>\n";
                            }
                            ?>
                        </select></p>
                    <p><label><strong>What are you importing?</strong><br>
                            Please be aware there are only certain things you can import depending on the
                            script you choose. For instance, you can only import categories and joined from
                            Enthusiast, codes from CodeSort, updates from FanUpdate, and so on. (Members
                            and affiliates can be imported from all related scripts &#8211; BellaBuffs,
                            Enthusiast, Listing Admin, and phpFanUpdate.)
                        </label>
                        <select name="importcat" id="importcat" class="input1">
                            <?php
                            $importarray = $get_import_cats_array;
                            foreach ($importarray as $ik => $iv) {
                                echo " <option class=\"$ik\"";
                                if ($ik == 'members') {
                                    echo ' selected="selected"';
                                }
                                echo " value=\"$ik\">$iv</option>\n";
                            }
                            ?>
                        </select></p>
                    <p class="clearBottom"></p>
                    <div id="joined" style="display: none;">
                        <p><label>
                                <strong>Import joined categories from the old table?</strong><br>
                                By clicking the button next to "Yes", you're importing the joined listings
                                with the category IDs they were added/edited with in your <em>old</em>
                                installation -- if you did not import your categories from your old
                                Enthusiast/Listing Admin installation, the categories are likely to be out
                                of order.
                            </label>
                            <input name="importjoined" class="input3" type="radio" value="y"> Yes
                            <input name="importjoined" checked="checked" class="input3" type="radio" value="n"> No</p>
                        <p class="clearBottom"></p>
                    </div>
                    <div id="updates" style="display: none;">
                        <p><label>
                                <strong>Import crossposting settings from Listing Admin updates table?</strong><br>
                                You only need to click the button next to "Yes" if you're importing from
                                Listing Admin and would like your crosspost journal (Dreamwidth,
                                Insanejournal and Livejournal) settings saved.
                            </label>
                            <input name="importcp" class="input3" type="radio" value="y"> Yes
                            <input name="importcp" checked="checked" class="input3" type="radio" value="n"> No</p>
                        <p class="clearBottom"></p>
                        <p><label><strong>'catjoin' Table:</strong><br>
                                The <samp>catjoin</samp> table for your FanUpdate installation; usually
                                <samp>blog_catjoin</samp>. You only need to fill this out if you're importing
                                from FanUpdate.</label>
                            <input name="tablenamecj" class="input1" type="text"></p>
                        <p class="clearBottom"></p>
                    </div>
                    <p><label><strong>Are we importing a file?</strong><br>
                            This should
                            <ins>only</ins>
                            be used if we're importing a .txt file from
                            Listing Admin or BellaBuffs &#8211; we'll be pulling members from the database
                            with Enthusiast and phpFanUpdate. (If you have an .sql file from Enthusiast and
                            phpFanUpdate, simply import it to one of your databases; you can delete the
                            table afterwards.)</label>
                        <input name="importfile" class="input1" type="file"></p>
                    <p class="clearBottom"></p>
                    <p><label><strong>Listing ID</strong><br>
                            This will be the listing ID we're importing to; if you're importing to the
                            collective, leave the field blank.</label>
                        <input name="fanlistingid" class="input1" type="text"></p>
                    <p class="clearBottom"></p>
                    <p><label><strong>Old Listing ID</strong><br>
                            This will be the listing ID we're importing <em>from</em>; you only need to
                            fill this out if you're importing codes or updates from CodeSort and FanUpdate
                            respectively. If you're importing from the collective, leave the field blank.
                        </label>
                        <input name="oldfanlistingid" class="input1" type="text"></p>
                    <p class="clearBottom"></p>

                    <p class="noteButton">The following database settings only need to be filled
                        out if you're importing something from a script other than Listing Admin and
                        BellaBuffs. While the table name is required, the database settings are not,
                        and if left blank, the script will assume the script's database tables are
                        in the same database as your Listing Admin tables.</p>
                    <p><label><strong>Database Host:</strong></label>
                        <input name="dbhost" class="input1" type="text"></p>
                    <p><label><strong>Database Username:</strong></label>
                        <input name="dbuser" class="input1" type="text"></p>
                    <p><label><strong>Database Password:</strong></label>
                        <input name="dbpass" class="input1" type="text"></p>
                    <p><label><strong>Database Name:</strong></label>
                        <input name="dbname" class="input1" type="text"></p>
                    <p><label><strong>Table Name:</strong></label>
                        <input name="tablename" class="input1" type="text"></p>

                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>

            <form action="convert.php" method="post">
                <p class="noMargin">
                    <input name="opttype" type="hidden" value="export">
                </p>

                <fieldset>
                    <legend>Export</legend>
                    <p><label><strong>Script:</strong></label> <select name="script" class="input1">
                            <?php
                            $scriptarray = $get_script_array;
                            foreach ($scriptarray as $sk => $sv) {
                                echo ' <option';
                                if ($sk == 'enthusiast') {
                                    echo ' selected="selected"';
                                }
                                echo " value=\"$sk\">$sv</option>\n";
                            }
                            ?>
                        </select></p>
                    <p><label><strong>What are you exporting?</strong><br>
                            Like with importing, be aware there are only certain things you can export
                            depending on the script you choose. For example, while you can exporting your
                            affiliates and members to almost any script, you can't exporting your
                            affiliates to phpFanBase.</label>
                        <select name="exportcat" class="input1">
                            <?php
                            $exportarray = $get_export_cats_array;
                            foreach ($exportarray as $ek => $ev) {
                                echo ' <option';
                                if ($ek == 'members') {
                                    echo ' selected="selected"';
                                }
                                echo " value=\"$ek\">$ev</option>\n";
                            }
                            ?>
                        </select></p>
                    <p class="clearBottom"></p>
                    <p><label><strong>Listing ID</strong><br>
                            This will be the listing ID we're exporting (i.e. pulling affiliates/members
                            from).</label>
                        <input name="fanlistingid" class="input1" type="text"></p>
                    <p class="clearBottom"></p>
                    <p><label><strong>Save file to harddrive?</strong><br>
                            If you check the box, the script will prompt you to save a .txt file to your
                            harddrive; if you uncheck the box, the script will display a textbox for you
                            to copy and paste the text there to a .txt or .sql file.
                        </label> <input name="savefile" checked="checked" class="input3" type="checkbox" value="y"> Yes
                    </p>
                    <p class="clear"></p>
                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>
            <?php
        } elseif (isset($_GET['g']) && $_GET['g'] == 'options') {
            ?>
            <form action="options.php" method="post">
                <p class="noMargin"><input name="opt" type="hidden" value="4"></p>

                <fieldset>
                    <legend>E-mails</legend>
                    <p><label><strong>Would you like to receive approval e-mails?</strong><br>
                            Set to 'off' if you would <em>not</em> like to receive notifications when
                            someone joins a fanlisting.</label>
                        <input name="approval" class="input3"
                            <?php if ($seahorses->getOption('notify_approval') == 'y') {
                                echo ' checked="checked"';
                            } ?>
                               type="radio" value="y"> On
                        <input name="approval" class="input3"
                            <?php if ($seahorses->getOption('notify_approval') == 'n') {
                                echo ' checked="checked"';
                            } ?>
                               type="radio" value="n"> Off
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Formatting Options</legend>
                    <p><label><strong>Markup:</strong><br>You can choose between HTML, HTML5 or
                            XHTML markup for your collective (listings, joined, updates, et al.)</label>
                        <select name="markupopt" class="input1">
                            <?php
                            $htArray = $get_markup_array;
                            foreach ($htArray as $htKey => $htVal) {
                                echo '<option value="' . $htKey . '"';
                                if ($htKey == $seahorses->getOption('markup')) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $htVal . "</option>\n";
                            }
                            ?>
                        </select></p>
                    <p class="clearBottom"></p>
                    <p><label><strong>External Link:</strong><br>
                            You can place a HTML entity -- or piece of text -- to the right of any
                            external links; if you don't want external links marked, leave the field blank.
                            Example: if you'd like the &#187; symbol added to the right of your
                            external links, you'd enter in <samp>&#38;raquo;</samp>, and the result would
                            look like this:
                            <samp>&#60;a href=&#34;http://wyngs.net/&#34;&#62;Wyngs &#38;raquo;&#60;/a&#62;</samp>
                        </label>
                        <?php
                        $lk = $seahorses->getOption('format_links');
                        if (strpos($lk, '&amp;') !== false || strpos($lk, '&#38;') !== false) {
                            $links = $lk;
                        } else {
                            $links = str_replace('&', '&amp;', $lk);
                        }
                        ?>
                        <input name="formatlinks" class="input1" type="text" value="<?php echo $links; ?>"></p>
                </fieldset>

                <fieldset>
                    <legend>Pagination</legend>
                    <p><label><strong>Per Members:</strong><br>
                            Pagination for <samp>members.php</samp>; this is the amount of results
                            displayed per page
                        </label>
                        <input name="per_members" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('per_members'); ?>"></p>
                    <p style="clear: both; margin: 0 0 2% 0;"></p>
                    <p><label><strong>Per Page:</strong><br>
                            Pagination for the admin panel as a whole. This displays the amount of results
                            displayed per page, and covers Listings, Joined and Wishlist, to namen a few.
                        </label>
                        <input name="per_page" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('per_page'); ?>"></p>
                </fieldset>

                <fieldset>
                    <legend>Paths</legend>
                    <p><label><strong>Admin Paths:</strong><br>
                            Admin paths are the absolute path and <abbr title="Uniform Resource Identifier">URI</abbr>
                            path to your admin panel.</label>
                        <input name="adm_path" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('path', '', $seahorses->getOption('adm_path'),
                                   ''); ?>"><br>
                        <input name="adm_http" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('url', $seahorses->getOption('adm_http'), '',
                                   ''); ?>">
                    </p>
                    <p style="clear: both; margin: 0 0 2% 0;"></p>
                    <p><label><strong>Affiliate Paths:</strong><br>
                            Affiliate paths are the absolute and URL paths for your affiliate images.</label>
                        <input name="aff_path" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('path', '', $seahorses->getOption('aff_path'),
                                   'images/affiliates/'); ?>"><br>
                        <input name="aff_http" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('url', $seahorses->getOption('aff_http'), '',
                                   'images/affiliates/'); ?>">
                    </p>
                    <p style="clear: both; margin: 0 0 2% 0;"></p>
                    <p><label><strong>Image Paths:</strong><br>
                            Image paths are the absolute and URL paths to your listing images.</label>
                        <input name="img_path" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('path', '', $seahorses->getOption('img_path'),
                                   'images/'); ?>"><br>
                        <input name="img_http" class="input1" type="text"
                               value="<?php echo $leopards->getPaths('url', $seahorses->getOption('img_http'), '',
                                   'images/'); ?>">
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Submit</legend>
                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>
            <?php
        } elseif (isset($_GET['g']) && $_GET['g'] == 'plugins') {
            ?>
            <p>All plugins are optional, and minus Akismet, have all been written by me.</p>

            <form action="options.php" method="post">
                <p class="noMargin"><input name="opt" type="hidden" value="5"></p>

                <fieldset>
                    <legend>Akismet</legend>
                    <p><label><strong>On or Off:</strong></label>
                        <select name="akismet_opt_input" class="input1">
                            <?php
                            $akArray = array('y' => 'On', 'n' => 'Off');
                            foreach ($akArray as $akKey => $akVal) {
                                echo '  <option value="' . $akKey . '"';
                                if ($akKey == $seahorses->getOption('akismet_opt')) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $akVal . "</option>\n";
                            }
                            ?>
                        </select></p>
                    <p><label><strong>Akismet Key:</strong></label> <input name="akismet_key_input" class="input1"
                                                                           type="text"
                                                                           value="<?php echo $seahorses->getOption('akismet_key'); ?>">
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Captcha</legend>
                    <p><label><strong>On or Off:</strong></label> <select name="captcha_opt_input" class="input1">
                            <?php
                            $caArray = array('y' => 'On', 'n' => 'Off');
                            foreach ($caArray as $caKey => $caVal) {
                                echo '  <option value="' . $caKey . '"';
                                if ($caKey == $seahorses->getOption('captcha_opt')) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $caVal . "</option>\n";
                            }
                            ?>
                        </select></p>
                </fieldset>

                <fieldset>
                    <legend>JavaScript</legend>
                    <p><label><strong>On or Off:</strong></label> <select name="javascript_opt_input" class="input1">
                            <?php
                            $jaArray = array('y' => 'On', 'n' => 'Off');
                            foreach ($jaArray as $jaKey => $jaVal) {
                                echo '  <option value="' . $jaKey . '"';
                                if ($jaKey == $seahorses->getOption('javascript_opt')) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $jaVal . "</option>\n";
                            }
                            ?>
                        </select></p>
                    <p><label><strong>JavaScript Key:</strong></label>
                        <input name="javascript_key_input" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('javascript_key'); ?>"></p>
                    <p><label><strong>Generate Javascript Key?</strong></label>
                        <input name="generate_js" class="input3" type="radio" value="y"> Yes
                        <input name="generate_js" checked="checked" class="input3" type="radio" value="n"> No
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Submit</legend>
                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>
            <?php
        } else {
            ?>
            <form action="options.php" method="post">
                <p class="noMargin"><input name="opt" type="hidden" value="1"></p>

                <fieldset>
                    <legend>Web Details</legend>
                    <p><label><strong>Collective Name:</strong></label>
                        <input name="cname" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('collective_name'); ?>"></p>
                    <p><label><strong>Name:</strong></label>
                        <input name="my_name" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('my_name'); ?>"></p>
                    <p><label><strong>E-mail Address:</strong></label>
                        <input name="my_email" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('my_email'); ?>"></p>
                    <p><label><strong>Website:</strong></label>
                        <input name="my_website" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('my_website'); ?>"></p>
                </fieldset>

                <fieldset>
                    <legend>User Details</legend>
                    <p class="tc">User details are for logging in and out of your admin panel, and
                        do not affect the above details.</p>
                    <p><label><strong>Username:</strong></label>
                        <input name="myusername" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('user_username'); ?>"></p>
                    <p><label><strong>Current Password:</strong><br>
                            Current password is only needed if you're changing your password.</label>
                        <input name="mypasswordc" class="input1" type="password"></p>
                    <p style="clear: both; margin: 0 0 1% 0;"></p>
                    <p class="noteButton"><span class="error">NOTE:</span> Your password must be a
                        8 character password, with a minimum of alphanumeric characters; you can have
                        as many symbols as you like. :D (<a href="http://ow.ly/c57V7">This article &#187;</a>
                        explains more about why this requirement is in place; long, unique passwords
                        are important, and protect you, et al. on my lecturing. :'D)</p>
                    <p><label><strong>New Password:</strong><br>
                            Type in your new password twice to change your current one; otherwise, leave blank.</label>
                        <input name="mypasswordn" class="input1" type="password"><br>
                        <input name="mypasswordv" class="input1" type="password"></p>
                </fieldset>

                <fieldset>
                    <legend>Password Hint</legend>
                    <p class="tc">You can keep a password hint on record, in case you forget your
                        password. It's recommended you make it hard to guess, relatable to your
                        password and easy to remember. Be aware that you will only have to fill this
                        out to request a new password. It also must be one word, is not limited in
                        length, and can be mixed with as many characters as you want. :D</p>
                    <p><label><strong>Password Hint:</strong></label>
                        <input name="mypasshint" class="input1" type="text"
                               value="<?php echo $seahorses->getOption('user_passhint'); ?>">
                    </p>
                </fieldset>

                <fieldset>
                    <legend>Submit</legend>
                    <p class="tc"><input name="action" class="input2" type="submit" value="Edit Options"></p>
                </fieldset>
            </form>
            <?php
        }
        ?>
    </div>

    <div id="menuRight">
        <h3>Menu</h3>
        <menu>
            <?php
            $optarray = $get_option_nav_array;
            $in = 0;
            foreach ($optarray as $oa => $ar) {
                $ur = $oa == 'details' ? 'options.php' : 'options.php?g=' . $oa;
                $ca = $in == ((is_countable($optarray) ? count($optarray) : 0) - 1) ? (isset($_GET['g']) &&
                in_array($_GET['g'], array_values($get_option_array)) ?
                    ($oa == $_GET['g'] ? ' class="c last"' : ' class="last"') : ' class="last"') :
                    (isset($_GET['g']) && in_array($_GET['g'], array_values($get_option_array)) ?
                        ($oa == $_GET['g'] ? ' class="c"' : '') : '');
                echo " <li$ca><a href=\"$ur\">$ar</a></li>\n";
                $in++;
            }
            ?>
        </menu>
    </div>
    <?php
}

require('footer.php');
