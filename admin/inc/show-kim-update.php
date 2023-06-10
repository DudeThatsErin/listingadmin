<div id="show-kim-update">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-kim-update.php>
     * @version          Robotess Fork
     */

    use Robotess\StringUtils;

    require('b.inc.php');
    require_once('Robotess/StringUtils.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('class-antispam.inc.php');
    require_once('class-kimadmin.inc.php');
    require_once('fun.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');

    /**
     * Aaaaa-and we're posting, so let's check this shit!
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Update Information') {
        $name = $tigers->cleanMys($_POST['name']);
        if (empty($name)) {
            $tigers->displayError('Form Error', 'You have not filled out the' .
                ' <samp>name</samp> field.', false);
        } elseif (strlen($name) > 20) {
            $tigers->displayError('Form Error', 'Your <samp>name</samp> is too' .
                ' long. Go back and shorten it.', false);
        }
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        $new_email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['new_email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'You have not filled out the' .
                ' <samp>email</samp> field.</p>', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>email</samp> field are not allowed.', false);
        }
        if (!empty($new_email) && !StringUtils::instance()->isEmailValid($new_email)) {
            $tigers->displayError('Form Error', 'The characters specified in' .
                ' the <samp>email</samp> field are not allowed.', false);
        }
        $new_url = StringUtils::instance()->normalizeUrl($_POST['new_url']);
        if (!empty($new_url) && !StringUtils::instance()->isUrlValid($new_url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp>' .
                ' appears to be invalid; make sure you haven\'t included any invalid characters' .
                ' and you prepended your URL with <samp>http</samp>.', false);
        }

        $listing = $tigers->cleanMys($_POST['listing']);
        if (empty($listing) || !in_array($listing, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'The <samp>listing</samp> field is invalid.', false);
        }
        $passwordo = $tigers->cleanMys($_POST['passwordo']);
        $passwordn = $tigers->cleanMys($_POST['passwordn']);
        $passwordv = $tigers->cleanMys($_POST['passwordv']);
        $check = $kimadmin->checkPassword($email, $passwordo, $listing);
        if ($check->email == false || $check->password == false) {
            $tigers->displayError('Form Error', 'Oops! It appears the' .
                ' <samp>password</samp> you entered is incorrect. Go back and supply a' .
                ' correct one. If you have forgotten your old password, you can reset one' .
                ' <a href="' . $seahorses->getOption('kim_reset') . '">&#187; at the lost' .
                ' password page</a>!', false);
        } elseif ($passwordn !== $passwordv) {
            $tigers->displayError('Form Error', 'Your <samp>new password</samp>' .
                ' fields do not match. Go back and try again.', false);
        }
        if (!empty($passwordn) && empty($passwordv)) {
            $tigers->displayError('Form Error', 'In order to update your' .
                ' password, you need to fill out both new password fields.', false);
        }
        $visible = $tigers->cleanMys($_POST['visible']);
        if (!is_numeric($visible) || $visible > 3) {
            $tigers->displayError('Form Error', 'Your <samp>visible</samp> field is' .
                ' not valid.', false);
        }

        /**
         * Get the member's email, depending on $new_email~
         */
        $e = empty($new_email) ? $email : $new_email;

        /**
         * Get the admin panel KIM page~ :D
         */
        $akp = strpos($my_website, '/') !== false ? $my_website . 'kim.php' : $my_website .
            '/kim.php';

        /**
         * Now we will mail zee member!
         */
        $update = "UPDATE `$_ST[kim]` SET `mEmail` = '$e',";
        if (!empty($new_url)) {
            $update .= " `mURL` = '$new_url',";
        }
        if (!empty($passwordn) && !empty($passwordv)) {
            $update .= " `mPassword` = MD5('$passwordn'),";
        }
        if ($visible != 3) {
            $update .= " `mVisible` = '$visible',";
        }
        $update .= " `mPending` = '1', `mUpdate` = 'y' WHERE LOWER(`mEmail`) = '$email' AND" .
            " `fNiq` = '$listing' LIMIT 1";
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($update);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to update your' .
                ' information.', false);
        } elseif ($true == true) {
            $subject = $qname . ' KIM: Update Member';

            $message = 'You have a received a update form from a member for your KIM' .
                " list:\n\nName: {$name}\nOld E-mail: {$email}\n";
            if (!empty($new_email)) {
                $message .= "New E-mail: {$new_email}\n";
            }
            if (!empty($new_url)) {
                $message .= "New URL: <{$new_url}>\n";
            }
            if ($visible != 3) {
                if ($visible == 1) {
                    $message .= "Show E-Mail: Yes (0)\n\n";
                } else {
                    $message .= "Show E-Mail: No (1)\n\n";
                }
            } else {
                $message .= "\n";
            }
            $message .= "IP Address: {$_SERVER['REMOTE_ADDR']}\nBrowser:" .
                " {$_SERVER['HTTP_USER_AGENT']}\n\nTo moderate (or delete) this member" .
                ' update, go here: <' . $myadminpath->http . '>';
            $headers = "From: Listing Admin <$my_email>\nReply-To: <{$e}>";
            $mmail = mail($my_email, $subject, $message, $headers);

            echo '<p><span class="success">Success!</span> Your update form was processed' .
                ' and you are now listed under the pending list for approval. When you have' .
                " been approved of your update, an approval email will be sent to you. :D</p>\n";
        }
    } /**
     * Now let's get our form, so we can make the above possible~
     */
    else {
        $symb = $seahorses->getOption('markup') == 'html5' ? '&#187;' : '&raquo;';
        if ($seahorses->getOption('markup') == 'xhtml') {
            $mark = ' /';
        } else {
            $mark = '';
        }
        $options = new stdClass();
        $options->markup = $mark;
        ?>
        <p>Please use the form below for updating your information only. If you would like
            to join the list, you can <a href="<?php echo $seahorses->getOption('kim_join'); ?>">do so here</a>.
            Hit the submit button only once, as your update form is entered into the
            database, and ready for approval. If you have any problems, feel more than free
            to contact me <?php echo $hide_address; ?>. The asterisks (*) are required fields.</p>

        <form action="<?php echo $seahorses->getOption('kim_update'); ?>" method="post">
            <?php
            if ($seahorses->getOption('javascript_opt') == 'y') {
                echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
            }
            ?>

            <fieldset>
                <legend>Details</legend>
                <p><label>* <strong>Name:</strong></label>
                    <input name="name" class="input1" type="text" required="required"<?php echo $mark; ?>></p>
                <p><label><strong>New URL:</strong></label>
                    <input name="new_url" class="input1" type="url"<?php echo $mark; ?>></p>
            </fieldset>

            <fieldset>
                <legend>Password</legend>
                <p><label>* <strong>Old Password:</strong></label>
                    <input name="passwordo" class="input1" type="password" required="required"<?php echo $mark; ?>></p>
                <p><label style="float: left; padding: 0 1%; width: 48%;"><strong>New
                            Password</strong><br<?php echo $mark; ?>>
                        Type in your new password (if desired) twice for verification:</label>
                    <input name="passwordn" class="input1" style="width: 48%;"
                           type="password"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                    <input name="passwordv" class="input1" style="width: 48%;" type="password"<?php echo $mark; ?>></p>
            </fieldset>

            <fieldset>
                <legend>E-mail Settings</legend>
                <p><label>* <strong>Old E-mail:</strong></label>
                    <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                <p><label><strong>New E-mail:</strong></label>
                    <input name="new_email" class="input1" type="email"<?php echo $mark; ?>></p>
                <p><label><strong>Show my e-mail on the members list?</strong></label>
                    <input name="visible" checked="checked" class="input3" type="radio" value="3"<?php echo $mark; ?>>
                    Leave
                    <input name="visible" class="input3" type="radio" value="0"<?php echo $mark; ?>> Yes
                    <input name="visible" class="input3" type="radio" value="1"<?php echo $mark; ?>> No</p>
            </fieldset>

            <fieldset>
                <legend>Listing</legend>
                <p><label>* <strong>Listing:</strong></label> <select name="listing" class="input1" required="required">
                        <?php
                        $select = "SELECT * FROM `$_ST[main]` WHERE `status` = '0' ORDER BY `subject` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>No Listings Available</option>\n";
                        } else {
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <?php
                /**
                 * M'dears, if you're going to edit this, make sure line 30 echos this as
                 * well; the world will never forgive you otherwise ;)
                 */
                ?>
                <p class="tc">
                    <input name="action" class="input2" type="submit" value="Update Information"<?php echo $mark; ?>>
                </p>
            </fieldset>
        </form>

        <p class="showCredits-LA-RF" style="text-align: center;">
            Powered by <?php echo $octopus->formatCredit(); ?>
        </p>
<?php
    }
    ?>
</div>
