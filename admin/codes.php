<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <codes.php>
 * @version          Robotess Fork
 */

error_reporting(E_ALL);
$getTitle = 'Codes';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if (!isset($_GET['page']) || empty($_GET['page']) || !is_numeric($_GET['page'])) {
    $page = 1;
} else {
    $page = $tigers->cleanMys((int)$_GET['page']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if ($seahorses->getOption('codes_opt') == 'y') {

if (
isset($_GET['listing']) &&
($_GET['listing'] == '0' || in_array($_GET['listing'], $wolves->listingsList()))
) {
$listing = $tigers->cleanMys((int)$_GET['listing']);

if (isset($_GET['g']) && $_GET['g'] == 'new') {
    $id = (int)$tigers->cleanMys((int)$_GET['d']) + 1;
    ?>
    <form action="codes.php?listing=<?php echo $listing; ?>" enctype="multipart/form-data" method="post">

        <div id="codesFloatRight">
            <h4>Set All</h4>
            <fieldset class="setChoice">
                <legend>Set All As</legend>
                <p class="noteButton">You can set the following values for all fields to the
                    left (minus the file and code title).</p>
                <p><label><strong>Size:</strong></label> <select name="size_all" class="input1" id="getSize">
                        <?php
                        $select = "SELECT * FROM `$_ST[codes_sizes]` ORDER BY `sOrder`, `sName` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>No Sizes Available</option>\n";
                        } else {
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->sID . '">' . $getItem->sName . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
                <p><label><strong>Category:</strong></label>
                    <select name="category_all" class="input1" id="getCategory">
                        <?php
                        $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `fNiq` LIKE '%!$listing!%'" .
                            " AND `catParent` = '0' ORDER BY `catName` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>No Categories Available</option>\n";
                        } else if ($scorpions->obj($true) > 0) {
                            echo "  <option selected=\"selected\" value=\"0\">None</option>\n";
                            while ($getItem = $scorpions->obj($true)) {
                                $catid = $getItem->catID;
                                echo '  <option value="' . $getItem->catID . '">' . $getItem->catName .
                                    "</option>\n";
                                $pull = $scorpions->query("SELECT * FROM `$_ST[codes_categories]` WHERE" .
                                    " `catParent` = '$catid' ORDER BY `catName`");
                                while ($items = $scorpions->obj($pull)) {
                                    echo '  <option value="' . $items->catID . '">&raquo; ' . $items->catName .
                                        "</option>\n";
                                }
                            }
                        } else {
                            echo "  <option selected=\"selected\" value=\"0\">None</option>\n";
                        }
                        ?>
                    </select></p>
                <p><label><strong>Donor:</strong></label>
                    <select name="donor_all" class="input1" id="getDonor">
                        <?php
                        $select = "SELECT * FROM `$_ST[codes_donors]` ORDER BY `dName` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>No Donors Available</option>\n";
                        } else if ($scorpions->total($true) > 0) {
                            echo "  <option value=\"0\">None</option>\n";
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->dID . '">' . $getItem->dName;
                                if (empty($getItem->dURL)) {
                                    echo '';
                                } else {
                                    echo ' (' . $octopus->shortURL($getItem->dURL) . ')';
                                }
                                echo "</option>\n";
                            }
                        } else {
                            echo "  <option value=\"0\">None</option>\n";
                        }
                        ?>
                    </select></p>
            </fieldset>
        </div>

        <div id="codesFloatLeft">
            <?php
            for ($i = 1; $i < $id; $i++) {
                ?>
                <fieldset>
                    <legend>Details</legend>
                    <input name="numeric[]" type="hidden" value="<?php echo $i; ?>">
                    <p><label><strong>Name:</strong></label>
                        <input name="name[]" class="input1" type="text"></p>
                    <p><label><strong>File:</strong></label>
                        <input name="image[]" class="input1" type="file"></p>
                    <p><label><strong>Size:</strong></label>
                        <select name="size[]" class="input1 setSize">
                            <?php
                            $select = "SELECT * FROM `$_ST[codes_sizes]` ORDER BY `sOrder`, `sName` ASC";
                            $true = $scorpions->query($select);
                            if ($true == false) {
                                echo "  <option>No Sizes Available</option>\n";
                            } else {
                                while ($getItem = $scorpions->obj($true)) {
                                    echo '  <option value="' . $getItem->sID . '">' . $getItem->sName . "</option>\n";
                                }
                            }
                            ?>
                        </select></p>
                    <p><label><strong>Category:</strong></label>
                        <select name="category[]" class="input1 setCategory">
                            <?php
                            $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `fNiq` LIKE '%!$listing!%'" .
                                " AND `catParent` = '0' ORDER BY `catName` ASC";
                            $true = $scorpions->query($select);
                            if ($true == false) {
                                echo "  <option>No Categories Available</option>\n";
                            } else if ($scorpions->total($true) > 0) {
                                echo "  <option value=\"0\">None</option>\n";
                                while ($getItem = $scorpions->obj($true)) {
                                    $catid = $getItem->catID;
                                    echo '  <option value="' . $getItem->catID . '">' . $getItem->catName .
                                        "</option>\n";
                                    $pull = $scorpions->query("SELECT * FROM `$_ST[codes_categories]` WHERE" .
                                        " `catParent` = '$catid' ORDER BY `catName`");
                                    while ($items = $scorpions->obj($pull)) {
                                        echo '  <option value="' . $items->catID . '">&#187; ' . $items->catName .
                                            "</option>\n";
                                    }
                                }
                            } else {
                                echo "  <option value=\"0\">None</option>\n";
                            }
                            ?>
                        </select></p>
                    <p><label><strong>Donor:</strong></label>
                        <select name="donor[]" class="input1 setDonor">
                            <?php
                            $select = "SELECT * FROM `$_ST[codes_donors]` ORDER BY `dName` ASC";
                            $true = $scorpions->query($select);
                            if ($true == false) {
                                echo "<option>No Donors Available</option>\n";
                            } else if ($scorpions->total($true) > 0) {
                                echo "  <option value=\"0\">None</option>\n";
                                while ($getItem = $scorpions->obj($true)) {
                                    echo '  <option value="' . $getItem->dID . '">' . $getItem->dName;
                                    if (empty($getItem->dURL)) {
                                        echo '';
                                    } else {
                                        echo ' (' . $octopus->shortURL($getItem->dURL) . ')';
                                    }
                                    echo "</option>\n";
                                }
                            } else {
                                echo "  <option value=\"0\">None</option>\n";
                            }
                            ?>
                        </select></p>
                </fieldset>
                <?php
            }
            ?>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc">
                    <input name="action" class="input2" type="submit" value="Add Code(s)">
                    <input class="input2" type="reset" value="Reset">
                </p>
            </fieldset>
        </div>
    </form>
    <?php
} elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
    $id = $tigers->cleanMys($_GET['d']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Form Error', 'Your ID is empty. This means you' .
            ' selected an incorrect code or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }

    $select = "SELECT * FROM `$_ST[codes]` WHERE `cID` = '$id' LIMIT 1";
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'Unable to select that specific' .
            ' listing.|Make sure the ID is not empty and the listings table exists.',
            true, $select);
    }
    $getItem = $scorpions->obj($true);
    ?>
    <form action="codes.php?listing=<?php echo $listing; ?>" enctype="multipart/form-data" method="post">
        <input name="id" type="hidden" value="<?php echo $getItem->cID; ?>">

        <fieldset>
            <legend>Details</legend>
            <p><label><strong>Name:</strong></label>
                <input name="name" class="input1" type="text" value="<?php echo $getItem->cName; ?>"></p>
            <p><label><strong>Size:</strong></label> <select name="size" class="input1">
                    <?php
                    $select = "SELECT * FROM `$_ST[codes_sizes]` ORDER BY `sOrder`, `sName` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option>No Sizes Available</option>\n";
                    } else {
                        while ($getSize = $scorpions->obj($true)) {
                            echo '  <option value="' . $getSize->sID . '"';
                            if (in_array($getSize->sID, explode('!', $getItem->cSize))) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $getSize->sName . "</option>\n";
                        }
                    }
                    ?>
                </select></p>
            <p><label><strong>Category:</strong></label>
                <select name="category" class="input1" size="7">
                    <?php
                    $select = "SELECT * FROM `$_ST[codes_categories]` WHERE `fNiq` LIKE" .
                        " '%!$listing!%' AND `catParent` = '0' ORDER BY `catName` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option>No Categories Available</option>\n";
                    } else if ($scorpions->total($true) > 0) {
                        echo "  <option value=\"0\">None</option>\n";
                        while ($getCat = $scorpions->obj($true)) {
                            $catid = $getCat->catID;
                            echo '  <option value="' . $getCat->catID . '"';
                            if (in_array($getCat->catID, explode('!', $getItem->cCategory))) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $getCat->catName . "</option>\n";
                            $pull = $scorpions->query("SELECT * FROM `$_ST[codes_categories]` WHERE" .
                                " `catParent` = '$catid' ORDER BY `catName`");
                            while ($items = $scorpions->obj($pull)) {
                                echo '  <option value="' . $items->catID . '"';
                                if (in_array($getCat->catID, explode('!', $getItem->cCategory))) {
                                    echo ' selected="selected"';
                                }
                                echo '>&#187; ' . $items->catName . "</option>\n";
                            }
                        }
                    } else {
                        echo "  <option selected=\"selected\" value=\"0\">None</option>\n";
                    }
                    ?>
                </select></p>
            <p><label><strong>Donor:</strong></label> <select name="donor" class="input1">
                    <?php
                    $select = "SELECT * FROM `$_ST[codes_donors]` ORDER BY `dName` ASC";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        echo "  <option>No Donors Available</option>\n";
                    } else if ($scorpions->total($true) > 0) {
                        echo "  <option value=\"0\">None</option>\n";
                        while ($getDonor = $scorpions->obj($true)) {
                            echo '  <option value="' . $getDonor->dID . '"';
                            if (in_array($getDonor->dID, explode('!', $getItem->cDonor))) {
                                echo ' selected="selected"';
                            }
                            echo '>' . $getDonor->dName;
                            if (empty($getDonor->dURL)) {
                                echo '';
                            } else {
                                echo ' (' . $octopus->shortURL($getDonor->dURL) . ')';
                            }
                            echo "</option>\n";
                        }
                    } else {
                        echo "  <option value=\"0\">None</option>\n";
                    }
                    ?>
                </select></p>
        </fieldset>

        <fieldset>
            <legend>Image</legend>
            <?php
            $img = $seahorses->getOption('codes_img_path') . $getItem->cFile;
            if (!empty($getItem->cFile) && file_exists($img)) {
                ?>
                <p class="tc"><img src="<?php echo $seahorses->getOption('codes_img_http') . $getItem->cFile; ?>"
                                   alt=""></p>
                <?php
            }
            ?>
            <p><label><strong>Changes:</strong></label>
                <input name="change" class="input3" type="radio" value="edit"> Edit
                <input name="change" checked="checked" class="input3" type="radio" value="none"> No Change</p>
            <p><label><strong>New Image:</strong></label> <input name="image" class="input1" type="file"></p>
        </fieldset>

        <fieldset>
            <legend>Submit</legend>
            <p class="tc">
                <input name="action" class="input2" type="submit" value="Edit Code">
                <input class="input2" type="reset" value="Reset">
            </p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action'])) {
    if (isset($_POST['action']) && $_POST['action'] == 'Add Code(s)') {
        foreach ($_POST['numeric'] as $field => $value) {
            if (!empty($_FILES['image']['name'][$field])) {
                $name = $tigers->cleanMys($_POST['name'][$field]);
                $image_tag = substr(sha1(date('YmdHis')), 0, 7) . substr(sha1(random_int(0, mt_getrandmax())), 0, 7);
                if (
                    $_FILES['image']['type'][$field] != 'image/jpeg' &&
                    $_FILES['image']['type'][$field] != 'image/png' &&
                    $_FILES['image']['type'][$field] != 'image/gif'
                ) {
                    $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' .
                        ' <samp>.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
                }
                if (!empty($_POST['category'][$field]) && $_POST['category'][$field] != 0) {
                    $category = $tigers->cleanMys((int)$_POST['category'][$field]);
                    $cat = $category;
                } else {
                    $cat = '0';
                }
                $size = $tigers->cleanMys((int)$_POST['size'][$field]);
                $donor = $tigers->cleanMys((int)$_POST['donor'][$field]);

                $img_path = $seahorses->getOption('codes_img_path');
                if (!empty($img_path) || is_dir($img_path)) {
                    $path = $seahorses->getOption('codes_img_path');
                } else {
                    $path = str_replace('codes.php', '', $_SERVER['SCRIPT_FILENAME']);
                }

                $s = file_exists($path . $_FILES['image']['name'][$field]) ? $image_tag . '_' : '';
                $file = $scorpions->escape($s . $_FILES['image']['name'][$field]);
                echo "Path: $path<br>Image file name: $s<br>Escape image file name: $file";
                $success = move_uploaded_file($_FILES['image']['tmp_name'][$field], $path . $file);

                $insert = "INSERT INTO `$_ST[codes]` (`fNiq`, `cName`, `cFile`, `cCategory`," .
                    " `cSize`, `cDonor`, `cPending`, `cAdded`) VALUES ('$listing', '$name'," .
                    " '$file', '$cat', '$size', '$donor', '0', NOW())";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);

                if ($true == false) {
                    $tigers->displayError('Database Error', 'The script was unable to add' .
                        ' the <strong>' . $file . '</strong> code to the database.|Make sure your' .
                        ' table exists.', true, $insert);
                } elseif ($true == true) {
                    echo $tigers->displaySuccess('Your <samp>' . $file . '</samp> code was' .
                        ' added to the database! :D');
                }
            }
        }
        echo $tigers->backLink('codes', $listing);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Code') {
        $id = $tigers->cleanMys($_POST['id']);
        if (empty($id) || !is_numeric($id)) {
            $tigers->displayError('Form Error', 'Your ID is empty. This means' .
                ' you selected an incorrect code or you\'re trying to access something' .
                ' that doesn\'t exist. Go back and try again.', false);
        }
        $name = $tigers->cleanMys($_POST['name']);
        $change = $tigers->cleanMys($_POST['change']);
        $changea = array('edit', 'none');
        if (!in_array($change, $changea)) {
            $tigers->displayError('Form Error', 'You can only add, edit and' .
                ' delete an image.', false);
        }
        $image_tag = substr(sha1(date('YmdHis')), 0, 7) . substr(sha1(random_int(0, mt_getrandmax())), 0, 7);
        if (($change == 'edit') && $_FILES['image']['type'] != 'image/jpeg' &&
            $_FILES['image']['type'] != 'image/png' &&
            $_FILES['image']['type'] != 'image/gif') {
            $tigers->displayError('Form Error', 'Only <samp>.gif</samp>,' .
                ' <samp>.jpg</samp> and <samp>.png</samp> extensions allowed.', false);
        }
        if (!empty($_POST['category']) && $_POST['category'] != 0) {
            $category = $tigers->cleanMys((int)$_POST['category']);
            $cat = $category;
        } else {
            $cat = '0';
        }
        $size = $tigers->cleanMys((int)$_POST['size']);
        $donor = $tigers->cleanMys((int)$_POST['donor']);

        if ($change != 'none' && $change == 'edit') {
            $sImage = $cheetahs->codeFile($id);
            $dImage = $seahorses->getOption('codes_img_path') . $sImage;
            if (!empty($sImage) && file_exists($dImage)) {
                $delete = @unlink($dImage);
            }
        }

        $img_path = $seahorses->getOption('codes_img_path');
        if (!empty($img_path) || is_dir($img_path)) {
            $path = $seahorses->getOption('codes_img_path');
        } else {
            $path = str_replace('codes.php', '', $_SERVER['SCRIPT_FILENAME']);
        }

        $s = file_exists($path . $_FILES['image']['name']) ? $image_tag . '_' : '';
        $file = $scorpions->escape($s . $_FILES['image']['name']);
        if ($change == 'add' || $change == 'edit') {
            if ($change != 'delete' && $change != 'none') {
                $success = @move_uploaded_file($_FILES['image']['tmp_name'], $path . $file);
            }
        }

        $update = "UPDATE `$_ST[codes]` SET `fNiq` = '$listing', `cName` = '$name',";
        if ($change == 'edit') {
            $update .= " `cFile` = '$file',";
        }
        $update .= " `cCategory` = '$cat', `cSize` = '$size', `cDonor` = '$donor'" .
            " WHERE `cID` = '$id' LIMIT 1";
        $scorpions->query("SET NAMES 'utf8';");
        $true = $scorpions->query($update);

        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to edit' .
                ' the <strong>' . $file . '</strong> code.', true, $update);
        } elseif ($true == true) {
            echo $tigers->displaySuccess("Your <samp>$file</samp> code was edited! :D");
            if (isset($delete, $success) && $delete && $success) {
                echo $tigers->displaySuccess('Your old code image was deleted and replaced' .
                    ' with a new one');
            }
        }
        echo $tigers->backLink('codes', $listing);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'Approve') {
        if (empty($_POST['code'])) {
            $tigers->displayError('Form Error', 'You need to select a code' .
                ' (or two, etc.) in order to approve them.', false);
        }

        foreach ($_POST['code'] as $pm) {
            $update = "UPDATE `$_ST[codes]` SET `cPending` = '0', `cAdded` = NOW()" .
                " WHERE `cID` = '$pm' LIMIT 1";
            $true = $scorpions->query($update);
            if ($true == true) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span>' .
                    " The code was approve! :D</p>\n";
            }
        }
        echo $tigers->backLink('codes', $listing);
    } elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
        if (empty($_POST['code'])) {
            $tigers->displayError('Form Error', 'You need to select a code (or' .
                ' two, etc.) in order to delete them.', false);
        }

        foreach ($_POST['code'] as $pm) {
            $sImage = $cheetahs->codeFile($pm);
            $dImage = $seahorses->getOption('codes_img_path') . $sImage;
            if (!empty($sImage) && file_exists($dImage)) {
                $deleteImage = @unlink($dImage);
            }

            $delete = "DELETE FROM `$_ST[codes]` WHERE `cID` = '$pm' LIMIT 1";
            $true = $scorpions->query($delete);
            if ($true == true) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span>' .
                    " The code was deleted from the database!</p>\n";
            }
            if ($deleteImage) {
                echo '<p class="successButton"><span class="success">SUCCESS!</span>' .
                    " The code image was deleted!</p>\n";
            }
        }
        echo $tigers->backLink('codes', $listing);
    }
}

else {
$typea = $listing == 0 ? '' : ' listing';
$typeb = $listing == 0 ? 'collective' : 'listing';
$codesNumb = $cheetahs->codeCount($listing);
echo "<h3>Viewing <em>$codesNumb</em> codes from the " .
    $wolves->getSubject($listing) . "$typea...</h3>\n";
?>
<form action="codes.php?listing=<?php echo $listing; ?>" method="get">
    <input name="listing" type="hidden" value="<?php echo $listing; ?>">
    <input name="g" type="hidden" value="new">

    <fieldset>
        <legend>Add Codes</legend>
        <p><label><strong>Number of Codes:</strong></label> <select name="d" class="input1">
                <option>1</option>
                <option>2</option>
                <option>3</option>
                <option>4</option>
                <option>5</option>
                <option>6</option>
                <option>7</option>
                <option>8</option>
                <option>9</option>
                <option>10</option>
            </select></p>
        <p class="tc"><input type="submit" value="Add Listings"></p>
    </fieldset>
</form>
<?php
$o = $seahorses->getOption('codes_order');
$select = "SELECT * FROM `$_ST[codes]` WHERE `fNiq` = '$listing' ORDER BY" .
    " `cAdded` $o LIMIT $start, $per_page";
$true = $scorpions->query($select);
if ($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to select' .
        ' the codes from the specified listing.|Make sure your codes tables exists.',
        true, $select);
}
$count = $scorpions->total($true);

if ($count > 0) {
?>

<form action="codes.php?listing=<?php echo $listing; ?>" method="post">
    <table class="index">
        <thead>
        <tr>
            <th>&#160;</th>
            <th>Image</th>
            <th>Status</th>
            <th>Size</th>
            <th>Category</th>
            <th>Donor</th>
            <th>Action</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td class="tc" colspan="7">With Checked:
                <input name="action" class="input2" type="submit" value="Approve">
                <input name="action" class="input2" type="submit" value="Delete">
            </td>
        </tr>
        </tfoot>
        <?php
        while ($getItem = $scorpions->obj($true)) {
            if ($getItem->cCategory != 0) {
                $c = $cheetahs->getCodeCategory($getItem->cCategory);
                $x = !empty($c->catParent) ? $cheetahs->getCodesCatName($c->catParent) .
                    ' &#187; ' . $c->catName : $c->catName;
            } else {
                $x = '&#8211;';
            }

            $codestatus = $getItem->cPending == 1 ? 'Pending' : 'Approved';
            ?>
            <tbody>
            <tr>
                <td class="tc"><input name="code[]" type="checkbox" value="<?php echo $getItem->cID; ?>"></td>
                <td class="tc"><img src="<?php echo $seahorses->getOption('codes_img_http') . $getItem->cFile; ?>"
                                    alt=""></td>
                <td class="tc"><?php echo $codestatus; ?></td>
                <td class="tc"><?php echo $cheetahs->getSize($getItem->cSize); ?></td>
                <td class="tc"><?php echo $x; ?></td>
                <td class="tc"><?php echo $cheetahs->getDonor($getItem->cDonor); ?></td>
                <td class="floatIcons tc">
                    <a href="codes.php?listing=<?php echo $getItem->fNiq; ?>&#38;g=old&#38;d=<?php echo $getItem->cID; ?>">
                        <img src="img/icons/edit.png" alt="">
                    </a>
                    <a href="codes.php?listing=<?php echo $getItem->fNiq; ?>&#38;g=erase&#38;d=<?php echo $getItem->cID; ?>">
                        <img src="img/icons/delete.png" alt="">
                    </a>
            </tr>
            </tbody>
            <?php
        }
        echo "</table>\n</form>\n\n";

        echo "\n<p id=\"pagination\">Pages: ";
        $total = is_countable($cheetahs->codesList('listing', $tigers->cleanMys($_GET['listing']))) ? count($cheetahs->codesList('listing', $tigers->cleanMys($_GET['listing']))) : 0;
        $pages = ceil($total / $per_page);

        for ($i = 1; $i <= $pages; $i++) {
            if ($page == $i) {
                echo $i . ' ';
            } else {
                echo '<a href="codes.php?listing=' . $listing . '&#38;page=' . $i .
                    '">' . $i . '</a> ';
            }
        }

        echo "</p>\n";
        } else {
            echo "<p class=\"tc\">Currently no codes for this $typeb!</p>\n";
        }
        }
        }

        else {
        $countListings = is_countable($wolves->listingsList()) ? count($wolves->listingsList()) : 0;
        $select = "SELECT * FROM `$_ST[main]` ORDER BY `subject` ASC LIMIT $countListings";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to select the' .
                ' listings from the database.', true, $select);
        }
        $count = $scorpions->total($true);

        if ($count > 0) {
        $codesNumb = $cheetahs->codeCount('n');
        echo "<h3>Viewing <em>$codesNumb</em> codes from all listings...</h3>\n";
        ?>

        <table class="index">
            <thead>
            <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Codes</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody class="collective">
            <tr>
                <td class="tc" colspan="2">Whole Collective</td>
                <td class="tc"><?php echo is_countable($cheetahs->codesList('listing', '0')) ? count($cheetahs->codesList('listing', '0')) : 0; ?></td>
                <td class="tc"><a href="codes.php?listing=0">Manage Codes</a></td>
            </tr>
            </tbody>
            <?php
            while ($getItem = $scorpions->obj($true)) {
                ?>
                <tbody>
                <tr>
                    <td class="tc"><?php echo $getItem->id; ?></td>
                    <td class="tc"><?php echo $getItem->subject; ?></td>
                    <td class="tc"><?php echo is_countable($cheetahs->codesList('listing', $getItem->id)) ? count($cheetahs->codesList('listing', $getItem->id)) : 0; ?></td>
                    <td class="tc"><a href="codes.php?listing=<?php echo $getItem->id; ?>">Manage Codes</a></td>
                </tr>
                </tbody>
                <?php
            }
            echo "</table>\n\n";
            } else {
                echo "\n<p class=\"tc\">Currently no listings to list!</p>\n";
            }
            }
            }

            else {
                ?>
                <p class="errorButton"><span class="error">ERROR:</span> You have turned off the
                    <samp>codes</samp> feature. To turn it on this feature, visit the
                    <a href="addons.php">&#187; addons page</a> to install it!</p>
                <?php
            }

            require('footer.php');
