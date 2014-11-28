<?php

abstract class KT_Field {

    const DEFAULT_CLASS = 'kt-field';

    private $label = null;
    private $name = null;
    private $postPrefix = null;
    private $toolTip = null;
    private $unit = null;
    private $classes = array(self::DEFAULT_CLASS);
    private $id = null;
    private $placeholder = null;
    private $value = null;
    private $error = false;
    private $validators = array();
    private $attributes = array();

    /**
     * Abstraktní třída pro všechny KT_Fields
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param type $name
     * @param type $label
     * @return \KT_Field
     */
    public function __construct($name, $label) {
        $this->setId($name);
        $this->setLabel($label);
        $this->setName($name);

        return $this;
    }

    // --- settery ------------------------

    /**
     * Nastavení popisek inputu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
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
     * @link http://www.KTStudio.cz
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
     * @link http://www.KTStudio.cz
     * 
     * @param string $postPrefix
     * @return \KT_Field
     */
    public function setPostPrefix($postPrefix) {
        $this->postPrefix = $postPrefix;
    }

    /**
     * Nastavení ToolTip fieldu - nápověda, která se vpíše do titulku inputu attr - title
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $toolTip
     * @return \KT_Field
     */
    public function setToolTip($toolTip) {
        $this->toolTip = $toolTip;

        return $this;
    }

    /**
     * Nastavení jednotky vstupovadla - neřeší validaci ani jinou závislost.
     * Jednotka je určena čistě pro presentační účely
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $unit
     * @return \KT_Field
     */
    public function setUnit($unit) {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Nastavení pole tříd, které budou ve fieldu definované
     * array("class1", "class2");
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param array $classes
     * @return \KT_Field
     */
    public function setClasses(array $classes) {
        $this->classes = $classes;

        return $this;
    }

    /**
     * Nastavení ID field u - slouží pro identifikaci HTML elementu attr id
     * Defaultně je id nastaven jako název fieldu (name)
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $id
     * @return \KT_Field
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Nastavení placeholderu elementu fieldu - attr placeholder
     * Neřeší starší prohlížeče.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $placeholder
     * @return \KT_Field
     */
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Nastavení / změnu hodnoty fildu attr - value.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
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
     * @link http://www.KTStudio.cz
     * 
     * @param string $error
     * @return \KT_Field
     */
    public function setError($error) {
        $this->error = $error;

        return $this;
    }

    /**
     * Nastavení kolekci Validotárů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @param array $validators
     * @return \KT_Field
     */
    public function setValidators(array $validators) {
        $this->validators = $validators;
        return $this;
    }

    /**
     * Nastavení kolekci attributů
     * array( "attrName" => "attrValue")
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @param array $attributes
     * @return \KT_Field
     */
    public function setAttributes(array $attributes) {
        $this->attributes = $attributes;
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
     * @return stromg
     */
    protected function getToolTip() {
        return $this->toolTip;
    }

    /**
     * @return stromg
     */
    public function getUnit() {
        return $this->unit;
    }

    /**
     * @return array
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * @return stromg
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return stromg
     */
    public function getPlaceholder() {
        return $this->placeholder;
    }

    /**
     * @return stromg
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

    /**
     * @return array
     */
    private function getAttributes() {
        return $this->attributes;
    }

    // --- abstraktní funkce ---------------

    abstract function renderField();

    abstract function getField();

    abstract function getFieldType();

    // --- veřejné funkce ------------------

    /**
     * Přidá fieldu classu do html tagu - attr class
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $class
     * @return \KT_Field
     */
    public function addClass($class) {
        if (kt_isset_and_not_empty($class)) {
            array_push($this->classes, $class);
        }

        return $this;
    }

    /**
     * Přidá html attribute do tagu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name - nzev attributu (id)
     * @param string $value - hodnota (kt-form)
     * @return \KT_Field
     */
    public function addAttribute($name, $value = null) {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Založí fieldu nový KT_Field_Validator
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
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
     * @link http://www.KTStudio.cz
     *
     * @return boolean
     */
    public function Validate() {
        if (kt_not_isset_or_empty($this->getValidators())) {
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
     * @link http://www.KTStudio.cz
     *
     * @return string
     */
    public function getBasicHtml() {

        $this->validatorJsonContentInit();

        $html = "class=\"{$this->getClassAttributeContent()}\" ";

        $html .= $this->getNameAttribute();

        $html .= "id=\"" . static::getId() . "\" ";

        $html .= $this->getAttributesContent();

        $html .= $this->getPlaceholder() . " ";

        if (kt_isset_and_not_empty($this->getToolTip())) {
            $html .= 'title="' . htmlspecialchars($this->getToolTip()) . '" ';
        }

        return $html;
    }

    /**
     * Vrátí zda má field chybu po validaci
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return boolean
     */
    public function hasErrorMsg() {
        if (kt_isset_and_not_empty($this->error)) {
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
     * @link http://www.KTStudio.cz
     *
     * @return null
     */
    public function getValue() {
        if (kt_isset_and_not_empty($this->getPostPrefix())) {
            if (isset($_POST[$this->getPostPrefix()][$this->getName()])) {
                return $_POST[$this->getPostPrefix()][$this->getName()];
            }

            if (isset($_GET[$this->getPostPrefix()][$this->getName()])) {
                return $_GET[$this->getPostPrefix()][$this->getName()];
            }
        }

        if (isset($_POST[$this->getName()]))
            return $_POST[$this->getName()];


        if (isset($_GET[$this->getName()]))
            return $_GET[$this->getName()];


        if (kt_isset_and_not_empty($this->value) || $this->value === "0" || $this->value === 0) {
            return $this->value;
        }

        return null;
    }

    // --- protected funkce -------

    /**
     * Vrátí string za jméno každého fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
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
     * @link http://www.KTStudio.cz
     *
     * @return string
     */
    protected function getHtmlErrorMsg() {
        $html .= "<div class=\"validator\">";
        $html .= "<span class=\"erorr-s\">" . htmlspecialchars($this->getError()) . "</span>";
        $html .= "</div>";

        return $html;
    }

    // --- privátní funkce ----------------

    /**
     * Vrátí HTML s attributem name fieldu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @return string
     */
    protected function getNameAttribute() {

        $html = "";
        $afterNameString = $this->getAfterNameValue();

        if (kt_isset_and_not_empty($this->getPostPrefix())) {
            $html .= "name=\"{$this->getPostPrefix()}[{$this->getName()}]$afterNameString\" ";
        } else {
            $html .= "name=\"{$this->getName()}$afterNameString\" ";
        }

        return $html;
    }

    /**
     * Vrátí všechny definované classy fieldu v potřeném stringu pro print do class attributu.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @return string
     */
    public function getClassAttributeContent() {

        $html = "";

        if (kt_isset_and_not_empty($this->classes)) {
            foreach ($this->classes as $class) {
                $html .= "$class ";
            }
        }

        return $html;
    }

    /**
     * Připraví string se zadanými attributy pro field
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @return string
     */
    private function getAttributesContent() {

        $html = "";

        if (kt_isset_and_not_empty($this->getAttributes())) {
            foreach ($this->getAttributes() as $key => $value) {
                if (kt_isset_and_not_empty($value)) {
                    $html .= $key . "=\"" . htmlspecialchars($value) . "\" ";
                } else {
                    $html .= $key . " ";
                }
            }
        }

        return $html;
    }

    /**
     * Nastavení HTML 5 attributy fieldu na základě některých validačních prvků
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
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
     * @link http://www.KTStudio.cz  
     * 
     * @return \KT_Field
     */
    private function validatorJsonContentInit() {
        if (!kt_isset_and_not_empty($this->getValidators())) {
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
