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

add_filter("kt_post_id_to_title", "kt_post_id_to_title", 10, 1);

/**
 * Filtrační funkce převede post id na title
 * @author Jan Pokorný
 * @param int $postId
 * @return sting
 */
function kt_post_id_to_title($postId) {
    if (KT::isIdFormat($postId)) {
        return get_the_title($postId);
    }
    return KT_EMPTY_SYMBOL;
}
