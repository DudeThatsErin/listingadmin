<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com>  
 * @file       <fun-misc.inc.php> 
 * @since      September 2nd, 2011 
 * @version    1.8 
 */ 

if(class_exists('seahorses') == false) {
 class seahorses {

  /** 
	 * @function  $seahorses->editOption() 
	 * 
	 * @param     $o, string; option value we're updating 
	 * @param     $v, text; value we're updating to 
	 * @param     $e, character; decides if we're escaping the value 
	 */ 
  public function editOption($o, $v, $e = 1) {
   global $_ST, $scorpions, $tigers;

   if($e == 1) {
    $v = $scorpions->escape($v);
   }

   $update = "UPDATE `$_ST[options]` SET `text` = '$v' WHERE `name` = '$o' LIMIT 1";
   $true   = $scorpions->query($update);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to' . 
    ' update the specified option.|Make sure your table exists.', true, $update);
   } 

   else {
    echo '<p class="successButton"><span class="success">SUCCESS!</span> Your' .
    " <samp>$o</samp> option has been edited! :D</p>\n";
   }
  }

  /** 
	 * @function  $seahorses->getOption() 
	 * @param     $o, string; option we use to return it's value 
	 */ 
  public function getOption($o) {
   global $_ST, $scorpions;

   $select = "SELECT `text` FROM `$_ST[options]` WHERE `name` = '$o' LIMIT 1";
   $r = $scorpions->fetch($select, 'text');

   return $r;
  } 

  /** 
	 * @function  $seahorses->editListing() 
	 * 
   * @param     $i, int; listing ID 
	 * @param     $o, string; listing option value we're updating 
	 * @param     $v, text; value we're updating to 
	 * @param     $e, boolean; decides if we're escaping the value 
	 */ 
  public function editListing($i, $o, $v, $e = 1) {
   global $_ST, $scorpions, $tigers;

   if($e == 1) {
	  $v = $scorpions->escape($v);
   }

   $update = "UPDATE `$_ST[main]` SET `$o` = '$v' WHERE `id` = '$i' LIMIT 1";
   $true = $scorpions->update($update);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to' . 
    ' update the specified listing variable.', true, $update);
   } 

   else {
    echo '<p class="successButton"><span class="success">SUCCESS!</span>' .
    " Your <samp>$o</samp> listing option has been edited! :D</p>\n";
   }
  }

  /** 
	 * @function  $seahorses->getVar() 
   * @param     $i, int; listing ID 
	 * @param     $o, string; listing option we use to return it's value 
	 */ 
  public function getVar($i, $o) {
   global $_ST, $scorpions;

   $select = "SELECT `$o` FROM `$_ST[main]` WHERE `id` = '$i' LIMIT 1";
   $r = $scorpions->fetch($select, $o);

   return $r;
  } 

  /** 
   * @function    $seahorses->writeMessage() 
   * 
   * @param       $e, int; 0 for error messages, 1 for success messages 
   * @param       $t, string; message title 
   * @param       $u, string; URL 
   * @param       $b, text; text (usually contains escape $_POST and 
   * $_GET variables from forms) 
   * @param       $i, text; information, mostly server side 
   * 
   * @deprecated  $octopus->writeError(); 2.3alpha 
   */ 
  public function writeMessage($e = 0, $t, $u, $b, $i) {
   global $_ST, $scorpions;

   $s = $e == 0 ? $_ST['errors'] : $_ST['success'];
   $insert = "INSERT INTO `$s` (`messType`, `messURL`, `messText`, `messInfo`," . 
   " `messAdded`) VALUES ('$t', '$u', '$b', '$i', NOW())";
   $true = $scorpions->query($insert);
  }

  /** 
   * @function  $seahorses->formatExport() 
   * @desc      Format e-mails and URLs 
   */ 
  public function formatExport($s, $b = 'bb', $c = 'encode') {
   $p = trim($s);
   if($c == 'encode') {
    $p = str_replace('@', 'ATTIE', $p);
    if($b == 'bb') {
     $p = str_replace('.', 'DOTTY', $p);
     $p = str_replace('_', 'SCORY', $p);
     $p = str_replace('-', 'DASHY', $p);
    } elseif ($b == 'la') {
     $p = str_replace('.', 'DOTTIE', $p);
     $p = str_replace('_', 'SCORIE', $p);
     $p = str_replace('-', 'DASHIE', $p);
    }
   } elseif ($c == 'decode') {
    $p = str_replace('ATTIE', '@', $p);
    if($b == 'bb') {
     $p = str_replace('DOTTY', '.', $p);
     $p = str_replace('SCORY', '_', $p);
     $p = str_replace('DASHY', '-', $p);
    } elseif ($b == 'la') {
     $p = str_replace('DOTTIE', '.', $p);
     $p = str_replace('SCORIE', '_', $p);
     $p = str_replace('DASHIE', '-', $p);
    }
   }
   return $p;
  }

  /** 
   * This function, just. I created a MONSTER; this originally held, like, 
   * the listing and joined counts, and now it holds ways to pull counts 
   * from lyrics and shit, and I keep asking myself why I'm being a lazy 
   * slag at not creating addon functions for this very purpose 
   * 
   * @function  $seahorses->getCount() 
   * 
   * @param     $c, string; table to pull our count from 
   * @param     $y, string; options associated with each table 
   * @param     $f, boolean; 1 for formatting 0 before the appropriate 
   * counts and 0 for no formatting 
   */ 
  public function getCount($c, $y = 'n', $f = 0) {
   global $_ST, $mermaids, $scorpions, $tigers;

   $select = 'SELECT * FROM';
   if($c == 'cat') {
    $select .= " `$_ST[categories]`";
   } elseif ($c == 'joined') {
    if($y == 'y') {
     $select .= " `$_ST[joined]` WHERE `jStatus` = '1'";
    } elseif ($y == 'm') {
     $select .= " `$_ST[joined]` WHERE `jStatus` = '0'";
    } else {
     $select .= " `$_ST[joined]`";
    }
   } elseif ($c == 'kim') { 
    $select .= " `$_ST[kim]`";
    if($y == 'y') {
     $select .= " WHERE `mPending` = '1'";
    } else {
     $select .= " WHERE `mPending` = '0'";
    }
   } elseif ($c == 'lyrics') {
    if($y == 'a') {
     $select .= " `$_ST[lyrics_albums]`";
    } else {
     $select .= " `$_ST[lyrics]`";
    }
   } elseif ($c == 'quotes') {
    $select .= " `$_ST[quotes]`";
   } elseif ($c == 'wishlist') {
    if($y == 'n') {
		 $select .= " `$_ST[wishlist]`";
		} elseif ($y == 'y') {
		 if((is_countable($mermaids->wishlistList('type', 'granted')) ? count($mermaids->wishlistList('type', 'granted')) : 0) > 0) {
		  $select .= " `$_ST[wishlist]` WHERE `wType` = 'granted'";
		 } elseif ($mermaids->countWishes() > 0) {
      $select .= " `$_ST[main]` WHERE `granted` = '1'";
		 } else {
		  $select .= " `$_ST[wishlist]`";
		 }
    }
   } elseif ($c == 'current') {
    if($y == 'n') {
     $select .= " `$_ST[main]` WHERE `status` = '0'";
    } elseif ($y == 'y') {
     $select .= " `$_ST[main]` WHERE `status` = '0' AND `show` = '0'";
    }
   } elseif ($c == 'upcoming') {
    if($y == 'n') {
     $select .= " `$_ST[main]` WHERE `status` = '1'";
    } elseif ($y == 'y') {
     $select .= " `$_ST[main]` WHERE `status` = '1' AND `show` = '0'";
    }
   } elseif ($c == 'pending') {
    if($y == 'n') {
     $select .= " `$_ST[main]` WHERE `status` = '2'";
    } elseif ($y == 'y') {
     $select .= " `$_ST[main]` WHERE `status` = '2' AND `show` = '0'";
    }
   } elseif ($c == 'approved') {
    $select .= " `$_ST[members]` WHERE `mPending` = '0'";
   } elseif ($c == 'unapproved') {
    $select .= " `$_ST[members]` WHERE `mPending` = '1'";
   } elseif ($c == 'affiliates') {
    $select .= " `$_ST[affiliates]`";
    if($y == 'y') {
     $select .= " WHERE `fNiq` = '0' OR `fNiq` LIKE '%!0!%'";
    }
   } elseif ($c == 'updates') {
    $select .= " `$_ST[updates]`";
    if($y == 'c') {
     $select .= " WHERE `uCategory` LIKE '%!0!%'";
    }
   }
   $true  = $scorpions->query($select);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to' . 
    ' select the specific count.', true, $select);
   }
   $count = $scorpions->total($true);
   $c     = $f == 1 && $count != 0 && $count < 10 ? '0' . $count : $count;

   return $c;
  }

	/** 
   * @function  $seahorses->memberCount() 
   * @param     $p, int; status 
	 * @since     2.3alpha 
   */ 
  public function memberCount($p = 0) {
   global $snakes, $wolves;

   $listings = $wolves->listingsList();
	 $count    = 0;
   foreach($listings as $id) {
    $c = $snakes->getMemberCount($id, $p);
		if($c > 0) {
		 $count += $c;
		}
   }

   return $count;
  }

  /** 
   * @function  $seahorses->templatesList() 
   * @param     $b, string; return name or title 
   * @since     2.3alpha 
   */ 
  public function templatesList($b = 'name') {
   global $_ST, $scorpions, $tigers;

   $select = "SELECT * FROM `$_ST[templates]` ORDER BY `name` ASC";
   $true = $scorpions->query($select);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to' . 
		' select the templates from the database.', false);
   }

   $all = array();
   while($getItem = $scorpions->obj($true)) {
    $all[] = ($b == 'title' ? $getItem->title : $getItem->name);
   }

   return $all;
  }

  /** 
   * @function  $seahorses->getTemplate() 
   * @param     $n, string; template slug 
   * @param     $r, string; return template object or the template itself 
   * @updated   2.3alpha 
   */ 
  public function getTemplate($n, $r = 'template') {
   global $_ST, $scorpions, $tigers;

   $select = "SELECT * FROM `$_ST[templates]` WHERE `name` = '$n' LIMIT 1";
   $true = $scorpions->query($select);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script cannot select the' . 
    ' template from the database.', false);
   }
   $getItem = $scorpions->obj($true);

   return ($r == 'object' ? $getItem : $getItem->template);
  }

	/** 
   * Return variable list and example based on template slug :D  
   * 
   * @function  $seahorses->templates() 
   * @param     $n, string; template name (slug) 
   * @since     2.3beta 
   */ 
	public function templates($n) {
	 switch($n) {
	  case 'affiliates_template':
?>
<h4>Variables</h4>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{image}</td>
 <td class="d">Image of the affiliate</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{subject}</td>
 <td class="d">Subject of the affiliate</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{url}</td>
 <td class="d"><abbr title="Uniform Resource Identifier">URI</abbr> of the affiliate</td>
</tr></tbody>
</table>

<h4>Example</h4>
<p>Below is an example template you can use for the affiliates template.</p>
<code class="tc">
&#60;a href=&#34;{url}&#34;&#62;&#60;img src=&#34;{image}&#34; alt=&#34;{subject}&#34;
title=&#34;{subject}&#34; /&#62;&#60;/a&#62;
</code>
<?php 
		break;

		case 'collective_stats_template':
?>
<h4>Variables</h4>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{current}<br>{upcoming}<br>{pending}</td>
 <td class="d">The current, upcoming and pending numbers for your listings. If 
 you want your current listings listed, use {current} and so on</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{mAll}<br>{mApproved}<br>{mPending}</td>
 <td class="d">Total member counts.</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{joined}</td>
 <td class="d">Number of joined listings</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{url}</td>
 <td class="d"><abbr title="Uniform Resource Identifier">URI</abbr> of joined listing</td>
</tr></tbody>
</table>

<h4>Example</h4>
<p>Below is an example template you can use for the collective statistics template.</p>
<code>
&#60;table id=&#34;stats&#34; width=&#34;100%&#34;&#62;&#60;tbody&#62;<br>
&#60;tr&#62;&#60;td class=&#34;right&#34;&#62;Current Listings:&#60;/td&#62;<br>
&#60;td class=&#34;left&#34;&#62;{current} (&#60;em&#62;{upcoming} Upcoming
&#60;/em&#62;)&#60;/td&#62;&#60;/tr&#62;<br>
&#60;tr&#62;&#60;td class=&#34;right&#34;&#62;Joined Listings:&#60;/td&#62;<br>
&#60;td class=&#34;left&#34;&#62;{joined} Listings&#60;/td&#62;&#60;/tr&#62;<br>
&#60;tr&#62;&#60;td class=&#34;right&#34;&#62;Overall Member Count:&#60;/td&#62;<br>
&#60;td class=&#34;left&#34;&#62;{mApproved} (&#60;em&#62;{mPending} Pending
&#60;/em&#62;)&#60;/td&#62;&#60;/tr&#62;<br>
&#60;tr&#62;&#60;td class=&#34;right&#34;&#62;Newest Listing:&#60;/td&#62;<br>
&#60;td class=&#34;left&#34;&#62;{newest}&#60;/td&#62;&#60;/tr&#62;<br>
&#60;/tbody&#62;&#60;/table&#62;
</code>
<?php 
		break;

		case 'joined_template':
?>
<h4>Variables</h4>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{category}</td>
 <td class="d">Category (or categories) of the joined listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{image}</td>
 <td class="d">Image of joined listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{subject}</td>
 <td class="d">Subject of joined listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{url}</td>
 <td class="d"><abbr title="Uniform Resource Identifier">URI</abbr> of joined listing</td>
</tr></tbody>
</table>

<h4>Example</h4>
<code class="tc">
&#60;a href=&#34;{url}&#34;&#62;&#60;img src=&#34;{image}&#34; alt=&#34;{subject}&#34; 
title=&#34;{subject}&#34; class=&#34;joined&#34; /&#62;&#60;/a&#62;
</code>
<?php 
		break;

		case 'kim_stats_template': 
?>
<h4>Variables</h4>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{members}<br>{pending}</td>
 <td class="d">Number of approved and pending members</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{updated}</td>
 <td class="d">"Last Updated" date</td>
</tr></tbody>
</table>

<h4>Example</h4>
<code>
&#60;table class=&#34;kim-stats&#34; width=&#34;100%&#34;&#62;&#60;tbody&#62;<br>
&#60;tr&#62;&#60;td class=&#34;tr&#34;&#62;Listed:&#60;/td&#62;<br>
&#60;td class=&#34;tl&#34;&#62;{members} (&#60;em&#62;{pending} Pending
&#60;/em&#62;)&#60;/td&#62;&#60;/tr&#62;<br>
&#60;tr&#62;&#60;td class=&#34;tr&#34;&gt;Last Updated:&#60;/td&#62;<br>
&#60;td class=&#34;tl&#34;&#62;{updated}&#60;/td&#62;&#60;/tr&#62;<br>
&#60;/tbody&#62;&#60;/table&#62;
</code>
<?php 
		break;

		case 'listings_template':
?>
<h4>Variables</h4>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{approved}<br>{pending}</td>
 <td class="d">Number of approved and pending members of the listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{category}</td>
 <td class="d">Categor(y/ies) of listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{image}</td>
 <td class="d">Image of listing (returns image path + image, e.g. 
 <samp>http://myweb.com/images/imag36.gif</samp>)</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{since}<br>{updated}</td>
 <td class="d">Opened and "Last Updated" dates</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{subject}</td>
 <td class="d">Subject of listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{title}</td>
 <td class="d">Title of listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{url}</td>
 <td class="d"><abbr title="Uniform Resource Identifier">URI</abbr> of listing</td>
</tr></tbody>
</table>

<h4>Example</h4>
<code>
&#60;p class=&#34;img&#34;&#62;&#60;a href=&#34;{url}&#34;&#62;&#60;img src=&#34;{image}&#34;
alt=&#34;{subject}&#34; title=&#34;{subject}&#34; 
class=&#34;current&#34;&#62;&#60;/a&#62;&#60;/p&#62;<br>
&#60;p class=&#34;details&#34;&#62;<br>
&#60;strong&#62;Subject:&#60;/strong&#62; {subject}&#60;br&#62;<br>
&#60;strong&#62;Members:&#60;/strong&#62; {approved} (&#60;em&#62;{pending} Pending
&#60;/em&#62;)&#60;br&#62;<br>
&#60;strong&#62;Since:&#60;/strong&#62; {since}&#60;br&#62;<br>
&#60;strong&#62;Last Updated:&#60;/strong&#62; {updated}<br>
&#60;/p&#62;<br>
&#60;p class=&#34;desc&#34;&#62;<br>
{desc}<br>
&#60;/p&#62;
</code>
<?php 
		break;

		case 'wishlist_template':
		case 'wishlist_query_template':
		case 'wishlist_granted_template':
		case 'wishlist_top_template':
?>
<p>Below is the variables and example template for all four wishlist templates. 
All variables apply to the wishlist, but each template can be coded 
differently. :D</p>

<h4>Variables</h4>
<table class="statsTemplates">
<thead><tr>
 <th class="l">Template</th>
 <th>Description</th>
</tr></thead>
<tbody><tr>
 <td class="t">{category}</td>
 <td class="d">Category (or categories) of the wish</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{desc}</td>
 <td class="d">Description of wish</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{image}</td>
 <td class="d">Image of listing</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{query}</td>
 <td class="d"><abbr title="Uniform Resource Identifier">URI</abbr> of wish 
 (e.g. <samp>wishlist.php?q=24</samp>)</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{subject}</td>
 <td class="d">Subject of wish</td>
</tr></tbody>
<tbody><tr>
 <td class="t">{url}</td>
 <td class="d"><abbr title="Uniform Resource Identifier">URI</abbr> of the wish 
 (for wishlist_query), for the fanlisting (wishlist_granted) and a URL, if 
 provided (wishlist_top / wishlist)</td>
</tr></tbody>
</table>

<h4>Code</h4>
<code class="tc">
&#60;p class=&#34;floatLeft&#34;&#62;&#60;img src=&#34;{image}&#34; 
alt=&#34;{subject} ({category})&#34; title=&#34;{subject} ({category})&#34; 
class&#34;top-wishlist&#34;&#62;&#60;br&#62;{desc}&#60;/p&#62;
</code>
<?php 
		break;

		default:

		break;
	 }
	}

	/** 
   * @function  $seahorses->errorView() 
   * @param     $i, int; message ID; optional 
   */ 
	public function errorView($i) {
	 global $_ST, $scorpions, $get_errors_array, $tigers;

	 $select = "SELECT * FROM `$_ST[errors]` WHERE `messID` = '$i' LIMIT 1";
	 $true = $scorpions->query($select);
	 if($true == false) {
	  $tigers->displayError('Database Error', 'The error log selected' . 
		' appears to not exist in the database.', true, $select);
	 }
	 $getItem = $scorpions->obj($true, 0);

	 $class = '';
	 if($getItem->messType == 'Join Error' || $getItem->messType == 'Update Error') {
	  $class = 'forms';
		$text = explode("\n", $getItem->messText);
?>
 <tbody><tr>
  <td class="left">Type:</td>
	<td class="right">
   <?php echo $get_errors_array[$class]; ?> (<?php echo $getItem->messType; ?>)
  </td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">Name:</td>
	<td class="right"><?php echo str_replace('Name:', '', trim($text[0])); ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">E-mail Address:</td>
	<td class="right"><?php echo str_replace('E-Mail Address:', '', trim($text[1])); ?></td>
 </tr></tbody>
<?php  
    if(strpos($text[2], 'URL:') !== false) {
?>
 <tbody><tr>
  <td class="left">URL:</td>
	<td class="right"><?php echo str_replace('URL:', '', trim($text[2])); ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">New E-mail Address:</td>
	<td class="right"><?php echo str_replace('New E-Mail Address:', '', trim($text[4])); ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">New URL:</td>
	<td class="right"><?php echo str_replace('New URL:', '', trim($text[5])); ?></td>
 </tr></tbody>
<?php 
    } else {
?>
<tbody><tr>
  <td class="left">URL:</td>
	<td class="right">&#8211;</td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">New E-mail Address:</td>
	<td class="right"><?php echo str_replace('New E-Mail Address:', '', trim($text[3])); ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">New URL:</td>
	<td class="right"><?php echo str_replace('New URL:', '', trim($text[4])); ?></td>
 </tr></tbody>
<?php 
		}
	 } elseif (strpos($getItem->messType, 'SPAM') !== false) {
	  $class = 'spam';
		$subj = explode('(', $getItem->messType);
		$text = explode("\n", $getItem->messText);
?>
 <tbody><tr>
  <td class="left">Type:</td>
	<td class="right">
   <?php echo $get_errors_array[$class]; ?> (<?php echo str_replace('SPAM Error: ', '', trim($subj[0])); ?>)
  </td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">Name:</td>
	<td class="right"><?php echo str_replace('Name:', '', trim($text[0])); ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">E-mail Address:</td>
	<td class="right">
   <?php echo str_replace('E-Mail Address:', '', trim($text[1])); ?>
  </td>
 </tr></tbody>
<?php 
	 } elseif (strpos($getItem->messType, 'User Log-In') !== false) {
	  $class = 'user';
		$text = explode("\n", $getItem->messText);
?>
 <tbody><tr>
  <td class="left">Type:</td>
	<td class="right"><?php echo $get_errors_array[$class]; ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">Userame:</td>
	<td class="right"><?php echo str_replace('Username:', '', trim($text[0])); ?></td>
 </tr></tbody>
 <tbody><tr>
  <td class="left">Password:</td>
	<td class="right"><?php echo str_replace('Password:', '', trim($text[1])); ?></td>
 </tr></tbody>
<?php 
	 }
	}

	# End options~ 
 }
}

$seahorses = new seahorses();
