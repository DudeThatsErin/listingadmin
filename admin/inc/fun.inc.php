<?php
declare(strict_types=1);
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('tigers')) {
    class tigers
    {
        /**
         * Clean and escape the text passed through the $post param.
         *
         * @access   public
         * @function $tigers->cleanMys()
         * @param $post
         * @param string $p
         * @param string $s
         * @param string $e
         * @return string
         * @since    1.0
         */
        public function cleanMys($post, $p = 'y', $s = 'y', $e = 'y')
        {
            global $scorpions;

            $post = (string)$post;

            if ($p == 'y') {
                $post = strip_tags($post);
            }

            /**
             * If any characters not supported by htmlentities() exist, convert
             * them to their HTML-compliant entities :D
             *
             * @since 2.3alpha
             */
            $p = iconv('UTF-8', 'ASCII//TRANSLIT', $p);

            if ($s == 'y') {
                $post = trim(htmlentities($post, ENT_QUOTES, 'ISO-8859-15'));
            } elseif ($s == 'n') {
                $post = trim($post);
            } else {
                $post = trim(htmlentities($post, ENT_NOQUOTES, 'ISO-8859-15'));
            }

            if ($e == 'y') {
                $post = $scorpions->escape($post);
            }

            $post = trim($post);
            return $post;
        }

        /**
         * Replaces empty favourite fields with "NONE".
         *
         * @access   public
         * @function $tigers->replaceArray()
         * @param $p
         * @return string
         * @since    2.3beta
         */
        public function replaceArray($p)
        {
            if (empty($p) || $p == ' ') {
                $p = 'NONE';
            }
            $p = $this->cleanMys($p);
            return $p;
        }

        public function emptyarray($array)
        {
            $n = [];
            $e = [];

            foreach ($array as $k) {
                $k = trim($k);
                $e[] = '0';
                if (!in_array($k, $e) && $k != '') {
                    $n[] = $k;
                }
            }

            return $n;
        }

        /**
         * Empties multi-dimensional arrays; copy of $this->emptyarray().
         *
         * @access   public
         * @function $tigers->emptymulti()
         * @param $a
         * @return array
         * @since    2.4
         */
        public function emptymulti($a)
        {
            $n = array();
            $e = array();

            foreach ($a as $k => $v) {
                $v = trim($v);
                $e[] = '0';
                if (!in_array($v, $e) && $v != '') {
                    $n[$k] = $v;
                }
            }

            return $n;
        }

        /**
         * Empties objects of false or empty elements.
         *
         * @access   public
         * @function $tigers->emptyobject()
         * @param $o
         * @return object
         * @since    2.4
         */
        public function emptyobject($o)
        {
            $n = (object)array();

            foreach ($o as $k => $v) {
                $v = trim($v);
                if (!empty($v) && $v != '' && $v != '0' && $v != ' ') {
                    $n->$k = $v;
                }
            }

            return $n;
        }

        /**
         * Takes the 'collective' value out of the affiliates array and replaces
         * it with it's actual value, 0.
         *
         * @access   public
         * @function $tigers->collective()
         * @param $a
         * @return mixed
         * @since    2.3beta
         */
        public function collective($a)
        {
            $n = array();
            $e = array();

            if (($s = array_search('collective', $a)) !== false) {
                array_splice($a, $s, 1, '0');
                $n = $a;
            } else {
                $n = $a;
            }

            return $n;
        }

        /**
         * Build collective variables; equivalent of $wolves->getListings().
         *
         * @access   public
         * @function $tigers->buildCollective()
         * @since    2.3alpha
         */
        public function buildCollective()
        {
            global $seahorses;

            return (object)array(
                'subject' => $seahorses->getOption('collective_name'),
                'title' => $seahorses->getOption('collective_name'),
                'url' => $seahorses->getOption('my_website')
            );
        }

        /**
         * @access    public
         * @function  $ichigorukia->logout()
         * @copyright 2010 Shrine Manager <http://scripts.wyngs.net/scripts/shrinemanager/>
         * @since     2.3beta
         */
        public function logout()
        {
            global $leopards;

            setcookie('lalog', '');
            header('Location: ' . $leopards->isPage());
            die('Logging you out, m\'dear...');
        }

        /**
         * @access   public
         * @function $tigers->formDefault()
         * @param $d
         * @param string $c
         * @return string
         * @since    2.2
         */
        public function formDefault($d, $c = '')
        {
            switch ($d) {
                case 'delete':
                    if (!empty($c)) {
                        $n = $c;
                    } else {
                        $n = 'delete.php';
                    }
                    break;
                case 'form':
                    if (!empty($c)) {
                        $n = $c;
                    } else {
                        $n = 'site.php';
                    }
                    break;
                case 'join':
                    if (!empty($c)) {
                        $n = $c;
                    } else {
                        $n = 'join.php';
                    }
                    break;
                case 'reset':
                    if (!empty($c)) {
                        $n = $c;
                    } else {
                        $n = 'reset.php';
                    }
                    break;
                case 'update':
                    if (!empty($c)) {
                        $n = $c;
                    } else {
                        $n = 'update.php';
                    }
                    break;
            }
            return $n;
        }

        /**
         * @access   public
         * @function $tigers->replaceSpec()
         * @param $p
         * @return string
         * @since    1.0
         */
        public function replaceSpec($p)
        {
            $e = trim($p);
            $e = html_entity_decode($e, ENT_QUOTES, 'ISO-8859-15');
            return $e;
        }

        /**
         * Error display function; formats and displays regular ole errors, or
         * MySQL errors.
         *
         * @access   public
         * @function $tigers->displayError()
         * @param $e
         * @param $b
         * @param bool $a
         * @param string $q
         * @since    1.0
         */
        public function displayError($e, $b, $a = true, $q = 'n')
        {
            global $scorpions;

            echo "<h3>$e</h3>\n";

            if ($e == 'Database Error') {
                $class = 'mysql';
            } elseif ($e == 'Script Error') {
                $class = 'php';
            } else {
                $class = 'error';
            }

            $p = '';
            if ($e == 'Connection/Database Error') {
                $p = 'dbButton';
            } elseif ($e == 'Database Error') {
                $p = 'mysqlButton';
            } elseif ($e == 'Script Error') {
                $p = 'scriptButton';
            } elseif ($e == 'Form Error') {
                $p = 'formButton';
            } else {
                $p = 'errorButton';
            }

            $o = explode('|', $b);
            foreach ($o as $i) {
                echo "<p class=\"{$p} tb\">" . $i . "</p>\n";
            }

            if (isset($_COOKIE['lalog']) && $a == true) {
                echo "<h3>Debug Mode</h3>\n<p>There seems to be a few (hopefully minor)" .
                    " issues with the script:</p>\n<p class=\"$p\"><span class=\"$class\">DB" .
                    ' Errors:</span> ';
                if ($q != 'n') {
                    echo $scorpions->database->error();
                    echo "\n<br><em>" . $q . "</em></p>\n";
                } else {
                    echo "</p>\n";
                }
            }

            exit();
        }

        /**
         * @access   public
         * @function $tigers->displaySuccess()
         * @param $p
         * @return string
         * @since    2.3alpha
         */
        public function displaySuccess($p)
        {
            return '<p class="successButton"><span class="success">' .
                "Success!</span> $p</p>\n";
        }

        /**
         * Formats a back link, e.g. "Back to Affiliate"
         *
         * @access   public
         * @function $tigers->backLink()
         * @param $p
         * @param string $i
         * @param string $s
         * @param string $e
         * @return string
         * @since    1.0
         */
        public function backLink($p, $i = 'n', $s = '', $e = 'n')
        {
            global $kimadmin, $seahorses, $wolves;

            if ($p == 'aff') {
                if ($i != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="affiliates.php?listing=' . $e .
                        'g=old&#38;d=' . $i . '">&#171; Back to Affiliate</a>' . "</p>\n";
                } elseif ($i == 'n' && $e != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="affiliates.php?listing=' . $e .
                        '">&#171; Back to Affiliates</a>' . "</p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="affiliates.php">&#171; Back' .
                        " to Affiliates</a></p>\n";
                }
            } elseif ($p == 'cat') {
                $link = "\n<p class=\"backLink\"><a href=\"categories.php\">&#171; Back to" .
                    " Categories</a></p>\n";
            } elseif ($p == 'codes') {
                if ($i != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="codes.php?listing=' . $i .
                        '">&#171; Back to "' . $wolves->getSubject($i) . "\" Codes</a></p>\n";
                } else {
                    $link = "\n<p class=\"backLink\"><a href=\"codes.php\">&#171; Back to" .
                        " Codes</a></p>\n";
                }
            } elseif ($p == 'codes_categories') {
                if ($i != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="codes-categories.php?g=old' .
                        '&#38;d=' . $i . "\">&#171; Back to Code Category</a></p>\n";
                } else {
                    $link = "\n<p class=\"backLink\"><a href=\"codes-categories.php\">&#171;" .
                        " Back to Code Categories</a></p>\n";
                }
            } elseif ($p == 'codes_donors') {
                $link = "\n<p class=\"backLink\"><a href=\"codes-donors.php\">&#171; Back" .
                    " to Donors</a></p>\n";
            } elseif ($p == 'codes_sizes') {
                $link = "\n" . '<p class="backLink"><a href="codes-sizes.php">&#171; Back to Sizes</a>' . "</p>\n";
            } elseif ($p == 'emails') {
                $link = "\n<p class=\"backLink\"><a href=\"emails.php\">&#171; Back to" .
                    " Emails</a></p>\n";
            } elseif ($p == 'errors') {
                $link = "\n<p class=\"backLink\"><a href=\"errors.php\">&#171; Back to Error" .
                    " Logs</a></p>\n";
            } elseif ($p == 'joined') {
                if ($i != 'n' && is_numeric($s)) {
                    $link = "\n" . '<p class="backLink"><a href="joined.php?g=old&#38;d=' . $i .
                        '">&#171; Back to Joined Listing</a>' . "</p>\n";
                } elseif ($s != '' && is_numeric($s)) {
                    $link = "\n" . '<p class="backLink"><a href="joined.php?g=searchJoined' .
                        '&#38;c=' . $s . '">&#171; Back to Joined Search</a>' . "</p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="joined.php">&#171; Back to Joined</a>' . "</p>\n";
                }
            } elseif ($p == 'index') {
                $link = "\n" . '<p class="backLink"><a href="' . str_replace('inc/',
                        '', $seahorses->getOption('adm_http')) . '">&#171; Back to Control Panel</a>' .
                    "</p>\n";
            } elseif ($p == 'kim') {
                if (in_array($i, $kimadmin->membersList())) {
                    if ($s != '' && in_array($s, $wolves->listingsList())) {
                        $link = "\n<p class=\"backLink\"><a href=\"kim.php?g=search&#38;" .
                            "listingid=$s\">&#171; Back to KIM Search</a</p>\n";
                    } else {
                        $link = "\n<p class=\"backLink\"><a href=\"kim.php?g=old&#38;d=$i" .
                            "\">&#171; Back to KIM Member</a</p>\n";
                    }
                } else {
                    $link = "\n<p class=\"backLink\"><a href=\"kim.php\">&#171; Back to KIM</a></p>\n";
                }
            } elseif ($p == 'listings') {
                if ($i != 'n' && $e == 'n') {
                    $link = "\n" . '<p class="backLink"><a href="listings.php?g=manage&#38;d=' .
                        $i . '">&#171; Back to Listing</a>' . "</p>\n";
                } elseif ($i != 'n' && $e != 'n') {
                    $y = explode('o=', $e);
                    $x = ucwords($y[1]);
                    $ex = $e != 'n' ? $x : '';
                    $link = "\n" . '<p class="backLink"><a href="listings.php?g=manage&#38;d=' .
                        $i . $e . '">&#171; Back to Listing ' . $ex . "</a></p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="listings.php">&#171; Back to Listings</a>' . "</p>\n";
                }
            } elseif ($p == 'lyrics') {
                if ($i != 'n' && $i == 'albums') {
                    $link = "\n" . '<p class="backLink"><a href="lyrics.php?g=' . $i .
                        '">&#171; Back to Albums</a>' . "</p>\n";
                } elseif ($i != 'n' && $i == 'songs') {
                    $link = "\n" . '<p class="backLink"><a href="lyrics.php?g=' . $i .
                        '">&#171; Back to Songs</a>' . "</p>\n";
                } else {
                    $link = "\n<p class=\"backLink\"><a href=\"lyrics.php\">&#171; Back to" .
                        " Lyrics</a></p>\n";
                }
            } elseif ($p == 'mem') {
                if ($i != 'n') {
                    if ($e != 'n') {
                        $link = "\n" . '<p class="backLink"><a href="members.php?listing=' . $i .
                            '&#38;g=old&#38;d=' . $e . '">&#171; Back to Member</a>' . "</p>\n";
                    } else {
                        $link = "\n" . '<p class="backLink"><a href="members.php?listing=' . $i .
                            '">&#171; Back to "' . $wolves->getSubject($i) . '" Members</a>' . "</p>\n";
                    }
                } else {
                    $link = "\n" . '<p class="backLink"><a href="members.php">&#171; Back to Members</a>' . "</p>\n";
                }
            } elseif ($p == 'options') {
                if ($i != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="options.php?g=' . $i .
                        '">&#171; Back to ' . ucwords($i) . "</a></p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="options.php">&#171; Back to Options</a>' . "</p>\n";
                }
            } elseif ($p == 'quotes') {
                $link = "\n" . '<p class="backLink"><a href="quotes.php">&#171; Back to Quotes</a>' . "</p>\n";
            } elseif ($p == 'temp') {
                $link = "\n" . '<p class="backLink"><a href="templates.php">&#171; Back to Templates</a>' . "</p>\n";
            } elseif ($p == 'temp_e') {
                $link = "\n" . '<p class="backLink"><a href="templates_emails.php">&#171; Back to E-Mail Templates</a>' . "</p>\n";
            } elseif ($p == 'updates') {
                if ($i != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="updates.php?g=old&#38;d=' . $i .
                        '">&#171; Back to Entry</a>' . "</p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="updates.php">&#171; Back to Updates</a>' . "</p>\n";
                }
            } elseif ($p == 'updates_comments') {
                if ($i != 'n') {
                    $link = "\n" . '<p class="backLink"><a href="updates-comments.php?g=old&#38;d=' .
                        $i . '">&#171; Back to Comment</a>' . "</p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="updates-comments.php">&#171;' .
                        ' Back to Comments</a>' . "</p>\n";
                }
            } elseif ($p == 'wishlist') {
                if ($i != 'n') {
                    if (is_numeric($i)) {
                        $link = "\n" . '<p class="backLink"><a href="wishlist.php?g=old&#38;d=' . $i .
                            '">&#171; Back to Wish</a>' . "</p>\n";
                    } elseif ($i == 'new') {
                        $link = "\n" . '<p class="backLink"><a href="wishlist.php?g=new">&#171;' .
                            ' Add Another Wish?</a>' . "</p>\n";
                    }
                } elseif ($i == 'n' && is_numeric($s)) {
                    $link = "\n" . '<p class="backLink"><a href="wishlist.php?g=searchCategories' .
                        '&#38;c=' . $s . '">&#171; Back to Search Results</a>' . "</p>\n";
                } else {
                    $link = "\n" . '<p class="backLink"><a href="wishlist.php">&#171; Back to' .
                        ' Wishlist</a>' . "</p>\n";
                }
            }

            return $link;
        }
    }
}

$tigers = new tigers();
