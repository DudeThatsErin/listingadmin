<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <vars.inc.inc.php> 
 * @since      September 2nd, 2010 
 * @version    1.0   
 */ 

/** 
 * Grab user info automatically~ 
 */ 
$auserinfo = "|{$_SERVER['REMOTE_ADDR']}|{$_SERVER['HTTP_USER_AGENT']}|";
if(!empty($_SERVER['HTTP_REFERER'])) {
 $auserinfo .= "{$_SERVER['HTTP_REFERER']}|";
}
$automated = $tigers->cleanMys($auserinfo);

try {
    /**
     * Pre-defined link for e-mail (most often used in forms)
     */
    $my_email     = $seahorses->getOption('my_email');
    $hide_address = "<script type=\"text/javascript\">\n<!--\n" .
        " var jsEmail = '$my_email';\n" .
        " document.write('<a h' + 'ref=\"mailto:' + jsEmail + '\">via email</' + " .
        "'a>');\n//-->\n</script>";

    /**
     * Grab owner variables \o/
     */
    $qname = $seahorses->getOption('collective_name');
    $qowns = $seahorses->getOption('my_name');
    $qwebs = $seahorses->getOption('my_website');

    /**
     * Grab paths!
     */
    $my_webpath  = $seahorses->getOption('adm_path');
    $my_website  = $seahorses->getOption('adm_http');
    $my_imagesh  = $seahorses->getOption('img_http');
    $my_wishess  = $seahorses->getOption('wsh_http');
    $jnd_http    = $seahorses->getOption('jnd_http');
    $my_updates  = $seahorses->getOption('updates_url');

    /**
     * Get admin object, without the include folder attached, of course~
     */
    $myadminpath = (object) [
        'http' => str_replace('inc/', '', $seahorses->getOption('adm_http')),
        'path' => str_replace('inc/', '', $seahorses->getOption('adm_path'))
    ];

    $checkCr = "<script type=\"text/javascript\">setTimeout(function () {const coll = document.getElementsByClassName(\"showCredits-LA-RF\");if(coll !== undefined && coll.length >= 1) {const el = coll[0]; if(window.getComputedStyle(el).display === 'none') { el.style.display = 'block'; }if(window.getComputedStyle(el).visibility === 'hidden') { el.style.visibility = 'visible'; }}}, 1000);</script>";

    /**
     * Pending comments functioning~
     */
    $pending_comment_form = $seahorses->getOption('updates_comments_moderation') == 'y' ? '1' : '0';
    if($pending_comment_form == 1) {
        $if_pending = '<p class="noteButton">As the owner has set comments to <em>pending</em>, your comment' .
            ' is currently being held for moderation. If your comment does not appear in a week,' .
            " feel free to submit another one.</p>\n";
    } else {
        $if_pending = '<p class="noteButton">Your comment should appear once you hit' .
            ' "Back to Entry" below. Please do NOT hit "Back" in your browser, as this' .
            " will cause your comment to resubmit.</p>\n";
    }

    /**
     * Pagination variables
     */
    $per_joined  = $seahorses->getOption('per_joined');
    $per_members = $seahorses->getOption('per_members');
    $per_page    = $seahorses->getOption('per_page');
    $hide_address_check = $hide_address . $checkCr;

} catch (Exception $e) {
    if(($getTitle ?? 'none') === 'Install') {
        return;
    }

    ?>
    <section><span class="mysql">Notice:</span> there was an error while trying to retrieve Listing Admin options. Please make sure you have installed the script.
        <?php
        if(isset($scorpions)) {
            ?>Error message/code: <?= $scorpions->error(); ?>. <?php
    } else {
            echo 'Check your php logs.';
        }
        ?> The script stops executing.
    </section>
<?php
    die;
}

/**
 * Grab bad SPAM bots for forms
 */
$badheaders = '/(Indy|Blaiz|Java|libwww-perl|Python|OutfoxBot|User' .
    '-Agent|PycURL|AlphaServer|T8Abot|Syntryx|WinHttp|WebBandit|nicebot|Jakar' .
    'ta|curl|Snoopy|PHPcrawl|id-search|WebAlta Crawler|Baiduspider+|Gaisbot|K' .
    'aloogaBot|Gigabot|Gaisbot|ia_archiver)/i';

/** 
 * Month (date) array 
 */ 
$get_date_array = array(
 '01' => 'January',
 '02' => 'February',
 '03' => 'March',
 '04' => 'April',
 '05' => 'May',
 '06' => 'June',
 '07' => 'July',
 '08' => 'August',
 '09' => 'September',
 '10' => 'October',
 '11' => 'November',
 '12' => 'December'
);

/** 
 * Get Regular 'ole Status Array 
 */ 
$get_status_array = array(
 '0' => 'Current',
 1 => 'Pending'
);

/** 
 * Get the yes/no array, often seen in the form P: 
 */ 
$get_yn_array = array(
 'y', 'n'
);

/** 
 * Grab markup array for options and listings 
 */ 
$get_markup_array = array(
 'html'  => 'HTML',
 'html5' => 'HTML5',
 'xhtml' => 'XHTML'
);

/** 
 * This goes for both member e-mails and KIM members e-mails :D 
 */  
$get_email_array = array(
 '0' => 'Show',
 1   => 'Hide'
);

$get_previouso_array = array(
 '0' => 'No',
 1   => 'Yes'
);

/** 
 * Grab options.php's arrays! \o/ 
 */ 
$get_option_array = array(
 '2' => 'features', 
 '3' => 'import',
 '4' => 'options', 
 '5' => 'plugins', 
 '6' => 'antispam'
);

$get_option_nav_array = array(
 'details'  => 'Details',
 'features' => 'Features',
 'import'   => 'Import/Export',
 'options'  => 'Options',
 'plugins'  => 'Plugins',
 'antispam' => 'Plugins &#187; Antispam'
);

/** 
 * Set arrays for importing and exporting scripts \o/ 
 */ 
$get_script_array = array(
 'bellabuffs'   => 'BellaBuffs',
 'codesort'     => 'CodeSort',
 'enthusiast'   => 'Enthusiast',
 'fanupdate'    => 'FanUpdate',
 'listingadmin' => 'Listing Admin',
 'phpfanbase'   => 'phpFanBase'
);

$get_import_cats_array = array(
 'affiliates' => 'Affiliates',
 'categories' => 'Categories',
 'codes'      => 'Codes',
 'joined'     => 'Joined',
 'members'    => 'Members',
 'updates'    => 'Updates'
);

$get_export_cats_array = array(
 'affiliates' => 'Affiliates',
 'members'    => 'Members'
);

/** 
 * Error message categories 
 */ 
$get_errors_array = array(
 'all'   => 'All',
 'forms' => 'Form Errors',
 'spam'  => 'SPAM',
 'user'  => 'User Login'
);

/**
 * Get listing status array 
 */
$get_listing_array = array(
 'curent'   => 'Current',
 'upcoming' => 'Upcoming',
 'pending'  => 'Pending'
);

/** 
 * Get status array (returns real IDs!) 
 */ 
$get_fulllist_array = array(
 'current'  => 0,
 'upcoming' => 1,
 'pending'  => 2
);

/** 
 * Member search array :D 
 */ 
$get_type_id_array = array(
 'listingadmin' => array(
  'email' => 'mEmail', 
  'name' => 'mName', 
  'url' => 'mURL'
 ),
 'other' => array(
  'email' => 'email', 
  'name' => 'name', 
  'url' => 'url'
 )
);

/** 
 * Affiliate search array (Enthusiast and Listing Admin only) 
 */ 
$get_affsearch_array = array(
 'enth' => array(
  'email' => 'email', 
  'name' => 'subject', 
  'url' => 'url'
 ), 
 'listingadmin' => array(
  'email' => 'aEmail', 
  'name' => 'aSubject', 
  'url' => 'aURL'
 )
);

/** 
 * Affiliate/contact form reason options 
 */ 
$get_reason_array = array(
 'Affiliation', 
 'Affiliation: Update', 
 'Comments', 
 'Contact', 
 'Questions/Concerns'
);

/** 
 * Get array for wishlist types! 
 */ 
$get_wishlist_array = array(
 'custom'  => 'Custom',
 'granted' => 'Granted',
 'list'    => 'List',
 'top'     => 'Top'
);

/** 
 * Get an array of all addons~ :D 
 */ 
$get_addon_array = array(
 'codes' => 'Codes',
 'kim' => 'KIM List',
 'lyrics' => 'Lyrics',
 'quotes' => 'Quotes',
 'updates' => 'Updates'
);

$notSupportedAddons = ['lyrics', 'quotes'];

/** 
 * Journal server object :D 
 */ 
$journals = (object) array( 
 'dw' => 'www.dreamwidth.org',
 'ij' => 'www.insanejournal.com',
 'lj' => 'www.livejournal.com'
);
