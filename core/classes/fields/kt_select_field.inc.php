<?php

class KT_Select_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = "select";
    const OPTION_GROUP_NAME = "optgroup";

    private $firstEmpty; // Má se v SELECTU nabídnout možnost, která nevybere nic

    /**
     * Založení objektu typu Select
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     */

    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    // --- getter & settery ------------------------

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * @return string
     */
    protected function getFirstEmpty() {
        return $this->firstEmpty;
    }

    /**
     * Nastavuje se, zda má mít select možnost vybrat první položku jako prázdnou - odešle null
     * 
     * @param string $firstEmpty
     * @return \KT_Select_Field
     */
    public function setFirstEmpty($firstEmpty = true) {
        if (is_bool($firstEmpty)) {
            $this->firstEmpty = KT_EMPTY_SYMBOL;
        } else {
            $this->firstEmpty = $firstEmpty;
        }

        return $this;
    }

    // --- veřejné funkce ------------------------

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
        $html = "<select {$this->getBasicHtml()}>";
        $html .= static::getOptionsContent();
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
    public function getOptionsContent() {
        $html = "";

        $firstEmpty = $this->getFirstEmpty();
        if (isset($firstEmpty)) {
            $html .= "<option value=\"\">$firstEmpty</option>";
        }

        foreach ($this->getOptionsData() as $optionKey => $optionValue) {
            if (KT::arrayIssetAndNotEmpty($optionValue)) {
                $html .= $this->getOptionsGroupContent($optionValue);
            } else {
                $html .= $this->getSignleOptionItem($optionKey, $optionValue);
            }
        }

        return $html;
    }

    // --- privátní funkce ------------------------

    /**
     * Vrátí HTML se skupinou (<optgroup>) všech options, které do skupiny patří
     * Jedna z položek $optionGroupData musí mít klíč self::OPTION_GROUP_KEY
     * hodnota na tomto klíči bude použitá jako label celé kolekce
     * 
     * @author Tomáš Kocifaj
     * @link http:://www.ktstudio.cz
     * 
     * @param array $optionGroupData
     * @return string
     */
    protected function getOptionsGroupContent(array $optionGroupData = array()) {
        $html = "";

        if (!KT::arrayIssetAndNotEmpty($optionGroupData)) {
            return $html;
        }

        if (array_key_exists(self::OPTION_GROUP_NAME, $optionGroupData)) {
            $groupLable = $optionGroupData[self::OPTION_GROUP_NAME];
            unset($optionGroupData[self::OPTION_GROUP_NAME]);
        } else {
            return $html;
        }

        $html .= "<optgroup label=\"$groupLable\">";
        foreach ($optionGroupData as $optionKey => $optionValue) {
            $html .= $this->getSignleOptionItem($optionKey, $optionValue);
        }
        $html .= "</optgroup>";

        return $html;
    }

    /**
     * Vrátí HTML s jedním option pro celou kolekci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $optionKey
     * @param string $optionValue
     * @return string
     */
    protected function getSignleOptionItem($optionKey, $optionValue) {
        $selected = null;
        $value = $this->getValue();
        if ($optionKey == $value && $value !== null && $value !== '') {
            $selected = " selected=\"selected\"";
        }
        $filteredValue = filter_var($optionValue, $this->getFilterSanitize());
        return $html = "<option value=\"$optionKey\"$selected>$filteredValue</option>";
    }

}
