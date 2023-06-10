<?php
/**
 * @copyright  2007
 * @license    GPL Version 3; BSD Modified
 * @author     Tess <theirrenegadexxx@gmail.com>
 * @file       <e-mails.php>
 * @since      September 2nd, 2010
 * @version    1.0
 */

use Robotess\StringUtils;

$getTitle = 'Emails';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if (isset($_GET['p']) && !empty($_GET['p'])) {
    /**
     * E-mail a single affiliate
     */
    if (isset($_GET['p']) && $_GET['p'] == 'aff') {
        $id = $tigers->cleanMys($_GET['d']);
        if (!in_array($id, $wolves->listingsList())) {
            $tigers->displayError('Script Error', 'Your ID is empty. This means' .
                ' you selected an incorrect affiliate ID or you\'re trying to access' .
                ' something that doesn\'t exist. Go back and try again.', false);
        }
        $listing = $wolves->getListings($id, 'object');
        $aid = $tigers->cleanMys($_GET['a']);
        $affiliate = $rabbits->getAffiliate($aid, 'id', $id);
        ?>
        <form action="emails.php" method="post">
            <p class="noMargin">
                <input name="listingid" type="hidden" value="<?php echo $listing->id; ?>">
                <input name="affiliateid" type="hidden" value="<?php echo $affiliate->aID; ?>">
            </p>

            <fieldset>
                <legend>E-Mail Affiliate</legend>
                <p>You are choosing to e-mail the <strong><?php echo $affiliate->aSubject; ?></strong>
                    affiliate from the <strong><?php echo $wolves->pullSubjects($affiliate->fNiq, '!'); ?></strong>
                    listing(s). Below are two options: a e-mail template (which can edit through
                    <a href="templates_emails.php">&#187; E-Mail Templates</a>) and a subject and
                    body field for a more personalized e-mail.</p>
                <p><label><strong>Template:</strong></label> <select name="template" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[templates_emails]` ORDER BY `title` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option value=\"0\">No Templates</option>\n";
                        } else {
                            echo "  <option value=\"n\">None</option>\n";
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->name . '">' . $getItem->title . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
                <p><label><strong>Subject:</strong></label>
                    <input name="subject" class="input1" type="text"></p>
                <p><label><strong>E-Mail:</strong></label><br>
                    <textarea name="email" cols="50" rows="8" style="width: 100%;"></textarea></p>
                <p class="tc"><input name="action" type="submit" value="E-Mail Affiliate"></p>
            </fieldset>
        </form>
        <?php
    } /**
     * Build form for e-mailing one member
     */
    elseif (isset($_GET['p']) && $_GET['p'] == 'mem') {
        $id = $tigers->cleanMys($_GET['d']);
        if (!in_array($id, $wolves->listingsList())) {
            $tigers->displayError('Script Error', 'Your ID is empty. This means' .
                ' you selected an incorrect listing ID or you\'re trying to access' .
                ' something that doesn\'t exist. Go back and try again.', false);
        }
        $mid = $tigers->cleanMys($_GET['m']);
        if (empty($mid)) {
            $tigers->displayError('Script Error', 'Your member ID is empty.' .
                ' This means you selected an incorrect member or you\'re trying to access' .
                ' something that doesn\'t exist. Go back and try again.', false);
        }

        /**
         * Get our hidden fields depending on crosslisting features:
         */
        $listing = $wolves->getListings($id, 'object');
        $midtype = $listing->dblist == 1 && $listing->dbtype == 'enth' ? 'email' : 'id';
        $s = $listing->dblist == 1 ? " <input name=\"crosslisted\" type=\"hidden\" value=\"y\">\n" .
            ' <input name="crosslistedtype" type="hidden" value="' . $listing->dbtype . "\">\n" : '';
        $member = $snakes->getMembers($mid, $midtype, 'object', $listing->id);
        ?>
        <form action="emails.php" method="post">
            <p class="noMargin">
                <input name="listingid" type="hidden" value="<?php echo $listing->id; ?>">
                <input name="memberid" type="hidden" value="<?php echo $member->mID; ?>">
                <?php echo $s; ?>
            </p>

            <fieldset>
                <legend>E-Mail Member</legend>
                <p>You are choosing to e-mail the <strong><?php echo $member->mName; ?></strong>
                    member from the <strong><?php echo $listing->subject; ?></strong> listing. Below
                    are two options: a e-mail template (which can edit through
                    <a href="templates_emails.php">&#171; E-Mail Templates</a>) and a subject and
                    body field for a more personalized e-mail.</p>
                <p><label><strong>Template:</strong></label> <select name="template" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[templates_emails]` ORDER BY `title` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option value=\"0\">No Templates</option>\n";
                        } else {
                            echo "  <option value=\"n\">None</option>\n";
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->name . '">' . $getItem->title . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
                <p><label><strong>Subject:</strong></label> <input name="subject" class="input1" type="text"></p>
                <p><label><strong>E-Mail:</strong></label><br>
                    <textarea name="email" cols="50" rows="8"
                              style="height: 220px; margin: 0 1% 0 0; width: 99%;"></textarea></p>
                <p class="tc"><input name="action" type="submit" value="E-Mail Member"></p>
            </fieldset>
        </form>
        <?php
    }
} /**
 * E-mail single affiliate from crosslisted/non-crosslisted fanlistings~
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'E-Mail Affiliate') {
    $listingid = $tigers->cleanMys($_POST['listingid']);
    if ($listingid != 0 && $listingid != '0' && !in_array($listingid, $wolves->listingsList())) {
        $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' .
            ' invalid.', false);
    }
    if (!isset($listingid) || $listingid === 0 || $listingid === '0') {
        $list = 'collective';
    } else {
        $list = 'listing';
    }
    $affiliateid = $tigers->cleanMys($_POST['affiliateid']);
    if (!in_array($affiliateid, $rabbits->affiliatesList($listingid))) {
        $tigers->displayError('Form Error', 'The <samp>affiliate ID</samp> field is' .
            ' invalid.', false);
    }
    $template = $tigers->cleanMys($_POST['template']);
    $subject = $tigers->cleanMys($_POST['subject']);
    if (!empty($subject)) {
        $s = $subject;
    } else {
        $s = 'n';
    }
    $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
    if (!empty($email)) {
        $b = $email;
    } else {
        $b = 'n';
    }

    $affiliates = $rabbits->getAffiliate($affiliateid, 'id', $listingid);
    $mailAffiliate = $jaguars->sendAffEmail($template, $s, $list, $affiliateid, $b, $listingid);
    if ($mailAffiliate == true) {
        echo $tigers->displaySuccess('Your email has been sent!');
    }
    echo $tigers->backLink('emails');
} /**
 * E-mail all affiliats from a specified listing!
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'Email Affiliates') {
    $listing = $tigers->cleanMys($_POST['listing']);
    if ($listing != 0 && $listing != '0' && !in_array($listing, $wolves->listingsList())) {
        $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' .
            ' invalid.', false);
    }
    if (!isset($listingid) || $listingid === 0 || $listingid === '0') {
        $list = 'collective';
    } else {
        $list = 'listing';
    }
    $template = $tigers->cleanMys($_POST['template']);
    if (!in_array($template, $jaguars->emailList())) {
        $tigers->displayError('Form Error', 'The <samp>template</samp> field is' .
            ' invalid; go back and try again!', false);
    }
    $b = isset($_POST['email']) && !empty($_POST['email']) ? StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email'])) : 'n';
    $s = isset($_POST['subject']) && !empty($_POST['subject']) ? $tigers->cleanMys($_POST['subject']) : 'n';
    $affiliates = $rabbits->affiliatesList($listing);

    foreach ($affiliates as $a) {
        $mailAffiliate = $jaguars->sendAffEmail($template, $s, 'listing', $a, $b, $listing, 'n', 'n');
        if ($mailAffiliate == true) {
            echo $tigers->displaySuccess('Your email has been sent!');
        }
    }
    echo $tigers->backLink('emails');
} /**
 * E-mail a single member~
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'E-Mail Member') {
    $listingid = $tigers->cleanMys($_POST['listingid']);
    if (!in_array($listingid, $wolves->listingsList())) {
        $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' .
            ' invalid.', false);
    }
    $memberid = $tigers->cleanMys($_POST['memberid']);
    if (empty($memberid) || !in_array($memberid, $snakes->membersList($listingid))) {
        $tigers->displayError('Form Error', 'The <samp>member ID</samp> field is' .
            ' invalid.', false);
    }
    $template = $tigers->cleanMys($_POST['template']);
    $subject = $tigers->cleanMys($_POST['subject']);

    $b = empty($email) ? 'n' : StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
    $c = isset($_POST['crosslisted']) && $_POST['crosslisted'] == 'y' ? 1 : '';
    $d = isset($_POST['crosslistedtype']) ? $tigers->cleanMys($_POST['crosslistedtype']) : '';
    $e = $d == '' ? 'id' : ($d == 'enth' ? 'email' : 'id');
    $s = empty($subject) ? 'n' : $subject;

    $members = $snakes->getMembers($memberid, $d, 'object', $listingid);
    if ($template == 'members_approve') {
        $mailMember = $jaguars->approveMember($memberid, $listingid, $c, $d);
    } elseif ($template == 'members_update') {
        $mailMember = $jaguars->updateMember($memberid, $listingid);
    } else {
        $mailMember = $jaguars->sendEmail($template, 'listing', $memberid, $s, $b, $listingid, '', $e);
    }

    if ($mailMember == true) {
        echo $tigers->displaySuccess('Your email has been sent!');
    }
    echo $tigers->backLink('mem', $listingid);
    echo $tigers->backLink('emails');
} /**
 * E-mail multiple members from a specified listing!
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'Email Members') {
    $listingid = $tigers->cleanMys($_POST['listing']);
    if (!in_array($listingid, $wolves->listingsList())) {
        $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' .
            ' invalid.', false);
    }
    $listing = $wolves->getListings($listingid, 'object');

    $subject = $tigers->cleanMys($_POST['subject']);
    $template = $tigers->cleanMys($_POST['template']);
    $b = $template == 'members_moved' ? (!empty($subject) ? $subject : 'Moved') :
        (!empty($subject) ? $subject : 'n');
    $e = !isset($_POST['email']) || empty($_POST['email']) ? 'n' : StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));

    $members = $snakes->membersList($listingid, 0);
    foreach ($members as $m) {
        $p = $listing->dblist == 1 ? $listing->dbtype : '';
        $c = $listing->dblist == 1 && $listing->dbtype == 'enth' ? 'email' : 'id';
        if ($template == 'members_approve') {
            $mailMember = $jaguars->approveMember($m, $listingid, $listing->dblist, $p);
        } elseif ($template == 'members_update') {
            $mailMember = $jaguars->updateMember($m, $listingid);
        } else {
            $mailMember = $jaguars->sendEmail($template, 'listing', $m, $b, $e, $listingid, '', $c);
        }

        if ($mailMember == true) {
            echo $tigers->displaySuccess('Your emails have been sent!');
        }
    }
    echo $tigers->backLink('mem', $listingid);
    echo $tigers->backLink('emails');
} /**
 * Index
 */
else {
    ?>
    <p>Welcome to <samp>emails.php</samp>, the page to email current members or
        affiliates of a particular listing; emails can include any e-mail template.</p>
    <p class="noteButton"><span class="note">Note:</span> You can select the template
        you'd like below by using the affiliates <em>or</em> members section. Please be
        aware this sends e-mails to <em>all</em> members/affiliates of a particular
        listing. If you'd like to e-mail one member/affiliates, visit the respective
        pages.</p>

    <h3 id="emailAffiliates">Affiliates</h3>
    <p class="noteButton">All fields marked with a asterisk (*) are requred.</p>
    <form action="emails.php" method="post">
        <fieldset>
            <legend>Email Affiliates</legend>
            <p><label>* <strong>Listing:</strong></label> <select name="listing" class="input1">
                    <?php
                    $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option value=\"0\">No Listings</option>\n";
                    } else {
                        echo "  <option value=\"0\">&#8212; Collective</option>\n";
                        while ($getItem = $scorpions->obj($true)) {
                            echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
                        }
                    }
                    ?>
                </select></p>
            <p><label><strong>Subject:</strong></label> <input name="subject" class="input1" type="text"></p>
            <p><label>* <strong>Template:</strong></label> <select name="template" class="input1">
                    <?php
                    $select = "SELECT * FROM `$_ST[templates_emails]` ORDER BY `title` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option value=\"0\">No Templates</option>\n";
                    } else {
                        while ($getItem = $scorpions->obj($true)) {
                            echo '  <option value="' . $getItem->name . '">' . $getItem->title . "</option>\n";
                        }
                    }
                    ?>
                </select></p>
            <p><label><strong>E-Mail:</strong></label><br>
                <textarea name="email" cols="50" rows="8"
                          style="height: 120px; margin: 0 1% 0 0; width: 99%;"></textarea></p>
            <p class="tc"><input name="action" class="input2" type="submit" value="Email Affiliates"></p>
        </fieldset>
    </form>

    <h3 id="emailMembers">Members</h3>
    <p class="noteButton">All fields marked with a asterisk (*) are requred.</p>
    <form action="emails.php" method="post">
        <fieldset>
            <legend>Email Members</legend>
            <p><label>* <strong>Listing:</strong></label> <select name="listing" class="input1">
                    <?php
                    $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option value=\"0\">No Listings</option>\n";
                    } else {
                        while ($getItem = $scorpions->obj($true)) {
                            echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
                        }
                    }
                    ?>
                </select></p>
            <p><label><strong>Subject:</strong></label> <input name="subject" class="input1" type="text"></p>
            <p><label><strong>Template:</strong></label> <select name="template" class="input1">
                    <?php
                    $select = "SELECT * FROM `$_ST[templates_emails]` ORDER BY `title` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option value=\"0\">No Templates</option>\n";
                    } else {
                        while ($getItem = $scorpions->obj($true)) {
                            echo '  <option value="' . $getItem->name . '">' . $getItem->title . "</option>\n";
                        }
                    }
                    ?>
                </select></p>
            <p><label><strong>E-Mail:</strong></label><br>
                <textarea name="email" cols="50" rows="8"
                          style="height: 120px; margin: 0 1% 0 0; width: 99%;"></textarea></p>
            <p class="tc"><input name="action" class="input2" type="submit" value="Email Members"></p>
        </fieldset>
    </form>
    <?php
}

require('footer.php');
