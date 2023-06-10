<?php
/** 
 * @copyright  2007 
 * @license    GPL Version 3; BSD Modified 
 * @author     Tess <theirrenegadexxx@gmail.com> 
 * @file       <templates.inc.php> 
 * @since      November 20th, 2011  
 * @version    1.0 
 */ 

$affa = $scorpions->escape('Hello,

This is a notice to let you know that you have been added at the {listing} {type}! You can now see your listing listed at the {type}: <{listing_url}>.

If you need to change your information, you can send in another affiliation request at the {type}. :)

--
{owner}
{listing_title} <{listing_url}>');
$affc = $scorpions->escape('Hello,

This is a notice to let you know that the {listing} listing has closed. If you could remove your link(s) as soon as possible, it would be greatly appreciated!

--
{owner}
{listing} <{listing_url}>');
$affm = $scorpions->escape('Hello {name},

This is a notice to let you know that the {listing} listing has closed. If you could remove your links as soon as possible, it would be greatly appreciated!

--
{owner}
{listing} <{listing_url}>');
$kima = $scorpions->escape("Hello {name},

This is a notice to let you know that you have been removed from the pending list at {collective_name}'s KIM list and added to the members list. You can now see your information at the list: <{kim_list}>.

If you need to change your information, you can do so here: <{kim_update}>. :D

--
{owner}
{collective_name} <{collective_url}>");
$kimu = $scorpions->escape("Hello {name},

This is a notice to let you know that you have been removed from the pending list at {collective_name}'s KIM list and your information has been updated at your request. Your information is below: 

Name: {name}
E-Mail Address: {email}
URL: {url}
Listing: {listing}

If this has been an error, or the information listed is wrong, feel more than free to reply to this message and let me know. Thank you for keeping your information up to date! :D

--
{owner}
{collective_name} <{collective_url}>");
$mema = $scorpions->escape('Hello {name},

This is a notice to let you know that you have been removed from the pending list at the {listing} listing and added to the members list. You can now see your information at the listing: <{listing_url}>.

If you need to change your information, you can do so at the listing. :D

--
{owner}
{title} <{listing_url}>');
$memc = $scorpions->escape('Hello {name},

This is a notice to let you know that the {listing} listing has closed. If you could remove your link(s) (if applicable) as soon as possible, it would be greatly appreciated!

--
{owner}
{listing} <{listing_url}>');
$memd = '';
$meml = $scorpions->escape('Hello {name},

Someone (hopefully you) asked for a renewal of their password at the {listing} listing. Below is an automated alphanumerical password; I strongly recommend you change your password by way of update form at the listing:

Password: {password}

If this is in error, simply send in an update form updating your password. If the problem persists, feel more than free to contact me!

--
{owner}
{collective_name} <{collective_url}>');
$memm = $scorpions->escape('Hello {name},

This is a notice to let you know that the {listing} listing has closed. If you could remove your links (if applicable) as soon as possible, it would be greatly appreciated!

--
{owner}
{listing} <{listing_url}>');
$memu = $scorpions->escape('Hello {name},

This is a notice to let you know that you have been removed from the pending list at the {listing} listing and your information has been updated at your request. Your information is below: 

Name: {name}
E-Mail Address: {email}
URL: {url}
Country: {country}

If this has been an error, or the information listed is wrong, feel more than free to reply to this message and let me know. Thank you for keeping your information up to date! :D

--
{owner}
{listing_title} <{listing_url}>');
