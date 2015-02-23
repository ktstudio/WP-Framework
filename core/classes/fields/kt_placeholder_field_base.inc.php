<?php

/**
 * Abstraktní základ pro všechny KT fieldy s placeholdrem
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Placeholder_Field_base extends KT_Field {
    
    const PLACEHOLDER_KEY = "placeholder";

    /**
     * Abstraktní základ pro všechny KT fieldy s placeholdrem
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    /**
     * Vrátí holdu pro placeholder
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getPlaceholder() {
        return $this->getAttrValueByName(self::PLACEHOLDER_KEY);
    }

    /**
     * Nastavení hodnotu placeholderu elementu fieldu, resp. atributu placeholder
     * Neřeší starší prohlížeče.
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $placeholder
     * @return \KT_Placeholder_Field_base
     */
    public function setPlaceholder($placeholder) {
        $this->addAttribute(self::PLACEHOLDER_KEY, $placeholder);
        return $this;
    }

    /**
     * Označení, zda je placeholder zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function isPlaceholder() {
        $placeholder = $this->getPlaceholder();
        return KT::issetAndNotEmpty($placeholder);
    }

}
