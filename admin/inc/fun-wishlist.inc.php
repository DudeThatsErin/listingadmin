<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <treibend@gmail.com> 
 * @file       <fun-wishlist.inc.php> 
 * @since      September 2nd, 2010 
 * @version    2.3beta   
 */ 

if(class_exists('mermaids') == false) {
 class mermaids {

  /** 
   * @function  $mermaids->wishlistList() 
   * @param     $b, string; sort by category or type; optional 
   * @param     $c, string; category ID or wishlist type; optional 
   */ 
  public function wishlistList($b = 'id', $c = '') {
   global $_ST, $get_wishlist_array, $scorpions, $tigers;

   $select = "SELECT * FROM `$_ST[wishlist]`";
   if($b == 'category' && ($c != '' && is_numeric($c))) {  
    $select .= " WHERE `wCategory` LIKE '%!$c!%'";
   } elseif ($b == 'type' && ($c != '' && array_key_exists($c, $get_wishlist_array))) {
    $select .= " WHERE `wType` = '$c'";
   }
   $select .= ' ORDER BY `wID` ASC';
   $true = $scorpions->query($select);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script could not select' . 
    ' the wishes from the database.', true, $select);
   }

   $all = array();
   while($getItem = $scorpions->obj($true, 0)) {
    $all[] = $getItem->wID;
   }

   return $all;
  }

	/** 
   * @function  $mermaids->countWishes() 
   * @param     $b, string; sort by category or type; optional 
   * @param     $c, string; category ID or wishlist type; optional 
   */ 
  public function countWishes() {
	 global $_ST, $scorpions, $tigers;

	 $select = "SELECT * FROM `$_ST[main]` WHERE `granted` = '1'";
	 $true = $scorpions->query($select);
	 if($true == false) {
	  $tigers->displayError('Database Error', 'The script was unable to count the' . 
		' wishes from the listing database.', false);
	 }
	 $count = $scorpions->total($true);

	 return $count;
	}

	/** 
	 * @function  $mermaids->getWish() 
	 * @param     $i, int; wishlist ID 
	 */ 
  public function getWish($i) {
   global $_ST, $scorpions, $tigers;

   $select = "SELECT * FROM `$_ST[wishlist]` WHERE `wID` = '$i' LIMIT 1";
   $true   = $scorpions->query($select);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to select' . 
		' the wish from the specified ID.', false);
   }
   $getItem = $scorpions->obj($true);

   return $getItem;
  }

  /** 
	 * @function  $mermaids->pullImage_Wishlist() 
	 * @param     $i, int; wishlist ID 
	 */ 
  public function pullImage_Wishlist($i) {
   global $_ST, $scorpions, $tigers;

   $select = "SELECT `wImage` FROM `$_ST[wishlist]` WHERE `wID` = '$i' LIMIT 1";
   $true   = $scorpions->query($select);
   if($true == false) {
    $tigers->displayError('Database Error', 'The script was unable to select' . 
		' the image from the specified wish ID.', false);
   }
   $getItem = $scorpions->obj($true);

   return $getItem->wImage;
  }

  /** 
	 * @function  $mermaids->getTemplate_Wishlist() 
	 * @param     $i, int; wishlist ID 
	 * @param     $b, text; template slug 
	 */ 
  public function getTemplate_Wishlist($i, $b) {
   global $_ST, $_URL, $lions, $octopus, $scorpions, $seahorses, $tigers;

   $getItem  = $this->getWish($i);
   $template = $seahorses->getTemplate($b);

   if(strpos($getItem->wDesc, '{MORE}') !== false) {
    if($b == 'wishlist_query_template') {
	   $ex   = explode('{MORE}', $getItem->wDesc);
	   $desc = trim($ex[1]);
	   $desc = $octopus->getLineBreakers(html_entity_decode($desc));
    } elseif ($b == 'wishlist_top_template') {
     $ex   = explode('{MORE}', $getItem->wDesc);
	   $desc = html_entity_decode(trim($ex[0]));
	  }
   } else {
    $desc = html_entity_decode($getItem->wDesc);
   }

   $format = html_entity_decode($template);
   $format = str_replace('{category}', $lions->pullCatNames($getItem->wCategory, '!'), $format);
   $format = str_replace('{desc}', $desc, $format);
	 $format = str_replace('{id}', $getItem->wID, $format);
   $format = str_replace('{image}', $seahorses->getOption('wsh_http') . $getItem->wImage, $format);
   $format = str_replace('{query}', $_URL . 'q=' . $getItem->wID, $format);
   $format = str_replace('{subject}', $getItem->wSubject, $format);
   $format = str_replace('{url}', $getItem->wURL, $format);

   return $format;
  }

  # End functions here~ 
 }
}

$mermaids = new mermaids();
