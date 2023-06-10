<div id="show-update">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-update.php>
     * @version          Robotess Fork
     */

    use Robotess\StringUtils;

    require('b.inc.php');
    require_once('Robotess/StringUtils.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('fun.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');

    /**
     * Get variables and options!
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

    if ($getItem->markup == 'xhtml') {
        $options->markup = ' /';
    } else {
        $options->markup = '';
    }

    /**
     * For has been set, let's start the variable and check processing~
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Update Information') {
        $id = $getItem->id;
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        $new_email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['new_email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'You have not filled out the <samp>email' .
                '</samp> field.</p>', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the <samp>' .
                'email</samp> field are not allowed.', false);
        }
        if (!empty($_POST['new_email']) && !StringUtils::instance()->isEmailValid($new_email)) {
            $tigers->displayError('Form Error', 'The characters specified in the <samp>' .
                'new email</samp> field are not allowed.', false);
        }

        if ($snakes->checkIfEmailExists($new_email, $options->listingID)) {
            $tigers->displayError('Script Error', 'It appears your new email is already taken by someone else.', false);
        }

        $new_name = $tigers->cleanMys($_POST['new_name']);
        if (!empty($new_name)) {
            if (!preg_match("/([A-Za-z\\-\s]+)/i", $new_name)) {
                $tigers->displayError('Form Error', 'There are invalid characters in' .
                    ' the <samp>name</samp> field. Please supply a valid new name or empty the field.', false);
            } elseif (strlen($new_name) > 25) {
                $tigers->displayError('Form Error', 'Your <samp>name</samp> is too' .
                    ' long. Go back and shorten it or empty the field.', false);
            }
        }
        $new_name = ucwords($new_name);

        $new_url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['new_url']));
        if (!empty($new_url) && !StringUtils::instance()->isUrlValid($new_url)) {
            $tigers->displayError('Form Error', 'The <samp>site URL</samp> is not valid.' .
                ' Please supply a valid site URL or empty the field.', false);
        }

        $new_country = $tigers->cleanMys($_POST['new_country']);
        $visible = (int)$tigers->cleanMys($_POST['visible']);
        if ($visible < 0 || $visible > 2) {
            $tigers->displayError('Form Error', 'The <samp>show email</samp> field needs' .
                ' to be a number in range (0-2).', false);
        }
        $password = $tigers->cleanMys($_POST['password'], 'nom');
        if (empty($password)) {
            $tigers->displayError('Form Error', 'In order to update your details, you must' .
                ' fill out your password!', false);
        }
        $password1 = $tigers->cleanMys($_POST['passwordn']);
        $password2 = $tigers->cleanMys($_POST['passwordnv']);
        if (!empty($password1)) {
            if (empty($password2)) {
                $tigers->displayError('Form Error', 'In order to verify your password, you' .
                    ' need to fill out both new password fields or leave both empty.', false);
            } elseif ($password1 !== $password2) {
                $tigers->displayError('Form Error', 'Your passwords do not match.', false);
            }
        }

        /**
         * Grab user information so we can log messages
         */
        $userinfo = (object)array(
            'url' => $tigers->cleanMys(
                'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
            ),
            'text' => "E-Mail Address: $email\nPassword: [not shown]\nNew" .
                " E-Mail Address: $new_email\nNew Name: $new_name\nNew URL: $new_url\nNew Password: [not shown]\n" .
                "New Country: $new_country\nVisible: $visible"
        );

        /**
         * Check for SPAM words and bots, bbCode, and JavaScript, captcha,
         * Akismet, and antispam \o/ First: spam words!
         */
        if (
            empty($_SERVER['HTTP_USER_AGENT']) ||
            preg_match($badheaders, $_SERVER['HTTP_USER_AGENT'])
        ) {
            $tigers->displayError('SPAM Error', 'SPAM bots are not allowed.', false);
        }

        if ($seahorses->getOption('javascript_opt') == 'y') {
            if (
                !isset($_POST[$octopus->cheatJavascript]) ||
                $_POST[$octopus->cheatJavascript] != sha1($seahorses->getOption('javascript_key'))
            ) {
                $tigers->displayError('Form Error', 'It appears you have JavaScript' .
                    ' turned off. As it is required to have JavaScript enabled, I suggest you' .
                    ' go back and enable it.', false);
            }
        }

        if (!$snakes->validateMember($email, $password, $id)) {
            $octopus->writeError(
                'Update Error', $userinfo->url, $userinfo->text, $automated
            );
            $tigers->displayError('Script Error', 'It appears the script has not' .
                ' identified you as a member. This could be because you have not used the' .
                ' correct e-mail address or you have entered a incorrect password.' .
                ' Try finding your details on the members list and entering them in the form' .
                ' <em>exactly</em> as they appear, and/or requesting a new password.', false);
        }

        /**
         * Checks are done, now let's update the member!
         */
        $update = "UPDATE `$_ST[members]` SET `mPending` = '1',";
        if (!empty($password1)) {
            $update .= " `mPassword` = MD5('$password1'),";
        }
        if (isset($_POST['new_email']) && !empty($new_email)) {
            $update .= " `mEmail` = '$new_email',";
        }
        if (isset($_POST['new_url']) && !empty($new_url)) {
            $update .= " `mURL` = '$new_url',";
        }
        if (isset($_POST['new_name']) && !empty($new_name)) {
            $update .= " `mName` = '$new_name',";
        }
        if (isset($_POST['new_country']) && !empty($new_country) && $_POST['new_country'] !== 'Choose') {
            $update .= " `mCountry` = '$new_country',";
        }
        if (isset($visible) && ($visible === 0 || $visible === 1)) {
            $update .= " `mVisible` = '$visible',";
        }
        $update .= " `mUpdate` = 'y', `mAdd` = CURDATE() WHERE LOWER(`mEmail`) = '$email'" .
            " AND `mPassword` = MD5('$password') AND `fNiq` = '$id' LIMIT 1";
        $true = $scorpions->query($update);

        /**
         * Set the e-mail messages! :D
         */
        $subject = $getItem->subject . ': Update Member';

        $message = 'You have a received an update form from a member for' .
            ' the ' . $getItem->subject . " listing:\n\nOld E-Mail: " .
            "$email\nNew E-Mail: $new_email\nNew Name: $new_name\nNew Country: $new_country\nNew URL: <$new_url>\n";
        if ($visible !== 2) {
            if ($visible === 1) {
                $message .= "Show E-Mail: No (1)\n\n";
            } else {
                $message .= "Show E-Mail: Yes (0)\n";
            }
        } else {
            $message .= "\n";
        }
        $message .= "IP Address: {$_SERVER['REMOTE_ADDR']}\nBrowser:" .
            " {$_SERVER['HTTP_USER_AGENT']}\n\nTo moderate (or delete) this member" .
            ' update, go here: <' . $myadminpath->http . '>';

        $headers = "From: Listing Admin <{$my_email}>\nReply-To: <{$my_email}>";

        $mmail = mail($my_email, $subject, $message, $headers);
        echo '<p><span class="success">Success!</span> Your update form was processed' .
            ' and you are now listed under the pending list for approval. When you have' .
            " been approved of your update, an approval email will be sent to you. :D</p>\n";
    } else {
        $mark = $getItem->markup == 'xtml' ? ' /' : '';
        $d2 = $octopus->formURL('delete', $getItem);
        $j2 = $octopus->formURL('join', $getItem);
        $r2 = $octopus->formURL('reset', $getItem);
        $u2 = $octopus->formURL('update', $getItem);
        ?>
        <p>Please use the form below for updating your information only. If you would
            like to join the list, you can <a href="<?php echo $j2; ?>">do so here</a>; if
            you'd like to reset your password, you can
            <a href="<?php echo $r2; ?>">do so here</a>; if you would like to delete
            yourself from the listing, you can <a href="<?php echo $d2; ?>">do so here</a>.
            Hit the submit button only once, as your update form is entered into the
            database, and ready for approval. If you have any problems, feel more than free
            to contact me <?php echo $hide_address; ?>. The asterisks (*) are required
            fields.</p>

        <form action="<?php echo $u2; ?>" method="post">
            <?php
            if ($seahorses->getOption('javascript_opt') == 'y') {
                echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
            }
            ?>
            <fieldset>
                <legend>Required Details</legend>
                <p><label>* <strong>E-mail:</strong></label>
                    <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                <p><label>* <strong>Password:</strong></label>
                    <input name="password" class="input1" type="password" required="required"<?php echo $mark; ?>></p>
            </fieldset>

            <fieldset>
                <legend>New Details</legend>
                <p><label><strong>New Name:</strong></label>
                    <input name="new_name" class="input1" type="text"<?php echo $mark; ?>></p>
                <p><label><strong>New E-mail:</strong></label>
                    <input name="new_email" class="input1" type="email"<?php echo $mark; ?>></p>
                <p><label><strong>New URL:</strong></label>
                    <input name="new_url" class="input1" type="url"<?php echo $mark; ?>></p>
                <p><label style="float: left; padding: 0 1%; width: 48%;">
                        <strong>New Password</strong><br<?php echo $mark; ?>>
                        Type twice for verification:
                    </label>
                    <input name="passwordn" class="input1" style="width: 48%;"
                           type="password"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                    <input name="passwordnv" class="input1" style="width: 48%;" type="password"<?php echo $mark; ?>></p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>New Country:</strong></label>
                    <select name="new_country" class="input1">
                        <option value="Choose">&#8212; Choose:</option>
                        <?php require('countries.inc.php'); ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Optional</legend>
                <p><label><strong>Show E-mail:</strong></label>
                    <input name="visible" checked="checked" class="input3" type="radio" value="2"<?php echo $mark; ?>>
                    Leave
                    <input name="visible" class="input3" type="radio" value="0"<?php echo $mark; ?>> Yes
                    <input name="visible" class="input3" type="radio" value="1"<?php echo $mark; ?>> No
                </p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
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
