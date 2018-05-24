<?php

class KT_Checkbox_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = "checkbox";

    /**
     * Založení objektu typu Checkbox
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->setFilterSanitize(FILTER_SANITIZE_STRING);
    }

    // --- getry & settery ------------------------

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * Vrátí field value na základě zaslaného postu, getu, prefixu nebo nastaveného value
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public function getValue() {
        $value = parent::getCleanValue();
        if (KT::arrayIsSerialized($value)) {
            return unserialize($value);
        }
        return $value;
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
        if (KT::notIssetOrEmpty($this->getOptionsData())) {
            return "<span class=\"input-wrap checkbox\">" . KT_EMPTY_SYMBOL . "</span>";
        }

        $data = $this->getValue();

        $html = "";

        foreach ($this->getOptionsData() as $key => $value) {
            $html .= "<span class=\"input-wrap\">";
            $html .= "<input type=\"checkbox\" ";
            $html .= $this->getBasicHtml($key);
            $html .= " value=\"$key\" ";

            $checkClassAttribute = null;
            if (KT::issetAndNotEmpty($data)) {
                if (is_array($data)) {
                    if (in_array($key, array_keys($data))) {
                        $html .= " checked=\"checked\"";
                        $checkClassAttribute = " class=\"checked\"";
                    }
                } elseif($key == $data) {
                    $html .= " checked=\"checked\"";
                    $checkClassAttribute = " class=\"checked\"";
                }
            }

            $filteredValue = filter_var($value, $this->getFilterSanitize());
            $html .= "> <span class=\"desc-checkbox-{$this->getAttrValueByName("id")}\"><label for=\"{$this->getName()}-{$key}\"$checkClassAttribute>$filteredValue</label></span> ";

            if ($this->hasErrorMsg()) {
                $html .= parent::getHtmlErrorMsg();
            }

            $html .= "</span>";
        }

        return $html;
    }

    /**
     * Vrátí základní HTML prvky pro všechny fieldy
     * Class, Name, ID, Title(tooltip), validator jSON
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $inputName
     * @return string
     */
    public function getBasicHtml($inputName = null) {
        $this->validatorJsonContentInit();
        $this->setAttrId($this->getName() . "-" . $inputName);
        $html = $this->getNameAttribute($inputName);
        $html .= $this->getAttributeString();
        return $html;
    }

    // --- privátní funkce ------------------

    /**
     * Zajistí, aby hodnoty byly zpracovány jako pole
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    protected function getAfterNameValue() {
        return "[]";
    }

    /**
     * Vrátí HTML s attributem name fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $inputName
     * @return string
     */
    protected function getNameAttribute($inputName = null) {
        if (KT::issetAndNotEmpty($this->getPostPrefix())) {
            return $html = "name=\"{$this->getPostPrefix()}[{$this->getName()}][$inputName]\" ";
        } else {
            return $html = "name=\"{$this->getName()}[$inputName]\" ";
        }
    }

}
