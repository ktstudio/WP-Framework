<?php

/**
 * Abstraktní základ pro všechny KT fieldy s placeholdrem
 *
 * @author Martin Hlaváč
 * @link http://www.KTStudio.cz
 */
abstract class KT_Placeholder_Field_base extends KT_Field {

    private $placeholder = null;

    /**
     * Abstraktní základ pro všechny KT fieldy s placeholdrem
     *
     * @author Martin Hlaváč
     * @link http://www.KTStudio.cz
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
     * @link http://www.KTStudio.cz
     * 
     * @return string
     */
    public function getPlaceholder() {
        return $this->placeholder;
    }

    /**
     * Nastavení hodnotu placeholderu elementu fieldu, resp. atributu placeholder
     * Neřeší starší prohlížeče.
     * 
     * @author Martin Hlaváč
     * @link http://www.KTStudio.cz
     * 
     * @param string $placeholder
     * @return \KT_Placeholder_Field_base
     */
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Vrátí html attribute (i s hodnotou) pro sestavení html tagu
     * 
     * @author Martin Hlaváč
     * @link http://www.KTStudio.cz
     * 
     * @return (html)string
     */
    public function getPlaceholderAttribute() {
        $placeholder = $this->getPlaceholder();
        return " placeholder=\"$placeholder\" ";
    }

    /**
     * Označení, zda je placeholder zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.KTStudio.cz
     * 
     * @return boolean
     */
    public function isPlaceholder() {
        $placeholder = $this->getPlaceholder();
        return kt_isset_and_not_empty($placeholder);
    }

}
