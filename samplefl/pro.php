<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <pro.php>
 * @version          Robotess Fork
 */

if (!class_exists('fanlisting')) {
    class fanlisting
    {

        /**
         * Get title from page
         * @return string
         */
        public function isTitle(): string
        {
            $s = trim(strip_tags(basename($_SERVER['PHP_SELF'])));
            $s = str_replace('.php', '', $s);
            $s = str_replace('_', ' ', $s);
            $s = ucwords($s);
            return ' &#187; ' . $s;
        }

        /**
         * Get opened year and current year if not the same
         * @param $o
         */
        public function isYear($o): void
        {
            $y = date('Y');
            if ($o === $y) {
                echo $y;
            } else {
                echo $o . '-' . $y;
            }
        }

        /**
         * Get base page for current page
         */
        public function isPage(): string
        {
            return htmlspecialchars(trim(strip_tags(basename($_SERVER['PHP_SELF']))));
        }

        /**
         * Get base URL for our current website :D
         */
        public function isHome(): string
        {

            return 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SERVER_PORT'] . preg_replace('/[a-z]+.php/', '', $_SERVER['PHP_SELF']);
        }

        # End function list~!
    }
}

$fanlisting = new fanlisting();
