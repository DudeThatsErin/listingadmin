<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <class-crosspost.inc.php>
 * @version          Robotess Fork
 */

/**
 * Copyright (c) 2008 Alis Dee
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

if (!class_exists('crosspost')) {
    class crosspost
    {
        public $clientid;
        public $protocol_version;
        public $strict_utf8;
        public $lineendings;
        public $rpc_timeout;
        public $journal_url;

        public $lj_srvr;
        public $lj_port;
        public $lj_xmlrpcuri;
        public $lj_userid;
        public $lj_md5pwd;
        public $lj_comm;
        public $lj_challenge;
        public $lj_logged;

        /** @var IXR_Client */
        public $client;

        /**
         * @var bool
         */
        public $loggedin = false;

        # -- Get Client Function -----------------------------------------------------
        public function __construct($lj_userid = '', $lj_md5pwd = '', $server = '', $lj_comm = '')
        {
            global $laoptions;

            $this->clientid = 'PHP-Listing Admin/' . $laoptions->version;
            $this->protocol_version = 1;
            $this->strict_utf8 = array();
            $this->lineendings = 'unix';
            $this->rpc_timeout = 60;

            $this->lj_srvr = $server;
            $this->lj_port = '80';
            $this->lj_xmlrpcuri = '/interface/xmlrpc';

            $this->lj_logged = false;
            $this->lj_userid = $lj_userid;
            $this->lj_comm = $lj_comm;
            $this->lj_md5pwd = $lj_md5pwd;

            $this->client = new IXR_Client($this->lj_srvr, $this->lj_xmlrpcuri, $this->lj_port);
            $this->client->debug = false;
        }

        # -- Login -------------------------------------------------------------------
        public function login(): ?array
        {
            if (!$this->client->query('LJ.XMLRPC.getchallenge')) {
                return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
            }

            $response = $this->client->getResponse();
            $challenge = $response['challenge'];

            $lj_method = 'LJ.XMLRPC.login';
            $params = array(
                'username' => $this->lj_userid,
                'auth_method' => 'challenge',
                'auth_challenge' => $challenge,
                'auth_response' => md5($challenge . $this->lj_md5pwd),
                'ver' => $this->protocol_version,
                'clientversion' => $this->clientid,
                'getpickws' => 1,
            );

            $response = $this->do_the_thing($lj_method, $params);
            if ($response) {
                $this->loggedin = true;
                return array(TRUE, $this->client->getResponse(), 0);
            }

            return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
        }

        # -- Post New Post -----------------------------------------------------------
        public function postevent($d, $m): ?array
        {
            $this->lj_challenge = $this->get_challenge();

            $lj_method = 'LJ.XMLRPC.postevent';
            $params = array(
                'username' => $this->lj_userid,
                'auth_method' => 'challenge',
                'auth_challenge' => $this->lj_challenge,
                'auth_response' => md5($this->lj_challenge . $this->lj_md5pwd),
                'ver' => $this->protocol_version,
                'lineendings' => $this->lineendings,

                'subject' => $d['subject'],
                'event' => $d['event'],
                'year' => $d['year'],
                'mon' => $d['month'],
                'day' => $d['day'],
                'hour' => $d['hour'],
                'min' => $d['min'],
                'security' => $d['security'],
                'props' => $m
            );

            if (!empty($this->lj_comm)) {
                $params['usejournal'] = $this->lj_comm;
            }

            $response = $this->do_the_thing($lj_method, $params);
            if ($response) {
                return array(TRUE, $this->client->getResponse(), 0);
            }

            return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
        }

        # -- Edit Post ---------------------------------------------------------------
        public function editevent($d, $m): ?array
        {
            $this->lj_challenge = $this->get_challenge();

            $lj_method = 'LJ.XMLRPC.editevent';
            $params = array(
                'username' => $this->lj_userid,
                'auth_method' => 'challenge',
                'auth_challenge' => $this->lj_challenge,
                'auth_response' => md5($this->lj_challenge . $this->lj_md5pwd),

                'ver' => $this->protocol_version,
                'lineendings' => $this->lineendings,

                'itemid' => $d['itemid'],
                'subject' => $d['subject'],
                'event' => $d['event'],
                'year' => $d['year'],
                'mon' => $d['month'],
                'day' => $d['day'],
                'hour' => $d['hour'],
                'min' => $d['min'],
                'security' => $d['security'],
                'props' => $m
            );

            // are we trying to cross post this to a community?
            if (!empty($this->lj_comm)) {
                $params['usejournal'] = $this->lj_comm;
            }

            $response = $this->do_the_thing($lj_method, $params);
            if ($response) {
                return array(TRUE, $this->client->getResponse(), 0);
            }

            return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
        }

        # -- Delete Post -------------------------------------------------------------
        public function deleteevent($itemid): ?array
        {
            $this->lj_challenge = $this->get_challenge();

            $lj_method = 'LJ.XMLRPC.editevent';
            $params = array(
                'username' => $this->lj_userid,
                'auth_method' => 'challenge',
                'auth_challenge' => $this->lj_challenge,
                'auth_response' => md5($this->lj_challenge . $this->lj_md5pwd),

                'ver' => $this->protocol_version,
                'lineendings' => $this->lineendings,

                'itemid' => $itemid,
                'subject' => 'Deleted Post',
                'event' => ''
            );

            // are we trying to cross post this to a community?
            if (!empty($this->lj_comm)) {
                $params['usejournal'] = $this->lj_comm;
            }

            $response = $this->do_the_thing($lj_method, $params);
            if ($response) {
                return array(TRUE, $this->client->getResponse(), 0);
            }

            return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
        }

        # -- Get Data ----------------------------------------------------------------
        public function getevents($itemid = ''): ?array
        {
            $this->lj_challenge = $this->get_challenge();

            $type = $itemid ? 'one' : 'lastn';

            $lj_method = 'LJ.XMLRPC.getevents';
            $params = array(
                'username' => $this->lj_userid,
                'auth_method' => 'challenge',
                'auth_challenge' => $this->lj_challenge,
                'auth_response' => md5($this->lj_challenge . $this->lj_md5pwd),

                'ver' => $this->protocol_version,
                'lineendings' => $this->lineendings,
                'howmany' => 1,

                'selecttype' => $type
            );

            if (!empty($this->lj_comm)) {
                $params['usejournal'] = $this->lj_comm;
            }

            $response = $this->do_the_thing($lj_method, $params);
            if ($response) {
                return array(TRUE, $this->client->getResponse(), 0);
            }

            return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
        }

        # -- Internal Functions ------------------------------------------------------
        public function get_challenge(): array
        {
            if (!$this->client->query('LJ.XMLRPC.getchallenge')) {
                return array(FALSE, $this->client->getErrorMessage(), $this->client->getErrorCode());
            }

            $response = $this->client->getResponse();
            return $response['challenge'];
        }

        public function do_the_thing($method, $params): bool
        {
            if ($this->isStrictUTF8()) {
                $this->encodeRecurse($params);
            }

            return $this->client->query($method, $params);
        }

        public function isStrictUTF8(): bool
        {
            foreach ($this->strict_utf8 as $s) {
                if (stripos($this->lj_srvr, $s) !== false) {
                    return true;
                }
            }
            return false;
        }

        public function encodeRecurse(&$a): void
        {
            foreach ($a as $k => $v) {
                if (is_array($v)) {
                    $this->encodeRecurse($a[$k]);
                } else {
                    $a[$k] = $this->fixEncoding($v);
                }
            }
        }

        public function fixEncoding($in_str): string
        {
            return utf8_encode($in_str);
        }
    }
}
