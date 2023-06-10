<?php  
/* 
 /////////////////////////////////////////////////////////////
 Listing Admin (c) 2007 
 ///////////////////////////////////////////////////////////// 
*/ 
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <fun-utility.inc.php> 
 * @since      November 12th, 2011 
 * @version    2.3alpha  
 */ 

if(class_exists('frogs') == false) {
 class frogs {
 
  /** 
   * Install a Listing Admin addon 
   * 
   * @function  $frogs->installAddon() 
   * @param     $c, string; addon slug 
   */ 
  public function installAddon($c) {
   global $_ST, $get_addon_array, $notSupportedAddons, $scorpions;
   $get_addon_array_for_installation = array_diff_key($get_addon_array, array_flip($notSupportedAddons));
 
   /** 
    * Setup our return object ahead of time! 
    */ 
   $return = (object) array(
    'message' => '',
    'query'   => '',
    'status'  => true
   );

   if(array_key_exists($c, $get_addon_array_for_installation)) {
    switch($c) {
     case 'codes':
      $create = "CREATE TABLE `$_ST[codes]` (
 `cID` int(10) NOT NULL AUTO_INCREMENT,
 `fNiq` mediumint(6) UNSIGNED NOT NULL,
 `cName` varchar(255) NOT NULL,
 `cFile` varchar(255) NULL,
 `cCategory` varchar(255) NOT NULL,
 `cSize` tinyint(2) NOT NULL default '1',
 `cDonor` smallint(3) NOT NULL default '0',
 `cPending` tinyint(1) NOT NULL default '0',
 `cAdded` datetime NOT NULL,
 PRIMARY KEY  (`cID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $true = $scorpions->query($create);
      if($true == false) {
       $return->query   = $create;
       $return->message = "The script was unable to create the <samp>$_ST[codes]" .
           '</samp> table.';
       $return->status  = false;
      }

      $create = "CREATE TABLE `$_ST[codes_categories]` (
 `catID` mediumint(6) NOT NULL AUTO_INCREMENT,
 `fNiq` varchar(255) NOT NULL,
 `catName` varchar(255) NOT NULL,
 `catParent` mediumint(6) NOT NULL DEFAULT '0',
 PRIMARY KEY (`catID`),
 UNIQUE KEY `fNiq` (`fNiq`, `catName`(70), `catParent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $true = $scorpions->query($create);
      if($true == false) {
       $return->query   = $create;
       $return->message = 'The script was unable to create the' .
       " <samp>$_ST[codes_categories]</samp> table.";
       $return->status  = false;
      }

      $create = "CREATE TABLE `$_ST[codes_donors]` (
 `dID` mediumint(6) NOT NULL AUTO_INCREMENT,
 `dName` varchar(255) NOT NULL,
 `dEmail` varchar(255) NOT NULL,
 `dURL` varchar(255) NOT NULL,
 `dPending` tinyint(1) NOT NULL DEFAULT '1',
 `dUpdated` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
 `dAdded` datetime NOT NULL,
 PRIMARY KEY (`dID`),
 UNIQUE KEY `dName` (`dName`(25), `dEmail`(80), `dURL`(90))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $true = $scorpions->query($create);
      if($true == false) {
       $return->query   = $create;
       $return->message = 'The script was unable to create the' .
       " <samp>$_ST[codes_donors]</samp> table.";
       $return->status  = false;
      }

      $create = "CREATE TABLE `$_ST[codes_sizes]` (
 `sID` smallint(2) NOT NULL AUTO_INCREMENT,
 `sName` varchar(255) NOT NULL,
 `sOrder` smallint(2) UNSIGNED NOT NULL,
 PRIMARY KEY (`sID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $true = $scorpions->query($create);
      if($true == false) {
       $return->query   = $create;
       $return->message = 'The script was unable to create the <samp>' .
       "$_ST[codes_sizes]</samp> table.";
       $return->status  = false;
      }
     break;

     case 'kim':
      $create = "CREATE TABLE `$_ST[kim]` (
 `mID` int(10) NOT NULL AUTO_INCREMENT,
 `mEmail` varchar(255) NOT NULL,
 `fNiq` mediumint(6) UNSIGNED NOT NULL,
 `mName` varchar(100) NOT NULL,
 `mURL` varchar(255) NOT NULL,
 `mPassword` varchar(255) NOT NULL,
 `mVisible` tinyint(1) NOT NULL DEFAULT '0',
 `mPending` tinyint(1) NOT NULL DEFAULT '0',
 `mPrevious` tinyint(1) NOT NULL DEFAULT '0',
 `mUpdate` enum('y', 'n') NOT NULL DEFAULT 'n',
 `mEdit` datetime NOT NULL DEFAULT '1970-01-01',
 `mAdd` date NOT NULL,
PRIMARY KEY (`mID`),
UNIQUE KEY `mName` (`fNiq`, `mEmail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
       $true = $scorpions->query($create);
       if($true == false) {
        $return->query   = $create;
        $return->message = "The script was unable to create the <samp>$_ST[kim]" .
            '</samp> table.';
        $return->status  = false;
       }
      break;

      case 'lyrics':
       $create = "CREATE TABLE `$_ST[lyrics]` (
 `lyID` int(10) NOT NULL AUTO_INCREMENT,
 `fNiq` mediumint(6) UNSIGNED NOT NULL,
 `aNiq` int(10) unsigned NOT NULL,
 `lyName` varchar(255) NOT NULL,
 `lyText` longtext NOT NULL,
 PRIMARY KEY (`lyID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
       $true = $scorpions->query($create);
       if($true == false) {
        $return->query   = $create;
        $return->message = 'The script was unable to create the <samp>' .
        "$_ST[lyrics]</samp> table.";
        $return->status  = false;
       }

       $create = "CREATE TABLE `$_ST[lyrics_albums]` (
 `aID` int(10) NOT NULL AUTO_INCREMENT,
 `fNiq` mediumint(6) UNSIGNED NOT NULL,
 `aArtist` varchar(255) NOT NULL,
 `aName` varchar(255) NOT NULL,
 `aYear` mediumint(4) NOT NULL,
 PRIMARY KEY (`aID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
      $true = $scorpions->query($create);
      if($true == false) {
       $return->query   = $create;
       $return->message = 'The script was unable to create the <samp>' .
       "$_ST[lyrics_albums]</samp> table.";
       $return->status  = false;
      }
     break;

     case 'quotes':
      $create = "CREATE TABLE `$_ST[quotes]` (
 `qID` mediumint(5) NOT NULL AUTO_INCREMENT,
 `fNiq` mediumint(6) unsigned NOT NULL,
 `qQuote` text NOT NULL,
 `qAuthor` varchar(255) NOT NULL,
 `qUpdated` datetime NOT NULL DEFAULT '1970-01-01 00:00:00', 
 `qAdded` datetime NOT NULL,
 PRIMARY KEY (`qID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
       $true = $scorpions->query($create);
       if($true == false) {
        $return->query   = $create;
        $return->message = 'The script was unable to create the <samp>' .
        "$_ST[quotes]</samp> table.";
        $return->status  = false;
       }
      break;

      case 'updates':
       $create = "CREATE TABLE `$_ST[updates]` (
 `uID` int(10) NOT NULL AUTO_INCREMENT,
 `uTitle` varchar(255) NOT NULL,
 `uCategory` varchar(255) NOT NULL DEFAULT '!0!',
 `uEntry` longtext NOT NULL,
 `uDW` enum('y','n') NOT NULL DEFAULT 'n',
 `uDWOpt` varchar(255) NOT NULL DEFAULT '|community:|tags:|userpic:|',
 `uIJ` enum('y','n') NOT NULL DEFAULT 'n',
 `uIJOpt` varchar(255) NOT NULL DEFAULT '|community:|tags:|userpic:|',
 `uLJ` enum('y','n') NOT NULL DEFAULT 'n',
 `uLJOpt` varchar(255) NOT NULL DEFAULT '|community:|tags:|userpic:|',
 `uPending` tinyint(1) NOT NULL DEFAULT '0',
 `uDisabled` tinyint(1) NOT NULL DEFAULT '0',
 `uAdded` datetime NOT NULL,
 PRIMARY KEY (`uID`),
 UNIQUE KEY `uTitle` (`uID`, `uTitle`(50))
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
       $true = $scorpions->query($create);
       if($true == false) {
        $return->query   = $create;
        $return->message = 'The script was unable to create the <samp>' .
        "$_ST[updates]</samp> table.";
        $return->status  = false;
       }

       $create = "CREATE TABLE `$_ST[updates_comments]` (
 `cID` int(20) NOT NULL AUTO_INCREMENT,
 `eNiq` int(10) UNSIGNED NOT NULL,
 `cName` varchar(255) NOT NULL,
 `cEmail` varchar(255) NOT NULL,
 `cURL` varchar(255) NOT NULL,
 `cComment` text NOT NULL,
 `cInfo` text NOT NULL,
 `cFlag` enum('legit', 'spam') NOT NULL DEFAULT 'legit',
 `cPending` tinyint(1) NOT NULL DEFAULT '0',
 `cAdded` datetime NOT NULL, 
 PRIMARY KEY (`cID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
      $true = $scorpions->query($create);
      if($true == false) {
       $return->query   = $create;
       $return->message = 'The script was unable to create the <samp>' .
       "$_ST[updates_comments]</samp> table.";
       $return->status  = false;
      }
     break;
    }
   }

   return $return;
  } 

  /** 
   * @function  $frogs->uninstallAddon() 
   * @param     $c, string; addon slug 
   */ 
  public function uninstallAddon($c) {
   global $_ST, $scorpions;

   /** 
    * Setup our return object ahead of time! 
    */ 
   $return = (object) array(
    'message' => '',
    'query'   => '',
    'status'  => true
   );

   $delete = 'DROP TABLE';
   switch($c) {
    case 'codes':
     $delete .= " `$_ST[codes]`, `$_ST[codes_categories]`, `$_ST[codes_donors]`," . 
     " `$_ST[codes_sizes]`;";
    break;
    case 'kim':
     $delete .= " `$_ST[kim]`;";
    break;
    case 'lyrics':
     $delete .= " `$_ST[lyrics]`, `$_ST[lyrics_albums]`;";
    break;
    case 'quotes':
     $delete .= " `$_ST[quotes]`;";
    break;
    case 'updates':
     $delete .= " `$_ST[updates]`, `$_ST[updates_comments]`;";
    break;
   }
   $true = $scorpions->query($delete);
   if($true == false) {
    $return->message = 'The script was unable to uninstall the' .
        ' <samp>' . $c . '</samp> addon.';
    $return->query  = $delete;
    $return->status = false;
   }

   return $return;
  }

  /** 
   * Install categories from TFL and TAFL 
   * 
   * @function  $frogs->installCategories() 
   */ 
  public function installCategories() {
   global $_ST, $scorpions;

   $category = array();
   /** 
    * Other dates: April 18th, 2008; September 18th, 2008 
    * 
    * @updated June 30th, 2008 
    */  
   $category[] = 'Academia'; 
   $category[] = 'Actors'; 
   $category[] = 'Actresses';
   $category[] = 'Adult'; 
   $category[] = 'Advertising/TV Channels';
   $category[] = 'Albums'; 
   $category[] = 'Animals'; 
   $category[] = 'Animation';
   $category[] = 'Anime/Manga';
   $category[] = 'Anime/Manga: Adult';
   $category[] = 'Anime/Manga: Characters 0-M'; 
   $category[] = 'Anime/Manga: Characters N-Z'; 
   $category[] = 'Anime/Manga: Companies'; 
   $category[] = 'Anime/Manga: Episodes'; 
   $category[] = 'Anime/Manga: Fanstuff';
   $category[] = 'Anime/Manga: General'; 
   $category[] = 'Anime/Manga: Items/Locations'; 
   $category[] = 'Anime/Manga: Magazines';
   $category[] = 'Anime/Manga: Manga-ka/Directors'; 
   $category[] = 'Anime/Manga: Movies/OVAs';
   $category[] = 'Anime/Manga: Music'; 
   $category[] = 'Anime/Manga: Relationships'; 
   $category[] = 'Anime/Manga: Rivalries';
   $category[] = 'Anime/Manga: Series'; 
   $category[] = 'Anime/Manga: Songs'; 
   $category[] = 'Anime/Manga: Toys/Collectibles';
   $category[] = 'Anime/Manga: Websites';
   $category[] = 'Arts and Design'; 
   $category[] = 'Authors/Writers'; 
   $category[] = 'Calendar Events';
   $category[] = 'Characters: Book/Movie'; 
   $category[] = 'Characters: TV'; 
   $category[] = 'Comics'; 
   $category[] = 'Companies';
   $category[] = 'Computer Miscellany and Internet'; 
   $category[] = 'Directors/Producers'; 
   $category[] = 'Episodes'; 
   $category[] = 'Fan Works';
   $category[] = 'Fashion/Beauty'; 
   $category[] = 'Food/Drinks'; 
   $category[] = 'Games'; 
   $category[] = 'History/Royalty'; 
   $category[] = 'Hobbies and Recreation';
   $category[] = 'Literature'; 
   $category[] = 'Magazines/Newspapers'; 
   $category[] = 'Miscellaneous'; 
   $category[] = 'Models'; 
   $category[] = 'Movies';
   $category[] = 'Music Miscellany'; 
   $category[] = 'Musicians: Bands/Groups'; 
   $category[] = 'Musicians: Female'; 
   $category[] = 'Musicians: Male';
   $category[] = 'Mythology/Religion'; 
   $category[] = 'Nature'; 
   $category[] = 'Objects'; 
   $category[] = 'People Miscellany'; 
   $category[] = 'Personalities';
   $category[] = 'Places'; 
   $category[] = 'Politics and Organisations'; 
   $category[] = 'Relationships: Book/Movie';
   $category[] = 'Relationships: Real Life'; 
   $category[] = 'Relationships: TV'; 
   $category[] = 'Songs: Bands/Groups 0-M';
   $category[] = 'Songs: Bands/Groups N-Z'; 
   $category[] = 'Songs: Female Solo'; 
   $category[] = 'Songs: Male Solo'; 
   $category[] = 'Songs: Various';
   $category[] = 'Sports';
   $category[] = 'Sports Entertainment'; 
   $category[] = 'Stage/Theatre'; 
   $category[] = 'Toys/Collectibles'; 
   $category[] = 'Transportation';
   $category[] = 'TV Shows'; 
   $category[] = 'TV/Movie/Book Miscellany'; 
   $category[] = 'Webmasters'; 
   $category[] = 'Websites';

   foreach($category as $cat) {
    $insert = "INSERT INTO `$_ST[categories]` (`catname`, `parent`) VALUES" . 
    " ('$cat', '0')";
    $scorpions->query("SET NAMES 'utf8';");
    $true = $scorpions->query($insert);
    if($true == false) {
     exit('<p class="errorButton"><span class="mysql">ERROR:</span> ' . $scorpions->error() . 
     "<br />\n<em>" . $insert . "</em></p>\n");
    }
   }
  }

  /** 
   * @function  $frogs->showWelcome() 
   */ 
  public function showWelcome() {
	 global $laoptions;
?>
<h2>Install <?php echo $laoptions->version; ?></h2>
<p>Welcome to the installation of <strong><?php echo $laoptions->version; ?></strong>!
There is four steps in total to installing the script, and the script will guide 
you through each step. <strong>Each step is required</strong>, so be sure to read 
through each page!</p>
<p class="nextStep"><a href="install.php?step=1">Start Installation &#187;</a></p>
<?php 
	}

  /** 
   * @function  $frogs->installMain() 
   */ 
  public function installMain() {
	 global $caoptions;
?>
<h2>Step 1: Main Files</h2>
<p>Once you hit "Install" the script will install the main tables, such as
your listings, members, and options. Below is your details, and admin and image 
paths for your collective; although it's recommend you edit these now, you can 
always edit them later.</p>
<form action="install.php" method="post">
 <fieldset>
  <legend>Details</legend>
  <p><label><strong>Collective Name:</strong></label> 
  <input name="cname" class="input1" type="text"></p>
  <p><label><strong>Name:</strong></label> 
  <input name="my_name" class="input1" type="text"></p>
  <p><label><strong>E-Mail Address:</strong></label> 
  <input name="my_email" class="input1" type="text" value="you@yourdomain.com"></p>
  <p><label><strong>Website:</strong></label> 
  <input name="my_website" class="input1" type="text" value="http://"></p>
 </fieldset>

 <fieldset>
  <legend>Admin Path</legend>
<?php 
 $adminPath = $_SERVER['SCRIPT_FILENAME'];
 $adminPath = str_replace('install.php', '', $adminPath);
 $adminURI  = 'http://' . $_SERVER['SERVER_NAME'] . str_replace('install.php', '', $_SERVER['PHP_SELF']);
?>
  <p><label><strong>Admin Paths:</strong><br>
   Admin paths are the path and <abbr title="Uniform Resource Identifier">URI</abbr> 
   to your admin panel. All paths and URLs are already set for you, although 
   they can be changed to the desired path.
  </label> 
  <input name="adm_path" class="input1" type="text" value="<?php echo $adminPath; ?>"><br>
  <input name="adm_http" class="input1" type="text" value="<?php echo $adminURI; ?>"></p>
 </fieldset>

 <fieldset>
  <legend>Categories</legend>
  <p><label><strong>Install categories from TFL and TAFL?</strong><br>
  This installs the categories listed at both TFL and TAFL networks; this is 
  not required.</label> 
  <input name="installcats" class="input3" type="checkbox" value="y"></p>
  <p class="clear"></p>
  <p class="tc"><input name="action" class="nextStep" type="submit" value="Install Main Functions"></p>
 </fieldset>
</form>
<?php 
	}

  /** 
   * @function  $frogs->installFeatures() 
   */ 
  public function installFeatures() {
?>
<h2>Step 2: Features</h2>
<p>We'll be installing the features of Listing Admin -- Joined, Affiliates 
and Wishlist -- with this portion of the installation. All you have to do is
hit "Install Features" below; all options associated with these features can be 
edited later.</p>
<form action="install.php" method="post">
 <fieldset>
  <legend>Install Features</legend>
  <p class="tc"><input name="action" class="nextStep" type="submit" value="Install Features"></p>
 </fieldset>
</form>
<?php 
  }

  /** 
   * @function  $frogs->installAddons() 
   */ 
  public function installAddons() {
   global $get_addon_array, $notSupportedAddons;
   $get_addon_array_for_installation = array_diff_key($get_addon_array, array_flip($notSupportedAddons));
?>
<h2>Step 3: Addons</h2>
<p>The Addons for Listing Admin include -- but aren't limited to! -- Updates,
Codes and <abbr title="Keep In Mind">KIM</abbr>, with most controlling both 
your listings and collective. Don't worry though -- you don't have to install 
them all right away. You can install all of them here, a select few, or none
at all.</p>
<p class="noteButton">All addons can be installed and uninstalld inside the 
admin panel.</p>

<form action="install.php" method="post">
 <fieldset>
  <legend>Install Addons</legend>
<?php  
 foreach($get_addon_array_for_installation as $k => $v) {
  echo "<p><label><strong>$v:</strong></label> <input name=\"$k\" class=\"input3\"" . 
  " type=\"checkbox\" value=\"y\"> Install!</p>\n";
 }
?>
  <p class="tc"><input name="action" class="nextStep" type="submit" value="Install Addons"></p>
 </fieldset>
</form>
<?php 
  }

  /** 
   * @function  $frogs->showFinished() 
   */ 
  public function showFinished() {
   global $get_addon_array;
?>
<h2>Step 3: Addons</h2>
<p>Last step, m'dear! Simply enter in the password of your choice, and the
installation will be complete! (You can also leave the password blank, and 
one will be generated for you.)</p>

<form action="install.php" method="post">
 <fieldset>
  <legend>Finished Installation</legend>
  <p><label><strong>Password:</strong> 
  <input name="password" class="input1" type="password"></p>
  <p class="tc"><input name="action" class="nextStep" type="submit" value="Finish Installation"></p>
 </fieldset>
</form>
<?php 
  }
 
 }
}

$frogs = new frogs();
