<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-joined.inc.php>
 * @version          Robotess Fork
 */

use Robotess\PaginationUtils;

if (!class_exists('dragons')) {

    class dragons
    {
        private int $range = 10;

        /**
         * @function  $dragons->joinedList()
         * @param string $filterOption , string; sorting option (id, all, madeby, et
         * al.); optional
         * @param string $categoryId , int; category ID; optional
         * @param string $status
         * @param bool $withSubCategories
         * @param bool $wholeObj
         * @return array
         * @since     2.3beta
         */
        public function joinedList($filterOption = 'all', $categoryId = '', $status = '', bool $withSubCategories = false, bool $wholeObj = false): array
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[joined]`";
            if ($filterOption == 'id' && $categoryId != '') {
                $categoryFilter = $this->getSqlForCategory($categoryId, $withSubCategories);
                $select .= " WHERE {$categoryFilter}";
                if ($status !== '' && (int)$status === 0) {
                    $select .= " AND `jStatus` = '0'";
                } elseif ($status !== '' && (int)$status === 1) {
                    $select .= " AND `jStatus` = '1'";
                }
            } elseif ($filterOption == 'madeby' && $categoryId == '') {
                $select .= " WHERE `jMade` = 'y'";
            } elseif ($filterOption == 'madeby' && $categoryId != '') {
                $categoryFilter = $this->getSqlForCategory($categoryId, $withSubCategories);
                $select .= " WHERE {$categoryFilter} AND `jMade` = 'y'";
            } elseif ($filterOption == 'pending' && $categoryId == '') {
                $select .= " WHERE `jStatus` = '1'";
            }
            $select .= ' ORDER BY `jSubject` ASC';
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' select the all joined listings.', false);
            }

            $all = array();
            while ($getItem = $scorpions->obj($true, 0)) {
                $all[] = empty($wholeObj) ? $getItem->jID : $getItem;
            }

            return $all;
        }

        /**
         * For <joined.php>, so our joined listings can be just a bit more
         * organised than 2.2
         *
         * @function  $dragons->sortJoined()
         *
         * @param     $s , string; search term; optional
         * @param     $a , array; search contents; optional
         *
         * @return array
         * @return array
         * @since     2.3beta
         */
        public function sortJoined($s = '', $a = array()): array
        {
            global $_ST, $laantispam, $scorpions, $snakes, $tigers;

            /**
             * Build search query, if provided~
             */
            $q = '';
            if ($s == 'joined' && (is_array($a) && count($a) > 0)) {
                $search = new stdClass();
                foreach ($a as $k => $v) {
                    $search->$k = $v;
                }
                if (isset($search->searchSubject) && !empty($search->searchSubject)) {
                    $subject = $laantispam->clean($tigers->cleanMys($search->searchSubject), 'y', 'n');
                    $q .= " `jSubject` LIKE '%$subject%' AND";
                }
                if (isset($search->searchCategory) && !empty($search->searchCategory)) {
                    $q .= $this->getSqlForCategory($search->searchCategory, true) . ' AND';
                }
            }

            $select = "SELECT * FROM `$_ST[joined]`";
            if ($q != '') {
                $select .= ' WHERE' . trim($q, ' AND');
            }
            $select .= ' ORDER BY `jAdd` DESC';
            $true = $scorpions->query($select);
            $count = $scorpions->total($true);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not pull the' .
                    ' joined listings from the database.', true, $select);
            }

            $array = array();
            $prearray = array();
            if ($count > 0) {
                while ($item = $scorpions->obj($true)) {
                    $prearray[$item->jID] = $item->jAdd;
                }
                uasort($prearray, array($snakes, 'datecompare'));
                arsort($prearray);
                foreach ($prearray as $k => $v) {
                    $array[] = array(
                        'jID' => $k,
                        'jDate' => $v
                    );
                }
            }
            return $array;
        }

        /**
         * @function  $dragons->getJoined()
         * @param     $i , int; joined ID
         * @return mixed
         * @return mixed
         */
        public function getJoined($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[joined]` WHERE `jID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to' .
                    ' select the specified joined listing.|Make sure your joined table exists.',
                    true, $select);
            }
            return $scorpions->obj($true);
        }

        /**
         * Check if a category is in use
         *
         * @function  $dragons->checkJoined()
         * @param     $c , int; category ID
         * @return int
         * @return int
         */
        public function checkJoined($c): int
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[joined]` WHERE `jCategory` LIKE '%|$c|%' AND" .
                " `jStatus` = '0'";
            $count = $scorpions->counts($select);
            if ($count->status == false) {
                $tigers->displayError('Database Error', 'The script could not select the' .
                    ' joined listings from the database.', true, $select);
            }

            return ($count->rows == 0 ? 0 : 1);
        }

        /**
         * @function  $dragons->pullImage_Joined()
         * @param     $i , int; joined ID
         * @return
         */
        public function pullImage_Joined($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT `jImage` FROM `$_ST[joined]` WHERE `jID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the image from the specified joined listing.', false);
            }
            return $scorpions->obj($true)->jImage;
        }

        /**
         * For notes, see fun-members.inc.php, ->paginate(), line 842 8D
         *
         * @function  $dragons->paginate()
         * @param     $totalPagesCount , int; pages summed by ceil()
         * @since     2.3beta
         */
        public function paginate($totalPagesCount): void
        {
            if ($totalPagesCount <= 1) {
                //nothing
                return;
            }

            global $tigers;

            /**
             * Build our URL~
             */
            $q = 'joined.php?';
            if (isset($_GET['g']) && $_GET['g'] == 'searchJoined') {
                $q = 's=' . $tigers->cleanMys($_GET['s']) . '&#38;';
                if (isset($_GET['c']) && is_numeric($_GET['c'])) {
                    $q .= 'c=' . $tigers->cleanMys($_GET['c']) . '&#38;';
                }
            }

            $page = !isset($_GET['p']) || !is_numeric($_GET['p']) ? 1 : $tigers->cleanMys($_GET['p']);

            PaginationUtils::rangedPagination($page, $totalPagesCount, $q, $this->range, 'p');
        }

        /**
         * Format joined listing
         *
         * @function  $dragons->getTemplate_Joined()
         * @param     $i , int; joined ID
         * @return string|string[]
         * @return string|string[]
         */
        public function getTemplate_Joined($i)
        {
            global $_ST, $lions, $scorpions, $seahorses, $tigers;

            $select = "SELECT * FROM `$_ST[joined]` WHERE `jID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script cannot select the' .
                    ' specified joined listing from the database.', false);
            }
            $getItem = $scorpions->obj($true);

            $template = $seahorses->getTemplate('joined_template');
            if (isset($_GET['list'])) {
                $tp = '<li><a href="{url}" title="External Link: {subject}">{subject} &#187;</a></li>';
            } else {
                $tp = html_entity_decode($template);
            }

            if (
                file_exists($seahorses->getOption('jnd_path') . $getItem->jImage) &&
                !empty($getItem->jImage)
            ) {
                $image = $seahorses->getOption('jnd_http') . $getItem->jImage;
            } else {
                $image = $seahorses->getOption('jnd_http') . 'no_image.png';
            }

            $format = $tp;
            $format = str_replace('{subject}', $getItem->jSubject, $format);
            $format = str_replace('{url}', $getItem->jURL, $format);
            $format = str_replace('{category}', $lions->pullCatNames($getItem->jCategory, '|'), $format);
            $format = str_replace('{image}', $image, $format);

            return $format;
        }

        /**
         * @function  $dragons->joinedForm()
         */
        public function joinedForm(): void
        {
            global $_ST, $badheaders, $laantispam, $lions, $octopus, $options, $scorpions,
                   $seahorses, $tigers;

            if ($options->search == 'no_search') {
                $tigers->displayError('Script Error', 'It appears the collective owner' .
                    ' has set the joined form to <strong>off</strong>, leaving you unable to' .
                    ' search for fanlistings by form.', false);
            }

            if (isset($_GET['subject'])) {
                $s = trim($_GET['subject']);
                $s = strip_tags($s);
                $s = $laantispam->clean($s, 'n');
                if (isset($_GET['cat'])) {
                    $c = $tigers->cleanMys($_GET['cat']);
                }

                foreach ($laantispam->spamarray() as $b) {
                    if (strpos($s, (string) $b) !== false) {
                        $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
                    }

                    if (isset($_GET['cat']) && strpos($c, (string) $b) !== false) {
                        $tigers->displayError('SPAM Error', 'SPAM language is not allowed.', false);
                    }
                }

                if (
                    preg_match($badheaders, $_SERVER['HTTP_USER_AGENT']) ||
                    empty($_SERVER['HTTP_USER_AGENT'])
                ) {
                    $tigers->displayError('SPAM Error', 'SPAM bots are not allowed.', false);
                }

                if (isset($c)) {
                    $cats = $lions->categoryList();
                    if (!ctype_digit($c) || !in_array($c, $cats)) {
                        $tigers->displayError('Form Error', 'It appears the category you are' .
                            ' searching for is not a number or the category you searched for is' .
                            ' nonexistant. Go back and try again.', false);
                    }
                }

                $s = $scorpions->escape($s);
                $or = isset($c) ? ', in the <strong>' . $lions->getCatName($c) .
                    '</strong> category' : '';
                ?>
                <p>You have chosen to search for <strong><?php echo $_GET['subject']; ?></strong>
                    subject<?php echo $or; ?>. Below are any relevant results from your search.
                    <em>Please</em> be aware that your search is accounted for throughout the
                    <em>whole</em> database (or in the category, if given), and your subject may not
                    exist.</p>
                <?php
                $select = "SELECT * FROM `$_ST[joined]` WHERE `jSubject` LIKE '%$s%'";
                if (isset($c)) {
                    $select .= " AND `jCategory` LIKE '%|$c|%'";
                }
                $select .= ' ORDER BY `jSubject` ASC';
                $true = $scorpions->query($select);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'The script was unable to perform' .
                        ' the search. Try again later.', false);
                } else if ($scorpions->total($true) > 0) {
                    echo $octopus->alternate('menu', $seahorses->getOption('markup'));
                    while ($getItem = $scorpions->obj($true)) {
                        ?>
                        <li><a href="<?php echo $getItem->jURL; ?>"
                               title="External Link: <?php echo $getItem->jSubject; ?>">
                                <?php echo $getItem->jSubject; ?> &#187;</a></li>
                        <?php
                    }
                    echo $octopus->alternate('menu', $seahorses->getOption('markup'), 1);
                } else {
                    ?>
                    <p class="tc">No joined listings found!</p>
                    <?php
                }
            } else {
                $mark = $seahorses->getOption('markup') === 'xhtml' ? ' /' : '';
                ?>
                <p>Below is the form to search through my joined fanlistings. This is
                    particularly useful when looking for a listing of yours I may have joined, or
                    to see if I'm still linking to your listing. Please be aware before searching
                    that the form looks for the <em>subject</em> of a listing (and the subject in
                    a particular category, if set).</p>

                <form action="<?php echo str_replace('index.php', '', basename($_SERVER['PHP_SELF'])) . '?search'; ?>"
                      method="get">
                    <p style="margin: 0;"><input name="search" type="hidden"<?php echo $mark; ?>></p>

                    <fieldset>
                        <legend>Search Joined Fanlistings</legend>
                        <p><label>* <strong>Subject:</strong></label>
                            <input name="subject" class="input1" type="text" required="required"<?php echo $mark; ?>>
                        </p>
                        <?php
                        if (isset($_GET['getcat'])) {
                            ?>
                            <p><label><strong>Category</strong> (optional):</label> <select name="cat" class="input1">
                                <?php
                                $select = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '0' ORDER BY" .
                                    ' `catname` ASC';
                                $true = $scorpions->query($select);
                                if ($true == false) {
                                    echo "  <option>No Categories Found</option>\n";
                                } else {
                                    while ($getItem = $scorpions->obj($true)) {
                                        echo '  <option value="' . $getItem->catid . '">' . $getItem->catname .
                                            "</option>\n";
                                        $query = "SELECT * FROM `$_ST[categories]` WHERE `parent` = '" . $getItem->catid .
                                            "' ORDER BY `catname` ASC";
                                        $result = $scorpions->query($query);
                                        if ($scorpions->total($result) > 0) {
                                            while ($item = $scorpions->obj($result)) {
                                                echo '  <option value="' . $item->catid . '">' . $item->catname . "</option>\n";
                                            }
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <?php
                        } else {
                            ?>
                            <p class="tc"><a href="<?php echo $options->query; ?>getcat">Category List</a></p>
                            <?php
                        }
                        ?>
                        <p class="tc">
                            <input class="input2" type="submit" value="Search Joined"<?php echo $mark; ?>>
                            <input class="input2" type="reset" value="Reset"<?php echo $mark; ?>>
                        </p>
                    </fieldset>
                </form>
                <?php
            }
        }

        /**
         * Default joined \o/
         *
         * @function  $dragons->showJoined()
         * @param     $u
         */
        public function showJoined($u): void
        {
            global $options, $lions, $seahorses;

            if (!isset($_GET['list'])) {
                echo '<p>Below is the list of joined fanlistings I have joined. You can' .
                    ' browse by category by default, <a href="' . $options->query . 'search">' .
                    'search by form</a> or view the fanlistings <a href="' . $options->query .
                    "list\">without images</a>. Enjoy the fun! :D</p>\n";
            }

            $ids = $lions->categoryList('list', 'parent');

            echo '<table class="joined"';
            if ($seahorses->getOption('markup') != 'html5') {
                echo ' width="100%"';
            }
            echo ">\n";
            echo "<tfoot>\n<tr><td class=\"tc\" colspan=\"2\">&#187; <a href=\"" . $options->query .
                "sort=all\">All Categories</a></td></tr>\n";
            if ($u == 'lister' && !isset($_GET['list'])) {
                echo '<tr><td class="tc" colspan="2">&#187; <a href="' . $options->query .
                    "list\">All Categories by List</a></td></tr>\n</tfoot>\n";
            } else {
                echo "</tfoot>\n";
            }
            echo "<tbody class=\"pending\"><tr>\n";
            echo ' <td class="left"><a href="' . $options->query . 'sort=pending">[+]' .
                " All pending listings</a></td>\n";
            echo ' <td class="center">' . count($this->joinedList('pending')) . "</td>\n";
            echo "</tr></tbody>\n";
            if ($options->madeby == 'madeby') {
                echo "<tbody class=\"madebyme\"><tr>\n";
                echo ' <td class="left"><a href="' . $options->query . 'sort=madeby">[+]' .
                    " Joined buttons made by me</a></td>\n";
                echo ' <td class="center">' . count($this->joinedList('madeby')) . "</td>\n";
                echo "</tr></tbody>\n";
            }
            foreach ($ids as $id) {
                if (
                    ($this->checkJoined($id) == 1) ||
                    (
                        $this->checkJoined($id) == 0 &&
                        $lions->countChildren($id) > 0 &&
                        $lions->childrenJoined($id) > 0
                    )
                ) {
                    echo "<tbody><tr>\n";
                    echo ' <td class="left"><a href="' . $options->query . 'sort=' . $id .
                        '">' . $lions->getCatName($id) . "</a></td>\n";
                    echo ' <td class="center">' . count($this->joinedList('id', $id, 0)) . "</td>\n";
                    echo "</tr></tbody>\n";
                }
                $lists = $lions->categoryList('default', 'child', $id);
                if ($options->sort == 'y') {
                    foreach ($lists as $list) {
                        if ($this->checkJoined($list['catid']) == 1) {
                            echo "<tbody><tr>\n";
                            echo '<td class="left"><a href="' . $options->query . 'sort=' . $list['catid'] .
                                '">' . $lions->getCatName($list['parent']) .
                                ' &#187; ' . $lions->getCatName($list['catid']) . "</a></td>\n";
                            echo '<td class="center">' . count($this->joinedList('id', $list['catid'], 0)) . "</td>\n";
                            echo "</tr></tbody>\n";
                        }
                    }
                }
            }
            echo "</table>\n";
        }

        /**
         * @param string $categoryId
         * @param bool $withSubCategories
         * @return string
         */
        private function getSqlForCategory(string $categoryId, bool $withSubCategories = false): string
        {
            $categoryFilter = "`jCategory` LIKE '%%|%s|%%'";

            if (!$withSubCategories) {
                return sprintf($categoryFilter, $categoryId);
            }

            $allChildren = $this->getChildrenRecursive($categoryId, $categoryFilter);

            return ' ( ' . implode(' OR ', $allChildren) . ')';
        }

        private function getChildrenRecursive(string $categoryId, string $categoryFilter): array
        {
            global $lions;

            $allChildren = [];
            foreach ($lions->categoryList('list', 'child', $categoryId) as $childrenCategoryId) {
                foreach ($this->getChildrenRecursive($childrenCategoryId, $categoryFilter) as $item) {
                    $allChildren[] = $item;
                }
            }

            $allChildren[] = sprintf($categoryFilter, $categoryId);

            return $allChildren;
        }
    }
}

$dragons = new dragons();
