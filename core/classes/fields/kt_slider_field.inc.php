<?php

class KT_Slider_Field extends KT_Slider_Field_Base {

    const FIELD_TYPE = "slider";
    
    private $inputType = self::FIELD_TYPE;

    /**
     * Založení objektu pro jQuery UI v podobě inputu se sliderem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return KT_Text_Field
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->addAttrClass("sliderContainer");
    }

    // --- gettery a settery ---------------

    /**
     * Nastaví minimální číslo, na kterém bude slider začínat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $minNumber
     * @return \KT_Slider_Field
     */
    public function setMinValue($minNumber) {
        parent::setMinValue($minNumber);
        $this->addRule(KT_Field_Validator::MIN_NUMBER, sprintf(__("Minimální hodnota je %d"), $minNumber), $minNumber);
        return $this;
    }

    /**
     * Nastaví maximální číslo, na kterém bude slider končit
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $maxNumber
     * @return \KT_Slider_Field
     */
    public function setMaxValue($maxNumber) {
        parent::setMaxValue($maxNumber);
        $this->addRule(KT_Field_Validator::MAX_NUMBER, sprintf(__("Maximální hodnota je %d"), $maxNumber), $maxNumber);
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
        return $this->inputType;
    }

    // --- veřejné metody ------------------

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
        $this->validatorJsonContentInit();
              
        $html .= "<div " . $this->getAttributeString() . ">";
        $html .= "<div class=\"sliderInputElement\" data-min=\"{$this->getMinValue()}\" data-max=\"{$this->getMaxValue()}\" data-step=\"{$this->getStep()}\">";

        $html .= "<input type=\"number\"";
        $html .= " class=\"inputMin\" min=\"{$this->getMinValue()}\" max=\"{$this->getMaxValue()}\" step=\"{$this->getStep()}\"";
        $html .= $this->getNameAttribute();
        $html .= " value=\"{$this->getValue()}\"";
        $html .= ">";

        $html .= "</div>";
        $html .= "</div>";

        return $html;
    }

}
