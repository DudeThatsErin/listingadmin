<?php
declare(strict_types=1);
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <pro.inc.php>
 * @version          Robotess Fork
 */

ob_start();
session_start();
header('Cache-Control: no-cache, must-revalidate');

if (!file_exists('rats.inc.php')) {
    ?>
    <section><span class="mysql">Notice:</span> there was an error while trying to find file rats.inc.php.
        Please make sure you have copied rats.sample.inc.php to rats.inc.php and added it to <?= __DIR__; ?>. The script stops executing.
    </section>
    <?php
    die;
}

require('rats.inc.php');
require_once('inc/Robotess/autoloader.php');
require('inc/fun.inc.php');
require('inc/fun-addons.inc.php');
require('inc/fun-admin.inc.php');
require('inc/fun-misc.inc.php');
require('inc/fun-utility.inc.php');
require('inc/class-antispam.inc.php');

/**
 * Include necessary files that are *not* mods!
 */
require('inc/fun-affiliates.inc.php');
require('inc/fun-categories.inc.php');
require('inc/fun-emails.inc.php');
require('inc/fun-external.inc.php');
require('inc/fun-joined.inc.php');
require('inc/fun-listings.inc.php');
require('inc/fun-members.inc.php');
require('inc/fun-wishlist.inc.php');

try {
    /**
     * Include classes!
     */
    if ($seahorses->getOption('kim_opt') == 'y') {
        require('inc/class-kimadmin.inc.php');
    }
} catch (Exception $e) {
    if(($getTitle ?? 'none') === 'Install') {
        return;
    }

    ?>
    <section><span class="mysql">Notice:</span> there was an error while trying to retrieve Listing Admin options. Please make sure you have installed the script. <?php
        if(isset($scorpions)) {
            ?>Error message/code: <?= $scorpions->error(); ?>. <?php
        } else {
            echo 'Check your php logs. ';
        }
        ?>The script stops executing.
    </section>
    <?php
    die;
}

if ($seahorses->getOption('updates_opt') == 'y') {
    require('inc/vendors/class-crosspost.inc.php');
    require('inc/vendors/class-ixr.inc.php');
    require('inc/fun-updates.inc.php');
}

/**
 * Logout!
 */
if (isset($_GET['g']) && $_GET['g'] == 'logout') {
    setcookie('lalog', '');
    $baseu = str_replace('inc/', '', $seahorses->getOption('adm_http'));
    header("Location: $baseu");
}

/**
 * Get any variables we need~
 */
$loginForm = true;
$message = array();
$userObj = (object)array(
    'userHash' => substr(md5($seahorses->getOption('user_salthash')), 0, 5),
    'userInfo' => "|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_USER_AGENT']}|",
    'userPass' => '',
    'userUser' => '',
    'userText' => '',
    'userURL' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
);

/**
 * Get "Forgot Password" form!
 */
if (isset($_GET['forgot'])) {
    $loginForm = false;

    if (isset($_GET['h']) && preg_match('/([A-Za-z0-9]+)/i', $_GET['h'])) {
        if ($seahorses->getOption('user_passhinthash') == trim($_GET['h'])) {
            $password = substr(sha1(date('YmdHis')), 0, 8) . substr(sha1(random_int(99999, 999999)), 0, 8);
            $update = "UPDATE `$_ST[options]` SET `text` = MD5('$password') WHERE `name` =" .
                " 'user_password' LIMIT 1";
            $scorpions->query("SET NAMES 'utf8';");
            $scorpions->query($update);

            $message = 'Hello ' . $seahorses->getOption('my_name') . ",\n\n";
            $message .= 'You have received this email because you (or someone else)' .
                ' filled out the "Reset Password" form (with the password hint) at your collective' .
                ' admin panel, and reset the link that was sent to your e-mail address. If' .
                ' this is in error, please make sure to change your password via phpMyAdmin' .
                " as soon as you are able.\n\n";
            $message .= 'Your information is below; it is highly recommended you change' .
                " your password as soon as you are able. :D\n\n";

            $message .= "Password: $password\n\n";

            $message .= "--\n" . $seahorses->getOption('my_name') .
                "\n" . $seahorses->getOption('collective_name') .
                ' <' . $seahorses->getOption('my_website') . '>';

            $headers = 'From: Listing Admin <' . $seahorses->getOption('my_email') . ">\n";
            $headers .= 'Reply-To: <' . $seahorses->getOption('my_email') . '>';

            $mail = @mail($seahorses->getOption('my_email'), 'Reset Password', $message, $headers);

            if ($mail) {
                echo '<p><span class="success">Success!</span> Your password was reset and' .
                    " sent to your e-mail address!</p>\n";
            } else {
                echo '<p><span class="error">Error</span> Your password was reset, but the' .
                    ' the script was unable to send you your password. I would recommend trying' .
                    ' again, and if that fails, you update your password via phpMyAdmin or your' .
                    " MySQL manager.</p>\n";
            }
        } else {
            $tigers->displayError('Script Error', 'The hash in the URL you provided' .
                ' does not match the one on file.', false);
        }
    } else {
        ?>
        <!DOCTYPE html>

        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title> <?php echo $laoptions->version; ?> &#8212; Log In &#187; Reset Password </title>
            <link href="style.css" rel="stylesheet" type="text/css">
        </head>

        <body>

        <div id="login">
            <div id="logo">
                <h2>Reset Password</h2>
                <p>Fill in the password hint you provided in your installation of Listing Admin.</p>
                <?php
                if (isset($_POST['action']) && $_POST['action'] == 'Reset Password') {
                    $loginForm = false;
                    $passwordhint = $tigers->cleanMys($_POST['passwordhint']);

                    /**
                     * Check for SPAM bots in the form, respectively
                     */
                    if (
                        preg_match($loginInfo->logBots, $_SERVER['HTTP_USER_AGENT']) ||
                        empty($_SERVER['HTTP_USER_AGENT'])
                    ) {
                        $tigers->displayError('SPAM Error', 'SPAM bots are not allowed.', false);
                    }

                    /**
                     * Check if any bad words have been inserted :s
                     */
                    foreach ($loginInfo->logNots as $b) {
                        if (array_key_exists($b, $_POST)) {
                            $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
                        }
                    }

                    # -- Now check ze password hint! ----------------------------
                    if ($seahorses->getOption('user_passhint') === $passwordhint) {
                        $hash = substr(sha1(date('YmdHis')), 0, 12) . substr(sha1(random_int(99999, 999999)), 0, 12);
                        $update = "UPDATE `$_ST[options]` SET `text` = '$hash' WHERE `name` =" .
                            " 'user_passhinthash' LIMIT 1";
                        $scorpions->query("SET NAMES 'utf8';");
                        $scorpions->query($update);

                        $message = 'Hello ' . $seahorses->getOption('my_name') . ",\n\n";
                        $message .= 'You have received this email because you (or someone else) ' .
                            'filled out the "Reset Password" form (with the password hint) at your collective ' .
                            'admin panel. If this is in error, please make sure to change your password via ' .
                            "phpMyAdmin as soon as you are able.\n\n";
                        $message .= 'The link to reset your password is below; click the link (or copy' .
                            " and paste it into the address bar of your browser) to reset your password.\n\n";

                        $message .= 'Link: <' . str_replace('inc/', '', $seahorses->getOption('admin_http')) .
                            '?forgot&#38;h=' . $hash . ">\n\n";

                        $message .= "--\n" . $seahorses->getOption('my_name') .
                            "\n" . $seahorses->getOption('collective_name') .
                            ' <' . $seahorses->getOption('my_website') . '>';

                        $headers = 'From: Listing Admin <' . $seahorses->getOption('my_email') . ">\n";
                        $headers .= 'Reply-To: <' . $seahorses->getOption('my_email') . '>';

                        $mail = @mail($seahorses->getOption('my_email'), 'Reset Password', $message, $headers);

                        if ($mail) {
                            echo '<p><span class="success">Success!</span> Your lost password form was' .
                                ' processed, and a link was sent to your e-mail address to reset your password.' .
                                ' From there, visit the link in the e-mail sent to you, where your password' .
                                " will be reset and e-mailed to you.</p>\n";
                        } else {
                            echo '<p>Your form was processed, however, the script was unable to send you' .
                                ' the link to reset your password. I would recommend trying again, and if' .
                                " that fails, you update your password via phpMyAdmin or your MySQL manager.</p>\n";
                        }
                    } else {
                        $userObj->userText = "Password Hint: [not shown]";
                        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                            $userObj->userInfo .= "{$_SERVER['HTTP_REFERER']}|";
                        }
                        $octopus->writeError('Reset Password', $userObj->userURL, $userObj->userText,
                            $userObj->userInfo);
                        $tigers->displayError('Form Error', 'The password hint you entered does' .
                            ' not match the on file.', false);
                    }
                }
                ?>
            </div>

            <div id="form">
                <form action="<?php echo str_replace('inc/', '', $seahorses->getOption('adm_http')); ?>?forgot"
                      method="post">
                    <fieldset>
                        <legend>Reset Password</legend>
                        <p><label><strong>Password Hint:</strong></label>
                            <input name="passwordhint" class="input1" type="password" required></p>
                        <p class="tc"><input name="action" class="input2" type="submit" value="Reset Password"></p>
                    </fieldset>
                </form>
            </div>

            <div id="clear-login"></div>
        </div>

        </body>
        </html>
        <?php
    }

    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'Log In') {
    if (preg_match($loginInfo->logBots, $_SERVER['HTTP_USER_AGENT']) || empty($_SERVER['HTTP_USER_AGENT'])) {
        $message['error'] = '<p class="error tc">Known SPAM bots aren\'t allowed.</p>';
    }
    $userObj->userUser = trim(strip_tags($_POST['username']));
    $userObj->userPass = md5(trim(strip_tags($_POST['password'])));
    $userObj->userText = 'Username: ' . $userObj->userUser .
        "\nPassword: [not shown]";

    $checker = $leopards->checkUser($userObj->userUser, $userObj->userPass);
    if ($checker == 1) {
        $seahorses->writeMessage(0, 'Failed User Log-In', $userObj->userURL, $userObj->userText,
            $userObj->userInfo);
        $message['error'] = '<p class="error tc">ERROR: The username/password' .
            " combination you entered does not match the one on file. Try again, m'love!</p>";
    } elseif ($checker == 0) {
        $loginForm = false;
        $userNm = $tigers->cleanMys($userObj->userUser);
        $userPs = $tigers->cleanMys($userObj->userPass);

        /**
         * Diagnostics! Update user login, insert into logs and make sure the
         * user isn't locked out!
         */
        $leopards->logUser(0, $userObj->userUser, $userObj->userInfo);
        $seahorses->writeMessage(1, 'User Log-In Success', $userObj->userURL,
            $userObj->userText, $userObj->userInfo);

        if (isset($_POST['rememberMe']) && $_POST['rememberMe'] == 'y') {
            setcookie(
                'lalog',
                sha1($laoptions->saltPass . $userNm . $userPs . $userObj->userHash),
                time() + 60 * 60 * 24 * 30
            );
        } else {
            setcookie(
                'lalog',
                sha1($laoptions->saltPass . $userNm . $userPs . $userObj->userHash)
            );
        }
        header('Location: index.php');
        exit();
    }
} else {
    if (isset($_COOKIE['lalog']) && !empty($_COOKIE['lalog'])) {
        $userName = $seahorses->getOption('user_username');
        $userPass = $seahorses->getOption('user_password');

        $loginForm = false;
        if ($_COOKIE['lalog'] === sha1($laoptions->saltPass . $userName . $userPass . $userObj->userHash)) {
            $loginForm = false;
        } else {
            $loginForm = true;
        }
    } else {
        $loginForm = true;
    }
}

if ($loginForm) {
    ?>
    <!DOCTYPE html>

    <html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title> <?= $laoptions->version ?> &#8212; Log In </title>
        <link href="style.css" rel="stylesheet" type="text/css">
    </head>

    <body>

    <div id="login">

        <section id="login-logo">
            <h2>Enter Your Password</h2>
            <p>Fill in the username and password you filled out prior to installation.</p>
            <?php
            if (isset($message['error']) && count($message) > 0) {
                echo "\n<h3>Errors</h3>\n";
                echo "{$message['error']}\n";
                echo '<p><a href="' . str_replace('inc/', '', $seahorses->getOption('adm_http')) .
                    "?forgot\">&#171; Forgot Password?</a></p>\n";
            }
            ?>
        </section>

        <section id="login-form">
            <form action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">
                <fieldset>
                    <legend>Login</legend>
                    <p><label><strong>Username:</strong></label>
                        <input name="username" class="input1" type="text"></p>
                    <p><label><strong>Password:</strong></label>
                        <input name="password" class="input1" type="password"></p>
                    <p class="tc">
                        Remember? <input name="rememberMe" checked="checked" class="input3" type="checkbox"
                                         value="y"><br>
                        <input name="action" class="input2" type="submit" value="Log In">
                    </p>
                </fieldset>
            </form>
        </section>

        <section id="clear"></section>

    </div>

    </body>

    </html>
    <?php
    exit();
}
