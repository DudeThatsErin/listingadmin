<div id="show-join">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-join.php>
     * @version          Robotess Fork
     */

    use Robotess\StringUtils;

    require('b.inc.php');
    require_once('Robotess/StringUtils.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('class-antispam.inc.php');
    require_once('fun.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');
    if ($seahorses->getOption('akismet_opt') === 'y') {
        require_once('func.microakismet.inc.php');
    }

    /**
     * Get our SPAM arrays and listing object, o'course!
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

    if (
        (isset($show_comments) && $show_comments == 'n') ||
        ($getItem->form_join_comments == 1 || $getItem->form_join_comments == '1')
    ) {
        $options->formComments = false;
    } else {
        $options->formComments = true;
    }

    if ($getItem->markup == 'xhtml') {
        $options->markup = ' /';
    } else {
        $options->markup = '';
    }

    /**
     * Process the form once it's submitted :D
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Join Listing') {
        $id = $getItem->id;
        $name = $tigers->cleanMys($_POST['name']);
        if (empty($name)) {
            $tigers->displayError('Form Error', 'You have not filled out the <samp>' .
                'name</samp> field.', false);
        } elseif (!preg_match("/([A-Za-z\\-\s]+)/i", $name)) {
            $tigers->displayError('Form Error', 'There are invalid characters in' .
                ' the <samp>name</samp> field. Go back and try again.', false);
        } elseif (strlen($name) > 25) {
            $tigers->displayError('Form Error', 'Your <samp>name</samp> is too' .
                ' long. Go back and shorten it.', false);
        }
        $name = ucwords($name);
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'You have not filled out the <samp>' .
                'email</samp> field.</p>', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>email</samp> field are not allowed.', false);
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
                ' not valid. Please supply a valid site URL or empty the field.', false);
        }
        $country = $tigers->cleanMys($_POST['country']);
        if (empty($country) || $country == 'Choose' || $country == '� Choose' || $country == '#8212; Choose') {
            $tigers->displayError('Form Error', 'You have not filled out the <samp>country</samp> field!', false);
        }
        $password1 = $tigers->cleanMys($_POST['password']);
        $password2 = $tigers->cleanMys($_POST['passwordv']);
        if (!empty($password1)) {
            if (empty($password2)) {
                $tigers->displayError('Form Error', 'In order to verify your password,' .
                    ' you need to fill out both new password fields or leave both empty.', false);
            } elseif ($password1 !== $password2) {
                $tigers->displayError('Form Error', 'Your passwords do not match.', false);
            }
        }
        if (empty($password1) && empty($password2)) {
            $hashy1 = substr(sha1(random_int(9999, 999999)), 0, 8);
            $hashy2 = substr(sha1(date('YmdHis')), 0, 8);
            $pass = $hashy1 . $hashy2;
        } else {
            $pass = $password1;
        }
        $fav = '';
        if (isset($_POST['fave']) && !empty($getItem->fave_fields) || !empty($fave_field)) {
            $faves = $_POST['fave'];
            if ((is_countable($faves) ? count($faves) : 0) > 0 && !empty($_POST['fave'])) {
                $faves = array_map(array($tigers, 'replaceArray'), $faves);
                $faves = str_replace('+', '', $faves);
                $faves = str_replace('+ ', '', $faves);
                $faves = str_replace('[+] ', '', $faves);
                if (!empty($faves)) {
                    $fav = implode('|', $faves);
                    $fav = '|' . trim($fav, '|') . '|';
                }
            }
        }
        $visible = (int)$tigers->cleanMys($_POST['visible']);
        if ($visible < 0 || $visible > 1) {
            $visible = 1;
        }
        $comments = $tigers->cleanMys($_POST['comments']);
        if (preg_match('/(<.*>)/', $_POST['comments'])) {
            $tigers->displayError('Form Error', 'HTML is not allowed in the' .
                ' form!', false);
        }
        $ck = isset($_POST[$octopus->cheatCaptcha]) ? $tigers->cleanMys($_POST[$octopus->cheatCaptcha]) : '';
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
            'text' => "Name: $name\nE-mail Address: $email\nURL: $url\nVisible: $visible" .
                "\nFave Fields: $fav\nComments: $comments"
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

        if (
            preg_match($badheaders, $_SERVER['HTTP_USER_AGENT']) ||
            empty($_SERVER['HTTP_USER_AGENT'])
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

        if ($seahorses->getOption('captcha_opt') == 'y') {
            if (!isset($_POST['captcha']) || strpos(sha1($ck), (string) $_POST['captcha']) !== 0) {
                $tigers->displayError('Form Error', 'The <samp>CAPTCHA</samp> is invalid!', false);
            }
        }

        if ($seahorses->getOption('antispam_opt') == 'y') {
            $vars = array(
                'comments' => $comments,
                'mathproblem' => $_POST['mathproblem'],
                'nots' => $laantispam->spamarray(),
                's1' => $s1,
                's2' => $s2,
                'url' => $url
            );
            $antispam = $laantispam->antispam($vars);

            if ($antispam->status == false) {
                $seahorses->writeMessage(
                    0, 'SPAM Error: Anti-SPAM (Join)', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'It appears the script has' .
                    ' identified you as SPAM. If you believe you\'re not SPAM, feel free to' .
                    ' join ' . $hide_address . '.', false);
            }
        }

        if ($seahorses->getOption('akismet_opt') == 'y') {
            $vars = array(
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'referer' => $_SERVER['HTTP_REFERER'],
                'comment_type' => 'join',
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

        if ($snakes->checkIfEmailExists($email, $id)) {
            $octopus->writeError(
                'Join Error', $userinfo->url, $userinfo->text, $automated
            );
            $tigers->displayError('Script Error',
                'It appears that email you entered already exists in the system. Please use Update Form if you wish to update your information.',
                false);
        }

        /**
         * Now that we've checked everything, time to add the member! :D
         */
        $insert = "INSERT INTO `$_ST[members]` (`mEmail`, `fNiq`, `mName`, `mURL`," .
            ' `mCountry`, `mPassword`, `mExtra`, `mVisible`, `mPending`, `mUpdate`,' .
            " `mEdit`, `mAdd`) VALUES ('$email', '" . $options->listingID .
            "', '$name', '$url', '$country', MD5('$pass'), '$fav', '$visible', 1, 'n'," .
            " '1970-01-01 00:00:00', CURDATE())";
        $true = $scorpions->insert($insert);
        if ($true == false) {
            echo '<p>' . $scorpions->error() . "</p>\n";
            $tigers->displayError('Database Error', 'The script could not insert' .
                ' you into the database.', true, $select);
        } elseif ($true == true) {
            $subject = $tigers->replaceSpec($getItem->subject) . ': New Member';

            $message = 'You have a received a new member for' .
                ' the ' . $tigers->replaceSpec($getItem->subject) . " fanlisting:\n\nName:" .
                " $name\nE-mail: $email\n";
            if (!empty($url)) {
                $message .= "URL: <$url>\n";
            }
            if ($options->formComments == 'y') {
                $message .= 'Comments: ' . html_entity_decode($comments, ENT_QUOTES, 'ISO-8859-15') .
                    " \n\n";
            } else {
                $message .= "\n";
            }
            $message .= "IP Address: {$_SERVER['REMOTE_ADDR']}\nBrowser:" .
                " {$_SERVER['HTTP_USER_AGENT']}\n\nTo moderate (or delete) this member, go" .
                ' here: <' . $myadminpath->http . '>';
            $headers = "From: Listing Admin <$my_email>\nReply-To: <{$email}>";

            if ($seahorses->getOption('notify_approval') == 'y') {
                $mmail = mail($my_email, $subject, $message, $headers);
            }

            $them_subject = $tigers->replaceSpec($getItem->subject) . ' Listing Information';

            $them_message = "Hello {$name},\n\n";
            $them_message .= 'You have received this email because you (or someone else)' .
                ' used this email address to sign up as a member of the ' . $getItem->subject .
                ' listing. If this is in error, please reply to this email and tell me and I' .
                " will remove you from the listing as soon as possible.\n\nCurrently, you have" .
                ' been placed on the members pending list for approval, and are not yet part' .
                ' of the listing. If in two weeks, you have not yet been notified of your' .
                ' approval and you are not yet listed at the members list, please feel free' .
                " to email me and check up on your application.\n\nThe information you" .
                ' submitted to this listing is shown below; please keep this information for' .
                " future reference:\n\n";

            $them_message .= "Name: $name\nE-mail: $email\n";
            if (!empty($url)) {
                $them_message .= "URL: <{$url}>\n";
            }
            $them_message .= "Country: $country\nPassword: $pass\n\nThank you for joining" .
                " the listing! :D\n\n--\n{$qowns}\n" . $tigers->replaceSpec($getItem->title) .
                ' <' . $getItem->url . '>';

            $them_headers = "From: {$qowns} <$my_email>\nReply-To: <$my_email>";

            if (isset($_POST['emailMe']) && $_POST['emailMe'] == 'y') {
                $tmail = mail($email, $them_subject, $them_message, $them_headers);
                if ($tmail == true) {
                    echo '<p><span class="success">Success!</span> Your application was processed' .
                        ' and you are now listed under the pending list for approval. Your information' .
                        " has been sent to you. :)</p>\n";
                } elseif ($tmail == false) {
                    echo '<p><span class="success">Success!</span> Your application was processed' .
                        ' and you are now listed under the pending list for approval. However, the' .
                        " script was unable to send your information to you.</p>\n";
                }
            } else {
                echo '<p><span class="success">Success!</span> Your application was processed' .
                    " and you are now listed under the pending list for approval!</p>\n";
            }
        }
    } else {
        $symb = $getItem->markup === 'html5' ? '&#187;' : '&raquo;';
        $mark = $getItem->markup === 'xhtml' ? ' /' : '';
        $a2 = sha1(random_int(10000, 999999));
        $b1 = random_int(1, 10);
        $b2 = random_int(1, 10);
        $b3 = $b1 + $b2;
        $b4 = $b1 . ' + ' . $b2;
        $f3 = !empty($fave_field) && isset($fave_field) ? 'enctype="multipart/form-data" ' : '';
        $j2 = $octopus->formURL('join', $getItem);
        $u2 = $octopus->formURL('update', $getItem);
        ?>
        <p>Please use the form below for joining only. Hit the submit button only once,
            as your application is entered into the database, and ready for approval. If you
            have any problems, feel more than free to contact me <?php echo $hide_address_check; ?>.
            If you would like to update your information, you can
            <a href="<?php echo $u2; ?>">do so here</a>. The asterisks (*) are
            required fields.</p>

        <form action="<?= $j2; ?>" <?= $f3; ?>method="post">
            <?php
            if (
                $seahorses->getOption('javascript_opt') == 'y' ||
                $seahorses->getOption('captcha_opt') == 'y' ||
                $seahorses->getOption('antispam_opt') == 'y'
            ) {
                echo "<p style=\"margin: 0;\">\n";
            }

            echo '<!-- Listing Admin ' . $laoptions->version . ' Join Form -->';
            if ($seahorses->getOption('javascript_opt') == 'y') {
                echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
            }

            if ($seahorses->getOption('captcha_opt') == 'y') {
                echo $octopus->captchaCheat($a2);
            }

            if ($seahorses->getOption('antispam_opt') == 'y') {
                echo $octopus->antispamCheat($b3, $b4);
            }

            if (
                $seahorses->getOption('javascript_opt') == 'y' ||
                $seahorses->getOption('captcha_opt') == 'y' ||
                $seahorses->getOption('antispam_opt') == 'y'
            ) {
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
                <p><label>* <strong>Country:</strong></label> <select name="country" class="input1" required="required">
                        <option value="Choose">&#8212; Choose</option>
                        <?php require('countries.inc.php'); ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Password</legend>
                <p>Passwords are required to update your information (if wanted); however, you
                    can leave the fields blank and have the script generate a 16 alphanumeric
                    password for you.</p>
                <p class="inputPassword">
                    <label style="float: left; padding: 0 1%; width: 48%;">
                        <strong>Password</strong><br<?php echo $mark; ?>>
                        Type twice for verification:
                    </label>
                    <input name="password" class="input1" style="width: 48%;"
                           type="password"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                    <input name="passwordv" class="input1" style="width: 48%;" type="password"<?php echo $mark; ?>>
                </p>
            </fieldset>

            <fieldset>
                <legend>Options</legend>
                <p><label><strong>Show my e-mail on the members list?</strong></label>
                    <input name="visible" checked="checked" class="input3" type="radio" value="0"<?php echo $mark; ?>>
                    Yes
                    <input name="visible" class="input3" type="radio" value="1"<?php echo $mark; ?>> No</p>
                <?php
                if (!empty($getItem->fave_fields) && file_exists('joinff.inc.php')) {
                    require('joinff.inc.php');
                } else {
                    if (!empty($getItem->fave_fields) || (!empty($fave_field) && isset($fave_field))) {
                        $fave_fields_db = $getItem->fave_fields;
                        echo '<p style="clear: both; margin: 0;"></p>';
                        echo $snakes->favejoin();
                    }
                }

                if ($options->formComments) {
                    ?>
                    <p style="clear: both; margin: 0;"></p>
                    <p><label>Comments:</label> <textarea name="comments" class="input1" cols="50" rows="10"></textarea>
                    </p>
                    <?php
                }
                ?>
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
                                 src="<?php echo $my_website; ?>fun-captcha.inc.php?v=<?php echo $a2; ?>"<?php echo $mark; ?>>
                        </p>
                        <?php
                    }
                    if ($seahorses->getOption('antispam_opt') == 'y') {
                        ?>
                        <p class="inputAntispam">
                            <label style="float: left; padding: 0 1%; width: 48%;">
                                <strong><?php echo $b4; ?></strong><br<?php echo $mark; ?>>
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
                           value="y"<?= $mark; ?>> Send me my details!
                </p>
                <p class="tc">
                    <input name="action" class="input2" type="submit" value="Join Listing"<?= $mark; ?>>
                </p>
            </fieldset>
        </form>

        <p class="showCredits-LA-RF" style="text-align: center;">
            Powered by <?= $octopus->formatCredit(); ?>
        </p>
        <?php
    }
    ?>
</div>
