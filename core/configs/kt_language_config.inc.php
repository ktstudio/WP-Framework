<?php

/**
 * Základní (formulářové) konfigurace pro jednotlivé jazyky pro další použití v rámci systému
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Language_Config {

    const FORM_PREFIX = "kt-shop-language";
    const DECIMAL_POINT = "decimal-point";
    const THOUSANDS_SEPARATOR = "thousands-separator";

    // --- fieldsets ---------------------------

    /**
     * Vrátí základní fieldset pro detail jazyku na základě číselníku
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_Language_Model $item
     * @return \KT_Form_Fieldset
     */
    public static function getDetailFieldset(KT_Language_Model $item = null) {
        $fieldset = KT_Catalog_Base_Config::getCatalogBaseFieldset(self::FORM_PREFIX, self::FORM_PREFIX, __("Jazyk", KT_DOMAIN), $item);

        $decimalPointField = $fieldset
                ->addText(KT_Catalog_Base_Config::getPrefixedKey(self::FORM_PREFIX, self::DECIMAL_POINT), __("Symbol desetinné čárky: ", KT_DOMAIN))
                ->setTooltip(__("Symbol desetinné čárky pro formátování a výpisy", KT_DOMAIN))
                ->addRule(KT_Field_Validator::REQUIRED, __("Symbol desetinné čárky je povinná položka!", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Symbol desetinné čárky musí mít právě 1 znak!", KT_DOMAIN), 1);

        $thousandsSeparatorField = $fieldset
                ->addText(KT_Catalog_Base_Config::getPrefixedKey(self::FORM_PREFIX, self::THOUSANDS_SEPARATOR), __("Symbol oddělovače tisíců: ", KT_DOMAIN))
                ->setTooltip(__("Symbol oddělovače tisíců pro formátování a výpisy", KT_DOMAIN))
                ->addRule(KT_Field_Validator::REQUIRED, __("Symbol oddělovače tisíců musí mít právě 1 znak!", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Symbol oddělovače tisíců musí mít právě 1 znak!", KT_DOMAIN), 1);

        if (kt_isset_and_not_empty($item) && $item->isInDatabase()) {
            $decimalPointField->setValue($item->getDecimalPoint());
            $thousandsSeparatorField->setValue($item->getThousandsSeparator());
        }

        return $fieldset;
    }

}