<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <affiliates.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;

$getTitle = 'Affiliates';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = isset($_GET['listing']) && !isset($_GET['g']) ?
    '<span><a href="affiliates.php?listing=' . $tigers->cleanMys($_GET['listing']) .
    '&#38;g=new">Add Affiliate</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if (!isset($_GET['p']) || empty($_GET['p']) || !ctype_digit($_GET['p'])) {
    $start = 0;
} else {
    $start = $tigers->cleanMys($per_page * ($tigers->cleanMys($_GET['p']) - 1));
}
$ender = $start + $per_page;

if (isset($_GET['listing']) && is_numeric($_GET['listing'])) {
$getlistingid = $tigers->cleanMys($_GET['listing']);

/**
 * New Affiliate
 */
if (isset($_GET['g']) && $_GET['g'] == 'new') {
    ?>
    <form action="affiliates.php?listing=<?php echo $getlistingid; ?>" enctype="multipart/form-data" method="post">
        <fieldset>
            <legend>Details</legend>
            <p><label><strong>Subject:</strong></label>
                <input name="subject" class="input1" type="text"></p>
            <p><label><strong>E-Mail:</strong></label>
                <input name="email" class="input1" type="email"></p>
            <p><label><strong>URI:</strong></label>
                <input name="url" class="input1" type="url"></p>
            <p><label><strong>E-Mail Recipient:</strong></label>
                <input name="rec" class="input3" type="radio" value="y"> Yes
                <input name="rec" checked="checked" class="input3" type="radio" value="n"> No</p>
        </fieldset>

        <fieldset>
            <legend>Image</legend>
            <p><label><strong>Image:</strong></label> <input name="image" class="input1" type="file"></p>
        </fieldset>

        <fieldset>
            <legend>Listing</legend>
            <p><label><strong>Listing:</strong></label>
                <select name="listing[]" class="input1" multiple="multiple" size="10">
                    <?php
                    $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option value=\"0\">No Listings Available</option>\n";
                    } else {
                        $c = $getlistingid == '0' || $getlistingid == 0 ? ' selected="selected"' : '';
                        echo "<option$c value=\"collective\">&#8212; Collective</option>\n";
                        while ($getItem = $scorpions->obj($true)) {
                            echo '  <option value="' . $getItem->id . '"';
                            if ($getItem->id == $getlistingid) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $getItem->subject . "</option>\n";
                        }
                    }
                    ?>
                </select></p>
        </fieldset>

        <fieldset>
            <legend>Submit</legend>
            <p class="tc">
                <input name="action" class="input2" type="submit" value="Add Affiliate">
                <input class="input2" type="reset" value="Reset Form">
            </p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Add Affiliate') {
    $getItem = $wolves->getListings($getlistingid, 'object');
    $subject = $tigers->cleanMys($_POST['subject']);
    if (empty($subject)) {
        $tigers->displayError('Form Error', 'Your <samp>subject</samp> field' .
            ' is empty.', false);
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
    $rec = $tigers->cleanMys($_POST['rec']);
    $image = $_FILES['image'];
    $image_tag = substr(sha1(date('YmdHis')), random_int(0, 9), 15);
    if (!empty($_FILES['image']['name'])) {
        $imageinfo = getimagesize($_FILES['image']['tmp_name']);
        $imagetype = $imageinfo[2];
        if ($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
            $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' .
                ' <samp>.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
        }
    }

    if (!isset($getItem->dblist) || $getItem->dblist != 1) {
        $listing = $_POST['listing'];
        $listing = array_map(array($tigers, 'cleanMys'), $listing);
        $listing = $tigers->collective($listing);

        foreach ($listing as $listingid) {
            $v = $listingid == '0' ? 'collective' : 'listing';
            $g = $listingid == '0' ? 'n' : $listingid;

            if ($rec == 'y') {
                $mail = $jaguars->sendAffEmail(
                    'affiliates_approve', 'Affiliate Added', $v, 'n', 'n', $g, $url, $email
                );
                if ($mail == true) {
                    echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                        " affiliate was notified!</p>\n";
                }
            }
        }
        $dblisting = implode('!', $listing);
        $dblisting = '!' . trim($dblisting, '!') . '!';
    } else {
        $dblisting = '!' . $getItem->dbflid . '!';
        if ($rec == 'y') {
            $mail = $jaguars->sendAffEmail(
                'affiliates_approve', 'Affiliate Added', 'listing', 'n', 'n', $getlistingid, $url, $email
            );
            if ($mail == true) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                    " affiliate was notified!</p>\n";
            }
        }
    }

    $aff_path = (!isset($getItem->dblist) || $getItem->dblist != 1) ? ($seahorses->getVar($getlistingid, 'dbpath')
    != '' ? $seahorses->getVar($getlistingid, 'dbpath') :
        $seahorses->getOption('aff_path')) : $seahorses->getOption('aff_path');
    if (!empty($aff_path)) {
        $path = $aff_path;
    } else {
        $path = str_replace('affiliates.php', '', $_SERVER['SCRIPT_FILENAME']);
    }

    $e = file_exists($path . 'LAdminAff_' . $image['name']) ? $image_tag . '_' : '';
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $file = $scorpions->escape('LAdminAff_' . $e . $image['name']);
        $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
    } else {
        $file = '';
    }

    if ($getlistingid != '0' && $getItem->dblist == 1) {
        $scorpions->scorpions($getItem->dbhost, $getItem->dbuser, $getItem->dbpass,
            $getItem->dbname);

        $dbtable = $getItem->dbaffs;
        if ($getItem->dbtype == 'enth') {
            $insert = "INSERT INTO `$dbtable` (`url`, `title`, `imagefile`, `email`," .
                " `added`) VALUES ('$url', '$subject', '$file', '$email', CURDATE())";
        } elseif ($getItem->dbtype == 'listingadmin') {
            $insert = "INSERT INTO `$dbtable` (`fNiq`, `aSubject`, `aEmail`, `aURL`," .
                " `aImage`, `aAdd`) VALUES ('$dblisting', '$subject', '$email', '$url', '$file'," .
                ' CURDATE())';
            $scorpions->query("SET NAMES 'utf8';");
        }
        $true = $scorpions->query($insert);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to add' .
                ' the affiliate to the database.|Make sure your affiliates table exists.',
                false, $insert);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                " affiliate was added to the database!</p>\n";
            if (isset($success) && $success) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                    " affiliate image was uploaded!</p>\n";
            }
            echo '<p class="backLink">&#171; <a href="affiliates.php?listing=' . $getlistingid .
                "\">Back to '" . $getItem->subject . "' Affiliates</a></p>\n";
            echo $tigers->backLink('aff');
        }
        $scorpions->breach(0);
        $scorpions->breach(1);
    } else {
        $insert = "INSERT INTO `$_ST[affiliates]` (`fNiq`, `aSubject`, `aEmail`, `aURL`," .
            " `aImage`, `aAdd`) VALUES ('$dblisting', '$subject', '$email', '$url', '$file'," .
            ' CURDATE())';
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($insert);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to add' .
                ' the affiliate to the database.|Make sure your affiliates table exists.',
                false, $insert);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                " affiliate was added to the database!</p>\n";
            if (isset($success) && $success == true) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                    " affiliate image was uploaded!</p>\n";
            }
            echo $tigers->backLink('aff', 'n', 'n', $getlistingid);
            echo $tigers->backLink('aff');
        }
    }
} /**
 * @section   Edit Affiliate
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
    $id = $tigers->cleanMys($_GET['d']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
            ' selected an incorrect affiliate or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }
    $listing = $wolves->getListings($getlistingid, 'object');
    $getItem = $rabbits->getAffiliate($id, 'id', $getlistingid);
    ?>
    <form action="affiliates.php?listing=<?php echo $getlistingid; ?>" enctype="multipart/form-data" method="post">
        <p class="noMargin">
            <input name="id" type="hidden" value="<?php echo $getItem->aID; ?>">
        </p>

        <fieldset>
            <legend>Details</legend>
            <p><label><strong>Update Affiliate Date?</strong><br>
                    If set to yes, the affiliate will update to the current date.</label>
                <input name="datenow" class="input3" type="radio" value="y"> Yes
                <input name="datenow" checked="checked" class="input3" type="radio" value="n"> No</p>
            <p style="clear: both; margin: 0 0 2% 0;"></p>
            <p class="tc"><label><strong>Subject:</strong></label>
                <input name="subject" class="input1" type="text" value="<?php echo $getItem->aSubject; ?>"></p>
            <p class="tc"><label><strong>E-Mail:</strong></label>
                <input name="email" class="input1" type="email" value="<?php echo $getItem->aEmail; ?>"></p>
            <p class="tc"><label><strong>URI:</strong></label>
                <input name="url" class="input1" type="url" value="<?php echo $getItem->aURL; ?>"></p>
        </fieldset>

        <fieldset>
            <legend>Image</legend>
            <?php
            $http = $listing->dblist == 1 ? $seahorses->getVar($getlistingid, 'dbhttp') :
                $seahorses->getOption('aff_http');
            $path = $listing->dblist == 1 ? $seahorses->getVar($getlistingid, 'dbpath') :
                $seahorses->getOption('aff_path');

            $img = $path . $getItem->aImage;
            if (!empty($getItem->aImage) && file_exists($img)) {
                ?>
                <p class="tc"><img src="<?php echo $http . $getItem->aImage; ?>" alt=""></p>
            <?php } ?>
            <p><label><strong>Changes:</strong></label>
                <input name="change" class="input3" type="radio" value="add"> Add
                <input name="change" class="input3" type="radio" value="edit"> Edit
                <input name="change" class="input3" type="radio" value="delete"> Delete
                <input name="change" checked="checked" class="input3" type="radio" value="none"> No Change</p>
            <p><label><strong>New Image:</strong></label> <input name="image" class="input1" type="file"></p>
        </fieldset>

        <?php
        if ($listing->dblist == 0 || $listing->dblist == '0') {
            ?>
            <fieldset>
                <legend>Listing</legend>
                <p><label><strong>Listing:</strong></label>
                    <select name="listing[]" class="input1" multiple="multiple" size="10">
                        <?php
                        $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option value=\"0\">No Listings Available</option>\n";
                        } else {
                            $c = $getlistingid == 0 || $getlistingid == '0' ? 'selected="selected" ' : '';
                            echo "  <option {$c}value=\"0\">&#187; Collective</option>\n";
                            while ($getCat = $scorpions->obj($true)) {
                                $cats = explode('!', $getItem->fNiq);
                                echo '  <option value="' . $getCat->id . '"';
                                if (in_array($getCat->id, $cats)) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $getCat->subject . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
            </fieldset>
            <?php
        }
        ?>

        <fieldset>
            <legend>Submit</legend>
            <p class="tc"><input name="action" class="input2" type="submit" value="Edit Affiliate"></p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Affiliate') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Form Error', 'Your ID is empty. This means' .
            ' you selected an incorrect affiliate or you\'re trying to access something' .
            ' that doesn\'t exist. Go back and try again.', false);
    }
    $listingnow = $wolves->getListings($getlistingid, 'object');
    if ($listingnow->dblist == 0 || $listingnow->dblist == '0') {
        $listing = $_POST['listing'];
        if (empty($listing)) {
            $listing[] = '0';
        }
        $listing = array_map(array($tigers, 'cleanMys'), $listing);
        $dblisting = implode('!', $listing);
        $dblisting = '!' . trim($dblisting, '!') . '!';
    } else {
        $dblisting = '!' . $listingnow->dbflid . '!';
    }
    $subject = $tigers->cleanMys($_POST['subject']);
    if (empty($subject)) {
        $tigers->displayError('Form Error', 'Your <samp>subject</samp> field' .
            ' is empty.', false);
    }
    $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
    if (empty($email)) {
        $tigers->displayError('Form Error', 'Your <samp>email</samp> field' .
            ' is empty.', false);
    }
    $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
    if (empty($url)) {
        $tigers->displayError('Form Error', 'Your <samp>url</samp> field is' .
            ' empty.', false);
    }
    $change = $tigers->cleanMys($_POST['change']);
    $changeArray = array('add', 'edit', 'delete', 'none');
    if (!in_array($change, $changeArray)) {
        $tigers->displayError('Form Error', 'You can only add, edit and delete' .
            ' an image.', false);
    }
    $image = $_FILES['image'];
    $image_tag1 = substr(md5(random_int(80, 680)), 0, 5);
    $image_tag2 = substr(md5(date('YmdHis')), 0, 5);
    if ($change == 'add' || $change == 'edit') {
        $imageinfo = getimagesize($_FILES['image']['tmp_name']);
        $imagetype = $imageinfo[2];
        if ($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
            $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' .
                ' <samp>.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
        }
    }

    if ($change != 'none' && $change != 'add') {
        $sImage = $rabbits->pullImage_Affiliate($id);
        $dImage = $seahorses->getOption('aff_path') . $sImage;

        if ($change == 'delete' || $change == 'edit') {
            if (!empty($sImage) && file_exists($dImage)) {
                $delete = @unlink($dImage);
            }
        }
    }

    $aff_path = $listingnow->dblist == 1 ? $seahorses->getVar($getlistingid,
        'dbpath') : $seahorses->getOption('aff_path');
    if (!empty($aff_path)) {
        $path = $aff_path;
    } else {
        $path = str_replace('affiliates.php', '', $_SERVER['SCRIPT_FILENAME']);
    }

    if (file_exists($path . 'LAdminAff_' . $image['name']) && !empty($image['name'])) {
        $image_tag = substr(sha1(date('YmdHis')), 0, 10);
        $e = $image_tag . '_';
    } else {
        $e = '';
    }

    $file = $scorpions->escape('LAdminAff_' . $e . $image['name']);
    if ($change == 'add' || $change == 'edit') {
        if ($change != 'delete' && $change != 'none') {
            $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
        }
    }

    if ($listingnow->dblist == 1) {
        $scorpions->scorpions($listingnow->dbhost, $listingnow->dbuser,
            $listingnow->dbpass, $listingnow->dbname);

        $dbtable = $listingnow->dbaffs;
        if ($listingnow->dbtype == 'enth') {
            $update = "UPDATE `$dbtable` SET `title` = '$subject', `email` = '$email'," .
                " `url` = '$url'";
            if ($change == 'add' || $change == 'edit') {
                $update .= ", `imagefile` = '$file'";
            }
            if (isset($_POST['datenow']) && $_POST['datenow'] == 'y') {
                $update .= ', `added` = CURDATE()';
            }
            $update .= " WHERE `affiliateid` = '$id' LIMIT 1";
        } elseif ($listingnow->dbtype == 'listingadmin') {
            $update = "UPDATE `$dbtable` SET `aSubject` = '$subject', `aEmail` = '$email'," .
                " `aURL` = '$url'";
            if ($change == 'add' || $change == 'edit') {
                $update .= ", `aImage` = '$file'";
            }
            if (isset($_POST['datenow']) && $_POST['datenow'] == 'y') {
                $update .= ', `aAdd` = CURDATE()';
            }
            $update .= " WHERE `aID` = '$id' LIMIT 1";
            $scorpions->query("SET NAMES 'utf8';");
        }
        $true = $scorpions->query($update);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' update the affiliate.|Make sure your ID is not empty and your affiliates' .
                ' table exists.', true, $update);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                " affiliate was updated!</p>\n";
            echo '<p class="backLink">&#171; <a href="affiliates.php?listing=' .
                $getlistingid . "\">Back to '" . $listingnow->subject . "' Affiliates</a></p>\n";
            echo $tigers->backLink('aff');
        }
        $scorpions->breach(0);
        $scorpions->breach(1);
    } else {
        $update = "UPDATE `$_ST[affiliates]` SET `fNiq` = '$dblisting', `aSubject` =" .
            " '$subject', `aEmail` = '$email', `aURL` = '$url',";
        if ($change == 'add' || $change == 'edit') {
            $update .= " `aImage` = '$file',";
        }
        $update .= " `aAdd` = CURDATE() WHERE `aID` = '$id' LIMIT 1";
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($update);
        if ($true == false) {
            $tigers->displayError('Database Error', 'Unable to update the' .
                ' affiliate.|Make sure your ID is not empty and your affiliates table' .
                ' exists.', true, $update);
        } elseif ($true == true) {
            echo $tigers->displaySuccess('Your affiliate was updated!');
            $tigers->backLink('aff', $id, 'n', $getlistingid);
            $tigers->backLink('aff');
        }
    }

    if (isset($delete, $success)) {
        if ($delete && $success) {
            echo $tigers->displaySuccess('Your old image was deleted and replaced with' .
                ' a new one!');
        }
    } elseif (isset($delete) && !isset($success)) {
        if ($delete) {
            echo $tigers->displaySuccess('Your old image was deleted!');
        }
    } elseif (!isset($delete) && isset($success)) {
        if ($success) {
            echo $tigers->displaySuccess('Your image was uploaded!');
        }
    }
    if ($listing->dblist != 1) {
        echo $tigers->backLink('aff', 'n', 'n', $getlistingid);
    }
    echo $tigers->backLink('aff');
} /**
 * @section   Delete Affiliate
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
    $listing = $wolves->getListings($getlistingid, 'object');
    $id = $tigers->cleanMys($_GET['d']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Script Error', 'Your ID is empty. This means' .
            ' you selected an incorrect affiliate or you\'re trying to access something' .
            ' that doesn\'t exist. Go back and try again.', false);
    }
    $getItem = $rabbits->getAffiliate($id, 'id', $getlistingid);
    $s = $getlistingid == '0' ? '<strong>' . $qname . '</strong> collective' :
        '<strong>' . $listing->subject . '</strong> listing';
    ?>
    <p>You are about to delete the <strong><?php echo $getItem->aSubject; ?></strong>
        affiliate listed under the <?php echo $s ?>; please be aware that once you delete
        a affiliate, it is gone forever. <em>This cannot be undone!</em> To proceed,
        click the "Delete Affiliate" button.</p>

    <form action="affiliates.php?listing=<?php echo $getlistingid; ?>" method="post">
        <p class="noMargin"><input name="id" type="hidden" value="<?php echo $getItem->aID; ?>"></p>

        <fieldset>
            <legend>Delete Affiliate</legend>
            <p class="tc">Deleting <strong><?php echo $getItem->aSubject; ?></strong></p>
            <p class="tc"><input name="action" class="input2" type="submit" value="Delete Affiliate"></p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Affiliate') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Form Error', 'Your ID is empty. This means you' .
            ' selected an incorrect affiliate or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }
    $listing = $wolves->getListings($getlistingid, 'object');
    $affiliate = $rabbits->getAffiliate($id, 'id', $getlistingid);

    $sImage = $affiliate->aImage;
    $path = $getlistingid != '0' && $listing->dblist == 1 && $listing->dbtype ==
    'enth' ? $listing->dbpath : $seahorses->getOption('aff_path');
    $dImage = $path . $sImage;
    if (file_exists($dImage) && !empty($sImage)) {
        $delete = @unlink($dImage);
    }

    if ($getlistingid != '0' && $listing->dblist == 1) {
        $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
            $listing->dbname);

        $dbtable = $listing->dbaffs;
        if ($listing->dbtype == 'enth') {
            $delete = "DELETE FROM `$dbtable` WHERE `affiliateid` = '$id' LIMIT 1";
        } elseif ($listing->dbtype == 'listingadmin') {
            $delete = "DELETE FROM `$dbtable` WHERE `aID` = '$id' LIMIT 1";
        }
        $true = $scorpions->query($delete);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' delete the affiliate.|Make sure your ID is not empty and your affiliates' .
                ' table exists.', true, $delete);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                " affiliate was deleted!</p>\n";
            echo $tigers->backLink('aff', 'n', 'n', $getlistingid);
        }
        $scorpions->breach(0);
        $scorpions->breach(1);
    } else {
        $delete = "DELETE FROM `$_ST[affiliates]` WHERE `aID` = '$id' LIMIT 1";
        $true = $scorpions->query($delete);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' delete the affiliate.|Make sure your ID is not empty and your affiliates' .
                ' table exists.', true, $delete);
        } elseif ($true == true) {
            echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                " affiliate was deleted!</p>\n";
            echo $tigers->backLink('aff', 'n', 'n', $getlistingid);
        }
    }
} /**
 * Index
 */
else {
?>
<p>Welcome to <samp>affiliates.php?listing=<?php echo $getlistingid; ?></samp>,
    the page to add new affiliates, and edit or delete your current ones! Below is
    the list of your affiliates. To edit or delete, click "Edit" or "Delete" by
    the appropriate affiliate.</p>

<form action="affiliates.php" method="get">
    <p class="noMargin"><input name="g" type="hidden" value="searchAffiliates"></p>
    <fieldset>
        <legend>Search Affiliates</legend>
        <div id="affSearchForm">
            <p style="float: left; margin: 0; padding: 0 1% 0 0; width: 49%;">
                <select name="s" id="searchAff" class="input4" onchange="toggleForm('searchAff');">
                    <option value="email">E-Mail</option>
                    <option selected="selected" value="subject">Subject</option>
                    <option value="url">URL</option>
                </select>
            </p>
            <p style="float: right; margin: 0 1% 0 0; padding: 0; width: 49%;">
                <input name="q" disabled="disabled" id="subject_url" class="input4" style="display: block;" type="text">
                <select name="q" disabled="disabled" id="email" class="input4" style="display: none;">
                    <?php
                    $select = $rabbits->affiliatesList($getlistingid, 'asc');
                    foreach ($select as $obj) {
                        $getTion = $rabbits->getAffiliate($obj, 'id', $getlistingid);
                        $cleanEmail = StringUtils::instance()->normalizeEmail($laantispam->clean($getTion->aEmail, 'n', 'y'));
                        echo '<option value="' . $cleanEmail . '">' . $getTion->aEmail . "</option>\n";
                    }
                    ?>
                </select>
            </p>
        </div>
        <p style="clear: both;"></p>
        <p class="tc"><input class="input2" type="submit" value="Search Affiliates"></p>
    </fieldset>
</form>
<?php
if (isset($_GET['g']) && $_GET['g'] == 'searchAffiliates') {
    $a = array(
        'searchType' => $tigers->cleanMys($_GET['s']),
        'searchText' => $tigers->cleanMys($_GET['q'])
    );
    $s = 'affiliates';
    $b = $a;
} else {
    $s = '';
    $b = '';
}
$select = $rabbits->sortAffiliates($getlistingid, $s, $b);
$count = is_countable($select) ? count($select) : 0;

if ($count > 0) {
if ((int)$ender > $count) {
    $ender = $count;
}

if (isset($_GET['get']) && $_GET['get'] == 'searchAffiliates') {
    $s = $tigers->cleanMys($_GET['s']);
    echo '<h4>Searching for the <em>' . $get_affsearch_array[$s] . '</em> ' .
        $tigers->cleanMys($_GET['q']) . "...</h4>\n";
}
?>
<table class="index" width="100%">
    <thead>
    <tr>
        <th>Image</th>
        <th>Subject</th>
        <th>E-Mail</th>
        <th>Action</th>
    </tr>
    </thead>
    <?php
    $listing = $wolves->getListings($getlistingid, 'object');
    while ($start < $ender) {
        $u = $select[$start];
        $getItem = $rabbits->getAffiliate($u['aID'], 'id', $getlistingid);
        $m = $getlistingid != '0' && $listing->dblist == 1 && $listing->dbtype ==
        'enth' ? $listing->dbhttp : $seahorses->getOption('aff_http');
        ?>
        <tbody>
        <tr>
            <td class="tc"><img src="<?php echo $m . $getItem->aImage; ?>" alt=""></td>
            <td class="tc"><a href="<?php echo $getItem->aURL; ?>"><?php echo $getItem->aSubject; ?></a></td>
            <td class="tc"><?php echo $getItem->aEmail; ?></td>
            <td class="tc floatIcons">
                <a href="affiliates.php?listing=<?php echo $getlistingid; ?>&#38;g=old&#38;d=<?php echo $getItem->aID; ?>">
                    <img src="img/icons/edit.png" alt="">
                </a>
                <a href="emails.php?p=aff&#38;d=<?php echo $getlistingid; ?>&#38;a=<?php echo $getItem->aID; ?>">
                    <img src="img/icons/email.png" alt="">
                </a>
                <a href="affiliates.php?listing=<?php echo $getlistingid; ?>&#38;g=erase&#38;d=<?php echo $getItem->aID; ?>">
                    <img src="img/icons/delete.png" alt="">
                </a>
            </td>
        </tr>
        </tbody>
        <?php
        $start++;
    }
    echo "</table>\n";

    $p = !isset($_GET['p']) || !is_numeric($_GET['p']) ? 1 : $tigers->cleanMys($_GET['p']);
    $pages = ceil((is_countable($rabbits->affiliatesList($getlistingid)) ? count($rabbits->affiliatesList($getlistingid)) : 0) / $per_page);
    echo '<p id="pagination">Pages: ';
    for ($i = 1; $i <= $pages; $i++) {
        if ($p == $i) {
            echo $i . ' ';
        } else {
            echo '<a href="affiliates.php?listing=' . $getlistingid . '&#38;';
            if (isset($_GET['g']) && $_GET['g'] == 'searchMembers') {
                echo 'g=searchMembers&#38;s=' . $tigers->cleanMys($_GET['s']) .
                    '&#38;q=' . $tigers->cleanMys($_GET['q']) . '&#38;';
            }
            echo 'p=' . $i . '">' . $i . '</a> ';
        }
    }
    echo "</p>\n";

    $dbcheck = $wolves->getListings($getlistingid, 'object');
    if ($getlistingid != '0' && $dbcheck->dblist == 1) {
        $scorpions->breach(1);
        $scorpions->breach(0);
    }
    }

    else {
        echo "<p class=\"tc\">Currently no affiliates!</p>\n";
    }
    }
    }

    # -- Show Index of Fanlistings~ ------------------------------------------------
    else {
    $countListings = is_countable($wolves->listingsList()) ? count($wolves->listingsList()) : 0;
    $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC LIMIT $countListings";
    $count = $scorpions->counts($select, 1);
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to select' .
            ' the listings from the database.|Make sure your table exists.',
            true, $select);
    }

    if ($count->rows > 0) {
    ?>

    <table class="index">
        <thead>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Affiliates</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody class="collective">
        <tr>
            <td class="tc" colspan="2">Whole Collective</td>
            <td class="tc"><?php echo $rabbits->countAffiliates('0'); ?></td>
            <td class="tc"><a href="affiliates.php?listing=0">Manage Affiliates</a></td>
        </tr>
        </tbody>
        <?php
        while ($getItem = $scorpions->obj($true)) {
            ?>
            <tbody>
            <tr>
                <td class="tc"><?php echo $getItem->id; ?></td>
                <td class="tc"><?php echo $getItem->subject; ?></td>
                <td class="tc"><?php echo $rabbits->countAffiliates($getItem->id); ?></td>
                <td class="tc"><a href="affiliates.php?listing=<?php echo $getItem->id; ?>">Manage Affiliates</a></td>
            </tr>
            </tbody>
            <?php
        }
        echo "</table>\n\n";
        } else {
            echo "\n<p class=\"tc\">Currently no affiliates to list!</p>\n";
        }
        }

        require('footer.php');
