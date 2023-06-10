<?php /** @noinspection PhpIncludeInspection */
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-process.inc.php>
 * @version          Robotess Fork
 */

if (!isset($_POST['action']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    die('<p>ERROR: <em>Nobody ever told [her] it was the wrong way</em>...<br>' .
        "Sorry m'dear, you cannot access this file directly!</p>\n");
}

use Robotess\StringUtils;

require('b.inc.php');
require_once('Robotess/StringUtils.php');
require(MAINDIR . 'rats.inc.php');
require_once('class-antispam.inc.php');
require_once('fun.inc.php');
require_once('fun-external.inc.php');
require_once('fun-misc.inc.php');
require_once('fun-updates.inc.php');
require(MAINDIR . 'vars.inc.php');
if ($seahorses->getOption('akismet_opt') == 'y') {
    require_once('func.microakismet.inc.php');
}

/**
 * Start processing form!
 */
$entryid = $tigers->cleanMys($_POST['eid']);
if (empty($entryid) || !in_array($entryid, $turtles->updatesList('y'))) {
    $tigers->displayError('Form Error', 'Wrong entry! Go back and try again.', false);
}
$entry = $turtles->getEntry($entryid);
$name = $tigers->cleanMys($_POST['name']);
if (empty($name)) {
    $tigers->displayError('Form Error', 'You have not filled out the <samp>' .
        'name</samp> field.', false);
} elseif (!preg_match("/([A-Za-z\\-\s]+)/i", $name)) {
    $tigers->displayError('Form Error', 'There are invalid characters in' .
        ' the <samp>name</samp> field. Go back and try again.', false);
} elseif (strlen($name) > 25) {
    $tigers->displayError('Form Error', 'Your <samp>name</samp> is too' .
        ' long. Go back and shorten it.', false);
}
$name = ucwords($name);

$email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
if (empty($email)) {
    $tigers->displayError('Form Error', 'You have not filled out the <samp>' .
        'email</samp> field.</p>', false);
} elseif (!StringUtils::instance()->isEmailValid($email)) {
    $tigers->displayError('Form Error', 'The characters specified in the' .
        ' <samp>email</samp> field are not allowed.', false);
}
$url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
    $tigers->displayError('Form Error', 'Your <samp>site URL</samp> is' .
        ' not valid. Please supply a valid site URL or empty the field.', false);
}

$comment = $tigers->cleanMys($_POST['comment']);
if (empty($comment)) {
    $tigers->displayError('Form Error', 'In order to post a comment, you must' .
        ' fill out the <samp>Comment</samp> field.', false);
}
$ck = isset($_POST[$octopus->cheatCaptcha]) ? $tigers->cleanMys($_POST[$octopus->cheatCaptcha]) : '';
$s1 = $tigers->cleanMys($_POST['antispamh']);
$s2 = $tigers->cleanMys($_POST['antispamb']);
$s3 = $tigers->cleanMys($_POST['mathproblem']);

/**
 * Grab user information so we can log messages
 */
$uinfo = (object)array(
    'uInfo' => "|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_USER_AGENT']}|",
    'uUser' => '',
    'uText' => "Name: $name\nE-mail: $email\nURL: $url\nComment: $comment",
    'uURL' => 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
);

/**
 * Check for SPAM words and bots, bbCode, and JavaScript, captcha,
 * Akismet, and antispam \o/ First: spam words!
 */
foreach ($laantispam->spamarray() as $b) {
    if (strpos($_POST['comment'], (string) $b) !== false) {
        $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
    }
}

foreach ($laantispam->bbcode as $h) {
    if (strpos($_POST['comment'], (string) $h) !== false) {
        $tigers->displayError('SPAM Error', 'bbCode language is not allowed.', false);
    }
}

if (
    preg_match($badheaders, $_SERVER['HTTP_USER_AGENT']) ||
    empty($_SERVER['HTTP_USER_AGENT'])
) {
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

if ($seahorses->getOption('captcha_opt') == 'y') {
    if (!isset($_POST['captcha']) || strpos(sha1($ck), (string) $_POST['captcha']) !== 0) {
        $tigers->displayError('Form Error', 'The <samp>CAPTCHA</samp> is invalid!', false);
    }
}

if ($seahorses->getOption('antispam_opt') == 'y') {
    $vars = array(
        //@todo ?
        'comments' => $comment,
        'mathproblem' => $_POST['mathproblem'],
        'nots' => $laantispam->spamarray(),
        's1' => $s1,
        's2' => $s2,
        'url' => $url
    );
    $antispam = $laantispam->antispam($vars);

    if ($antispam->status == false) {
        $seahorses->writeMessage(
            0, 'SPAM Error: Anti-SPAM (Join)', $uinfo->uURL, $uinfo->uText, $automated
        );
        $tigers->displayError('SPAM Error', 'It appears the script has' .
            ' identified you as SPAM. If you believe you\'re not SPAM, feel free to' .
            ' join ' . $hide_address . '.', false);
    }
}

if ($seahorses->getOption('akismet_opt') == 'y') {
    $vars = array(
        'user_ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'referer' => $_SERVER['HTTP_REFERER'],
        'comment_type' => 'join',
        'comment_author' => $name,
        'comment_author_email' => $email,
        'comment_author_url' => $url,
        'comment_content' => $comment
    );

    $check = akismet_check($vars);
    if ($check == true) {
        $seahorses->writeMessage(
            0, 'SPAM Error: Anti-SPAM (Join)', $uinfo->uURL, $uinfo->uText, $automated
        );
        $tigers->displayError('SPAM Error', 'It appears Akismet thinks you\'re' .
            ' SPAM.|While this isn\'t a <em>huge</em> issue it\'s keeping you from being' .
            ' added to the pending list. If you believe you\'re not SPAM, feel free to' .
            ' join ' . $hide_address . '.', false);
    }
}

/**
 * ADD ZE COMMET
 */
$insert = "INSERT INTO `$_ST[updates_comments]` (`eNiq`, `cName`, `cEmail`," .
    ' `cURL`, `cComment`, `cInfo`, `cFlag`, `cPending`, `cAdded`) VALUES' .
    " ('$entryid', '$name', '$email', '$url', '$comment', '$uinfo->uInfo', 'legit'," .
    " '$pending_comment_form', NOW())";
$scorpions->query("SET NAMES 'utf8';");
$true = $scorpions->query($insert);
if ($true == false) {
    $seahorses->writeMessage(0, 'New Comment', $uinfo->uURL, $uinfo->uText, $uinfo->uInfo);
    $tigers->displayError('Database Error', 'For some reason, the script was' .
        ' unable to add your comment to the database. If the problems persists, feel' .
        ' free to contact me via form.');
} /**
 * On success, send e-mail to owner
 */
elseif ($true == true) {
    $seahorses->writeMessage(1, 'New Comment', $uinfo->uURL, $uinfo->uText, $uinfo->uInfo);

    $ex = $tigers->emptyarray(explode('!', $entry->uCategory));
    $wh = in_array('0', $ex) ? ' collective' : ' listing(s)';
    $ad = strpos($my_website,
        '/') !== false ? $my_website . 'updates-comments.php' : $my_website . '/updates-comments.php';
    if ($seahorses->getOption('updates_comments_notification') == 'y') {
        $subjectLine = $qname . ': New Comment';
        $message = "Hello $qowns,\n\nYou have received a new comment at your$wh:\n\n" .
            "Name: $name\nE-mail: $email\n";
        if (!empty($url)) {
            $message .= "URL: <$url>\n\n";
        } else {
            $message .= "\n";
        }
        $headers = 'From: Listing Admin <' . $seahorses->getOption('my_email') . ">\n";
        $headers .= 'Reply-To: <' . $seahorses->getOption('my_email') . '>';

        $message .= "Comment: $comment\n\nIP Address: " . $_SERVER['REMOTE_ADDR'] .
            "\nBrowser: " . $_SERVER['HTTP_USER_AGENT'] . "\n\nTo moderate this comment," .
            " go here: <$ad>From: Listing Admin <$email>\nReply-To: <{$email}>";
        $mail = @mail($my_email, $subjectLine, $message, $headers);
    }

    /**
     * Include header and footer files if they exist, along with success
     * message(s)!
     */
    $h1 = $seahorses->getOption('updates_comments_header');
    $h2 = $seahorses->getOption('updates_comments_footer');
    if (is_file($h1)) {
        require($h1);
    }
    echo "<h2>Comments</h2>\n<h3>Success</h3>\n<p class=\"successButton\"><span" .
        ' class="success">SUCCESS!</span> You have successfully submitted a comment' .
        " at <strong>$qname</strong>!</p>\n";
    /** @noinspection PhpUndefinedVariableInspection - in vars.inc.php */
    echo $if_pending;
    echo $octopus->frontEndLink('comments', $entryid);
    if (is_file($h2)) {
        require($h2);
    }
}
