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
