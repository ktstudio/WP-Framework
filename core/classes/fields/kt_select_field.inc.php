<?php

class KT_Select_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = 'select';

    private $firstEmpty = false; // Má se v SELECTU nabídnout možnost, která nevybere nic

    /**
     * Založení objektu typu Select
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */

    public function __construct($name, $label) {
        parent::__construct($name, $label);

        return $this;
    }

    // --- settery ------------------------

    /**
     * Nastavuje se, zda má mít select možnost vybrat první položku jako prázdnou - odešle null
     * 
     * @param bolean $firstEmpty
     * @return \KT_Select_Field
     */
    function setFirstEmpty($firstEmpty = true) {
        if (is_bool($firstEmpty)) {
            $this->firstEmpty = $firstEmpty;
        }

        return $this;
    }

    // --- getter ------------------------

    /**
     * @return boolean
     */
    protected function getFirstEmpty() {
        return $this->firstEmpty;
    }

    // --- veřejné funkce -----------------

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     */
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

        $html .= "<select ";
        $html .= $this->getBasicHtml();
        $html .= ">";

        $html .= static::getOptionContent();

        $html .= "</select>";

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

    /**
     * Připraví html content s <option> pro výběr selectu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    public function getOptionContent() {
        $html = "";
        $selected = "";
       
        $emptyOption = "<option value=\"\">" . KT_EMPTY_SYMBOL . "</option>";

        if ($this->getFirstEmpty() == true) {
            $html .= $emptyOption;
        }
        
       foreach ($this->getOptionsData() as $key => $val) {
            if ($key == $this->getValue() && $this->getValue() !== null && $this->getValue() !== '') {
                $selected = "selected=\"selected\"";
            }

            $html .= "<option value=\"$key\" $selected>$val</option>";

            $selected = "";
        }

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

}
