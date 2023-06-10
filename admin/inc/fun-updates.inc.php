<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-updates.inc.php>
 * @version          Robotess Fork
 */

use Robotess\PaginationUtils;

if (!class_exists('turtles')) {
    class turtles
    {

        /**
         * @function  $turtles->updatesList()
         * @param     $s , int; listing ID; optional
         * @return array
         * @return array
         */
        public function updatesList($s = '')
        {
            global $_ST, $scorpions, $tigers, $wolves;

            $select = "SELECT * FROM `$_ST[updates]`";
            if ($s != 'y' && ($s == 0 || $s == '0' || in_array($s, $wolves->listingsList()))) {
                $select .= " WHERE `uCategory` LIKE '%!$s!%'";
            }
            $select .= ' ORDER BY `uID` ASC';
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' updates from the database.', false);
            }

            $all = array();
            while ($getItem = $scorpions->obj($true)) {
                $all[] = $getItem->uID;
            }

            return $all;
        }

        /**
         * @function  $turtles->commentsList()
         * @param     $e , int; entry ID; optional
         * @return array
         * @return array
         */
        public function commentsList($e = '')
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[updates_comments]`";
            if ($e != '' && in_array($e, $this->updatesList())) {
                $select .= " WHERE `eNiq` = '$e'";
            }
            $select .= ' ORDER BY `cAdded` DESC';
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' comments from the database.', false);
            }

            $all = array();
            while ($getItem = $scorpions->obj($true)) {
                $all[] = $getItem->cID;
            }

            return $all;
        }

        /**
         * @access   public
         * @function $turtles->grabID()
         * @since    2.1.4
         */
        public function grabID()
        {
            global $_ST, $scorpions;

            $select = "SELECT `uID` FROM `$_ST[updates]` ORDER BY `uID` DESC LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false || $scorpions->total($true) < 0) {
                return 0;
            }

            return $scorpions->obj($true)->uID;
        }

        /**
         * @function  $turtles->getEntry()
         * @param     $i , int; update ID
         * @return mixed
         * @return mixed
         */
        public function getEntry($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[updates]` WHERE `uID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the specified update.', false);
            }

            return $scorpions->obj($true);
        }

        /**
         * @access   public
         * @function $turtles->entryName()
         * @param $i
         * @return string
         * @since    2.1.4
         */
        public function entryName($i)
        {
            global $_ST, $scorpions, $tigers;

            if ($i == 0 || $i == '0') {
                return 'Whole Collective';
            }

            $select = "SELECT `uTitle` FROM `$_ST[updates]` WHERE `uID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script could not select the' .
                    ' title from the database.', true, $select);
            }
            return $scorpions->obj($true)->uTitle;
        }

        /**
         * @access   public
         * @function $turtles->getComment()
         * @param $i
         * @return mixed
         * @since    2.1.4
         */
        public function getComment($i)
        {
            global $_ST, $scorpions, $tigers;

            $select = "SELECT * FROM `$_ST[updates_comments]` WHERE `cID` = '$i' LIMIT 1";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the specified comment.', false);
            }
            return $scorpions->obj($true);
        }

        /**
         * Revert HTML entities back to their characters
         *
         * @access   public
         * @function $turtles->strip_characters()
         * @param $r
         * @return string|string[]
         * @since    2.1.4
         */
        public function strip_characters($r)
        {
            $s = trim($r);
            $s = str_replace('&#039;', "'", $s);
            $s = str_replace('&amp;039;', "'", $s);
            $s = str_replace('&#034;', '"', $s);
            $s = str_replace('&amp;#034;', '"', $s);
            $s = str_replace('&quot;', '"', $s);
            $s = str_replace('&#8217;', "'", $s);
            $s = str_replace('&amp;#8217;', "'", $s);
            $s = str_replace('&amp;', '&', $s);
            $s = str_replace('&hearts;', '<3', $s);
            $s = str_replace('&lt;', '<', $s);
            $s = str_replace('&amp;lt;', '<', $s);
            $s = str_replace('&laquo;', '', $s);
            $s = str_replace('&amp;laquo;', '', $s);
            $s = str_replace('&gt;', '>', $s);
            $s = str_replace('&amp;gt;', '>', $s);
            $s = str_replace('&raquo;', '', $s);
            $s = str_replace('&amp;raquo;', '', $s);
            $s = str_replace('&Eacute;', 'E', $s);
            $s = str_replace('&eacute;', 'e', $s);
            $s = str_replace('&amp;eacute;', 'e', $s);
            $s = str_replace('&Aacute;', 'A', $s);
            $s = str_replace('&amp;aacute;', 'a', $s);
            $s = str_replace('&aacute;', 'a', $s);
            $s = str_replace('&amp;uacute;', 'u', $s);
            $s = str_replace('&uacute;', 'u', $s);
            $s = str_replace('&amp;ntilde;', 'n', $s);
            $s = str_replace('&ntilde;', 'n', $s);
            $s = str_replace('&sup1;', '<sup>1</sup>', $s);
            $s = str_replace('&sup2;', '<sup>2</sup>', $s);
            return $s;
        }

        /**
         * @access   public
         * @function $turtles->cleanLJ()
         * @param $post
         * @param string $p
         * @param string $s
         * @return string|string[]|null
         * @since    2.1.4
         */
        public function cleanLJ($post, $p = 'y', $s = 'y')
        {
            if ($p == 'y') {
                $post = strip_tags($post);
            }

            if ($s == 'y') {
                $post = trim(htmlentities($post, ENT_QUOTES, 'UTF-8'));
            } elseif ($s == 'n') {
                $post = trim($post);
            } else {
                $post = trim(htmlentities($post, ENT_NOQUOTES, 'UTF-8'));
            }

            $post = str_replace(array("\r\n", "\r"), "\n", $post);
            $post = preg_replace('|(?<!<br />)\s*\n\n|', "<br /><br />\n", $post);

            return $post;
        }

        /**
         * A rather bo-bo way of doing this, but I can't rely on the
         * function JournalPress uses, as Listing Admin handles cuts
         * differently.
         *
         * @access   public
         * @function $turtles->formatEntry()
         * @param $s
         * @param $c
         * @param $n
         * @param $k
         * @return string
         * @since    2.1.4
         */
        public function formatEntry($s, $c, $n, $k)
        {
            global $seahorses, $qname, $qwebs;

            $w = $qwebs;
            if ($seahorses->getOption('updates_prettyurls') == 'y') {
                if (strpos($w, '/') !== false) {
                    $w .= 'e/';
                } else {
                    $w .= '/e/';
                }
            } else if (strpos($w, '/') !== false) {
                $w .= '?e=';
            } else {
                $w .= '/?e=';
            }

            $b = trim($c);
            if ($s == 'dw') {
                if (strpos($b, '{MORE}') !== false) {
                    $e = explode('{MORE}', $c);
                    $r = $e[0];
                    $r .= '<cut text="Read More...">' . $e[1] . "</cut>\n";
                    if ($seahorses->getOption('updates_crosspost_dw_link') == 'y' && $k == 1) {
                        $r .= '<br /><br /><small>Originally posted at <a href="' . $w . $n . '">' .
                            $qname . '</a>. Please post comments there.</small>';
                    }
                } else {
                    $r = $b;
                    if ($seahorses->getOption('updates_crosspost_dw_link') == 'y' && $k == 1) {
                        $r .= '<br /><br /><small>Originally posted at <a href="' . $w . $n . '">' .
                            $qname . '</a>. Please post comments there.</small>';
                    }
                }
            } elseif ($s == 'ij') {
                if (strpos($b, '{MORE}') !== false) {
                    $e = explode('{MORE}', $c);
                    $r = $e[0];
                    $r .= '<lj-cut text="Read More...">' . $e[1] . "</lj-cut>\n";
                    if ($seahorses->getOption('updates_crosspost_ij_link') == 'y' && $k == 1) {
                        $r .= '<br /><br /><small>Originally posted at <a href="' . $w . $n . '">' .
                            $qname . '</a>. Please post comments there.</small>';
                    }
                } else {
                    $r = $b;
                    if ($seahorses->getOption('updates_crosspost_ij_link') == 'y' && $k == 1) {
                        $r .= '<br /><br /><small>Originally posted at <a href="' . $w . $n . '">' .
                            $qname . '</a>. Please post comments there.</small>';
                    }
                }
            } elseif ($s == 'lj') {
                if (strpos($b, '{MORE}') !== false) {
                    $e = explode('{MORE}', $c);
                    $r = $e[0];
                    $r .= '<lj-cut text="Read More...">' . $e[1] . "</lj-cut>\n";
                    if ($seahorses->getOption('updates_crosspost_lj_link') == 'y' && $k == 1) {
                        $r .= '<br /><br /><small>Originally posted at <a href="' . $w . $n . '">' .
                            $qname . '</a>. Please post comments there.</small>';
                    }
                } else {
                    $r = $b;
                    if ($seahorses->getOption('updates_crosspost_lj_link') == 'y' && $k == 1) {
                        $r .= '<br /><br /><small>Originally posted at <a href="' . $w . $n . '">' .
                            $qname . '</a>. Please post comments there.</small>';
                    }
                }
            }

            return $r;
        }

        /**
         * The following functions are from my personal CMS script, vle. Most
         * of the coding is bit hodge-podge, so I recommend talking with me
         * about using any of it, if to explain what the hell I was getting at.
         *
         * @subpackage Formatting
         * @since      2.1.4
         */

        /**
         * @access   public
         * @function $turtles->blogURL()
         * @since    2.1.4
         */
        public function blogURL()
        {
            global $seahorses;
            return str_replace('index.php', '', $seahorses->getOption('updates_url'));
        }

        /**
         * @access   public
         * @function $turtles->makeEntryLink()
         * @param $e
         * @return string
         * @since    2.1.4
         */
        public function makeEntryLink($e)
        {
            global $seahorses;

            return ($seahorses->getOption('updates_prettyurls') == 'n' ? $this->blogURL() .
                '?e=' . $e : $this->blogURL() . 'e/' . $e);
        }

        /**
         * @access   public
         * @function $turtles->makeExcerpt()
         * @param $i
         * @return string
         * @since    2.1.4
         */
        public function makeExcerpt($i)
        {
            global $seahorses;
            if ($seahorses->getOption('updates_prettyurls') == 'y') {
                return '<a href="' . $this->blogURL() . "e/$i\">Read More &#38;#187;</a>";
            }

            return '<a href="' . $this->blogURL() . "?e=$i\">Read More &#38;#187;</a>";
        }

        /**
         * Originally fom Matt <http://ma.tt/scripts/autop/> and WordPress
         * <http://wordpress.org>
         *
         * Heavy, heavy editing was done, here in this file of... entry...
         * ness. :x If taking, //please// make sure you keep in mind this
         * will most likely blow up in your face, because I really fucking
         * suck at coding for other people that aren't me. Needs must, as
         * the saying goes (but isn't definitively correct in this
         * scenario, BUT WHATEVS).
         *
         * @access   public
         * @function $turtles->cleanText()
         * @param $p
         * @param string $m
         * @return string|string[]|null
         * @since    2.1.4
         */
        public function cleanText($p, $m = 'html')
        {
            /**
             * Once we pass text through this function, shit blows up; aka
             * we split shit up, we re-format, and for the fucking love of
             * code, we make sure shit doesn't come back (I AM LOOKING AT YOU
             * HTML5) and bite us in our asses.
             *
             * First things first: change any formating htmlentities() has
             * done to our text, and change it to it's as-is character (there
             * was probably a much better way of saying that; the fuck, self).
             */
            $p = str_replace('&lt;', '<', $p);
            $p = str_replace('&#60;', '<', $p);
            $p = str_replace('&gt;', '>', $p);
            $p = str_replace('&#62;', '>', $p);
            $p = str_replace('&quot;', '"', $p);
            $p = str_replace('&#39;', "'", $p);

            $p .= "\n";
            $allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|section|' .
                'dl|dd|dt|menu|ul|ol|li|pre|select|fieldset|legend|form|map|area|code|blockquote|' .
                'address|math|style|input|p|h[1-6]|hr)';
            $p = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $p);
            $p = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $p);
            $p = str_replace(array("\r\n", "\r"), "\n", $p);
            if (strpos($p, '<object') !== false) {
                $p = preg_replace('|\s*<param([^>]*)>\s*|', '<param$1>', $p);
                $p = preg_replace('|\s*</embed>\s*|', '</embed>', $p);
            }
            $p = preg_replace("/\n\n+/", "\n\n", $p);
            $p = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $p);
            $p = preg_replace('|<p>\s*?</p>|', '', $p);
            $p = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', '<p>$1</p>$2', $p);
            $p = preg_replace('|<p>|', '$1<p>', $p);
            $p = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $p);
            $p = preg_replace('|<p>(<li.+?)</p>|', '$1', $p);
            $p = preg_replace('|<p><blockquote([^>]*)>|i', '<blockquote$1><p>', $p);
            $p = str_replace('</blockquote></p>', '</p></blockquote>', $p);
            $p = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', '$1', $p);
            $p = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', '$1', $p);
            $p = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', '$1', $p);
            $p = preg_replace('!<br>(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $p);
            $p = preg_replace("|\n</p>$|", '</p>', $p);

            /**
             * Grab our pre-defined separaters and encode them~
             */
            $p = str_replace(' - ', ' &#8211; ', $p);
            $p = str_replace(array('--', ' -- '), ' &#8212; ', $p);

            /**
             * For <samp>, <pre> and <code>; see dodgy fix note above
             */
            $p = str_replace('&amp;lt;', '&lt;', $p);
            $p = str_replace('&amp;gt;', '&gt;', $p);
            $p = str_replace(array('&amp;#034;', '&amp;#34;'), '&#34;', $p);
            $p = str_replace(array('&amp;#039;', '&amp;#39;'), '&#39;', $p);
            $p = str_replace('&amp;quot;', '&quot;', $p);

            /**
             * If the markup is set to HTML5, change the entities :D
             */
            if ($m == 'html5') {
                $p = str_replace('&lt;', '&#60;', $p);
                $p = str_replace('&gt;', '&#62;', $p);
                $p = str_replace('&quot;', '&#34;', $p);
            }

            /**
             * From another WordPress function, wptexturize()
             *
             * Edited from the original, I made sure that the pattern matches
             * any "2nd", "3rd", et al., tags that //aren't// wrapped in a
             * link tag :D
             */
            $p = preg_replace('#(?<![</?a>])([0-9]+)(nd|rd|st|th)#', '$1<sup>$2</sup>', $p);

            return $p;
        }

        /**
         * @access   public
         * @function $turtles->templateEntries()
         * @param $d
         * @param string $b
         * @return string
         * @since    2.1.4
         */
        public function templateEntries($d, $b = 'default')
        {
            global $options, $my_website, $seahorses, $wolves;

            $listing = $wolves->getListings($options->listingID, 'object');
            $getItem = $this->getEntry($d);
            $e = $getItem->uID;
            $mark = $seahorses->getOption('markup') === 'xhtml' ? ' /' : '';
            if (
                ($options->listingID == '0' || $options->listingID == 0) ||
                ($options->listingID != '0' && $options->listingID != 0 && empty($listing->updates))
            ) {
                $fulltemp = $seahorses->getTemplate('updates_template');
            } else {
                $fulltemp = $listing->updates;
            }

            $disabled = $getItem->uDisabled;
            if ($disabled == 1) {
                $count = count($this->commentsList($e));
                if ($count == 0) {
                    if ($seahorses->getOption('updates_prettyurls') == 'y') {
                        $comment_link = 'No Comments &amp;raquo; <a href="e/' . $getItem->uID . '/#reply">Add a Comment</a>';
                    } else {
                        $comment_link = 'No Comments &amp;raquo; <a href="' . $options->url . 'e=' . $e . '#reply">Add a Comment</a>';
                    }
                } elseif ($count == 1) {
                    if ($seahorses->getOption('updates_prettyurls') == 'y') {
                        $comment_link = '1 Comment &amp;raquo; <a href="e/' . $getItem->uID . '/#reply">Add a Comment</a>';
                    } else {
                        $comment_link = '1 Comment &amp;raquo; <a href="' . $options->url . 'e=' . $e . '#reply">Add a Comment</a>';
                    }
                } elseif ($count > 1) {
                    if ($seahorses->getOption('updates_prettyurls') == 'y') {
                        $comment_link = $count . ' Comments &amp;raquo; <a href="e/' . $getItem->uID . '/#reply">Add a Comment</a>';
                    } else {
                        $comment_link = $count . ' Comments &amp;raquo; <a href="' . $options->url . 'e=' . $e . '#reply">Add a Comment</a>';
                    }
                }
                if (
                    ($options->crosspost == 'y') && ($getItem->uDW == 'y' || $getItem->uIJ == 'y' || $getItem->uLJ == 'y')
                ) {
                    $comment_link .= "<br$mark>\n";
                    if ($getItem->uDW == 'y') {
                        $comment_link .= '<img src="' .
                            $my_website . "img/write_bw.png\" alt=\"\"$mark> <a href=\"http://" .
                            $seahorses->getOption('updates_crosspost_dw_user') . '.dreamwidth.org/"' .
                            ' title="External Link: Dreamwidth">Dreamwidth &amp;raquo;</a> ';
                    }
                    if ($getItem->uIJ == 'y') {
                        $comment_link .= '<img src="' .
                            $my_website . "img/write_bw.png\" alt=\"\"$mark> <a href=\"http://" .
                            $seahorses->getOption('updates_crosspost_ij_user') . '.insanejournal.com/"' .
                            ' title="External Link: InsaneJournal">InsaneJournal &amp;raquo;</a> ';
                    }
                    if ($getItem->uLJ == 'y') {
                        $ex = preg_split("/[\s|]+/", $getItem->uLJOpt);
                        if ($ex[2] === 'community:') {
                            $comment_link .= '<img src="' .
                                $my_website . "img/write_bw.png\" alt=\"\"$mark> <a href=\"http://" .
                                $seahorses->getOption('updates_crosspost_lj_user') . '.livejournal.com/"' .
                                ' title="External Link: LiveJournal">LiveJournal &amp;raquo;</a> ';
                        } else {
                            $comment_link .= '<img src="' .
                                $my_website . "img/write_bw.png\" alt=\"\"$mark> <a href=\"http://community.livejournal.com/" .
                                str_replace('community:', '', $ex[2]) . '/" title="External Link: LiveJournal">LiveJournal &amp;raquo;</a> ';
                        }
                    }
                }
                $comments = $comment_link;
            } else {
                $comments = 'Comments Closed';
            }
            $comments = trim($comments);

            if (($b == 'default') && strpos($getItem->uEntry, '{MORE}') !== false) {
                $x = explode('{MORE}', $getItem->uEntry);
                $getItem->uEntry = $x[0] . $this->makeExcerpt($getItem->uID);
            }

            if ($seahorses->getOption('updates_prettyurls') == 'y') {
                $permar = $this->blogURL() . 'e/' . $getItem->uID;
            } else {
                $permar = $this->blogURL() . '?e=' . $getItem->uID;
            }

            $perma = '<a href="' . $permar . '">Permalink</a>';
            $format = str_replace('{categories}', $wolves->pullSubjects_Links($getItem->uCategory, '!'), $fulltemp);
            $format = str_replace('{comments}', $comments, $format);
            $format = str_replace('{date}', date($seahorses->getTemplate('date_template'), strtotime($getItem->uAdded)), $format);
            $format = str_replace('{entry}', "<div class=\"entry\">\n" . $this->cleanText($getItem->uEntry) . "</div>\n", $format);
            $format = str_replace('{permalink}', $perma, $format);
            $format = str_replace('{permalink_raw}', $permar, $format);
            $format = str_replace('{title}', $getItem->uTitle, $format);

            return "<div class='post'>\n" . $this->cleanText(html_entity_decode($format)) . "\n</div>\n";
        }

        /**
         * @access   public
         * @function $turtles->updatesDefault()
         * @param $s
         * @param $b
         * @since    2.1.4
         */
        public function updatesDefault($s, $b)
        {
            global $_ST, $octopus, $options, $page, $scorpions, $seahorses,
                   $start_entry_lone, $tigers, $wolves;

            if ($s == 'archives') {
                echo "<div class=\"archives bymonth\">\n<h3>By Month</h3>\n";
                $select = "SELECT DATE_FORMAT(`uAdded`, '%M %Y') AS `month`, DATE_FORMAT(`uAdded`," .
                    " '%Y%m') AS `friendly`, COUNT(*) AS `entries` FROM `$_ST[updates]` WHERE" .
                    " `uPending` = '0' GROUP BY `friendly` DESC";
                $true = $scorpions->query($select);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'The script was unable to select' .
                        ' the updates from the specified listing.', false);
                } else {
                    echo $octopus->alternate('menu', $seahorses->getOption('markup'));
                    while ($getItem = $true->fetch_object()) {
                        echo ' <li><a href="?m=' . $getItem->friendly . '">' .
                            $getItem->month . '</a> (' . $getItem->entries . ")</li>\n";
                    }
                    echo $octopus->alternate('menu', $seahorses->getOption('markup'), 1);
                }
                echo "</div>\n";

                echo "<div class=\"archives bylisting\">\n<h3>By Listing</h3>\n";
                $listings = $wolves->listingsList('id', '0', 'status');
                if ((is_countable($listings) ? count($listings) : 0) > 0) {
                    echo $octopus->alternate('menu', $seahorses->getOption('markup'));
                    foreach ($listings as $id) {
                        $listing = $wolves->getListings($id, 'object');
                        $query = $scorpions->query("SELECT * FROM `$_ST[updates]` WHERE `uCategory`" .
                            " LIKE '%!" . $id . "!%' AND `uPending` = '0'");
                        $count = $scorpions->total($query);
                        if ($count > 0) {
                            echo ' <li><a href="?s=' . $listing->id . '">' . $listing->subject .
                                '</a> (' . $count . ")</li>\n";
                        }
                    }
                    echo $octopus->alternate('menu', $seahorses->getOption('markup'), 1);
                }
                echo "</div>\n";
            } elseif ($s == 'blog') {
                if ($b == 'y') {
                    $select = "SELECT * FROM `$_ST[updates]` WHERE `uPending` = '0' ORDER BY" .
                        " `uAdded` DESC LIMIT $start_entry_lone, " . $options->pagination;
                } else {
                    $select = "SELECT * FROM `$_ST[updates]` WHERE `uCategory` LIKE" .
                        " '%!" . $options->listingID . "!%' AND `uPending` = '0' ORDER BY `uAdded`" .
                        " DESC LIMIT $start_entry_lone, " . $options->pagination;
                }
                $true = $scorpions->query($select);
                if ($true == false) {
                    $tigers->displayError('Database Error', 'The script cannot select updates' .
                        ' from the database.', true, $select);
                }

                if ($scorpions->total($true) > 0) {
                    while ($getItem = $scorpions->obj($true)) {
                        echo $this->templateEntries($getItem->uID);
                    }

                    echo "\n<p id=\"pagination\">\n";
                    $o = $s == 'y' ? 'y' : $options->listingID;
                    $total = count($this->updatesList($o));
                    $pages = ceil($total / $options->pagination);

                    $listing = $wolves->getListings($options->listingID, 'object');
                    $prettyu = in_array($options->listingID, $wolves->listingsList())
                        ? 'n' : $seahorses->getOption('updates_prettyurls');
                    $blogurl = in_array($options->listingID, $wolves->listingsList())
                        ? $listing->url : $this->blogURL();

                    if ($prettyu == 'y') {
                        $next = $page + 1;
                        $prev = $page - 1;

                        if ($page > 1) {
                            echo '<a class="und" href="' . $blogurl . 'p/' . $prev . '">&#171; Previous</a> ';
                        } else {
                            echo '<span>&#171; Previous</span> ';
                        }

                        if ($page < $pages) {
                            echo '<a class="und" href="' . $blogurl . 'p/' . $next . '">Next &#187;</a>';
                        } else {
                            echo '<span>Next &#187;</span>';
                        }
                    } else {
                        PaginationUtils::rangedPagination($page, $pages, $blogurl . '?');
                    }

                    echo "\n</p>\n";
                } else {
                    echo "<p class=\"tc\">Currently no entries!</p>\n";
                }
            }
        }

        /**
         * @access   public
         * @function $turtles->comments()
         * @param $i
         * @since    2.1.4
         */
        public function comments($i)
        {
            global $my_email, $_ST, $octopus, $tigers, $scorpions, $seahorses;

            $select = "SELECT * FROM `$_ST[updates_comments]` WHERE `eNiq` = '$i'" .
                " AND `cFlag` = 'legit' AND `cPending` = '0' ORDER BY `cAdded`, `cID` ASC";
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script cannot select comments' .
                    ' from the database.', false);
            }
            $count = $scorpions->total($true);

            $mark = $seahorses->getOption('markup') == 'xhtml' ? ' /' : '';
            if ($count > 0) {
                echo $octopus->alternate('menu', $seahorses->getOption('markup'), 0, 'commentsDisplay');
                while ($getItem = $scorpions->obj($true)) {
                    $default = str_replace('inc/', '', $seahorses->getOption('adm_http')) . 'default.png';
                    $size = $seahorses->getOption('updates_gravatar_size');
                    $rating = $seahorses->getOption('updates_gravatar_rating');
                    $gravatar = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($getItem->cEmail) .
                        '&#38;rating=' . $rating . '&#38;default=' . urlencode($default) .
                        '&#38;size=' . $size;

                    if ($getItem->cEmail == $my_email) {
                        $image_class = '_author';
                    } else {
                        $image_class = '_other';
                    }

                    if (empty($getItem->cURL)) {
                        $n = '<strong>' . $getItem->cName . '</strong>';
                    } else {
                        $n = '<a href="' . $getItem->cURL . '" title="External Link: ' . $getItem->cName .
                            ' at ' . $octopus->shortURL($getItem->cURL) . '">' . $getItem->cName . ' &raquo;</a>';
                    }

                    echo '<li id="commentid-' . $getItem->cID . "\">\n";
                    if ($seahorses->getOption('updates_gravatar') == 'y') {
                        echo '<p class="i"><img src="' . $gravatar . '" class="commentImage" alt=""' . $mark . "></p>\n";
                    } else {
                        echo '<p class="i"><img src="' . $default . '" class="commentImage" alt=""' . $mark . "></p>\n";
                    }
                    echo "<p class=\"c$image_class\">{$n}<br$mark>\n";
                    echo '<span class="commentBox">' . date($seahorses->getTemplate('date_template'), strtotime($getItem->cAdded)) .
                        '</span> &#187; <a href="#commentid-' . $getItem->cID . "\">Permalink</a><br$mark>\n";
                    if ($mark == 'html') {
                        echo $octopus->lineBreak(html_entity_decode($getItem->cComment)) . "</p>\n";
                    } else {
                        echo nl2br(html_entity_decode($getItem->cComment)) . "</p>\n";
                    }
                    echo "<p style=\"clear: both; margin: 0;\"></p>\n</li>\n";
                }
                echo $octopus->alternate('menu', $seahorses->getOption('markup'), 1, 'commentsDisplay');
            } else {
                echo "<p class=\"tc\">Currently no comments!</p>\n";
            }

            $w = $seahorses->getOption('adm_http') . 'fun-process.inc.php';
            $a2 = random_int(10000, 99999);
            $b1 = random_int(1, 10);
            $b2 = random_int(1, 10);
            $b3 = $b1 + $b2;
            $b4 = $b1 . ' + ' . $b2;
            ?>
            <form action="<?php echo $w; ?>" method="post">
                <?php
                if ($seahorses->getOption('javascript_opt') == 'y') {
                    echo $octopus->javascriptCheat(sha1($seahorses->getOption('javascript_key')));
                }
                ?>
                <input name="eid" type="hidden" value="<?php echo $i; ?>"<?php echo $mark; ?>>
                <?php
                if ($seahorses->getOption('updates_captcha') == 'y') {
                    echo $octopus->captchaCheat($a2);
                }

                if ($seahorses->getOption('updates_antispam') == 'y') {
                    echo $octopus->antispamCheat($b3, $b4, 'comments');
                }
                ?>

                <fieldset id="reply">
                    <legend>Details</legend>
                    <p><label>* <strong>Name:</strong></label>
                        <input name="name" class="input1" type="text" required="required"<?php echo $mark; ?>></p>
                    <p style="    min-height: 60px;
    width: 100%;
    display: inline-block;"><label>* <strong>E-mail</strong><br>
                            Your e-mail will not be published:</label>
                        <input name="email" class="input1" type="email" required="required"<?php echo $mark; ?>></p>
                    <p><label><strong>URL:</strong></label>
                        <input name="url" class="input1" type="url"<?php echo $mark; ?>></p>
                </fieldset>

                <?php
                if ($seahorses->getOption('updates_captcha') == 'y' || $seahorses->getOption('updates_antispam') == 'y') {
                    ?>
                    <fieldset>
                        <legend>Anti-SPAM</legend>
                        <?php
                        if ($seahorses->getOption('updates_captcha') == 'y') {
                            ?>
                            <p>
                                <label style="float: left; padding: 0 1%; width: 48%;">
                                    * <strong>CAPTCHA</strong><br<?php echo $mark; ?>>
                                    Enter the letters/numbers as shown to the right:
                                </label>
                                <input name="captcha" class="input1" style="width: 48%;"
                                       type="text" required="required"<?php echo $mark; ?>><br<?php echo $mark; ?>>
                                <img alt="CAPTCHA Image" title="CAPTCHA Image"
                                     src="<?php echo $seahorses->getOption('adm_http'); ?>fun-captcha.inc.php?k=<?php echo $a2; ?>"
                                     style="width: 48%;"<?php echo $mark; ?>>
                            </p>
                            <?php
                        }
                        ?>

                        <?php
                        if ($seahorses->getOption('updates_antispam') == 'y') {
                            ?>
                            <p>
                                <label style="float: left; padding: 0 1%; width: 48%;">
                                    * <strong><?php echo $b4; ?></strong><br<?php echo $mark; ?>>
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
                    <legend>Comment</legend>
                    <p>
                        <strong>* Comment:</strong><br<?php echo $mark; ?>>
                        <textarea name="comment" cols="50" rows="15" style="height: 150px; width: 100%;"
                                  required="required"></textarea>
                    </p>
                    <p class="tc"><input name="action" class="input2" type="submit"
                                         value="Post Comment"<?php echo $mark; ?>></p>
                </fieldset>
            </form>
            <?php
        }

        /**
         * @access   public
         * @function $turtles->grabEntryDate()
         * @param string $i
         * @return string
         * @since    2.1.4
         */
        public function grabEntryDate($i = '')
        {
            global $_ST, $scorpions, $tigers, $wolves;

            $select = "SELECT `uAdded` FROM `$_ST[updates]`";
            if ($i != '' && in_array($i, $wolves->listingsList())) {
                $select .= " WHERE `uCategory` LIKE '%!$i!%'";
            }
            $select .= ' ORDER BY `uAdded` DESC LIMIT 1';
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select the' .
                    ' last entry from the updates table.', false);
            }
            $getItem = $scorpions->obj($true);
            $r = empty($getItem->uAdded) ? 'Date Unavailable' : date('D, d M Y H:i:s', strtotime($getItem->uAdded));

            return $r . ' GMT';
        }

        /**
         * @access   public
         * @function $turtles->getRSS()
         * @param string $c
         * @since    2.1.4
         */
        public function getRSS($c = '')
        {
            global $_ST, $laoptions, $seahorses, $scorpions, $tigers, $wolves;

            $b = false;
            $select = "SELECT * FROM `$_ST[updates]`";
            if ($c != '' && in_array($c, $wolves->listingsList())) {
                $b = true;
                $d = $wolves->getListings($b, 'object');
                $select .= " WHERE `uCategory` LIKE '%!$c!%'";
            }
            $select .= ' ORDER BY `uAdded` DESC';
            $true = $scorpions->query($select);
            if ($true == false) {
                $tigers->displayError('Database Error', 'The script was unable to select' .
                    ' the entries.', false);
            }
            $getSing = $scorpions->obj($true);

            $e = (object)array(
                'desc' => ($b == true ? $d->subject . ' fanlisting' : 'A Listing Collective'),
                'title' => ($b == true ? $d->title : $seahorses->getOption('collective_name')),
                'url' => ($b == true ? $d->url : $seahorses->getOption('my_website'))
            );

            /**
             * Get header and start building the RSS feed
             */
            header('Content-Type: text/xml; charset=ISO-8859-1');
            echo '<?xml version="1.0" encoding="iso-8859-1"?>' .
                "\n<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/" .
                'content/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:wfw="http://' .
                "wellformedweb.org/CommentAPI/\">\n<channel>\n<title>" . $e->title .
                "</title>\n<description>" . $e->desc . "</description>\n" .
                '<atom:link href="' . str_replace('inc/', '', $seahorses->getOption('adm_http')) .
                "rss.php\" rel=\"self\" type=\"application/rss+xml\" />\n<link>" . $e->url .
                "</link>\n<pubDate>" . $this->grabEntryDate($c) .
                "</pubDate>\n<generator>" . $laoptions->version . "</generator>\n";

            /**
             * Grab RSS items \o/
             */
            while ($getItem = $scorpions->obj($true)) {
                $entry = $this->strip_characters($getItem->uEntry);
                $entry = strip_tags($entry);
                if (strpos($getItem->uEntry, '{MORE}')) {
                    $x = explode('{MORE}', $entry);
                    $entry = trim($x[0]);
                    $y = explode('{MORE}', $getItem->eEntry);
                    $raw = $this->cleanText(html_entity_decode($y[0])) .
                        '<a href="' . $this->makeEntryLink($getItem->uID) .
                        '">Read More &#187;</a>';
                } else {
                    $raw = $this->cleanText(html_entity_decode($getItem->uEntry));
                }
                echo "<item>\n<title>" . $getItem->uTitle . "</title>\n" .
                    '<pubDate>' . date('D, d M Y H:i:s', strtotime($getItem->uAdded)) .
                    " GMT</pubDate>\n<guid" .
                    ' isPermaLink="true">' . $this->makeEntryLink($getItem->uID) .
                    "</guid>\n<description>$entry</description>" .
                    "\n<content:encoded><![CDATA[$raw]]></content:encoded>\n</item>\n";
            }
            echo "</channel>\n</rss>";
        }

        # End function list here :'D
    }
}

$turtles = new turtles();
