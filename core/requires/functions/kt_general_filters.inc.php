<?php

add_filter("kt_switch_field_value_to_string", "kt_switch_field_value_to_string", 10, 1);

/**
 * Filtrační funkce převede switch field value na ano /ne
 * @author Jan Pokorný
 * @param string $value
 * @return string
 */
function kt_switch_field_value_to_string($value) {
    return KT_Switch_Field::getSwitchConvertedValue($value);
}

add_filter("kt_date_to_fancy_date", "kt_date_to_fancy_date", 10, 1);

/**
 * Filtrační funkce převede libovolné datum na hezké datum
 * @author Jan Pokorný
 * @param string $date
 * @return sting
 */
function kt_date_to_fancy_date($date) {
    if (KT::issetAndNotEmpty($date)) {
        return date("j.n.Y", strtotime($date));
    }
    return KT_EMPTY_SYMBOL;
}

add_filter("kt_datetime_to_fancy_datetime", "kt_datetime_to_fancy_datetime", 10, 1);

/**
 * Filtrační funkce převede libovolné datum a čas na hezké datum a čas
 * @author Martin Hlaváč
 * @param string $datetime
 * @return sting
 */
function kt_datetime_to_fancy_datetime($datetime) {
    if (KT::issetAndNotEmpty($datetime)) {
        return date("j.n.Y H:i", strtotime($datetime));
    }
    return KT_EMPTY_SYMBOL;
}

add_filter("kt_post_id_to_title", "kt_post_id_to_title", 10, 1);

/**
 * Filtrační funkce převede post id na title
 * @author Jan Pokorný
 * @param int $postId
 * @return sting
 */
function kt_post_id_to_title($postId) {
    if (KT::isIdFormat($postId)) {
        $postTitle = get_the_title($postId);
        $link = sprintf('<a href="%s" title="%s">%s</a>', get_edit_post_link($postId), $postTitle, $postTitle);
        return $link;
    }
    return KT_EMPTY_SYMBOL;
}

add_filter("kt_get_page_template_name", "kt_get_page_template_name", 10, 1);

/**
 * Filtrační funkce převede dle "klíče" (relativní cesta, či název souboru v post meta) page templaty na její název
 * @author Martin Hlaváč
 * @param string $key
 * @return string
 */
function kt_get_page_template_name($key)
{
    if (KT::issetAndNotEmpty($key)) {
        $pageTemplates = wp_get_theme()->get_page_templates();
        $pageTemplateName = KT::arrayObjectTryGetValue($pageTemplates, $key);
        if (KT::issetAndNotEmpty($pageTemplateName)) {
            return $pageTemplateName;
        }
    }
    return KT_EMPTY_SYMBOL;
}
