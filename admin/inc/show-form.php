<div id="show-affiliates">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-form.php>
     * @version          Robotess Fork
     */

    use Robotess\StringUtils;

    require('b.inc.php');
    require_once('Robotess/StringUtils.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('class-antispam.inc.php');
    require_once('fun.inc.php');
    require_once('fun-emails.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');
    if ($seahorses->getOption('akismet_opt') == 'y') {
        require_once('func.microakismet.inc.php');
    }

    /**
     * Get variables and options!
     */
    $options = (object)array();

    if (
        !isset($fKey) ||
        ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
    ) {
        $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
    } else {
        $options->listingID = $tigers->cleanMys($fKey);
        $getItem = $wolves->getListings($options->listingID, 'object');
    }

    if (isset($fClose)) {
        if (empty($fClose)) {
            $options->form = 'open';
        } elseif ($fClose == 'n') {
            $options->form = 'open';
        } elseif ($fClose == 'y') {
            $options->form = 'closed';
        }
    } else {
        $options->form = 'open';
    }

    if (isset($fWhich)) {
        if (empty($fWhich)) {
            $options->formChoice = 'both';
        } elseif ($fWhich == 'form') {
            $options->formChoice = 'form';
        } elseif ($fWhich == 'list') {
            $options->formChoice = 'list';
        } elseif ($fWhich == 'both') {
            $options->formChoice = 'both';
        }
    } else {
        $options->formChoice = 'both';
    }

    if (isset($antispam) && $antispam == 'n') {
        $options->spamAnti = 'n';
    } else {
        $options->spamAnti = 'y';
    }

    if (isset($captcha) && $captcha == 'n') {
        $options->spamCaptcha = 'n';
    } else {
        $options->spamCaptcha = 'y';
    }

    if (isset($turnAff) && in_array($turnAff, array(0, 1))) {
        $options->turnAff = $turnAff;
    } else {
        $options->turnAff = 1;
    }

    if (isset($turnCon) && in_array($turnCon, array(0, 1))) {
        $options->turnCon = $turnCon;
    } else {
        $options->turnCon = 1;
    }

    if ($options->listingID == '0' || $options->listingID == 0) {
        $options->markup = $seahorses->getOption('markup');
    } else {
        $options->markup = $getItem->markup;
    }

    /**
     * Form is being submitted, so we process the form~
     */
    if (isset($_POST['action']) && $_POST['action'] == 'Submit Form') {
        $name = $tigers->cleanMys($_POST['name']);
        if (empty($name)) {
            $tigers->displayError('Form Error', 'The <samp>name</samp> field is empty.', false);
        } elseif (!preg_match("/([A-Za-z\\-\s]+)/i", $name)) {
            $tigers->displayError('Form Error', 'The <samp>name</samp> field contains' .
                ' invalid characters.', false);
        } elseif (strlen($name) > 20) {
            $tigers->displayError('Form Error', 'The <samp>name</samp> field is too long.',
                false);
        } elseif ($name == 'Anonymous' || $name == 'Anon' || $name == 'Anymous') {
            $tigers->displayError('Form Error', 'The owner currently has the \'Anonymous\'' .
                ' feature turned off. Please supply a valid name.', false);
        }
        $email = StringUtils::instance()->normalizeEmail($tigers->cleanMys($_POST['email']));
        if (empty($email)) {
            $tigers->displayError('Form Error', 'The <samp>e-mail</samp> field is empty.',
                false);
        } elseif (!StringUtils::instance()->isEmailValid($email)) {
            $tigers->displayError('Form Error', 'The characters specified in the <samp>' .
                'email</samp> field are not allowed.', false);
        }
        $url = StringUtils::instance()->normalizeUrl($tigers->cleanMys($_POST['url']));
        if (!empty($url) && !StringUtils::instance()->isUrlValid($url)) {
            $tigers->displayError('Form Error', 'The <samp>site URL</samp> is not valid.' .
                ' Please supply a valid site URL or empty the field.', false);
        }
        $reason = $tigers->cleanMys($_POST['reason']);
        if (empty($reason)) {
            $tigers->displayError('Form Error', 'The <samp>reason</samp> field is empty.', false);
        } elseif (
            ($reason == 'Option:' || $reason == 'Option') || !in_array($reason, $get_reason_array)
        ) {
            $tigers->displayError('Form Error', 'The <samp>reason</samp> field is not' .
                ' valid. Choose another option.', false);
        }
        if ($turnAff == 0) {
            if ($reason == 'Affiliation') {
                $tigers->displayError('Form Error', 'Your <samp>reason</samp> field' .
                    ' is invalid. Affiliation is currently turned off, and therfore you are' .
                    ' unable to apply. Check back another time.', false);
            }
        } elseif ($turnCon == 0) {
            if ($reason == 'Contact') {
                $tigers->displayError('Form Error', 'Your <samp>reason</samp> field' .
                    ' is invalid. Contact is currently turned off, and therfore you are unable' .
                    ' to contact the owner. Check back another time.', false);
            }
        }
        $comments = $tigers->cleanMys($_POST['comments']);
        $ck = isset($_POST['captchak']) ? $tigers->cleanMys($_POST['captchak']) : '';
        $s1 = $tigers->cleanMys($_POST['antispamh']);
        $s2 = $tigers->cleanMys($_POST['antispamb']);
        $s3 = $tigers->cleanMys($_POST['mathproblem']);

        /**
         * Grab user information so we can log messages
         */
        $userinfo = (object)array(
            'url' => $tigers->cleanMys(
                'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF']
            ),
            'text' => "Name: $name\nE-Mail Address: $email\nURL: $url\nReason: $reason\n" .
                "Comments: $comments"
        );

        /**
         * Check for spam-y words, both default and user-defined :D
         */
        foreach ($laantispam->spamarray() as $b) {
            if (strpos($comments, (string) $b) === true) {
                $octopus->writeError(
                    'SPAM Error: SPAM Language', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
            }
        }

        /**
         * Check for bbCode, because some bots think they're clever D:
         */
        foreach ($laantispam->bbcode as $h) {
            if (strpos($comments, (string) $h) === true) {
                $octopus->writeError(
                    'SPAM Error: bbCode Language', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'bbCode language is not allowed.', false);
            }
        }

        /**
         * And our SPAM ~bots~, of course, because they can sneak up on us
         * from time to time, yeah?
         */
        if (
            preg_match($badheaders, $_SERVER['HTTP_USER_AGENT']) ||
            empty($_SERVER['HTTP_USER_AGENT'])
        ) {
            $tigers->displayError('SPAM Error', 'SPAM bots are not allowed.', false);
        }

        /**
         * Our captcha image, if we've enabled it :D
         */
        if ($seahorses->getOption('captcha_opt') == 'y' && $options->spamCaptcha == 'y') {
            if (!isset($_POST['captcha']) || strpos(sha1($ck), (string) $_POST['captcha']) !== 0) {
                $tigers->displayError('Script Error', 'The <samp>CAPTCHA</samp> is invalid!', false);
            }
        }

        /**
         * Get Listing Admin's antispam plugin, which checks for common
         * errors (HTML in the comments, empty user agents, et al.) and tallies up a
         * points system; it's key feature, however, is the math problem~
         */
        if ($seahorses->getOption('antispam_opt') == 'y' && $options->spamAnti == 'y') {
            $vars = array(
                'comments' => $comments,
                'mathproblem' => $_POST['mathproblem'],
                'nots' => $laantispam->spamarray(),
                's1' => $s1,
                's2' => $s2,
                'url' => $url
            );
            $antispam = $laantispam->antispam($vars);

            if ($antispam->status == false) {
                $octopus->writeError(
                    'SPAM Error: Anti-SPAM (Join)', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'It appears the script has' .
                    ' identified you as SPAM. If you believe you\'re not SPAM, feel free to' .
                    ' join ' . $hide_address . '.', false);
            }
        }

        /**
         * As our last check, we get the Akismet plugin; A LIFE SAVER
         */
        if ($seahorses->getOption('akismet_opt') == 'y') {
            $vars = array(
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'referer' => $_SERVER['HTTP_REFERER'],
                'comment_type' => 'affiliate',
                'comment_author' => $name,
                'comment_author_email' => $email,
                'comment_author_url' => $url,
                'comment_content' => $comments
            );

            $check = akismet_check($vars);
            if ($check == true) {
                if ($options->affiliates == 1 && $options->contact == 1) {
                    $form_name = 'affiliation and/or contact';
                } elseif ($options->affiliates == 1 && $options->contact == 0) {
                    $form_name = 'affiliation';
                } elseif ($options->affiliates == 0 && $options->contact == 1) {
                    $form_name = 'contact';
                }
                $octopus->writeError(
                    'SPAM Error: Akismet (Contact)', $userinfo->url, $userinfo->text, $automated
                );
                $tigers->displayError('SPAM Error', 'It appears Akismet thinks you\'re' .
                    ' SPAM.|While this isn\'t a <em>huge</em> issue it\'s keeping you from' .
                    ' sending in a ' . $form_name . '. If you believe you\'re not SPAM, feel free to' .
                    ' contact me ' . $hide_address . '.', false);
            }
        }

        /**
         * Now we process the reasons and format e-mails to be sent~!
         */
        if ($reason == 'Affiliation') {
            $class = 'New Affiliate';
            $type = 'new affiliate';
        } elseif ($reason == 'Affiliation: Update') {
            $class = 'Update Affiliate';
            $type = 'update affiliate';
        } elseif ($reason == 'Comments') {
            $class = 'Comments';
            $type = 'new comment';
        } elseif ($reason == 'Contact') {
            $class = 'Contact';
            $type = 'new contact form';
        } elseif ($reason == 'Questions/Concerns') {
            $class = 'Inquiry';
            $type = 'new inquiry';
        }

        if ($options->listingID == 0) {
            $getItem->subject = $qname;
        }
        $subject = $getItem->subject . ': ' . $class;

        $message = '';
        $message .= "You have a received a $type for " . $getItem->subject . ":\n\n" .
            "Name: $name\nE-Mail: $email\n";
        if (!empty($url)) {
            $message .= "Site URL: <$url>\n";
        }
        $message .= "Reason: {$reason}\n\nComments: " . $_POST['comments'] .
            "\n\nIP Address: {$_SERVER['REMOTE_ADDR']}\nBrowser: {$_SERVER['HTTP_USER_AGENT']}";
        if ($reason == 'Affiliation' || $reason == 'Affiliation: Update') {
            $message .= "\n\nTo add/moderate this affiliate, go here: <" . $myadminpath->http . '>';
        }

        $headers = "From: Listing Admin <$my_email>\n";
        if ($reason == 'Affiliation' || $reason == 'Affiliation: Update') {
            $headers .= "Reply-To: <{$my_email}>";
        } else {
            $headers .= "Reply-To: <{$email}>";
        }

        $mail = $jaguars->sendMeMail($message, $subject, $headers);
        if (isset($mail) && $mail) {
            echo '<p><span class="success">Success!</span> Your affiliation/contact form' .
                ' was processed, and your request was sent. Please allow anywhere from one' .
                " to six days for a response. :D</p>\n";
        }
    } /**
     * Index: get our affiliates, contact form or both :D
     */
    else {
        $z = $options->listingID == 0 ? 'collective' : 'fanlisting';
        if ($options->formChoice == 'list' || $options->formChoice == 'both') {
            if (!empty($getItem->dbhost) && !empty($getItem->dbuser) && !empty($getItem->dbname)) {
                $scorpions->initDB($getItem->dbhost, $getItem->dbuser, $getItem->dbpass, $getItem->dbname);
            }

            if ($options->listingID == '0') {
                $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '0' OR `fNiq` LIKE" .
                    " '%!0!%' ORDER BY `aSubject` ASC";
            } else {
                $dbaffs = $getItem->dbaffs;
                if ($getItem->dblist == 1) {
                    if ($getItem->dbtype == 'enth') {
                        $select = "SELECT * FROM `$dbaffs` ORDER BY `title` ASC";
                    } elseif ($getItem->dbtype == 'listingadmin') {
                        $select = "SELECT * FROM `$dbaffs` ORDER BY `fNiq` = '" . $getItem->dbflid .
                            "' OR `fNiq` LIKE '%!" . $getItem->dbflid . "!%'";
                    }
                } else {
                    $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '" . $options->listingID .
                        "' OR `fNiq` LIKE '%!" . $options->listingID . "!%' ORDER BY `aSubject` ASC";
                }
            }

            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' affiliates from the specified listing.', false);
            }
            $count = $scorpions->total($true);

            $pullTemps = $options->listingID == 0 || $options->listingID ==
            '0' ? $seahorses->getTemplate('affiliates_template') : $getItem->affiliates;
            $pemp = html_entity_decode($pullTemps, ENT_QUOTES, 'ISO-8859-15');
            if ($count > 0) {
                if (empty($pullTemps)) {
                    echo $octopus->alternate('menu', $options->markup);
                } else {
                    if (preg_match('/(<li>)/', $pemp)) {
                        echo $octopus->alternate('menu', $options->markup);
                    } elseif (preg_match('/(<tbody>|<tr>|<td>)/', $pemp)) {
                        echo $octopus->alternate('table', $options->markup);
                    } else {
                        echo "<div class=\"showAffiliates tc\">\n";
                    }
                }
                while ($getOb = $scorpions->obj($true)) {
                    if ($options->listingID == '0') {
                        $subjectnow = $getOb->aSubject;
                        $urlnow = $getOb->aURL;
                        $imagenow = $getOb->aImage;
                    } else {
                        $subjectnow = $getItem->dblist == 1 ? $getOb->title : $getOb->aSubject;
                        $urlnow = $getItem->dblist == 1 ? $getOb->url : $getOb->aURL;
                        $imagenow = $getItem->dblist == 1 ? $getOb->imagefile : $getOb->aImage;
                    }
                    if (empty($pullTemps)) {
                        echo '<li><a href="' . $urlnow . '" title="' . $subjectnow .
                            '">' . $subjectnow . "</a></li>\n";
                    } else {
                        if ($options->listingID == 0 || $options->listingID == '0') {
                            $format = html_entity_decode($pullTemps);
                            $format = str_replace('{subject}', $subjectnow, $format);
                            $format = str_replace('{url}', $urlnow, $format);
                            $format = str_replace('{image}', $seahorses->getOption('aff_http') . $imagenow, $format);
                            echo $format . "\n";
                        } else {
                            echo $wolves->listingTemplate(
                                $options->listingID, 'affiliates', $urlnow, $subjectnow, $imagenow
                            );
                        }
                    }
                }
                if (empty($pullTemps)) {
                    echo $octopus->alternate('menu', $options->markup, 1);
                } else {
                    if (preg_match('/(<li>)/', $pemp)) {
                        echo $octopus->alternate('menu', $options->markup, 1);
                    } elseif (preg_match('/(<tbody>|<tr>|<td>)/', $pemp)) {
                        echo $octopus->alternate('table', $options->markup, 1);
                    } else {
                        echo "</div>\n";
                    }
                }
            } else {
                echo '<p class="tc">Currently no affiliates!</p>' . "\n";
            }
        }

        if ($options->formChoice == 'form' || $options->formChoice == 'both') {
            if ($options->listingID == '0') {
                $ctype = 'collective';
            } else {
                $ctype = 'listing';
            }

            if ($options->listingID != '0') {
                if ($getItem->markup == 'xhtml') {
                    $mark = ' /';
                } else {
                    $mark = '';
                }
            } else {
                if ($seahorses->getOption('markup') == 'xhtml') {
                    $mark = ' /';
                } else {
                    $mark = '';
                }
            }

            if ($options->form == 'open') {
                $c2 = $octopus->formURL('contact', $getItem);
                $spamvars = (object)array(
                    'antiuno' => random_int(1, 10),
                    'antidos' => random_int(1, 10)
                );
                $spamoptions = (object)array(
                    'capthash' => sha1(random_int(10000, 999999)),
                    'antians' => $spamvars->antiuno + $spamvars->antidos,
                    'antipro' => $spamvars->antiuno . ' + ' . $spamvars->antidos
                );
                ?>
                <p>The following form should be used to contact the owner of the <?php echo $ctype; ?> and/or to apply
                    for
                    affiliates. Fields marked with a asterick (*) are required.</p>

                <form action="<?php echo $c2; ?>" method="post">
                    <?php
                    if (
                        $seahorses->getOption('captcha_opt') == 'y' ||
                        $seahorses->getOption('antispam_opt') == 'y'
                    ) {
                        echo "<p style=\"margin: 0;\">\n";
                    }

                    if ($seahorses->getOption('captcha_opt') == 'y') {
                        echo $octopus->captchaCheat($spamoptions->capthash);
                    }

                    if ($seahorses->getOption('antispam_opt') == 'y') {
                        echo $octopus->antispamCheat($spamoptions->antians, $spamoptions->antipro);
                    }

                    if (
                        $seahorses->getOption('captcha_opt') == 'y' ||
                        $seahorses->getOption('antispam_opt') == 'y'
                    ) {
                        echo "</p>\n";
                    }
                    ?>

                    <fieldset>
                        <legend>Details</legend>
                        <p><label>* <strong>Name:</strong></label>
                            <input name="name" class="input1" type="text" required="required"<?php echo $mark; ?>></p>
                        <p><label>* <strong>E-Mail Address:</strong></label>
                            <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                        <p><label><strong>Site URL:</strong></label>
                            <input name="url" class="input1" type="url"<?php echo $mark; ?>></p>
                    </fieldset>

                    <?php
                    $reasonOptions = [];
                    if ($options->turnAff == 1) {
                        $reasonOptions[] = 'Affiliation';
                        $reasonOptions[] = 'Affiliation: Update';
                    }
                    if ($options->turnCon == 1) {
                        $reasonOptions[] = 'Comments';
                        $reasonOptions[] = 'Contact';
                        $reasonOptions[] = 'Questions/Concerns';
                    }
                    $reasonRequestParam = isset($_GET['reason']) ? trim($_GET['reason']) : '';
                    ?>
                    <fieldset>
                        <legend>Extra</legend>
                        <p><label>* <strong>Reason:</strong></label> <select name="reason" class="input1"
                                                                             required="required">
                                <option value="">---</option>
                                <?php
                                foreach ($reasonOptions as $reasonOption) {
                                    $selected = strcasecmp($reasonOption, $reasonRequestParam) === 0 ? ' selected' : '';
                                    echo '<option value="' . $reasonOption . '"' . $selected . '>' . $reasonOption . '</option>';
                                }
                                ?>
                            </select></p>
                        <?php
                        if ($options->turnCon == 1) {
                            ?>
                            <p class="tc">
                                <strong>Comments:</strong><br<?php echo $mark; ?>>
                                <textarea name="comments" class="input1" cols="9" rows="5"></textarea>
                            </p>
                            <?php
                        }
                        ?>
                    </fieldset>

                    <?php
                    if (
                        ($seahorses->getOption('captcha_opt') == 'y' && $options->spamAnti == 'y') ||
                        ($seahorses->getOption('antispam_opt') == 'y' && $options->spamCaptcha == 'y')
                    ) {
                        ?>
                        <fieldset>
                            <legend>Anti-SPAM</legend>
                            <?php
                            if ($seahorses->getOption('captcha_opt') == 'y' && $options->spamCaptcha == 'y') {
                                ?>
                                <p class="inputCaptcha">
                                    <label style="float: left; padding: 0 1%; width: 48%;">
                                        <strong>CAPTCHA</strong><br<?php echo $mark; ?>>
                                        Enter the letters/numbers as shown to the right:
                                    </label>
                                    <input name="captcha" class="input1" style="width: 48%;"
                                           type="text"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                                    <img alt="CAPTCHA Image" id="captcha" style="height: 80px; width: 170px;"
                                         title="CAPTCHA Image"
                                         src="<?php echo $my_website; ?>fun-captcha.inc.php?v=<?php echo $spamoptions->capthash; ?>"<?php echo $mark; ?>>
                                </p>
                                <?php
                            }
                            if ($seahorses->getOption('antispam_opt') == 'y' && $options->spamAnti == 'y') {
                                ?>
                                <p class="inputAntispam">
                                    <label style="float: left; padding: 0 1%; width: 48%;">
                                        <strong><?php echo $spamoptions->antipro; ?></strong><br<?php echo $mark; ?>>
                                        Enter the answer to the math problem:
                                    </label>
                                    <input name="mathproblem" class="input1" style="width: 48%;"
                                           type="text"<?php echo $mark; ?>>
                                </p>
                                <?php
                            }
                            ?>
                        </fieldset>
                        <?php
                    }
                    ?>

                    <fieldset>
                        <legend>Submit</legend>
                        <p class="tc">
                            <input name="action" class="input2" type="submit" value="Submit Form"<?php echo $mark; ?>>
                            <input class="input2" type="reset" value="Reset Form"<?php echo $mark; ?>>
                        </p>
                    </fieldset>
                </form>
                <?php
            } else {
                ?>
                <p>The affiliation/contact form is currently <strong>closed</strong>. If you are in dire need of
                    e-mailing the
                    owner of the <?php echo $z; ?>, you can do so <?php echo $hide_address; ?>.</p>
                <?php
            }
        }
    }

    if (
        (
            ($options->form == 'open') || ($options->formChoice == 'both' || $options->formChoice == 'list')
        ) &&
        (
        !isset($_POST['action'])
        )
    ) {
        ?>
        <p class="showCredits-LA-RF" style="text-align: center;">
            Powered by <?php echo $octopus->formatCredit(); ?>
        </p>
        <?php
    }
    ?>
</div>
