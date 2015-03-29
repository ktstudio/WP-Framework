<?php

class KT_Slider_Field extends KT_Field{
    
    const FIELD_TYPE = "slider";
    
    private $minNumber = 0;
    private $maxNumber = null;
    private $step = 1;
    private $range = false;
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
        $this->addAttrClass("rangerContainer");
    }
    
    // --- gettery a settery ---------------
    
    /**
     * @return int
     */
    public function getMinNumber() {
        return $this->minNumber;
    }
    
    /**
     * Nastaví minimální číslo, na kterém bude slider začínat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $minNumber
     * @return \KT_Slider_Field
     */
    public function setMinNumber($minNumber) {
        $this->minNumber = $minNumber;
        $this->addRule(KT_Field_Validator::MIN_NUMBER, sprintf(__("Minimální hodnota je %d"), $minNumber), $minNumber);
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxNumber() {
        return $this->maxNumber;
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
    public function setMaxNumber($maxNumber) {
        $this->maxNumber = $maxNumber;
        $this->addRule(KT_Field_Validator::MAX_NUMBER, sprintf(__("Maximální hodnota je %d"), $maxNumber), $maxNumber);
        return $this;
    }
    
    /**
     * @return int
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * Nastaví velikost "kroku", po kterém bude bude možné zvyšovat / zmenšovat hodnotu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $step
     * @return \KT_Slider_Field
     */
    public function setStep($step) {
        $this->step = $step;
        return $this;
    }   

    /**
     * @return boolean
     */
    public function getRange() {
        return $this->range;
    }
    
    /**
     * Nastaví, zda má být možnost výběru od MIN do MAX
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param bolean $range
     * @return \KT_Slider_Field
     * @throws InvalidArgumentException
     */
    public function setRange($range) {
        if(is_bool($range)){
            $this->range = $range;
            return $this;
        }
        throw new InvalidArgumentException("Range must by bol type");
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
        $html .= "<div ". $this->getAttributeString() .">";
        $html .= "<div class=\"sliderInputElement\" data-min=\"{$this->getMinNumber()}\" data-max=\"{$this->getMaxNumber()}\" data-step=\"{$this->getStep()}\" data-range=\"{$this->getRange()}\">";
        
        $this->validatorJsonContentInit();
        
        $html .= "<input type=\"number\"";
        $html .= " class=\"inputMin\" ";
        $html .= $this->getNameAttribute();
        $html .= " value=\"{$this->getValue()}\"";
        $html .= ">";
        
        $html .= "</div>";
        $html .= "</div>";
        
        return $html;
    }
}
