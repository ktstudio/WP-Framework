<?php

/**
 * Prověří, zda zadaný parametr je ve formátu pro ID v databázi
 * Je: Setnutý, není prázdný a je větší než 0
 *
 * @param mixed $value
 * @return boolean
 */
function kt_is_id_format($value) {

    $id = kt_try_get_int($value);

    if (kt_isset_and_not_empty($id) && $id > 0)
        return true;

    return false;
}

/**
 * Kontrola hodnoty, jestli je číselného typu, resp. int a případné přetypování nebo rovnou návrat, jinak null
 * @param number $value
 * @return integer|null
 */
function kt_try_get_int($value) {
    if (kt_isset_and_not_empty($value) && is_numeric($value)) {
        if (is_int($value)) {
            return $value;
        }
        return (int) $value;
    }
    if ($value === "0" || $value === 0) {
        return (int) 0;
    }
    return null;
}

/**
 * Kontrola hodnoty, jestli je číselného typu, resp. float a případné přetypování nebo rovnou návrat, jinak null
 * @param number $value
 * @return float|null
 */
function kt_try_get_float($value) {
    if (kt_isset_and_not_empty($value) && is_numeric($value)) {
        if (is_float($value)) {
            return $value;
        }
        return (float) $value;
    }
    if ($value === "0" || $value === 0) {
        return (float) 0;
    }
    return null;
}

/**
 * Kontrola hodnoty, jestli je číselného typu, resp. double a případné přetypování nebo rovnou návrat, jinak null
 * @param number $value
 * @return double|null
 */
function kt_try_get_double($value) {
    if (kt_isset_and_not_empty($value) && is_numeric($value)) {
        if (is_double($value)) {
            return $value;
        }
        return (double) $value;
    }
    if ($value === "0" || $value === 0) {
        return (double) 0;
    }
    return null;
}

/**
 * Obecné zaokrouhlení podle celých nebo destinných čísel
 * @param number $value
 * @return number
 */
function kt_round($value) {
    if ((kt_isset_and_not_empty($value) && is_numeric($value) || $value === "0")) {
        if (is_int($value)) {
            return round($value, 0, PHP_ROUND_HALF_UP);
        } else {
            return round($value, 2, PHP_ROUND_HALF_UP);
        }
    }
    return $value;
}
