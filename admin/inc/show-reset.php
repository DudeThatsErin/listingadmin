<div id="show-reset">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-reset.php>
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

    /**
     * Get variables and options~!
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
     * The form has been set, so let's check the variables
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Reset Password') {
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'You have not filled out the <samp>email' .
                '</samp> field.</p>', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the <samp>' .
                'e-mail</samp> field are not allowed.', false);
        }
        if (!$snakes->checkIfEmailExists($email, $options->listingID)) {
            $tigers->displayError('Form Error', 'It appears your email is not listed at' .
                ' this listing. This might be because you are not a member, or you have' .
                ' chosen the wrong e-mail address.', false);
        }
        $password = substr(sha1(date('YmdHis')), 0, 8) . substr(sha1(random_int(80, 850)), 0, 8);

        /**
         * Grab user information so we can log messages
         */
        $userinfo = (object)array(
            'url' => $tigers->cleanMys(
                'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
            ),
            'text' => "E-Mail Address: $email"
        );

        /**
         * Check for spam words and SPAM bots :D
         */
        foreach ($laantispam->spamarray() as $b) {
            foreach ($_POST as $po) {
                if (strpos($po, (string) $b) !== false) {
                    $octopus->writeError(
                        'SPAM Error: SPAM Language', $userinfo->url, $userinfo->text, $automated
                    );
                    $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
                }
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
                $tigers->displayError('Form Error', 'It appears you have JavaScript turned' .
                    ' off. As it is required to have JavaScript enabled, I suggest you go back' .
                    ' and enable it.', false);
            }
        }

        /**
         * Now that we've checked everything: update password, e-mail
         */
        $update = "UPDATE `$_ST[members]` SET `mPassword` = MD5('$password') WHERE" .
            " LOWER(`mEmail`) = '$email' AND `fNiq` = '" . $options->listingID . "' LIMIT 1";
        $true = $scorpions->query($update);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to reset your' .
                ' password. If you so wish it, you can email me ' . $hide_address .
                ' to reset it.', false);
        } else {
            $member = $snakes->getMembers($email, 'email', 'object');
            $subject = $getItem->subject . ': Lost Password';

            $message = '';
            $message .= 'Hello ' . $member->mName . ",\n\n";
            $message .= 'You have received this email because you (or someone else) used' .
                ' this email address to request a lost password at the ' . $getItem->subject .
                ' listing. If this is in error, please reply to this email and tell me and I' .
                " will rectify the situation as soon as possible.\n\nThe information you" .
                ' requested to this list is shown below; I strongly recommend you update your' .
                " password via update form at the listing as soon as you can:\n\n" .
                'Password: ' . $password . "\nListing: " . $getItem->subject . "\n\n--\n" .
                "{$qowns}\n" . $getItem->title . ' <' . $getItem->url . '>';

            $headers = "From: {$qowns} <$my_email>\nReply-To: <$my_email>";

            $mail = mail($email, $subject, $message, $headers);
            if ($mail) {
                echo '<p><span class="success">Success!</span> Your lost password form was' .
                    ' processed, and your password changed. It has been sent to your e-mail' .
                    ' address. From there, I advise you to change your password as soon as you' .
                    " have the chance.</p>\n";
            } else {
                echo '<p>Your form was processed and your password updated, however, I was' .
                    ' unable to send you your password. I would recommend trying again, and if' .
                    ' that fails, you are always free to e-mail me ' . $hide_address .
                    ", and have me change your password for you.</p>\n";
            }
        }
    } else {
        $mark = $getItem->markup == 'xhtml' ? ' /' : '';
        $r2 = $octopus->formURL('reset', $getItem);
        ?>
        <p>Please use the form below for requesting a lost password only. The e-mail you
            enter will supply you with a new password for this listing <em>only</em>. If the
            form does not work for you, or you have lost access to your current e-mail
            account, you can e-mail me <?php echo $hide_address; ?> and ask me to reset it
            for you.</p>

        <form action="<?php echo $r2; ?>" method="post">
            <?php
            if ($seahorses->getOption('javascript_opt') == 'y') {
                echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
            }
            ?>
            <fieldset>
                <legend>Lost Password</legend>
                <p><label>* <strong>E-mail:</strong></label>
                    <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                <p class="tc">
                    <input name="action" class="input2" type="submit" value="Reset Password"<?php echo $mark; ?>>
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
