<?php

/**
 * Základní (formulářové) konfigurace pro obecné výpisy z databáze - číselníky apod.
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Catalog_Base_Config {

    const ID = "id";
    const TITLE = "title";
    const DESCRIPTION = "description";
    const CODE = "code";
    const VISIBILITY = "visibility";

    // --- fieldsets ---------------------------

    /**
     * Vrátí základní fieldset pro číselník
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @param string $prefix
     * @param KT_Catalog_Base_Model $item
     * @return \KT_Form_Fieldset
     */
    public static function getCatalogBaseFieldset($name, $prefix, $title = null, KT_Catalog_Base_Model $item = null) {
        $fieldset = new KT_Form_Fieldset($name, $title);
        $fieldset->setPostPrefix($prefix);

        $titleField = $fieldset
                ->addText(self::getPrefixedKey($prefix, self::TITLE), __("Název: ", KT_DOMAIN))
                ->addRule(KT_Field_Validator::REQUIRED, "Název je povinná položka")
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Název musí mít alespoň 3 znaky"), 3)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Název může mít maximálně 30 znaků"), 30);

        $descriptionField = $fieldset
                ->addTextarea(self::getPrefixedKey($prefix, self::DESCRIPTION), __("Popis: ", KT_DOMAIN))
                ->setRows(5)
                ->setTooltip(__("Doplňující údaj informačního charakteru...", KT_DOMAIN));

        $codeField = $fieldset
                ->addText(self::getPrefixedKey($prefix, self::CODE), __("Kód: ", KT_DOMAIN))
                ->addRule(KT_Field_Validator::REQUIRED, "Kód je povinná položka")
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Kód musí mít alespoň 2 znaky"), 2)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Kód může mít maximálně 10 znaků"), 10);

        $visibilityField = $fieldset
                ->addSwitch(self::getPrefixedKey($prefix, self::VISIBILITY), __("Viditelnost: ", KT_DOMAIN))
                ->addRule(KT_Field_Validator::REQUIRED, "Viditelnost je povinná položka");

        if (kt_isset_and_not_empty($item) && $item->isInDatabase()) {
            $fieldset->addHidden(self::getPrefixedKey($prefix, self::ID))
                    ->setValue($item->getId());

            $titleField->setValue($item->getTitle());
            $descriptionField->setValue($item->getDescription());
            $codeField->setValue($item->getCode());
            $visibilityField->setValue($item->getVisibility());
        }

        return $fieldset;
    }

    // --- helpers ---------------------------

    /**
     * Spojí prefix a klíče dohromady pro dynamické sestavování v rámci dědičnosti pro formuláře, resp. field(set)y
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $prefix
     * @param string $key
     * @return string
     */
    public static function getPrefixedKey($prefix, $key) {
        return "{$prefix}_{$key}";
    }

}
