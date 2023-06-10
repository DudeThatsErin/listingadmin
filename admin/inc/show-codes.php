<div id="show-codes">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-codes.php>
     * @version          Robotess Fork
     */

    require_once('b.inc.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('fun.inc.php');
    require_once('fun-addons.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');

    /**
     * Get variables and options!
     */
    $options = new stdClass();
    $dArray = array('name', 'id');
    $fArray = array('codes', 'form');

    if (!isset($fKey) || ($fKey != '0' && $fKey != 0 && !in_array($fKey, $wolves->listingsList()))
    ) {
        $tigers->displayError('Script Error', 'The fanlisting ID is not set!', false);
    } else {
        $options->listingID = $tigers->cleanMys($fKey);
        $getItem = $wolves->getListings($options->listingID, 'object');
    }

    if (!isset($pretty_urls) || !in_array($pretty_urls, $get_yn_array)) {
        $options->prettyURL = false;
        $query = $tigers->cleanMys($_SERVER['QUERY_STRING']);
        if (isset($query) && !empty($query)) {
            $options->url = '?' . str_replace('&', '&#38;', $query) . '&#38;';
        } else {
            $options->url = '?';
        }
    } else {
        $options->prettyURL = true;
        if (isset($set_query) && preg_match("/^[A-Za-z0-9\/-_]+$/", $set_query)) {
            $options->url = $getItem->url . $set_query;
        } else {
            $options->url = $getItem->url;
        }
    }

    if (isset($show_all) && in_array($show_all, $get_yn_array)) {
        if ($show_all == 'y') {
            $options->type = 'all';
        } elseif ($show_all == 'n') {
            $options->type = 'list';
        }
    } else {
        $options->type = 'list';
    }

    if (isset($show_number) && in_array($show_number, $get_yn_array)) {
        if ($show_number == 'y') {
            $options->show = 'y';
        } elseif ($show_number == 'n') {
            $options->show = 'n';
        }
    } else {
        $options->show = 'y';
    }

    if (isset($sort_by) && in_array($sort_by, $dArray)) {
        if ($sort_by == 'name') {
            $options->sort = 'name';
        } elseif ($sort_by == 'id') {
            $options->sort = 'id';
        }
    } else {
        $options->sort = 'id';
    }

    if (isset($donor_link) && in_array($donor_link, $get_yn_array)) {
        if ($donor_link == 'y') {
            $options->link = 'y';
        } elseif ($donor_link == 'n') {
            $options->link = 'n';
        }
    } else {
        $options->link = 'y';
    }

    if ($getItem->markup == 'xhtml') {
        $options->markup = ' /';
    } else {
        $options->markup = '';
    }

    /**
     * Get category!
     */
    if (isset($_GET['c'])) {
        $c = $tigers->cleanMys($_GET['c']);
        if (!empty($c) || in_array($c, $cheetahs->categoryCodes())) {
            $v = $cheetahs->getCodeCategory($c);
            $z = !empty($v->catParent) ? $cheetahs->getCodesCatName($v->catParent) .
                ' &#187; ' . $v->catName : $v->catName;
            echo "<h3>$z</h3>\n";
            $select = "SELECT * FROM `$_ST[codes_sizes]` ORDER BY `sOrder` ASC";
            $true = $scorpions->query($select);
            if ($true == true) {
                while ($getSize = $scorpions->obj($true)) {
                    $sizeid = $getSize->sID;
                    $s1 = "SELECT * FROM `$_ST[codes]` WHERE `fNiq` = '" . $options->listingID .
                        "' AND `cCategory` = '$c' AND `cSize` = '$sizeid' AND `cPending` = '0'";
                    if ($options->sort == 'name') {
                        $s1 .= ' ORDER BY `cName` ASC';
                    } elseif ($options->sort == 'id') {
                        $s1 .= ' ORDER BY `cID` ' . $seahorses->getOption('codes_order');
                    }
                    $s2 = $scorpions->query($s1);
                    if ($scorpions->total($s2) > 0) {
                        echo '<h4>' . $cheetahs->getSize($sizeid) . "</h4>\n";
                        echo "<div class=\"codesBlock tc\">\n";
                        while ($getItem = $scorpions->obj($s2)) {
                            if ($getItem->cDonor == 0) {
                                echo '<img src="' . $seahorses->getOption('codes_img_http') . $getItem->cFile .
                                    '" alt=""' . $options->markup . ">\n";
                            } else if ($options->link == 'y') {
                                echo '<a href="' . $cheetahs->getDonor($getItem->cDonor, 'url') .
                                    '"><img src="' . $seahorses->getOption('codes_img_http') . $getItem->cFile .
                                    '" alt="Made by: ' . $cheetahs->getDonor($getItem->cDonor) .
                                    ' of ' . $octopus->shortURL($cheetahs->getDonor($getItem->cDonor, 'url')) .
                                    '" title="Made by: ' . $cheetahs->getDonor($getItem->cDonor) .
                                    ' of ' . $octopus->shortURL($cheetahs->getDonor($getItem->cDonor, 'url')) .
                                    '"' . $options->markup . "></a>\n";
                            } elseif ($options->link == 'n') {
                                echo '<img src="' . $seahorses->getOption('codes_img_http') . $getItem->cFile .
                                    '" alt=""' . $options->markup . ">\n";
                            }
                        }
                        echo "</div>\n";
                    }
                }
            }
        } else {
            $tigers->displayError('Script Error', 'Invalid category!', false);
        }
    } /**
     * Get code size~!
     */
    elseif (isset($_GET['s'])) {
        $s = $tigers->cleanMys($_GET['s']);
        if ($s != 'all') {
            if (!empty($s) || in_array($s, $cheetahs->sizesList())) {
                echo '<h3>' . $cheetahs->getSize($s) . "</h3>\n";
                $select = "SELECT * FROM `$_ST[codes]` WHERE `fNiq` = '" . $options->listingID .
                    "' AND `cSize` = '$s' AND `cPending` = '0'";
                if ($options->sort == 'name') {
                    $select .= ' ORDER BY `cName` ASC';
                } elseif ($options->sort == 'id') {
                    $select .= ' ORDER BY `cID` ' . $seahorses->getOption('codes_order');
                }
                $true = $scorpions->query($select);
                if ($true == true) {
                    if ($scorpions->total($true) > 0) {
                        echo '<div class="codesBlock tc">';
                        while ($getItem = $scorpions->obj($true)) {
                            if ($getItem->cDonor == 0) {
                                echo '<img src="' . $seahorses->getOption('codes_img_http') . $getItem->cFile .
                                    '" alt=""' . $options->markup . ">\n";
                            } else if ($options->link == 'y') {
                                echo '<a href="' . $cheetahs->getDonor($getItem->cDonor, 'url') .
                                    '"><img src="' . $seahorses->getOption('codes_img_http') .
                                    $getItem->cFile . '" alt="Made by: ' . $cheetahs->getDonor($getItem->cDonor) .
                                    ' of ' . $octopus->shortURL($cheetahs->getDonor($getItem->cDonor, 'url')) .
                                    '" title="Made by: ' . $cheetahs->getDonor($getItem->cDonor) .
                                    ' of ' . $octopus->shortURL($cheetahs->getDonor($getItem->cDonor, 'url')) .
                                    '"' . $options->markup . "></a>\n";
                            } elseif ($options->link == 'n') {
                                echo '<img src="' . $seahorses->getOption('codes_img_http') . $getItem->cFile .
                                    '" alt=""' . $options->markup . ">\n";
                            }
                        }
                        echo "</div>\n";
                    } else {
                        echo "<p style=\"text-align: center;\">Currently no codes under this size!</p>\n";
                    }
                }
            } else {
                $tigers->displayError('Script Error', 'Invalid size!', false);
            }
        } elseif ($s == 'all') {
            $cheetahs->codesDefault(
                $options->listingID, 'all', $options->show, $options->sort
            );
        }
    } /**
     * Nothing has been searched for, so let's get the default setup!
     */
    else {
        $count = is_countable($cheetahs->codesList('id', $options->listingID)) ? count($cheetahs->codesList('id', $options->listingID)) : 0;
        if ($count > 0) {
            $cheetahs->codesDefault(
                $options->listingID, $options->type, $options->show, $options->sort
            ); echo $checkCr;
        } else {
            echo $checkCr.'<p style="text-align: center;">Currently no codes uploaded for this' .
                " listing!</p>\n";
        }
        ?>
        <p class="showCredits-LA-RF" style="text-align: center;">
            Powered by <?php echo $octopus->formatCredit(); ?>
        </p>
        <?php
    }
    ?>
</div>
