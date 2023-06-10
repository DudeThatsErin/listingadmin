<?php
declare(strict_types=1);

namespace Robotess;

/**
 * Class Templates
 * @package Robotess
 */
final class Templates
{
    public string $stats = '<p class="details">
<strong>Members:</strong> {members} (<em>{pending} Pending </em>)<br>
<strong>Since:</strong> {since}<br>
<strong>Last Updated:</strong> {updated}<br>
<strong>Newest Members:</strong> {newest}<br>
<strong>Script used:</strong> <a href="https://scripts.robotess.net" target="_blank">Listing Admin [Robotess Fork]</a> (originally by <a href="http://scripts.wyngs.net/scripts/listingadmin/" target="_blank">Tess</a>)
</p>';
    public string $affiliates = '<a href="{url}" target="_blank"><img src="{image}" alt="{subject}" title="{subject}" /></a>';
    public string $wishlist = '';
    public string $members = '<li>{name}<br />
{email} &middot; {url}{fave_field}</li>';
    public string $members_header = '<ol>';
    public string $members_footer = '</ol>';
    public string $updates = '<div class="entry_listingadmin">
<span class="date">{date}</span> {entry}
<p class="tc cat">Filed Under: {categories}</p>
</div>';
}