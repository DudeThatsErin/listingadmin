<?php
/**
 * @copyright  2007
 * @license    GPL Version 3; BSD Modified
 * @author     Tess <theirrenegadexxx@gmail.com>
 * @file       <fun-admin.inc.php>
 * @since      September 2nd, 2010
 * @version    1.8
 */

if (!class_exists('leopards')) {
    class leopards
    {

        /**
         * @function  $leopards->isTitle()
         * @param     $e , string
         */
        public function isTitle($e = '')
        {
            if (!empty($e) && $e != '') {
                return $e;
            } else {
                return 'A Fanlisting Management Script';
            }
        }

        /**
         * Quite a bit different version of $this->isTitle(), which grabs the
         * title from (now this function) the $getTitle variable on each
         * page, and returns it for the page title. $this->getTitle() gets
         * the title for our headers! :D
         *
         * @function  $leopards->getTitle()
         * @version   2.3alpha
         */
        public function getTitle($e = '')
        {
            global $tigers;

            $r = $e;
            if (empty($e) || $e == '') {
                $e = ucwords(str_replace('_', ' ', str_replace('.php', '', $this->isPage())));
            }

            if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                $q = $tigers->cleanMys($_SERVER['QUERY_STRING'], 'y', 'n', 'n');
                if (preg_match('/([a-z]+)=([a-z]+)&([a-z]+)=([a-z]+)/', $q)) {
                    $x = $tigers->emptyarray(explode('=', $q));
                    $p = $tigers->emptyarray(explode('&', $x[1]));
                    $r .= ' &#8211; ' . ucwords(str_replace('_', ' ', $p[0])) .
                        ' &#187; ' . ucwords(str_replace('_', ' ', $p[1]));
                } else {
                    if (preg_match("/([a-z]+)=([a-z]+)/", $q)) {
                        $x = $tigers->emptyarray(explode("=", $q));
                        $r .= " &#187; " . ucwords(str_replace('_', " ", $x[1]));
                    }
                }
            }

            return $r;
        }

        /**
         * @function  $leopards->skins()
         * @version   2.3beta
         */
        public function skins()
        {
            global $_SKINS, $tigers;

            $skins = $tigers->emptyarray($_SKINS);
            foreach ($skins as $skin) {
                echo '    <li><a href="' . basename($_SERVER['PHP_SELF']) .
                    "/?skin=$skin\">" . ucwords($skin) . "</a></li>\n";
            }
        }

        /**
         * @function  $leopards->isPage()
         */
        public function isPage()
        {
            return basename($_SERVER['PHP_SELF']);
        }

        /**
         * This fucking function. D: The original markup of this was
         * created in version... 1.2? :x It was in the installation file
         * for a looo-ooo-oong time, and then I expanded it to go with
         * options.php for the 38495848995 paths, and alas, it is now
         * compiled with the admin functions (APT) :D
         *
         * @function   $leopards->getPaths()
         *
         * @param      $b , string; path or url; optional
         * @param      $u , string; URL path; optional
         * @param      $p , string; absolute path; optional
         * @param      $s , string; optional text to add onto the path
         *
         * @version    2.3alpha
         */
        public function getPaths($b = 'path', $u = '', $p = '', $s = '')
        {
            if ($b == 'path') {
                if (empty($p) || $p == '') {
                    return str_replace(basename($_SERVER['PHP_SELF']), $s, $_SERVER['SCRIPT_FILENAME']);
                } else {
                    return $p;
                }
            } elseif ($b == 'url') {
                if (empty($u) || $u == '') {
                    return 'http://' . $_SERVER['SERVER_NAME'] . '/' . $s;
                } else {
                    return $u;
                }
            }
        }

        /**
         * Return a string of the copyright year and the current year; will
         * return only the current year if $o and $y are the same
         *
         * @function  $leopards->isYear()
         * @param     $o , digits; four-digit year
         */
        public function isYear($o)
        {
            $y = date('Y');
            if ($o == $y) {
                return $o;
            } else {
                return $o . '-' . $y;
            }
        }

        /**
         * If the current page matches the string given, a "current" class
         * will be returned, denoting the current page
         *
         * @function   $leopards->currently()
         * @param      $n , string; base name for the current page without
         * an extension
         */
        public function currently($n, $o = 0)
        {
            if ($n == 'n') {
                return ' class="lastSpace"';
            } elseif ($n != 'index' && basename($_SERVER['PHP_SELF']) == $n . '.php') {
                return " class=\"$n\" id=\"c\"";
            } elseif ($n == 'index') {
                if (basename($_SERVER['PHP_SELF']) == $n . '.php') {
                    return ' class="cp" id="c"';
                } else {
                    return ' class="cp"';
                }
            } else {
                if ($o == 1) {
                    return " class=\"$n last\"";
                } else {
                    return " class=\"$n\"";
                }
            }
        }

        /**
         * @function  $leopards->checkUser()
         * @param     $u , string; username; optional
         * @param     $p , string; password; encrypted
         */
        public function checkUser($u = '', $p)
        {
            global $seahorses;
            if ($u != '') {
                if (addslashes($seahorses->getOption('user_username')) == addslashes($u)
                    && addslashes($seahorses->getOption('user_password')) == addslashes($p)) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                if (addslashes($seahorses->getOption('user_password')) == addslashes($p)) {
                    return 0;
                } else {
                    return 1;
                }
            }
        }

        public function logUser($i, $u, $n = ''): void
        {
            global $_ST, $scorpions, $tigers, $loginInfo;

            /**
             * Although unlikely, in the event we try to log a user without
             * info, we just grab our predefined login object :D
             */
            $o = $n == '' ? $tigers->cleanMys($loginInfo->logInfo) : $n;

            /**
             * Loooooo-ooo-og the info! \o/
             */
            $select = "INSERT INTO `$_ST[logs]` (`userNiq`, `logUser`, `logInfo`, `logLast`)" .
                " VALUES ('$i', '$u', '$o', NOW())";
            $wax = $scorpions->query($select);
        }

        /**
         * @function  $leopards->gravtar()
         * @desc      Builds a gravatar for display~
         */
        public function gravatar()
        {
            global $seahorses;

            $e = $seahorses->getOption('my_email');

            /**
             * Look, I'm not about to feel bad about the X rating, because
             * ajsfhgjgklf how am I supposed to display my own Gravatar? >:|
             */
            $d = 'default.png';
            $s = '100';
            $r = 'X';
            $u = 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($e) .
                '&#38;rating=' . $r . '&#38;default=' . urlencode($d) . '&#38;size=' . $s;

            return $u;
        }

        /**
         * @function  $leopards->buildErrorLog()
         * @desc      Build the error log sort menu for <errors.php>
         * @version   2.3beta
         */
        public function buildErrorLog()
        {
            global $get_errors_array;

            $urls = array(
                'all' => 'errors.php',
                'forms' => 'errors.php?by=forms',
                'spam' => 'errors.php?by=spam',
                'user' => 'errors.php?by=user'
            );

            $new = array();
            foreach ($get_errors_array as $k => $v) {
                if (isset($_GET['by']) && in_array($_GET['by'], array('forms', 'spam', 'user'))) {
                    $p = explode('=', $k);
                    if ($k == $_GET['by']) {
                        $new[$k] = $v;
                    }
                } else {
                    $new[$k] = $v;
                }
            }

            $s = '';
            foreach ($new as $n => $e) {
                $s .= "<span id=\"$n\"><a href=\"" . $urls[$n] . '">' . $e . '</a></span> ';
            }

            return trim($s, ' ');
        }

        # End functionssss here!
    }
}

$leopards = new leopards();
