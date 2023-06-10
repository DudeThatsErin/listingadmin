/**
 * Coded under 1.6.1, but compatible with 1.4.2, 1.5,
 * 1.5.1 and 1.6.1 :D
 *
 * @copyright jQuery Copyright (c) John Resig
 * @license   Dual Licenses MIT/GPL Version 2
 * @author    Tess <treibend@gmail.com>
 */

$(document).ready(function () {

    var joinedlistings = $('div.joined');
    var total = joinedlistings.length;

    /**
     * Our listingadmin { } object, with methods o'course!
     */
    var listingadmin = {
        showListings: function (n) {
            if ($('div#category' + n).css('display') == 'none') {
                $('div#category' + n).slideDown("slow");
            } else {
                return;
            }
        }
    };

    /**
     * Wherein the navigation toggles when the plus sign is clicked~
     */
    $('section#sidebar nav menu li span').click(function () {
        var cclass = $(this).parent().attr('class');
        if ($('li#' + cclass).css('display') == 'none') {
            $('li#' + cclass).slideDown(900);
        } else {
            $('li#' + cclass).slideUp(900);
        }
        return false;
    });

    if (tess.getURL() === 'listings.php') {
        /**
         * Add previous owner through listings.php
         */
        $('div.owner span.add').live("click",function () {
            var uno = $(this).parent().parent().attr('id');
            var dos = uno.split('p');
            var tres = dos[1] + 1;

            /**
             * Aaaaa-aaa-aand append after~
             */
            var getp = '<div class="owner" id="p' + tres + '">' +
                '<input name="pnumeric[]" type="hidden" value="' + tres + '">' +
                '<p><label><strong>Name:</strong></label> <input name="pname[]"' +
                ' class="input1" type="text"></p>' +
                '<p><label><strong>URL:</strong></label> <input name="purl[]"' +
                ' class="input6" type="text"> <span class="add">[+]</span></p></div>';
            $(getp).insertAfter("div#" + uno);
            return false;
        });
    }

    /**
     * If a select field with the class of "toggle" is changed, toggle some shit!
     */
    $('select.togglediv').change(function () {
        var toggleids = $(this).attr('id');
        var togglediv = 'div.' + toggleids;
        if ($(togglediv).css('display') == 'none') {
            $(togglediv).slideDown(900);
        } else {
            $(togglediv).slideUp(900);
        }
    });

    /**
     * When the joined.php?g=edit is visited (without the ID specified)
     * the user can click on a category, which brings up a list of joined
     * listings under that category, so. LET'S BRING ON THE TOGGLING-NESS! \o/
     */
    $('select#joined').change(function () {
        var joined = $('option:selected').attr('id');
        var joinedid = tess.joinedID(joined);

        /**
         * First off, let's see if any of the DIVs are actually displaying, yeah?
         */
        for (i = 1; i < (total + 1); i++) {
            if (i != joinedid) {
                if ($('div#category' + i).css('display') == 'block') {
                    $('div#category' + i).slideUp("slow");
                }
            } else {
                listingadmin.showListings(i);
            }
        }
    });

    /**
     * Show full length of subject for joined listings P:
     */
    $('td.th').click(function () {
        var cl = $(this).attr('id');
        var h = $(cl + ' p.bottom').css('height');
        var e = $(cl + ' p.top').css('height');

        if ($('td#' + cl + ' p.bottom').css('display') == 'none') {
            $('td#' + cl + ' p.top').fadeOut(800);
            $('td#' + cl).animate({
                'height': h,
                'paddingBottom': '5px'
            }, {
                duration: 800, complete: function () {
                    $('td#' + cl + ' p.bottom').fadeIn(800);
                }
            });
        } else {
            $('td#' + cl + ' p.bottom').fadeOut(800);
            $('td#' + cl + ' p.top').fadeIn(800);
        }
        return false;
    });

    /**
     * Display hidden fields \o/
     */
    if (tess.getURL() == 'options.php' && tess.getQuery() == 'import') {
        $('select#importcat').change(function () {
            var selectid = $('select#importcat option:selected').attr('class');
            if ($('div#' + selectid).length > 0) {
                if ($('div#' + selectid).css('display') == 'none') {
                    $('div#' + selectid).slideDown('slow');
                } else {
                    $('div#' + selectid).slideUp('slow');
                }
            }
        });
    }

    /**
     * Toggle DIVs if a specified value is selected~
     */
    $('select#crosslist').change(function () {
        if ($('select#crosslist option:selected').val() == 1) {
            $('div.noCrosslistBlock').slideDown("slow");
        } else {
            $('div.noCrosslistBlock').slideUp("slow");
        }
    });

    /**
     * Toggle crossposting in addons.php
     */
    $('select.cps').change(function () {
        var getid = $(this).attr('id');
        if ($('div.' + getid).css('display') == 'none') {
            $('div.' + getid).slideDown("slow");
        } else {
            $('div.' + getid).slideUp("slow");
        }
    });

    /**
     * Get function or something for updates.php~! This is for the toggling
     * of crosslisting to journals, such as Dreamwidth Livejournal and
     * InsaneJournal 8D
     */
    $('#cp input[type=checkbox]').click(function () {
        var div = $(this).attr('id');
        if ($(this).is(':checked')) {
            if ($('div.' + div).css('display') == 'none') {
                $('div.' + div).slideDown("slow");
                $('input#' + div).attr('checked', checked);
            } else {
                $('div.' + div).slideUp("slow");
                $('input#' + div).attr('checked', false);
            }
        } else {
            $('div.' + div).slideUp("slow");
            $('input#' + div).attr('checked', checked);
        }
        return false;
    });

    /**
     * Aaaaa-aaa-and here we're selecting all (or none) for listings on
     * the right. \o/
     */
    $('menu.selectAll li.select a').click(function () {
        var li = $(this).parent().attr('id');
        if (li == 'select_all') {
            $('menu.selectAll li input[type=checkbox]').each(function () {
                $(this).attr('checked', true);
            });
        } else if (li == 'select_none') {
            $('menu.selectAll li input[type=checkbox]').each(function () {
                $(this).attr('checked', false);
            });
        }
        return false;
    });

    /**
     * Codes: toggle categories :D
     */
    $('select#getCategory').click(function () {
        var catvalue = $(this).val();
        $('select.setCategory').each(function () {
            $(this).val(catvalue);
        });
    });

    /**
     * Codes: toggle all donors!
     */
    $('select#getDonor').click(function () {
        var donorvalue = $(this).val();
        $('select.setDonor').each(function () {
            $(this).val(donorvalue);
        });
    });

    /**
     * Codes: aaaaaa-aaa-and toggle all sizes!
     */
    $('select#getSize').click(function () {
        var sizevalue = $(this).val();
        $('select.setSize').each(function () {
            $(this).val(sizevalue);
        });
    });

    function showRss() {
        const maxEntries = 3;

        let result = "";

        $.ajax(RSS_URL, {
            accepts: {
                xml: "application/rss+xml"
            },

            dataType: "xml",

            success: function (data) {
                $(data)
                    .find("item")
                    .slice(0, maxEntries)
                    .each(function () {
                        const el = $(this);

                        const template = `
          <li class="block"><strong>${el.find("title").text()}</strong> [${el.find("pubDate").text()}]<br>
          <p>${el.find("description").text()}</p>
          <a href="${el.find("link").text()}" title="External Link: ${el.find("link").text()}" target="_blank">Read More &#187;</a></li>
        `;

                        result += template;
                    });

                if (result !== '') {
                    $("#lafeeds menu").html(result);
                }
            }
        });
    }

    showRss();
});
