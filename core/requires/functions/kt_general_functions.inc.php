<?php

add_action("after_switch_theme", "kt_core_theme_activated");

/**
 * Po aktivaci šablony zkusí zavést obecný SQL skript šablony (ze souboru)
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * 
 * @global \WPDB $wpdb
 */
function kt_core_theme_activated() {
    /* @var $wpdb \WPDB */
    global $wpdb;
    $sqlFileName = path_join(KT_CORE_PATH, "kt_core.sql");
    if (file_exists($sqlFileName)) {
        $sqlScriptContent = file_get_contents($sqlFileName);
        if (KT::issetAndNotEmpty($sqlScriptContent)) {
            $wpdb->query($sqlScriptContent);
        }
    }
}

add_action("wp_ajax_kt_delete_row_from_table_list", "kt_delete_row_from_table_lis_callback");

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
    $classModel->addNewColumnValue($columnName, $columnValue)->saveRow();

    die(1);
}

add_action("wp_ajax_kt_edit_sorting_crud_list", "kt_edit_sorting_crud_list_callback");

/**
 * Funkce obslouží ajax dotaz, který má provést uložení pořadí itemů po Sortable
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
function kt_edit_sorting_crud_list_callback() {
    $itemCollection = $_REQUEST["data"];
    $className = $_REQUEST["class_name"];
    if (KT::arrayIssetAndNotEmpty($itemCollection)) {
        foreach ($itemCollection as $index => $itemId) {
            $crudClassObject = new $className($itemId);
            if ($crudClassObject->isInDatabase()) {
                $crudClassObject->setMenuOrder($index)->saveRow();
            }
        }
    }
}

add_action("wp_before_admin_bar_render", "kt_wp_before_admin_bar_render_callback");

/**
 * Funce zajistí přidání vlastních odkazů do (WP) Admin Baru 
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * 
 * @global WP_Admin_Bar $wp_admin_bar
 */
function kt_wp_before_admin_bar_render_callback() {
    global $wp_admin_bar;
    $wp_admin_bar->add_menu(array(
        "id" => KT_WP_Configurator::THEME_SETTING_PAGE_SLUG,
        "title" => __("Nastavení šablony", KT_DOMAIN),
        "href" => admin_url("themes.php") . "?page=" . KT_WP_Configurator::THEME_SETTING_PAGE_SLUG,
    ));
}
