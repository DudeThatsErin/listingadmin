<?php
$feedUrl = 'https://scripts.robotess.net/projects/listing-admin/atom.xml';
?>
<script>
    const RSS_URL = `<?= $feedUrl?>`;
</script>
<div id="feeds">
    <div id="lafeeds">
        <h4>
            <ins><?= $laoptions->version ?></ins>
            Feed
        </h4>
        <menu>
            Please wait until the feed loads via JavaScript or <a href="<?= $feedUrl; ?>" target="_blank">check manually</a>.
        </menu>
    </div>
    <section class="clear"></section>
</div>
</section>

</div>

<footer>
    <p><strong>Listing Admin</strong> &#169; <?php echo $leopards->isYear('2007'); ?> <a
                href="http://scripts.wyngs.net/scripts/listingadmin/" target="_blank">Tess</a>
        <br><br><a href="https://scripts.robotess.net" target="_blank"
                   title="PHP Scripts: Enthusiast, Siteskin, Codesort, Listing Admin, FanUpdate - ported to PHP 7"><?php echo $laoptions->version; ?>
            for PHP 7</a>
        - support since 2020 by <a href="https://robotess.net" target="_blank" title="PHP Developer">Ekaterina</a>
    </p>
</footer>

</div>

<script src="js.js?v=2" type="text/javascript"></script>
<script src="jquery.js" type="text/javascript"></script>
<script src="jquery-custom.js?v=3" type="text/javascript"></script>

</body>
</html>
