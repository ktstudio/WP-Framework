<?php

class KT_Checkbox_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = 'checkbox';

    /**
     * Založení objektu typu Checkbox
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);

        return $this;
    }

    // --- veřejné funkce ------------

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
        
        $html = "";

        foreach ($this->getOptionsData() as $key => $val) {
            $html .= "<span class=\"input-wrap\">";
            $html .= "<input type=\"checkbox\" ";
            $html .= $this->getBasicHtml( $key );
            $html .= " value=\"$key\" ";

            $data = $this->getValue();

            if (KT::issetAndNotEmpty($data) && is_array($data)) {
                if (in_array($key, array_keys($data))) {
                    $html .=" checked=\"checked\"";
                }
            }

            $html .= "> <span class=\"desc-checkbox-{$this->getAttrValueByName("id")}\"><label for=\"{$this->getName()}-{$key}\">$val</label></span> ";

            if ($this->hasErrorMsg()) {
                $html .= parent::getHtmlErrorMsg();
            }

            $html .= "</span>";
        }

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
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
    public function getBasicHtml( $inputName = null ) {

        $html = "";
        
        $this->validatorJsonContentInit();
        $this->setAttrId($this->getName() . "-" . $inputName);
        $html .= $this->getNameAttribute( $inputName );
        $html .= $this->getAttributeString();

        return $html;
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
    protected function getNameAttribute( $inputName = null ) {

        $html = "";

        if (KT::issetAndNotEmpty($this->getPostPrefix())) {
            $html .= "name=\"{$this->getPostPrefix()}[{$this->getName()}][$inputName]\" ";
        } else {
            $html .= "name=\"{$this->getName()}[$inputName]\" ";
        }

        return $html;
    }

}
