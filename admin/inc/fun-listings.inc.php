<?php
declare(strict_types=1);
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-listings.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('wolves')) {
    class wolves
    {

        /**
         * @function  $wolves->listingsList()
         *
         * @param string $sortBySubjectOrId , string; sort by subject or ID
         * @param string $filterByStatus , string; sort by status (current, upcoming or pending)
         * @param string $sortBy , string; sort by category or status
         * @param string $status , string; current listings with status; optional
         * @param int $withSubCategories , int
         *
         * @return array
         */
        public function listingsList(
            $sortBySubjectOrId = 'id',
            $filterByStatus = '',
            $sortBy = 'status',
            $status = '',
            $withSubCategories = 0
        ) {
            global $_ST, $lions, $scorpions;

            $select = "SELECT * FROM `$_ST[main]`";
            if ($filterByStatus != '') {
                if ($sortBy == 'categories') {
                    $select .= ' WHERE';
                    if ($status == 'current' || $status == '0') {
                        $select .= " `status` = '0' AND";
                    } elseif ($status == 'upcoming' || $status == 1) {
                        $select .= " `status` = '1' AND";
                    } elseif ($status == 'pending' || $status == 2) {
                        $select .= " `status` = '2' AND";
                    }
                    if (in_array($filterByStatus, $lions->categoryList())) {
                        $select .= " (`category` LIKE '%!$filterByStatus!%' AND";
                    }
                    $select = trim($select, ' AND');
                    if ($withSubCategories == 1 && (is_countable($lions->categoryList('list', 'child', $filterByStatus)) ? count($lions->categoryList('list', 'child', $filterByStatus)) : 0) > 0) {
                        $query = '';
                        $childcats = $lions->categoryList('list', 'child', $filterByStatus);
                        foreach ($childcats as $cc) {
                            $query .= " OR `category` LIKE '%!$cc!%'";
                        }
                        $select .= rtrim($query, ' OR ');
                    }
                    if (in_array($filterByStatus, $lions->categoryList())) {
                        $select .= ' ) ';
                    }
                } elseif ($sortBy == 'status') {
                    if ($filterByStatus == 'current' || $filterByStatus == '0') {
                        $select .= " WHERE `status` = '0'";
                    } elseif ($filterByStatus == 'upcoming' || $filterByStatus == 1) {
                        $select .= " WHERE `status` = '1'";
                    } elseif ($filterByStatus == 'pending' || $filterByStatus == 2) {
                        $select .= " WHERE `status` = '2'";
                    }
                }
            }
            if ($sortBySubjectOrId == 'id') {
                $select .= ' ORDER BY `id` ASC';
            }
            if ($sortBySubjectOrId == 'subject') {
                $select .= ' ORDER BY `subject` ASC';
            }
            $true = $scorpions->query($select);
            if ($true == false) {
                echo $scorpions->database->error();
            }

            $all = [];
            while ($getItem = $scorpions->obj($true)) {
                $all[] = $getItem->id;
            }

            return $all;
        }

        /**
         * A handy function that collects the arrays for both neglected and
         * overdue listings, so I can... make things pretty :| WHATEVS
         *
         * @function  $wolves->indexListings()
         * @param     $b , boolean; 1 for selecting neglected listings, 0 for overdue
         *
         * @version   2.3beta
         */
        public function indexListings($b = 1)
        {
            global $snakes;

            if ($b == 1) {
                $listings = $this->listingsList('subject', 'current');
            } else {
                $listings = $this->listingsList('subject', 'upcoming');
            }

            $a = [];
            foreach ($listings as $i) {
                $p = $this->getListings($i, 'object');
                if ($b == 1) {
                    if (
                        $snakes->getUpdated($p->id, 'raw') <= date('Y-m-d', strtotime('-2 months')) ||
                        $snakes->getUpdated($p->id, 'raw') <= date('Y-m-d', strtotime('-60 days'))
                    ) {
                        $a[] = $i;
                    }
                } elseif ($p->since <= date("Y-m-d", strtotime("-1 month"))) {
                    $a[] = $i;
                }
            }

            return $a;
        }

        /**
         * @access   public
         * @function $wolves->getListings()
         * @since    2.3beta
         */
        public function getListings($i, $b = 'array')
        {
            global $_ST, $scorpions, $tigers;

            if ($i == 0 || $i == '0') {
                return $tigers->buildCollective();
            }

            $select = "SELECT * FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select the' .
                    ' specified listing.', true, $select);
            }
            $getItem = $b == 'array' ? $scorpions->obj($true, 1) : $scorpions->obj($true, 0);

            return $getItem;
        }

        /**
         * @function  $wolves->getSubject()
         * @param     $i , int; listing ID
         */
        public function getSubject($i)
        {
            global $_ST, $scorpions, $tigers;

            if (!is_numeric($i) || $i == 0 || $i == '0') {
                return 'Whole Collective';
            }

            $select = "SELECT `subject` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not select' .
                    ' the title from the database.', true, $select);
            }
            $getItem = $scorpions->obj($true);

            return $getItem->subject;
        }

        /**
         * @function  $wolves->getSubject()
         * @param     $i , int; listing ID
         */
        public function checkListings($c, $s)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[main]` WHERE `category` LIKE '%!$c!%' AND" .
                " `show` = '0'";
            if ($s == 'current') {
                $select .= " AND `status` = '0'";
            } elseif ($s == 'upcoming') {
                $select .= " AND `status` = '1'";
            } elseif ($s == 'pending') {
                $select .= " AND `status` = '2'";
            }
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not count the' .
                    ' rows from the specified parameters from the database.', false);
            }
            $count = $scorpions->total($true);

            if ($count > 0) {
                $valid = 1;
            } else {
                $valid = 0;
            }

            return $valid;
        }

        /**
         * @function  $wolves->getNewest()
         * @since     1.9
         */
        public function getNewest()
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[main]` WHERE `status` = '0' ORDER BY `since`" .
                ' DESC LIMIT 1';
            $count = $scorpions->counts($select);
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the newest listing.', false);
            }
            $newest = $scorpions->obj($true);

            if ($count->rows < 0) {
                $b = '';
            } else {
                $b = '<a href="' . $newest->url . '" title="' . $newest->title . ', the ' .
                    $newest->subject . ' listing">' . $newest->subject . " &#187;</a>\n";
            }

            return $b;
        }

        /**
         * @function  $wolves->pullImage()
         * @param     $i , int; listing ID
         */
        public function pullImage($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT `image` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the image from the specified listing.', false);
            }
            $getItem = $scorpions->obj($true);

            return $getItem->image;
        }

        /**
         * @function  $wolves->pullSubjects()
         * @param     $i , int; a character-separated string of listing ID(s)
         * @param     $s , character; splits the string into an array
         *
         * @since     1.9
         */
        public function pullSubjects($i, $s)
        {
            global $tigers;

            if ($s == '!') {
                $a = "/[\s!]+/";
            } else {
                $a = "/[\s|]+/";
            }

            $subj = '';
            $subjects = preg_split($a, $i);
            $subjects = $tigers->emptyarray($subjects);
            foreach ($subjects as $u) {
                if ($u != '' && $u != ' ' && $u != ' 0 ') {
                    $subj .= $this->getSubject($u) . ', ';
                }
            }
            $subj = trim($subj, ', ');

            return $subj;
        }

        /**
         *  Backwoods-type of function, which is ultimately
         *  $this->pullSubjects(), with added linkage!
         *
         * @function   $wolves->pullSubjects_Links()
         * @param      $i , string; character-separated string of listing IDs
         * @param      $s , character; character we use to split the $i param
         */
        public function pullSubjects_Links($i, $s)
        {
            global $my_updates, $seahorses, $qwebs;

            if ($s === '!') {
                $a = "/[\s!]+/";
            } else {
                $a = "/[\s|]+/";
            }

            if (strpos($qwebs, '/') !== false) {
                $myw = $qwebs;
            } else {
                $myw = $qwebs . '/';
            }


            if (strpos($my_updates, 'index.php') !== false) {
                //@todo is ?s= needed????
                // $q = $myw . '?s=';
                $q = $myw;
            } else {
                //@todo is ?s= needed????
                //$q = $my_updates . '?s=';
                $q = $my_updates;
            }

            $subj = '';
            $subjects = preg_split($a, $i);
            foreach ($subjects as $u) {
                if ($u !== '' && $u !== ' ' && $u !== ' 0 ') {
                    $w = $seahorses->getOption('updates_prettyurls') === 'y' ? $myw .
                        's/' . $u : $q . '?s=' . $u;
                    $subj .= '<a href="' . $w . '">' . $this->getSubject($u) . '</a>, ';
                }
            }
            $subj = trim($subj, ', ');

            return $subj;
        }

        /**
         * @function   $wolves->getListingTemplate()
         * @param      $i , int; listing ID
         * @param      $t , string; template title
         *
         * @since      2.3alpha
         */
        public function getListingTemplate($i, $t)
        {
            global $_ST, $scorpions;

            $select = "SELECT `$t` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $template = $scorpions->fetch($select, $t, 'The script was unable to select' .
                ' the listing template.');

            return $template;
        }

        /**
         * @function   $wolves->listingTemplate()
         *
         * @param      $i , int; listing ID
         * @param      $t , string; listing template title
         * @param      $u , string; listing URL
         * @param      $s , string; listing subject
         * @param      $m , string; listing image
         */
        public function listingTemplate($i, $t, $u, $s, $m)
        {
            global $_ST, $scorpions, $seahorses, $tigers;

            $select = "SELECT `$t` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the listing affiliates template.', false);
            }
            $template = $scorpions->obj($true);

            if (empty($template->affiliates)) {
                return '';
            }

            if ($t == 'affiliates') {
                $v = 'aff';
            } elseif ($t == 'joined') {
                $v = 'jnd';
            } elseif ($t == 'listings') {
                $v = 'img';
            } elseif ($t == 'wishlist') {
                $v = 'wsh';
            }

            $image = $seahorses->getOption($v . '_http') . $m;

            $format = html_entity_decode($template->affiliates);
            $format = str_replace('{image}', $image, $format);
            $format = str_replace('{subject}', $s, $format);
            $format = str_replace('{url}', $u, $format);

            return $format;
        }

        /**
         * @function   $wolves->getTemplate_Listings()
         * @param      $i , int; listing ID
         * @param      $b , string; template title; optional
         *
         * @since      1.9
         */
        public function getTemplate_Listings($i, $b = '', $f = '')
        {
            global $_ST, $lions, $resultImage, $scorpions, $seahorses, $snakes, $tigers,
                   $wolves;

            $select = "SELECT * FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script cannot select the' .
                    ' specified ID from the database.', false);
            }
            $getItem = $scorpions->obj($true);

            if ($b != '' && preg_match('/([A-Za-z_]+)/i', $b)) {
                $p = $scorpions->escape($b);
            } else {
                $p = 'listings_template';
            }

            $template = $seahorses->getTemplate($p);
            $approved = $snakes->getMemberCount($getItem->id, '0');
            $pending = $snakes->getMemberCount($getItem->id, 1);
            $updated = $snakes->getUpdated($getItem->id);

            $format = html_entity_decode($template);
            $format = str_replace('{i}', $f, $format);
            $format = str_replace('{id}', $getItem->id, $format);
            $format = str_replace('{subject}', $getItem->subject, $format);
            $format = str_replace('{title}', $getItem->title, $format);
            $format = str_replace('{url}', $getItem->url, $format);
            $format = str_replace('{desc}', html_entity_decode($getItem->desc), $format);
            $format = str_replace(
                ['{category}', '{categories}'], $lions->pullCatNames($getItem->category, '!'), $format
            );
            $format = str_replace('{image}', $seahorses->getOption('img_http') . $getItem->image, $format);
            $format = str_replace('{approved}', $approved, $format);
            $format = str_replace('{pending}', $pending, $format);
            $format = str_replace('{since}', date($getItem->date, strtotime($getItem->since)), $format);
            $format = str_replace('{updated}', $updated, $format);

            return $format;
        }

        /**
         * Grab dropdown func-tion!
         *
         * @function  $wolves->dropdown()
         * @param     $s , string; status
         */
        public function dropdown($s, $a = '')
        {
            global $lions, $octopus, $options;

            $p = '';
            $cats = $lions->categoryList('list', 'parent');

            if (count($this->listingsList('id', $s, 'status')) > 0) {
                $p .= '<form action="' . $options->query . "\" method=\"get\" name=\"sort\">\n" .
                    '<p class="ddListings"><select name="sort" class="input1" id="sort"' .
                    " onchange=\"this.form.submit();\">\n";
                foreach ($cats as $c) {
                    $cat = $lions->getCategory($c);
                    $listingcount = count($this->listingsList('id', $c, 'categories', $s, 1));
                    if (
                        ($this->checkListings($c, $s) == 1) ||
                        (
                            $this->checkListings($c, $s) == 0 &&
                            $lions->countChildren($c) > 0 &&
                            $lions->childrenListings($c, $s) > 0
                        )
                    ) {
                        $p .= ' <option';
                        if (
                            (isset($_GET['sort']) && $_GET['sort'] == $c) ||
                            ($a != '' && in_array($a, $lions->categoryList()) && $a == $c)
                        ) {
                            $p .= ' selected="selected"';
                        }
                        $p .= " value=\"$c\">" . $cat->catname . "</option>\n";
                    }
                }
                $p .= "</select></p>\n</form>\n";
            }

            return $p;
        }

        /**
         * Format header/footer for various displays
         *
         * @function  $wolves->_h()
         * @param     $p , string; type
         * @param     $c , boolean; closing tag
         */
        public function _h($p = 'table', $c = 0)
        {
            global $options, $seahorses;

            $r = $seahorses->getOption('markup') == 'html5' ? '&#187;' : '&raquo;';
            $s = '';

            if ($p == 'table') {
                if ($c == 0) {
                    $s .= "<table class=\"owned\" style=\"width: 100%;\">\n<tfoot><tr>\n<td" .
                        " class=\"tc\" colspan=\"2\">$r <a href=\"" . $options->query .
                        "sort=all\">All categories</a></td>\n</tr></tfoot>\n";
                } elseif ($c == 1) {
                    $s .= "</table>\n";
                }
            }

            return $s;
        }

        /**
         * @function  $wolves->showListings()
         * @param     $s , string; status
         * @param     $d , string; display mode
         */
        public function showListings($s, $d)
        {
            global $_ST, $lions, $options, $scorpions, $seahorses;

            $ids = $lions->categoryList('list', 'parent');

            if ($d == 'list') {
                echo $this->_h('table');
                if (count($this->listingsList('id', $s, 'status')) > 0) {
                    foreach ($ids as $catId) {
                        $listingcount = count($this->listingsList('id', $catId, 'categories', $s, 1));
                        if (
                            ($this->checkListings($catId, $s) == 1) ||
                            (
                                $this->checkListings($catId, $s) == 0 &&
                                $lions->countChildren($catId) > 0 &&
                                $lions->childrenListings($catId, $s) > 0
                            )
                        ) {
                            echo "<tbody><tr>\n";
                            echo ' <td class="left"><a href="' . $options->query . 'sort=' . $catId .
                                '">' . $lions->getCatName($catId) . "</a></td>\n";
                            echo ' <td class="center">' . $listingcount . "</td>\n";
                            echo "</tr></tbody>\n";
                        }
                        if ($options->sort == 'y') {
                            $lists = $lions->categoryList('default', 'child', $catId);
                            foreach ($lists as $list) {
                                if ($this->checkListings($list['catid'], $s) == 1) {
                                    echo "<tbody><tr>\n";
                                    echo '<td class="left"><a href="' . $options->query . 'sort=' . $list['catid'] .
                                        '">' . $lions->getCatName($list['parent']) .
                                        ' &raquo; ' . $lions->getCatName($list['catid']) . "</a></td>\n<td" .
                                        ' class="center">' . count($this->listingsList('id', $list['catid'],
                                            'categories', $s, 1)) . "</td>\n</tr></tbody>\n";
                                }
                            }
                        }
                    }
                    echo $this->_h('table', 1);
                }
            } elseif ($d == 'dropdown') {
                echo $this->dropdown($s);
            } /**
             * Display pre-selected category
             */
            elseif ($d == 'category') {
                if (isset($options->showcat) && in_array($options->showcat, $lions->categoryList())) {
                    $category = $lions->getCategory($options->showcat);
                    $parentcat = $category->parent == 0 ? '' : $lions->getCatName($category->parent) .
                        ($seahorses->getOption('markup') == 'html5' ? ' &#187; ' : ' &raquo; ');
                    $name = $parentcat . $lions->getCatName($options->showcat);

                    /**
                     * Get SQL query P:
                     */
                    $select = "SELECT * FROM `$_ST[main]` WHERE `status` = '$s' AND `show` =" .
                        " '0' AND `category` LIKE '%!" . $options->showcat . "!%'";
                    $true = $scorpions->query($select);
                    $count = $scorpions->total($true);

                    /**
                     * Displaying listing(s) now...
                     */
                    echo "<p class=\"tc\">You are viewing the <strong>$name</strong> category." .
                        " There are currently <strong>$count</strong> listings listed.</p>\n";
                    echo $this->dropdown($s, $options->showcat);
                    echo "<div class=\"sep\">\n";
                    while ($getItem = $scorpions->obj($true)) {
                        echo $this->getTemplate_Listings($getItem->id) . "\n";
                    }
                    echo "</div>\n";
                } else {
                    return $this->showListings($s, 'list');
                }
            } /**
             * Display all listings \o/
             */
            elseif ($d == 'all') {
                if ($s == 'current') {
                    $s = '0';
                } elseif ($s == 'upcoming') {
                    $s = 1;
                } elseif ($s == 'pending') {
                    $s = 2;
                }
                $select = "SELECT * FROM `$_ST[main]` WHERE `status` = '$s' AND `show` = '0'";
                $true = $scorpions->query($select);
                $template = $seahorses->getTemplate('listings_template');

                echo '<div class="sec">';
                while ($getItem = $scorpions->obj($true)) {
                    echo $this->getTemplate_Listings($getItem->id) . "\n";
                }
                echo "</div>\n";

                /**
                 * This only needs to be applied if we're displaying all listings, hey-ho:
                 */
                if ($options->list == 'y') {
                    echo "<h3 class=\"showListingsSort\">Listings</h3>\n";
                    echo $this->showListings($s, 'list');
                }
            } elseif ($d == 'table') {
                echo $this->_h('table');
                if (count($this->listingsList('id', $s, 'status')) > 0) {
                    foreach ($ids as $catId) {
                        $listingcount = count($this->listingsList('id', $catId, 'categories', $s, 1));
                        if (
                            ($this->checkListings($catId, $s) == 1) ||
                            (
                                $this->checkListings($catId, $s) == 0 &&
                                $lions->countChildren($catId) > 0 &&
                                $lions->childrenListings($catId, $s) > 0
                            )
                        ) {
                            echo "<tbody><tr>\n";
                            echo ' <td class="left"><a href="' . $options->query . 'sort=' . $catId .
                                '">' . $lions->getCatName($catId) . "</a></td>\n";
                            echo ' <td class="center">' . $listingcount . "</td>\n";
                            echo "</tr></tbody>\n";
                        }
                        if ($options->sort == 'y') {
                            $lists = $lions->categoryList('default', 'child', $catId);
                            foreach ($lists as $list) {
                                if ($this->checkListings($list['catid'], $s) == 1) {
                                    echo "<tbody><tr>\n";
                                    echo '<td class="left"><a href="' . $options->query . 'sort=' . $list['catid'] .
                                        '">' . $lions->getCatName($list['parent']) .
                                        ' &raquo; ' . $lions->getCatName($list['catid']) . "</a></td>\n<td" .
                                        ' class="center">' . count($this->listingsList('id', $list['catid'],
                                            'categories', $s, 1)) . "</td>\n</tr></tbody>\n";
                                }
                            }
                        }
                    }
                    echo $this->_h('table', 1);
                }
            }
        }
    }
}

$wolves = new wolves();
