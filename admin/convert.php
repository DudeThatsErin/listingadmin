<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <convert.php>
 * @version          Robotess Fork
 */

/**
 * In case buffering is turned off automatically:
 */
ob_start();

/**
 * Make sure we're logged in and got our necessary files~
 */
require('pro.inc.php');
require('vars.inc.php');

const IMPORT_OPERATION = 'import';
const EXPORT_OPERATION = 'export';
$operationTypes = [IMPORT_OPERATION, EXPORT_OPERATION];

/**
 * Now, if this file has been accessed via a form, let's start this jazz!
 */
if (
    isset($_POST) &&
    $_SERVER['REQUEST_METHOD'] == 'POST'
) {
    $opttype = $tigers->cleanMys($_POST['opttype']);
    if (empty($opttype) || !in_array($opttype, $operationTypes)) {
        $tigers->displayError('Form Error', 'Eeek, something went wrong there!' .
            ' In order to import/export something, the correct form for each must be' .
            ' used, m\'dear!', false);
    }

    if ($opttype === IMPORT_OPERATION) {
        $script = $tigers->cleanMys($_POST['script']);
        if (empty($script) || !array_key_exists($script, $get_script_array)) {
            $tigers->displayError('Form Error', 'The given script to export to' .
                ' appears to be invalid! :x', false);
        }
        $importcat = $tigers->cleanMys($_POST['importcat']);
        if (!array_key_exists($importcat, $get_import_cats_array)) {
            $tigers->displayError('Form Error', 'The import category you chose is' .
                ' invalid!', false);
        }
        if (
            isset($_FILES['importfile']) &&
            !empty($_FILES['importfile']['name']) &&
            !empty($_FILES['importfile']['tmp_name'])
        ) {
            $file = $_FILES['importfile'];
            if (!preg_match('/.txt$/', $file['name']) || filetype($file['tmp_name']) != 'file') {
                $tigers->displayError('Form Error', 'Only a <samp>.txt</samp> file is' .
                    ' allowed to be imported; if you\'re importing a .sql file, import it to' .
                    ' your database instead, and fill out the database settings in the form. :D',
                    false);
            }
        }
        $fanlistingid = $tigers->cleanMys($_POST['fanlistingid']);
        if (!empty($fanlistingid) && !in_array($fanlistingid, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'In order to import, you need to supply' .
                ' a fanlisting ID, love. :D', false);
        }
        $fanlistingid = empty($fanlistingid) ? '0' : $fanlistingid;
        $oldfanlistingid = $tigers->cleanMys($_POST['oldfanlistingid']);
        if (
            ($script == 'fanupdate' || $script == 'codesort') && $fanlistingid == '0'
        ) {
            $oldfanlistingid = '0';
        }
        $usedb = 0;
        if (
            (
                $script != 'bellabuffs' || (
                    $script == 'listingadmin' && ($importcat != 'affiliates' && $importcat != 'members')
                )
            ) &&
            (isset($_POST['tablename']) && !empty($_POST['tablename']))
        ) {
            $usedb = 1;
            if (
                isset($_POST['dbhost'], $_POST['dbuser']) && isset($_POST['dbname'])
            ) {
                $dbhost = $tigers->cleanMys($_POST['dbhost']);
                $dbuser = $tigers->cleanMys($_POST['dbuser']);
                $dbpass = $tigers->cleanMys($_POST['dbpass']);
                $dbname = $tigers->cleanMys($_POST['dbname']);
            }
        }
        $tablename = $tigers->cleanMys($_POST['tablename']);
        if (
            ($script != 'bellabuffs' && $script != 'listingadmin') &&
            (!isset($_POST['tablename']) || empty($tablename))
        ) {
            $tigers->displayError('Form Error', 'If you\'re importing a members' .
                ' list that is not Listing Admin or BellaBuffs, you must provide the members' .
                ' table.', false);
        }
        $tablenamecj = !isset($_POST['tablenamecj']) || empty($_POST['tablenamecj']) ?
            $tablename . '_catjoin' : $tigers->cleanMys($_POST['tablenamecj']);
        $importcp = !isset($_POST['importcp']) || empty($_POST['importcp']) ?
            'n' : $tigers->cleanMys($_POST['importcp']);
        $importjoined = !isset($_POST['importjoined']) || empty($_POST['importjoined']) ?
            'n' : $tigers->cleanMys($_POST['importjoined']);
        if (isset($_POST['toggle']) && $_POST['toggle'] == 'y') {
            $favefield = 1;
            if (isset($_POST['fave']) && (is_countable($_POST['fave']) ? count($_POST['fave']) : 0) > 0) {
                $faves = $_POST['fave'];
                $faves = array_map(array($tigers, 'cleanMys'), $faves);
            }
        } else {
            $favefield = 0;
        }

        /**
         * Check if we're using different database variables, and proceed from
         * there (if not, pull members from the same database; if so close our
         * current connection, and open a new one with the new database settings)
         */
        if ($usedb == 1) {
            if (
                $database_host == $dbhost && $database_user == $dbuser &&
                $database_name == $dbname
            ) {
                $query = "SELECT * FROM `$tablename`";
                if ($importcat == 'codes') {
                    if ($script == 'codesort') {
                        $query .= " WHERE `code_fl` = '$oldfanlistingid'";
                    } elseif ($script == 'listingadmin') {
                        $query .= " WHERE `fNiq` = '$oldfanlistingid'";
                    }
                } elseif ($script == 'fanupdate' && $importcat == 'updates') {
                    $query .= " b JOIN `$tablenamecj` j ON b.entry_id = j.entry_id WHERE" .
                        " j.cat_id = '$oldfanlistingid'";
                } elseif ($script == 'listingadmin') {
                    if ($importcat == 'updates') {
                        $query .= " WHERE `uCategory` LIKE '%!" . $oldfanlistingid . "!%'";
                    }
                }
                $result = $scorpions->query($query);
                if ($result == false) {
                    $tigers->displayError('Database Error', 'There was an error trying' .
                        ' to pull from the table you specified in the form.', true, $query);
                }
                $array = array();

                while ($item = $scorpions->obj($result)) {
                    /**
                     * Import Affiliates
                     */
                    if ($importcat == 'affiliates') {
                        if ($script == 'enthusiast') {
                            $array[$item->affiliateid] = (object)array(
                                'email' => $item->email,
                                'url' => $item->url,
                                'subject' => $item->title,
                                'added' => $item->added
                            );
                        } else {
                            $tigers->displayError('Database Error', 'Sorry, m\'love, you can only' .
                                ' import affiliates from a database with Enthusiast!', false);
                        }
                    } /**
                     * Import Categories
                     */
                    elseif ($importcat == 'categories') {
                        if ($script == 'enthusiast') {
                            $parents = array();
                            $array[$item->catid] = (object)array(
                                'id' => $item->catid,
                                'name' => htmlentities($item->catname, ENT_QUOTES, 'ISO-8859-15'),
                                'parent' => $item->catparent
                            );
                            if ($item->parent == 0) {
                                $parents[] = $item->catid;
                            }
                        } elseif ($script == 'listingadmin') {
                            $array[$item->catid] = (object)array(
                                'id' => $item->catid,
                                'name' => $item->catname,
                                'parent' => $item->parent
                            );
                        } else {
                            $tigers->displayError('Database Error', 'Sorry, m\'love, you can only' .
                                ' import categories from a database with Enthusiast or Listing Admin!',
                                false);
                        }
                    } /**
                     * Import Codes
                     */
                    elseif ($importcat == 'codes') {
                        if ($script == 'codesort') {
                            $array[$item->code_id] = (object)array(
                                'name' => '',
                                'image' => $item->code_image,
                                'category' => '',
                                'donor' => '',
                                'size' => $item->code_size,
                                'status' => ($item->code_approved == 'y' ? 0 : 1)
                            );
                        } elseif ($script == 'listingadmin') {
                            $array[$item->cID] = (object)array(
                                'name' => $item->cName,
                                'image' => $item->cFile,
                                'category' => $item->cCategory,
                                'donor' => $item->cDonor,
                                'size' => $item->cSize,
                                'status' => $item->cPending
                            );
                        }
                    } /**
                     * Import Joined
                     */
                    elseif ($importcat == 'joined') {
                        if ($script == 'enthusiast') {
                            $array[$item->joinedid] = (object)array(
                                'subject' => htmlentities($item->subject, ENT_QUOTES, 'ISO-8859-15'),
                                'url' => $item->url,
                                'category' => ($importjoined == 'y' ? $item->catid : ''),
                                'image' => $item->imagefile,
                                'madeby' => '',
                                'pending' => $item->pending,
                                'added' => $item->added
                            );
                        } elseif ($script == 'listingadmin') {
                            $array[$item->jID] = (object)array(
                                'subject' => $item->jSubject,
                                'url' => $item->jURL,
                                'category' => ($importjoined == 'y' ? $item->jCategory : ''),
                                'image' => $item->jImage,
                                'madeby' => $item->jMade,
                                'pending' => $item->jStatus,
                                'added' => $item->jAdd
                            );
                        }
                    } /**
                     * Import Members
                     */
                    elseif ($importcat == 'members') {
                        if (($favefield == 1) && $script == 'enthusiast') {
                            $h = array();
                            foreach ($faves as $f) {
                                if (!empty($f)) {
                                    if (empty($item->$f)) {
                                        $h[] = 'NONE';
                                    } else {
                                        $h[] = $item->$f;
                                    }
                                }
                            }
                            $ff = implode("|", $h);
                            $ff = '|' . trim($p, '|') . '|';
                        } else {
                            $ff = '';
                        }

                        $ps = $script == 'phpfanbase' ? '' : $item->password;
                        $ap = $script == 'phpfanbase' ? ($item->apr == 'y' ? 0 : 1) : $item->pending;
                        $se = $script == 'phpfanbase' ? ($item->hideemail == 'y' ? 1 : 0) :
                            ($item->showemail == 0 ? 1 : 0);
                        $da = $script == 'enthusiast' ? $item->added : date('Y-m-d');
                        $array[$item->email] = (object)array(
                            'name' => $item->name,
                            'email' => $item->email,
                            'url' => $item->url,
                            'country' => str_replace('USA', 'United States', $item->country),
                            'password' => $ps,
                            'fave' => $ff,
                            'show' => $se,
                            'pending' => $ap,
                            'update' => 'n',
                            'added' => $da
                        );
                    } /**
                     * Import Updates
                     */
                    elseif ($importcat == 'updates') {
                        if ($script == 'fanupdate') {
                            $array[$item->entry_id] = (object)array(
                                'title' => $item->title,
                                'entry' => htmlentities($item->body, ENT_QUOTES, 'ISO-8859-15'),
                                'dwpost' => '',
                                'dwpostopt' => '',
                                'ijpost' => '',
                                'ijpostopt' => '',
                                'ljpost' => '',
                                'ljpostopt' => '',
                                'status' => ($item->is_public == 1 ? 0 : 1),
                                'disabled' => $item->comments_on,
                                'added' => $item->added
                            );
                        } elseif ($script == 'listingadmin') {
                            $array[$item->uID] = (object)array(
                                'title' => $item->uTitle,
                                'entry' => $item->uEntry,
                                'dwpost' => ($importcp == 'y' ? $item->uDW : ''),
                                'dwpostopt' => ($importcp == 'y' ? $item->uDWOpt : ''),
                                'ijpost' => ($importcp == 'y' ? $item->uIJ : ''),
                                'ijpostopt' => ($importcp == 'y' ? $item->uIJOpt : ''),
                                'ljpost' => ($importcp == 'y' ? $item->uLJ : ''),
                                'ljpostopt' => ($importcp == 'y' ? $item->uLJOpt : ''),
                                'status' => $item->uPending,
                                'disabled' => $item->uDisabled,
                                'added' => $item->uAdded
                            );
                        } else {
                            $tigers->displayError('Database Error', 'Sorry, m\'love, you can only' .
                                ' import updates from FanUpdate and Listing Admin!', false);
                        }
                    }
                }
            } else {
                $scorpions->breach(0);
                $scorpions->initDB($dbhost, $dbuser, $dbpass, $dbname);

                $query = "SELECT * FROM `$tablename`";
                if ($importcat == 'codes') {
                    if ($script == 'codesort') {
                        $query .= " WHERE `code_fl` = '$oldfanlistingid'";
                    } elseif ($script == 'listingadmin') {
                        $query .= " WHERE `fNiq` = '$oldfanlistingid'";
                    }
                } elseif ($importcat == 'updates') {
                    if ($script == 'fanupdate') {
                        $query .= " b JOIN `$tablenamecj` j ON b.entry_id = j.entry_id WHERE" .
                            " j.cat_id = '$oldfanlistingid'";
                    } elseif ($script == 'listingadmin') {
                        $query .= " WHERE `uCategory` LIKE '%!" . $oldfanlistingid . "!%'";
                    }
                }
                $result = $scorpions->query($query);
                if ($result == false) {
                    exit("<h3>Database Error</h3>\n<p class=\"mysqlButton\"><span class=\"" .
                        'mysql\">Error:</span> There was an error trying to pull from the table' .
                        " you specified in the form.</p>\n<h3>MySQL Error(s)</h3>\n<p><em>$query" .
                        "</em><br>\n" .
                        $scorpions->database->error() .
                        "</p>\n");
                }

                $array = array();
                while ($item = $scorpions->obj($result)) {
                    /**
                     * Import Affiliates
                     */
                    if ($importcat == 'affiliates') {
                        if ($script == 'enthusiast') {
                            $array[$item->affiliateid] = (object)array(
                                'email' => $item->email,
                                'image' => $item->imagefile,
                                'url' => $item->url,
                                'subject' => $item->title,
                                'added' => $item->added
                            );
                        } else {
                            $tigers->displayError('Database Error', 'Sorry, m\'love, you can only' .
                                ' import affiliates from a database with Enthusiast!', false);
                        }
                    } /**
                     * Import Categories
                     */
                    elseif ($importcat == 'categories') {
                        if ($script == 'enthusiast') {
                            $parents = array();
                            $array[$item->catid] = (object)array(
                                'id' => $item->catid,
                                'name' => htmlentities($item->catname, ENT_QUOTES, 'ISO-8859-15'),
                                'parent' => $item->catparent
                            );
                            if ($item->parent == 0) {
                                $parents[] = $item->catid;
                            }
                        } elseif ($script == 'listingadmin') {
                            $array[$item->catid] = (object)array(
                                'id' => $item->catid,
                                'name' => $item->catname,
                                'parent' => $item->parent
                            );
                        } else {
                            $tigers->displayError('Database Error', 'Sorry, m\'love, you can only' .
                                ' import categories from a database with Enthusiast or Listing Admin!',
                                false);
                        }
                    } /**
                     * Import Codes
                     */
                    elseif ($importcat == 'codes') {
                        if ($script == 'codesort') {
                            $array[$item->code_id] = (object)array(
                                'name' => '',
                                'image' => $item->code_image,
                                'category' => '',
                                'donor' => '',
                                'size' => $item->code_size,
                                'status' => ($item->code_approved == 'y' ? 0 : 1)
                            );
                        } elseif ($script == 'listingadmin') {
                            $array[$item->cID] = (object)array(
                                'name' => $item->cName,
                                'image' => $item->cFile,
                                'category' => $item->cCategory,
                                'donor' => $item->cDonor,
                                'size' => $item->cSize,
                                'status' => $item->cPending
                            );
                        }
                    } /**
                     * Import Joined
                     */
                    elseif ($importcat == 'joined') {
                        if ($script == 'enthusiast') {
                            $array[$item->joinedid] = (object)array(
                                'subject' => htmlentities($item->subject, ENT_QUOTES, 'ISO-8859-15'),
                                'url' => $item->url,
                                'category' => ($importjoined == 'y' ? $item->catid : ''),
                                'image' => $item->imagefile,
                                'madeby' => '',
                                'pending' => $item->pending,
                                'added' => $item->added
                            );
                        } elseif ($script == 'listingadmin') {
                            $array[$item->jID] = (object)array(
                                'subject' => $item->jSubject,
                                'url' => $item->jURL,
                                'category' => ($importjoined == 'y' ? $item->jCategory : ''),
                                'image' => $item->jImage,
                                'madeby' => $item->jMade,
                                'pending' => $item->jStatus,
                                'added' => $item->jAdd
                            );
                        }
                    } /**
                     * Import Members
                     */
                    elseif ($importcat == 'members') {
                        if (($favefield == 1) && $script == 'enthusiast') {
                            $h = array();
                            foreach ($faves as $f) {
                                if (!empty($f)) {
                                    if (empty($item->$f)) {
                                        $h[] = 'NONE';
                                    } else {
                                        $h[] = $item->$f;
                                    }
                                }
                            }
                            $ff = implode("|", $h);
                            $ff = '|' . trim($ff, '|') . '|';
                        } else {
                            $ff = '';
                        }

                        $ps = $script == 'phpfanbase' ? '' : $item->password;
                        $ap = $script == 'phpfanbase' ? ($item->apr == 'y' ? 0 : 1) : $item->pending;
                        $se = $script == 'phpfanbase' ? ($item->hideemail == 'y' ? 1 : 0) :
                            ($item->showemail == 0 ? 1 : 0);
                        $da = $script == 'enthusiast' ? $item->added : date('Y-m-d');
                        $array[$item->email] = (object)array(
                            'name' => $item->name,
                            'email' => $item->email,
                            'url' => $item->url,
                            'country' => str_replace('USA', 'United States', $item->country),
                            'password' => $ps,
                            'fave' => $ff,
                            'show' => $se,
                            'pending' => $ap,
                            'update' => 'n',
                            'added' => $da
                        );
                    } /**
                     * Import Updates
                     */
                    elseif ($importcat == 'updates') {
                        if ($script == 'fanupdate') {
                            $array[$item->entry_id] = (object)array(
                                'title' => $item->title,
                                'entry' => $item->body,
                                'dwpost' => '',
                                'dwpostopt' => '',
                                'ijpost' => '',
                                'ijpostopt' => '',
                                'ljpost' => '',
                                'ljpostopt' => '',
                                'status' => ($item->is_public == 1 ? 0 : 1),
                                'disabled' => $item->comments_on,
                                'added' => $item->added
                            );
                        } elseif ($script == 'listingadmin') {
                            $array[$item->uID] = (object)array(
                                'title' => $item->uTitle,
                                'entry' => $item->uEntry,
                                'dwpost' => ($importcp == 'y' ? $item->uDW : ''),
                                'dwpostopt' => ($importcp == 'y' ? $item->uDWOpt : ''),
                                'ijpost' => ($importcp == 'y' ? $item->uIJ : ''),
                                'ijpostopt' => ($importcp == 'y' ? $item->uIJOpt : ''),
                                'ljpost' => ($importcp == 'y' ? $item->uLJ : ''),
                                'ljpostopt' => ($importcp == 'y' ? $item->uLJOpt : ''),
                                'status' => $item->uPending,
                                'disabled' => $item->uDisabled,
                                'added' => $item->uAdded
                            );
                        } else {
                            $tigers->displayError('Database Error', 'Sorry, m\'love, you can only' .
                                ' import updates from FanUpdate and Listing Admin!', false);
                        }
                    }
                }
                $scorpions->breach(0);
                $scorpions->breach(1);
            }
        } /**
         * Grab the members from the text file instead! \o/
         */
        elseif ($usedb == 0) {
            if (isset($_FILES['importfile']) && !empty($_FILES['importfile']['name'])) {
                $members = file($_FILES['importfile']['tmp_name']);
                if ((is_countable($members) ? count($members) : 0) == 0) {
                    $tigers->displayError('File Error', 'The file you supplied appears' .
                        ' to be empty!', false);
                }

                $array = array();
                foreach ($members as $m) {
                    if ($script == 'bellabuffs') {
                        if ($importcat == 'affiliates') {
                            [$button, $name, $email, $url, $sitename] = explode(',', $m);
                            $array[$sitename] = (object)array(
                                'name' => $name,
                                'email' => $seahorses->formatExport($email, 'bb', 'decode'),
                                'url' => $url,
                                'subject' => $sitename,
                                'added' => date('Y-m-d')
                            );
                        } elseif ($importcat == 'members') {
                            [$name, $email, $show, $url, $country, $fave] = explode(',', $m);
                            $se = $show == 'yes' ? 0 : 1;
                            $array[$email] = (object)array(
                                'name' => $name,
                                'email' => $seahorses->formatExport($email, 'bb', 'decode'),
                                'url' => $url,
                                'country' => $country,
                                'password' => '',
                                'fave' => $seahorses->formatExport($fave, 'bb', 'decode'),
                                'show' => $se,
                                'pending' => 0,
                                'update' => 'n',
                                'added' => date('Y-m-d')
                            );
                        }
                    } elseif ($script == 'listingadmin') {
                        if ($importcat == 'affiliates') {
                            [$subject, $email, $url, $image, $added] = explode('||', $m);
                            $array[$subject] = (object)array(
                                'email' => $seahorses->formatExport($email, 'la', 'decode'),
                                'url' => $seahorses->formatExport($url, 'la', 'decode'),
                                'subject' => $subject,
                                'image' => $image,
                                'added' => $added
                            );
                        } elseif ($importcat == 'members') {
                            [
                                $email,
                                $listing,
                                $name,
                                $url,
                                $country,
                                $password,
                                $fave,
                                $visible,
                                $pending,
                                $update,
                                $added
                            ] = explode('__', $m);
                            $array[$email] = (object)array(
                                'name' => $name,
                                'email' => $seahorses->formatExport($email, 'la', 'decode'),
                                'url' => $seahorses->formatExport($url, 'la', 'decode'),
                                'country' => $country,
                                'password' => $password,
                                'fave' => $fave,
                                'show' => $visible,
                                'pending' => $pending,
                                'update' => $update,
                                'added' => $added
                            );
                        }
                    }
                }
            }
        }

        /**
         * Now that we have our array, let's add our members! :D :D :D
         */
        if (empty($array)) {
            $tigers->displayError('Script Error', 'Oops! There was an answer fetching' .
                ' the data to import!', false);
        }

        foreach ($array as $member => $obj) {
            if ($importcat == 'affiliates') {
                $insert = "INSERT INTO `$_ST[affiliates]` (`fNiq`, `aSubject`, `aEmail`," .
                    " `aURL`, `aImage`, `aAdd`) VALUES ('" . $fanlistingid . "', '" . $obj->subject .
                    "', '" . $obj->email . "', '" . $obj->url .
                    "', '" . $obj->image . "', '" . $obj->added . "')";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'There was an error inserting' .
                        ' the affiliate into the listing.', true, $insert);
                } else {
                    echo $tigers->displaySuccess('The <strong>' . $obj->subject . '</strong> (' .
                        '<samp>' . $obj->email . '</samp>) affiliate was added to the listing! :D');
                }
            } elseif ($importcat == 'categories') {
                $p = $script == 'enthusiast' ? (in_array($obj->parent, $parents)
                    ? $obj->parent : '0') : $obj->parent;
                $insert = "INSERT INTO `$_ST[categories]` (`catid`, `catname`, `parent`)" .
                    " VALUES ('" . $obj->id . "', '" . $obj->name . "', '$p')";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'There was an error inserting' .
                        ' the category into the listing.', true, $insert);
                } else {
                    echo $tigers->displaySuccess('The <strong>' . $obj->catname . '</strong>' .
                        ' category was added to the listing! :D');
                }
            } elseif ($importcat == 'codes') {
                $insert = "INSERT INTO `$_ST[codes]` (`fNiq`, `cName`, `cFile`, `cCategory`," .
                    " `cSize`, `cDonor`, `cPending`, `cAdded`) VALUES ('$fanlistingid', '" . $obj->name .
                    "', '" . $obj->image . "', '" . $obj->category . "', '" . $obj->size .
                    "', '" . $obj->donor . "', '" . $obj->status . "', NOW())";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'There was an error inserting' .
                        ' the code into the listing.', true, $insert);
                } else {
                    echo $tigers->displaySuccess('The <strong>' . $obj->image . '</strong>' .
                        ' code was added to the listing! :D');
                }
            } elseif ($importcat == 'joined') {
                $insert = "INSERT INTO `$_ST[joined]` (`jSubject`, `jURL`, `jImage`," .
                    " `jCategory`, `jMade`, `jStatus`, `jAdd`) VALUES ('" . $obj->subject . "'," .
                    " '" . $obj->url . "', '" . $obj->image . "', '" . $obj->category . "'," .
                    " '" . $obj->madeby . "', '" . $obj->pending . "', '" . $obj->added . "')";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'There was an error inserting' .
                        ' the joined listing into the listing.', true, $insert);
                } else {
                    echo $tigers->displaySuccess('The <strong>' . $obj->subject . '</strong>' .
                        ' joined listing was added to the database! :D');
                }
            } elseif ($importcat == 'members') {
                $mem = trim($member);
                if (!empty($mem) && !empty($obj)) {
                    $insert = "INSERT INTO `$_ST[members]` (`mEmail`, `fNiq`, `mName`, `mURL`," .
                        ' `mCountry`, `mPassword`, `mExtra`, `mVisible`, `mPending`, `mUpdate`,' .
                        " `mAdd`) VALUES ('" . $obj->email . "', '" . $fanlistingid .
                        "', '" . htmlentities(html_entity_decode($obj->name), ENT_QUOTES, 'UTF-8') .
                        "', '" . $obj->url . "', '" . $obj->country . "', '" . $obj->password .
                        "', '" . $obj->fave . "', '" . $obj->show . "', '" . $obj->pending .
                        "', '" . $obj->update . "', '" . $obj->added . "')";
                    $scorpions->query("SET NAMES 'utf8';");
                    $true = $scorpions->query($insert);
                    if ($true == false) {
                        $tigers->displayError('Database Error', 'There was an error inserting' .
                            ' the member into the listing.', true, $insert);
                    } else {
                        echo $tigers->displaySuccess('The <strong>' . $obj->name . '</strong> (' .
                            '<samp>' . $obj->email . '</samp>) member was added to the listing! :D');
                    }
                }
            } elseif ($importcat == 'updates') {
                $insert = "INSERT INTO `$_ST[updates]` (`uTitle`, `uCategory`, `uEntry`," .
                    ' `uDW`, `uDWOpt`, `uIJ`, `uIJOpt`, `uLJ`, `uLJOpt`, `uPending`, `uDisabled`,' .
                    " `uAdded`) VALUES ('" . $obj->title . "', '!" . $fanlistingid .
                    "!', '" . $obj->entry . "', '" . $obj->dwpost . "', '" . $obj->dwpostopt .
                    "', '" . $obj->ijpost . "', '" . $obj->ijpostopt . "', '" . $obj->ljpost .
                    "', '" . $obj->ljpostopt . "', '" . $obj->status . "'," .
                    " '" . $obj->disabled . "', '" . $obj->added . "')";
                $scorpions->query("SET NAMES 'utf8';");
                $true = $scorpions->query($insert);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'There was an error inserting' .
                        ' the update into the listing.', true, $insert);
                } else {
                    echo $tigers->displaySuccess('The <strong>' . $obj->title . '</strong> (' .
                        '<samp>' . date('F jS, Y', strtotime($obj->added)) .
                        '</samp>) update was added to the listing! :D');
                }
            }
        }
    } /**
     * Not a whole lot of code going on here compared to above, as we're only
     * pulling and prompting~ :D
     */
    elseif ($opttype === EXPORT_OPERATION) {
        $script = $tigers->cleanMys($_POST['script']);
        if (empty($script) || !array_key_exists($script, $get_script_array)) {
            $tigers->displayError('Form Error', 'The given script to export to' .
                ' appears to be invalid! :x', false);
        }
        $exportcat = $tigers->cleanMys($_POST['exportcat']);
        if (empty($exportcat) || !array_key_exists($exportcat, $get_export_cats_array)) {
            $tigers->displayError('Form Error', 'You can only export members and' .
                ' affiliates, m\'dear!', false);
        }
        $fanlistingid = $tigers->cleanMys($_POST['fanlistingid']);
        if (empty($fanlistingid) || !in_array($fanlistingid, $wolves->listingsList())) {
            $tigers->displayError('Form Error', 'In order to export affiliates' .
                '/members, you need to supply a fanlisting ID, love. :D', false);
        }

        if ($exportcat == 'affiliates') {
            $array = $rabbits->affiliatesList($fanlistingid, 'asc');
            $str = '';
            foreach ($array as $a) {
                $affiliate = $rabbits->getAffiliate($a, 'id', $fanlistingid);
                if ($script == 'enthusiast') {
                    $str .= "('" . $affiliate->aURL . "', '" . $affiliate->aSubject .
                        "', '" . $affiliate->aImage . "', '" . $affiliate->aEmail .
                        "', '" . $affiliate->aAdd . "'),\n";
                } elseif ($script == 'listingadmin') {
                    $str .= $affiliate->aSubject . '||' . $seahorses->formatExport($affiliate->aEmail) .
                        '||' . $seahorses->formatExport($affiliate->aURL) . '||' . $affiliate->aImage .
                        '||' . $affiliate->aAdd . "__\n";
                }
            }
        } else {
            $array = $snakes->membersList($fanlistingid);
            $listing = $wolves->getListings($fanlistingid, 'object');
            $str = '';
            foreach ($array as $a) {
                $member = $snakes->getMembers($a, 'id', 'object', $fanlistingid);
                if ($script == 'bellabuffs') {
                    $favefields = $tigers->emptyarray(explode('|', $listing->fave_fields));
                    $ff = (is_countable($favefields) ? count($favefields) : 0) == 1 ? str_replace(',', '|',
                        trim(str_replace('|', '', $member->mFave))) : '';
                    $se = $member->mPending == 0 ? 'yes' : 'no';
                    $str .= $member->mName . ',' . $seahorses->formatExport($member->mEmail) .
                        ',' . $se . ',' . $member->mURL . ',' . $ff . "\n";
                } elseif ($script == 'enthusiast') {
                    $se = $member->mVisible == 1 ? 0 : 1;
                    $str .= "('" . $member->mEmail . "', '" . $member->mName . "', '" . $member->mCountry .
                        "', '" . $member->mURL . "',";
                    if ($listing->fave_fields != '') {
                        $ff = $tigers->emptyarray(explode('|', $listing->fave_fields));
                        $mm = $tigers->emptyarray(explode('|', $member->mExtra));
                        if (!empty($mm) && (is_countable($mm) ? count($mm) : 0) > 0) {
                            foreach ($mm as $k) {
                                $str .= " '" . str_replace('NONE', '', $k) . "',";
                            }
                        } else {
                            foreach ($ff as $f) {
                                $str .= " '',";
                            }
                        }
                    }
                    $str .= " '" . $member->mPending . "', '" . $member->mPassword .
                        "', " . $se . ", 1, '" . $member->mAdd . "'),\n";
                } elseif ($script == 'listingadmin') {
                    $str .= $seahorses->formatExport($member->mEmail, 'la') . '__0__' . $member->mName .
                        '__' . $seahorses->formatExport($member->mURL, 'la') . '__' . $member->mCountry .
                        '__' . $member->mPassword . '__' . $member->mExtra . '__' . $member->mVisible .
                        '__' . $member->mPending . '__' . $member->mUpdate . '__' . $member->mAdd . "\n";
                }
            }
        }

        /*
         *  Format string before we do anything :D
         */
        $finalstr = '';
        if ($script == 'bellabuffs') {
            $finalstr .= trim($str, "\n");
        } elseif ($script == 'enthusiast') {
            if ($exportcat == 'affiliates') {
                $finalstr .= "CREATE TABLE IF NOT EXISTS `table` (
 `affiliateid` int(5) NOT NULL auto_increment,
 `url` varchar(255) NOT NULL default '',
 `title` varchar(255) NOT NULL default '',
 `imagefile` varchar(255) default NULL,
 `email` varchar(255) NOT NULL default '',
 `added` date NOT NULL default '1970-01-01',
 PRIMARY KEY (`affiliateid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `table` (`url`, `title`, `imagefile`, `email`, `added`) VALUES
";
            } else {
                $finalstr .= "CREATE TABLE IF NOT EXISTS `table` (
 `email` varchar(64) NOT NULL default '',
 `name` varchar(128) NOT NULL default '',
 `country` varchar(128) NOT NULL default '',
 `url` varchar(255) default NULL,";
                if (!empty($listing->fave_fields)) {
                    $ff = $tigers->emptyarray(explode('|', $listing->fave_fields));
                    foreach ($ff as $f) {
                        $d = strtolower(str_replace(array('(', ')'), '', $f));
                        $finalstr .= "
 `$d` varchar(255) NOT NULL,";
                    }
                }
                $finalstr .= "
 `pending` tinyint(1) NOT NULL default '0',
 `password` varchar(255) NOT NULL default '',
 `showemail` tinyint(1) NOT NULL default '1',
 `showurl` tinyint(1) NOT NULL default '1',
 `added` date default NULL,
 PRIMARY KEY (`email`),
 FULLTEXT KEY `email` (`email`, `name`, `country`, `url`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

";
                $finalstr .= 'INSERT INTO `table` (`email`, `name`, `country`, `url`,';
                if (!empty($listing->fave_fields)) {
                    $ff = $tigers->emptyarray(explode('|', $listing->fave_fields));
                    foreach ($ff as $f) {
                        $d = strtolower(str_replace(array('(', ')'), '', $f));
                        $finalstr .= " `$d`,";
                    }
                }
                $finalstr .= " `pending`, `password`, `showemail`, `showurl`, `added`) VALUES\n";
                $finalstr .= rtrim($str, ",\n") . ';';
            }
        } elseif ($script == 'listingadmin') {
            $finalstr .= trim($str, "\n") . "\n";
        }

        /*
         *  Prompt the user to save the file; depending on the script, it'll be
         *  a .sql or .txt file~
         */
        if (isset($_POST['savefile']) && $_POST['savefile'] == 'y') {
            $headername = $script == 'enthusiast' ? 'table.sql' : 'members.txt';
            $headertype = $script == 'enthusiast' ? 'text/x-sql' : 'text/plain';
            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename=' . $headername);
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: ' . $headertype);

            echo $finalstr;
            exit();
        } else {
            $sw = $script == 'enthusiast' || $script == 'phpfanbase' ? '.sql' : '.txt';
            echo '<p class="noteButton">Copy and paste the text below into a blank' .
                " file, and save it with a <samp>$sw</samp> extension.</p>\n";
            echo "<code>$finalstr</code>\n";
        }
    }
}
