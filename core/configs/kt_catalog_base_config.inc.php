<?php

/**
 * Základní (formulářové) konfigurace pro obecné výpisy z databáze - číselníky apod.
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Catalog_Base_Config {
    // --- fieldsets ---------------------------

    /**
     * Vrátí základní fieldset pro číselník
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @param string $prefix
     * @param KT_Catalog_Model_Base $item
     * @return \KT_Form_Fieldset
     */
    public static function getCatalogBaseFieldset($name, $prefix, $title = null, KT_Catalog_Model_Base $item = null) {
        $fieldset = new KT_Form_Fieldset($name, $title);
        $fieldset->setPostPrefix($prefix);

        $fieldset->addText(KT_Catalog_Model_Base::TITLE_COLUMN, __("Name*: ", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::REQUIRED, "Name is required", "KT_CORE_DOMAIN")
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("The name must have at least 3 characters", "KT_CORE_DOMAIN"), 3)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("The name can have a maximum of 50 characters", "KT_CORE_DOMAIN"), 50);

        $fieldset->addTextarea(KT_Catalog_Model_Base::DESCRIPTION_COLUMN, __("Description: ", "KT_CORE_DOMAIN"))
                ->setRows(5)
                ->setTooltip(__("Additional detail - information character...", "KT_CORE_DOMAIN"));

        $fieldset->addText(KT_Catalog_Model_Base::CODE_COLUMN, __("Code: ", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 30)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("The code can have a maximum of 30 characters", "KT_CORE_DOMAIN"), 30);

        $fieldset->addSwitch(KT_Catalog_Model_Base::VISIBILITY_COLUMN, __("Visibility*: ", "KT_CORE_DOMAIN"))
                ->setDefaultValue(KT_Switch_Field::YES)
                ->addRule(KT_Field_Validator::REQUIRED, __("Visibility is required", "KT_CORE_DOMAIN"));

        if (KT::issetAndNotEmpty($item) && $item->isInDatabase()) {
            $fieldset->addHidden(KT_Catalog_Model_Base::ID_COLUMN)
                    ->setDefaultValue($item->getId());

            $fieldset->setFieldsData($item->getData());
        }

        return $fieldset;
    }

    // --- CRUD Admin List ------------------

    /**
     * Vrátí základní sadu sloupců pro KT_CRUD_Admin_List
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $className
     * @param string $tableName
     * @param string $templateTitle
     * @return \KT_CRUD_Admin_List
     */
    public static function getCatalogCrudList($className, $tableName, $templateTitle = null) {
        $crudList = new KT_CRUD_Admin_List($className, $tableName);
        $crudList->setTemplateTitle($templateTitle);

        $crudList->addColumn(KT_Catalog_Model_Base::TITLE_COLUMN)
                ->setType(KT_CRUD_Admin_Column::EDIT_LINK_TYPE)
                ->setDeletable()
                ->setCssClass("title-column")
                ->setLabel(__("Title", "KT_CORE_DOMAIN"));

        $crudList->addColumn(KT_Catalog_Model_Base::CODE_COLUMN)
                ->setCssClass("code-column")
                ->setLabel(__("Code", "KT_CORE_DOMAIN"));

        $crudList->addColumn(KT_Catalog_Model_Base::VISIBILITY_COLUMN)
                ->setType(KT_CRUD_Admin_Column::SWITCH_BUTTON_TYPE)
                ->setPosition(99)
                ->setCssClass("visibility-column")
                ->setLabel(__("Visibility", "KT_CORE_DOMAIN"));

        // výchozí řazení
        $crudList->getRepository()
                ->addOrder(KT_Catalog_Model_Base::MENU_ORDER_COLUMN, KT_Repository::ORDER_ASC)
                ->addOrder(KT_Catalog_Model_Base::TITLE_COLUMN, KT_Repository::ORDER_ASC);

        return $crudList;
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
