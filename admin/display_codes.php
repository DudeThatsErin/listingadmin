<?php
/**
 * @project          Listing Admin
 * @copyright        2007
 * @license          GPL Version 3; BSD Modified
 * @author           Tess <theirrenegadexxx@gmail.com>
 * @contributor      Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <display-codes.php>
 * @version          Robotess Fork
 */

$getTitle = 'Display Codes';
require('pro.inc.php');
require('vars.inc.php');
require('header.php');

echo "<h2>{$getTitle}</h2>\n";

if (isset($_GET['g']) && $_GET['g'] == 'collective') {
    if (isset($_GET['s']) && $_GET['s'] == 'codes') {
        ?>
        <h3>Codes</h3>
        <p>Codes are very much like CodeSort - simply upload them to the correct listing
            (collective in this case) and grab the snippet. You can set the order
            (<samp>ASC</samp> or <samp>DESC</samp>) on the
            <a href="addons.php?g=codes">&#187; Addons: Codes</a> page.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$donor_link</td>
                <td class="d">'y' if you'd like to link the donor, 'n' if you would not.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$pretty_urls</td>
                <td class="d">Set to 'y' if you'd like to utilise pretty URLs (for example,
                    "fanlisting/codes/"), and 'n' to turn them off.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$set_query</td>
                <td class="d"><samp>$set_query</samp> coincides with <samp>$pretty_urls</samp>,
                    and can set to to a custom URL, e.g. <samp>fanlisting/buttons/</samp>. This is
                    optional, even with the use of <samp>$pretty_urls</samp>.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_all</td>
                <td class="d">'y' if you'd like to show all codes (under each code size), or
                    'n' if you would not.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_number</td>
                <td class="d">'y' if you'd like to show the number of codes next to each
                    size or category, or 'n' if you would not; this will only display if you're
                    displaying a list of your sizes/categories, not when you're displaying all
                    codes.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$sort_by</td>
                <td class="d">Can be set to 'id' -- for the code ID automatically assigned to
                    each code when you add it to the database -- or 'name', an optional field for
                    each code.
                </td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 0;<br>
            $donor_link = 'y';<br>
            $pretty_urls = 'y';<br>
            $set_query = 'fanlisting/buttons/';<br>
            $show_all = 'n';<br>
            $show_number = 'y';<br>
            $sort_by = 'id';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-codes.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h3 id="donate">Codes: Donate</h3>
        <p>You can now display a form to donate codes! The only variable required
            is the fanlisting ID -- all other options can be found and edited on the
            <a href="addons.php?g=codes">&#187; Addons: Codes</a> page.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 0;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-codes-form.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'contact') {
        ?>
        <h3>Affiliate/Contact Form</h3>
        <p>The affiliates and contact form feature features your affiliates list
            and a affiliation and contact form in one. Both the contact and affiliate
            portion can be optional.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fClose</td>
                <td class="d">'y' to turn on the form, and 'n' for turning off the form.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fWhich</td>
                <td class="d">With this variable, you can choose which section you'd like to
                    display: <samp>form</samp> (for showing the form), <samp>list</samp> (for
                    showing the affiliates), and <samp>both</samp> (for showing both). Example:
                    <samp>$fWhch = 'form';</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$turnAff</td>
                <td class="d">Set to 1 to turn on the affiliates portion of the form, and 0
                    to turn it off. E.g.: <samp>$turnAff = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$turnCon</td>
                <td class="d">Set to 1 to turn on the contact form, and 0 to turn it off. For
                    example: <samp>$turnCon = 1;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 0;<br>
            $fClose = 'n';<br>
            $fWhich = 'form';<br>
            $turnAff = 0;<br>
            $turnCon = 1;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-form.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'joined') {
        ?>
        <h3>Joined</h3>
        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$list</td>
                <td class="d">Set to 'y' to display subcategories with the parent categories in
                    the joined list, and 'n' to not display them.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_lister</td>
                <td class="d">You can set this to 'y' for providing an alternate way to display
                    your joined listings via list format (<samp>&#60;ol&#62;</samp> or
                    <samp>&#60;menu&#62;</samp>); to turn this feature off, set it to 'n'.
                    Example: <samp>$show_lister = 'n';</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_search</td>
                <td class="d">Set this to 'y' to turn on the search form, which visitors (or
                    you yourself) can search for joined listings; set to 'n' to turn it off.
                    Example: <samp>$show_search = 'y';</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $list = 'n';<br>
            $show_lister = 'y';<br>
            $show_search = 'y';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-joined.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'listings') {
        ?>
        <h3>Listings</h3>
        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$list</td>
                <td class="d">Set to 'y' to display subcategories with the parent categories in
                    the category list of your listings, and 'n' to not display them.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$status</td>
                <td class="d">This <em>must</em> be set in order to display listings. Set this
                    to <samp>current</samp>, <samp>upcoming</samp> or <samp>pending</samp>. For
                    example: <samp>$status = 'upcoming';</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $list = 'n';<br>
            $status = &#39;current&#39;;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-owned.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'kim') {
        ?>
        <h3>KIM</h3>
        <p>A <abbr title="Keep In Mind">KIM</abbr> list stands for 'Keep In Mind', and
            is used for potential new owners of your listings. It's an optional feature and
            has only just recently become common in fanlisting collectives.</p>
        <p>As a KIM list is more or less like a fanlisting, there are several display
            codes for several aspects of the list. Statistics include last update date and
            member count (approved and pending). There's also join, reset password and update
            forms, and a members list.</p>

        <h4>Statistics</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-kim-stats.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Join Form</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-kim-join.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Update Form</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-kim-update.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Reset Password Form</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-kim-reset.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Members List</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-kim-members.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'random') {
        ?>
        <h3>Random Listings</h3>
        <p>You can display your newest listing/joined listing and a random
            listing/joined listing via this snippet.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$show_joined</td>
                <td class="d">Set to 'y' if you'd like your newest/random joined listing
                    displayed; set to 'n' if you'd like to be turned off. Please note that is this
                    option is turned off, so will your random joined listings.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_joined_number</td>
                <td class="d">Set this to the number of newest and random joined listings you'd
                    like to display.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_joined_rotate</td>
                <td class="d">Set to 'y' if you'd like random joined listing(s) to be displayed, set to 'n' if you'd like newest listing(s) to be displayed.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_owned</td>
                <td class="d">Set to 'y' if you'd like your newest owned listing displayed; set
                    to 'n' if you'd like to be turned off. Please note that is this option is
                    turned off, so will your random owned listings.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_owned_number</td>
                <td class="d">Set this to the number of newest and random owned listings you'd
                    like to display.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_owned_rotate</td>
                <td class="d">Set to 'y' if you'd like random owned listing(s) to be displayed, set to 'n' if you'd like newest listing(s) to be displayed.</td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require(&#34;jac.inc.php&#34;);<br>
            # -----------------------------------------------------------<br>
            $show_joined = 'n';<br>
            $show_joined_number = 1;<br>
            $show_joined_rotate = 'y';<br>
            $show_owned = 'y';<br>
            $show_owned_number = 1;<br>
            $show_owned_rotate = 'y';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-random.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'stats') {
        ?>
        <h3>Statistics</h3>
        <p>Collective statistics work int the same way listings templates do. Given
            the template (which you can edit on the <a href="templates.php">Templates</a>
            page), it lists the stats. All you need is to include the following code:</p>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-c-stats.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'stats_real') {
        ?>
        <h3>Statistics: Real Statistics</h3>
        <p>Unlike
            <a href="display_codes.php?g=collective&#38;s=stats">&#187; Collective Statistics</a>,
            real statistics return the direct variables of collective statistics.</p>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-real-stats.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Example</h4>
        <code>
            &#60;p class=&#34;collectiveStats&#34;&#62;<br>
            I own &#60;strong&#62;&#60;?php echo $current; ?&#62;&#60;/strong&#62; current listings,
            with &#60;strong&#62;&#60;?php echo $upcoming; ?&#62;&#60;/strong&#62;, making that
            &#60;strong&#62;&#60;?php echo $listings; ?&#62;&#60;/strong&#62; listings in total! :D<br>
            &#60;/p&#62;
        </code>

        <h4>Variables</h4>
        <table class="statsTemplates" width="100%">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$affiliates<br>$affiliates_collective</td>
                <td class="d">Total number of affiliates, including listing affiliates, as well
                    as collective affiliates. $affiliates_collective is your collective affiliates
                    <em>alone</em>.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$categories<br>$categories_listed</td>
                <td class="d">The total number of categories in the database; $categories_listed
                    returns the amount of categories with listings under them.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$current<br>$upcoming<br>$pending<br>$listings</td>
                <td class="d">The current, upcoming and pending numbers for your listings. If
                    you want your current listings listed, use $current and so on. $listings is
                    all listings in total.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$joined</td>
                <td class="d">Number of joined listings</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$newest</td>
                <td class="d">Newest returns a link of the newest listing</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$members<br>$approved<br>$unapproved</td>
                <td class="d">Total member counts.</td>
            </tr>
            </tbody>
        </table>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'updates') {
        ?>
        <h3>Updates</h3>
        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">Set to the listing ID you'd like the updates from; collective is
                    always <samp>0</samp>. For example: <samp>$fKey = 1;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$pagination</td>
                <td class="d">The number of entries you'd like displayed per page.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_all</td>
                <td class="d"><samp>$show_all</samp> is only available when displaying entries
                    for your collective. To display all entries -- even entries that aren't listed
                    under the collective -- set to 'y'; to turn this feature off, set it to 'n'.
                    E.g.: <samp>$show_all = 'y';</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 0;<br>
            $pagination = 3;<br>
            $show_all = 'y';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-updates.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'wishlist') {
        ?>
        <h3>Wishlist</h3>
        <p>There are four types of wishlist categories. The first is the 'granted'
            wishes, which coincide with your <a href="listings.php">Listings</a> (go to
            Listings > Manage to the appropriate listing and choose 'yes' for
            <samp>Granted Wish</samp>); the second is the 'top' wishes (usually 'Top 10'
            or 'Top 3', for instance); the third is the "rest" of the list; and the fourth
            is custom.</p>
        <p>There's no need to turn "off" any section, as they only appear if you
            include them. There are four statuses: <samp>granted</samp>, <samp>top</samp>,
            <samp>list</samp>, and <samp>custom</samp>. These can be included on the same
            page.</p>
        <p>To edit the templates of each, go to <a href="templates.php">Templates</a>.</p>

        <h4>Granted</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $status = &#39;granted&#39;;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-wishlist.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Top</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $status = &#39;top&#39;;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-wishlist.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>List</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $status = &#39;list&#39;;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-wishlist.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Custom</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $status = &#39;custom&#39;;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-wishlist.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    }
} /**
 * Get listings portion of the codes
 */
elseif (isset($_GET['g']) && $_GET['g'] == 'listings') {
    if (isset($_GET['s']) && $_GET['s'] == 'codes') {
        ?>
        <h3>Codes</h3>
        <p>Codes are very much like CodeSort - simply upload them to the correct listing
            (collective in this case) and grab the snippet. You can set the order
            (<samp>ASC</samp> or
            <samp>DESC</samp>) on the
            <a href="addons.php?g=codes">&#187; Addons: Codes</a> page.
        </p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$donor_link</td>
                <td class="d">'y' if you'd like to link the donor, 'n' if you would not.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$pretty_urls</td>
                <td class="d">Set to 'y' if you'd like to utilise pretty URLs (for example,
                    "fanlisting/codes/"), and 'n' to turn them off.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$set_query</td>
                <td class="d"><samp>$set_query</samp> coincides with <samp>$pretty_urls</samp>,
                    and can set to to a custom URL, e.g. <samp>fanlisting/buttons/</samp>. This is
                    optional, even with the use of <samp>$pretty_urls</samp>.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_all</td>
                <td class="d">'y' if you'd like to show all codes (under each code size), or
                    'n' if you would not.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$show_number</td>
                <td class="d">'y' if you'd like to show the number of codes next to each
                    size or category, or 'n' if you would not; this will only display if you're
                    displaying a list of your sizes/categories, not when you're displaying all
                    codes.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$sort_by</td>
                <td class="d">Can be set to 'id' -- for the code ID automatically assigned to
                    each code when you add it to the database -- or 'name', an optional field for
                    each code.
                </td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 4;<br>
            $donor_link = 'y';<br>
            $pretty_urls = 'y';<br>
            $set_query = 'fanlisting/buttons/';<br>
            $show_all = 'n';<br>
            $show_number = 'y';<br>
            $sort_by = 'id';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-codes.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h3 id="donate">Codes: Donate</h3>
        <p>You can now display a form to donate codes! The only variable required
            is the fanlisting ID -- all other options can be found and edited on the
            <a href="addons.php?g=codes">&#187; Addons: Codes</a> page.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 34;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-codes-form.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'contact') {
        ?>
        <h3>Affiliate/Contact Form</h3>
        <p>The affiliates and contact form feature features your affiliates list
            and a affiliation and contact form in one. Both the contact and affiliate
            portion can be optional.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fClose</td>
                <td class="d">'y' to turn on the form, and 'n' for turning off the form.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fWhich</td>
                <td class="d">With this variable, you can choose which section you'd like to
                    display: <samp>form</samp> (for showing the form), <samp>list</samp> (for
                    showing the affiliates), and <samp>both</samp> (for showing both). Example:
                    <samp>$fWhch = 'form';</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$turnAff</td>
                <td class="d">Set to 1 to turn on the affiliates portion of the form, and 0
                    to turn it off. E.g.: <samp>$turnAff = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$turnCon</td>
                <td class="d">Set to 1 to turn on the contact form, and 0 to turn it off. For
                    example: <samp>$turnCon = 1;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 9;<br>
            $fClose = 'n';<br>
            $fWhich = 'both';<br>
            $turnAff = 0;<br>
            $turnCon = 1;<br>
            $contact_form = 'contact.php';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-form.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'delete') {
        ?>
        <h3>Delete</h3>
        <p>A member can delete themselves through this form by supplying their
            e-mail and password. Easy-peasy, and not something you'd have to do
            every time a member may request this.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 1;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-delete.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'join') {
        ?>
        <h3>Join Form</h3>
        <p>Your join form is most obviously for members to join a listing of yours.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fave_field</td>
                <td class="d">This is your fave field, which is entirely optional, and can also
                    be set via your settings under <a href="listings.php">Listings &#62; Manage</a>.
                    To set it, simply type in <samp>$fave_field = "fields";</samp>; make sure each
                    field has a <samp>|</samp> symbol between each field. If there is only one
                    field, the symbol is not needed.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 18;<br>
            $fave_field = "Favourite Character(s)|Favourite Episode(s)|Favourite Couple(s)";<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-join.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Setting &#60;select&#62; Fields</h4>
        <p><em>Are options available for favourite fields?</em>, you ask? Certainly!
            They're a little tricky to understand, but they can be set easily.
            <strong>Please note:</strong> this method is currently the only way to display
            select fields in the join form; adding favourite fields via the database is the
            more recommended method, but does not currently support select fields.</p>

        <h5>To Set One Field</h5>
        <p>Make sure, for each option, that they are separated with a comma (,)
            <em>if</em> there is a option before and one after. If you come to the end,
            <ins>don't</ins>
            insert a comma.
        </p>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 69;<br>
            $fave_field = "Favourite Character(s)";<br>
            $fave_field_e = array(<br>
            &#160;"Favourite Character(s)" => array('All', 'Draco', 'Ginny', 'Harry', 'Hermione', 'Neville', 'Ron')<br>
            );<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-join.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h5>To Set Several Fields</h5>
        <p>Make sure, for each option, that they are separated with a comma (,)
            <em>if</em> there is a option before and one after. If you come to the end,
            <ins>don't</ins>
            insert a comma.
        </p>
        <p><strong>For each field</strong>, you need a comma after each field's array.
            Notice how there's a comma after each array for this code, but not for the code
            above. Once the last field has been set, do
            <ins>not</ins>
            set a comma.
        </p>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = NUM;<br>
            $fave_field = "Favourite Character(s)|Favourite Couple(s)";<br>
            $fave_field_e = array(<br>
            &#160;"Favourite Character(s)" => array('All', 'Draco', 'Ginny', 'Harry', 'Hermione', 'Neville', 'Ron'),<br>
            &#160;"Favourite Couple(s)" => array('All', 'Draco/Ginny', 'Draco/Harry', 'Draco/Hermione',
            'Ginny/Harry',<br>
            'Ginny/Neville', 'Harry/Hermione', 'Harry/Ron', 'Hermione/Ron')<br>
            );<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-join.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'lyrics') {
        ?>
        <h3>Lyrics</h3>
        <p>Lyrics are for your albums and songs for each listing. This feature is currently
            only available to listings (and not the collective).</p>
        <p><samp>$album_id</samp> is set if you'd like to the album ID you'd like to pull
            lyrics from&#8212;of course, this isn't required, and can be unset if you'd like albums
            <em>and</em> lyrics pulled from the listing you add them to.</p>
        <p class="noteButton">Be sure to replace <samp>NUM</samp> with the proper listing ID.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$album_id</td>
                <td class="d">The album ID you'd like to display. This is optional, and if you
                    decide to not include an album ID, all albums and lyrics under the fanlisting
                    ID you provided will be returned. Example: <samp>$album_id = 3;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 3;<br>
            $album_id = '';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-lyrics.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'members') {
        ?>
        <h3>Members List</h3>
        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fave_field</td>
                <td class="d">This is your fave field, which is entirely optional, and can also
                    be set via your settings under <a href="listings.php">Listings &#62; Manage</a>.
                    To set it, simply type in <samp>$fave_field = "fields";</samp>; make sure each
                    field has a <samp>|</samp> symbol between each field. If there is only one
                    field, the symbol is not needed.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 7;<br>
            $fave_field = "Favourite Character(s)|Favourite Episode(s)|Favourite Couple(s)";<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-members.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'quotes') {
        ?>
        <h3>Quotes</h3>
        <p>The quotes feature is an optional feature that provides you with quotes
            (randomly or otherwise). It's set by the listing ID (required), the number of
            quotes you want displayed and the option of being random. To configure your
            quotes template for each listing, visit the <a href="listings.php">Listings</a>
            page.</p>
        <p><strong>How it works:</strong> Quotes work like any other quote script: provided
            with a quote number (if there is not, it'll use the default setting (2)), it'll
            provide you with quotes (randomly or not). It's pulled from the database and listed
            by a template you have provided (or it will use a default one).</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$quote_number</td>
                <td class="d">The number of quotes you'd like displayed per page.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$quote_random</td>
                <td class="d">Setting this variable to 1 would rotate -- and by proxy,
                    randomise -- the number of quotes returned; setting this to 0 would turn this
                    featue off. Please note that if this is turned on, the quotes will not be
                    paginated.
                </td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 9;<br>
            $quote_number = 2;<br>
            $quote_random = 1;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-quotes.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'reset') {
        ?>
        <h3>Reset Password</h3>
        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 29;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-reset.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'stats') {
        ?>
        <h3>Statistics</h3>
        <p>Statistics can be configured by included one file. Make sure <samp>$fKey</samp>
            is set to the listing number. To set the template of each listing, visit the
            <a href="listings.php">Listings</a> page.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 18;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-stats.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'stats_real') {
        ?>
        <h3>Statistics: Real Statistics</h3>
        <p>Unlike
            <a href="display_codes.php?g=listing&#38;s=stats">&#187; Statistics</a>,
            real statistics return the direct variables of your listing statistics.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$use_template</td>
                <td class="d">Set to 'n' if you'd like to return variables, instead of
                    returning an HTML template of your statistics.
                </td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 18;<br>
            $use_template = 'n';<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-stats.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>

        <h4>Example</h4>
        <code>
            &#60;p class=&#34;stats&#34;&#62;<br>
            There is currently &#60;strong&#62;&#60;?php echo $la_approved; ?&#62;&#60;/strong&#62;
            approved members,
            with &#60;strong&#62;&#60;?php echo $la_pending; ?&#62;&#60;/strong&#62; pending
            admission to the fanlisting. This fanlisting was opened on
            &#60;em&#62;&#60;?php echo $la_fl_opened; ?&#62;&#60;/em&#62;, and was last
            updated on &#60;ins&#62;&#60;?php echo $la_fl_updated; ?&#62;&#60;/ins&#62;!<br>
            &#60;/p&#62;
        </code>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$la_aff_count</td>
                <td class="d">Total number of affiliates.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$la_newest</td>
                <td class="d">The newest members that have joined; returns a comma-separated
                    list of fans.
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$la_approved</td>
                <td class="d">The number of approved members.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$la_pending</td>
                <td class="d">The number of pending members.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$la_fl_opened</td>
                <td class="d">Returns the date the fanlisting opened.</td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$la_fl_updated</td>
                <td class="d">Returns the date the fanlisting was last updated.</td>
            </tr>
            </tbody>
        </table>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'update') {
        ?>
        <h3>Update Form</h3>
        <p>Your update form is most obviously for members to update their information on
            a listing of yours.</p>

        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 22;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-update.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    } elseif (isset($_GET['s']) && $_GET['s'] == 'updates') {
        ?>
        <h3>Updates</h3>
        <h4>Variables</h4>
        <table class="statsTemplates">
            <thead>
            <tr>
                <th class="l">Template</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="t">$fKey</td>
                <td class="d">The fanlisting ID; for collective, use 0, e.g.:
                    <samp>$fKey = 0;</samp></td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td class="t">$pagination</td>
                <td class="d">The number of entries you'd like displayed per page.</td>
            </tr>
            </tbody>
        </table>

        <h4>Snippet</h4>
        <code>
            &#60;?php<br>
            # -----------------------------------------------------------<br>
            require("jac.inc.php");<br>
            # -----------------------------------------------------------<br>
            $fKey = 20;<br>
            $pagination = 3;<br>
            # -----------------------------------------------------------<br>
            require(STPATH . &#34;show-updates.php&#34;);<br>
            # -----------------------------------------------------------<br>
            ?&#62;
        </code>
        <?php
    }
} /**
 * Index
 */
else {
    ?>
    <p>Below are two menus (and their submenus): <strong>Collective</strong> for
        collective display codes and <strong>Listings</strong> for the Listings
        proportion. These sections have been seperated for better sufficiency.</p>

    <div class="floatLeft floater">
        <h3>Collective</h3>
        <menu id="menu">
            <li><a href="display_codes.php?g=collective&#38;s=contact">&#187; Affiliates/Contact Form</a></li>
            <?php
            if ($seahorses->getOption('codes_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=collective&#38;s=codes">&#187; Codes</a></li>
                <li><a href="display_codes.php?g=collective&#38;s=codes#donate">&#187; Codes: Donate</a></li>
                <?php
            }
            ?>
            <li><a href="display_codes.php?g=collective&#38;s=joined">&#187; Joined</a></li>
            <li><a href="display_codes.php?g=collective&#38;s=listings">&#187; Listings</a></li>
            <?php
            if ($seahorses->getOption('kim_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=collective&#38;s=kim">&#187; KIM</a></li>
                <?php
            }
            ?>
            <li><a href="display_codes.php?g=collective&#38;s=random">&#187; Random Listings</a></li>
            <li><a href="display_codes.php?g=collective&#38;s=stats">&#187; Statistics</a></li>
            <li><a href="display_codes.php?g=collective&#38;s=stats_real">&#187; Statistics: Real</a></li>
            <?php
            if ($seahorses->getOption('updates_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=collective&#38;s=updates">&#187; Updates</a></li>
                <?php
            }
            ?>
            <li><a href="display_codes.php?g=collective&#38;s=wishlist">&#187; Wishlist</a></li>
        </menu>
    </div>

    <div class="floatLeft floater">
        <h3>Listing</h3>
        <menu class="menu">
            <li><a href="display_codes.php?g=listings&#38;s=contact">&#187; Affiliates/Contact Form</a></li>
            <?php
            if ($seahorses->getOption('codes_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=listings&#38;s=codes">&#187; Codes</a></li>
                <li><a href="display_codes.php?g=listings&#38;s=codes#donate">&#187; Codes: Donate</a></li>
                <?php
            }
            ?>
            <li><a href="display_codes.php?g=listings&#38;s=delete">&#187; Delete</a></li>
            <li><a href="display_codes.php?g=listings&#38;s=join">&#187; Join Form</a></li>
            <?php
            if ($seahorses->getOption('lyrics_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=listings&#38;s=lyrics">&#187; Lyrics</a></li>
                <?php
            }
            ?>
            <li><a href="display_codes.php?g=listings&#38;s=members">&#187; Members List</a></li>
            <?php
            if ($seahorses->getOption('quotes_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=listings&#38;s=quotes">&#187; Quotes</a></li>
                <?php
            }
            ?>
            <li><a href="display_codes.php?g=listings&#38;s=reset">&#187; Reset Password Form</a></li>
            <li><a href="display_codes.php?g=listings&#38;s=stats">&#187; Statistics</a></li>
            <li><a href="display_codes.php?g=listings&#38;s=stats_real">&#187; Statistics: Real</a></li>
            <li><a href="display_codes.php?g=listings&#38;s=update">&#187; Update Form</a></li>
            <?php
            if ($seahorses->getOption('updates_opt') == 'y') {
                ?>
                <li><a href="display_codes.php?g=listings&#38;s=updates">&#187; Updates</a></li>
                <?php
            }
            ?>
        </menu>
    </div>
    <?php
}

require('footer.php');
