<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-affiliates.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('rabbits')) {
    class rabbits
    {

        /**
         * @function  $rabbits->affiliatesList()
         * @param string $i , int; listing ID; optional
         * @param string $o , string; sort by date
         * @param string $b
         * @return array
         */
        public function affiliatesList($i = '', $o = '', $b = 'id'): array
        {
            global $_ST, $scorpions, $tigers, $wolves;

            /**
             * Are we dealing with a listing, and if so, is it the collective? We process
             * that fucker first! 8D
             */
            if ($i != '') {
                if ($i == '0') {
                    $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '0' OR `fNiq`" .
                        " LIKE '%!0!%'";
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        $tigers->displayError('Database Error', 'The script was unable to' .
                            ' select the affiliates.|Make sure your affiliates table exists.', false);
                    }
                    $all = array();
                    while ($getItem = $scorpions->obj($true)) {
                        $all[] = $getItem->aID;
                    }
                } /**
                 * Appending the fanlisting ID to the query, as only Listing Admin
                 * works with pulling affiliates from the entire table. :D
                 */
                else {
                    $listing = $wolves->getListings($i, 'object');
                    if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                        $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass, $listing->dbname);
                    }

                    if ($listing->dblist == 1) {
                        $dbtable = $listing->dbaffs;
                        if ($listing->dbtype == 'enth') {
                            $select = "SELECT * FROM `$dbtable`";
                            if ($o != '' && ($o == 'asc' || $o == 'desc')) {
                                $select .= ' ORDER BY `added` ' . strtoupper($o);
                            }
                        } elseif ($listing->dbtype == 'listingadmin') {
                            $select = "SELECT * FROM `$dbtable` WHERE `fNiq` = '" . $listing->dbflid .
                                "' OR `fNiq` LIKE '%!" . $listing->dbflid . "!%'";
                            if ($o != '' && ($o == 'asc' || $o == 'desc')) {
                                $select .= ' ORDER BY `aAdd` ' . strtoupper($o);
                            }
                        }
                    } else {
                        $d = $tigers->cleanMys($i);
                        $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '$d' OR `fNiq`" .
                            " LIKE '%!$d!%'";
                        if ($o != '' && ($o == 'asc' || $o == 'desc')) {
                            $select .= ' ORDER BY `aAdd` ' . strtoupper($o);
                        }
                    }
                    $true = $scorpions->query($select);
                    if ($true == false) {
                        $tigers->displayError('Database Error', 'The script was unable to' .
                            ' select the affiliates.|Make sure your affiliates table exists.', false);
                    }
                    $all = array();
                    while ($getItem = $scorpions->obj($true)) {
                        if ($b == 'email') {
                            $all[] = ($listing->dblist == 1 && $listing->dbtype ==
                            'enth' ? $getItem->email : $getItem->aEmail);
                        } else {
                            $all[] = ($listing->dblist == 1 && $listing->dbtype ==
                            'enth' ? $getItem->affiliateid : $getItem->aID);
                        }
                    }
                }
            } /**
             * We're not crosslisting/pulling from a fanlisting ID
             */
            else {
                $select = "SELECT * FROM `$_ST[affiliates]`";
                $true = $scorpions->query($select);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'The script was unable to' .
                        ' select the affiliates.|Make sure your affiliates table exists.', false);
                }
                $all = array();
                while ($getItem = $scorpions->obj($true)) {
                    $all[] = $getItem->aID;
                }
            }

            if (isset($listing) && $listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $all;
        }

        public function allAffiliates($i = 'n'): array
        {
            return $this->affiliatesList($i);
        }

        # -- Get Member Sort (for <members.php>) --------------------
        /**
         * @param $i Listing ID
         * @param string $s Search query (empty by default)
         * @param array $a Search query contents~
         * @return array
         */
        # -----------------------------------------------------------
        public function sortAffiliates($i, $s = '', $a = array()): array
        {
            global $_ST, $scorpions, $get_affsearch_array, $laantispam, $tigers,
                   $snakes, $wolves;

            /*
             *  Are we crosslisting to another script/database? Let's seeee~
             */
            if ($i != '0') {
                $listing = $wolves->getListings($i, 'object');
                if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                    $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass, $listing->dbname);
                }

                /*
                 *  Build search query, if provided~
                 */
                $q = '';
                if ($s == 'affiliates' && (is_array($a) && count($a) > 0)) {
                    $findarray = $listing->dblist == 1 ? ($listing->dbtype == 'listingadmin' ?
                        'listingadmin' : 'enth') : 'listingadmin';
                    $typearray = $get_affsearch_array[$findarray];
                    $search = new stdClass();
                    foreach ($a as $k => $v) {
                        $search->$k = $v;
                    }
                    $typeid = $tigers->cleanMys($search->searchType);
                    if (!array_key_exists($typeid, $typearray)) {
                        $typeid = 'name';
                    }
                    $textid = $laantispam->clean($tigers->cleanMys($search->searchText), 'n', 'y', 'declean');
                    $q .= ' `' . $typearray[$typeid] . "` LIKE '%$textid%'";
                }

                if ($listing->dblist == 1) {
                    $dbtabl = $listing->dbaffs;
                    if ($listing->dbtype == 'enth') {
                        $select = "SELECT * FROM `$dbtabl`";
                        if ($q != '') {
                            $select .= ' WHERE' . $q;
                        }
                        $select .= ' ORDER BY `added` DESC';
                    } elseif ($listing->dbtype == 'listingadmin') {
                        $listidnow = $listing->dbflid;
                        $select = "SELECT * FROM `$dbtabl` WHERE `fNiq` = '$listidnow'";
                        if ($q != '') {
                            $select .= ' AND' . $q;
                        }
                        $select .= ' ORDER BY `aAdd` DESC';
                    }
                } else {
                    $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '$i' OR `fNiq`" .
                        " LIKE '%!$i!%'";
                    if ($q != '') {
                        $select .= ' AND' . $q;
                    }
                    $select .= ' ORDER BY `aAdd` DESC';
                }
            } /*
	  *  We're dealing with collective here, so we don't need our $listing-> object 
	  */
            else {
                $q = '';
                if ($s == 'affiliates' && (is_array($a) && count($a) > 0)) {
                    $typearray = $get_affsearch_array['listingadmin'];
                    $search = false;
                    foreach ($a as $k => $v) {
                        $search->$k = $v;
                    }
                    $typeid = $tigers->cleanMys($search->searchType);
                    if (!array_key_exists($typeid, $typearray)) {
                        $typeid = 'name';
                    }
                    $textid = $laantispam->clean($tigers->cleanMys($search->searchText), 'n', 'y', 'declean');
                    $q .= ' `' . $typearray[$typeid] . "` LIKE '%$textid%'";
                }

                $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '0' OR `fNiq`" .
                    " LIKE '%!0!%'";
                if ($q != '') {
                    $select .= ' AND' . $q;
                }
                $select .= ' ORDER BY `aAdd` DESC';
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
                while ($item = $scorpions->obj($true)) {
                    if ($i != '0') {
                        $d = $listing->dblist == 1 && $listing->dbtype != 'listingadmin' ? $item->affiliateid : $item->aID;
                        $h = $listing->dblist == 1 && $listing->dbtype != 'listingadmin' ? $item->added : $item->aAdd;
                    } else {
                        $d = $item->aID;
                        $h = $item->aAdd;
                    }
                    $prearray[$d] = $h;
                }
                uasort($prearray, array($snakes, 'datecompare'));
                arsort($prearray);
                foreach ($prearray as $k => $v) {
                    $array[] = array(
                        'aID' => $k,
                        'aDate' => $v
                    );
                }
            }

            if (isset($listing) && $listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $array;
        }

        # -- Get Affiliate (from ID or Email) Function --------------
        # -----------------------------------------------------------
        public function getAffiliate($i, $p = 'id', $e)
        {
            global $_ST, $scorpions, $tigers, $wolves;

            /*
             *  If we're dealing with an actual listing, we need our $listing-> object;
             *  otherwise, we skip to the actual Listing Admin query~
             */
            if ($e != '0') {
                $listing = $wolves->getListings($e, 'object');
                if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                    $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass, $listing->dbname);
                }

                if ($listing->dblist == 1) {
                    $dbaff = $listing->dbaffs;
                    if ($listing->dbtype == 'enth') {
                        $select = "SELECT * FROM `$dbaff`";
                        if ($p == 'id') {
                            $select .= " WHERE `affiliateid` = '$i'";
                        } elseif ($p == 'email') {
                            $select .= " WHERE LOWER(`email`) = '$i'";
                        }
                    } elseif ($listing->dbtype == 'listingadmin') {
                        $select = "SELECT * FROM `$dbaff`";
                        if ($p == 'id') {
                            $select .= " WHERE `aID` = '$i'";
                        } elseif ($p == 'email') {
                            $select .= " WHERE LOWER(`aEmail`) = '$i'";
                        }
                    }
                } else {
                    $select = "SELECT * FROM `$_ST[affiliates]`";
                    if ($p == 'id') {
                        $select .= " WHERE `aID` = '$i'";
                    } elseif ($p == 'email') {
                        $select .= " WHERE LOWER(`aEmail`) = '$i'";
                    }
                }
                $select .= ' LIMIT 1';
            } /*
	  *  Whole collective jazz~! 
	  */
            else {
                $select = "SELECT * FROM `$_ST[affiliates]`";
                if ($p == 'id') {
                    $select .= " WHERE `aID` = '$i'";
                } elseif ($p == 'email') {
                    $select .= " WHERE LOWER(`aEmail`) = '$i'";
                }
            }
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' select the specified member.|Make sure your members table exists.',
                    true, $select);
            }

            $getItem = $scorpions->obj($true);
            if ($e != '0' && $listing->dblist == 1) {
                if ($listing->dbtype == 'enth') {
                    $sendArray = new stdClass();
                    /**
                     * $sendArray->fNiq for core Listing Admin functioning only~
                     */
                    $sendArray->fNiq = '!' . $e . '!';
                    $sendArray->aID = $getItem->affiliateid;
                    $sendArray->aEmail = $getItem->email;
                    $sendArray->aSubject = $getItem->title;
                    $sendArray->aImage = $getItem->imagefile;
                    $sendArray->aURL = $getItem->url;
                    $sendArray->aAdd = $getItem->added;
                } elseif ($listing->dbtype == 'listingadmin') {
                    $sendArray = $getItem;
                } else {
                    $sendArray = $getItem;
                }
            } else {
                $sendArray = $getItem;
            }

            $scorpions->breach(0);
            $scorpions->breach(1);

            return $sendArray;
        }

        /**
         * @param      $i , int; listing ID
         * @return
         */
        public function countAffiliates($i)
        {
            global $_ST, $scorpions, $tigers, $wolves;

            if ($i != '0') {
                $d = $tigers->cleanMys($i);
                $listing = $wolves->getListings($d, 'object');
                if (!empty($listing->dbhost) && !empty($listing->dbuser) && !empty($listing->dbname)) {
                    $scorpions->scorpions($listing->dbhost, $listing->dbuser, $listing->dbpass,
                        $listing->dbname);
                }

                if ($listing->dblist == 1) {
                    $dbaff = $listing->dbaffs;
                    if ($listing->dbtype == 'enth') {
                        $select = "SELECT * FROM `$dbaff`";
                    } elseif ($listing->dbtype == 'listingadmin') {
                        $select = "SELECT * FROM `$dbaff` WHERE `fNiq` = '" . $listing->dbflid .
                            "' OR `fNiq` LIKE '%!$d!%'";
                    }
                } else {
                    $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '$d' OR `fNiq`" .
                        " LIKE '%!$d!%'";
                }
                $true = $scorpions->counts($select, 1);
                if ($true->status == false) {
                    $tigers->displayError('Database Error', 'The script was nable to' .
                        ' select the list of affiliates.|Make sure your members table exists.',
                        true, $select);
                }
            } else {
                $select = "SELECT * FROM `$_ST[affiliates]` WHERE `fNiq` = '0' OR `fNiq`" .
                    " LIKE '%!0!%'";
                $true = $scorpions->counts($select, 1, 'The script was nable to select the' .
                    ' list of affiliates.|Make sure your affiliates table(s) exist.');
            }
            $count = $true->rows;

            if (isset($listing) && $listing->dblist == 1) {
                $scorpions->breach(0);
                $scorpions->breach(1);
            }

            return $count;
        }

        # -- Pull Affiliates Image (direct) -------------------------
        # -----------------------------------------------------------
        public function pullImage_Affiliate($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT `aImage` FROM `$_ST[affiliates]` WHERE `aID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select the' .
                    ' image from the specific site.|Make sure your table(s) exist.',
                    true, $select);
            }
            return $scorpions->obj($true)->aImage;
        }

        # End affiliates here~!
    }
}

$rabbits = new rabbits();
