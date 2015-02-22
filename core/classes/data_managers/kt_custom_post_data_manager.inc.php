<?php

class KT_Custom_Post_Data_Manager extends KT_Data_Manager_Base {

    private $queryArgs = array();
    private $prefixMetaKey = null;
    private $suffixMetaKey = null;
    private $prefixMetaValue = null;
    private $suffixMetaValue = null;

    // --- gettery -----------

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
     * @return array
     */
    private function getQueryArgs() {
        return $this->queryArgs;
    }

    /**
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * @return string
     */
    private function getPrefixMetaKey() {
        return $this->prefixMetaKey;
    }

    /**
     * @return string
     */
    private function getSuffixMetaKey() {
        return $this->suffixMetaKey;
    }

    /**
     * @return string
     */
    private function getPrefixMetaValue() {
        return $this->prefixMetaValue;
    }

    /**
     * @return string
     */
    private function getSuffixMetaValue() {
        return $this->suffixMetaValue;
    }

    // --- settery -----------

    /**
     * Definice WP_Query $args pro příslušných post_typů, které budou součástí kolekce dat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $queryArgs
     * @return \KT_Post_Type_Select_Field
     */
    public function setQueryArgs(array $queryArgs) {
        $this->queryArgs = $queryArgs;

        return $this;
    }

    /**
     * V popisu u jednotlivých položek dat může být nastavený textový prefix na základě meta_key
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $prefixMetaKey
     * @return \KT_Post_Type_Select_Field
     */
    public function setPrefixMetaKey($prefixMetaKey) {
        $this->prefixMetaKey = $prefixMetaKey;

        return $this;
    }

    /**
     * V popisu u jednotlivých položek dat může být nastavený textový suffix na základě meta_key
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $suffixMetaKey
     * @return \KT_Post_Type_Select_Field
     */
    public function setSuffixMetaKey($suffixMetaKey) {
        $this->suffixMetaKey($suffixMetaKey);

        return $this;
    }

    /**
     * Nastaví vyčtenou hodnotu z postMetas pro další použití
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $prefixMetaValue
     */
    private function setPrefixMetaValue($prefixMetaValue) {
        $this->prefixMetaValue = $prefixMetaValue;
    }

    /**
     * Nastaví vyčtenou hodnotu z postMetas pro další použití
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $suffixMetaValue
     */
    private function setSuffixMetaValue($suffixMetaValue) {
        $this->suffixMetaValue = $suffixMetaValue;
    }

    // --- veřejné funkce -----

    /**
     * Dojde k inicilizaci dat manageru dle nastavení argumentů pro WP_Query
     * Selekt probíhá pomocí get_posts @link http://codex.wordpress.org/Function_Reference/get_posts
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return \KT_Post_Type_Select_Field
     */
    public function dataInit() {

        if (KT::notIssetOrEmpty($this->getQueryArgs())) {
            return;
        }

        $postCollection = get_posts($this->getQueryArgs());

        if (KT::issetAndNotEmpty($postCollection)) {
            foreach ($postCollection as $postItem) {
                $options[$postItem->ID] = $this->getPrefixValue($postItem->ID) . " " . $postItem->post_title . " " . $this->getSuffixValue($postItem->ID);
            }
        }

        if (KT::issetAndNotEmpty($options)) {
            $this->setData($options);
        }

        return $this;
    }

    // --- privátní funkce -----

    /**
     * Vrátí hodnotu meta_value na základě nastaveného meta klíče
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param int $postId
     * @return string
     */
    private function getPrefixValue($postId) {
        if (KT::issetAndNotEmpty($this->getPrefixMetaKey())) {
            if (KT::issetAndNotEmpty($this->getPrefixMetaValue())) {
                return $this->getPrefixMetaValue();
            }

            $prefixMetaValue = get_post_meta($postId, $this->getPrefixMetaKey(), true);
            $this->setPrefixMetaValue($prefixMetaValue);

            return $this->getPrefixMetaValue();
        }

        return "";
    }

    /**
     * Vrátí hodnotu meta_value na základě nastaveného meta klíče
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param int $postId
     * @return string
     */
    private function getSuffixValue($postId) {
        if (KT::issetAndNotEmpty($this->getSuffixMetaKey())) {
            if (KT::issetAndNotEmpty($this->getSuffixMetaValue())) {
                return $this->getSuffixMetaValue();
            }

            $suffixMetaValue = get_post_meta($postId, $this->getSuffixMetaKey(), true);
            $this->setSuffixMetaValue($suffixMetaValue);

            return $this->getSuffixMetaValue();
        }

        return "";
    }

}
