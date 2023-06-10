<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-external.inc.php>
 * @version          Robotess Fork
 */

use Robotess\StringUtils;

$getTitle = 'Comments';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if (!isset($_GET['p']) || empty($_GET['p']) || !ctype_digit($_GET['p'])) {
    $page = 1;
} else {
    $page = $tigers->cleanMys((int)$_GET['p']);
}
$start = $scorpions->escape((($page * $per_page) - $per_page));

if (isset($_GET['g']) && $_GET['g'] == 'old') {
    if (empty($_GET['d']) || !isset($_GET['d'])) {
        ?>
        <form action="updates-comments.php" method="get">
            <input name="g" type="hidden" value="old">

            <fieldset>
                <legend>Choose Comment</legend>
                <p><label><strong>Comment:</strong></label> <select name="d" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[updates_comments]` ORDER BY `cAdded` DESC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>Comments Unavailable</option>\n";
                        } else {
                            while ($getItem = $scorpions->obj($true)) {
                                echo '   <option value="' . $getItem->cID . '">' . $getItem->cName . ' (' .
                                    $turtles->entryName($getItem->eNiq) . ")</option>\n";
                            }
                        }
                        ?>
                    </select></p>
                <p class="tc"><input class="input2" type="submit" value="Edit Comment"></p>
            </fieldset>
        </form>
        <?php
    }

    if (!empty($_GET['d'])) {
        $id = $tigers->cleanMys($_GET['d']);
        if (!ctype_digit($id)) {
            $tigers->displayError('Script Error', 'Your ID is not a number. Go' .
                ' back and try again.', false);
        }

        $select = "SELECT * FROM `$_ST[updates_comments]` WHERE `cID` = '$id' LIMIT 1";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to select that' .
                ' specific entry.', true, $select);
        }
        $getItem = $scorpions->obj($true);
        ?>
        <form action="updates-comments.php" enctype="multipart/form-data" method="post">
            <input name="id" type="hidden" value="<?php echo $getItem->cID; ?>">

            <fieldset>
                <legend>Commenter Details</legend>
                <p><label><strong>Name:</strong></label>
                    <input name="name" class="input1" type="text" value="<?php echo $getItem->cName; ?>"></p>
                <p><label><strong>E-mail:</strong></label>
                    <input name="email" class="input1" type="email" value="<?php echo $getItem->cEmail; ?>"></p>
                <p><label><strong>URL:</strong></label>
                    <input name="url" class="input1" type="url" value="<?php echo $getItem->cURL; ?>"></p>
                <p class="tc"><strong>Info:</strong><br>
                    <?php
                    $in = explode('|', $getItem->cInfo);
                    echo '  <samp>IP Address:</samp> ' . $in[0] . '<br>';
                    echo '  <samp>Browser Info:</samp> ' . $in[1] . '<br>';
                    echo '  <samp>Referer:</samp> ' . $in[2];
                    ?>
                </p>
            </fieldset>

            <fieldset>
                <legend>Entry</legend>
                <p><label><strong>Entry:</strong></label> <select name="entry" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[updates]` ORDER BY `uTitle` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "<option>Entries Unavailable</option>\n";
                        } else {
                            while ($getSit = $scorpions->obj($true)) {
                                echo '<option value="' . $getSit->uID . '"';
                                if ($getSit->uID == $getItem->eNiq) {
                                    echo ' selected="selected"';
                                }
                                echo '>' . $getSit->uTitle . "</option>\n";
                            }
                        }
                        ?>
                    </select></p>
            </fieldset>

            <fieldset>
                <legend>Comment</legend>
                <p class="tc">
  <textarea name="comment" cols="60" rows="13" style="height: 200px; margin: 0 1% 0 0; width: 99%;">
<?php echo $getItem->cComment; ?>
  </textarea>
                </p>
            </fieldset>

            <fieldset>
                <legend>Submit</legend>
                <p class="tc"><input name="action" class="input2" type="submit" value="Edit Comment"></p>
            </fieldset>
        </form>
        <?php
    }
} elseif (isset($_POST['action']) && $_POST['action'] == 'Edit Comment') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !ctype_digit($id)) {
        $tigers->displayError('Form Error', 'Your ID is invalid. This means' .
            ' you selected an incorrect comment or you\'re trying to access something' .
            ' that doesn\'t exist. Go back and try again.', false);
    }
    $name = $tigers->cleanMys($_POST['name']);
    if (empty($name)) {
        $tigers->displayError('Form Error', 'The <samp>name</samp> field is empty.',
            false);
    }
    $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
    if (empty($email)) {
        $tigers->displayError('Form Error', 'Your <samp>email</samp> is empty.', false);
    } elseif (!StringUtils::instance()->isEmailValid($email)) {
        $tigers->displayError('Comment Error', 'The characters specified in the' .
            ' <samp>email</samp> field are not allowed.', false);
    }
    $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
    if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
        $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
            ' not valid. Please supply a valid site URL or empty the field.', false);
    }
    $entryid = $tigers->cleanMys($_POST['entry']);
    if (!in_array($entryid, $turtles->updatesList('y'))) {
        $tigers->displayError('Form Error', 'The <samp>entry</samp> field is invalid.',
            false);
    }
    $comment = $tigers->cleanMys($_POST['comment']);
    if (empty($comment)) {
        $tigers->displayError('Form Error', 'The <samp>comment</samp> field is empty.',
            false);
    }

    $update = "UPDATE `$_ST[updates_comments]` SET `eNiq` = '$entryid', `cName`" .
        " = '$name', `cEmail` = '$email', `cURL` = '$url', `cComment` = '$comment'" .
        " WHERE `cID` = '$id' LIMIT 1";
    $scorpions->query("SET NAMES 'utf8';");
    $true = $scorpions->query($update);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script unable to edit the comment.',
            true, $update);
    } elseif ($true == true) {
        echo $tigers->displaySuccess('Your comment was edited!');
        echo $tigers->backLink('updates_comments', $id);
        echo $tigers->backLink('updates_comments');
    }
} /**
 * Delete
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'erase') {
    if (empty($_GET['d']) || !isset($_GET['d'])) {
        ?>
        <form action="updates-comments.php" method="get">
            <input name="g" type="hidden" value="erase">

            <fieldset>
                <legend>Choose Comment</legend>
                <p><label><strong>Comment:</strong></label> <select name="d" class="input1">
                        <?php
                        $select = "SELECT * FROM `$_ST[updates_comments]` ORDER BY `cAdded` ASC";
                        $true = $scorpions->query($select);
                        if ($true == false) {
                            echo "  <option>No Comments Unavailable</option>\n";
                        } else {
                            while ($getItem = $scorpions->obj($true)) {
                                echo '  <option value="' . $getItem->cID . '">' . $getItem->cName . ' (' .
                                    $turtles->entryName($getItem->eNiq) . ")</option>\n";
                            }
                        }
                        ?>
                    </select></p>
                <p class="tc"><input class="input2" type="submit" value="Delete Comment"></p>
            </fieldset>
        </form>
        <?php
    }

    if (!empty($_GET['d'])) {
        $id = $tigers->cleanMys($_GET['d']);
        if (!ctype_digit($id)) {
            $tigers->displayError('Script Error', 'Your ID is not a number. Go back' .
                ' and try again.', false);
        }

        $select = "SELECT * FROM `$_ST[updates_comments]` WHERE `cID` = '$id' LIMIT 1";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to select that' .
                ' specific comment.', true, $select);
        }
        $getItem = $scorpions->obj($true);
        ?>
        <p>You are about to delete the <strong><?php echo $getItem->cName; ?></strong> (of the
            <?php echo $turtles->entryName($getItem->eNiq); ?> entry) comment; please be
            aware that once you delete a comment, it is gone forever. <em>This cannot be
                undone!</em> To proceed, click the "Delete Comment" button.</p>

        <form action="updates-comments.php" method="post">
            <input name="id" type="hidden" value="<?php echo $getItem->cID; ?>">

            <fieldset>
                <legend>Delete Comment</legend>
                <p class="tc">
                    Deleting <strong><?php echo $getItem->cName; ?></strong>
                    (of the <em><?php echo $turtles->entryName($getItem->eNiq); ?></em> entry)<br>
                    <input name="action" class="input2" type="submit" value="Delete Comment">
                </p>
            </fieldset>
        </form>
        <?php
    }
} elseif (isset($_POST['action']) && $_POST['action'] == 'Delete Comment') {
    $id = $tigers->cleanMys($_POST['id']);
    if (empty($id) || !ctype_digit($id)) {
        $tigers->displayError('Form Error', 'Your ID is invalid. This means' .
            ' you selected an incorrect comment or you\'re trying to access something' .
            ' that doesn\'t exist. Go back and try again.', false);
    }

    $delete = "DELETE FROM `$_ST[updates_comments]` WHERE `cID` = '$id' LIMIT 1";
    $true = $scorpions->query($delete);

    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to delete the' .
            ' comment.', true, $delete);
    } elseif ($true == true) {
        echo $tigers->displaySuccess('Your comment was deleted!');
        echo $tigers->backLink('updates_comments');
    }
} /**
 * Mass-approve comments
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'Approve') {
    if (empty($_POST['comment'])) {
        $tigers->displayError('Form Error', 'You need to select a comment (or' .
            ' two, etc.) in order to approve them.', false);
    }

    foreach ($_POST['comment'] as $pm) {
        $comment = $turtles->getComment($pm);
        $update = "UPDATE `$_ST[updates_comments]` SET `cPending` = '0', `cFlag` =" .
            " 'legit', `cAdded` = NOW() WHERE `cID` = '$pm' LIMIT 1";
        $true = $scorpions->query($update);
        if ($true == true) {
            echo $tigers->displaySuccess('The <samp>' . $comment->cName . '</samp>' .
                ' comment (from the <em>' . $turtles->entryName($comment->eNiq) .
                '</em> entry) has been approved! :D');
        }
    }
    echo $tigers->backLink('updates_comments');
} /**
 * Mass-delete comments
 */
elseif (isset($_POST['action']) && $_POST['action'] == 'Delete') {
    if (empty($_POST['comment'])) {
        $tigers->displayError('Form Error', 'You need to select a comment (or' .
            ' two, etc.) in order to approve them.', false);
    }

    foreach ($_POST['comment'] as $pm) {
        $delete = "DELETE FROM `$_ST[updates_comments]` WHERE `cID` = '$pm' LIMIT 1";
        $true = $scorpions->query($delete);
        if ($true == true) {
            echo $tigers->displaySuccess('The comment was deleted!');
        }
    }
    echo $tigers->backLink('updates_comments');
} /**
 * Index
 */
else {
    ?>
    <p>Welcome to <samp>updates-comments.php</samp>, the page to edit or delete your
        current comments! Below is your list of comments. To edit or delete a current
        one, click "Edit" or "Delete" by the appropriate comment.</p>
    <?php
    $select = "SELECT * FROM `$_ST[updates_comments]` ORDER BY `cAdded` DESC" .
        " LIMIT $start, $per_page";
    $true = $scorpions->fetch($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script was unable to select the' .
            ' comments from the database.', true, $select);
    }
    $count = $scorpions->total($true);

    if ($count > 0) {
        ?>
        <form action="updates-comments.php" method="post">

            <table class="index">
                <thead>
                <tr>
                    <th>&#160;</th>
                    <th>Status</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Entry</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td class="tc" colspan="6">With Checked:
                        <input name="action" class="input2" type="submit" value="Approve">
                        <input name="action" class="input2" type="submit" value="Delete">
                    </td>
                </tr>
                </tfoot>
                <?php
                while ($getItem = $scorpions->obj($true)) {
                    $colour = $getItem->cPending == 1 && $getItem->cFlag == 'spam' ?
                        ' class="pending_spam"' : '';
                    ?>
                    <tbody<?php echo $colour; ?>>
                    <tr>
                        <td class="tc"><input name="comment[]" type="checkbox" value="<?php echo $getItem->cID; ?>">
                        </td>
                        <td class="tc">
                            <?php
                            if ($getItem->cPending == 1) {
                                if ($getItem->mUpdate == 'y') {
                                    echo '<strong>Pending: SPAM</strong>';
                                } else {
                                    echo '<em>Pending Approval</em>';
                                }
                            } else {
                                echo 'Approved';
                            }
                            ?>
                        </td>
                        <td class="tc"><?php echo $getItem->cName; ?></td>
                        <td class="tc"><?php echo $getItem->cEmail; ?></td>
                        <td class="tc"><?php echo $turtles->entryName($getItem->eNiq); ?></td>
                        <td class="floatIcnos tc">
                            <a href="updates-comments.php?g=old&#38;d=<?php echo $getItem->cID; ?>">
                                <img src="img/icons/edit.png" alt="">
                            </a>
                            <a href="updates-comments.php?g=erase&#38;d=<?php echo $getItem->cID; ?>">
                                <img src="img/icons/delete.png" alt="">
                            </a>
                        </td>
                    </tr>
                    </tbody>
                    <?php
                }
                ?>
            </table>
        </form>
        <?php
        echo '<p id="pagination">Pages: ';
        $total = is_countable($turtles->commentsList()) ? count($turtles->commentsList()) : 0;
        $pages = ceil($total / $per_page);

        for ($i = 1; $i <= $pages; $i++) {
            if ($page == $i) {
                echo $i . ' ';
            } else {
                echo '<a href="updates-comments.php?p=' . $i . '">' . $i . '</a> ';
            }
        }

        echo "</p>\n";
    } else {
        echo "<p class=\"tc\">Currently no comments!</p>\n";
    }
}

require('footer.php');
