<?php

abstract class KT_Field extends KT_HTML_Tag_Base{

    const DEFAULT_CLASS = 'kt-field';

    private $label = null;
    private $name = null;
    private $postPrefix = null;
    private $unit = null;
    private $value = null;
    private $error = false;
    private $validators = array();

    /**
     * Abstraktní třída pro všechny KT_Fields
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label) {
        $this->setAttrId($name)
            ->addAttrClass(self::DEFAULT_CLASS)
            ->setLabel($label)
            ->setName($name);
                    
    }

    // --- settery ------------------------

    /**
     * Nastavení popisek inputu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $label
     * @return \KT_Field
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Nastavení název inputu pro jeho identifikaci v rámci fieldsetu, $_POST a $_GET - attr name
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $name
     * @return \KT_Field
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Nastavení prefix pro odeslání dat metodou $_POST nebo $_GET.
     * Field tak bude dostupný v další úrovni pole
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $postPrefix
     * @return \KT_Field
     */
    public function setPostPrefix($postPrefix) {
        if(KT::issetAndNotEmpty($postPrefix)){
            $this->postPrefix = $postPrefix;
        }
        return $this;
    }

    /**
     * Nastavení ToolTip fieldu - nápověda, která se vpíše do titulku inputu attr - title
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $toolTip
     * @return \KT_Field
     */
    public function setToolTip($toolTip) {
        $this->setAttrTitle($toolTip);

        return $this;
    }

    /**
     * Nastavení jednotky vstupovadla - neřeší validaci ani jinou závislost.
     * Jednotka je určena čistě pro presentační účely
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $unit
     * @return \KT_Field
     */
    public function setUnit($unit) {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Nastavení / změnu hodnoty fildu attr - value.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value
     * @return \KT_Field
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Nastaví fieldu příslušnou error msg
     * Pro interní použítí třídy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $error
     * @return \KT_Field
     */
    public function setError($error) {
        $this->error = $error;

        return $this;
    }
    
    /**
     * Nastaví HTML parametr tabindex fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $tabindex
     * @return \KT_Field
     */
    public function setTabindex($tabindex) {
        $tabindex = KT::tryGetInt($tabindex);
        if(KT::isIdFormat($tabindex)){
            $this->addAttribute("tabindex", $tabindex);
        }
        
        return $this;
    }   

    /**
     * Nastavení kolekci Validotárů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $validators
     * @return \KT_Field
     */
    private function setValidators(array $validators) {
        $this->validators = $validators;
        return $this;
    }

    // --- gettery ------------------------

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPostPrefix() {
        return $this->postPrefix;
    }

    /**
     * @return string
     */
    protected function getToolTip() {
        return $this->getAttrValueByName("title");
    }

    /**
     * @return string
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @return string
     */
    private function getError() {
        return $this->error;
    }
     
    /**
     * @return array
     */
    private function getValidators() {
        return $this->validators;
    }

    // --- abstraktní funkce ---------------

    abstract function renderField();

    abstract function getField();

    abstract function getFieldType();

    // --- veřejné funkce ------------------
    
    /**
     * Vrátí HTML s <label> a samotný fieldem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getControlHtml(){
        $html = $this->getLabelHtml();
        return $html .= $this->getField();
    }
    
    /**
     * Vrátí HTML element <label> pro daný field
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getLabelHtml(){
        return "<label for=\"". $this->getAttrValueByName("id") ."\">". $this->getLabel() ."</label>";
    }
  

    /**
     * Založí fieldu nový KT_Field_Validator
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $condition
     * @param string $message
     * @param mixed $param -> int || array
     * @return \KT_Field
     */
    public function addRule($condition, $message, $param = null) {
        $this->validators[] = new KT_Field_Validator($condition, $message, $param);

        $this->setFieldAttributeByValidator($condition, $param);

        return $this;
    }

    /**
     * Provede validaci fieldu na základě zadaných podmínek
     * V případě, že se jedná o chybu, nastaví automaticky fieldu hlášku s errorem z validatoru
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function Validate() {
        if (KT::notIssetOrEmpty($this->getValidators())) {
            return true;
        }

        foreach ($this->getValidators() as $validator) {
            if (!$validator->validate($this->getValue())) {
                $this->setError($validator->getMessage());
                return false;
            }
        }

        $this->setError(null);
        return true;
    }

    /**
     * Vrátí základní HTML prvky pro všechny fieldy
     * Class, Name, ID, Title(tooltip), validator jSON
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getBasicHtml() {
        $html = "";
        $this->validatorJsonContentInit();
        $html .= $this->getNameAttribute();
        $html .= " ";
        $html .= $this->getAttributeString();
        return $html;
    }

    /**
     * Vrátí zda má field chybu po validaci
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function hasErrorMsg() {
        if (KT::issetAndNotEmpty($this->error)) {
            return true;
        }
        return false;
    }

    /**
     * V případě potřeby vrátí přeconvertovanou hodnotu fieldu pro prezentaci
     * Hlavním účelem funkce je pro dědičnost.
     * 
     * @return mixed
     */
    public function getConvertedValue() {
        return $this->getValue();
    }

    /**
     * Vrátí field value na základě zaslaného postu, getu, prefixu nebo nastaveného value
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return null
     */
    public function getValue() {
        if (KT::issetAndNotEmpty($this->getPostPrefix())) {
            if (isset($_POST[$this->getPostPrefix()][$this->getName()])) {
                return $_POST[$this->getPostPrefix()][$this->getName()];
            }

            if (isset($_GET[$this->getPostPrefix()][$this->getName()])) {
                return $_GET[$this->getPostPrefix()][$this->getName()];
            }
        }

        if (isset($_POST[$this->getName()])){
            return $_POST[$this->getName()];
        }


        if (isset($_GET[$this->getName()])){
            return $_GET[$this->getName()];
        }
        
        if($this->getFieldType() == KT_Checkbox_Field::FIELD_TYPE){
            if(isset($_POST[$this->getPostPrefix()])){
                return $_POST[$this->getPostPrefix()];
            }
            
            if(isset($_GET[$this->getPostPrefix()])){
                return $_GET[$this->getPostPrefix()];
            }
        }


        if (KT::issetAndNotEmpty($this->value) || $this->value === "0" || $this->value === 0) {
            return $this->value;
        }

        return null;
    }

    // --- protected funkce -------

    /**
     * Vrátí string za jméno každého fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    protected function getAfterNameValue() {
        return "";
    }

    /**
     * Vrátí html s notice obsahující ErrorMessage
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    protected function getHtmlErrorMsg() {
        $html = "<div class=\"validator\">";
        $html .= "<span class=\"erorr-s\">" . htmlspecialchars($this->getError()) . "</span>";
        $html .= "</div>";

        return $html;
    }

    // --- privátní funkce ----------------

    /**
     * Vrátí HTML s attributem name fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    protected function getNameAttribute() {

        $html = "";
        $afterNameString = static::getAfterNameValue();

        if (KT::issetAndNotEmpty($this->getPostPrefix())) {
            $html .= "name=\"{$this->getPostPrefix()}[{$this->getName()}]$afterNameString\" ";
        } else {
            $html .= "name=\"{$this->getName()}$afterNameString\" ";
        }

        return $html;
    }

    /**
     * Nastavení HTML 5 attributy fieldu na základě některých validačních prvků
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param type $validatorCondition
     * @param type $params
     */
    private function setFieldAttributeByValidator($validatorCondition, $params = null) {
        switch ($validatorCondition) {
            case KT_Field_Validator::REQUIRED:
                $this->addAttribute("required");
                break;

            case KT_Field_Validator::MAX_NUMBER:
                $this->addAttribute("max", $params);
                break;

            case KT_Field_Validator::MIN_NUMBER:
                $this->addAttribute("min", $params);
                break;

            case KT_Field_Validator::RANGE:
                $this->addAttribute("min", $params[0])->addAttribute("max", $params[1]);
        }

        return $this;
    }

    /**
     * Ze všech definovaných validátorů sestaví json, který vrátí jako string
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @return \KT_Field
     */
    protected function validatorJsonContentInit() {
        if (!KT::issetAndNotEmpty($this->getValidators())) {
            return "";
        }

        foreach ($this->getValidators() as $validator) {
            /* @var $validator \KT_Field_Validator */
            $validatorCollection[] = array(
                "condition" => $validator->getCondition(),
                "msg" => $validator->getMessage(),
                "params" => $validator->getParam()
            );
        }

        $html = json_encode($validatorCollection);

        $this->addAttribute("data-validators", $html);

        return $this;
    }

}
