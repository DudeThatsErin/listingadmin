<div id="feeds">
    <div id="lafeeds">
        <h4>
            <ins><?= $laoptions->version ?></ins>
            Feed
        </h4>
        <script>
            const feedUrl = `https://blog.dudethatserin.com/category/la/feed`;
        showRss(`${feedUrl}?date=<?=date('Y-m-d');?>`);
        console.log(feedUrl);
    </script>
        <div id="rss-feed">
            Nothing here yet. Please check <a href="https://blog.dudethatserin.com/category/la/feed" target="_blank">feed</a> manually.
        </div>
    </div>

</div>
</section>

</div>

<footer>
    <p><strong>Listing Admin</strong> &#169; <?php echo $leopards->isYear('2007'); ?> <a
                href="http://github.com/DudeThatsErin/listingadmin" target="_blank">Tess, Ekaterina, &amp; Erin</a>
        <br><br><a href="https://scripts.robotess.net/projects/listing-admin/" target="_blank"
                   title="PHP Scripts: Enthusiast, Codesort, Listing Admin, FanUpdate - ported to PHP 8">Listing Admin [Robotess Fork] v1.0.5
            for PHP 7</a>
        - support from 2020 through 2022 by <a href="https://robotess.net" target="_blank" title="PHP Developer">Ekaterina</a><br><br>
        <a href="https://github.com/DudeThatsErin/listingadmin" target="_blank"><?php echo $laoptions->version; ?> for PHP8</a> - support from 2023 by <a href="https://dudethatserin.com/" target="_blank" title="Full-Stack Developer">Erin</a>
    </p>
</footer>

</div>



</body>
</html>
