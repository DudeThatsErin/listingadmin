<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <fun-db.inc.php>
 * @version          Robotess Fork
 */

if (!class_exists('scorpions')) {
    abstract class DBConnection
    {
        protected $dbConnect;

        private string $errorMsgInit = '<p class="errorButton"><span class="error">ERROR:</span> You cannot' .
        ' currently connect to MySQL. Make sure all variables are correct in' .
        ' <samp>rats.inc.php</samp>; if it is a random error, wait it out and see' .
        ' if it\'ll magically disappear.</p>';

        /**
         * @param $hostname
         * @param $username
         * @param $password
         * @param $database
         */
        public function __construct($hostname, $username, $password, $database)
        {
            $this->dbConnect = $this->initDbConnect($hostname, $username, $password, $database);
            if($this->dbConnect === null || $this->dbConnect === false) {
                die($this->errorMsgInit);
            }
        }

        abstract protected function initDbConnect($hostname, $username, $password, $database);

        abstract public function error(): string;
        abstract public function errno(): string;
        abstract public function close(): void;
        abstract public function real_escape_string($e): string;
        abstract public function query($q);
        abstract public function num_rows($result);
        abstract public function fetch_object($result);
        abstract public function fetch_array($result);
    }

    class MySQLiConnection extends DBConnection
    {
        const CODE_TABLE_NOT_FOUND = 1146;

        /** @var mysqli */
        protected $dbConnect;

        /**
         * @param $hostname
         * @param $username
         * @param $password
         * @param $database
         * @return mysqli
         */
        protected function initDbConnect($hostname, $username, $password, $database): mysqli
        {
            return new mysqli($hostname, $username, $password, $database);
        }

        public function error(): string
        {
            return $this->dbConnect->error;
        }

        public function errno(): string
        {
            return $this->dbConnect->errno;
        }

        public function close(): void
        {
            $this->dbConnect->close();
            unset($this->dbConnect);
        }

        /**
         * @param $e
         * @return string
         */
        public function real_escape_string($e): string
        {
            return $this->dbConnect->real_escape_string($e);
        }

        /**
         * @param $q
         * @return bool|mysqli_result
         */
        public function query($q)
        {
            $r = $this->dbConnect->query($q);
            if($r === false && (int)$this->errno() === self::CODE_TABLE_NOT_FOUND) {
                throw new \RuntimeException($this->error());
            }

            return $r;
        }

        /**
         * @param mysqli_result $result
         * @return mixed
         */
        public function num_rows($result)
        {
            return $result->num_rows;
        }

        /**
         * @param mysqli_result $result
         * @return mixed
         */
        public function fetch_object($result)
        {
            return $result->fetch_object();
        }

        /**
         * @param mysqli_result $result
         * @return mixed
         */
        public function fetch_array($result)
        {
            return $result->fetch_array();
        }
    }

    class PDOMySQLConnection extends DBConnection
    {
        /** @var PDO */
        protected $dbConnect;

        /**
         * @param $hostname
         * @param $username
         * @param $password
         * @param $database
         * @return PDO|null
         */
        protected function initDbConnect($hostname, $username, $password, $database): ?PDO
        {
            try {
                $db_link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
                $db_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $db_link;
            } catch (PDOException $e) {
                return null;
            }
        }

        public function error(): string
        {
            $errorInfo = $this->dbConnect->errorInfo();
            return $errorInfo[2] ?? $this->dbConnect->errorCode();
        }

        public function errno(): string
        {
            return $this->dbConnect->errorCode();
        }

        public function close(): void
        {
            $this->dbConnect = null;
            unset($this->dbConnect);
        }

        public function real_escape_string($e): string
        {
            return trim($this->dbConnect->quote($e), '\'');
        }

        /**
         * @param $q
         * @return false|PDOStatement
         */
        public function query($q)
        {
            return $this->dbConnect->query($q);
        }

        /**
         * @param PDOStatement $result
         * @return mixed
         */
        public function num_rows($result)
        {
            return $result->rowCount();
        }

        /**
         * @param PDOStatement $result
         * @return mixed
         */
        public function fetch_object($result)
        {
            return $result->fetchObject();
        }

        /**
         * @param PDOStatement $result
         * @return mixed
         */
        public function fetch_array($result)
        {
            return $result->fetchAll();
        }
    }

    class scorpions
    {
        /** @var DBConnection */
        public $database;

        /**
         * Our initialising function, which connects us to the
         * database through the user's defined method
         *
         * @param $h
         * @param $u
         * @param $p
         * @param $n
         */
        public function __construct($h, $u, $p, $n)
        {
            $this->initDB($h, $u, $p, $n);
        }

        /**
         * @param $h
         * @param $u
         * @param $p
         * @param $n
         * @deprecated
         *
         * Legacy method
         *
         */
        public function scorpions($h, $u, $p, $n): void
        {
            $this->initDB($h, $u, $p, $n);
        }

        /**
         * @param $h
         * @param $u
         * @param $p
         * @param $n
         */
        public function initDB($h, $u, $p, $n): void
        {
            global $laoptions;

            switch ($laoptions->dbEngine) {
                case 'mysqli':
                    $dbConnectionClass = MySQLiConnection::class;
                    break;
                case 'pdo_mysql':
                    $dbConnectionClass = PDOMySQLConnection::class;
                    break;
                default:
                    die('Unsupported SQL extension. Please set $_ST[\'dbengine\'] in rats.inc.php either with mysqli or with pdo_mysql');
            }

            $this->database = new $dbConnectionClass($h, $u, $p, $n) or die(
                '<p class="errorButton"><span class="error">ERROR:</span> You cannot' .
                ' currently connect to MySQL. Make sure all variables are correct in' .
                ' <samp>rats.inc.php</samp>; if it is a random error, wait it out and see' .
                ' if it\'ll magically disappear.</p>'
            );
        }

        /**
         * @function  $scorpions->error()
         */
        public function error(): string
        {
            return $this->database->error();
        }

        public function errno(): string
        {
            return $this->database->errno();
        }

        /**
         * @function  $scorpions->breach()
         * @param     $y , boolean; 0 if closing the connection, 1 if opening
         * the connection natively
         * @notes     Originally located in <fun.inc.php>
         * @version   2.3beta
         */
        public function breach($y = 1): void
        {
            global $database_host, $database_name, $database_pass,
                   $database_user;

            switch ($y) {
                case 0:
                    $this->database->close();
                    unset($this->database);
                    break;

                case 1:
                    $this->initDB($database_host, $database_user, $database_pass,
                        $database_name);
                    break;
            }
        }

        /**
         * @function  $scorpions->escape()
         * @param     $p , text
         * @desc      Escape text via real_escape_string() and -
         * optionally - magic quotes gpc D:
         * @return string
         */
        public function escape($p): string
        {
            $e = trim($p);
            $e = $this->database->real_escape_string($e);

            return $e;
        }

        /**
         * @function  $scorpions->counts()
         *
         * @param     $q , text; query string
         * @param     $e , boolean
         * @param     $m , text; predefined error message; optional
         *
         * @return object
         */
        public function counts($q, $e = 1, $m = '')
        {
            $r = (object)[
                'message' => '',
                'rows' => 0,
                'status' => false
            ];

            $select = $q;
            $true = $this->database->query($select);

            /**
             * Are we returning strictly the boolean return value, or values
             * and messages?
             */
            if ($e === 1) {
                $r->status = $true !== false;
            } else if ($true === false) {
                $r->status = false;
                $r->message = $m;
            } else {
                $r->status = true;
            }

            /*
             *  Fetch our number of rows, depending on the database method~
             */
            $r->rows = $this->database->num_rows($true);

            return $r;
        }

        /**
         * Instead of repeating the OOP and procedural coding 34859959
         * times over, we've got a handy function \o/
         *
         * @function  $scorpions->fetch()
         * @param     $q , string; the query string
         * @param string $m , string; a specified value to pull; optional
         * @param string $e
         * @return bool|mysqli_result|object|stdClass|string
         */
        public function fetch($q, $m = '', $e = '')
        {
            global $tigers;

            $select = $q;

            /**
             * Are there actually an results to pull from? Find this out first
             * and send us back if we don't~
             */
            $c = $this->counts($select, 1);
            if ($c->rows === 0) {
                return '';
            }

            $true = $this->database->query($select);

            if ($true === false) {
                $o = $e !== '' ? $e : 'There was an error in selecting the specified data' .
                    ' from the database.';
                $tigers->displayError('Database Error', $o, false);
            }

            /*
             *  Fetch our data, whether we're pulling full rows, or just one value~ :D
             */
            $getItem = $this->database->fetch_object($true);

            if ($m === '*') {
                return $m !== '' ? ($getItem) : $true;
            }

            return $m !== '' ? ($getItem->$m) : $true;
        }

        /**
         * @function  $scorpions->insert()
         *
         * @param     $q , text; query string
         * @param     $e , boolean; 0 for returing status, 1 for returning status
         * on success, and error on failure
         * @return bool
         * @return bool
         */
        public function insert($q, $e = 0): bool
        {
            global $tigers;

            $r = false;

            $insert = $q;
            $this->database->query("SET NAMES 'utf8';");
            $true = $this->database->query($insert);


            /**
             * Are we returning strictly the boolean return value, or values
             * and messages?
             */
            if ($e === 0) {
                $r = $true !== false;
            } else if ($true === false) {
                $tigers->displayError('Database Error', 'The script was unable to insert' .
                    'the data.', false);
            } else {
                $r = true;
            }

            return $r;
        }

        /**
         * @function  $scorpions->obj()
         * @param     $q , string; the query string
         * @param     $m , boolean; 0 returns the object, 1 returns the array
         * @return mixed
         * @return mixed
         */
        public function obj($q, $m = 0)
        {
            if ($m === 0) {
                $getItem = $this->database->fetch_object($q);
            } else {
                $getItem = $this->database->fetch_array($q);
            }

            return $getItem;
        }

        /**
         * @function  $scorpions->query()
         * @param     $q , string; the query string
         * @return bool|mysqli_result
         * @return bool|mysqli_result
         */
        public function query($q)
        {
            if ($q === '') {
                return false;
            }

            $select = $q;
            return $this->database->query($select);
        }

        /**
         * $this->counts() returns the rows, status and an error message
         * if one is given; $this->total() returns the rows only
         *
         * @function  $scorpions->total()
         * @param     $q , string; resource
         * @return mixed
         * @return mixed
         */
        public function total($q)
        {
            return $this->database->num_rows($q);
        }

        /**
         * @function  $scorpions->update()
         *
         * @param     $query , string; query
         * @param     $returnError , boolean; 1 for displaying the error natively or 0 for
         * handling the error outside of the method
         * @param     $successText , text; the success/error text to display; optional
         * @return bool|null
         * @return bool|null
         */
        public function update($query, $returnError = 1, $successText = ''): ?bool
        {
            global $tigers;

            $update = $query;
            $this->database->query("SET NAMES 'utf8';");
            $true = $this->database->query($update);

            if ($returnError === 1) {
                return $true !== false;
            }

            $s = explode('__', $successText);
            $s = $tigers->emptyarray($s);
            if ($true === false) {
                $tigers->displayError('Database Error', $s[0], false);
                return false;
            }

            echo '<p class="successButton"><span class="success">Success!</span>' .
                $s[1] . "</p>\n";
            return true;
        }
    }
}
