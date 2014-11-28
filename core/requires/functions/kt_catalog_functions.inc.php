<?php

function kt_get_catalog_visibility_switch_field(KT_Catalog_Base_Model $item) {
    $itemId = $item->getId();
    $switchField = new KT_Switch_Field("catalog-visibility-" . $itemId, __("Viditelnost", KT_DOMAIN));
    $switchField->setValue(KT_Switch_Field::convertBooleanToSwitch(kt_get_catalog_visibility($item)))
            ->addAttribute("data-item-type", get_class($item))
            ->addAttribute("data-item-id", $itemId)
            ->addClass("catalog-edit-visibility")
            ->setTooltip(__("Viditelnost v systému pro záznam č.$itemId", KT_DOMAIN));
    return $switchField;
}

function kt_the_catalog_visibility_switch_field(KT_Catalog_Base_Model $item) {
    $switchField = kt_get_catalog_visibility_switch_field($item);
    echo $switchField->getField();
}

add_action("wp_ajax_kt_catalog_edit_visibility", "kt_catalog_edit_visibility");

function kt_catalog_edit_visibility() {
    if (kt_isset_and_not_empty($_REQUEST["item_type"]) && kt_isset_and_not_empty($_REQUEST["item_id"]) && kt_isset_and_not_empty($_REQUEST["item_visibility"])) {
        try {
            $itemType = $_REQUEST["item_type"];
            $itemId = $_REQUEST["item_id"];
            $itemVisibility = $_REQUEST["item_visibility"];
            $item = new $itemType($itemId);
            $item->setVisibility(KT_Switch_Field::convertSwitchToBoolean($itemVisibility));
            $item->saveRow();
            die(true);
        } catch (Exception $ex) {
            log($ex);
            die(false);
        }
    }
    die(false);
}
