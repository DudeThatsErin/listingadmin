<?php
/** 
 *  @copyright   2007 
 *  @license     GPL Version 3; BSD Modified 
 *  @author      Tess <theirrenegadexxx@gmail.com> 
 *  @file        <fun-captcha.inc.php> 
 *  @since       September 2nd, 2010 
 *  @version     2.1+     
 */ 
 require('b.inc.php');
 require_once(MAINDIR . 'rats.inc.php');
 require_once('fun.inc.php');
 require_once('fun-misc.inc.php');
 
 /** 
  * @desc  Grab captcha text from "v" query in the URL 
	* @note  Regardless of what's returned in the string, it will ultimately be 
	* checked in the script; I'm still sanitising the string, though, 'cause I 
	* R ANAL 8D 
  */ 
 $s = $tigers->cleanMys(substr(sha1($_GET['v']), 0, 6));
 
 /** 
  * $desc  Create image, background color and text color~ 
  */ 
 $m = imagecreatetruecolor(170, 80);
 $b = imagecolorallocate($m, 48, 48, 48);
 $c = imagecolorallocate($m, 215, 215, 215);
 
 /** 
  * $desc  Create actual image background, and apply text 8D 
  */
 imagefilledrectangle($m, 0, 0, 169, 79, $b);
 imagettftext($m, 30, 0, 13, 53, $c, MAINDIR . 'textMuseo.otf', $s);
 
 /** 
  * $desc  Headers first, image outputting/destorying last~ 
  */ 
 header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
 header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
 header('Cache-Control: no-store, no-cache, must-revalidate');
 header('Cache-Control: post-check=0, pre-check=0', false);
 header('Pragma: no-cache');
 header('Content-Type: image/png');

 imagepng($m);
 imagedestroy($m);
