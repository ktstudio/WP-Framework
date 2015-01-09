<?php

/**
 * Vložení nového klíč-hodnota do pole za zadaný index na základně číselného indexu
 * @param array $input
 * @param int $index
 * @param int|string $newKey
 * @param object $newValue
 * @return array
 * @throws KT_Not_Supported_Exception
 */
function kt_array_insert(array $input, $index, $newKey, $newValue) {
    $index = kt_try_get_int($index);
    $count = count($input);
    if ($index < 0 || $index >= $count) {
        throw new KT_Not_Supported_Exception("Index mimo rozsah: $index");
    }
    $output = array();
    $currentIndex = 0;
    foreach ($input as $key => $value) {
        if ($currentIndex === $index) {
            $output[$newKey] = $newValue;
        }
        $output[$key] = $value;
        $currentIndex ++;
    }
    return $output;
}

/**
 * Vložení nového klíč-hodnota do pole za zadaný index na základě klíče
 * @param array $input
 * @param int|string $index
 * @param int|string $newKey
 * @param object $newValue
 * @return array
 * @throws KT_Duplicate_Exception
 */
function kt_array_insert_before(array $input, $index, $newKey, $newValue) {
    if (!array_key_exists($index, $input)) {
        throw new KT_Duplicate_Exception($key);
    }
    $output = array();
    foreach ($input as $key => $value) {
        if ($key === $index) {
            $output[$newKey] = $newValue;
        }
        $output[$key] = $value;
    }
    return $output;
}

/**
 * Vložení nového klíč-hodnota do pole před zadaný index na základě klíče
 * @param array $input
 * @param int|string $index
 * @param int|string $newKey
 * @param object $newValue
 * @return array
 * @throws KT_Duplicate_Exception
 */
function kt_array_insert_after(array $input, $index, $newKey, $newValue) {
    if (!array_key_exists($index, $input)) {
        throw new KT_Duplicate_Exception($key);
    }
    $output = array();
    foreach ($input as $key => $value) {
        $output[$key] = $value;
        if ($key === $index) {
            $output[$newKey] = $newValue;
        }
    }
    return $output;
}

/**
 * Ze zadaného pole odstraní zadanou hodnotu a vrátí pole hodnot
 * @return array
 */
function kt_array_remove(array $haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if ($value == $needle) {
            unset($haystack[$key]);
        }
    }
    return array_values($haystack);
}

/**
 * Funkce smaže hodnoty z pole na základně předaného pole s klíčem
 *
 * @param array $input - pole, kde se smažou hodnoty
 * @param array $delete_keys - které klíče se mají smazat z $input
 * @return array
 */
function kt_array_keys_remove($input, $delete_keys) {
    foreach ($delete_keys as $value) {
        unset($input[$value]);
    }

    return $input;
}

/**
 * Ze zadaného pole odstraní zadanou hodnotu
 * @return array
 */
function kt_array_remove_by_value(array $haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if ($value == $needle) {
            unset($haystack[$key]);
        }
    }
    return $haystack;
}

/**
 * Ze zadaného pole odstraní zadaný klíč (i s hodnotou)
 * @return array
 */
function kt_array_remove_by_key(array $haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if ($key == $needle) {
            unset($haystack[$key]);
        }
    }
    return $haystack;
}

/**
 * Vrátí, zda má pole více než jednu úroveň
 * @param array $array
 * @return boolean
 */
function kt_array_is_multi(array $array) {
    if (count($array) == count($array, COUNT_RECURSIVE))
        return true;

    return false;
}

/**
 * Kontrola, zda je zadaný parameter přiřezený, typu pole a má jeden nebo více záznamů
 * 
 * @param array|type $array
 * @return boolean
 */
function kt_array_isset_and_not_empty($array) {
    return kt_isset_and_not_empty($array) && is_array($array) && count($array) > 0;
}

/**
 * Vrátí první klíč v poli
 * 
 * @param array
 * @return string|int
 */
function kt_array_get_first_key(array $array) {
    foreach ($array as $key => $value) {
        return $key;
    }
}

/**
 * Vrátí první hodnotu v poli
 * 
 * @param array
 * @return type
 */
function kt_array_get_first_value(array $array) {
    foreach ($array as $key => $value) {
        return $value;
    }
}
