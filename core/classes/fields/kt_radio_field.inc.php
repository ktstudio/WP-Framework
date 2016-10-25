<?php

class KT_Radio_Field extends KT_Options_Field_Base
{
    const FIELD_TYPE = "radio";

    private $additionalClasses = array();

    /**
     * Založení objektu typeu Radio
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    // --- getry & setry ---------------------

    /** @return string */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * Vrátí zadané dodateční CSS classy podle jejich ID hodnot
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public function getAdditionalClasses() {
        return $this->additionalClasses;
    }

    /**
     * Nastaví zadané dodateční CSS classy ve formátu [hodnota => CSS class]
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $additionalClasses
     */
    public function setAdditionalClasses(array $additionalClasses) {
        $this->additionalClasses = $additionalClasses;
    }

    // --- veřejné metody ---------------------

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
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
            return KT_EMPTY_SYMBOL;
        }
        $html = null;
        foreach ($this->getOptionsData() as $key => $value) {
            $html .= "<span class=\"input-wrap radio\">";
            $html .= "<input type=\"radio\" ";
            $html .= $this->getBasicHtml($key);
            $html .= " value=\"$key\" ";
            if ($key == $this->getValue() && $this->getValue() !== null) {
                $html .= "checked=\"checked\"";
            }
            $filteredValue = filter_var($value, $this->getFilterSanitize());
            $additionalClass = KT::arrayTryGetValue($this->getAdditionalClasses(), $key);
            $html .= "> <span class=\"radio radio-name-{$this->getAttrValueByName("id")} radio-key-$key $additionalClass\"><label for=\"{$this->getName()}-{$key}\">$filteredValue</label></span> ";
            $html .= "</span>";
        }
        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }
        return $html;
    }

    /**
     * Vrátí základní HTML prvky pro Radio field
     * Class, Name, ID, Title(tooltip), validator jSON
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getBasicHtml($inputName = null) {
        $html = null;
        $this->validatorJsonContentInit();
        $this->setAttrId($this->getName() . "-" . $inputName);
        $html .= $this->getNameAttribute();
        $html .= $this->getAttributeString();
        return $html;
    }

}
