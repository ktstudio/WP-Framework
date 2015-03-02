<?php

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

add_action("wp_ajax_kt_edit_sorting_crud_list", "kt_edit_sorting_crud_list_callback");

/**
 * Funkce obslouží ajax dotaz, který má provést uložení pořadí itemů po Sortable
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
function kt_edit_sorting_crud_list_callback(){
    $itemCollection = $_REQUEST["data"];
    $className = $_REQUEST["class_name"];
    if(KT::arrayIssetAndNotEmpty($itemCollection)){
        foreach($itemCollection as $index => $itemId){
            $crudClassObject = new $className($itemId);
            if($crudClassObject->isInDatabase()){
                $crudClassObject->setMenuOrder($index)->saveRow();
            }
        }
    }
}