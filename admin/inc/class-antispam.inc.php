<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <class-antispam.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('laantispam')) {
    class laantispam
    {

        public array $bbcode = array('[b]', '[/b]', '[i]', '[/i]', '[link]', '[/link]', '[link',
            '[link=', '[url]', '[/url]', '[url', '[url=');

        /**
         * @function  $laantispam->clean()
         *
         * @param     $s , text; text to be cleaned
         * @param     $r , character; 'y' for cleaning SQL queries from
         * $s; 'n' for no
         * @param     $p , character; 'y' for replacing characters with text
         * representations (e.g. . for DOTTIE, @ for ATTIE, et al.)
         * @param     $c , string; for encoding and decoding param $p
         * @return string
         * @return string
         */
        public function clean($s, $r = 'y', $p = 'y', $c = 'clean'): string
        {
            if ($r == 'y') {
                $a = array(
                    'OR',
                    " OR ''",
                    "' OR ''",
                    " AND ''",
                    " AND '' OR",
                    'DROP',
                    'DELETE',
                    'DELETE FROM'
                );
                $s = str_replace($a, '', $s);
            }

            if ($p == 'y') {
                if ($c == 'clean') {
                    $s = str_replace(' ', '+', $s);
                    $s = str_replace('.', 'DOTTIE', $s);
                    $s = str_replace('&', 'ANDIE', $s);
                    $s = str_replace('%26', 'ANDIE', $s);
                    $s = str_replace('@', 'ATTIE', $s);
                    $s = str_replace('%40', 'ATTIE', $s);
                    $s = str_replace('-', 'DASHIE', $s);
                    $s = str_replace('_', 'SCORIE', $s);
                } elseif ($c == 'declean') {
                    $s = str_replace('+', ' ', $s);
                    $s = str_replace('DOTTIE', '.', $s);
                    $s = str_replace('ANDIE', '&', $s);
                    $s = str_replace('ATTIE', '@', $s);
                    $s = str_replace('DASHIE', '-', $s);
                    $s = str_replace('SCORIE', '_', $s);
                }
            }
            return trim($s);
        }

        /**
         * Get required and custom SPAM words, compile and return an array \o/
         *
         * @function  $laantispam->spamarray()
         */
        public function spamarray(): array
        {
            global $seahorses, $tigers;

            $spam_words_req = $seahorses->getOption('antispam_spam_words_required');
            $a = explode('|', $spam_words_req);
            $a = $tigers->emptyarray($a);

            $spam_words = $seahorses->getOption('antispam_spam_words');
            if (!empty($spam_words)) {
                $b = explode('|', $spam_words);
                $b = $tigers->emptyarray($b);
            } else {
                $b = array();
            }

            return $tigers->emptyarray(array_merge($a, $b));
        }

        /**
         * The actual laAntispam function! \o/ So, the script isn't
         * nearly as... busy as it looks. It's a math problem and
         * rating system all in one, and checks for common spamming
         * errors. Don't worry, though; all of the things looked for
         * are only errors and common SPAM attempts, not necessarily
         * anything one person can do in one fell swoop. :D
         *
         * @function  $laantispam->antispam()
         * @param     $vars , array; options
         * @return object
         * @return object
         */
        public function antispam($vars = array())
        {
            if (is_array($vars)) {
                foreach ($vars as $k => $v) {
                    ${$k} = $v;
                }
            }

            $status = true;
            $antispam = true;
            $pointssys = true;

            $ex = explode(' + ', $s2);
            $answer = $ex[0] + $ex[1];
            if (
                (!isset($mathproblem)) ||
                ($mathproblem != $s1 || $answer != $mathproblem)
            ) {
                $status = false;
                $antispam = false;
            }

            $points = 0;
            if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $points -= 2;
            }

            if (isset($url) && strlen($url) > 40) {
                --$points;
            }

            if (!empty($comments)) {
                if (
                    strpos($comments, 'http://') !== false ||
                    strpos($comments, 'https://') !== false ||
                    strpos($comments, 'www.') !== false
                ) {
                    --$points;
                }

                if (
                    strpos($comments, '[link=') !== false ||
                    strpos($comments, '[link]') !== false ||
                    strpos($comments, '[url=') !== false ||
                    strpos($comments, '[url]') !== false
                ) {
                    $points -= 2;
                }

                if (preg_match('/(<.*>)/', $comments)) {
                    $points -= 3;
                }

                if (strlen($comments) > 2000) {
                    $points -= 2;
                }

                foreach ($this->spamarray() as $not) {
                    if (strpos($comments, (string) $not) !== false) {
                        $points -= 2;
                    }
                }
            }

            if ($points < -5) {
                $status = false;
                $pointssys = false;
            }

            return (object)array(
                'status' => $status,
                'antispam' => $antispam,
                'points' => $pointssys
            );
        }

        # End functions right harrrr~! :D
    }
}

$laantispam = new laantispam();
