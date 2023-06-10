<div id="show-kim-members">
    <?php
    /**
     * @project          Listing Admin
     * @copyright        2007
     * @license          GPL Version 3; BSD Modified
     * @author           Tess <theirrenegadexxx@gmail.com>
     * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
     * @file             <show-kim-members.php>
     * @version          Robotess Fork
     */

    require('b.inc.php');
    require(MAINDIR . 'rats.inc.php');
    require_once('class-kimadmin.inc.php');
    require_once('fun.inc.php');
    require_once('fun-external.inc.php');
    require_once('fun-listings.inc.php');
    require_once('fun-members.inc.php');
    require_once('fun-misc.inc.php');
    require(MAINDIR . 'vars.inc.php');

    /**
     * Get Variables, yo'
     */
    $options = (object)array();

    if (!isset($_GET['page']) || empty($_GET['page']) || !is_numeric($_GET['page'])) {
        $options->page = 1;
    } else {
        $options->page = $tigers->cleanMys((int)$_GET['page']);
    }
    $options->start = $scorpions->escape((($options->page * $per_page) - $per_page));
    $options->prettyURL = false;

    $query = $_SERVER['QUERY_STRING'];
    if (isset($query) && !empty($query)) {
        if (isset($_GET['page'])) {
            $_URL = '?';
            $query = str_replace($_GET['page'], '', $query);
            $query = str_replace('&amp;page=', '', $query);
            $query = str_replace('&page=', '', $query);
            $query = str_replace('page=', '', $query);
            $query = str_replace('&', '&amp;', $query);
            $_URL .= $query;
        } else {
            $_URL = '?' . str_replace('&', '&amp;', $query);
        }
        $options->url = $octopus->ampersand($_URL, 1);
    } else {
        $options->url = '?';
    }

    $options->mark = $seahorses->getOption('markup') == 'xhtml' ? ' /' : '';
    $symb = $seahorses->getOption('markup') == 'html5' ? '&#187;' : '&raquo;';

    /**
     * Count members and go from there \o/
     */
    if ((is_countable($kimadmin->membersList(0)) ? count($kimadmin->membersList(0)) : 0) > 0) {
        if (isset($_GET['sort']) && in_array($_GET['sort'], $wolves->listingsList())) {
            $sortid = $tigers->cleanMys($_GET['sort']);

            /**
             * Get members and pagination!
             */
            $kimadmin->membersSort($sortid);
            $kimadmin->membersPagination($sortid);
        } else {
            $kimadmin->kimDefault();
        }
    } else {
        echo "<p class=\"tc\">Currently no members!</p>\n";
    }
    ?>
    <p class="showCredits-LA-RF" style="text-align: center;">
        Powered by <?php echo $octopus->formatCredit(); ?>
    </p>
</div>
