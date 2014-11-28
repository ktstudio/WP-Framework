<?php

/**
 * Vypíše stránkování určené pro WP loopu v bootstrap stylu
 *
 * @global integer $paged
 * @global WP_Query $wp_query
 * @param boolean $previousNext
 * @param string $customClass
 */
function kt_pagination($previousNext = true, $customClass = "pagination-centered") {
    global $paged;
    $paged = kt_try_get_int($paged) ? : 1;
    if (kt_isset_and_not_empty($paged) && $paged > 0) {
        global $wp_query;
        $pages = kt_try_get_int($wp_query->max_num_pages);
        if (kt_isset_and_not_empty($pages) && $pages > 1 && $paged >= $paged) {
            echo kt_the_tabs_indent(0, "<ul class=\"pagination $customClass\">", true);

            if ($previousNext) {
                $firstClass = $paged > 2 ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $firstClass><a href='" . get_pagenum_link(1) . "'>&laquo;</a></li>", true);
                $secondClass = $paged > 1 ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $secondClass><a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a></li>", true);
            }

            for ($i = 1; $i <= $pages; $i ++) {
                $pagenumlink = get_pagenum_link($i);
                $activeClass = ($i == $paged) ? 'class="active"' : "";
                echo kt_the_tabs_indent(1, "<li $activeClass><a href=\"$pagenumlink\">$i</a></li>", true);
            }

            if ($previousNext) {
                $penultimateClass = $paged < $pages ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $penultimateClass><a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a></li>", true);
                $latestClass = $paged < $pages - 1 ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $latestClass><a href='" . get_pagenum_link($pages) . "'>&raquo;</a></li>", true);
            }

            kt_the_tabs_indent(0, "</div>", true, true);
        }
    }
}
