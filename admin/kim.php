<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <kim.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;

$getTitle = 'KIM';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g']) ? '<span><a href="kim.php?g=new">Add KIM Member</a>' .
    '</span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if (!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
    $page = 1;
} else {
    $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if ($seahorses->getOption('kim_opt') == 'y') {
    if (isset($_GET['g']) && $_GET['g'] == 'new') {
        ?>
        <p class="noteButton"><span class="note">Note:</span> This is only for adding a
            member without sending over information e-mails, such as the listing information
            and approval e-mails. If you'd like to send out these e-mails, use the KIM join
            form.</p>

        <form action="kim.php" enctype="multipart/form-data" method="post">
            <fieldset>
                <legend>Listing</legend>
                <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option value=\"0\">No Listings Available</option>\n";
                        } else {
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->id . '">' . $getItem->subject . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Member Details</legend>
                <p><label><strong>Name:</strong></label> <input name="name" class="input1" type="text"></p>
                <p><label><strong>E-Mail:</strong></label> <input name="email" class="input1" type="email"></p>
                <p><label><strong>URL:</strong></label> <input name="url" class="input1" type="url"></p>
                <p><label><strong>Show E-Mail:</strong></label>
                    <input name="visible" class="input3" type="radio" value="0"> Yes
                    <input name="visible" checked="checked" class="input3" type="radio" value="1"> No
                </p>
                <p><label><strong>Previous Owner?</strong></label>
                    <input name="previous" class="input3" type="radio" value="1"> Yes
                    <input name="previous" checked="checked" class="input3" type="radio" value="0"> No
                </p>
            </fieldset>

            <fieldset>
                <legend>Password</legend>
                <p>Passwords are required to update a member's information (if wanted); however,
                    you can leave the fields blank and have the script generate a 16 alphanumeric
                    password for you.</p>
                <p><label style="float: left; padding: 0 1%; width: 48%;">
                        <strong>Password</strong><br>Type twice for verification:</label>
                    <input name="password" class="input1" style="width: 48%;" type="password"><br>
                    <input name="passwordv" class="input1" style="width: 48%;" type="password"></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Add Member"></p>
            </fieldset>
        </form>
        <?php
    } elseif (isset($_POST['action']) && $_POST['action'] == 'Add Member') {
        $listing = $tigers->cleanMys($_POST['listing']);
        if (!in_array($listing, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'In order to be added to the KIM' .
                ' list, you need to choose a listing, love!', false);
        }
        $name = $tigers->cleanMys($_POST['name']);
        if (empty($name)) {
            $tigers->displayError('Form Error', 'The <samp>name</samp> field is' .
                ' empty.', false);
        } elseif (strlen($name) > 20) {
            $tigers->displayError('Script Error', 'The <samp>name</samp> is too' .
                ' long. Go back and shorten it.', false);
        }
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'Your <samp>e-mail</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>e-mail</samp> field are not allowed.', false);
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (empty($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> does' .
                ' not start with http:// and therefore is not valid. Try again.', false);
        }
        $visible = $tigers->cleanMys($_POST['visible']);
        if (!isset($_POST['visible'])) {
            $visible = 1;
        } elseif (!is_numeric($visible) || $visible > 1 || strlen($visible) > 1) {
            $tigers->displayError('Form Error', 'The <samp>visible</samp> field' .
                ' is invalid.', false);
        }
        $previous = $tigers->cleanMys($_POST['previous']);
        if (!isset($_POST['previous'])) {
            $previous = 0;
        } elseif (!is_numeric($previous) || $previous > 1 || strlen($previous) > 1) {
            $tigers->displayError('Form Error', 'The <samp>previous owner</samp>' .
                ' field is invalid.', false);
        }
        $password1 = $tigers->cleanMys($_POST['password']);
        $password2 = $tigers->cleanMys($_POST['passwordv']);
        if (!empty($password1)) {
            if (empty($password2)) {
                $tigers->displayError('Form Error', 'In order to verify the password,' .
                    ' you need to fill out both password fields or leave both empty.', false);
            } elseif ($password1 !== $password2) {
                $tigers->displayError('Script Error', 'The passwords do not match.', false);
            }
        }
        if (empty($password1) && empty($password2)) {
            $hashy1 = substr(sha1(random_int(0, mt_getrandmax())), 0, 8);
            $hashy2 = substr(sha1(date('YmdHis')), 14, 8);
            $pass = $hashy1 . $hashy2;
        } else {
            $pass = $password1;
        }

        $insert = "INSERT INTO `$_ST[kim]` (`mEmail`, `fNiq`, `mName`, `mURL`," .
            ' `mPassword`, `mVisible`, `mPending`, `mPrevious`, `mUpdate`, `mAdd`) VALUES' .
            " ('$email', '$listing', '$name', '$url', MD5('$pass'), '$visible', '0'," .
            " '$previous', 'n', CURDATE())";
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($insert);

        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' insert the member.', true, $insert);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">SUCCESS!</span> The' .
                " KIM member was added! :D</p>\n";
            echo $tigers->backLink('kim');
        }
    } /**
     * Edit
     */
    elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
        $id = $tigers->cleanMys($_GET['d']);
        if (empty($id) || !is_numeric($id)) {
            $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
                ' selected an incorrect KIM member or you\'re trying to access something that' .
                ' doesn\'t exist. Go back and try again.', false);
        }

        $select = "SELECT * FROM `$_ST[kim]` WHERE `mID` = '$id' LIMIT 1";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'Unable to select the specified' .
                ' member from the database.|Make sure the ID is not empty and the KIM' .
                ' members table exists.', true, $select);
        }
        $getItem = $scorpions->obj($true);
        ?>
        <form action="kim.php" enctype="multipart/form-data" method="post">
            <p class="noMargin">
                <input name="id" type="hidden" value="<?php echo $getItem->mID; ?>">
                <?php
                if (isset($_GET['listingid']) && is_numeric($_GET['listingid'])) {
                    $listingid = $tigers->cleanMys((int)$_GET['listingid']);
                    ?>
                    <input name="searchlisting" type="hidden" value="<?php echo $listingid; ?>">
                    <?php
                }
                ?>
            </p>

            <fieldset>
                <legend>Intermiate Changes</legend>
                <p><label><strong>Change date?</strong><br>
                        Would you like this member's added date to be updated to today's date?
                    </label> <input name="changedate" checked="checked" class="input3" type="radio" value="n"> No
                    <input name="changedate" class="input3" type="radio" value="y"> Yes</p>
                <p style="clear: both; margin: 0 0 1% 0;"></p>
                <p><label><strong>E-mail Recipient?</strong><br>
                        Would you like to e-mail the recipient about their update?
                    </label> <input name="changeemail" checked="checked" class="input3" type="radio" value="n"> No
                    <input name="changeemail" class="input3" type="radio" value="y"> Yes</p>
                <p style="clear: both; margin: 0 0 1% 0;"></p>
                <p><label><strong>Change status?</strong><br>
                        Would you like to change the status of this member?
                    </label> <input name="changestatus" checked="checked" class="input3" type="radio" value="n"> No
                    <input name="changestatus" class="input3" type="radio" value="y"> Yes</p>
            </fieldset>

            <fieldset>
                <legend>Member Details</legend>
                <p><label><strong>Name:</strong></label>
                    <input name="name" class="input1" type="text" value="<?php echo $getItem->mName; ?>"></p>
                <p><label><strong>E-mail:</strong></label>
                    <input name="email" class="input1" type="email" value="<?php echo $getItem->mEmail; ?>"></p>
                <p><label><strong>URI:</strong></label>
                    <input name="url" class="input1" type="url" value="<?php echo $getItem->mURL; ?>"></p>
                <p><label><strong>Show E-mail:</strong></label>
                    <?php
                    $emailarray = $get_email_array;
                    foreach ($emailarray as $k => $v) {
                        $h = $k == $getItem->mVisible ? $v . ' (Leave)' : $v;
                        echo '  <input name="visible"';
                        if ($k == $getItem->mVisible) {
                            echo ' checked="checked"';
                        }
                        echo " class=\"input3\" type=\"radio\" value=\"$k\"> $h\n";
                    }
                    ?>
                </p>
                <p><label><strong>Is this member a previous owner?</strong></label>
                    <?php
                    $poarray = $get_previouso_array;
                    foreach ($poarray as $e => $a) {
                        $h = $e == $getItem->mPrevious ? $a . ' (Leave)' : $a;
                        echo '  <input name="previous"';
                        if ($e == $getItem->mPrevious) {
                            echo ' checked="checked"';
                        }
                        echo " class=\"input3\" type=\"radio\" value=\"$e\"> $h\n";
                    }
                    ?>
                </p>
            </fieldset>

            <fieldset>
                <legend>Listing</legend>
                <p><label><strong>Listing:</strong></label> <select name="listing" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "<option value=\"0\">No Listings Available</option>\n";
                        } else {
                            while ($getList = $scorpions->obj($true)) {
                                $cats = $tigers->emptyarray(explode('!', $getItem->fNiq));
                                echo '<option value="' . $getList->id . '"';
                                if (in_array($getList->id, $cats)) {
                                    echo '" selected="selected"';
                                }
                                echo '>' . $getList->subject . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Member"></p>
            </fieldset>
        </form>
        <?php
    } elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Member') {
        $id = $tigers->cleanMys($_POST['id']);
        if (empty($id) || !ctype_digit($id)) {
            $tigers->displayError('Form Error', 'Your ID is empty. This means you' .
                ' selected an incorrect KIM member or you\'re trying to access something that' .
                ' doesn\'t exist. Go back and try again.', false);
        }
        if (
            isset($_POST['searchlisting']) &&
            in_array($tigers->cleanMys($_POST['searchlisting']), $wolves->listingsList())
        ) {
            $searchlisting = $tigers->cleanMys((int)$_POST['searchlisting']);
        }
        $member = $kimadmin->getMember($id);
        $name = $tigers->cleanMys($_POST['name']);
        if (empty($name)) {
            $tigers->displayError('Form Error', 'The <samp>name</samp> field is empty.', false);
        }
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'Your <samp>e-mail</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the' .
                ' <samp>e-mail</samp> field are not allowed.', false);
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (empty($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> field' .
                ' is empty.', false);
        } elseif (!StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> does' .
                ' not start with http:// and therefore is not valid. Try again.', false);
        }
        $visible = $tigers->cleanMys($_POST['visible']);
        if (!isset($_POST['visible'])) {
            $visible = 1;
        } elseif (!is_numeric($visible)) {
            $tigers->displayError('Form Error', 'The <samp>show e-mail</samp> field' .
                ' is not a number.', false);
        }
        $previous = $tigers->cleanMys((int)$_POST['previous']);
        if (!isset($_POST['previous'])) {
            $previous = 0;
        } elseif (!is_numeric($previous) || $previous > 1 || strlen($previous) > 1) {
            $tigers->displayError('Form Error', 'The <samp>previous owner</samp>' .
                ' field is invalid.', false);
        }
        $listing = $tigers->cleanMys($_POST['listing']);
        if (!in_array($listing, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'The <samp>listing</samp> field is' .
                ' invalid.', false);
        }

        if (isset($_POST['changeemail']) && $_POST['changeemail'] == 'y') {
            $mailkim = $jaguars->updateKIM($id);
        }

        $update = "UPDATE `$_ST[kim]` SET `mEmail` = '$email', `fNiq` = '$listing'," .
            " `mName` = '$name', `mURL` = '$url', `mVisible` = '$visible'";
        if (isset($_POST['changedate']) && $_POST['changedate'] == 'y') {
            $update .= ', `mAdd` = CURDATE()';
        }
        if (isset($_POST['changestatus']) && $_POST['changestatus'] == 'y') {
            $changestatus = $member->mPending == 1 || $member->mPending == '1' ? '0' : 1;
            $update .= ", `mPending` = '$changestatus'";
            if ($member->mUpdate == 'y') {
                $update .= ", `mUpdate` = 'n'";
            }
        }
        $update .= " WHERE `mID` = '$id' LIMIT 1";
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($update);

        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' update the KIM member.|Make sure your ID is not empty and your KIM table' .
                ' exists.', true, $update);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">Success!</span> The' .
                " KIM member was updated! :D</p>\n";
            if (isset($mailkim) && $mailkim == true) {
                echo '<p class="successButton"><span class="success">Success!</span> The' .
                    " member was notified of their approval!</p>\n";
            }
            echo $tigers->backLink('kim', $id);
            if (isset($searchlisting)) {
                echo $tigers->backLink('kim', $id, $searchlisting);
            }
            echo $tigers->backLink('kim');
        }
    } /**
     * Delete
     */
    elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
        $id = $tigers->cleanMys((int)$_GET['d']);
        if (empty($id) || !is_numeric($id)) {
            $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
                ' selected an incorrect KIM member or you\'re trying to access something that' .
                ' doesn\'t exist. Go back and try again.', false);
        }

        $select = "SELECT * FROM `$_ST[kim]` WHERE `mID` = '$id' LIMIT 1";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to select' .
                ' the specified KIM member.|Make sure your ID is not empty and your table' .
                ' exists.', true, $select);
        }
        $getItem = $scorpions->obj($true);
        ?>
        <p>You are about to delete the member <strong><?php echo $getItem->mName; ?></strong>
            (of <strong><?php echo $wolves->getSubject($getItem->fNiq); ?></strong>);
            please be aware that once you delete a member, they are gone forever. <em>This
                cannot be undone!</em> To proceed, click the "Delete Member" button.</p>

        <form action="kim.php" method="post">
            <input name="id" type="hidden" value="<?php echo $getItem->mID; ?>">

            <fieldset>
                <legend>Delete Member</legend>
                <p class="tc">
                    Deleting <strong><?php echo $getItem->mName; ?></strong> (of
                    <?php echo $wolves->getSubject($getItem->fNiq); ?>)<br>
                    <input name="action" class="input2" type="submit" value="Delete Member">
                </p>
            </fieldset>
        </form>
        <?php
    } elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Member') {
        $id = $tigers->cleanMys($_POST['id']);
        if (empty($id) || !ctype_digit($id)) {
            $tigers->displayError('Form Error', 'Your ID is empty. This means you' .
                ' selected an incorrect KIM member or you\'re trying to access something that' .
                ' doesn\'t exist. Go back and try again.', false);
        }
        $delete = "DELETE FROM `$_ST[kim]` WHERE `mID` = '$id' LIMIT 1";
        $true = $scorpions->query($delete);

        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to delete' .
                ' the KIM member.|Make sure your ID is not empty and your KIM table exists.',
                true, $delete);
        } elseif ($true == true) {
            echo $tigers->displaySuccess('Your member was deleted!');
            echo $tigers->backLink('kim');
        }
    } /**
     * Mass-approve members
     */
    elseif (isset($_POST['action']) && $_POST['action'] == 'Approve') {
        if (empty($_POST['member'])) {
            $tigers->displayError('Form Error', 'You need to select a member (or' .
                ' two, etc.) in order to approve them.', false);
        }

        foreach ($_POST['member'] as $pm) {
            $m = $tigers->cleanMys($pm);
            $member = $kimadmin->getMember($m, 'id', 'object');
            $update = "UPDATE `$_ST[kim]` SET `mPending` = '0', `mAdd` = CURDATE() WHERE" .
                " `mID` = '$m' LIMIT 1";
            $true = $scorpions->query($update);
            if ($true == true) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> The' .
                    ' <samp>' . $member->mName . '</samp> member (from the <em>' .
                    $wolves->getSubject($member->fNiq) . '</em> listing) has been approved!' .
                    ' :D</p>';
            }
            $mailNow = $jaguars->approveKIM($pm);
            if ($mailNow) {
                echo $tigers->displaySuccess('The member was notified of their approval!');
            }
        }
        echo $tigers->backLink('kim');
    } /**
     * Mass-delete members
     */
    elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
        if (empty($_POST['member'])) {
            $tigers->displayError('Form Error', 'You need to select a member (or' .
                ' two, etc.) in order to approve them.', false);
        }

        foreach ($_POST['member'] as $pm) {
            $m = $tigers->cleanMys($pm);
            $delete = "DELETE FROM `$_ST[kim]` WHERE `mID` = '$m' LIMIT 1";
            $true = $scorpions->query($delete);
            if ($true == true) {
                echo $tigers->displaySuccess('The KIM member was deleted! :D');
            }
        }
        echo $tigers->backLink('kim');
    } /**
     * Mass-update member
     */
    elseif (isset($_POST['action']) && $_POST['action'] == 'Update') {
        if (empty($_POST['member'])) {
            $tigers->displayError('Form Error', 'You need to select a member (or' .
                ' two, etc.) in order to approve them.', false);
        }

        foreach ($_POST['member'] as $pm) {
            $m = $tigers->cleanMys($pm);
            $member = $kimadmin->getMember($m);
            $update = "UPDATE `$_ST[kim]` SET `mPending` = '0', `mUpdate` = 'n', `mAdd`" .
                " = CURDATE() WHERE `mID` = '$pm' LIMIT 1";
            $true = $scorpions->query($update);
            if ($true == true) {
                echo $tigers->displaySuccess('The <samp>' . $member->mName . '</samp> member' .
                    ' (from the <em>' . $wolves->getSubject($member->fNiq) . '</em> listing) has' .
                    ' been update! :D');
            }
            $mailNow = $jaguars->updateKIM($pm);
            if ($mailNow) {
                echo $tigers->displaySuccess('The member was notified of their update!');
            }
        }
        echo $tigers->backLink('kim');
    } /**
     * Index
     */
    else {
        ?>
        <p>Welcome to <samp>kim.php</samp>, the page to edit or delete current
            <abbr title="Keep In Mind">KIM</abbr> members! Below is the list of your members.
            To edit or delete, click "Edit" or "Delete" by the appropriate member.</p>

        <form action="kim.php" method="get">
            <input name="g" type="hidden" value="search">
            <fieldset>
                <legend>Search Listings</legend>
                <p><label><strong>Listing:</strong></label> <select name="listingid" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                        $true = $scorpions->query($select);

                        while ($getTion = $scorpions->obj($true)) {
                            echo '  <option value="' . $getTion->id . '">' . $getTion->subject . "</option>\n";
                        }
                        ?>
                    </select></p>
                <p class="tc"><input class="input2" type="submit" value="Search Listings"></p>
            </fieldset>
        </form>

        <?php
        $select = "SELECT * FROM `$_ST[kim]`";
        if ((isset($_GET['g']) && $_GET['g'] == 'search') && is_numeric($_GET['listingid'])) {
            $listingid = $tigers->cleanMys($_GET['listingid']);
            $select .= " WHERE `fNiq` = '$listingid'";
        }
        $select .= " ORDER BY `mAdd` DESC LIMIT $start, $per_page";
        $true = $scorpions->query($select);
        $count = $scorpions->total($true);

        if ($count > 0) {
            ?>
            <form action="kim.php" method="post">
                <table class="index">
                    <thead>
                    <tr>
                        <th>&#160;</th>
                        <th>Status</th>
                        <th>Listing</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <?php
                    while ($getItem = $scorpions->obj($true)) {
                        $qw = !isset($_GET['listingid']) ? '' : '&#38;listingid=' . $tigers->cleanMys($_GET['listingid']);
                        $pn = $getItem->mPending == 1 ? ($getItem->mUpdate == 'y' ?
                            '<strong>Pending Update</strong>' : '<em>Pending Approval</em>') :
                            'Approved';
                        ?>
                        <tbody>
                        <tr>
                            <td class="tc"><input name="member[]" type="checkbox" value="<?php echo $getItem->mID; ?>">
                            </td>
                            <td class="tc"><?php echo $pn; ?></td>
                            <td class="tc"><?php echo $wolves->getSubject($getItem->fNiq); ?></td>
                            <td class="tc"><?php echo $getItem->mName; ?></td>
                            <td class="tc"><?php echo $getItem->mEmail; ?></td>
                            <td class="floatIcons tc">
                                <a href="kim.php?g=old<?php echo $qw; ?>&#38;d=<?php echo $getItem->mID; ?>">
                                    <img src="img/icons/edit.png" alt="">
                                </a>
                                <a href="kim.php?g=erase&#38;d=<?php echo $getItem->mID; ?>">
                                    <img src="img/icons/delete.png" alt="">
                                </a>
                            </td>
                        </tr>
                        </tbody>
                        <?php
                    }
                    ?>
                    <tfoot>
                    <tr>
                        <td class="tc" colspan="6">With Checked:
                            <input name="action" class="input2" type="submit" value="Approve">
                            <input name="action" class="input2" type="submit" value="Update">
                            <input name="action" class="input2" type="submit" value="Delete">
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </form>
            <?php
            echo '<p id="pagination">Pages: ';
            $select = "SELECT * FROM `$_ST[kim]`";
            if (isset($_GET['g']) && $_GET['g'] == 'search') {
                $select .= " WHERE `fNiq` = '$listingid'";
            }
            $true = $scorpions->query($select);
            $total = $scorpions->total($true);
            $pages = ceil($total / $per_page);


            for ($i = 1; $i <= $pages; $i++) {
                if ($page == $i) {
                    echo $i . ' ';
                } else {
                    $pg = '<a href="kim.php?';
                    if (isset($_GET['g']) && $_GET['g'] == 'search') {
                        $pg .= 'g=search&#38;listingid=' . $listingid . '&#38;';
                    }
                    $pg .= 'p=' . $i . "\">$i</a> ";
                    echo $pg;
                }
            }

            echo "</p>\n";
        } else {
            echo "<p class=\"tc\">Currently no KIM members!</p>\n";
        }
    }
} else {
    ?>
    <p class="errorButton"><span class="error">ERROR:</span> You have turned off the
        <samp>KIM</samp> feature. To turn it on this feature, visit the
        <a href="addons.php">&#171; addons page</a> to install it!</p>
    <?php
}

require('footer.php');
