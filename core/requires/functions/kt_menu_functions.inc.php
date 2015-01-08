<?php

/**
 * Vrátí uživatelský titulek menu podle jeho lokace nebo zadaný vlastní - výchozí
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * 
 * @param string $location
 * @param string $defaultTitle
 * @return string
 */
function kt_get_custom_menu_name($location, $defaultTitle = null) {
    $locations = get_nav_menu_locations();
    $menuLocation = $locations[$location];
    if (kt_isset_and_not_empty($menuLocation)) {
        $menuObject = wp_get_nav_menu_object($menuLocation);
        if (kt_isset_and_not_empty($menuObject)) {
            $menuName = $menuObject->name;
            if (kt_isset_and_not_empty($menuName)) {
                return esc_html($menuName);
            }
        }
    }
    return $defaultTitle;
}

/**
 * Vypíše požadované menu bez "obalujícího" divu
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * 
 * @param string $themeLocation
 * @param int $depth
 */
function kt_the_wp_nav_menu($themeLocation, $depth = 0) {
    wp_nav_menu(array(
        "theme_location" => $themeLocation,
        "container" => false,
        "depth" => $depth,
        "items_wrap" => '%3$s',
        "fallback_cb" => false
    ));
}
