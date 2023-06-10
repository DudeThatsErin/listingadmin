<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-members.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('snakes')) {
class snakes {

public $next = '';
public int $page = 1;
public $prev = '';
public int $range = 10;

/**
 * @function  $snakes->membersList()
 *
 * @param     $i , int; listing ID; optional
 * @param     $p , int; 0 for approved, 1 for all members; optional
 */
public function membersList($i = 'n', $p = 1, $b = '', $s = '')
{
    global $_ST, $get_type_id_array, $scorpions, $tigers, $wolves;

    if ($i != 'n') {
        $d = $tigers->cleanMys($i);
        $listing = $wolves->getListings($d, 'object');
        if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
            $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                $listing->dbname);
        }

        /**
         * If we're sorting by approved or search criteria, start processing
         * both! :D Go through each crosslisting option, as well as build the
         * query so we have it on hand~
         */
        $q = '';
        if ($p == 0 || ($b != '' && $s != '')) {
            $m = '';
            if ($p == 0) {
                if ($listing->dblist == 1 && $listing->dbtype == 'enth') {
                    $m .= " `pending` = '0' AND";
                } elseif ($listing->dblist == 1 && $listing->dbtype == 'fanupdate') {
                    $m .= " `apr` = 'y' AND";
                } elseif ($listing->dblist == 1 && $listing->dbtype == 'listingadmin') {
                    $m .= " `mPending` = '0' AND";
                } elseif ($listing->dblist == 0) {
                    $m .= " `mPending` = '0' AND";
                }
            }
            $v = $listing->dblist == 1 ? ($listing->dbtype == 'listingadmin' ?
                $get_type_id_array['listingadmin'] : $get_type_id_array['enth']) :
                $get_type_id_array['listingadmin'];
            if ($b != '' && array_key_exists($b, $v) && $s != '') {
                $m = ' `' . $v[$b] . "` LIKE '%" . $scorpions->escape(stripslashes($s)) . "%' AND";
            }
            if ($m != '') {
                $k = ($listing->dblist == 1 && $listing->dbtype == 'listingadmin')
                || $listing->dblist == 0 ? ' AND' : ' WHERE';
                $q .= $k . trim($m, ' AND');
            }
        }

        /*
         *  Now we get our queries! \o/
         */
        if ($listing->dblist == 1) {
            $dbtable = $listing->dbtabl;
            $dbflid = $listing->dbflid;
            if ($listing->dbtype == 'enth') {
                $select = "SELECT * FROM `$dbtable`";
            } elseif ($listing->dbtype == 'fanupdate') {
                $select = "SELECT * FROM `$dbtable`";
            } elseif ($listing->dbtype == 'listingadmin') {
                $select = "SELECT * FROM `$dbtable` WHERE `fNiq` = '$dbflid'";
            }
        } else {
            $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '$d'";
        }
        if ($q != '') {
            $select .= $q;
        }
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' select the members.|Make sure your members table exists.', true, $select);
        }

        $all = array();
        while ($getItem = $scorpions->obj($true, 0)) {
            if ($listing->dblist == 1 && $listing->dbtype == 'enth') {
                $all[] = $getItem->email;
            } elseif ($listing->dblist == 1 && $listing->dbtype == 'fanupdate') {
                $all[] = $getItem->id;
            } else {
                $all[] = $getItem->mID;
            }
        }
    } else {
        $select = "SELECT * FROM `$_ST[members]`";
        $true = $scorpions->query($select);
        if ($true == false) {
            $tigers->displayError('Database Error', 'The script was unable to' .
                ' select the members.|Make sure your members table exists.', false);
        }

        $all = array();
        while ($getItem = $scorpions->obj($true, 0)) {
            $all[] = $getItem->mID;
        }
    }

    if ($listing->dblist == 1) {
        $scorpions->breach(0);
        $scorpions->breach(1);
    }

    return $all;
}

/**
 * @function    $snakes->allMembers()
 * @returns     $snakes->membersList()
 * @deprecated  2.3beta
 */
public function allMembers($i = 'n', $p = 1)
{
    return $this->membersList($i, $p);
}

/**
 * @function  $snakes->sortMembers()
 *
 * @param     $i , int; listing ID
 * @param     $s , string; search title; optional
 * @param     $a , array; search data; optional
 *
 * @desc      Sorts members according to listing ID, database settings
 * and search options (if provided) and returns array :D
 * @since     2.3beta
 */
public function sortMembers($i, $s = '', $a = array())
{
    global $_ST, $get_type_id_array, $laantispam, $scorpions, $tigers, $wolves;

    /*
     *  Are we crosslisting to another script/database? Let's seeee~
     */
    $listing = $wolves->getListings($i, 'object');
    if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
        $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
            $listing->dbname);
    }

    /**
     * Build search query, if provided~
     */
    $q = '';
    if ($s == 'members' && (is_array($a) && count($a) > 0)) {
        $findarray = $listing->dblist == 1 ? ($listing->dbtype == 'listingadmin' ?
            'listingadmin' : 'other') : 'listingadmin';
        $typearray = $get_type_id_array[$findarray];
        $search = new stdClass();
        foreach ($a as $k => $v) {
            $search->$k = $v;
        }
        $typeid = $tigers->cleanMys($search->searchType);
        if (!array_key_exists($typeid, $typearray)) {
            $typeid = 'name';
        }
        $textid = $laantispam->clean($tigers->cleanMys($search->searchText), 'n', 'y', 'declean');
        $q .= ' AND `' . $typearray[$typeid] . "` LIKE '%$textid%'";
    }

    if ($listing->dblist == 1) {
        $dbtabl = $listing->dbtabl;
        if ($listing->dbtype == 'enth') {
            $select = "SELECT * FROM `$dbtabl`";
            if ($q != '') {
                $select .= $q;
            }
            $select .= ' ORDER BY `added` DESC';
        } elseif ($listing->dbtype == 'fanupdate') {
            $select = "SELECT * FROM `$dbtabl`";
            if ($q != '') {
                $select .= $q;
            }
            $select .= ' ORDER BY `id` DESC';
        } elseif ($listing->dbtype == 'listingadmin') {
            $listidnow = $listing->dbflid;
            $select = "SELECT * FROM `$dbtabl` WHERE `fNiq` = '$listidnow'";
            if ($q != '') {
                $select .= $q;
            }
            $select .= ' ORDER BY `mAdd` DESC';
        }
    } else {
        $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '$i'";
        if ($q != '') {
            $select .= $q;
        }
        $select .= ' ORDER BY `mAdd` DESC';
    }
    $count = $scorpions->counts($select, 1);
    $true = $scorpions->query($select);
    if ($true == false) {
        $tigers->displayError('Database Error', 'The script could not pull the' .
            ' members from the database.', false);
    }

    $array = array();
    $prearray = array();
    if ($count->rows > 0) {
        while ($item = $scorpions->obj($true, 0)) {
            $d = $listing->dblist == 1 && $listing->dbtype != 'listingadmin' ? $item->email : $item->mID;
            $h = $listing->dblist == 1 && $listing->dbtype != 'listingadmin' ?
                ($listing->dbtype == 'enth' ? $item->added : $item->id) : $item->mAdd;
            $prearray[$d] = $h;
        }
        uasort($prearray, array($this, 'datecompare'));
        arsort($prearray);
        foreach ($prearray as $k => $v) {
            $array[] = array(
                'mID' => $k,
                'mDate' => $v
            );
        }
    }

    if ($listing->dblist == 1) {
        $scorpions->breach(0);
        $scorpions->breach(1);
    }

    return $array;
}

/**
 * @function  $snakes->membersPage()
 * @param     $i , int; listing ID
 * @param     $p , string; page section
 * @desc      Returns forms and tables and... stuff :x
 * @since     2.3beta
 */
public function membersPage($i, $p) {
global $_ST;

switch ($p) {
case 'head':
?>
<form action="members.php?listing=<?php echo $i; ?>" method="post">
    <input name="listing" type="hidden" value="<?php echo $i; ?>">

    <table class="index">
        <thead>
        <tr>
            <th>&#160;</th>
            <th>Status</th>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Action</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td class="tc" colspan="6">With Checked:
                <input name="action" class="input2" type="submit" value="Approve">
                <input name="action" class="input2" type="submit" value="Delete">
                <input name="action" class="input2" type="submit" value="Update">
            </td>
        </tr>
        </tfoot>
        <?php
        break;

        case 'form':
            ?>
            <form action="members.php" method="get">
                <p class="noMargin">
                    <input name="listing" type="hidden" value="<?php echo $i; ?>">
                    <input name="g" type="hidden" value="searchMembers">
                </p>

                <fieldset>
                    <legend>Search Members</legend>
                    <div id="memSearch">
                        <p style="float: left; margin: 0; padding: 0 1% 0 0; width: 19%;">
                            <select name="s" class="input4">
                                <option value="email">E-Mail</option>
                                <option selected="selected" value="name">Name</option>
                                <option value="url">URL</option>
                            </select></p>
                        <p style="float: left; margin: 0 1% 0 0; padding: 0; width: 59%;">
                            <input name="q" id="text" class="input4" type="text">
                        </p>
                        <p style="float: right; margin: 0 1% 0 0; padding: 0; width: 19%;">
                            <input class="input2" type="submit" value="Search Members">
                        </p>
                        <p class="clear"></p>
                    </div>
                </fieldset>
            </form>
            <?php
            break;
        }
        }

        /**
         * @function  $snakes->getMembers()
         *
         * @param     $i , int, string; member ID or member e-mail
         * @param     $p , string; id or e-mail
         * @param     $b , string; return by object or array
         * @param     $e , int; listing ID; optional
         */
        public function getMembers($i, $p = 'id', $b = 'array', $e = '')
        {
            global $_ST, $getlistingid, $options, $scorpions, $tigers, $wolves;

            if ($e != '' && is_numeric($e)) {
                $d = $tigers->cleanMys($e);
            } elseif (isset($options) && !empty($options)) {
                $d = $tigers->cleanMys($options->listingID);
            } elseif (isset($getlistingid) && !empty($getlistingid)) {
                $d = $tigers->cleanMys($getlistingid);
            }

            $listing = $wolves->getListings($d, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            if ($listing->dblist == 1) {
                $dbtable = $listing->dbtabl;
                $dbflid = $listing->dbflid;
                if ($listing->dbtype == 'enth') {
                    $select = "SELECT * FROM `$dbtable`";
                    if ($p == 'id' || $p == 'email') {
                        $select .= " WHERE LOWER(`email`) = '$i'";
                    }
                } elseif ($listing->dbtype == 'fanbase') {
                    $select = "SELECT * FROM `$dbtable`";
                    if ($p == 'id') {
                        $select .= " WHERE `id` = '$i'";
                    } elseif ($p == 'email') {
                        $select .= " WHERE LOWER(`email`) = '$i'";
                    }
                } elseif ($listing->dbtype == 'listingadmin') {
                    $select = "SELECT * FROM `$dbtable` WHERE `fNiq` = '$dbflid'";
                    if ($p == 'id') {
                        $select .= " AND `mID` = '$i'";
                    } elseif ($p == 'email') {
                        $select .= " AND LOWER(`mEmail`) = '$i'";
                    }
                }
            } else {
                $select = "SELECT * FROM `$_ST[members]`";
                if ($p == 'id') {
                    $select .= " WHERE `mID` = '$i'";
                } elseif ($p == 'email') {
                    $select .= " WHERE LOWER(`mEmail`) = '$i'";
                }
            }
            $select .= ' LIMIT 1';
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' select the specified member.|Make sure your members table exists.',
                    true, $select);
            }

            $getItem = $scorpions->obj($true, 0);
            if ($b == 'array') {
                if ($listing->dblist == 1) {
                    if ($listing->dbtype == 'enth') {
                        $sendArray = array(
                            'mID' => $getItem->email,
                            'mName' => $getItem->name,
                            'mEmail' => $getItem->email,
                            'mURL' => $getItem->url,
                            'mPending' => $getItem->pending
                        );
                    } elseif ($listing->dbtype == 'fanbase') {
                        $pending = $getItem->apr == 'y' ? 0 : 1;
                        $sendArray = array(
                            'mID' => $getItem->id,
                            'mName' => $getItem->name,
                            'mEmail' => $getItem->email,
                            'mURL' => $getItem->url,
                            'mPending' => $pending
                        );
                    } elseif ($listing->dbtype == 'listingadmin') {
                        $sendArray = $scorpions->obj($true, 1);
                    }
                } else {
                    $sendArray = $scorpions->obj($true, 1);
                }
            } elseif ($b == 'object') {
                if ($listing->dblist == 1) {
                    if ($listing->dbtype == 'enth') {
                        $sendArray = false;
                        $sendArray->mID = $getItem->email;
                        $sendArray->mName = $getItem->name;
                        $sendArray->mEmail = $getItem->email;
                        $sendArray->mURL = $getItem->url;
                        $sendArray->mCountry = $getItem->country;
                        $sendArray->mPending = $getItem->pending;
                        $sendArray->mVisible = $getItem->showemail;
                    } elseif ($listing->dbtype == 'fanbase') {
                        $pending = $getItem->apr == 'y' ? 0 : 1;
                        $hide = $getItem->hideemail == 'y' ? 1 : 0;
                        $sendArray = false;
                        $sendArray->mID = $getItem->id;
                        $sendArray->mName = $getItem->name;
                        $sendArray->mEmail = $getItem->email;
                        $sendArray->mURL = $getItem->url;
                        $sendArray->mCountry = $getItem->country;
                        $sendArray->mPending = $pending;
                        $sendArray->mVisible = $hide;
                    } elseif ($listing->dbtype == 'listingadmin') {
                        $sendArray = $getItem;
                    }
                } else {
                    $sendArray = $getItem;
                }
            }

            if ($listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $sendArray;
        }

        /**
         * @function  $snakes->memberName()
         * @param     $i , int; member ID
         */
        public function memberName($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT `mName` FROM `$_ST[members]` WHERE `mID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not select the' .
                    ' title from the database.', true, $select);
            }

            return $scorpions->obj($true)->mName;
        }

        /**
         * @function  $snakes->pullMembers()
         */
        public function pullMembers($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '$i' AND `mPending` = '0'";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'Unable to select the members from' .
                    ' the specified listing.|Make sure your member table exists.', true, $select);
            }

            $all = array();
            while ($getItem = $scorpions->obj($true)) {
                $all[] = $getItem->mEmail;
            }

            return $all;
        }

        /**
         * @function  $snakes->countMembers()
         * @param     $i , int; listing ID
         * @param     $s , int; pull members by approved or pending; optional
         */
        public function countMembers($i, $s = '')
        {
            global $_ST, $scorpions, $tigers, $wolves;

            $listing = $wolves->getListings($i, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            if ($listing->dblist == 1) {
                $dbtabl = $listing->dbtabl;
                if ($listing->dbtype == 'enth') {
                    $select = "SELECT * FROM `$dbtabl`";
                    if ($s != '') {
                        if ($s == '0') {
                            $select .= " WHERE `pending` = '0'";
                        } elseif ($s == 1) {
                            $select .= " WHERE `pending` = '1'";
                        }
                    }
                } elseif ($listing->dbtype == 'fanupdate') {
                    $select = "SELECT * FROM `$dbtabl`";
                    if ($s != '') {
                        if ($s == '0') {
                            $select .= " WHERE `apr` = 'y'";
                        } elseif ($s == 1) {
                            $select .= " WHERE `apr` = 'n'";
                        }
                    }
                } elseif ($listing->dbtype == 'listingadmin') {
                    $listidnow = $listing->dbflid;
                    $select = "SELECT * FROM `$dbtabl` WHERE `fNiq` = '$listidnow'";
                    if ($s != '') {
                        if ($s == '0') {
                            $select .= " AND `mPending` = '0'";
                        } elseif ($s == 1) {
                            $select .= " AND `mPending` = '1'";
                        }
                    }
                }
                $true = $scorpions->counts($select, 1);
            } else {
                $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '$i'";
                if ($s != '') {
                    if ($s == '0') {
                        $select .= " AND `mPending` = '0'";
                    } elseif ($s == 1) {
                        $select .= " AND `mPending` = '1'";
                    }
                }
                $true = $scorpions->counts($select, 1);
            }

            if ($true->status == false) {
                $tigers->displayError('Database Error', 'The script cannot count members' .
                    ' from the specified listing.', true, $select);
            }
            $count = $true->rows;

            if ($listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $count;
        }

        /**
         * @function  $snakes->getMemberCount()
         * @param     $i , int; listing ID
         * @param     $s , int; status ID; optional
         */
        public function getMemberCount($i, $s = '')
        {
            global $_ST, $scorpions, $tigers, $wolves;

            $listing = $wolves->getListings($i, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            if ($listing->dblist == 1) {
                $dbtable = $listing->dbtabl;
                if ($listing->dbtype == 'enth') {
                    $select = "SELECT * FROM `$dbtable`";
                    if ($s != '') {
                        if ($s == '0') {
                            $select .= " WHERE `pending` = '0'";
                        } elseif ($s == 1) {
                            $select .= " WHERE `pending` = '1'";
                        }
                    }
                } elseif ($listing->dbtype == 'fanupdate') {
                    $select = "SELECT * FROM `$dbtable`";
                    if ($s != '') {
                        if ($s == '0') {
                            $select .= " WHERE `apr` = 'y'";
                        } elseif ($s == 1) {
                            $select .= " WHERE `apr` = 'n'";
                        }
                    }
                } elseif ($listing->dbtype == 'listingadmin') {
                    $select = "SELECT * FROM `$dbtable` WHERE `fNiq` = '" . $listing->dbflid . "'";
                    if ($s != '') {
                        if ($s == '0') {
                            $select .= " AND `mPending` = '0'";
                        } elseif ($s == 1) {
                            $select .= " AND `mPending` = '1'";
                        }
                    }
                }
            } else {
                $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '$i'";
                if ($s != '') {
                    if ($s == '0') {
                        $select .= " AND `mPending` = '0'";
                    } elseif ($s == 1) {
                        $select .= " AND `mPending` = '1'";
                    }
                }
            }

            $true = $scorpions->counts($select, 1);
            if ($true->status == false) {
                $tigers->displayError('Database Error', 'The script cannot select the' .
                    ' specified listing from the database.', false);
            }
            $count = $true->rows;

            if ($listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $count;
        }

        /**
         * @function  $snakes->newestFans()
         * @param     $i , int; listing ID
         */
        public function newestFans($i)
        {
            global $_ST, $scorpions, $octopus, $tigers, $wolves;

            if ($this->getMemberCount($i) == 0) {
                return '';
            }

            $listing = $wolves->getListings($i, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            $updated = $this->getUpdated($i, 'raw', 'y');
            if ($listing->dblist == 1) {
                $dbtable = $listing->dbtabl;
                if ($listing->dbtype == 'enth') {
                    $select = "SELECT * FROM `$dbtable` WHERE `pending` = '0' AND `added`" .
                        " = '$updated' ORDER BY `name` ASC";
                } elseif ($listing->dbtype == 'listingadmin') {
                    $select = "SELECT * FROM `$dbtable` WHERE `fNiq` = '" . $listing->dbflid .
                        "' WHERE `mPending` = '0' AND `mAdd` = '$updated' ORDER BY `mName` ASC";
                }
            } else {
                $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '$i' AND `mPending` = '0'" .
                    " AND `mAdd` = '$updated' ORDER BY `mName` ASC";
            }
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script cannot select the' .
                    ' members -- from the specified listing -- from the database.', false);
            }

            $b = '';
            while ($getItem = $scorpions->obj($true, 0)) {
                $namenow = $listing->dblist == 1 && $listing->dbtype !=
                'listingadmin' ? $getItem->name : $getItem->mName;
                $emailnow = $listing->dblist == 1 && $listing->dbtype !=
                'listingadmin' ? $getItem->email : $getItem->mEmail;
                $urlnow = $listing->dblist == 1 && $listing->dbtype !=
                'listingadmin' ? $getItem->url : $getItem->mURL;
                if (empty($urlnow)) {
                    $b .= "$namenow, ";
                } else {
                    $b .= "<a href=\"$urlnow\" title=\"External Link: " . $octopus->shortURL($urlnow) .
                        "\">$namenow &#187;</a>, ";
                }
            }
            $links = trim($b, ', ');

            if ($listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $links;
        }

        /**
         * @function  $snakes->formatPrevious()
         * @param     $i , int; listing ID
         * @since     2.4
         */
        public function formatPrevious($i)
        {
            global $octopus, $wolves;

            $listing = $wolves->getListings($i, 'object');
            if (empty($listing->previous)) {
                return '';
            }

            $previous = unserialize($listing->previous, ['allowed_classes' => true]);
            $b = '';
            foreach ($previous as $k => $v) {
                if (empty($k) || $k == $listing->url) {
                    $b .= "<span class=\"notext\">$v</span>, ";
                } else {
                    $b .= "<a href=\"$k\" title=\"External Link: " . $octopus->shortURL($k) .
                        "\">$v &#187;</a>, ";
                }
            }
            $links = trim($b, ', ');

            return $links;
        }

        /**
         * Pulls update dates from members, affiliates and
         * listing table, and picks the most recent date.
         *
         * @function  $snakes->getUpdated()
         */
        public function getUpdated($i, $b = 'format', $s = '')
        {
            global $_ST, $scorpions, $tigers, $wolves;

            $listing = $wolves->getListings($i, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions(
                    $listing->dbhost, $listing->dbuser, $listing->dbpass, $listing->dbname
                );
            }

            if ($listing->dblist == 1) {
                $dbtable = $listing->dbtabl;
                if ($listing->dbtype == 'enth') {
                    $select = "SELECT `added` FROM `$dbtable` WHERE `pending` = '0' ORDER BY" .
                        ' `added` DESC LIMIT 1';
                    $true = $scorpions->query($select);
                    $getMembers = $scorpions->obj($true);

                    $query = 'SELECT `added` FROM `' . $listing->dbaffs . '` ORDER BY `added`' .
                        ' DESC LIMIT 1';
                    $result = $scorpions->query($query);
                    $getAffiliates = $scorpions->obj($result);

                    if ($s != '') {
                        $array = array(
                            $getMembers->added
                        );
                    } else {
                        $array = array(
                            $getMembers->added, $getAffiliates->added
                        );
                    }
                } elseif ($listing->dbtype == 'fanbase') {
                    $select = "SELECT `date` FROM `{$dbtable}_u` ORDER BY `date` DESC LIMIT 1";
                    $true = $scorpions->query($select);
                    $getMembers = $scorpions->obj($true);

                    $array = array(
                        $getMembers->date
                    );
                } elseif ($listing->dbtype == 'listingadmin') {
                    $select = "SELECT `mAdd` FROM `$dbtable` WHERE `fNiq` = '" . $listing->dbflid .
                        "' AND `mPending` = '0' ORDER BY `mAdd` DESC LIMIT 1";
                    $true = $scorpions->query($select);
                    $getMembers = $scorpions->obj($true);

                    $edit = "SELECT `mEdit` FROM `$dbtable` WHERE `fNiq` = '" . $listing->dbflid .
                        "' AND `mPending` = '0' ORDER BY `mEdit` DESC LIMIT 1";
                    $update = $scorpions->query($edit);
                    $getEdit = $scorpions->obj($true);

                    $query = 'SELECT `aAdd` FROM `' . $listing->dbaffs .
                        "` WHERE `fNiq` = '" . $listing->dbflid . "'  ORDER BY `aAdd` DESC LIMIT 1";
                    $result = $scorpions->query($select);
                    $getAffiliates = $scorpions->obj($true);

                    $flid = $listing->dbflid;
                    $tabl = $tigers->emptyarray(explode('_', $dbtable));
                    $noow = $tabl[0];
                    $phpnow = "SELECT `updated` FROM `$noow` WHERE `id` = '$flid' LIMIT 1";
                    $sql = $scorpions->query($phpnow);
                    $getUpdated = $scorpions->obj($sql);

                    if ($s != '') {
                        $array = array(
                            $getMembers->mAdd
                        );
                    } else {
                        $array = array(
                            $getMembers->mAdd, $getEdit->mEdit, $getAffiliates->aAdd, $getUpdated->updated
                        );
                    }
                }
            } else {
                $scorpions->breach(0);
                $scorpions->breach(1);

                $select = "SELECT `mAdd` FROM `$_ST[members]` WHERE `fNiq` = '$i' AND" .
                    " `mPending` = '0' ORDER BY `mAdd` DESC LIMIT 1";
                $getMembers = $scorpions->fetch($select, 'mAdd');

                $edit = "SELECT `mEdit` FROM `$_ST[members]` WHERE `fNiq` = '$i' AND" .
                    " `mPending` = '0' ORDER BY `mEdit` DESC LIMIT 1";
                $getUpdate = $scorpions->fetch($edit, 'mEdit');

                $query = "SELECT `aAdd` FROM `$_ST[affiliates]` WHERE `fNiq` = '$i' ORDER" .
                    ' BY `aAdd` DESC LIMIT 1';
                $getAffiliates = $scorpions->fetch($query, 'aAdd');

                $sql = "SELECT `updated` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
                $getUpdated = $scorpions->fetch($sql, 'updated');

                if (empty($getMembers) && empty($getAffiliates) && empty($getUpdated)) {
                    $r = $b == 'format' ? 'Date Unavailable' : '0000-00-00';
                    return $r;
                }

                $array = array();
                if ($s != '') {
                    $array[] = $getMembers;
                } else {
                    if ($getMembers != false && !empty($getMembers)) {
                        $array[] = $getMembers;
                    }
                    if ($getUpdate != false && !empty($getUpdate)) {
                        $array[] = $getUpdate;
                    }
                    if ($getAffiliates != false && !empty($getAffiliates)) {
                        $array[] = $getAffiliates;
                    }
                    if ($getUpdated != false && !empty($getUpdated)) {
                        $array[] = $getUpdated;
                    }
                }
            }

            usort($array, array($this, 'datecompare'));
            $dates = array_reverse($array);

            if ($b == 'format') {
                $r = empty($dates) || empty($dates[0]) ? 'Date Unavailable' : date($listing->date, strtotime($dates[0]));
            } elseif ($b == 'raw') {
                $r = empty($dates) || empty($dates[0]) ? '0000-00-00' : $dates[0];
            }

            if ($listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $r;
        }

        /**
         * @function  $snakes->datecompare()
         */
        public function datecompare($a, $b)
        {
            return strcmp($a, $b);
        }

        /**
         * @function  $snakes->checkMembers()
         * @param     $c , string; member country
         */
        public function checkMembers($c)
        {
            global $_ST, $options, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[members]` WHERE `mCountry` = '$c' AND `fNiq`" .
                " = '" . $options->listingID . "'";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not select the' .
                    ' country from the database.', false);
            }

            $valid = 0;
            if ($scorpions->total($true) == 1) {
                $valid = 1;
            }

            return $valid;
        }

        /**
         * @function  $snakes->checkIfEmailExists()
         *
         * @param     $email , string; member e-mail
         * @param     $listingId , int; listing ID
         * @return bool
         */
        public function checkIfEmailExists($email, $listingId): bool
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[members]` WHERE LOWER(`mEmail`) =" .
                " '$email' AND `fNiq` = '$listingId'";

            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' check if email exists.', false);
            }

            return $scorpions->total($true) !== 0;
        }

        /**
         * @function  $snakes->getMemberInfo()
         *
         * @param     $email , string; member e-mail
         * @param     $password , string; password
         * @param     $listingId , int; listing ID
         * @return mixed
         */
        public function getMemberInfo($email, $password, $listingId)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[members]` WHERE LOWER(`mEmail`) =" .
                " '$email' AND `mPassword` = MD5('$password') AND `fNiq` = '$listingId'";

            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' check you as a member.', false);
            }

            if($scorpions->total($true) !== 1) {
                $tigers->displayError('Database Error', 'The script was unable to pull your' .
                ' information from the database. Please make sure your information is correct.',
                false);
            }

            return $scorpions->fetch($select, 'mID');
        }

        /**
         * @function  $snakes->checkMember()
         *
         * @param     $e , string; member e-mail
         * @param     $p , string; member password
         * @param     $i , int; listing ID
         */
        public function validateMember($e, $p, $i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[members]` WHERE LOWER(`mEmail`) =" .
                " '$e' AND `mPassword` = MD5('$p') AND `fNiq` = '$i'";

            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' check you as a member.', false);
            }

            $valid = false;
            if ($scorpions->total($true) == 1) {
                $valid = true;
            }

            return $valid;
        }

        /**
         * Encode and decode favourite fields
         *
         * @function  $snakes->additional()
         */
        public function additional($b, $n)
        {
            global $tigers;

            if ($n == 'decode') {
                $e = $tigers->cleanMys($b);
                $e = str_replace('_', ' ', $e);
            } elseif ($n == 'encode') {
                $e = $tigers->cleanMys($b);
                $e = str_replace(' ', '_', $e);
            }

            return trim($e);
        }

        /**
         * Add favourite fields
         *
         * @function  $snakes->addtoFave()
         */
        public function addtoFave($a, $e)
        {
            global $tigers;

            $n = array();
            $x = array();
            $b = explode('|', $e);
            $b = $tigers->emptyarray($b);
            $z = $tigers->emptyarray($a);
            $c = is_countable($z) ? count($z) : 0;
            if ($c >= (is_countable($b) ? count($b) : 0)) {
                return false;
            }

            $i = 0;
            foreach ($b as $d) {
                if ($i == (is_countable($b) ? count($b) : 0)) {
                    break;
                }
                $n[] = $d;
                $x[] = $i;
                $i++;
            }
            for ($q = 0; $q < $c; $q++) {
                if (!in_array($q, $x)) {
                    $n[] = 'NONE';
                }
            }
            $w = implode('|', $n);
            $w = '|' . trim($w, '|') . '|';

            return $w;
        }

        /**
         * Erase favourite field from member
         *
         * @function  $snakes->eraseFave()
         * @param     $a , string; favourite fields from members
         * @param     $e , string; favourite field to be deleted
         */
        public function eraseFave($a, $e)
        {
            global $tigers;

            $n = array();
            $b = explode('|', $a);
            $b = $tigers->emptyarray($b);

            $i = 0;
            foreach ($b as $k) {
                $k = trim($k);
                if ($i != $e && $k != '') {
                    $n[] = $k;
                }
                $i++;
            }
            $w = implode('|', $n);
            $w = '|' . trim($w, '|') . '|';

            return $w;
        }

        /**
         * Originally from: <http://net.tutsplus.com/tutorials/php/how-to-paginate-data-with-php/>
         * Which is terribly messy in terms of coding, and when, after the
         * last five digits, it stopped working, I google'd, and I google'd
         * HARD. Alas:
         * <http://kenai.com/projects/cms-codeigniter-rds/sources/cms-in-codeigniter/content/system/libraries/Pagination.php?rev=1>
         * Or, if the link is ever broken sometime in the future, from a
         * CMS designed in Code Igniter (a PHP framework program). I basically
         * just ~borrowed~, like, three variables from the looping bit.
         *
         * Just... don't use this. See above tutorial or something, but
         * yeah, no stealing, because you will be fucking BURNED, this
         * is so terrible.
         *
         * @function  $snakes->paginate()
         * @param     $pages , int; basically, the pagination through ceil()
         * @since     2.3beta
         */
        public function paginate($pages)
        {
            global $tigers;

            $this->page = !isset($_GET['p']) || !is_numeric($_GET['p']) ? 1 : $tigers->cleanMys($_GET['p']);
            $this->next = $this->page + 1;
            $this->prev = $this->page - 1;

            /**
             * Build our search object~
             */
            if (isset($_GET['g']) && $_GET['g'] == 'searchMembers') {
                $search = new stdClass();
                $search->sType = $tigers->cleanMys($_GET['s']);
                $search->sText = $tigers->cleanMys($_GET['q']);
            }

            /**
             * Get previous link/span!
             */
            if ($this->page > 1) {
                $pg = 'members.php?listing=' . $tigers->cleanMys($_GET['listing']) . '&#38;';
                if (isset($_GET['g']) && $_GET['g'] == 'searchMembers') {
                    $pg .= 'g=searchMembers&#38;s=' . $search->sType . '&#38;q=' . $search->sText .
                        '&#38;';
                }
                $pg .= 'p=' . $this->prev;
                echo "<span id=\"prev\"><a href=\"$pg\">&#171; Previous</a></span> ";

                /*
             *  Get the 1st page *unless* it's actually, ya' know, the first page
             */
                if ($this->page != 1 && $this->page > 6) {
                    $pg = 'members.php?listing=' . $tigers->cleanMys($_GET['listing']) . '&#38;';
                    if (isset($_GET['get']) && $_GET['g'] == 'searchMembers') {
                        $pg .= 'g=searchMembers&#38;s=' . $search->sType . '&#38;q=' . $search->sText .
                            '&#38;';
                    }
                    $pg .= 'p=1';
                    echo "<span class=\"pagi\"><a href=\"$pg\">1</a></span> ";
                    echo ' ... ';
                }
            } else {
                echo '<span>&#171; Previous</span> ';
            }

            /**
             * Get the range right before our in-between pages :D
             */
            $first = $this->page - 5 > 0 ? $this->page - (5 - 1) : 1;
            $last = $this->page + 1 < $pages ? $this->page + 5 : $pages;
            $begin = $this->page - floor($this->range / 2);
            $ender = $this->page + floor($this->range / 2);
            if ($begin <= 0) {
                $begin = 1;
                $ender += abs($begin) + 1;
            }
            if ($ender > $pages) {
                $begin = $ender - $pages;
                $ender = $pages;
            }
            $range = range($begin, $ender);

            /**
             * Where we'll be looping through the links~ This'll only show
             * five links before and after the current page
             */
            for ($i = ($first - 1); $i <= $last; $i++) {
                if ($this->page != $pages && $i == $pages) {
                    echo ' ... ';
                }
                if ($i == 1 || $i == $this->page || in_array($i, $range)) {
                    if ($i == $this->page) {
                        echo "<span id=\"current\">$i</span> ";
                    } else {
                        $pg = 'members.php?listing=' . $tigers->cleanMys($_GET['listing']) . '&#38;';
                        if (isset($_GET['get']) && $_GET['get'] == 'searchMembers') {
                            $pg .= 'g=searchMembers&#38;s=' . $search->sType . '&#38;q=' . $search->sText .
                                '&#38;';
                        }
                        $pg .= 'p=' . $i;
                        echo "<span class=\"pagi\"><a href=\"$pg\">$i</a></span> ";
                    }
                }
            }

            /**
             * And, finally, the next link!
             */
            if ($this->page < $pages) {
                $pg = 'members.php?listing=' . $tigers->cleanMys($_GET['listing']) . '&#38;';
                if (isset($_GET['g']) && $_GET['g'] == 'searchMembers') {
                    $pg .= 'g=searchMembers&#38;s=' . $search->sType . '&#38;q=' . $search->sText .
                        '&#38;';
                }
                $pg .= 'p=' . $this->next;
                echo "<span id=\"next\"><a href=\"$pg\">Next &#187;</a></span>";
            } else {
                echo '<span>Next &#187;</span>';
            }
        }

        /**
         * Format a member for the members list
         *
         * @function  $snakes->format()
         *
         * @param     $i , int; listing ID
         * @param     $t , string; template slug
         * @param     $memberId , int; member ID
         * @param     $e , string; pull member by ID or e-mail
         */
        public function format($i, $t, $memberId = '', $e = 'id')
        {
            global $_ST, $octopus, $scorpions, $tigers, $wolves;

            $scorpions->breach(1);
            $listing = $wolves->getListings($i, 'object');

            $select = "SELECT `$t` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'Unable to select the specified' .
                    ' template.', false);
            }
            $getItem = $scorpions->obj($true);

            $mark = $listing->markup == 'xhtml' ? ' /' : '';
            switch ($t) {
                case 'members':
                    if (empty($getItem->members)) {
                        $getItem->members = '<li>{name}<br' . $mark . '>' .
                            '{email} &amp;middot; {url}</li>' . "\n";
                    }
                    break;
                case 'members_header':
                    if (empty($getItem->members_header)) {
                        $getItem->members_header = "<ol>\n";
                    }
                    break;
                case 'members_footer':
                    if (empty($getItem->members_footer)) {
                        $getItem->members_footer = "</ol>\n";
                    }
                    break;
            }

            if ($memberId != '') {
                $member = $this->getMembers($memberId, $e, 'object', $listing->id);
                if ((int)$member->mVisible === 0) {
                    $email = $octopus->javascriptEmail($member->mEmail);
                } else {
                    $email = '<del>E-mail</del>';
                }

                if (!empty($member->mURL)) {
                    $url = '<a href="' . $member->mURL . '" title="External Link: ' .
                        $octopus->shortURL($member->mURL) . '">URL &raquo;</a>';
                } else
                    { $url = "<del>URL</del>"; }
            }

            if (empty($member->mExtra) || count($tigers->emptyarray(explode('|', $member->mExtra))) === 0) {
                $ffHead ='';
                $ffFoot ='';
                $ffBody = '';
            } else {
                $fields = $tigers->emptyarray(explode('|', $listing->fave_fields));
                $answers = $tigers->emptyarray(explode('|', $member->mExtra));
                $ffBody ='';
                $ffHead = "<p class=\"faveField\">\n";
                    $n = 0;
                    foreach ($fields as $f) {
                        if(isset($answers[$n]) && strcasecmp($answers[$n], '') !== 0) {
                            $ffBody .= "<span class=\"faveField$n\"><strong>" . str_replace('_', ' ', $f) .
                                ':</strong> ' . str_replace('NONE', 'All', $answers[$n]) .
                                "</span> <br$mark>\n";
                        }
                        $n++;
                    }
                    $ffBody = rtrim($ffBody, "<br$mark>\n") . ">\n";
                $ffFoot = "\n</p>\n";
            }

            $format = html_entity_decode($getItem->$t);
            if ($memberId != '') {
                $format = str_replace('{name}', $member->mName, str_replace('&amp;', '&', $format));
                $format = str_replace('{email}', $email, $format);
                $format = str_replace('{url}', $url, $format);
                $format = str_replace('{country}', $member->mCountry, $format);
                if (strpos($getItem->$t, '{fave_field}') !== false) {
                    $format = str_replace('{fave_field}', $ffHead . $ffBody . $ffFoot, $format);
                }
            }

            return $format;
        }

        /**
         * @function  $snakes->favejoin()
         */
        public function favejoin()
        {
            global $fave_field, $fave_field_e, $fave_fields_db, $tigers, $mark;

            if (empty($fave_fields_db) || empty($fave_field)) {
                return '';
            }

            if (!empty($fave_fields_db)) {
                $fields_db = explode('|', $fave_fields_db);
                $fields_db = $tigers->emptyarray($fields_db);

                if ($fields_db == 0) {
                    return '';
                } elseif ((is_countable($fields_db) ? count($fields_db) : 0) == 1) {
                    echo '<p><label><strong>' . ucwords($this->additional($fields_db[0], 'decode')) .
                        ':</strong></label> <input name="fave[]" class="input1" type="text"' . $mark .
                        '></p>';
                } elseif ((is_countable($fields_db) ? count($fields_db) : 0) > 1) {
                    $n = 0;
                    foreach ($fields_db as $f) {
                        echo '<p><label><strong>' . ucwords($this->additional($f, 'decode')) .
                            ':</strong></label> <input name="fave[]" class="input1" type="text"' . $mark .
                            '></p>';
                        $n++;
                    }
                }
            } else {
                $fields = explode('|', $fave_field);
                $fields = $tigers->emptyarray($fields);

                if ($fields == 0) {
                    return '';
                } elseif ((is_countable($fields) ? count($fields) : 0) == 1) {
                    if (isset($fave_field_e) && is_array($fave_field_e)) {
                        echo '<p><label><strong>' . ucwords($fields[0]) . ':</strong></label>' .
                            " <select name=\"fave[]\" class=\"input1\">\n";
                        foreach ($fave_field_e as $f2 => $f3) {
                            foreach ($f3 as $f4) {
                                echo '<option>' . $f4 . "</option>\n";
                            }
                        }
                        echo "</select></p>\n";
                    } else {
                        echo '<p><label><strong>' . ucwords($fields[0]) . ':</strong></label>' .
                            ' <input name="fave[]" class="input1" type="text"' . $mark . '></p>';
                    }
                } elseif ((is_countable($fields) ? count($fields) : 0) > 1) {
                    $n = 0;
                    foreach ($fields as $f) {
                        if (isset($fave_field_e) && is_array($fave_field_e)) {
                            foreach ($fave_field_e as $f2 => $f3) {
                                echo '<p><label><strong>' . ucwords($f2) . ':</strong></label>' .
                                    " <select name=\"fave[]\" class=\"input1\">\n";
                                foreach ($f3 as $f4) {
                                    echo '<option>' . $f4 . "</option>\n";
                                }
                                echo "</select></p>\n";
                            }
                        } else {
                            echo '<p><label><strong>' . ucwords($f) . ':</strong></label>' .
                                ' <input name="fave[]" class="input1" type="text"' . $mark . '></p>';
                        }
                        if ($n == ((is_countable($fields) ? count($fields) : 0) - 1)) {
                            break;
                        }
                        $n++;
                    }
                }
            }
        }

        /**
         * @function    $snakes->faveDisplay()
         * @deprecated  2.3alpha
         */
        public function faveDisplay($e)
        {
            global $fave_field, $tigers, $mark;

            if ((isset($fave_field) && !empty($fave_field)) && (!empty($e))) {
                $fields = explode('|', $fave_field);
                $answers = explode('|', $e);
                $answers = $tigers->emptyarray($answers);
                echo "<p class=\"faveField\">\n";
                if (count($fields) == 1) {
                    echo '<span class="faveField1">';
                    echo '<strong>' . $fields[0] . ':</strong> ' . $answers[0] . "\n";
                    echo "</span>\n";
                } elseif (count($fields) > 1) {
                    $n = 0;
                    foreach ($fields as $f) {
                        echo "<span class=\"faveField{$n}\">";
                        echo '<strong>' . $f . ':</strong> ' . str_replace('NONE', 'All', $answers[$n]) . "<br$mark>\n";
                        echo "</span>\n";
                        $n++;
                    }
                }
                echo "\n</p>\n";
            } else {
                return '';
            }
        }

        /**
         * @function  $snakes->membersPagination()
         * @param     $s
         * @param     $q , string; country; optional
         */
        public function membersPagination($s, $q = '')
        {
            global $_ST, $options, $page, $per_page, $scorpions, $wolves;

            $listing = $wolves->getListings($options->listingID, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            echo "<p id=\"pagination\">\n";
            if ($listing->dblist == 1) {
                if ($listing->dbtype == 'enth') {
                    $select = 'SELECT * FROM `' . $listing->dbtabl . "` WHERE `pending` = '0'";
                    if ($s == 'country') {
                        $select .= " AND `country` = '$q'";
                    }
                } elseif ($listing->dbtype == 'fanbase') {
                    $select = 'SELECT * FROM `' . $listing->dbtabl . "` WHERE `apr` = 'y'";
                    if ($s == 'country') {
                        $select .= " AND `country` = '$q'";
                    }
                } elseif ($listing->dbtype == 'listingadmin') {
                    $select = 'SELECT * FROM `' . $listing->dbtabl . "` WHERE `fNiq` = '" . $listing->dbflid .
                        "' AND `mPending` = '0'";
                    if ($s == 'country') {
                        $select .= " AND `mCountry` = '$q'";
                    }
                }
            } else {
                $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '" . $options->listingID .
                    "' AND `mPending` = '0'";
                if ($s == 'country') {
                    $select .= " AND `mCountry` = '$q'";
                }
            }

            $true = $scorpions->counts($select);
            $pages = ceil($true->rows / $per_page);
            $next = $page + 1;
            $prev = $page - 1;

            if ($page > 1) {
                if ($options->prettyURL == true) {
                    echo '<a href="' . $listing->url . $options->url . 'page/' . $prev . '">&#171; Previous</a> ';
                } else {
                    echo '<a href="' . $listing->url . $options->url . 'page=' . $prev . '">&#171; Previous</a> ';
                }
            } else {
                echo '&laquo; Previous ';
            }

            for ($i = 1; $i <= $pages; $i++) {
                if ($page == $i) {
                    echo $i . ' ';
                } else if ($options->prettyURL == true) {
                    echo '<a href="' . $listing->url . $options->url . 'page/' . $i . '">' . $i . '</a> ';
                } else {
                    echo '<a href="' . $options->url . 'page=' . $i . '">' . $i . '</a> ';
                }
            }

            if ($page < $pages) {
                if ($options->prettyURL == true) {
                    echo '<a href="' . $listing->url . $options->url . 'page/' . $next . '">Next &#187;</a>';
                } else {
                    echo '<a href="' . $options->url . 'page=' . $next . '">Next &#187;</a>';
                }
            } else {
                echo 'Next &#187;';
            }
            echo "\n</p>\n";

            if ($listing->dblist == 1) {
                $scorpions->breach(1);
            }
        }

        /**
         * Sort members by country, name, or both
         *
         * @function  $snakes->membersSort()
         */
        public function membersSort($s, $b, $p)
        {
            global $_ST, $options, $per_page, $scorpions, $start, $tigers, $wolves;

            $countfls = $this->getMemberCount($options->listingID, '0');
            $listing = $wolves->getListings($options->listingID, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            switch ($s) {
                case 'all':
                    if ($listing->dblist == 1) {
                        $dbtabl = $listing->dbtabl;
                        $dbflid = $listing->dbflid;
                        if ($listing->dbtype == 'enth') {
                            $select = "SELECT * FROM `$dbtabl` WHERE `pending` = '0' ORDER BY `name`" .
                                " ASC LIMIT 0, $countfls";
                        } elseif ($listing->dbtype == 'fanbase') {
                            $select = "SELECT * FROM `$dbtabl` WHERE `apr` = 'y' ORDER BY `name` ASC" .
                                " LIMIT 0, $countfls";
                        } elseif ($listing->dbtype == 'listingadmin') {
                            $select = "SELECT * FROM `$dbtabl` WHERE `fNiq` = '$dbflid' AND `mPending`" .
                                " = '0' ORDER BY `mName` ASC LIMIT 0, $countfls";
                        }
                    } else {
                        $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '" . $options->listingID .
                            "' AND `mPending` = '0' ORDER BY `mName` ASC LIMIT 0, $countfls";
                    }
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        $tigers->displayError('Script Error', 'The script was unable to select' .
                            ' the members from the specified listing.', false);
                    }

                    echo $this->format($options->listingID, 'members_header');
                    while ($getItem = $scorpions->obj($true)) {
                        $get_id_now = $b == 1 ? 'id' : 'mID';
                        echo $this->format($options->listingID, 'members', $getItem->$get_id_now);
                    }
                    echo $this->format($options->listingID, 'members_footer');
                    break;

                case 'country':
                    if (isset($_GET['name'])) {
                        $g = str_replace('+', ' ', $tigers->cleanMys($_GET['name']));
                        if ($listing->dblist == 1) {
                            $dbtabl = $listing->dblist;
                            if ($listing->dbtype == 'enth') {
                                $select = "SELECT * FROM `$dbtabl` WHERE `pending` = '0' AND `country` =" .
                                    " '$g' ORDER BY `name` ASC LIMIT $start, $per_page";
                            } elseif ($listing->dbtype == 'fanbase') {
                                $select = "SELECT * FROM `$dbtabl` WHERE `apr` = 'y' AND `country` = '$g'" .
                                    " ORDER BY `name` ASC LIMIT $start, $per_page";
                            } elseif ($listing->dbtype == 'listingadmin') {
                                $select = "SELECT * FROM `$dbtabl` WHERE `fNiq` = '$listing->dbflid' AND `mPending`" .
                                    " = '0' AND `mCountry` = '$g' ORDER BY `mName` ASC LIMIT $start, $per_page";
                            }
                        } else {
                            $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '" . $options->listingID .
                                "' AND `mPending` = '0' AND `mCountry` = '$g' ORDER BY `mName` ASC LIMIT" .
                                " $start, $per_page";
                        }
                        $true = $scorpions->query($select);
                        if ($true === false) {
                            $tigers->displayError('Database Error', 'Unable to select the members' .
                                ' from the specified country.', false);
                        }

                        echo "<p class='tc'>Members from the country, <strong>" . ucwords($g) . "</strong>.</p>\n";
                        echo $this->format($options->listingID, 'members_header');
                        while ($getItem = $scorpions->obj($true)) {
                            $get_id_now = $b == 1 ? $getItem->email : $getItem->mID;
                            $get_id_opt = $b != 1 ? 'id' : ($p == 'enth' ? 'email' : 'id');
                            echo $this->format($options->listingID, 'members', $get_id_now, $get_id_opt);
                        }
                        echo $this->format($options->listingID, 'members_footer');
                    } else {
                        $this->membersDefault('country');
                    }
                    break;

                case 'name':
                    if ($listing->dblist == 1) {
                        $dbtabl = $listing->dbtabl;
                        $dbflid = $listing->dbflid;
                        if ($listing->dbtype == 'enth') {
                            $select = "SELECT * FROM `$dbtabl` WHERE `pending` = '0' ORDER BY `name`" .
                                " ASC LIMIT $start, $per_page";
                        } elseif ($listing['dbtype'] == 'fanbase') {
                            $select = "SELECT * FROM `$dbtabl` WHERE `apr` = 'y' ORDER BY `name` ASC" .
                                " LIMIT $start, $per_page";
                        } elseif ($listing['dbtype'] == 'listingadmin') {
                            $select = "SELECT * FROM `$dbtabl` WHERE `fNiq` = '$dbflid' AND `mPending`" .
                                " = '0' ORDER BY `mName` ASC LIMIT $start, $per_page";
                        }
                    } else {
                        $select = "SELECT * FROM `$_ST[members]` WHERE `fNiq` = '" . $options->listingID .
                            "' AND `mPending` = '0' ORDER BY `mName` ASC LIMIT $start, $per_page";
                    }
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        $tigers->displayError('Script Error', 'The script was unable to select the' .
                            ' members from the specified listing.', false);
                    }

                    echo $this->format($options->listingID, 'members_header');
                    while ($getItem = $scorpions->obj($true)) {
                        $get_id_now = $b == 1 ? 'id' : 'mID';
                        echo $this->format($options->listingID, 'members', $getItem->$get_id_now);
                    }
                    echo $this->format($options->listingID, 'members_footer');
                    break;
            }
        }

        /**
         * @function  $snakes->membersDefault()
         * @param     $s , string; sort by all, name or country
         */
        public function membersDefault($s)
        {
            global $_ST, $options, $octopus, $scorpions, $tigers, $wolves;

            $listing = $wolves->getListings($options->listingID, 'object');
            if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                $scorpions->initDB($listing->dbhost, $listing->dbuser, $listing->dbpass,
                    $listing->dbname);
            }

            switch ($s) {
                case 'all':
                    $this->membersSort('all', $listing->dblist, $listing->dbtype);
                    break;

                case 'country':
                    $dbtable = $listing->dbtabl;
                    if ($listing->dblist == 1) {
                        if ($listing->dbtype == 'enth') {
                            $select = "SELECT DISTINCT `country` FROM `$dbtable` WHERE `pending` =" .
                                " '0' ORDER BY `country` ASC";
                        } elseif ($listing->dbtype == 'fanbase') {
                            $select = "SELECT DISTINCT `country` FROM `$dbtable` WHERE `apr` = 'y'" .
                                ' ORDER BY `country` ASC';
                        }
                    } else {
                        $select = "SELECT DISTINCT `mCountry` FROM `$_ST[members]` WHERE `fNiq`" .
                            " = '" . $options->listingID . "' AND `mPending` = '0' ORDER BY" .
                            ' `mCountry` ASC';
                    }
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        $tigers->displayError('Database Error', 'The script was unable to select' .
                            ' the members from the specified listing.', false);
                    }

                    echo $octopus->alternate('menu', $listing->markup);
                    while ($getItem = $scorpions->obj($true)) {
                        $type = $listing->dblist == 1 ? $getItem->country : $getItem->mCountry;
                        if ($options->prettyURL == true) {
                            echo '<li><a href="' . $listing->url . $options->url . 'country/' .
                                str_replace(' ', '+', $type) . "\">$type</a></li>\n";
                        } else {
                            $url = $options->url;
                            $url = str_replace(['sort=country&amp;', 'sort=country&#38;'], '', $url);
                            echo '<li><a href="' . $url . 'sort=country&amp;name=' .
                                str_replace(' ', '+', $type) . "\">$type</a></li>\n";
                        }
                    }
                    echo $octopus->alternate('menu', $listing->markup, 1);
                    break;

                case 'list':
                    echo $octopus->alternate('menu', $listing->markup);
                    echo '<li><a href="' . $options->url . 'sort=name' . '"' . ">by Name</a></li>\n";
                    echo '<li><a href="' . $options->url . 'sort=country' . '"' . ">by Country</a></li>\n";
                    echo $octopus->alternate('menu', $listing->markup, 1);
                    break;

                case 'name':
                    $this->membersSort('name', $listing->dblist, $listing->dbtype);
                    break;
            }

            if ($listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }
        }

        # Functions end here 8D
}
}

$snakes = new snakes();
