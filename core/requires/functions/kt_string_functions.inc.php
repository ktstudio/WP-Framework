<?php

/**
 * Kontrola, zda první zadaný textový řetezec někde uvnitř sebe obsahuje ten druhý zadaný
 * @param string $string řetězec k prohledání
 * @param string $substring hledaný podřetězec
 * @return boolean true, pokud $substring se nachází v $string, jinak false
 */
function kt_string_contains($string, $substring) {
    $position = strpos($string, $substring);
    if ($position === false) {
        return false;
    }
    return true;
}

/**
 * Kontrola, zda první zadaný textový řetezec obsahuje na svém konci ten druhý zadaný
 * @param string $string
 * @param string $ending
 * @return boolean
 */
function kt_string_ends_with($string, $ending) {
    $length = strlen($ending);
    $string_end = substr($string, strlen($string) - $length);
    return $string_end === $ending;
}

/**
 * Kontrola, zda první zadaný textový řetezec obsahuje na svém začátku ten druhý zadaný
 * @param string $string
 * @param string $starting
 * @return boolean
 */
function kt_string_starts_with($string, $starting) {
    $length = strlen($starting);
    return (substr($string, 0, $length) === $starting);
}

/**
 * Odstranění html ze zadaného textu + převod speciálních znaků
 * @param string $text
 * @return string
 */
function kt_string_clear_html($text) {
    return htmlspecialchars(strip_tags($text));
}

/**
 * Ořízně zadaný řetezec, pokud je delší než požadovaná maximální délka včetně případné přípony
 * @param string $text
 * @param int $maxLength
 * @param string $suffix
 * @return string
 */
function kt_string_crop($text, $maxLength, $suffix = "...") {
    $maxLength = kt_try_get_int($maxLength);
    $currentLength = strlen($text);
    if ($maxLength > 0 && $currentLength > $maxLength) {
        $text = strip_tags($text);
        $text = mb_substr($text, 0, $maxLength);
        $text .= $suffix;
    }
    return $text;
}
