<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-categories.php>
 * @version          Robotess Fork
 */

if (!class_exists('lions')) {
    class lions
    {

        /**
         * @function  $lions->categoryList()
         *
         * @param     $b , string; return value (e.g. list, array, et al.)
         * @param     $g , string; choice between choosing a parent
         * category, as well as returning a category ID or name; 'id'
         * by default
         * @param     $m , int; category parent ID; optional
         */
        public function categoryList($b = 'list', $g = '', $n = '')
        {
            global $_ST, $scorpions;

            $select = "SELECT * FROM `$_ST[categories]`";
            if ($g != '' && $g == 'parent') {
                $select .= " WHERE `parent` = '0'";
            } elseif (($g != '' && $g == 'child') && ($n != '' && is_numeric($n))) {
                $select .= " WHERE `parent` = '$n'";
            }
            $select .= ' ORDER BY `catname` ASC';
            $true = $scorpions->query($select);

            $all = array();
            while ($getItem = $scorpions->obj($true)) {
                if ($b == 'array') {
                    $all[] = array('catid' => $getItem->catid, 'catname' => $getItem->catname);
                } elseif ($b == 'default') {
                    $all[] = array('parent' => $getItem->parent, 'catid' => $getItem->catid);
                } elseif ($b == 'list' || $b == 'parent') {
                    $all[] = $getItem->catid;
                }
            }

            return $all;
        }

        /**
         * Shamelessly stolen from my own script, Icon Admin 1.1 - this
         * is basically a hodge-podge way of sorting categories and
         * subcategories without getting into the nitty gritty. I AM A
         * LAZY SHIT, WE ALL KNOW THIS. 8D
         *
         * Edited May 22nd, 2011: Soooo-ooo-oo, extra hodge-podge-ness
         * added, which was to escape the same-name category mess. YAY
         * MESSY CODE! \o/
         *
         * @function  $lions->categorySort()
         */
        public function categorySort()
        {
            $preparents = $this->categoryList('list', 'parent');
            $parents = array();
            foreach ($preparents as $p) {
                $parents[$p] = $this->getCatName($p);
            }
            asort($parents);

            $subparents = array();
            foreach ($parents as $k => $v) {
                $subparents[] = array(
                    'catID' => $k,
                    'catName' => $v
                );
                $prechildren = $this->categoryList('list', 'child', $k);
                $children = array();
                foreach ($prechildren as $c) {
                    $children[$c] = $this->getCatName($c);
                }
                asort($children);
                if (count($children) > 0) {
                    foreach ($children as $a => $u) {
                        $subparents[] = array(
                            'catID' => $a,
                            'catName' => $u
                        );
                    }
                }
            }

            return $subparents;
        }

        /**
         * @function   $lions->getCategory()
         * @param      $i , int; category ID
         */
        public function getCategory($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[categories]` WHERE `catid` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Script Error', 'The script was unable to pull' .
                    ' the category you specified.', false);
            }
            $getItem = $scorpions->obj($true, 0);

            return $getItem;
        }

        /**
         * @function   $lions->getCatName()
         * @param      $i , int; category ID
         */
        public function getCatName($i)
        {
            global $_ST, $scorpions;

            $select = "SELECT `catname` FROM `$_ST[categories]` WHERE `catid` = '$i'" .
                ' LIMIT 1';
            $r = $scorpions->fetch($select, 'catname');

            return $r;
        }

        /**
         * @function   $lions->pullCatNames()
         * @param      $i , text; category IDs inside a string
         * @param      $s , character; character that splits the string into
         *  an array
         */
        public function pullCatNames($i, $s)
        {
            global $tigers;

            $categories = explode($s, $i);
            $categories = $tigers->emptyarray($categories);
            $cat = '';
            foreach ($categories as $category) {
                $c = $this->getCategory($category);
                if ($c->parent == 0) {
                    $cat .= $this->getCatName($category) . ', ';
                } else {
                    $cat .= $this->getCatName($c->parent) . ' &#187; ' . $this->getCatName($category) . ', ';
                }
            }
            $cat = trim($cat, ', ');

            return $cat;
        }

        /**
         *  Counts child categories under the specified parent category
         *
         * @function   $lions->countChildren()
         * @param      $c , int; category parent ID
         */
        public function countChildren($c)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '$c'";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not count' .
                    ' the children categories under the specified parent category.', false);
            }
            $count = $scorpions->total($true);

            return $count;
        }

        /**
         * @function   $lions->childrenJoined()
         * @param      $c , int; parent category ID
         */
        public function childrenJoined($c)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '$c'";
            $count = $scorpions->counts($select);
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'Could not select the category' .
                    ' from the database.', false);
            }

            $r = 0;
            if ($count->rows > 0) {
                while ($getItem = $scorpions->obj($true)) {
                    $catid = $getItem->catid;
                    $s2 = "SELECT * FROM `$_ST[joined]` WHERE `jCategory` LIKE '%|$catid|%'";
                    $c2 = $scorpions->counts($s2);
                    $q2 = $scorpions->query($s2);
                    if ($c2->rows > 0) {
                        $r += $c2->rows;
                    } else {
                        $r += 0;
                    }
                }
            } else {
                $r += 0;
            }

            return $r;
        }

        /**
         * Get counts from the specified parent category - THE LISTING
         * VER-SION :D
         *
         * @function  $lions->childrenListings()
         * @param     $c , int; parent category ID
         */
        public function childrenListings($c, $s = '')
        {
            global $_ST, $get_fulllist_array, $get_listing_array, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '$c'";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not select' .
                    ' the category from the database.', false);
            }
            $count = $scorpions->total($true);

            $r = 0;
            if ($count > 0) {
                while ($getItem = $scorpions->obj($true)) {
                    $catid = $getItem->catid;
                    $s2 = "SELECT * FROM `$_ST[main]` WHERE `category` LIKE '%!$catid!%'";
                    if ($s != '' && array_key_exists($s, $get_listing_array)) {
                        $s2 .= " AND `status` = '" . $get_fulllist_array[$s] . "'";
                    }
                    $q2 = $scorpions->query($s2);
                    $c2 = $scorpions->total($q2);
                    if ($c2 > 0) {
                        $r += $c2;
                    } else {
                        $r += 0;
                    }
                }
            } else {
                $r += 0;
            }

            return $r;
        }

        /**
         * @function   $lions->countCategories()
         * @param      $b , boolean; on 1, includes joined listings in the
         *  category count
         */
        public function countCategories($b = 0)
        {
            global $_ST, $scorpions, $seahorses;

            $count = $seahorses->getCount('cat');

            $r = 0;
            if ($count > 0) {
                $select = "SELECT * FROM `$_ST[categories]`";
                $true = $scorpions->fetch($select);
                while (
                $getItem = $scorpions->obj($true)
                ) {
                    $catid = $getItem->catid;
                    $s2 = "SELECT * FROM `$_ST[main]` WHERE `category` LIKE '%!$catid!%'";
                    $q2 = $scorpions->counts($s2, 1);
                    $c2 = $q2->rows;
                    if ($c2 > 0) {
                        $r += $c2;
                    } else {
                        $r += 0;
                    }
                }
            } else {
                $r += 0;
            }

            if ($b == 1) {
                $j = 0;
                if ($count > 0) {
                    $select = "SELECT * FROM `$_ST[categories]`";
                    $true = $scorpions->fetch($select);
                    while (
                    $getItem = $scorpions->obj($true)
                    ) {
                        $catid = $getItem->catid;
                        $s2 = "SELECT * FROM `$_ST[joined]` WHERE `jCategory` LIKE '%!$catid!%'";
                        $q2 = $scorpions->counts($s2, 1);
                        $c2 = $q2->rows;
                        if ($c2 > 0) {
                            $j += $c2;
                        } else {
                            $j += 0;
                        }
                    }
                } else {
                    $j += 0;
                }
                $r += $j;
            }

            return $r;
        }
    }
}

$lions = new lions();
