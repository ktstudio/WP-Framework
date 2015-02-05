<?php

/**
 * Kontrola na isset a ! empty v "jednom" kroku
 * @return bool
 */
function kt_isset_and_not_empty($value) {
    return isset($value) && !empty($value);
}

/**
 * Kontrola na ! isset nebo empty v "jednom" kroku
 * @return bool
 */
function kt_not_isset_or_empty($value) {
    return !isset($value) || empty($value);
}

/**
 * Vypise obsah cehokoliv (pole, objektu, ...)
 * Slouzi jako pomucka pri programovani
 *
 * @author Ing. Martin Dostál
 * @link http://www.dysoft.cz
 *
 * @param mixed $objekt
 * @param string $name
 * @param booelan $return
 * @return string
 */
function kt_pr($objekt, $name = '', $return = false) {
    if (is_string($objekt))
        $objekt .= "\n";
    if ($return)
        return '<pre>' . $name . (print_r($objekt, true)) . '</pre>';
    echo '<pre>' . $name . ' ' . (print_r($objekt, true)) . '</pre>';
}

/**
 * Funkce vrátí post object na základě předaného parametru post_id = null
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 *
 * @global object WP_Post $post - post object: Použitelný pouze v loopě
 * @param int $post_id - Id postu, který se nastaveí
 * @return ojbect WP_post
 */
function kt_setup_post_object($post_id = null) {

    if (kt_is_id_format($post_id)) {
        if (!$post_id instanceof WP_Post) {
            return get_post($post_id);
        }
        $post = $post_id;
    } else {
        global $post;
    }

    return $post;
}

/**
 * Kontrola, zda má aktuální uživatel práva pro "manage_options"
 * @return boolean
 */
function kt_check_current_user_can_manage_options() {
    if (current_user_can("manage_options")) {
        return true;
    } else {
        wp_die(__("Nemáte dostatečná oprávnění k přístupu na tuto stránku.", KT_DOMAIN));
        return false;
    }
}

/**
 * Na základě zadaných parametrů vrátí řetezec pro programové odsazení tabulátorů s případnými novými řádky
 * @param integer $tabsCount
 * @param string $content
 * @param boolean $newLineBefore
 * @param boolean $newLineAfter
 * @return string
 */
function kt_get_tabs_indent($tabsCount, $content = null, $newLineBefore = false, $newLineAfter = false) {
    $result = "";
    if ($newLineBefore == true) {
        $result .= "\n";
    }
    $result .= str_repeat("\t", $tabsCount);
    if (kt_isset_and_not_empty($content)) {
        $result .= $content;
    }
    if ($newLineAfter == true) {
        $result .= "\n";
    }
    return $result;
}

/**
 * Na základě zadaných parametrů vypíše řetezec pro programové odsazení tabulátorů s případnými novými řádky
 * @param integer $tabsCount
 * @param string $content
 * @param boolean $newLineBefore
 * @param boolean $newLineAfter
 * @return string
 */
function kt_the_tabs_indent($tabsCount, $content = null, $newLineBefore = false, $newLineAfter = false) {
    echo kt_get_tabs_indent($tabsCount, $content, $newLineBefore, $newLineAfter);
}

/**
 * Vrátí aktuální URL na základě nastavení APACHE HTTP_HOST a REQUEST_URI
 * @param bool $fullUrl - true i s pametry, false bez
 * @return string
 */
function kt_get_request_url($fullUrl = true) {
    $requestUrl = "http";
    if ($_SERVER["HTTPS"] == "on") {
        $requestUrl .= "s";
    }
    $requestUrl .= "://";
    $serverPort = $_SERVER["SERVER_PORT"];
    $serverName = $_SERVER["SERVER_NAME"];
    $serverUri = ($fullUrl) ? $_SERVER["REQUEST_URI"] : $_SERVER["REDIRECT_URL"];
    if ($serverPort != "80") {
        $requestUrl .= "$serverName:$serverPort$serverUri";
    } else {
        $requestUrl .= "$serverName$serverUri";
    }
    return $requestUrl;
}

/**
 * Vrátí buď zpětnou URL z serverového pole anebo home URL
 * @return string
 */
function kt_get_backlink_url() {
    $refererUrl = $_SERVER['HTTP_REFERER'];
    if (filter_var($refererUrl, FILTER_VALIDATE_URL)) {
        return $refererUrl;
    }
    return get_home_url();
}

/**
 * Na základě zadané adresy vrátí GPS souřadnice pomocí Google API pokud je možné
 * @param string $address
 * @return string|null
 */
function kt_get_google_maps_gps($address) {
    if (kt_isset_and_not_empty($address) && is_string($address)) {
        $address = urlencode(trim($address));
        $googleApiLink = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";
        $googleApiResult = file_get_contents($googleApiLink);
        if ($googleApiResult) {
            $googleApiResultJson = json_decode($googleApiResult);
            if (kt_isset_and_not_empty($googleApiResultJson)) {
                $gpsLatitude = (float) $googleApiResultJson->results[0]->geometry->location->lat;
                $gpsLongtitude = (float) $googleApiResultJson->results[0]->geometry->location->lng;
                if (kt_isset_and_not_empty($gpsLatitude) && kt_isset_and_not_empty($gpsLongtitude)) {
                    $coordinates = $gpsLatitude . ", " . $gpsLongtitude;
                    return $coordinates;
                }
            }
        }
    }
    return null;
}

add_action('wp_ajax_kt_delete_row_from_table_list', 'kt_delete_row_from_table_lis_callback');

/**
 * Funkce obslouží ajax dotaz, který pošle název objektu a row id.
 * Tento záznam je pak smazán.
 *
 * Pro účeli custom číselníků pracující na báz KT_Crud - převážně.
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
function kt_delete_row_from_table_lis_callback() {
    $className = $_REQUEST["type"];
    $itemId = $_REQUEST["rowId"];

    $classModel = new $className($itemId);

    $classModel->deleteRow();

    die();
}

add_action("wp_ajax_kt_edit_crud_list_switch_field", "kt_edit_crud_list_switch_field_callback");

/**
 * Funkce obslouží ajax dotaz, který přepne visibility stav u daného CRUD catalog base modelu
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
function kt_edit_crud_list_switch_field_callback() {
    $className = $_REQUEST["type"];
    $itemId = $_REQUEST["rowId"];
    $columnName = $_REQUEST["columnName"];
    $columnValue = $_REQUEST["value"];

    $classModel = new $className($itemId);
    $classModel->addNewColumnToData($columnName, $columnValue)->saveRow();

    die(1);
}