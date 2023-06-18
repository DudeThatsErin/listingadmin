# Listing Admin for PHP7 or 8 [Erin's Fork]
This is the main repo for Listing Admin 1.1 for PHP8.

The original author is Tess (website is not online anymore). Tess' ReadMe can be found [here](https://github.com/DudeThatsErin/listingadmin/blob/main/old-readmes/Tess-Readme.md).

The previous author is [Ekaterina](https://scripts.robotess.net/). Ekaterina's ReadMe can be found [here](https://github.com/DudeThatsErin/listingadmin/blob/main/old-readmes/Ekaterina-Readme.md).

*Note: Ekaterina's version is not recommended for new installations. Mine **can** be used for new installations.*

# Installation Instructions

These are the instructions you will want to follow when you are installing LA for the first time.

## Step 1 - Download an archive.

COMING SOON!

## Step 2 - Upload all files to your collective.

You will want to unzip the archive and upload everything inside the  `public` folder to your folder.

## Step 3 - Update Database Details for your Collective + Fanlistings
Find the `jac.sample.inc.php` file that you downloaded and open it up in your editor. Can be NotePad though I recommend NotePad++, Visual Studio Code, or Sublime. Either way, just open it so you can see the lines of code.

Find these lines:
```php
$database_host = 'localhost';
$database_user = 'username';
$database_pass = 'password';
$database_name = 'databaseName';
```
Update those details to your details that you found in Step 1. Then save and close the file.

In your file explorer, rename the file from `jac.sample.inc.php` to `jac.inc.php` (make sure there is only one `.php` at the end) and upload to **every** fanlisting + your fanlisting collective.

Finally, in your `admin/` directory do the same thing that you just did with the `jac.sample.inc.php` file except you are looking for the `rats.sample.inc.php` file. You will be updating the database details (they look the same in this file as well) and renaming the file to `rats.inc.php`.

## Step 4 - Visit your collective to make sure it works!
This should 100% work on the first try. If it doesn't, make sure you read these steps carefully. If it doesn't and you have re-read these instructions, open an issue (at the top) and let me know what you have tried.


# Update Instructions
*Note: I am **not** providing support for versions lower than 1.0.5.*

If you are using Listing admin 2.4 (the old version by Tess) please follow her readme, though I **highly** recommend against that.

## Step 1 - Backup, Backup, BACKUP!
Backup all of your current Listing Admin configurations, files, and databases first. ALWAYS do this before doing any install or upgrade.

Also, take note of your database information (I refer to as *variables*) in all of your `config.php` files.

## Step 2 - Download an archive.

COMING SOON!

## Step 3 - Replace your current files with the new files
Replace the files inside your `admin/` directory (folder) with the `public/admin/` files from this repository. Make sure that you have all files from the folder uploaded.

## Step 4 - Update Database Details for your Collective + Fanlistings
Find the `jac.sample.inc.php` file that you downloaded and open it up in your editor. Can be NotePad though I recommend NotePad++, Visual Studio Code, or Sublime. Either way, just open it so you can see the lines of code.

Find these lines:
```php
$database_host = 'localhost';
$database_user = 'username';
$database_pass = 'password';
$database_name = 'databaseName';
```
Update those details to your details that you found in Step 1. Then save and close the file.

In your file explorer, rename the file from `jac.sample.inc.php` to `jac.inc.php` (make sure there is only one `.php` at the end) and upload to **every** fanlisting + your fanlisting collective.

Finally, in your `admin/` directory do the same thing that you just did with the `jac.sample.inc.php` file except you are looking for the `rats.sample.inc.php` file. You will be updating the database details (they look the same in this file as well) and renaming the file to `rats.inc.php`.

## Step 5 - Visit your collective to make sure it works!
This should 100% work on the first try. If it doesn't, make sure you read these steps carefully. If it doesn't and you have re-read these instructions, open an issue (at the top) and let me know what you have tried.

# Questions?
## What are the `samplecollective` and `samplefl` for?
They are folders that were added by either Tess or Ekaterina for previewing how your fanlisting and collective might look. They are there for convenience. You can delete these or keep them to reuse for future fanlistings.

## Future questions will be added later!
As they come up I will add more FAQ here. :)
