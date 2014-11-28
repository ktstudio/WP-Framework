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
 *  Vypíše všechny soubory nahrané k postu
 *
 *  @author Tomáš Kocifaj
 *  @link http://www.ktstudio.cz
 *
 *  @param int $post_id - ID Post $post->ID
 *  @return echo gallery
 */
function kt_the_print_files($post_id) {

    $args = array(
        'post_type' => 'attachment',
        'post_parent' => $post_id,
        'numberposts' => -1,
        'post_mime_type' => 'application',
        'orderby' => 'post_title',
        'order' => 'ASC'
    );

    $attachments = get_posts($args);
    if ($attachments) {
        echo '<h2>' . __('Přiložené soubory', 'Helios') . '</h2>';
        echo '<ul class="kt_files">';
        foreach ($attachments as $attachment) {
            ?>
            <li>
                <a href="<?php echo wp_get_attachment_url($attachment->ID, false); ?>" target="_blank" title="<?php echo $attachment->post_title ?>">
                    <?php echo $attachment->post_title; ?>
                </a>
            </li>
            <?php
        }
        echo '</ul>';
    }
}

/**
 * Očistí pole od hodnot "." a ".." při scandiru
 *
 * @param array $input
 * @return array
 */
function kt_clear_dir_array($input) {
    foreach ($input as $key => $value) {
        if ($value == '.' || $value == '..') {
            unset($input[$key]);
        }
    }

    return array_values($input);
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
 * @link http://www.KTStudio.cz
 *
 * @global object WP_Post $post - post object: Použitelný pouze v loopě
 * @param int $post_id - Id postu, který se nastaveí
 * @return ojbect WP_post
 */
function kt_setup_post_object($post_id = null) {

    if (isset($post_id)) {
        if (!is_object($post_id))
            return get_post($post_id);
        $post = $post_id;
    } else {
        global $post;
    }

    return $post;
}

/**
 * Získání skriptu do html pro přesměrování zadané url
 * @param string $url
 * @return string
 */
function kt_get_redirect_script($url) {
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    return $string;
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

function kt_get_skype_link($userName) {
    return '<a href="skype:' . $userName . '?call" title="' . __("Zavolat na Skype", KT_DOMAIN) . '">' . __("Skype hovor", KT_DOMAIN) . '</a>';
}

/**
 * Taková malá pomůcka, pro návrat prvního nenulového parametru podle toho jak jdou za s sebou a pokud je to možné
 */
function kt_get_one_or_other($firstValue, $secondValue, $thirdValue = null) {
    if (kt_isset_and_not_empty($firstValue)) {
        return $firstValue;
    }
    if (kt_isset_and_not_empty($secondValue)) {
        return $secondValue;
    }
    return $thirdValue;
}

/**
 * Vrátí excerpt pro aktuální post s případným ožíznutím podle zadaného počtu znaků
 * @param integer $maxLength
 * @return string
 */
function kt_get_excerpt($maxLength, $suffix = "...", WP_Post $post = null) {
    if (kt_not_isset_or_empty($post)) {
        global $post;
    }
    if (kt_isset_and_not_empty($post)) {
        $excerpt = kt_get_post_excerpt($post);
        if (kt_not_isset_or_empty($excerpt)) {
            $excerpt = kt_get_post_content($post);
        }
        return kt_string_crop($excerpt, $maxLength, $suffix);
    }
    return "";
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
 * Velmi jednoduché ověření hodnoty na základě vstupu a KT_SIMPLE_CODE
 * @param string $value
 * @return boolean
 */
function kt_check_simple_code_verification($value) {
    if (kt_isset_and_not_empty($value) && is_string($value)) {
        $value = strtolower(htmlspecialchars(trim($value)));
        return $value === KT_SIMPLE_CODE;
    }
    return false;
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

/**
 * Funkce obslouží ajax dotaz, který pošle název objektu a row id.
 * Tento záznam je pak smazán.
 *
 * Pro účeli custom číselníků pracující na báz KT_Crud - převážně.
 *
 * @author Tomáš Kocifaj
 * @link http://www.KTStudio.cz
 */
add_action('wp_ajax_kt_delete_row_from_table_list', 'kt_delete_row_from_table_lis_callback');

function kt_delete_row_from_table_lis_callback() {
    $deletingObject = $_REQUEST["type"];
    $idRow = $_REQUEST["rowId"];

    $createdObjectToDelete = new $deletingObject($idRow);

    $createdObjectToDelete->deleteRow();

    die();
}

/**
 * Sestavení URL adresy v administraci podle zadaných parametrů
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 *
 * @param string $page
 * @param string $action
 * @param string $paramName
 * @param string|integer $paramValue
 * @return string
 * @throws KT_Not_Set_Argument_Exception
 */
function kt_get_admin_url($page, $action, $paramName = null, $paramValue = null) {
    if (kt_isset_and_not_empty($page) && is_string($page)) {
        if (kt_isset_and_not_empty($action) && is_string($action)) {
            $adminUrl = admin_url("admin.php?page=$page&action=$action");
            if (kt_isset_and_not_empty($paramName) && kt_isset_and_not_empty($paramValue)) {
                $adminUrl .= "&$paramName=$paramValue";
            }
            return $adminUrl;
        }
        throw new KT_Not_Set_Argument_Exception("action");
    }
    throw new KT_Not_Set_Argument_Exception("page");
}
