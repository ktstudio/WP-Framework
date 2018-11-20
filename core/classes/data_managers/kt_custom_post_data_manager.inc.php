<?php

class KT_Custom_Post_Data_Manager extends KT_Data_Manager_Base {

    private $queryArgs = [];
    private $prefixMetaKey = null;
    private $suffixMetaKey = null;

    function __construct(array $queryArgs = null) {
        if (KT::arrayIssetAndNotEmpty($queryArgs)) {
            $this->setQueryArgs($queryArgs);
        }
    }

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

    /** @return string */
    private function getQueryArgs() {
        return $this->queryArgs;
    }

    /** @return string */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /** @return string */
    private function getPrefixMetaKey() {
        return $this->prefixMetaKey;
    }

    /** @return string */
    private function getSuffixMetaKey() {
        return $this->suffixMetaKey;
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
        $this->suffixMetaKey = $suffixMetaKey;
        return $this;
    }

    // --- veřejné metody ------------------------

    /** @return boolean */
    private function isPrefixMetaKey() {
        return KT::issetAndNotEmpty($this->getPrefixMetaKey());
    }

    /** @return boolean */
    private function isSuffixMetaKey() {
        return KT::issetAndNotEmpty($this->getSuffixMetaKey());
    }

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
        $options = [];
        if (KT::issetAndNotEmpty($postCollection)) {
            foreach ($postCollection as $postItem) {
                $postId = $postItem->ID;
                $options[$postId] = $this->getPrefixValue($postId) . $postItem->post_title . $this->getSuffixValue($postId);
            }
        }
        $this->setData($options);

        return $this;
    }

    // --- neveřejné metody ------------------------

    /**
     * Vrátí hodnotu meta_value na základě nastaveného meta klíče
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $postId
     * @return string
     */
    protected function getPrefixValue($postId) {
        if ($this->isPrefixMetaKey()) {
            $metaValue = get_post_meta($postId, $this->getPrefixMetaKey(), true);
            if (KT::issetAndNotEmpty($metaValue)) {
                return "$metaValue - ";
            }
        }
        return null;
    }

    /**
     * Vrátí hodnotu meta_value na základě nastaveného meta klíče
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $postId
     * @return string
     */
    protected function getSuffixValue($postId) {
        if ($this->isSuffixMetaKey()) {
            $metaValue = get_post_meta($postId, $this->getSuffixMetaKey(), true);
            if (KT::issetAndNotEmpty($metaValue)) {
                return " - $metaValue";
            }
        }
        return null;
    }

}
