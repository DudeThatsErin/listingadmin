<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="Author" content="Tess, Ekaterina (http://scripts.robotess.net)">
    <title> <?php echo $laoptions->version; ?> &#8212; <?php echo $leopards->isTitle($getTitle); ?> </title>
    <link href="style.css?v=2" rel="stylesheet" type="text/css">
</head>

<body>

<div id="container">

    <header>
        <h1><?php echo $laoptions->version; ?></h1>
    </header>

    <div id="wrapper">

        <section id="sidebar">
            <nav>
                <menu>
                    <li<?php echo $leopards->currently('listings'); ?>><a href="listings.php">Listings</a></li>
                    <li<?php echo $leopards->currently('members', 'n'); ?>><a href="members.php">Members</a></li>
                    <li<?php echo $leopards->currently('affiliates'); ?>><a href="affiliates.php">Affiliates</a></li>
                    <li<?php echo $leopards->currently('joined'); ?>><a href="joined.php">Joined</a></li>
                    <li<?php echo $leopards->currently('wishlist'); ?>><a href="wishlist.php">Wishlist</a></li>
                </menu>
                <menu>
                    <li<?php echo $leopards->currently('addons'); ?>><span>[+]</span> <a href="addons.php">Addons</a>
                    </li>
                    <?php
                    $currentPage = $leopards->isPage();
                    if ($currentPage === 'codes.php' ||
                        $currentPage === 'codes-categories.php' ||
                        $currentPage === 'codes-donors.php' ||
                        $currentPage === 'codes-sizes.php' ||
                        $currentPage === 'kim.php' ||
                        $currentPage === 'lyrics.php' ||
                        $currentPage === 'quotes.php' ||
                        $currentPage === 'updates.php' ||
                        $currentPage === 'updates-comments.php'
                    ) {
                        $displayblock = ' style="display: block;"';
                    } else {
                        $displayblock = ' style="display: none;"';
                    }
                    ?>
                    <li id="addons"<?php echo $displayblock; ?>>
                        <menu>
                    <li<?php echo $leopards->currently('codes'); ?>><a href="codes.php">Codes</a></li>
                    <li<?php echo $leopards->currently('codes-categories'); ?>><a href="codes-categories.php">Codes:
                            Categories</a></li>
                    <li<?php echo $leopards->currently('codes-donors'); ?>><a href="codes-donors.php">Codes: Donors</a>
                    </li>
                    <li<?php echo $leopards->currently('codes-sizes'); ?>><a href="codes-sizes.php">Codes: Sizes</a>
                    </li>
                    <li<?php echo $leopards->currently('lyrics'); ?>><a href="lyrics.php">Lyrics</a></li>
                    <li<?php echo $leopards->currently('kim'); ?>><a href="kim.php">KIM</a></li>
                    <li<?php echo $leopards->currently('quotes'); ?>><a href="quotes.php">Quotes</a></li>
                    <li<?php echo $leopards->currently('updates'); ?>><a href="updates.php">Updates</a></li>
                    <li<?php echo $leopards->currently('updates-comments', 1); ?>><a href="updates-comments.php">Updates:
                            Comments</a></li>
                    <li<?php echo $leopards->currently('n'); ?>>&#183;</li>
                </menu>
                </li>
                <li<?php echo $leopards->currently('categories'); ?>><a href="categories.php">Categories</a></li>
                <li<?php echo $leopards->currently('templates'); ?>><a href="templates.php">Templates</a></li>
                <li<?php echo $leopards->currently('templates_email'); ?>><a href="templates_emails.php">Templates:
                        Emails</a></li>
                <li<?php echo $leopards->currently('emails'); ?>><a href="emails.php">Emails</a></li>
                </menu>
                <menu>
                    <li<?php echo $leopards->currently('options'); ?>><a href="options.php">Options</a></li>
                    <li<?php echo $leopards->currently('display_codes'); ?>><a href="display_codes.php">Display
                            Codes</a></li>
                    <li<?php echo $leopards->currently('errors'); ?>><a href="errors.php">Error Log</a></li>
                    <li<?php echo $leopards->currently('index'); ?>>
                        <a href="<?php echo str_replace('inc/', '', $seahorses->getOption('adm_http')); ?>">Control
                            Panel</a>
                    </li>
                    <li class="last"><a href="<?php echo $currentPage; ?>?g=logout">Logout &#187;</a></li>
                </menu>
            </nav>
        </section>

        <section id="content">
