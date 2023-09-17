/** 
 * Listing Admin (c) 2007-2012 
 * 
 * @author Tess <treibend@gmail.com> 
 * @license GPL3 
 * @version 2.4 
 */ 

Listing Admin -- formerly known as STFU T-Rex! Admin -- is a fanlisting 
collective script that manages both your collective and fanlistings. Its main 
features are listings, joined listings, categories (parent and one-level
subcategories), wishlist and management of members. The features include, but 
aren't limited to updates, code buttons and a KIM list; there is also the option 
of crosslisting fanlistings to Enthusiast or phpFanUpdate, as well as other 
Listing Admin installations.

Listing Admin is managed by one person -- that'd be me, Tess! -- with the sole 
purpose of helping out other fanlisting owners. No guarantee is made that this 
will satisfy all, and/or that it'll be problem free. I am more than willing to 
give support, help and advice for installing, upgrading, bugfixing and any other 
jazz via e-mail -- treibend@gmail.com or theirrenegadexxx@gmail.com -- 
or my script archive, Pumpin'!: <http://scripts.wyngs.net/>

>> LISTING ADMIN USES:

Listing Admin was originally compiled with PHP version 5.2+ and MySQL 
5.0.51b+. Since then, Listing Admin has been tested with PHP 5.4.3 and 
MySQL 5.5.24.

Support for PHP4 and MySQL4 is neither being tested nor supported. 
Listing Admin uses many functions and methods exclusive with PHP5, 
and while I wouldn't be against anybody who tested LA for both of these 
versions, I'd highly recommend not doing so because of aforementioned 
reason. :P

>> INSTALL THE SCRIPT

1.) Download Listing Admin (which, if you're viewing this file, you've already 
done), and unzip the .zip file. Go to the rats.inc.php file -- located in the 
"admin" folder in the .zip file -- and edit the variables the file tells you 
to edit. There's a "STOP RIGHT HERE" note when the editing process ends in the 
file, so you need only edit the database variables; the table prefix and 
database engine variables are optional.

2.) Upload all files in the admin folder to a folder apart from your main 
directory. These files can be located on their own subdomain, but they must not 
be uploaded with your main site. When all files have been uploaded, go to the 
install.php file in your browser. 

For example, let's say I've uploaded all the files to a folder called 
!listingadmin; the address might be something like this:

http://mywebsite.com/!listingadmin/install.php

3.) There are several steps to installing the script, which will guide you; 
only installing addons to Listing Admin are optional, and you can go back at a 
later date in your admin panel (under "Addons") and install/uninstall your 
addons.

4.) If no errors have appeared, and the script has created a username and 
password for you, you have installed the script! Using the above example, you 
can go to:

http://mywebsite.com/!listingadmin/

And login to the script!

>> UPGRADE THE SCRIPT 

1.) Go to upgrade.php in your browser, in your admin folder. If the admin folder 
is named !adminpanel, then your upgrade file would be located at:

http://mywebsite.com/!adminpanel/upgrade.php

2.) Once there, you can select your //last// version of the script (the version 
you're upgrading from), and upgrade the script!

Please note that not all versions will need to be upgraded; if your version is 
not in the dropdown menu, then you need not upgrade. :') 

>> CUSTOMISING THE SCRIPT 

 1.) Templates -- under "Templates" in the admin panel -- are an important part 
of customising your collective and fanlistings. As of version 2.3, there are 
templates for Affiliates, Collective statistics, Dates, Joined, KIM statistics, 
Listings, Updates, and Wishlist (for each category: query, top, regular and 
custom). 

1a.) You are encouraged to edit these templates, which can contain HTML, as you 
go add includes to your website.

1b.) When you manage your listings, each listing has it's own set of templates: 
affiliates, description, members (list, header and footer), wishlist, 
statistics, quotes, and updates.

 2.) Display codes -- or includes -- under "Display Codes" in the admin panel 
are made available for displaying the script across your websites. Each include 
will give you examples, as well as explain what the variables therein will do.
