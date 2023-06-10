<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <codes-donors.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;

$getTitle = 'Codes: Donors';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

$sp = !isset($_GET['g']) ? '<span><a href="codes-donors.php?g=new">Add' .
    ' Code Donor</a></span>' : '';
echo "<h2>{$getTitle}$sp</h2>\n";

if (!isset($_GET['p']) || empty($_GET['p']) || !is_numeric($_GET['p'])) {
    $page = 1;
} else {
    $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if (isset($_GET['g']) && $_GET['g'] == 'new') {
    ?>
    <form action="codes-donors.php" method="post">
        <fieldset>
            <legend>Add Donor</legend>
            <p><label><strong>Name:</strong></label>
                <input name="name" class="input1" type="text"></p>
            <p><label><strong>E-mail:</strong></label>
                <input name="email" class="input1" type="email"></p>
            <p><label><strong>URL:</strong></label>
                <input name="url" class="input1" type="url"></p>
            <p class="tc">
                <input name="action" class="input2" type="submit" value="Add Donor">
                <input class="input2" type="reset" value="Reset">
            </p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Add Donor') {
    $name = $tigers->cleanMys($_POST['name']);
    if (empty($name)) {
        $tigers->displayError('Form Error', 'The <samp>name</samp> field is empty.',
            false);
    }
    $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
    if (!empty($email) && !StringUtils::instance()->isEmailValid($email)) {
        $tigers->displayError('Form Error', 'The <samp>e-mail</samp> field contains' .
            ' invalid characters. Try again.', false);
    }
    $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
    if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
        $tigers->displayError('Form Error', 'The <samp>URL</samp> field does not' .
            ' start with http:// and therefore is not valid. Try again.', false);
    }

    $insert = "INSERT INTO `$_ST[codes_donors]` (`dName`, `dEmail`, `dURL`," .
        " `dPending`, `dUpdated`, `dAdded`) VALUES ('$name', '$email', '$url', 0," .
        " '1970-01-01 00:00:00', NOW())";
    $scorpions->query("SET NAMES 'utf8';");
    $true = $scorpions->query($insert);

    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to add the' .
            ' donor to the database.', true, $insert);
    } elseif ($true == true) {
        echo $tigers->displaySuccess('The donor was added to the database!');
        echo $tigers->backLink('codes_donors');
    }
} /**
 * Edit
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'old') {
    $id = $tigers->cleanMys($_GET['d']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
            ' selected an incorrect donor or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }

    $select = "SELECT * FROM `$_ST[codes_donors]` WHERE `dID` = '$id' LIMIT 1";
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to select the' .
            ' donor from the specified ID.', true, $select);
    }
    $getItem = $scorpions->obj($true);
    ?>
    <form action="codes-donors.php" method="post">
        <p class="noMargin"><input name="id" type="hidden" value="<?php echo $getItem->dID; ?>"></p>

        <fieldset>
            <legend>Edit Donor</legend>
            <p><label><strong>Name:</strong></label>
                <input name="name" class="input1" type="text" value="<?php echo $getItem->dName; ?>"></p>
            <p><label><strong>E-mail:</strong></label>
                <input name="email" class="input1" type="email" value="<?php echo $getItem->dEmail; ?>"></p>
            <p><label><strong>URL:</strong></label>
                <input name="url" class="input1" type="url" value="<?php echo $getItem->dURL; ?>"></p>
            <p class="tc">
                <input name="action" class="input2" type="submit" value="Edit Donor">
            </p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Donor') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
            ' selected an incorrect donor or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }
    $name = $tigers->cleanMys($_POST['name']);
    if (empty($name)) {
        $tigers->displayError('Form Error', 'The <samp>name</samp> field is empty.',
            false);
    }

    $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
    if (!empty($email) && !StringUtils::instance()->isEmailValid($email)) {
        $tigers->displayError('Form Error', 'The <samp>e-mail</samp> field contains' .
            ' invalid characters. Try again.', false);
    }
    $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
    if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
        $tigers->displayError('Form Error', 'The <samp>URL</samp> field does not' .
            ' start with http:// and therefore is not valid. Try again.', false);
    }

    $update = "UPDATE `$_ST[codes_donors]` SET `dName` = '$name', `dEmail` = '$email'," .
        " `dURL` = '$url', `dUpdated` = NOW() WHERE `dID` = '$id' LIMIT 1";
    $scorpions->query("SET NAMES 'utf8';");
    $true = $scorpions->query($update);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to edit the' .
            ' donor.', true, $update);
    } elseif ($true == true) {
        echo $tigers->displaySuccess('The donor was edited!');
        echo $tigers->backLink('codes_donors');
    }
} /**
 * Delete
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
    $id = $tigers->cleanMys($_GET['d']);
    if (empty($id) || !is_numeric($id)) {
        $tigers->displayError('Script Error', 'Your ID is empty. This means you' .
            ' selected an incorrect donor or you\'re trying to access something that' .
            ' doesn\'t exist. Go back and try again.', false);
    }

    $select = "SELECT * FROM `$_ST[codes_donors]` WHERE `dID` = '$id' LIMIT 1";
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to select that' .
            ' specific category.', true, $select);
    }
    $getItem = $scorpions->obj($true);
    ?>
    <p>You are about to delete the <strong><?php echo $getItem->dName; ?></strong>
        donor; please be aware that once you delete a donor, it is gone forever.
        <em>This cannot be undone!</em> To proceed, click the "Delete Donor" button. :)</p>

    <form action="codes-donors.php" method="post">
        <input name="id" type="hidden" value="<?php echo $getItem->dID; ?>">

        <fieldset>
            <legend>Delete Donor</legend>
            <p class="tc">
                Deleting <strong><?php echo $getItem->dName; ?></strong><br>
                <input name="action" class="input2" type="submit" value="Delete Donor">
            </p>
        </fieldset>
    </form>
    <?php
} elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Donor') {
    $id = $tigers->cleanMys($_POST['id']);

    $delete = "DELETE FROM `$_ST[codes_donors]` WHERE `dID` = '$id' LIMIT 1";
    $true = $scorpions->query($delete);

    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to delete the' .
            ' donor.', true, $delete);
    } elseif ($true == true) {
        echo $tigers->displaySuccess('Your donor was deleted!');
        echo $tigers->backLink('codes_donors');
    }
} /**
 * Index
 */
else {
?>
<p>Welcome to <samp>codes-donors.php</samp>, the page to add code donors and
    edit or delete your current ones! Below is your list of donors. To edit or
    delete a current one, click "Edit" or "Delete" by the appropriate donor.</p>
<?php
$select = "SELECT * FROM `$_ST[codes_donors]` ORDER BY `dName` ASC LIMIT" .
    " $start, $per_page";
$true = $scorpions->query($select);
$count = $scorpions->total($true);

if ($count > 0) {
?>
<table class="index">
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>URL</th>
        <th>Action</th>
    </tr>
    </thead>
    <?php
    while ($getItem = $scorpions->obj($true)) {
        ?>
        <tbody>
        <tr>
            <td class="tc"><?php echo $getItem->dID; ?></td>
            <td class="tc"><?php echo $getItem->dName; ?></td>
            <td class="tc"><?php echo $getItem->dURL; ?></td>
            <td class="floatIcons tc">
                <a href="codes-donors.php?g=old&#38;d=<?php echo $getItem->dID; ?>">
                    <img src="img/icons/edit.png" alt="">
                </a>
                <a href="codes-donors.php?g=erase&#38;d=<?php echo $getItem->dID; ?>">
                    <img src="img/icons/delete.png" alt="">
                </a>
            </td>
        </tr>
        </tbody>
        <?php
    }
    echo "</table>\n\n<p id=\"pagination\">Pages: ";

    $total = is_countable($cheetahs->donorsList()) ? count($cheetahs->donorsList()) : 0;
    $pages = ceil($total / $per_page);

    for ($i = 1; $i <= $pages; $i++) {
        if ($page == $i) {
            echo $i . ' ';
        } else {
            echo '<a href="codes-donors.php?p=' . $i . '">' . $i . '</a> ';
        }
    }
    echo "</p>\n";
    }

    else {
        echo "<p class=\"tc\">Currently no donors!</p>\n";
    }
    }

    require('footer.php');
