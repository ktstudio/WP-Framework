<?php

class KT_Taxonomy_Data_Manager extends KT_Data_Manager_Base {

    const FIELD_ID = "id";
    const FIELD_SLUG = "slug";

    private $taxonomy = null;
    private $queryArgs = array();
    private $optionValueType = self::FIELD_ID;

    // --- gettery -----------------

    /**
     * Přepis původní funkce getData za účelem inicializace dat
     * 
     * @return array
     */
    public function getData() {

        if (kt_not_isset_or_empty(parent::getData())) {
            $this->dataInit();
        }

        return parent::getData();
    }

    /**
     * @return string
     */
    private function getTaxonomy() {
        return $this->taxonomy;
    }

    /**
     * @return string
     */
    private function getQueryArgs() {
        return $this->queryArgs;
    }

    /**
     * @return string
     */
    public function getOptionValueType() {
        return $this->optionValueType;
    }

    // --- settery -----------------

    /**
     * Nastaví taxonomy, z které budou vyčítány termy pro výběr
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $taxonomy
     */
    public function setTaxonomy($taxonomy) {
        $this->taxonomy = $taxonomy;

        return $this;
    }

    /**
     * Nastaví paremtry pro výběr a filtraci příslušných termů
     * get_terms(); @link http://codex.wordpress.org/Function_Reference/get_terms
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param array $queryArgs
     */
    public function setQueryArgs(array $queryArgs) {
        $this->queryArgs = $queryArgs;

        return $this;
    }

    /**
     * Nastaví, jaká hodnota se má vracet jako value fieldu
     * self::FIELD_ID || self::FIELD_SLUG
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @param type $optionValueType
     * @return \KT_Taxonomy_Field
     */
    public function setOptionValueType($optionValueType) {
        if ($optionValueType == self::FIELD_ID || $optionValueType == self::FIELD_SLUG) {
            $this->optionValueType = $optionValueType;
        }

        return $this;
    }

    // --- privátní funkce ------------

    /**
     * Provede inicilizaci polože pro field na základě nastavení
     * Funkci je nutné volat po nastavení fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     */
    private function dataInit() {
        $taxonomyValues = array();

        $taxonomyItems = get_terms($this->getTaxonomy(), $this->getQueryArgs());

        if (is_wp_error($taxonomyItems)) {
            return;
        }

        foreach ($taxonomyItems as $taxItem) {
            switch ($this->getOptionValueType()) {
                case self::FIELD_SLUG:
                    $key = $taxItem->slug;
                    break;

                case self::FIELD_ID:
                    $key = $taxItem->term_id;
                    break;
            }

            $name = $taxItem->name;
            $taxonomyValues[$key] = $name;
        }

        if (kt_isset_and_not_empty($taxonomyValues)) {
            $this->setData($taxonomyValues);
        }
    }

}
