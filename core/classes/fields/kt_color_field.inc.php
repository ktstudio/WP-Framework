<?php

/**
 * GUI prvek pro výběr barvy
 * 
 * @deprecated ve vývoji
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Color_Field extends KT_Field {

    const FIELD_TYPE = "color";

    private $defaultColor = null;

    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->addAttrClass("colorField");
    }

    // --- gettery & settery ------------------

    /**
     * Vrátí nastavenou výchozí barvu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return type
     */
    public function getDefaultColor() {
        return $this->defaultColor;
    }

    /**
     * Nastaví výchozí barvu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $defaultColor
     * @return \KT_Color_Field
     */
    public function setDefaultColor($defaultColor) {
        $this->defaultColor = $defaultColor;
        return $this;
    }

    /**
     * Vrátí typ fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    // --- veřejné funkce ------------------

    public function renderField() {
        echo $this->getField();
    }

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getField() {

        $html = "";

        $value = htmlentities($this->getValue());

        $html .= "<input type=\"text\" ";
        $html .= $this->getBasicHtml();
        $html .= " value=\"{$value}\" ";

        if (KT::notIssetOrEmpty($this->getDefaultColor())) {
            $html .= " data-default-color=\"{$this->getDefaultColor()}\"";
        }

        $html .= "/>";

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

}
