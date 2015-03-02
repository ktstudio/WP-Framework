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
     * @param KT_Catalog_Base_Model $item
     * @return \KT_Form_Fieldset
     */
    public static function getCatalogBaseFieldset($name, $prefix, $title = null, KT_Catalog_Base_Model $item = null) {
        $fieldset = new KT_Form_Fieldset($name, $title);
        $fieldset->setPostPrefix($prefix);

        $fieldset->addText(KT_Catalog_Base_Model::TITLE_COLUMN, __("Název: ", KT_DOMAIN))
                ->addRule(KT_Field_Validator::REQUIRED, "Název je povinná položka")
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Název musí mít alespoň 3 znaky"), 3)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Název může mít maximálně 50 znaků"), 50);

        $fieldset->addTextarea(KT_Catalog_Base_Model::DESCRIPTION_COLUMN, __("Popis: ", KT_DOMAIN))
                ->setRows(5)
                ->setTooltip(__("Doplňující údaj informačního charakteru...", KT_DOMAIN));

        $fieldset->addText(KT_Catalog_Base_Model::CODE_COLUMN, __("Kód: ", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Kód může mít maximálně 30 znaků"), 30);

        $fieldset->addSwitch(KT_Catalog_Base_Model::VISIBILITY_COLUMN, __("Viditelnost: ", KT_DOMAIN))
                ->setValue(KT_Switch_Field::YES)
                ->addRule(KT_Field_Validator::REQUIRED, "Viditelnost je povinná položka");

        if (KT::issetAndNotEmpty($item) && $item->isInDatabase()) {
            $fieldset->addHidden(KT_Catalog_Base_Model::ID_COLUMN)
                    ->setValue($item->getId());

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

        $crudList->addColumn(KT_Catalog_Base_Model::TITLE_COLUMN)
                ->setType(KT_CRUD_Admin_Column::EDIT_LINK_TYPE)
                ->setDeletable()
                ->setLabel(__("Titulek", KT_DOMAIN));

        //$crudList->addColumn(KT_Catalog_Base_Model::DESCRIPTION_COLUMN)
        //        ->setLabel(__("Popisek", KT_DOMAIN));

        $crudList->addColumn(KT_Catalog_Base_Model::CODE_COLUMN)
                ->setLabel(__("Kód", KT_DOMAIN));

        //$crudList->addColumn(KT_Catalog_Base_Model::MENU_ORDER_COLUMN)
        //        ->setLabel(__("Pořadí", KT_DOMAIN));

        $crudList->addColumn(KT_Catalog_Base_Model::VISIBILITY_COLUMN)
                ->setType(KT_CRUD_Admin_Column::SWITCH_BUTTON_TYPE)
                ->setPosition(99)
                ->setLabel(__("Viditelnost", KT_DOMAIN));

        // výchozí řazení
        $crudList->getRepository()
                ->addOrder(KT_Catalog_Base_Model::MENU_ORDER_COLUMN, KT_Repository::ORDER_ASC)
                ->addOrder(KT_Catalog_Base_Model::TITLE_COLUMN, KT_Repository::ORDER_ASC);

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
