<?php
/**
 * Essentially the formatting file, really, that's mostly used
 * for the forms and a select few show- files :D
 *
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-external.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('octopus')) {
    class octopus
    {
        public string $cheatAnti = 'antispamh';
        public string $cheatSpam = 'antispamb';
        public string $cheatCaptcha = 'captchak';
        public string $cheatJavascript = 'cheatsheetCheat';

        public function writeError($t, $u, $b, $i)
        {
            global $seahorses;
            $seahorses->writeMessage(0, $t, $u, $b, $i);
        }

        public function formURL($s, $g)
        {
            global $contact_form, $delete_form, $join_form, $reset_form, $update_form;

            switch ($s) {
                case 'contact':
                    if (!empty($g->form_form)) {
                        $u = $g->form_form;
                    } elseif ((empty($g->form_form)) && (isset($contact_form) && !empty($contact_form))) {
                        $u = $contact_form;
                    } else {
                        $u = basename($_SERVER['PHP_SELF']);
                    }
                    break;

                case 'delete':
                    if (!empty($g->form_delete)) {
                        $u = $g->form_delete;
                    } elseif ((empty($g->form_delete)) && (isset($delete_form) && !empty($delete_form))) {
                        $u = $delete_form;
                    } else {
                        $u = basename($_SERVER['PHP_SELF']);
                    }
                    break;

                case 'join':
                    if (!empty($g->form_join)) {
                        $u = $g->form_join;
                    } elseif ((empty($g->form_join)) && (isset($join_form) && !empty($join_form))) {
                        $u = $join_form;
                    } else {
                        $u = basename($_SERVER['PHP_SELF']);
                    }
                    break;

                case 'reset':
                    if (!empty($g->form_reset)) {
                        $u = $g->form_reset;
                    } elseif ((empty($g->form_reset)) && (isset($reset_form) && !empty($reset_form))) {
                        $u = $reset_form;
                    } else {
                        $u = basename($_SERVER['PHP_SELF']);
                    }
                    break;

                case 'update':
                    if (!empty($g->form_update)) {
                        $u = $g->form_update;
                    } elseif ((empty($g->form_update)) && (isset($update_form) && !empty($update_form))) {
                        $u = $update_form;
                    } else {
                        $u = basename($_SERVER['PHP_SELF']);
                    }
                    break;
            }

            return $u;
        }

        public function frontEndLink($p, $i)
        {
            global $seahorses, $my_updates, $qwebs;

            $myw = strpos($qwebs, '/') !== false ? $qwebs : $qwebs . '/';
            $myw2 = strpos($my_updates, 'index.php') !== false ? $myw : $my_updates;

            if ($p == 'comments') {
                if ($seahorses->getOption('updates_prettyurls') == 'y') {
                    $link = "\n" . '<p class="backLink">&#8212; <a href="' . $myw .
                        'e/' . $i . '/">Back to Entry</a>' . "</p>\n";
                } else {
                    $link = "\n" . '<p class="backLink">&#8212; <a href="' . $myw2 .
                        '?e=' . $i . '">Back to Entry</a>' . "</p>\n";
                }
            }

            return $link;
        }

        /**
         * (Essentially!) from <http://ma.tt>.
         *
         * @access   public
         * @function $octopus->lineBreak()
         * @since    2.1.4
         */
        public function lineBreak($c)
        {
            $c .= "\n";
            $c = str_replace(array("\r\n", "\r"), "\n", $c);
            $c = preg_replace('|(?<!<br>)\s*\n\n|', "<br><br>\n", $c);
            return $c;
        }

        public function getLineBreakers($p)
        {
            $e = $p . "\n";
            $e = preg_replace("/\n\n+/", "\n\n", $e);
            $e = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $e);
            $e = preg_replace('|<p>\s*?</p>|', '', $e);
            return $e;
        }

        /**
         * One of my favourite functions, and one of the oldest to
         * date *___* Basically does what the tin says: shortens URLs.
         *
         * @access   public
         * @function $octopus->shortURL()
         * @since    1.0
         */
        public function shortURL($u)
        {
            $u = str_replace('http://', '', $u);
            $u = str_replace('https://', '', $u);
            $u = str_replace('www.', '', $u);
            $e = explode('/', $u);
            $n = str_replace('/', '', $e[0]);
            return $n;
        }

        /**
         * Formats members' pages in the user's specified HTML preference (HTML5
         * or X/HTML)
         *
         * @access   public
         * @function $octopus->alternate()
         * @since    2.3beta
         */
        public function alternate($h, $c = 'html', $e = 0, $d = '')
        {
            $k = $d != '' && preg_match('/([A-Za-z0-9-_]+)/i', $d) ? " id=\"$d\"" : '';
            $s = '';
            if ($c == 'html' || $c == 'xhtml') {
                if ($h == 'menu') {
                    $s = $e == 0 ? "<ol$k>\n" : "</ol>\n";
                } elseif ($h == 'table') {
                    $s = $e == 0 ? "<table$k width=\"100%\">\n" : "</table>\n";
                }
            } /**
             * HTML5 markup
             */
            else if ($h == 'menu') {
                $s = $e == 0 ? "<menu$k>\n" : "</menu>\n";
            } elseif ($h == 'table') {
                $s = $e == 0 ? "<table$k>\n" : "</table>\n";
            }
            return $s;
        }

        public function ampersand($q, $a = 0)
        {
            global $seahorses;

            $m = $seahorses->getOption('markup') == 'html5' ? '&#38;' : '&amp;';
            if ($seahorses->getOption('markup') == 'html5') {
                $p = str_replace(array('&', '&amp;'), '&#38;', $q);
            } else {
                $p = str_replace('&', '&amp;', $q);
            }
            return ($a == 1 ? $p . $m : $p);
        }

        public function entities($s)
        {
            $p = $s;
            $p = str_replace('&amp;', '&#38;', $p);
            $p = str_replace('raquo;', '#187;', $p);
            $p = str_replace('laquo;', '#171;', $p);
        }

        public function javascriptEmail($e)
        {
            $s = "<script type=\"text/javascript\">\n<!--\n" .
                " var jsEmail = '$e';\n" .
                " document.write('<a h' + 'ref=\"mailto:' + jsEmail + '\">E-mail</' + 'a>');\n" .
                "//-->\n</script>\n";
            return $s;
        }

        public function javascriptCheat($p)
        {
            global $options, $seahorses;

            if ($seahorses->getOption('javascript_opt') == 'n') {
                return;
            }

            $s = "<script type=\"text/javascript\">\n<!--\n" .
                " var jsString = '$p';\n document.write('<input name=\"" . $this->cheatJavascript .
                "\" type=\"hidden\" value=\"' + jsString + '\"" . $options->markup .
                ">');\n//-->\n</script>\n";
            return $s;
        }

        /**
         * @access   public
         * @function $octopus->javascriptFunc()
         * @since    2.3beta
         */
        public function javascriptFunc()
        {
            $s = "<script type=\"text/javascript\">\n function submitForm(f) {\n" .
                "  if(document.f.onsubmit()) {\n   document.f.submit();\n  }\n }\n</script>\n";
            return $s;
        }

        /**
         * @access   public
         * @function $octopus->returnURL()
         * @since    2.4
         */
        public function returnURL($l, $text = 'via form')
        {
            global $wolves;

            $g = $wolves->getListings($l, 'object');
            $f = $g->url;
            $s = "<a href=\"$f\">$text</a>";
            return $s;
        }

        public function captchaCheat($p, $b = 'join')
        {
            global $mark, $seahorses;

            if ($b == 'comments') {
                if ($seahorses->getOption('updates_captcha') == 'n') {
                    return;
                }
            } elseif ($b == 'contact') {
                if ($seahorses->getOption('contact_captcha') == 'n') {
                    return;
                }
            } elseif ($b == 'join') {
                if ($seahorses->getOption('captcha_opt') == 'n') {
                    return;
                }
            }

            $string = '<input name="' . $this->cheatCaptcha . '" type="hidden"' .
                ' value="' . $p . '"' . $mark . ">\n";
            return $string;
        }

        public function antispamCheat($p, $r, $b = 'join')
        {
            global $mark, $seahorses;

            if ($b == 'comments') {
                if ($seahorses->getOption('updates_antispam') == 'n') {
                    return;
                }
            } elseif ($b == 'contact') {
                if ($seahorses->getOption('contact_antispam') == 'n') {
                    return;
                }
            } elseif ($b == 'join') {
                if ($seahorses->getOption('antispam_opt') == 'n') {
                    return;
                }
            }

            $string = '<input name="' . $this->cheatAnti . '" type="hidden"' .
                ' value="' . $p . '"' . $mark . ">\n";
            $string .= '<input name="' . $this->cheatSpam . '" type="hidden"' .
                ' value="' . $r . '"' . $mark . ">\n";
            return $string;
        }

        public function formatCredit()
        {
            global $laoptions, $seahorses;

            $symbol = $seahorses->getOption('format_links');
            $point = !empty($symbol) ? ' ' . $symbol : '';
            $link = '<a href="' . $laoptions->versionURI .
                '" title="External Link: ' . $this->shortURL($laoptions->versionURI) .
                "\" target=\"_blank\"''>Tess $point</a>";
            return $this->newLinkFormatCredit() . ' (originally by ' . $link . ')';
        }

        public function newLinkFormatCredit(): string
        {
            global $laoptions;
            $link = '<a href="https://scripts.robotess.net" title="PHP Scripts: Enthusiast, Siteskin, Codesort, Listing Admin, FanUpdate - ported to PHP 7 ' .
                "\" target=\"_blank\"''>" . $laoptions->version . '</a>';
            return $link;
        }

        # End function list here!
    }
}

$octopus = new octopus();
