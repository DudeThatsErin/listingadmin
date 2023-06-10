<?php
/**
 * Addons, addons, where art thou, addons? fnfjnfjfnj ONLY ME. I can't
 * believe after (coming up in December 2011) four years I've managed
 * to include so many addons, they need their own file and panel, COME
 * ON SELF, but what can I say; I'm as hoarding as they come~
 *
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <addons.php>
 * @version          Robotess Fork
 */
require('pro.inc.php');

$getTitle = $leopards->getTitle('Addons');

$lyricsTurnedOn = $cheetahs->isInstalled('lyrics');
$quotesTurnedOn = $cheetahs->isInstalled('quotes');

require('vars.inc.php');
require('header.php');

$get_addon_array_for_installation = array_diff_key($get_addon_array, array_flip($notSupportedAddons));

echo "<h2>$getTitle</h2>\n";
?>
    <div id="menuRight">
        <menu>
            <li><a href="addons.php?sec=codes">Codes</a></li>
            <li><a href="addons.php?sec=kim">KIM</a></li>
            <?php if ($lyricsTurnedOn) { ?>
                <li><a href="addons.php?sec=lyrics">Lyrics (not supported)</a></li>
            <?php } ?>
            <?php if ($quotesTurnedOn) { ?>
                <li><a href="addons.php?sec=quotes">Quotes (not supported)</a></li>
            <?php } ?>
            <li><a href="addons.php?sec=updates">Updates</a></li>
            <li><a href="addons.php?sec=install">&#187; Install</a></li>
            <li class="last"><a href="addons.php">Addons</a></li>
        </menu>
    </div>

    <div id="mainContent">
<?php
if (
    isset($_GET['sec']) &&
    ($_GET['sec'] == 'install' || array_key_exists($_GET['sec'], $get_addon_array))
) {
    $sec = $tigers->cleanMys($_GET['sec']);

    if ($sec == 'codes') {
        $cpfull1 = $seahorses->getOption('codes_img_http');
        $cpfull2 = $seahorses->getOption('codes_img_path');
        if (empty($cpfull1)) {
            $ch = 'http://' . $_SERVER['SERVER_NAME'] . str_replace('addons.php', 'images/codes/', $_SERVER['PHP_SELF']);
        } else {
            $ch = $seahorses->getOption('codes_img_http');
        }
        if (empty($cpfull2)) {
            $cp = str_replace('addons.php', 'images/codes/', $_SERVER['SCRIPT_FILENAME']);
        } else {
            $cp = $seahorses->getOption('codes_img_path');
        }
        ?>
        <form action="addons.php" method="post">
            <input name="sec" type="hidden" value="codes">
            <input name="sech" type="hidden" value="<?php echo sha1($get_addon_array['codes']); ?>">

            <fieldset>
                <legend>Addons &#187; Codes</legend>
                <p><label>On or Off:</label> <select name="codes_opt_input" class="input1">
                        <?php
                        $cdArray = array('y' => 'On', 'n' => 'Off');
                        $cdValue = explode('!', $seahorses->getOption('codes_opt'));
                        foreach ($cdArray as $cdKey => $cdVal) {
                            echo '  <option value="' . $cdKey . '"';
                            if (in_array($cdKey, $cdValue)) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $cdVal . "</option>\n";
                        }
                        ?>
                    </select></p>
                <p><label><strong>Codes Paths:</strong><br>
                        Absolute and <abbr title="Uniform Resource Identifier">URI</abbr> paths to your
                        codes folder.
                    </label> <input name="codes_path" class="input1" type="text" value="<?php echo $cp; ?>"><br>
                    <input name="codes_http" class="input1" type="text" value="<?php echo $ch; ?>"></p>
                <p class="clearBottom"></p>
                <p><label><strong>Codes Order</strong><br>
                        Please enter only <samp>ASC</samp> or <samp>DESC</samp> in the field:</label>
                    <input name="codes_order_input" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('codes_order'); ?>"></p>
            </fieldset>

            <fieldset>
                <legend>Donate Form</legend>
                <p>These options are form the donate form (<samp>show-codes-form.php</samp>),
                    and only need to be changed if you're using the donate form on any of your
                    fanlistings/your collective.</p>
                <p><label><strong>Form URL</strong><br>
                        This can be a page (e.g. <samp>donate.php</samp>) or a URL (e.g.
                        <samp>http://fan.wyngs.net/donate.php</samp>):</label>
                    <input name="codes_formurl_input" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('codes_formurl'); ?>"></p>
                <p class="clearBottom"></p>
                <p><label><strong>Max File Size</strong><br>
                        The max file size is defaulted to 921600 bytes (900 KB); 1024 bytes is equal
                        to 1 KB (kilobyte), so if you'd like your file size limit to 50 KB, that would
                        equal to 51200 bytes. You can find a byte converter
                        <a href="http://www.matisse.net/bitcalc/" title="External Link: matisse.net">at this website
                            &#187;</a>:
                    </label>
                    <input name="codes_filesize_input" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('codes_filesize'); ?>"></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Addons"></p>
            </fieldset>
        </form>
        <?php
    } elseif ($sec == 'install') {
        ?>
        <p class="noteButton">Below you can install and uninstall addons for Listing
            Admin. If the <strong>Install Addon</strong> field displays "No Addons", this
            means all addons for Listing Admin are currently installed; furthermore, if the
            <strong>Uninstall Addon</strong> field displays "No Addons", this means all
            addons for Listing Admin are uninstalled.</p>

        <form action="addons.php" method="post">
            <input name="sec" type="hidden" value="install">
            <input name="sech" type="hidden" value="<?php echo sha1('Install'); ?>">

            <fieldset>
                <legend>Addons &#187; Install</legend>
                <p><label><strong>Install Addon:</strong></label>
                    <select name="installaddon" class="input1">
                        <?php
                        $countaddons = 0;
                        $getaddons = $get_addon_array_for_installation;
                        foreach ($getaddons as $k => $v) {
                            if (!$cheetahs->isInstalled($k)) {
                                echo "  <option value=\"$k\">$v</option>\n";
                                $countaddons++;
                            }
                        }
                        if ($countaddons === 0) {
                            echo "  <option value=\"none\">No Addons</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Addons &#187; Uninstall</legend>
                <p><label><strong>Uninstall Addon:</strong></label>
                    <select name="uninstalladdon" class="input1">
                        <?php
                        $countuaddons = 0;
                        foreach ($get_addon_array as $code => $name) {
                            if ($cheetahs->isInstalled($code)) {
                                echo "  <option value=\"$code\">$name" . (in_array($code, $notSupportedAddons, true) ? ' (not supported anymore, once uninstalled, won\'t be allowed to install)' : '') . "</option>\n";
                                $countuaddons++;
                            }
                        }
                        if ($countuaddons === 0) {
                            echo "  <option value=\"none\">No Addons</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Addons"></p>
            </fieldset>
        </form>
        <?php
    } elseif ($sec == 'kim') {
        ?>
        <form action="addons.php" method="post">
            <input name="sec" type="hidden" value="kim">
            <input name="sech" type="hidden" value="<?php echo sha1($get_addon_array['kim']); ?>">

            <fieldset>
                <legend>Addons &#187; KIM</legend>
                <p><label><strong>On or Off:</strong></label> <select name="kim_opt_input" class="input1">
                        <?php
                        $kmArray = array('y' => 'On', 'n' => 'Off');
                        foreach ($kmArray as $kmKey => $kmVal) {
                            echo '  <option value="' . $kmKey . '"';
                            if ($kmKey == $seahorses->getOption('kim_opt')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $kmVal . "</option>\n";
                        }
                        ?>
                    </select></p>
                <p><label><strong>KIM Join Form:</strong><br>
                        URL to your KIM join form. Examples: <samp>kim.php?join</samp>,
                        <samp>/kim/join.php</samp></label>
                    <input name="kim_join_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('kim_join'); ?>"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>KIM Members List:</strong><br>
                        URL to your KIM members list. Examples: <samp>kim.php?list</samp>,
                        <samp>/kim/list.php</samp></label>
                    <input name="kim_list_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('kim_list'); ?>"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>KIM Reset Form:</strong><br>
                        URL to your KIM lost password form. Examples: <samp>kim.php?reset</samp>,
                        <samp>/kim/lostpass.php</samp></label>
                    <input name="kim_reset_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('kim_reset'); ?>"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>KIM Update Form:</strong><br>
                        URL to your KIM update form. Examples: <samp>kim.php?edit</samp>,
                        <samp>/kim/update.php</samp></label>
                    <input name="kim_update_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('kim_update'); ?>"></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Addons"></p>
            </fieldset>
        </form>
        <?php
    } elseif ($sec == 'lyrics' && $lyricsTurnedOn) {
        ?>
        <p class="scriptButton"><span class="script"><b>Notice:</b></span> This extension is simply legacy hence is not
            supported by current version of the LA script. I would recommend turning it off.</ins>
        </p>
        <form action="addons.php" method="post">
            <input name="sec" type="hidden" value="lyrics">
            <input name="sech" type="hidden" value="<?php echo sha1($get_addon_array['lyrics']); ?>">

            <fieldset>
                <legend>Addons &#187; Lyrics</legend>
                <p class="noteButton">The Lyrics addon power albums and lyrics you can display
                    on your listings.</p>
                <p><label><strong>On or Off:</strong></label> <select name="lyrics_opt_input" class="input1">
                        <?php
                        $lyArray = array('y' => 'On', 'n' => 'Off');
                        foreach ($lyArray as $lyKey => $lyVal) {
                            echo ' <option value="' . $lyKey . '"';
                            if ($lyKey == $seahorses->getOption('lyrics_opt')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $lyVal . "</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Addons"></p>
            </fieldset>
        </form>
        <?php
    } elseif ($sec == 'quotes' && $quotesTurnedOn) {
        ?>
        <p class="scriptButton"><span class="script"><b>Notice:</b></span> This extension is simply legacy hence is not
            supported by current version of the LA script. I would recommend turning it off.</ins>
        </p>
        <form action="addons.php" method="post">
            <input name="sec" type="hidden" value="quotes">
            <input name="sech" type="hidden" value="<?php echo sha1($get_addon_array['quotes']); ?>">

            <fieldset>
                <legend>Addons &#187; Quotes</legend>
                <p><label><strong>On or Off:</strong></label> <select name="quotes_opt_input" class="input1">
                        <?php
                        $qsArray = array('y' => 'On', 'n' => 'Off');
                        foreach ($qsArray as $qsKey => $qsVal) {
                            echo ' <option value="' . $qsKey . '"';
                            if ($qsKey == $seahorses->getOption('quotes_opt')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $qsVal . "</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Addons"></p>
            </fieldset>
        </form>
        <?php
    } elseif ($sec == 'updates') {
        ?>
        <form action="addons.php" method="post">
            <input name="sec" type="hidden" value="updates">
            <input name="sech" type="hidden" value="<?php echo sha1($get_addon_array['updates']); ?>">

            <fieldset>
                <legend>Addons &#187; Updates</legend>
                <p><label>On or Off:</label> <select name="updates_opt_input" class="input1">
                        <?php
                        $upArray = array('y' => 'On', 'n' => 'Off');
                        foreach ($upArray as $upKey => $upVal) {
                            echo '  <option value="' . $upKey . '"';
                            if ($upKey == $seahorses->getOption('updates_opt')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $upVal . "</option>\n";
                        }
                        ?>
                    </select></p>
                <p><label><strong>Updates URL:</strong><br>
                        URL to your updates, e.g.: <samp>http://mywebsite.com/updates.php</samp> (if
                        index file, <samp>http://mywebsite.com/index.php</samp>)</label>
                    <input name="updates_url_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('updates_url'); ?>"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>Pretty URLs:</strong><br>
                        If set to on, will display entries like <samp>http://website.com/e/12/</samp>;
                        if set to off, will display entries like <samp>http://website.com/?e=12</samp>
                    </label> <select name="updates_prettyurls_opt" class="input1">
                        <?php
                        $upArray = array('y' => 'On', 'n' => 'Off');
                        foreach ($upArray as $upKey => $upVal) {
                            echo '  <option value="' . $upKey . '"';
                            if ($upKey == $seahorses->getOption('updates_prettyurls')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $upVal . "</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Comment Settings</legend>
                <p><label><strong>Comments:</strong> On or Off?</label>
                    <?php
                    $coArray = array('y' => 'On', 'n' => 'Off');
                    foreach ($coArray as $coKey => $coVal) {
                        echo '  <input name="updates_comments_opt"';
                        if ($coKey == $seahorses->getOption('updates_comments')) {
                            echo ' checked="checked"';
                        }
                        echo ' class="input3" type="radio" value="' . $coKey . '"> ' . $coVal . "\n";
                    }
                    ?>
                </p>
                <p><label><strong>Comments Header:</strong><br>
                        Header file to your entries (this will be the header in your <em>main</em> directory);
                        make sure include the full path, like so: <samp>/home/username/site/header.php</samp></label>
                    <input name="updates_comments_header_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('updates_comments_header'); ?>"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>Comments Footer:</strong><br>
                        Footer file to your entries (this will be the footer in your <em>main</em> directory);
                        make sure include the full path, like so: <samp>/home/username/site/footer.php</samp></label>
                    <input name="updates_comments_footer_opt" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('updates_comments_footer'); ?>"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>Comment Moderation:</strong> On or Off?<br>
                        If comment moderation is set to on, comments will be held for moderation, and
                        you will need to approve them via the admin panel for them to show up on the
                        entry.</label>
                    <?php
                    $cmArray = array('y' => 'On', 'n' => 'Off');
                    foreach ($cmArray as $cmKey => $cmVal) {
                        echo '  <input name="updates_comments_moderation_opt"';
                        if ($cmKey == $seahorses->getOption('updates_comments_moderation')) {
                            echo ' checked="checked"';
                        }
                        echo ' class="input3" type="radio" value="' . $cmKey . '" /> ' . $cmVal . "\n";
                    }
                    ?>
                </p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>Comment Notification:</strong> On or Off?<br>
                        If comment notification is set to on, you will be e-mailed whenever a person
                        comments.</label>
                    <?php
                    $cnArray = array('y' => 'On', 'n' => 'Off');
                    foreach ($cnArray as $cnKey => $cnVal) {
                        echo ' <input name="updates_comments_notification_opt"';
                        if ($cnKey == $seahorses->getOption('updates_comments_notification')) {
                            echo ' checked="checked"';
                        }
                        echo ' class="input3" type="radio" value="' . $cnKey . '"> ' . $cnVal . "\n";
                    }
                    ?>
                </p>
            </fieldset>

            <fieldset>
                <legend>Plugins</legend>
                <p class="noteButton">The following plugins are for comments
                    <ins>only</ins>
                    . To
                    set each plugin, choose "On" or "Off" from each drop down menu.
                </p>
                <p><label><strong>Akismet:</strong></label> <select name="akismet_opt_input2" class="input1">
                        <?php
                        $ak1Array = array('y' => 'On', 'n' => 'Off');
                        foreach ($ak1Array as $ak1Key => $ak1Val) {
                            echo ' <option value="' . $ak1Key . '"';
                            if ($ak1Key == $seahorses->getOption('updates_akismet')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $ak1Val . "</option>\n";
                        }
                        ?>
                    </select></p>
                <p><label><strong>Anti-SPAM:</strong></label> <select name="antispam_opt_input2" class="input1">
                        <?php
                        $an1Array = array('y' => 'On', 'n' => 'Off');
                        foreach ($an1Array as $an1Key => $an1Val) {
                            echo ' <option value="' . $an1Key . '"';
                            if ($an1Key == $seahorses->getOption('updates_antispam')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $an1Val . "</option>\n";
                        }
                        ?>
                    </select></p>
                <p><label><strong>Akismet Key:</strong></label>
                    <input name="akismet_key_input2" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('updates_akismet_key'); ?>"></p>
                <p><label><strong>Captcha:</strong></label> <select name="captcha_opt_input2" class="input1">
                        <?php
                        $ca1Array = array('y' => 'On', 'n' => 'Off');
                        foreach ($ca1Array as $ca1Key => $ca1Val) {
                            echo '  <option value="' . $ca1Key . '"';
                            if ($ca1Key == $seahorses->getOption('updates_captcha')) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $ca1Val . "</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Cross-Posting Settings</legend>
                <p><label><strong>Dreamwidth:</strong> On or Off?</label>
                    <select name="dw_input" class="cps" id="toggleDW">
                        <?php
                        $dwArray = array('y' => 'On', 'n' => 'Off');
                        $dwValue = explode('!', $seahorses->getOption('updates_crosspost_dw'));
                        foreach ($dwArray as $dwKey => $dwVal) {
                            echo '  <option';
                            if (in_array($dwKey, $dwValue)) {
                                echo ' selected="selected"';
                            }
                            echo ' value="' . $dwKey . '">' . $dwVal . "</option>\n";
                        }
                        ?>
                    </select></p>
                <?php
                if ($seahorses->getOption('updates_crosspost_dw') == 'y') {
                    echo "<div class=\"toggleDW\" style=\"display: block;\">\n";
                } elseif ($seahorses->getOption('updates_crosspost_dw') == 'n') {
                    echo "<div class=\"toggleDW\" style=\"display: none;\">\n";
                }
                ?>
                <p><label><strong>Username:</strong></label>
                    <input name="dw_user_input" class="input1" type="text"
                           value="<?php echo $seahorses->getOption('updates_crosspost_dw_user'); ?>"></p>
                <p><label><strong>Password:</strong><br>
                        Leave blank to keep current password</label>
                    <input name="dw_pass_input" class="input1" type="password"></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>Add Link Back to the Updates Blog?</strong></label>
                    <?php
                    $lbdwArray = array('y' => 'Yes', 'n' => 'No');
                    foreach ($lbdwArray as $lbdwKey => $lbdwVal) {
                        echo '<input name="updates_linkback_dw_opt"';
                        if ($lbdwKey == $seahorses->getOption('updates_crosspost_dw_link')) {
                            echo ' checked="checked"';
                        }
                        echo ' class="input3" type="radio" value="' . $lbdwKey . '"> ' . $lbdwVal . "\n";
                    }
                    ?>
                </p>
        </div>

        <p><label><strong>InsaneJournal:</strong> On or Off?</label>
            <select name="ij_input" class="cps" id="toggleIJ">
                <?php
                $ijArray = array('y' => 'On', 'n' => 'Off');
                foreach ($ijArray as $ijKey => $ijVal) {
                    echo '  <option';
                    if ($ijKey == $seahorses->getOption('updates_crosspost_ij')) {
                        echo ' selected="selected"';
                    }
                    echo ' value="' . $ijKey . '">' . $ijVal . "</option>\n";
                }
                ?>
            </select></p>
        <?php
        if ($seahorses->getOption('updates_crosspost_ij') == 'y') {
            echo " <div class=\toggleIJ\" style=\"display: block;\">\n";
        } elseif ($seahorses->getOption('updates_crosspost_ij') == 'n') {
            echo " <div class=\"toggleIJ\" style=\"display: none;\">\n";
        }
        ?>
        <p><label><strong>Username:</strong></label>
            <input name="ij_user_input" class="input1" type="text"
                   value="<?php echo $seahorses->getOption('updates_crosspost_ij_user'); ?>"></p>
        <p><label><strong>Password:</strong><br>
                Leave blank to keep current password</label>
            <input name="ij_pass_input" class="input1" type="password"></p>
        <p style="clear: both; margin: 0 0 2% 0;"></p>
        <p><label><strong>Add Link Back to the Updates Blog?</strong></label>
            <?php
            $lbijArray = array('y' => 'Yes', 'n' => 'No');
            foreach ($lbijArray as $lbijKey => $lbijVal) {
                echo '   <input name="updates_linkback_ij_opt"';
                if ($lbijKey == $seahorses->getOption('updates_crosspost_ij_link')) {
                    echo ' checked="checked"';
                }
                echo ' class="input3" type="radio" value="' . $lbijKey . '"> ' . $lbijVal . "\n";
            }
            ?>
        </p>
        </div>

        <p><label><strong>LiveJournal:</strong> On or Off?</label>
            <select name="lj_input" class="cps" id="toggleLJ">
                <?php
                $ljArray = array('y' => 'On', 'n' => 'Off');
                foreach ($ljArray as $ljKey => $ljVal) {
                    echo '   <option';
                    if ($ljKey == $seahorses->getOption('updates_crosspost_lj')) {
                        echo ' selected="selected"';
                    }
                    echo ' value="' . $ljKey . '">' . $ljVal . "</option>\n";
                }
                ?>
            </select></p>
        <?php
        if ($seahorses->getOption('updates_crosspost_lj') == 'y') {
            echo " <div class=\"toggleLJ\" style=\"display: block;\">\n";
        } elseif ($seahorses->getOption('updates_crosspost_lj') == 'n') {
            echo " <div class=\"toggleLJ\" style=\"display: none;\">\n";
        }
        ?>
        <p><label><strong>Username:</strong></label>
            <input name="lj_user_input" class="input1" type="text"
                   value="<?php echo $seahorses->getOption('updates_crosspost_lj_user'); ?>"></p>
        <p><label><strong>Password:</strong><br>
                Leave blank to keep current password</label>
            <input name="lj_pass_input" class="input1" type="password"></p>
        <p style="clear: both; margin: 0 0 2% 0;"></p>
        <p><label><strong>Add Link Back to the Updates Blog?</strong></label>
            <?php
            $lbljArray = array('y' => 'Yes', 'n' => 'No');
            foreach ($lbljArray as $lbljKey => $lbljVal) {
                echo '  <input name="updates_linkback_lj_opt"';
                if ($lbljKey == $seahorses->getOption('updates_crosspost_lj_link')) {
                    echo ' checked="checked"';
                }
                echo ' class="input3" type="radio" value="' . $lbljKey . '"> ' . $lbljVal . "\n";
            }
            ?>
        </p>
        </div>
        </fieldset>

        <fieldset>
            <legend>Gravatar Settings</legend>
            <p><label><strong>Gravatar:</strong> On or Off?</label>
                <select name="gravatar_opt" class="togglediv input1" id="toggleGravtar">
                    <?php
                    $grArray = array('y' => 'On', 'n' => 'Off');
                    foreach ($grArray as $grKey => $grVal) {
                        echo '  <option';
                        if ($grKey == $seahorses->getOption('updates_gravatar')) {
                            echo ' selected="selected"';
                        }
                        echo ' value="' . $grKey . '">' . $grVal . "</option>\n";
                    }
                    ?>
                </select></p>
            <?php
            if ($seahorses->getOption('updates_gravatar') == 'y') {
                echo " <div class=\"toggleGravatar\" style=\"display: block;\">\n";
            } elseif ($seahorses->getOption('updates_gravatar') == 'n') {
                echo " <div class=\"toggleGravatar\" style=\"display: none;\">\n";
            }
            ?>
            <p><label><strong>Gravatar Rating:</strong></label>
                <input name="gravatar_rating_opt" class="input1" type="text"
                       value="<?php echo $seahorses->getOption('updates_gravatar_rating'); ?>"></p>
            <p><label><strong>Gravatar Size:</strong></label>
                <input name="gravatar_size_opt" class="input1" type="text"
                       value="<?php echo $seahorses->getOption('updates_gravatar_size'); ?>"></p>
            </div>
        </fieldset>

        <fieldset>
            <legend>Submit</legend>
            <p class="tc"><input name="action" class="input2" type="submit" value="Edit Addons"></p>
        </fieldset>
        </form>
        <?php
    }
} elseif (
    isset($_POST['action']) &&
    $_POST['action'] == 'Edit Addons' &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    $secpost = $tigers->cleanMys($_POST['sec']);
    $sechash = $tigers->cleanMys($_POST['sech']);
    $get_addon_array['install'] = 'Install';
    if (empty($secpost) || !array_key_exists($secpost, $get_addon_array)) {
        $tigers->displayError('Form Error', 'You can only edit existing addons,' .
            ' love!', false);
    }
    $ynArray = array('y', 'n');

    if ($secpost == 'codes' && $sechash == sha1($get_addon_array['codes'])) {
        $codes_opt_input = $tigers->cleanMys($_POST['codes_opt_input']);
        if (in_array($codes_opt_input, $ynArray) && $seahorses->getOption('codes_opt') != $codes_opt_input) {
            $seahorses->editOption('codes_opt', $codes_opt_input);
        }
        $codes_path = $tigers->cleanMys($_POST['codes_path']);
        $codes_http = $tigers->cleanMys($_POST['codes_http']);
        if (!strrchr($codes_path, '/')) {
            $tigers->displayError('Form Error', 'The <samp>codes path</samp>' .
                ' need trailing slashes. Go back and add them.', false);
        } elseif (empty($codes_path) || empty($codes_http)) {
            $tigers->displayError('Form Error', 'Your admin and image paths are' .
                ' empty. Enter the proper path.', false);
        }
        if ($seahorses->getOption('codes_img_http') != $codes_http) {
            $seahorses->editOption('codes_img_http', $codes_http);
        }
        if ($seahorses->getOption('codes_img_path') != $codes_path) {
            $seahorses->editOption('codes_img_path', $codes_path);
        }
        $codes_formurl_input = $tigers->cleanMys($_POST['codes_formurl_input']);
        if ($seahorses->getOption('codes_formurl') != $codes_formurl_input) {
            $seahorses->editOption('codes_formurl', $codes_formurl_input);
        }
        $codes_filesize_input = $tigers->cleanMys($_POST['codes_filesize_input']);
        if (!is_numeric($codes_filesize_input)) {
            $tigers->displayError('Form Error', 'The <samp>Max File Size</samp> field is' .
                ' invalid.', false);
        }
        if ($seahorses->getOption('codes_filesize') != $codes_filesize_input) {
            $seahorses->editOption('codes_filesize', $codes_filesize_input);
        }
        $codes_order_input = $tigers->cleanMys($_POST['codes_order_input']);
        if (empty($codes_order_input) || !in_array($codes_order_input, array('ASC', 'DESC'))) {
            $coi = 'DESC';
        } else {
            $coi = $codes_order_input;
        }
        if ($seahorses->getOption('codes_order') != $coi) {
            $seahorses->editOption('codes_order', $coi);
        }
    } /**
     * If we're installing/uninstalling an addon:
     */
    elseif ($secpost == 'install' && $sechash == sha1('Install')) {
        $installaddon = $tigers->cleanMys($_POST['installaddon']);
        $uninstalladdon = $tigers->cleanMys($_POST['uninstalladdon']);
        if (
            $installaddon != 'none' &&
            !array_key_exists($installaddon, $get_addon_array_for_installation)
        ) {
            $tigers->displayError('Form Error', 'The addon you want to install is not' .
                ' valid, m\'dear!', false);
        }
        if (($uninstalladdon != 'none') && !array_key_exists($uninstalladdon, $get_addon_array)) {
            $tigers->displayError('Form Error', 'The addon you want to uninstall is not' .
                ' valid, m\'dear!', false);
        }

        /**
         * Install the addon!
         */
        if (($installaddon != 'none') && !$cheetahs->isInstalled($installaddon)) {
            $install = $frogs->installAddon($installaddon);
            if ($install->status == true) {
                echo $tigers->displaySuccess('The addon has been installed!');
                $seahorses->editOption($installaddon . '_opt', 'y');
            } else {
                $tigers->displayError(
                    'Database Error',
                    $install->message,
                    true,
                    $install->query
                );
            }
        }

        /**
         * Uninstall the addon!
         */
        if (($uninstalladdon != 'none') && $cheetahs->isInstalled($uninstalladdon) == true) {
            $uninstall = $frogs->uninstallAddon($uninstalladdon);
            if ($uninstall->status == true) {
                echo $tigers->displaySuccess('The addon has been uninstalled!');
                $seahorses->editOption($uninstalladdon . '_opt', 'n');
            } else {
                $tigers->displayError(
                    'Database Error',
                    $uninstall->message,
                    true,
                    $uninstall->query
                );
            }
        }
    } /**
     * Edit the KIM options for the KIM list :D
     */
    elseif ($secpost == 'kim' && $sechash == sha1($get_addon_array['kim'])) {
        $kim_opt_input = $tigers->cleanMys($_POST['kim_opt_input']);
        if (in_array($kim_opt_input, $ynArray) && $seahorses->getOption('kim_opt') != $kim_opt_input) {
            $seahorses->editOption('kim_opt', $kim_opt_input);
        }
        $kim_join_opt = $tigers->cleanMys($_POST['kim_join_opt']);
        $kim_list_opt = $tigers->cleanMys($_POST['kim_list_opt']);
        $kim_reset_opt = $tigers->cleanMys($_POST['kim_reset_opt']);
        $kim_update_opt = $tigers->cleanMys($_POST['kim_update_opt']);
        if (
            ($kim_opt_input == 'y') &&
            (empty($kim_join_opt) &&
                empty($kim_list_opt) &&
                empty($kim_reset_opt) &&
                empty($kim_update_opt)
            )
        ) {
            $tigers->displayError('Form Error', 'You must fill out the KIM URL' .
                " options if you're using your KIM list!", false);
        }
        if ($seahorses->getOption('kim_join') != $kim_join_opt) {
            $seahorses->editOption('kim_join', $kim_join_opt);
        }
        if ($seahorses->getOption('kim_list') != $kim_list_opt) {
            $seahorses->editOption('kim_list', $kim_list_opt);
        }
        if ($seahorses->getOption('kim_reset') != $kim_reset_opt) {
            $seahorses->editOption('kim_reset', $kim_reset_opt);
        }
        if ($seahorses->getOption('kim_update') != $kim_update_opt) {
            $seahorses->editOption('kim_update', $kim_update_opt);
        }
    } elseif ($secpost == 'lyrics' && $sechash == sha1($get_addon_array['lyrics'])) {
        $lyrics_opt_input = $tigers->cleanMys($_POST['lyrics_opt_input']);
        if (in_array($lyrics_opt_input, $ynArray) && $seahorses->getOption('lyrics_opt') != $lyrics_opt_input) {
            $seahorses->editOption('lyrics_opt', $lyrics_opt_input);
        }
    } elseif ($secpost == 'quotes' && $sechash == sha1($get_addon_array['quotes'])) {
        $quotes_opt_input = $tigers->cleanMys($_POST['quotes_opt_input']);
        if (in_array($quotes_opt_input, $ynArray) && $seahorses->getOption('quotes_opt') != $quotes_opt_input) {
            $seahorses->editOption('quotes_opt', $quotes_opt_input);
        }
    } elseif ($secpost == 'updates' && $sechash == sha1($get_addon_array['updates'])) {
        /**
         * Updates o' doom! I just... can't anymore. ;_; (I BLAME VERSION 2.1.9
         * FOR THIS BY THE WAY)
         */
        $ratingArray = array('G', 'PG', 'R', 'X');
        $updates_opt_input = $tigers->cleanMys($_POST['updates_opt_input']);
        $updates_prettyurls_opt = $tigers->cleanMys($_POST['updates_prettyurls_opt']);
        $updates_url_opt = $tigers->cleanMys($_POST['updates_url_opt']);
        $updates_comments_opt = $tigers->cleanMys($_POST['updates_comments_opt']);
        $updates_comments_header_opt = $tigers->cleanMys($_POST['updates_comments_header_opt']);
        $updates_comments_footer_opt = $tigers->cleanMys($_POST['updates_comments_footer_opt']);
        $updates_comments_moderation_opt = $tigers->cleanMys($_POST['updates_comments_moderation_opt']);
        $updates_comments_notification_opt = $tigers->cleanMys($_POST['updates_comments_notification_opt']);
        $akismet_opt_input2 = $tigers->cleanMys($_POST['akismet_opt_input2']);
        $akismet_key_input2 = $tigers->cleanMys($_POST['akismet_key_input2']);
        $antispam_opt_input2 = $tigers->cleanMys($_POST['antispam_opt_input2']);
        $captcha_opt_input2 = $tigers->cleanMys($_POST['captcha_opt_input2']);
        $gravatar_opt = $tigers->cleanMys($_POST['gravatar_opt']);
        $gravatar_rating_opt = $tigers->cleanMys($_POST['gravatar_rating_opt']);
        $gravatar_size_opt = $tigers->cleanMys($_POST['gravatar_size_opt']);
        $dreamwidth_opt = $tigers->cleanMys($_POST['dw_input']);
        $dreamwidth_opt_user = $tigers->cleanMys($_POST['dw_user_input']);
        $dreamwidth_opt_pass = $tigers->cleanMys($_POST['dw_pass_input']);
        $dreamwidth_opt_link = $tigers->cleanMys($_POST['updates_linkback_dw_opt']);
        $insanej_opt = $tigers->cleanMys($_POST['ij_input']);
        $insanej_opt_user = $tigers->cleanMys($_POST['ij_user_input']);
        $insanej_opt_pass = $tigers->cleanMys($_POST['ij_pass_input']);
        $insanej_opt_link = $tigers->cleanMys($_POST['updates_linkback_ij_opt']);
        $livejournal_opt = $tigers->cleanMys($_POST['lj_input']);
        $livejournal_opt_user = $tigers->cleanMys($_POST['lj_user_input']);
        $livejournal_opt_pass = $tigers->cleanMys($_POST['lj_pass_input']);
        $livejournal_opt_link = $tigers->cleanMys($_POST['updates_linkback_lj_opt']);
        if (
            !in_array($updates_opt_input, $ynArray) || !in_array($updates_prettyurls_opt, $ynArray) ||
            !in_array($updates_comments_opt, $ynArray) || !in_array($updates_comments_moderation_opt, $ynArray) ||
            !in_array($updates_comments_notification_opt, $ynArray) || !in_array($akismet_opt_input2, $ynArray) ||
            !in_array($antispam_opt_input2, $ynArray) || !in_array($captcha_opt_input2, $ynArray) ||
            !in_array($gravatar_opt, $ynArray) || !in_array($dreamwidth_opt, $ynArray) ||
            !in_array($dreamwidth_opt_link, $ynArray) || !in_array($insanej_opt, $ynArray) ||
            !in_array($insanej_opt_link, $ynArray) || !in_array($livejournal_opt, $ynArray) ||
            !in_array($livejournal_opt_link, $ynArray)
        ) {
            $tigers->displayError('Form Error', 'One or more of the update' .
                ' options is invalid.', false);
        }
        if ($updates_opt_input != 'n') {
            if (empty($updates_comments_header_opt) || empty($updates_comments_footer_opt)) {
                $tigers->displayError('Form Error', 'One or more of the comments' .
                    ' header or footer paths are incorrect!', false);
            } elseif (empty($updates_url_opt)) {
                $tigers->displayError('Form Error', 'If using the updates feature,' .
                    ' you need to fill in the updates URL option field.', false);
            }
        }
        if ($akismet_opt_input2 == 'y' && empty($akismet_key_input2)) {
            $tigers->displayError('Form Error', 'The <samp>Akismet</samp> plugin for' .
                ' <samp>Updates</samp> is invalid.', false);
        }
        /**
         * Get Default settings!
         */
        if (empty($gravatar_rating_opt)) {
            $gravatar_rating_opt = 'PG';
        } elseif (!empty($gravatar_rating_opt) && !in_array($gravatar_rating_opt, $ratingArray)) {
            $tigers->displayError('Form Error', 'The <samp>Gravatar: Rating</samp>' .
                ' setting is invalid.', false);
        }
        if (empty($gravatar_size_opt)) {
            $gravatar_size_opt = '60';
        } elseif (!empty($gravatar_size_opt) && ($gravatar_size_opt < 0 || $gravatar_size_opt > 80)) {
            $tigers->displayError('Form Error', 'The <samp>Gravatar: Size</samp>' .
                ' setting is invalid.', false);
        }
        if ($dreamwidth_opt == 'y' && empty($dreamwidth_opt_user)) {
            $tigers->displayError('Form Error', 'The <samp>Cross-Post:' .
                ' Dreamwidth</samp> option for <samp>Updates</samp> is invalid.', false);
        }
        if ($insanej_opt == 'y' && empty($insanej_opt_user)) {
            $tigers->displayError('Form Error', 'The <samp>Cross-Post:' .
                ' InsaneJournal</samp> option for <samp>Updates</samp> is invalid.', false);
        }
        if ($livejournal_opt == 'y' && empty($livejournal_opt_user)) {
            $tigers->displayError('Form Error', 'The <samp>Cross-Post:' .
                ' LiveJournal</samp> option for <samp>Updates</samp> is invalid.', false);
        }
        if ($seahorses->getOption('updates_opt') != $updates_opt_input) {
            $seahorses->editOption('updates_opt', $updates_opt_input);
        }
        if ($seahorses->getOption('updates_url') != $updates_url_opt) {
            $seahorses->editOption('updates_url', $updates_url_opt);
        }
        if ($seahorses->getOption('updates_comments_header') != $updates_comments_header_opt) {
            $seahorses->editOption('updates_comments_header', $updates_comments_header_opt);
        }
        if ($seahorses->getOption('updates_comments_footer') != $updates_comments_footer_opt) {
            $seahorses->editOption('updates_comments_footer', $updates_comments_footer_opt);
        }
        if ($seahorses->getOption('updates_prettyurls') != $updates_prettyurls_opt) {
            $seahorses->editOption('updates_prettyurls', $updates_prettyurls_opt);
        }
        if ($seahorses->getOption('updates_comments') != $updates_comments_opt) {
            $seahorses->editOption('updates_comments', $updates_comments_opt);
        }
        if ($seahorses->getOption('updates_comments_moderation') != $updates_comments_moderation_opt) {
            $seahorses->editOption('updates_comments_moderation', $updates_comments_moderation_opt);
        }
        if ($seahorses->getOption('updates_comments_notification') != $updates_comments_notification_opt) {
            $seahorses->editOption('updates_comments_notification', $updates_comments_notification_opt);
        }
        if ($seahorses->getOption('updates_opt') != $updates_opt_input) {
            $seahorses->editOption('updates_opt', $updates_opt_input);
        }
        if ($seahorses->getOption('updates_opt') != $updates_opt_input) {
            $seahorses->editOption('updates_opt', $updates_opt_input);
        }
        if ($seahorses->getOption('updates_akismet') != $akismet_opt_input2) {
            $seahorses->editOption('updates_akismet', $akismet_opt_input2);
        }
        if ($seahorses->getOption('updates_akismet_key') != $akismet_key_input2) {
            $seahorses->editOption('updates_akismet_key', $akismet_key_input2);
        }
        if ($seahorses->getOption('antispam_opt') != $antispam_opt_input2) {
            $seahorses->editOption('antispam_opt', $antispam_opt_input2);
        }
        if ($seahorses->getOption('updates_captcha') != $captcha_opt_input2) {
            $seahorses->editOption('updates_captcha', $captcha_opt_input2);
        }
        if ($seahorses->getOption('updates_gravatar') != $gravatar_opt) {
            $seahorses->editOption('updates_gravatar', $gravatar_opt);
        }
        if ($seahorses->getOption('updates_gravatar_rating') != $gravatar_rating_opt) {
            $seahorses->editOption('updates_gravatar_rating', $gravatar_rating_opt);
        }
        if ($seahorses->getOption('updates_gravatar_size') != $gravatar_size_opt) {
            $seahorses->editOption('updates_gravatar_size', $gravatar_size_opt);
        }
        if ($seahorses->getOption('updates_crosspost_dw') != $dreamwidth_opt) {
            $seahorses->editOption('updates_crosspost_dw', $dreamwidth_opt);
        }
        if ($seahorses->getOption('updates_crosspost_dw_user') != $dreamwidth_opt_user) {
            $seahorses->editOption('updates_crosspost_dw_user', $dreamwidth_opt_user);
        }
        if (
            !empty($dreamwidth_opt_pass) &&
            $seahorses->getOption('updates_crosspost_dw_pass') != md5($dreamwidth_opt_pass)
        ) {
            $seahorses->editOption('updates_crosspost_dw_pass', md5($dreamwidth_opt_pass));
        }
        if ($seahorses->getOption('updates_crosspost_dw_link') != $dreamwidth_opt_link) {
            $seahorses->editOption('updates_crosspost_dw_link', $dreamwidth_opt_link);
        }
        if ($seahorses->getOption('updates_crosspost_ij') != $insanej_opt) {
            $seahorses->editOption('updates_crosspost_ij', $insanej_opt);
        }
        if ($seahorses->getOption('updates_crosspost_ij_user') != $insanej_opt_user) {
            $seahorses->editOption('updates_crosspost_ij_user', $insanej_opt_user);
        }
        if (
            !empty($insanej_opt_pass) &&
            $seahorses->getOption('updates_crosspost_ij_pass') != md5($insanej_opt_pass)
        ) {
            $seahorses->editOption('updates_crosspost_ij_pass', md5($insanej_opt_pass));
        }
        if ($seahorses->getOption('updates_crosspost_ij_link') != $insanej_opt_link) {
            $seahorses->editOption('updates_crosspost_ij_link', $insanej_opt_link);
        }
        if ($seahorses->getOption('updates_crosspost_lj') != $livejournal_opt) {
            $seahorses->editOption('updates_crosspost_lj', $livejournal_opt);
        }
        if ($seahorses->getOption('updates_crosspost_lj_user') != $livejournal_opt_user) {
            $seahorses->editOption('updates_crosspost_lj_user', $livejournal_opt_user);
        }
        if (
            !empty($livejournal_opt_pass) &&
            $seahorses->getOption('updates_crosspost_lj_pass') != md5($livejournal_opt_pass)
        ) {
            $seahorses->editOption('updates_crosspost_lj_pass', md5($livejournal_opt_pass));
        }
        if ($seahorses->getOption('updates_crosspost_lj_link') != $livejournal_opt_link) {
            $seahorses->editOption('updates_crosspost_lj_link', $livejournal_opt_link);
        }
    }
} else {
    ?>
    <p>Hola, amigos, and welcome to the addons portion of the script!
        <samp>addons.php</samp> &#8211; or just <strong>Addons</strong> &#8211; is
        here to give you statistics on your installed addons, allows you to install
        and uninstall addons, and edit the options for each addon you have installed.
        Navigation is muy simple, so go ahead &#8211;
        <a href="addons.php?sec=install">install some addons</a>, edit options and
        view your fun stats! :D</p>

    <h3>Statistics</h3>
    <?php
    $number = 0;
    $addonsarray = $get_addon_array;
    foreach ($addonsarray as $k => $v) {
        $n = $number === count($addonsarray) - 1 ? ' class="last"' : '';
        echo "<div class=\"addonStatistics\">\n";
        if ($cheetahs->isInstalled($k) === false) {
            echo "<p$n>The <strong>$v</strong> addon is not installed, m'love. /dramatic tear</p>\n";
        } else {
            echo "<p$n>The <strong>$v</strong> addon currently has " . $cheetahs->stats($k, 1) . ".</p>\n";
        }
        echo '</div>';
        $number++;
    }
    ?>
    <?php
}
echo "</div>\n";

require('footer.php');
