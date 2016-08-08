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
    return date("j.n.Y", strtotime($date));
}
