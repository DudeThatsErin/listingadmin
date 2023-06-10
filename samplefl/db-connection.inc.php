<?php
/**
 * @project          Listing Admin
 * @license          GPL Version 3; BSD Modified
 * @author           Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <db-connection.inc.php>
 * @version          Robotess Fork
 */

# -- DON'T MODIFY THIS FILE! --------------------------------
# -----------------------------------------------------------
#
#  Below this line are sensitive lines that should NOT be
#  messed with unless you know exactly what you're doing.
#
# -----------------------------------------------------------
# -----------------------------------------------------------

if (basename($_SERVER['PHP_SELF']) === 'db-connection.inc.php') {
    die('<p>ERROR: <em>Sorry, you cannot access this file directly!</p>');
}

if (!function_exists('getLAPath')) {
    function getLAPath($settings, $dbHost, $dbUser, $dbPass, $dbName)
    {
        $errorMsgNoConnect = '<p><span class="error">Error:</span> You cannot currently connect to MySQL.' .
            ' Make sure all variables are correct in <samp>rats.inc.php</samp>; if it is a random' .
            ' error, wait it out and see if it\'ll magically disappear.</p>';

        $errorMsgNoOptionsTable = '<p class="errorButton"><span class="error">ERROR:</span> Unable to select the specified option.' .
            ' Make sure your options table exists.</p>';

        /**
         * Get MySQLi/PDO_MySQL link!
         */
        if (extension_loaded('mysqli')) {
            $connect = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName) or die($errorMsgNoConnect);
        } else if (extension_loaded('pdo_mysql')) {
            try {
                $connect = new PDO('mysql:host=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8', $dbUser, $dbPass);
                $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die($errorMsgNoConnect . $e->getMessage());
            }
        } else {
            exit('<p class="errorButton"><span class="error">ERROR:</span> Please install either MySQLi or PDO_MySQL extension for PHP.</p>');
        }

        /**
         * Aaaaaa-aaa-and run query~!
         */
        $select = "SELECT `text` FROM `$settings[options]` WHERE `name` = 'adm_path' LIMIT 1";
        $optionsSqlResult = $connect->query($select);
        if ($optionsSqlResult === false) {
            exit($errorMsgNoOptionsTable);
        }
        $getItem = wrapFetchArray($optionsSqlResult);
        return $getItem['text'];
    }

    function wrapFetchArray($result)
    {
        if ($result instanceof PDOStatement) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        if ($result instanceof mysqli_result) {
            return $result->fetch_array();
        }
        die('Unsupported SQL extension');
    }
}

if (!defined('STPATH')) {
    define('STPATH', getLAPath($_ST, $database_host, $database_user, $database_pass, $database_name));
}
