<div id="show-kim-reset">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-kim-reset.php>
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
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');

    $options = new stdClass();
    $options->markup = $seahorses->getOption('markup') == 'xhtml' ? ' /' : '';

    /**
     * The form is submitted, so run through the variables before formatting
     * our e-mail :D
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Reset Password') {
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'You have not filled out the' .
                ' <samp>email</samp> field.</p>', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>e-mail</samp> field are not allowed.', false);
        }
        $listingid = $tigers->cleanMys($_POST['listing']);
        if (empty($listingid) || !in_array($listingid, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'In order to be added to the' .
                ' KIM list, you need to choose a listing.', false);
        }
        $listing = $wolves->getListings($listingid, 'object');
        if ($kimadmin->checkReset($email, $listingid) == false) {
            $tigers->displayError('Form Error', 'It appears your email is not' .
                ' listed at this listing. This might be because you are not a member, or' .
                ' you have chosen the wrong e-mail address.', false);
        }
        $password = substr(md5(date('YmdHis')), 0, 8) . substr(md5(random_int(10000, 999999)), 0, 8);

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

        /**
         * See if the KIM member exists~
         */
        $update = "UPDATE `$_ST[kim]` SET `mPassword` = MD5('$password') WHERE LOWER(`mEmail`)" .
            " = '$email' AND `fNiq` = '$listingid' LIMIT 1";
        $true = $scorpions->query($update);

        if ($true == false) {
            $tigers->displayError('Script Error', 'The script was unable to reset your' .
                ' password. If you so wish it, you can email me ' . $hide_address .
                ' to reset it.', false);
        } else {
            $member = $kimadmin->getMember($email, 'email', 'object');

            $subject = $qname . ' KIM: Lost Password';
            $message = 'Hello ' . $member->mName . ",\n\nYou have received this email" .
                ' because you (or someone else) used this email address to request a lost' .
                " password at the $qwebs KIM list. If this is in error, please reply to this" .
                ' email and tell me and I will rectify the situation as soon as possible.' .
                "\n\nThe information you requested to this list is shown below; I strongly" .
                ' recommend you update your password via update form at the KIM list as soon' .
                " as you can:\n\nPassword: " . $password . "\nListing: " . $listing->subject .
                "\n\n--\n{$qowns}\n{$qname} <{$qwebs}>";
            $headers = "From: $qowns <$my_email>\nReply-To: <$my_email>";
            $mail = mail($email, $subject, $message, $headers);

            if ($mail) {
                echo '<p><span class="success">Success!</span> Your reset password form was' .
                    ' processed, and your password changed. It has been sent to your e-mail' .
                    ' address. From there, I advise you to change your password as soon as you' .
                    " have the chance.</p>\n";
            } else {
                echo '<p>Your form was processed and your password updated, however, I was' .
                    ' unable to send you your password. I would recommend trying again, and if' .
                    ' that fails, you are always free to e-mail me ' . $hide_address . ', and' .
                    " have me change your password for you.</p>\n";
            }
        }
    } /**
     * Grab quick form!
     */
    else {
        $symb = $seahorses->getOption('markup') == 'html5' ? '&#187;' : '&raquo;';
        if ($seahorses->getOption('markup') == 'xhtml') {
            $mark = ' /';
        } else {
            $mark = '';
        }
        ?>
        <p>Please use the form below for requesting a lost password only. The e-mail you
            enter will supply you with a new password for each listing you choose. If the
            form does not work for you, or you have lost access to your current e-mail
            account, you can e-mail me <?php echo $hide_address; ?> and ask me to reset it
            for you.</p>

        <form action="<?php echo $seahorses->getOption('kim_reset'); ?>" method="post">
            <?php
            if ($seahorses->getOption('javascript_opt') == 'y') {
                echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
            }
            ?>

            <fieldset>
                <legend>Lost Password</legend>
                <p><label>* <strong>E-mail:</strong></label>
                    <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                <p><label>* <strong>Listing:</strong></label> <select name="listing" class="input1" required="required">
                        <?php
                        $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
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
