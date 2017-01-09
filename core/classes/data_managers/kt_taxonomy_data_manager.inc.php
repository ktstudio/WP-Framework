<?php

class KT_Taxonomy_Data_Manager extends KT_Data_Manager_Base {

    const FIELD_ID = "id";
    const FIELD_SLUG = "slug";

    private $args = array("hide_empty" => false);
    private $optionValueType = self::FIELD_ID;
    private $withDescriptionSuffix = false;
    private $withParentSuffix = false;

    function __construct($taxonomy = null, $args = null) {
        if (KT::arrayIssetAndNotEmpty($args)) {
            $this->setArgs($args);
        }
        if (KT::issetAndNotEmpty($taxonomy)) {
            $this->setTaxonomy($taxonomy);
        }
    }

    // --- gettery -----------------

    /**
     * Přepis původní funkce getData za účelem inicializace dat
     * 
     * @return array
     */
    public function getData() {

        if (KT::notIssetOrEmpty(parent::getData())) {
            $this->dataInit();
        }
        return parent::getData();
    }

    /**
     * @return string
     */
    private function getTaxonomy() {
        return $this->args["taxonomy"];
    }

    /**
     * @return string
     */
    private function getArgs() {
        return $this->args;
    }

    /**
     * @return string
     */
    public function getOptionValueType() {
        return $this->optionValueType;
    }

    /**
     * @return boolean
     */
    public function getWithDescriptionSuffix() {
        return $this->withDescriptionSuffix;
    }

    /**
     * @param boolean $withDescriptionSuffix
     * @return \KT_Taxonomy_Data_Manager
     */
    public function setWithDescriptionSuffix($withDescriptionSuffix) {
        $this->withDescriptionSuffix = $withDescriptionSuffix;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getWithParentSuffix() {
        return $this->withParentSuffix;
    }

    /**
     * @param boolean $withParentSuffix
     * @return \KT_Taxonomy_Data_Manager
     */
    public function setWithParentSuffix($withParentSuffix) {
        $this->withParentSuffix = $withParentSuffix;
        return $this;
    }

    // --- settery -----------------

    /**
     * Nastaví taxonomy, z které budou vyčítány termy pro výběr
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $taxonomy
     */
    public function setTaxonomy($taxonomy) {
        $this->args["taxonomy"] = $taxonomy;
        return $this;
    }

    /**
     * Nastaví paremtry pro výběr a filtraci příslušných termů
     * get_terms(); @link http://codex.wordpress.org/Function_Reference/get_terms
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $args
     */
    public function setArgs(array $args) {
        $this->args = $args;

        return $this;
    }

    /**
     * Nastaví, jaká hodnota se má vracet jako value fieldu
     * self::FIELD_ID || self::FIELD_SLUG
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
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
     * @link http://www.ktstudio.cz 
     */
    private function dataInit() {
        $results = array();
        $terms = get_terms($this->getArgs());
        if (is_wp_error($terms)) {
            return;
        }

        foreach ($terms as $term) {
            switch ($this->getOptionValueType()) {
                case self::FIELD_SLUG:
                    $key = $term->slug;
                    break;
                case self::FIELD_ID:
                    $key = $term->term_id;
                    break;
            }
            $name = $term->name;
            if ($this->getWithDescriptionSuffix()) {
                $description = $term->description;
                if (KT::issetAndNotEmpty($description)) {
                    $name .= " ($description)";
                }
            }
            if ($this->getWithParentSuffix()) {
                $parentId = $term->parent;
                if ($parentId > 0) {
                    foreach ($terms as $parent) {
                        if ($parent->term_id === $parentId) {
                            $name .= " ({$parent->name})";
                            break;
                        }
                    }
                }
            }
            $results[$key] = $name;
        }

        if (KT::issetAndNotEmpty($results)) {
            $this->setData($results);
        }
    }

}
