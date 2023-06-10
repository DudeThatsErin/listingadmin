<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <joined.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;
$getTitle = 'Joined';

require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g'])
|| (isset($_GET['g']) && preg_match('/^(search)([A-Za-z]+)/', $_GET['g'])) ?
    '<span><a href="joined.php?g=new">Add Joined Listing</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if (!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
    $start = 0;
} else {
    $start = $per_joined * ($tigers->cleanMys($_GET['p']) - 1);
}
$ender = $start + $per_joined;

/**
 * @section   Add Joined Listing
 */
if (isset($_GET['g']) && $_GET['g'] == 'new') {
    ?>
    <form action="joined.php" enctype="multipart/form-data" method="post">
        <fieldset>
            <legend>Details</legend>
            <p><label><strong>Subject:</strong></label>
                <input name="subject" class="input1" type="text"></p>
            <p><label><strong>URL:</strong></label>
                <input name="url" class="input1" type="url"></p>
            <p><label><strong>Status:</strong></label>
                <input name="status" class="input3" type="radio" value="0"> Current
                <input name="status" checked="checked" class="input3" type="radio" value="1"> Pending</p>
        </fieldset>

        <fieldset>
            <legend>Image</legend>
            <p><label><strong>Image:</strong></label>
                <input name="image" class="input1" type="file"></p>
            <p><label><strong>Made by You?</strong></label>
                <input name="madeby" class="input3" type="radio" value="y"> Yes
                <input name="madeby" checked="checked" class="input3" type="radio" value="n"> No</p>
        </fieldset>

        <fieldset>
            <legend>Categories</legend>
            <p><label><strong>Categories:</strong></label>
                <select name="category[]" class="input1" multiple="multiple" size="15">
                    <?php
                    $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY `catname` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option value=\"0\">No Categories Available</option>\n";
                    } else {
                        while ($getItem = $scorpions->obj($true)) {
                            $catid = $getItem->catid;
                            echo '  <option value="' . $getItem->catid . '">' . $getItem->catname . "</option>\n";
                            $q2 = $scorpions->query("SELECT * FROM `$_ST[categories]` WHERE `parent` =" .
                                " '$catid' ORDER BY `catname` ASC");
                            while ($getItem2 = $scorpions->obj($q2)) {
                                echo '  <option value="' . $getItem2->catid . '">' .
                                    $lions->getCatName($getItem2->parent) . ' &#187; ' . $getItem2->catname . "</option>\n";
                            }
                        }
                    }
                    ?>
                </select></p>
        </fieldset>

        <fieldset>
            <legend>Submit</legend>
            <p class="tc">
                <input name="action" class="input2" type="submit" value="Add Joined">
                <input class="input2" type="reset" value="Reset Form">
            </p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Add Joined') {
    $subject = $tigers->cleanMys($_POST['subject']);
    if (empty($subject)) {
        $tigers->displayError('Form Error', 'Your <samp>subject</samp> field is' .
            ' empty.', false);
    }
    $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (empty($url)) {
            $tigers->displayError('Form Error', 'Your <samp>site URL</samp> field' .
                ' is empty.', false);
        }
    $status = $tigers->cleanMys($_POST['status']);
    if (!is_numeric($status) || $status > 1 || strlen($status) > 1) {
        $tigers->displayError('Form Error', 'Your <samp>status</samp> field' .
            ' is empty.', false);
    }
    $image_tag = substr(sha1(date('YmdHis')), 0, 10);
    $image = $_FILES['image'];
    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
        $imageinfo = getimagesize($_FILES['image']['tmp_name']);
        $imagetype = $imageinfo[2];
        if ($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
            $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' .
                ' <samp>.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
        }
    }
    $madeby = $tigers->cleanMys($_POST['madeby']);
    if (!empty($madeby) && in_array($madeby, array('y', 'n'))) {
        $mb = $madeby;
    } else {
        $mb = 'n';
    }
    $category = $_POST['category'];
    if (empty($category)) {
        $tigers->displayError('Form Error', 'Your <samp>category</samp> field' .
            ' is empty.', false);
    }
    $category = array_map(array($tigers, 'cleanMys'), $category);

    $jnd_path = $seahorses->getOption('jnd_path');
    if (!empty($jnd_path) || is_dir($jnd_path)) {
        $path = $jnd_path;
    } else {
        $path = str_replace('joined.php', '', $_SERVER['SCRIPT_FILENAME']);
    }

    $e = file_exists($path . $image['name']) ? $image_tag . '_' : '';
    if (!empty($image)) {
        $file = $scorpions->escape($e . $image['name']);
        $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
    } else {
        $file = '';
    }

    $cat = implode('|', $category);
    $cat = '|' . trim($cat, '|') . '|';

    $insert = "INSERT INTO `$_ST[joined]` (`jSubject`, `jURL`, `jImage`, `jCategory`," .
        " `jMade`, `jStatus`, `jAdd`) VALUES ('$subject', '$url', '$file', '$cat'," .
        " '$mb', '$status', CURDATE())";
    $scorpions->query("SET NAMES 'utf8';");
    $true = $scorpions->query($insert);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to add' .
            ' the joined listing to the database.|Make sure your joined table exists.',
            true, $insert);
    } elseif ($true == true) {
        echo '<p class="successButton"><span class="success">Success!</span>' .
            " Your joined listing was added to the database!</p>\n";
        if (isset($success) && $success) {
            echo '<p class="successButton"><span class="success">Success!</span>' .
                " Your joined image was uploaded!</p>\n";
        }
        echo $tigers->backLink('joined');
    }
} /**
 * @section   Edit Joined Listing
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
    if (!isset($_GET['d']) || empty($_GET['d']) || !is_numeric($_GET['d'])) {
        ?>
        <p>Choose a category from the dropdown to view the joined listings listed under
            it.</p>
        <div id="chooseJoined">
            <form action="joined.php" method="get">
                <fieldset>
                    <legend>Choose Category</legend>
                    <p><label for="joined"><strong>Category:</strong></label> <select name="joined" id="joined"
                                                                                      class="input1">
                            <?php
                            $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY `catname` ASC";
                            $true = $scorpions->query($select);
                            if ($true == false) {
                                echo "  <option value=\"0\">No Categories Available</option>\n";
                            } else {
                                while ($getItem = $scorpions->obj($true)) {
                                    $catid = $getItem->catid;
                                    echo '  <option class="selected" id="categoryid' . $catid . '" value="' . $getItem->catid .
                                        '">' . $getItem->catname . "</option>\n";
                                    $q2 = $scorpions->query("SELECT * FROM `$_ST[categories]` WHERE `parent` =" .
                                        " '$catid' ORDER BY `catname` ASC");
                                    while ($getItem2 = $scorpions->obj($q2)) {
                                        echo '  <option class="selected" id="categoryid' . $catid .
                                            '" value="' . $getItem2->catid . '">' . $lions->getCatName($getItem2->parent) .
                                            ' &#187; ' . $getItem2->catname . "</option>\n";
                                    }
                                }
                            }
                            ?>
                        </select></p>
                </fieldset>
            </form>
        </div>

        <?php
        $categories = $lions->categoryList();

        foreach ($categories as $c) {
            $joined = $dragons->joinedList('id', $c);
            if ($joined > 0) {
                ?>
                <div class="joined" id="category<?php echo $c; ?>" style="display: none;">
                    <form action="joined.php" method="get">
                        <input name="g" type="hidden" value="old">
                        <fieldset>
                            <legend>Choose a Joined Listing Under Category
                                <em><?php echo $lions->getCatName($c); ?></em></legend>
                            <p><label for="joinedlisting"><strong>Joined Listing:</strong></label>
                                <select name="d" id="joinedlisting" class="input1">
                                    <?php
                                    $select = "SELECT * FROM `$_ST[joined]` WHERE `jCategory` LIKE '%|$c|%' ORDER" .
                                        ' BY `jSubject` ASC';
                                    $true = $scorpions->query($select);
                                    if ($true == false) {
                                        echo "   <option>Listings Unavailable</option>\n";
                                    } else {
                                        while ($getItem = $scorpions->obj($true)) {
                                            echo '   <option value="' . $getItem->jID . '">' . $getItem->jSubject . "</option>\n";
                                        }
                                    }
                                    ?>
                                </select></p>
                            <p class="tc"><input class="input2" type="submit" value="Edit Joined Listing"></p>
                        </fieldset>
                    </form>
                </div>
                <?php
            }
        }
    } elseif (isset($_GET['d']) && is_numeric($_GET['d'])) {
        $id = $tigers->cleanMys((int)$_GET['d']);
        $select = "SELECT * FROM `$_ST[joined]` WHERE `jID` = '$id' LIMIT 1";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to select' .
                ' that specific joined listing.|Make sure the ID is not empty and the joined' .
                ' table exists.', true, $select);
        }
        $getItem = $scorpions->obj($true);
        ?>
        <form action="joined.php" enctype="multipart/form-data" method="post">
            <p class="noBottom">
                <input name="id" type="hidden" value="<?php echo $getItem->jID; ?>">
                <?php
                if (isset($_GET['c']) && is_numeric($_GET['c'])) {
                    ?>
                    <input name="catid" type="hidden" value="<?php echo $tigers->cleanMys((int)$_GET['c']); ?>">
                    <?php
                }
                ?>
            </p>

            <fieldset>
                <legend>Details</legend>
                <p><label><strong>Update Joined Date?</strong><br>
                        If set to yes, the joined listing will update to the current date.</label>
                    <input name="datenow" class="input3" type="radio" value="yes"> Yes
                    <input name="datenow" checked="checked" class="input3" type="radio" value="no"> No</p>
                <p style="clear: both; margin: 0 0 2% 0;"></p>
                <p><label><strong>Subject:</strong></label>
                    <input name="subject" class="input1" type="text" value="<?php echo $getItem->jSubject; ?>"></p>
                <p><label><strong>URI:</strong></label>
                    <input name="url" class="input1" type="url" value="<?php echo $getItem->jURL; ?>"></p>
                <p><label><strong>Status:</strong></label>
                    <?php
                    $statusarray = $get_status_array;
                    foreach ($statusarray as $k => $v) {
                        echo '  <input name="status"';
                        if ($k == $getItem->jStatus) {
                            echo ' checked="checked"';
                        }
                        echo " class=\"input3\" type=\"radio\" value=\"$k\"> $v\n";
                    }
                    ?>
                </p>
            </fieldset>

            <fieldset>
                <legend>Image</legend>
                <?php
                $img = $seahorses->getOption('jnd_path') . $getItem->jImage;
                if (!empty($getItem->jImage) && file_exists($img)) {
                    ?>
                    <p class="tc"><img src="<?php echo $seahorses->getOption('jnd_http') . $getItem->jImage; ?>" alt="">
                    </p>
                    <?php
                }
                ?>
                <p><label><strong>Changes:</strong></label>
                    <input name="change" class="input3" type="radio" value="add"> Add
                    <input name="change" class="input3" type="radio" value="edit"> Edit
                    <input name="change" class="input3" type="radio" value="delete"> Delete
                    <input name="change" checked="checked" class="input3" type="radio" value="none"> No Change</p>
                <p><label>
                        <strong>New Image:</strong>
                    </label>
                    <input name="image" class="input1" type="file"></p>
                <p><label><strong>Made By You?</strong></label>
                    <?php
                    if ($getItem->jMade == 'y') {
                        ?>
                        <input name="madeby" checked="checked" class="input3" type="radio" value="y"> Yes
                        <input name="madeby" class="input3" type="radio" value="n"> No
                        <?php
                    } elseif ($getItem->jMade == 'n') {
                        ?>
                        <input name="madeby" class="input3" type="radio" value="y"> Yes
                        <input name="madeby" checked="checked" class="input3" type="radio" value="n"> No
                        <?php
                    }
                    ?>
                </p>
            </fieldset>

            <fieldset>
                <legend>Categories</legend>
                <p class="tc"><label>Categories:</label> <select name="category[]" class="input1" multiple="multiple"
                                                                 size="7">
                        <?php
                        $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY `catname` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>No Joined Listings Available</option>\n";
                        } else {
                            while ($getCat = $scorpions->obj($true)) {
                                $catid = $getCat->catid;
                                $cats = explode('|', $getItem->jCategory);
                                echo '  <option value="' . $getCat->catid . '"';
                                if (in_array($getCat->catid, $cats)) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $getCat->catname . "</option>\n";
                                $q2 = $scorpions->query("SELECT * FROM `$_ST[categories]` WHERE `parent` =" .
                                    " '$catid' ORDER BY `catname` ASC");
                                while ($getCat2 = $scorpions->obj($q2)) {
                                    echo '  <option value="' . $getCat2->catid . '"';
                                    if (in_array($getCat2->catid, $cats)) {
                                        echo ' selected="selected"';
                                    }
                                    echo '>' . $lions->getCatName($getCat2->parent) .
                                        ' &#187; ' . $getCat2->catname . "</option>\n";
                                }
                            }
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Joined"></p>
            </fieldset>
        </form>
        <?php
    }
} elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Joined') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Form Error', 'Your ID is empty. This means' .
            ' you selected an incorrect joined listing or you\'re trying to access' .
            ' something that doesn\'t exist. Go back and try again.', false);
    }
    if (isset($_POST['catid'])) {
        $catid = $tigers->cleanMys((int)$_POST['catid']);
    }
    $subject = $tigers->cleanMys($_POST['subject']);
    if (empty($subject)) {
        $tigers->displayError('Form Error', 'Your <samp>subject</samp> field is empty.', false);
    }
    $url = $tigers->cleanMys($_POST['url']);
    if (empty($url)) {
        $tigers->displayError('Form Error', 'Your <samp>url</samp> field is empty.', false);
    }
    $status = $tigers->cleanMys($_POST['status']);
    if (!is_numeric($status) || $status > 1 || strlen($status) > 1) {
        $tigers->displayError('Form Error', 'Your <samp>status</samp> field' .
            ' is empty.', false);
    }
    $change = $tigers->cleanMys($_POST['change']);
    $changeArray = array('add', 'edit', 'delete', 'none');
    if (!in_array($change, $changeArray)) {
        $tigers->displayError('Form Error', 'You can only add, edit and delete an image.', false);
    }
    $image_tag = substr(sha1(date('YmdHis')), random_int(0, 8), 15);
    $image = $_FILES['image'];
    if ($change == 'add' || $change == 'edit') {
        $imageinfo = getimagesize($_FILES['image']['tmp_name']);
        $imagetype = $imageinfo[2];
        if ($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
            $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' .
                ' <samp>.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
        }
    }
    $madeby = $tigers->cleanMys($_POST['madeby']);
    if (!empty($madeby) && in_array($madeby, array('y', 'n'))) {
        $mb = $madeby;
    } else {
        $mb = 'n';
    }
    $category = $_POST['category'];
    if (empty($category)) {
        $tigers->displayError('Form Error', 'Your <samp>category</samp> field' .
            ' is empty.', false);
    }
    $category = array_map(array($tigers, 'cleanMys'), $category);

    if ($change != 'none' && $change != 'add') {
        $sImage = $dragons->pullImage_Joined($id);
        $dImage = $seahorses->getOption('jnd_path') . $sImage;

        if ($change == 'delete' || $change == 'edit') {
            if (!empty($sImage) && file_exists($dImage)) {
                $delete = @unlink($dImage);
            }
        }
    }

    $jnd_path = $seahorses->getOption('jnd_path');
    if (!empty($jnd_path)) {
        $path = $jnd_path;
    } else {
        $path = $_SERVER['SCRIPT_FILENAME'];
        $path = str_replace('joined.php', '', $path);
    }

    $e = file_exists($path . $image['name']) ? $image_tag . '_' : '';
    $file = $scorpions->escape($e . $image['name']);
    if ($change == 'add' || $change == 'edit') {
        if ($change != 'delete' && $change != 'none') {
            $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
        }
    }

    $cat = implode('|', $category);
    $cat = '|' . trim($cat, '|') . '|';

    $update = "UPDATE `$_ST[joined]` SET `jSubject` = '$subject', `jURL` = '$url'," .
        " `jCategory` = '$cat',";
    if ($change == 'add' || $change == 'edit') {
        $update .= " `jImage` = '$file',";
    }
    $update .= " `jMade` = '$mb', `jStatus` = '$status'";
    if ($_POST['datenow'] == 'yes') {
        $update .= ', `jAdd` = CURDATE()';
    }
    $update .= " WHERE `jID` = '$id' LIMIT 1";
    $scorpions->query("SET NAMES 'utf8';");
    $true = $scorpions->query($update);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to update' .
            ' the joined listing.|Make sure your ID is not empty and your joined table' .
            ' exists.', true, $update);
    } elseif ($true == true) {
        echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
            " joined listing was updated!</p>\n";
        if (isset($delete, $success)) {
            if ($delete && $success) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                    " old image was deleted and replaced with a new one!</p>\n";
            }
        } elseif (isset($delete) && !isset($success)) {
            if ($delete) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                    " old image was deleted!</p>\n";
            }
        } elseif (!isset($delete) && isset($success)) {
            if ($success) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
                    " image was uploaded!</p>\n";
            }
        }
        if (!empty($catid) && is_numeric($catid)) {
            echo $tigers->backLink('joined', 'n', $catid);
        } else {
            echo $tigers->backLink('joined', $id);
            echo $tigers->backLink('joined');
        }
    }
} /**
 * @section   Delete Joined Listing
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
    $id = $tigers->cleanMys($_GET['d']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
            ' selected an incorrect listing or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }

    $select = "SELECT * FROM `$_ST[joined]` WHERE `jID` = '$id' LIMIT 1";
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to select' .
            ' that specific joined listing.|Make sure the ID is not empty and the joined' .
            ' table exists.', true, $select);
    }
    $getItem = $scorpions->obj($true);
    ?>
    <p>You are about to delete the <strong><?php echo $getItem->jSubject; ?></strong>
        joined listing; please be aware that once you delete a joined listing, it is
        gone forever. <em>This cannot be undone!</em> To proceed, click the "Delete
        Joined" button!</p>

    <form action="joined.php" method="post">
        <input name="id" type="hidden" value="<?php echo $getItem->jID; ?>">

        <fieldset>
            <legend>Delete Joined</legend>
            <p class="tc">Deleting <strong><?php echo $getItem->jSubject; ?></strong><br>
                <input name="action" class="input2" type="submit" value="Delete Joined"></p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Joined') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Form Error', 'Your ID is empty. This means' .
            ' you selected an incorrect joined listing or you\'re trying to access' .
            ' something that doesn\'t exist. Go back and try again.', false);
    }

    $sImage = $dragons->pullImage_Joined($id);
    $dImage = $seahorses->getOption('jnd_path') . $sImage;
    if (!empty($sImage) && file_exists($dImage)) {
        $remove = @unlink($dImage);
    }

    $delete = "DELETE FROM `$_ST[joined]` WHERE `jID` = '$id' LIMIT 1";
    $true = $scorpions->query($delete);

    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to delete' .
            ' the joined listing.', true, $delete);
    } elseif ($true == true) {
        echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
            " joined listing was deleted!</p>\n";
    }
    echo $tigers->backLink('joined');
} /**
 * Mass-approve joined listings!
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'Approve') {
    if (empty($_POST['joined'])) {
        $tigers->displayError('Form Error', 'You need to select a joined listing' .
            ' (or two, etc.) in order to approve them.', false);
    }

    foreach ($_POST['joined'] as $pm) {
        $joinedid = $tigers->cleanMys($pm);
        $joined = $dragons->getJoined($joinedid);
        $delete = "UPDATE `$_ST[joined]` SET `jStatus` = '0' WHERE `jID`" .
            " = '$joinedid' LIMIT 1";
        $true = $scorpions->query($delete);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to approve' .
                ' the joined listing.', true, $delete);
        } elseif ($true == true) {
            echo $tigers->displaySuccess('The <samp>' . $joined->jSubject .
                '</samp> joined listing was approved!');
        }
    }
    echo $tigers->backLink('joined');
} /**
 * Mass-delete joined listings!
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
    if (empty($_POST['joined'])) {
        $tigers->displayError('Form Error', 'You need to select a joined listing' .
            ' (or two, etc.) in order to delete them.', false);
    }

    foreach ($_POST['joined'] as $pm) {
        $joined = $tigers->cleanMys($pm);
        $sImage = $dragons->pullImage_Joined($joined);
        $dImage = $seahorses->getOption('jnd_path') . $sImage;
        if (!empty($sImage) && file_exists($dImage)) {
            $remove = @unlink($dImage);
        }

        $delete = "DELETE FROM `$_ST[joined]` WHERE `jID` = '$joined' LIMIT 1";
        $true = $scorpions->query($delete);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to delete' .
                ' the joined listing.', true, $delete);
        } elseif ($true == true) {
            echo $tigers->displaySuccess('The joined listing was deleted!');
        }
        echo $tigers->backLink('joined');
    }
} /**
 * Index
 */
else {
?>
<p>Welcome to <samp>joined.php</samp>, the page to add a joined listing and edit
    or delete current ones! Below is the list of your joined listings. To edit or
    delete, click "Edit" or "Delete" by the appropriate listing.</p>

<h3>Search Joined</h3>
<form action="joined.php" method="get">
    <input name="g" type="hidden" value="searchJoined">

    <fieldset>
        <legend>Search Joined</legend>
        <p><label>Subject:</label> <input name="s" class="input1" type="text"></p>
        <p><label>Category:</label> <select name="c" class="input1">
                <?php
                $categories = $lions->categoryList();
                echo "<option value=\"0\">Choose</option>\n";
                foreach ($categories as $cat) {
                    $c = $lions->getCategory($cat);
                    $n = $c->parent == 0 ? $c->catname : $lions->getCatName($c->parent) .
                        ' &#187; ' . $c->catname;
                    echo '<option value="' . $c->catid . "\">$n</option>\n";
                }
                ?>
            </select></p>
        <p class="tc"><input class="input2" type="submit" value="Search Joined"></p>
    </fieldset>
</form>
<?php
if (isset($_GET['g']) && $_GET['g'] == 'searchJoined') {
    if (isset($_GET['s']) && !empty($_GET['s'])) {
        $s = $tigers->cleanMys($_GET['s']);
    }
    if (isset($_GET['c']) && $_GET['c'] != 'none') {
        $c = $tigers->cleanMys($_GET['c']);
    }
    $q = '';
    $b = '';
    if (
        (isset($_GET['s']) && !empty($_GET['s'])) ||
        (isset($_GET['c']) && $_GET['c'] != 'none')
    ) {
        $a = array();
        if (isset($_GET['s']) && !empty($_GET['s']) && $_GET['s'] != '') {
            $a['searchSubject'] = $s;
        }
        if (isset($_GET['c']) && $_GET['c'] != 'none') {
            $a['searchCategory'] = $c;
        }
        $q = 'joined';
        $b = $a;
    }
} else {
    $q = '';
    $b = '';
}
$select = $dragons->sortJoined($q, $b);
$count = is_countable($select) ? count($select) : 0;

if ($count > 0) {
if (isset($s)) {
    $e = isset($_GET['s']) ? " for <strong>$s</strong>..." : '...';
    echo '<h4>Searching for in the <em>' . $lions->getCatName($c) .
        "</em> category$e</h4>\n";
}
if ($ender > $count) {
    $ender = $count;
}
?>
<form action="joined.php" method="post">
    <table class="index">
        <thead>
        <tr>
            <th>&#160;</th>
            <th>Subject</th>
            <th>Image</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td class="tc" colspan="5">With Checked:
                <input name="action" class="input2" type="submit" value="Approve">
                <input name="action" class="input2" type="submit" value="Delete">
            </td>
        </tr>
        </tfoot>
        <?php
        while ($start < $ender) {
            $u = $select[$start];
            $getItem = $dragons->getJoined($u['jID']);
            $qw = isset($_GET['c']) ? '&#38;c=' . $c . '&#38;' : '';
            $mg = !empty($getItem->jImage) && file_exists($seahorses->getOption('jnd_path')) ?
                '<img src="' . $seahorses->getOption('jnd_http') . $getItem->jImage . '" alt="" />' : '';

            /**
             * Get subject wrapper for jQuery
             */
            $sc = strlen($getItem->jSubject) > 15 ? substr($getItem->jSubject, 0, 15) .
                '<span>...</span>' : $getItem->jSubject;
            $sb = '<a href="' . $getItem->jURL . '">' . $getItem->jSubject . '</a>';
            ?>
            <tbody>
            <tr>
                <td class="tc"><input name="joined[]" type="checkbox" value="<?php echo $getItem->jID; ?>"></td>
                <td class="th" id="tro<?php echo $getItem->jID; ?>">
                    <p class="top"><?php echo $sc; ?></p>
                    <p class="bottom" style="display: none;"><?php echo $sb; ?></p>
                </td>
                <td class="tc"><?php echo $mg; ?></td>
                <td class="tc"><?php echo $lions->pullCatNames($getItem->jCategory, '|'); ?></td>
                <td class="floatIcons tc">
                    <a href="joined.php?g=old<?php echo $qw; ?>&#38;d=<?php echo $getItem->jID; ?>">
                        <img src="img/icons/edit.png" alt="">
                    </a>
                    <a href="joined.php?g=erase&#38;d=<?php echo $getItem->jID; ?>">
                        <img src="img/icons/delete.png" alt="">
                    </a>
                </td>
            </tr>
            </tbody>
            <?php
            $start++;
        }
        echo "</table>\n";

        $v = isset($_GET['c']) ? $dragons->joinedList('id', $c) : $dragons->joinedList();
        echo '<p id="pagination">';
        $pages = ceil((is_countable($v) ? count($v) : 0) / $per_joined);
        $dragons->paginate($pages);
        echo "</p>\n";
        }

        else {
            echo "\n<p class=\"tc\">Currently no joined listings!</p>\n";
        }
        }

        require('footer.php');
