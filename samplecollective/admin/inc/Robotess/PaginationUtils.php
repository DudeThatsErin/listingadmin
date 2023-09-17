<?php
declare(strict_types=1);
/**
 * @project          Listing Admin
 * @copyright        2020
 * @license          GPL Version 3; BSD Modified
 * @author           Ekaterina <scripts@robotess.net> http://scripts.robotess.net
 * @file             <PaginationUtils.php>
 * @version          Robotess Fork
 */

namespace Robotess;

/**
 * Class PaginationUtils
 * @package Robotess
 */
final class PaginationUtils
{
    public static function rangedPagination(
        int $currentPage,
        int $totalPagesCount,
        string $linkToPage,
        int $pagesRangeCount = 3,
        string $pageQueryParam = 'p'
    ): void {
        if ($totalPagesCount === 1) {
            return;
        }

        $prev = $currentPage - 1;
        $next = $currentPage + 1;

        $otherPages = [];

        if ($currentPage > 1) {
            $pg = $linkToPage . $pageQueryParam . '=' . $prev;
            echo "<span id=\"prev\"><a href=\"$pg\">&#171; Previous</a></span> ";
            $otherPages[] = 1;
        } else {
            echo '<span id="current">&#171; Previous</span> ';
        }

        $otherPages[] = $totalPagesCount;

        $pagesRangeCount = max(3, $pagesRangeCount);
        $showLeftPagesCount = floor($pagesRangeCount / 2);

        // if range is even, on the right part there will be 1 link less; otherwise it will be equal
        $showRightPagesCount = $pagesRangeCount % 2 === 0 ? $showLeftPagesCount - 1 : $showLeftPagesCount;
        $begin = $currentPage - $showLeftPagesCount;
        $ender = $currentPage + $showRightPagesCount;
        if ($begin <= 0 && $ender > $totalPagesCount) {
            // show all the pages
            $otherPages = array_merge($otherPages, range(1, $totalPagesCount));
        } else {
            if ($begin <= 0) {
                $ender += abs($begin) + 1;
                $begin = 1;
            } elseif ($ender > $totalPagesCount) {
                $begin -= $ender - $totalPagesCount;
                $ender = $totalPagesCount;
            }
            $otherPages = array_merge($otherPages, range(max(1, $begin), min($ender, $totalPagesCount)));
        }

        sort($otherPages);
        $otherPages = array_unique($otherPages);

        $previousPage = null;
        foreach ($otherPages as $i) {
            if ($previousPage !== null && ((int)$previousPage + 1) !== (int)$i) {
                echo ' ... ';
            }

            if ((int)$i === $currentPage) {
                echo "<span id=\"current\">$i</span> ";
            } else {
                $pg = $linkToPage . $pageQueryParam . '=' . $i;
                echo "<span class=\"pagi\"><a href=\"$pg\">$i</a></span> ";
            }

            $previousPage = $i;
        }

        if ($currentPage < $totalPagesCount) {
            $pg = $linkToPage . $pageQueryParam . '=' . $next;
            echo "<span id=\"next\"><a href=\"$pg\">Next &#187;</a></span>";
        } else {
            echo '<span id="current">Next &#187;</span>';
        }
    }
}
