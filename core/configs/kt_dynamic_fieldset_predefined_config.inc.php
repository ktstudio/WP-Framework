<?php

/**
 * Třída pro předefinované dynamické fieldy
 * @author Jan Pokorný
 */
class KT_Dynamic_Fieldset_Predefined_Config implements KT_Dynamic_Configable {

    public static function getAllDynamicFieldsets() {
        return [
            self::KEY_VALUE_FIELDSET => self::getKeyValueFieldset()
        ];
    }

    const KEY_VALUE_FIELDSET = "kt-filedset-key-value";
    const KEY_VALUE_KEY = "kt-filedset-key-key";
    const KEY_VALUE_VALUE = "kt-filedset-key-value-value";

    public static function getKeyValueFieldset() {
        $fieldset = new KT_Form_Fieldset(self::KEY_VALUE_FIELDSET, __("Parameters", "KT_CORE_DOMAIN"));
        $fieldset->addText(self::KEY_VALUE_KEY, __("Key:", "KT_CORE_DOMAIN"));
        $fieldset->addText(self::KEY_VALUE_VALUE, __("Value:", "KT_CORE_DOMAIN"));
        return $fieldset;
    }

}
