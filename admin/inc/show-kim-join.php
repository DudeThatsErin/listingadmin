<div id="show-kim-join">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-kim-join.php>
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

    if ($seahorses->getOption('akismet_opt') == 'y') {
        require_once('func.microakismet.inc.php');
    }

    $options = (object)array();
    $options->markup = ' /';

    /**
     * Since our form is processing, we check the variables before we do
     * anything else :x
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Join List') {
        $name = $tigers->cleanMys($_POST['name']);
        if (empty($name) || !preg_match("/([A-Za-z\\-\s]+)/", $name)) {
            $tigers->displayError('Form Error', 'The <samp>name</samp> field contains' .
                ' invalid characters.', false);
        }
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'You have not filled out the' .
                ' <samp>email</samp> field.</p>', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>email</samp> field are not allowed.', false);
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> does' .
                ' not start with http:// and therefore is not valid. Try again.', false);
        }
        $listing = $tigers->cleanMys($_POST['listing']);
        if (empty($listing) || !in_array($listing, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'The <samp>listing</samp> field is invalid.', false);
        }
        $passwordn = $tigers->cleanMys($_POST['passwordn']);
        $passwordv = $tigers->cleanMys($_POST['passwordv']);
        if (!empty($passwordn) && empty($passwordv)) {
            $tigers->displayError('Script Error', 'In order to update your password, you need to fill' .
                ' out both new password fields or leave both empty.', false);
        }
        if (empty($passwordn) && empty($passwordv)) {
            $pass = substr(md5(date('YmdHis')), 0, 8) . substr(md5(random_int(99999, 999999)), 0, 8);
        } else {
            $pass = $passwordn;
        }
        $visible = $tigers->cleanMys($_POST['visible']);
        if (!is_numeric($visible) || $visible > 1) {
            $tigers->displayError('Form Error', 'Your <samp>visible</samp> field is' .
                ' not valid.', false);
        }
        if (!isset($_POST['comments']) || empty($_POST['comments'])) {
            $_POST['comments'] = '';
            $comments = $tigers->cleanMys($_POST['comments']);
        } else {
            $comments = $tigers->cleanMys($_POST['comments']);
        }
        $emailMe = $tigers->cleanMys($_POST['emailMe']);
        $ck = isset($_POST['captchak']) ? $tigers->cleanMys($_POST['captchak']) : '';
        $s1 = $tigers->cleanMys($_POST['antispamh']);
        $s2 = $tigers->cleanMys($_POST['antispamb']);
        $s3 = $tigers->cleanMys($_POST['mathproblem']);

        /**
         * Grab user information so we can log messages
         */
        $userinfo = (object)array(
            'url' => $tigers->cleanMys(
                'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
            ),
            'text' => "Name: $name\nE-Mail Address: $email\nURL: $url\nVisible: $visible" .
                "\nComments: $comments"
        );

        /**
         * Check for SPAM words and bots, bbCode, and JavaScript, captcha,
         * Akismet, and antispam \o/ First: spam words!
         */
        foreach ($laantispam->spamarray() as $b) {
            if (strpos($_POST['comments'], (string) $b) !== false) {
                $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
            }
        }

        foreach ($laantispam->bbcode as $h) {
            if (strpos($_POST['comments'], (string) $h) !== false) {
                $tigers->displayError('SPAM Error', 'bbCode language is not allowed.', false);
            }
        }

        if (preg_match($badheaders, $_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
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

        if ($seahorses->getOption('captcha_opt') == 'y') {
            if (!isset($_POST['captcha']) || strpos(sha1($ck), (string) $_POST['captcha']) !== 0) {
                $tigers->displayError('Form Error', 'The <samp>CAPTCHA</samp> is invalid!', false);
            }
        }

        if ($seahorses->getOption('antispam_opt') == 'y') {
            $vars = array(
                'comments' => $comments,
                'mathproblem' => $_POST['mathproblem'],
                's1' => $s1,
                's2' => $s2,
                'url' => $url
            );
            $antispam = $laantispam->antispam($vars);

            if ($antispam->status == false) {
                $octopus->writeError(
                    'SPAM Error: Anti-SPAM (Join)', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'It appears the script has' .
                    ' identified you as SPAM. If you believe you\'re not SPAM, feel free to' .
                    ' join us.', false);
            }
        }

        if ($seahorses->getOption('akismet_opt') == 'y') {
            $vars = array(
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'referer' => $_SERVER['HTTP_REFERER'],
                'comment_type' => 'kim_join',
                'comment_author' => $name,
                'comment_author_email' => $email,
                'comment_author_url' => $url,
                'comment_content' => $comments
            );

            $check = akismet_check($vars);
            if ($check == true) {
                $octopus->writeError(
                    'SPAM Error: Anti-SPAM (Join)', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'It appears Akismet thinks you\'re' .
                    ' SPAM.|While this isn\'t a <em>huge</em> issue it\'s keeping you from being' .
                    ' added to the pending list. If you believe you\'re not SPAM, feel free to' .
                    ' join ' . $hide_address . '.', false);
            }
        }

        /**
         * Now we will insert zee member (or try to, or something)
         */
        $insert = "INSERT INTO `$_ST[kim]` (`mEmail`, `fNiq`, `mName`, `mURL`, `mPassword`," .
            " `mVisible`, `mPending`, `mUpdate`, `mAdd`) VALUES ('$email', '$listing', '$name'," .
            " '$url', MD5('$pass'), '$visible', '1', 'n', CURDATE())";
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($insert);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script could not insert you' .
                ' as a KIM member.', false);
        } elseif ($true == true) {
            $subject = $qname . ' KIM: New Member';
            $akp = strpos($my_website, '/') !== false ? $my_website .
                'kim.php' : $my_website . '/kim.php';

            /**
             * We need to notify ourselves, so let's format this 8D
             */
            $message = "You have a received a new member at your KIM list:\n\nName:" .
                " {$name}\nE-Mail: {$email}\n";
            if (!empty($url)) {
                $message .= "URL: <{$url}>\n";
            }
            $message .= 'Listing: ' . $wolves->getSubject($listing) .
                "\n\nComments: " . $_POST['comments'] . "\n\nIP Address:" .
                " {$_SERVER['REMOTE_ADDR']}\n\nTo moderate (or delete) this member," .
                ' go here: <' . $myadminpath->http . '>';
            $headers = "From: Listing Admin <$my_email>\nReply-To: <$my_email>";
            $mmail = mail($my_email, $subject, $message, $headers);

            /**
             * Format the e-mail to send to them!
             */
            $them_subject = $qname . ' KIM: Information';
            $them_message = "Hello {$name},\n\nYou have received this email because you" .
                ' (or someone else) used this email address to sign up as a member of' .
                " {$qname}'s KIM list. If this is in error, please reply to this email and" .
                " tell me and I will remove you from the list as soon as possible.\n\n" .
                'Currently, you have been placed on the members pending list for approval,' .
                ' and are not yet part of the listing. If in two weeks, you have not yet been' .
                ' notified of your approval and you are not yet listed at the members list,' .
                " please feel free to email me and check up on your application.\n\nThe" .
                ' information you submitted to this list is shown below. Please keep this' .
                " information for future reference.\n\nName: {$name}\nE-Mail: {$email}\n";
            if (!empty($url)) {
                $them_message .= "URL: <{$url}>\n";
            }
            $them_message .= "Password: $pass\nListing: " . $wolves->getSubject($listing) .
                "\n\nThank you for joining the KIM list! :D\n\n--\n{$qowns}\n{$qname}" .
                " KIM List\n<{$qwebs}>";
            $them_headers = "From: $qowns <$my_email>\nReply-To: <$my_email>";

            if ($emailMe == 'y') {
                $tmail = mail($email, $them_subject, $them_message, $them_headers);
                if ($tmail) {
                    echo '<p><span class="success">Success!</span> Your application was' .
                        ' processed and you are now listed under the pending list for approval. Your' .
                        " information has been sent to you. :)</p>\n";
                } elseif (!$tmail) {
                    echo '<p><span class="success">Success!</span> Your application was' .
                        ' processed and you are now listed under the pending list for approval.' .
                        " However, we were unable to send your information to you.</p>\n";
                }
            } elseif ($emailMe != 'yes') {
                echo '<p><span class="success">Success!</span> Your application was processed' .
                    " and you are now listed under the pending list for approval.</p>\n";
            }
        }
    } /**
     * Let's build our form, shall we ;)
     */
    else {
        if ($seahorses->getOption('markup') == 'xhtml') {
            $mark = ' /';
        } else {
            $mark = '';
        }
        $symb = $seahorses->getOption('markup') == 'html5' ? '&#187;' : '&raquo;';

        $spamvars = (object)array(
            'antiuno' => random_int(1, 10),
            'antidos' => random_int(1, 10)
        );
        $spamoptions = (object)array(
            'capthash' => sha1(random_int(10000, 999999)),
            'antians' => $spamvars->antiuno + $spamvars->antidos,
            'antipro' => $spamvars->antiuno . ' + ' . $spamvars->antidos
        );
        ?>
        <p>Please use the form below for joining only. Hit the submit button only once,
            as your application is entered into the database, and ready for approval. If you
            have any problems, feel more than free to contact me <?php echo $hide_address; ?>.
            The asterisks (*) are required fields.</p>

        <form action="<?php echo $seahorses->getOption('kim_join'); ?>" method="post">
            <?php
            if ($seahorses->getOption('javascript_opt') == 'y'
                || $seahorses->getOption('captcha_opt') == 'y'
                || $seahorses->getOption('antispam_opt') == 'y') {
                echo "<p style=\"margin: 0;\">\n";
            }

            if ($seahorses->getOption('javascript_opt') == 'y') {
                echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
            }

            if ($seahorses->getOption('captcha_opt') == 'y') {
                echo $octopus->captchaCheat($spamoptions->capthash);
            }

            if ($seahorses->getOption('antispam_opt') == 'y') {
                echo $octopus->antispamCheat($spamoptions->antians, $spamoptions->antipro);
            }

            if ($seahorses->getOption('javascript_opt') == 'y'
                || $seahorses->getOption('captcha_opt') == 'y'
                || $seahorses->getOption('antispam_opt') == 'y') {
                echo "</p>\n";
            }
            ?>

            <fieldset>
                <legend>Details</legend>
                <p><label>* <strong>Name:</strong></label>
                    <input name="name" class="input1" type="text" required="required"<?php echo $mark; ?>></p>
                <p><label>* <strong>E-mail:</strong></label>
                    <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                <p><label><strong>URL:</strong></label>
                    <input name="url" class="input1" type="url"<?php echo $mark; ?>></p>
            </fieldset>

            <fieldset>
                <legend>Password</legend>
                <p class="inputPassword">
                    <label style="float: left; padding: 0 1%; width: 48%;"><strong>Password:</strong><br<?php echo $mark; ?>>
                        Type in your password (if desired) twice for verification (a random one is generated otherwise):</label>
                    <input name="passwordn" class="input1" style="width: 48%;"
                           type="password"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                    <input name="passwordv" class="input1" style="width: 48%;" type="password"<?php echo $mark; ?>></p>
                <p style="clear: both; margin: 0 0 1% 0;"></p>
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
                <legend>Options</legend>
                <p><label><strong>Show E-mail:</strong></label>
                    <input name="visible" checked="checked" class="input3" type="radio" value="0"<?php echo $mark; ?>>
                    Yes
                    <input name="visible" class="input3" type="radio" value="1"<?php echo $mark; ?>> No</p>
                <p><strong>Comments:</strong><br<?php echo $mark; ?>>
                    <textarea name="comments" class="input1" cols="50" rows="10"
                              style="height: 150px; margin: 0 1% 0 0; width: 99%;"></textarea>
                </p>
            </fieldset>

            <?php
            if (
                $seahorses->getOption('captcha_opt') == 'y' ||
                $seahorses->getOption('antispam_opt') == 'y'
            ) {
                ?>
                <fieldset>
                    <legend>Anti-SPAM</legend>
                    <?php
                    if ($seahorses->getOption('captcha_opt') == 'y') {
                        ?>
                        <p class="inputCaptcha">
                            <label style="float: left; padding: 0 1%; width: 48%;">
                                <strong>CAPTCHA</strong><br<?php echo $mark; ?>>
                                Enter the letters/numbers as shown to the right:
                            </label>
                            <input name="captcha" class="input1" style="width: 48%;"
                                   type="text"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                            <img alt="CAPTCHA Image" id="captcha" style="height: 80px; width: 170px;"
                                 title="CAPTCHA Image"
                                 src="<?php echo $my_website; ?>fun-captcha.inc.php?v=<?php echo $spamoptions->capthash; ?>"<?php echo $mark; ?>>
                        </p>
                        <?php
                    }
                    if ($seahorses->getOption('antispam_opt') == 'y') {
                        ?>
                        <p class="inputAntispam">
                            <label style="float: left; padding: 0 1%; width: 48%;">
                                <strong><?php echo $spamoptions->antipro; ?></strong><br<?php echo $mark; ?>>
                                Enter the answer to the math problem:
                            </label>
                            <input name="mathproblem" class="input1" style="width: 48%;"
                                   type="text"<?php echo $mark; ?>>
                        </p>
                        <?php
                    }
                    ?>
                </fieldset>
                <?php
            }
            ?>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc">
                    <input name="emailMe" checked="checked" class="input3" type="checkbox"
                           value="y"<?php echo $mark; ?>> Send me my details!
                </p>
                <?php
                /**
                 * Darlings, if the form should be changed, please make sure line 36 reflects
                 * this, or the form ceases to work; don't hesitate to ask me how you should
                 * change this text ;)
                 */
                ?>
                <p class="tc">
                    <input name="action" class="input2" type="submit" value="Join List"<?php echo $mark; ?>>
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
