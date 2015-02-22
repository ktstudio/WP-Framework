<?php

class KT_Radio_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = 'radio';

    /**
     * Založení objektu typeu Radio
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);

        return $this;
    }

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

        if (KT::notIssetOrEmpty($this->getOptionsData())) {
            return $html = KT_EMPTY_SYMBOL;
        }

        foreach ($this->getOptionsData() as $key => $value) {

            $html .= "<span class=\"input-wrap radio\">";
            $html .= "<input type=\"radio\" ";
            $html .= $this->getBasicHtml( $key );
            $html .= " value=\"$key\" ";

            if ($key == $this->getValue() && $this->getValue() !== null) {
                $html .= "checked=\"checked\"";
            }

            $html .= "> <span class=\"radio radio-name-{$this->getAttrValueByName("id")} radio-key-$key \"><label for=\"{$this->getName()}-{$key}\">$value</label></span> ";

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
    public function getBasicHtml( $inputName = null) {
        $html = "";
        $this->validatorJsonContentInit();
        $this->setAttrId($this->getName() . "-". $inputName);
        $html .= $this->getNameAttribute();
        $html .= $this->getAttributeString();

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

}
