<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <show-codes-form.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;

require('b.inc.php');
require_once('Robotess/StringUtils.php');
require(MAINDIR . 'rats.inc.php');
require_once('fun.inc.php');
require_once('fun-addons.inc.php');
require_once('fun-emails.inc.php');
require_once('fun-external.inc.php');
require_once('fun-listings.inc.php');
require_once('fun-members.inc.php');
require_once('fun-misc.inc.php');
require(MAINDIR . 'vars.inc.php');

/**
 * Get options!
 */
$options = (object)array();

if (
    !isset($fKey) ||
    ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
) {
    $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
} else {
    $options->listingID = $tigers->cleanMys($fKey);
    if ($fKey == 0 || $fKey == '0') {
        $getItem = $tigers->buildCollective();
    } else {
        $getItem = $wolves->getListings($options->listingID, 'object');
    }
}

$filesize = $seahorses->getOption('codes_filesize');
$maxsize = !empty($filesize) ? $filesize : 921600;
$whattext = $fKey == 0 || $fKey == '0' ? 'collective' : 'fanlisting';
$markup = $seahorses->getOption('markup') == 'xhtml' ? ' /' : '';

/**
 * If the form has been set!
 */
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'start') {
        $name = $tigers->cleanMys($_POST['name']);
        $new_name = $tigers->cleanMys($_POST['new_name']);
        if (empty($name) && (!isset($_POST['new_name']) || empty($_POST['new_name']))) {
            $tigers->displayError('Form Error', 'You must choose your name from the' .
                ' dropdown menu, or -- if you\'re a new donator -- fill out the new' .
                ' information fields: the new name field, and new e-mail field.', false);
        } elseif (!empty($name) && !in_array($name, $cheetahs->donorsList())) {
            $tigers->displayError('Form Error', 'You chose an invalid donor via the' .
                ' donor list!', false);
        }
        if (!empty($name)) {
            $donor = $cheetahs->donor($name);
        }
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (
            empty($email) &&
            (empty($name) && (isset($_POST['new_name']) && !empty($_POST['new_name'])))
        ) {
            $tigers->displayError('Form Error', 'You must choose your name from the' .
                ' dropdown menu, or -- if you\'re a new donator -- fill out the new' .
                ' information fields: the new name field, and new e-mail field.', false);
        }
        $doadd = 0;
        $donator = $donor->dName;
        $donatorid = $donor->dID;
        if (
            isset($_POST['new_name'], $_POST['email']) && empty($name) && !empty($_POST['new_name']) && !empty($_POST['email'])
        ) {
            $doadd = 1;
            $donator = $new_name;
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        $number = $tigers->cleanMys($_POST['number']);
        if (!is_numeric($number) || $number > 10) {
            $tigers->displayError('Form Error', 'You can only upload 1-10 code buttons' .
                ' at a time!', false);
        }
        $postcap = isset($_POST[$octopus->cheatCaptcha]) ? $tigers->cleanMys($_POST[$octopus->cheatCaptcha]) : '';
        if ($seahorses->getOption('captcha_opt') == 'y') {
            if (!isset($_POST['captcha']) || strpos(sha1($postcap), (string) $_POST['captcha']) !== 0) {
                $tigers->displayError('Form Error', 'The <samp>CAPTCHA</samp> is invalid!', false);
            }
        }

        if ($doadd) {
            $insert = "INSERT INTO `$_ST[codes_donors]` (`dName`, `dEmail`, `dURL`," .
                " `dPending`, `dUpdated`, `dAdded`) VALUES ('$new_name', '$email', '$url'," .
                " 0, '1970-01-01 00:00:00', NOW())";
            $true = $scorpions->query($insert);
            if ($true == false) {
                $tigers->displayError('Database Error', 'There was an error inserting you' .
                    ' into the donors table at the <strong>' . $getItem->title .
                    '</strong> listing.', false);
            } else {
                $donatorid = $cheetahs->donor($email, 'email', 'dID');
                echo $tigers->displaySuccess('You\'ve been added to the donors table!');
            }
        }
        ?>
        <form enctype="multipart/form-data" method="post">

            <p>
                <input name="action" type="hidden" value="end"<?php echo $markup; ?>>
                <input name="donorid" type="hidden" value="<?php echo $donatorid; ?>"<?php echo $markup; ?>>
            </p>

            <fieldset>
                <legend>Donate Code Buttons</legend>
                <p class="note">Only .gif, .jpg and .png files are allowed to be submitted.</p>
                <?php
                for ($n = 1; $n <= $number; $n++) {
                    ?>
                    <p style="margin: 0;"><input name="numeric[]" type="hidden"
                                                 value="<?php echo $n; ?>"<?php echo $markup; ?>></p>
                    <p><label><strong>File:</strong></label>
                        <input name="image[]" class="input1" type="file"<?php echo $markup; ?>></p>
                    <p><label><strong>Size:</strong></label> <select name="size[]" class="input1">
                            <?php
                            foreach ($cheetahs->sizesList('array') as $s) {
                                echo '  <option value="' . $s['id'] . '">' . $s['name'] . "</option>\n";
                            }
                            ?>
                        </select></p>
                    <?php
                }
                ?>
                <p class="tc" style="text-align: center;">
                    <input class="input2" type="submit" value="Submit"<?php echo $markup; ?>>
                </p>
            </fieldset>
        </form>
        <?php
    } /**
     * This is where we start uploading files :D
     */
    elseif ($_POST['action'] == 'end') {
        $donator = $tigers->cleanMys($_POST['donorid']);
        if (!in_array($donator, $cheetahs->donorsList())) {
            $tigers->displayError('Form Error', 'Only existing donors can donate!', false);
        }
        $donor = $cheetahs->donor($donator);

        /**
         * Count fields and return error if too many
         */
        $howmany = 0;
        foreach ($_POST['numeric'] as $f => $v) {
            ++$howmany;
        }
        if ($howmany == 0 || $howmany > 10) {
            $tigers->displayError('Form Error', 'You can only add up to 10 code buttons' .
                ' per form!', false);
        }

        /**
         * Now we start evaluating the code buttons P:
         */
        foreach ($_POST['numeric'] as $field => $value) {
            if (!empty($_FILES['image']['name'][$field])) {
                $size = $tigers->cleanMys($_POST['size'][$field]);
                if (empty($size) || !in_array($size, $cheetahs->sizesList())) {
                    $tigers->displayError('Form Error', 'You chose an invalid size.', false);
                }
                $imageinfo = getimagesize($_FILES['image']['tmp_name'][$field]);
                $imagetype = $imageinfo[2];
                $imagetag = substr(sha1(date('YmdHis')), 0, 7) . substr(sha1(random_int(0, mt_getrandmax())), 0, 7);
                if ($imagetype != 1 && $imagetype != 2 && $imagetype != 3) {
                    $tigers->displayError('Form Error', 'Only <samp>.gif</samp>, <samp>.jpg' .
                        '</samp> and <samp>.png</samp> extensions are allowed.', false);
                } elseif (filesize($_FILES['image']['tmp_name'][$field]) > $maxsize) {
                    $tigers->displayError('Form Error', 'The file you chose to upload extended' .
                        " the maximum file size the owner of this $whattext chose. Try uploading" .
                        ' again, love? :(', false);
                }
                $img_path = $seahorses->getOption('codes_img_path');
                if (!empty($img_path) || is_dir($img_path)) {
                    $path = $seahorses->getOption('codes_img_path');
                } else {
                    $path = $seahorses->getOption('adm_path');
                }

                $image_tag = substr(sha1(date('YmdHis')), random_int(0, 9), 15);
                $string = file_exists($path . $_FILES['image']['name'][$field]) ? $image_tag . '_' : '';
                $file = $scorpions->escape($string . $_FILES['image']['name'][$field]);
                $success = @move_uploaded_file($_FILES['image']['tmp_name'][$field], $path . $file);

                $insert = "INSERT INTO `$_ST[codes]` (`fNiq`, `cName`, `cFile`, `cCategory`," .
                    " `cSize`, `cDonor`, `cPending`, `cAdded`) VALUES ('" . $options->listingID .
                    "', '', '$file', '', '$size', '$donator', 1, NOW())";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);

                if ($true == false) {
                    $tigers->displayError('Database Error', 'The script was unable to add' .
                        ' the <strong>' . $file . '</strong> code button to the database.', false);
                } elseif ($true == true && $success == false) {
                    echo $tigers->displaySuccess('Your <samp>' . $file . '</samp> code button' .
                        ' was added to the database, but the image could not be uploaded. If the' .
                        " problem persists, don\'t hesitate to e-mail me {$hide_address}.");
                } elseif ($true == true && $success) {
                    echo $tigers->displaySuccess('Your <samp>' . $file . '</samp> code button' .
                        ' was added to the database and is awaiting approval!');
                }
            }
        }

        $jaguars->sendMeMail('Hello ' . $qowns . "!\n\nYou have received a new code" .
            " donation; the details are below:\n\nDonator: " . $donor->dName .
            "\nNumber of codes: $howmany\nListing: " . $getItem->subject .
            "\n\nBrowser: " . $tigers->cleanMys($_SERVER['HTTP_USER_AGENT']) .
            "\nIP Address: " . $tigers->cleanMys($_SERVER['REMOTE_ADDR']) .
            "\n\nTo review and approve/deny these codes, go to your" . ' collective: ' .
            $myadminpath->http, 'New Codes Donated', "From: <$my_email>\nReply-To: <$my_email>");
    }
} /**
 * Get index (form)
 */
else {
    global $mark;
    $captcha = sha1(random_int(10000, 999999));
    ?>
    <form method="post">
        <p>
            <input name="action" type="hidden" value="start"<?php echo $markup; ?>>
            <?php
            if ($seahorses->getOption('captcha_opt') == 'y') {
                echo ' ' . $octopus->captchaCheat($captcha);
            }
            ?>
        </p>

        <fieldset>
            <legend>Donate Codes</legend>
            <p><label><strong>Name</strong><br>
                    If you've donated before, select your name from the dropdown menu:</label>
                <select name="name" class="input1">
                    <option value="">Please choose:</option>
                    <?php
                    foreach ($cheetahs->donorsList() as $d) {
                        $donor = $cheetahs->donor($d);
                        $url = empty($donor->dURL) ? '' : ' (' . $octopus->shortURL($donor->dURL) . ')';
                        echo "  <option value=\"$d\">" . $donor->dName . "$url</option>\n";
                    }
                    ?>
                </select></p>
            <p class="clearField" style="clear: both; margin: 0 0 0.5% 0;"></p>
            <p><label><strong>New Name:</strong></label>
                <input name="new_name" class="input1" type="text"<?php echo $markup; ?>></p>
            <p><label><strong>New E-mail</strong><br>
                    This will not be published:</label>
                <input name="email" class="input1" type="email"<?php echo $markup; ?>></p>
            <p class="clearField" style="clear: both; margin: 0 0 0.5% 0;"></p>
            <p><label><strong>New URL:</strong></label>
                <input name="url" class="input1" type="url"<?php echo $markup; ?>></p>
            <p><label><strong>Number of Buttons:</strong><br>
                    This is the number of code buttons you'd like to donate:</label>
                <select name="number" class="input1">
                    <?php
                    for ($i = 1; $i < 10; $i++) {
                        echo "  <option value=\"$i\">$i</option>\n";
                    }
                    ?>
                </select></p>
            <p class="clearField" style="clear: both; margin: 0 0 0.5% 0;"></p>
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
                    <img alt="CAPTCHA Image" id="captcha" style="height: 80px; width: 170px;" title="CAPTCHA Image"
                         src="<?php echo $my_website; ?>fun-captcha.inc.php?v=<?php echo $captcha; ?>"<?php echo $mark; ?>>
                </p>
                <p class="clearField" style="clear: both; margin: 0 0 0.5% 0;"></p>
                <?php
            }
            ?>
            <p class="tc" style="text-align: center;">
                <input class="input2" type="submit" value="Submit"<?php echo $markup; ?>>
            </p>
        </fieldset>
    </form>
    <?php
}
