<?php

abstract class KT_Field extends KT_HTML_Tag_Base {

    const DEFAULT_CLASS = 'kt-field';

    private $label = null;
    private $name = null;
    private $postPrefix = null;
    private $unit = null;
    private $value;
    private $cleanValue;
    private $defaultValue = null;
    private $filterSanitize = FILTER_SANITIZE_SPECIAL_CHARS;
    private $error = false;
    private $validators = array();
    protected $visible = true;

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
        if (KT::issetAndNotEmpty($postPrefix)) {
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
     * @deprecated since version 1.7 
     * @see setDefaultValue
     */
    public function setValue($value) {
        return $this->setDefaultValue($value);
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
    public function setDefaultValue($value) {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * Nastavení (vlastní) sanatizační kód pro hodnotu(y) (výpis)
     * 
     * @author Matin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $code
     * @return \KT_Field
     */
    public function setFilterSanitize($code) {
        $this->filterSanitize = KT::tryGetInt($code);
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
        if (KT::isIdFormat($tabindex)) {
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

    /**
     * Má se field veřejně zobrazovat
     * 
     * @author Jan Pokorný
     * @param bool $visible
     */
    public function setVisible($visible) {
        $this->visible = $visible;
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
    public function getError() {
        return $this->error;
    }

    /**
     * @author Jan Pokorný
     * @return bool
     */
    public function getVisible() {
        return $this->visible;
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
    public function getControlHtml() {
        $html = $this->getLabelHtml();
        return $html .= $this->getField();
    }

    /**
     * Vrátí HTML element <label> pro daný field
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param $class string
     * @return string
     */
    public function getLabelHtml($class = null) {
        $classAttribute = KT::issetAndNotEmpty($class) ? " class=\"$class\"" : "";
        return "<label for=\"" . $this->getAttrValueByName("id") . "\"$classAttribute>" . $this->getLabel() . "</label>";
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
        // TODO: sanitace
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
     * @author Martin Hlaváč
     * @return mixed
     */
    public function getConvertedValue() {
        return $this->getValue();
    }

    /**
     * Vrátí field value na základě zaslaného postu, getu, prefixu nebo nastaveného value
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getValue() {
        if (isset($this->value)) {
            return $this->value;
        }
        return $this->value = filter_var($this->getCleanValue(), $this->getFilterSanitize());
    }

    /**
     * @deprecated since version 1.7 
     * @see getValue()
     */
    public function getFieldValue() {
        return $this->getValue();
    }

    /**
     * Vrátí přímo čistou hodnotu bez zpracování - sanitizace
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getCleanValue() {
        if (isset($this->cleanValue)) {
            return $this->cleanValue;
        }
        $name = $this->getName();

        $postPrefix = $this->getPostPrefix();
        if (KT::issetAndNotEmpty($postPrefix)) {
            $postValues = filter_input(INPUT_POST, $postPrefix, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            if (KT::arrayIssetAndNotEmpty($postValues)) {
                $postPrefixValue = KT::arrayTryGetValue($postValues, $name);
                if (isset($postPrefixValue)) {
                    return $this->cleanValue = $postPrefixValue;
                }
            }
            $getValues = filter_input(INPUT_GET, $postPrefix, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            if (KT::arrayIssetAndNotEmpty($getValues)) {
                $getPrefixValue = KT::arrayTryGetValue($getValues, $name);
                if (isset($getPrefixValue)) {
                    return $this->cleanValue = $getPrefixValue;
                }
            }
        }

        $postValue = KT::arrayTryGetValue($_POST, $name);
        if (isset($postValue)) {
            return $this->cleanValue = $postValue;
        }
        $getValue = KT::arrayTryGetValue($_GET, $name);
        if (isset($getValue)) {
            return $this->cleanValue = $getValue;
        }

        $defaultValue = $this->getDefaultValue();
        if (isset($defaultValue)) {
            return $this->cleanValue = $defaultValue;
        }
        return $this->cleanValue = "";
    }

    /**
     * Vrátí přímo výchozí hodnotu fieldu, pokud je zadána
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    protected function getDefaultValue() {
        return $this->defaultValue;
    }

    /**
     * Vrátí nastavený sanatizační kód pro (výpis) hodnotu(y)
     * Pozn.: výchozí je FILTER_SANITIZE_SPECIAL_CHARS
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public function getFilterSanitize() {
        return $this->filterSanitize;
    }

	/**
	 * Vypíše chybovou hlášku, pokud je zadána
	 *
	 * @author Martin Hlaváč
	 * @link http://www.ktstudio.cz
	 */
	public function renderErrorMsg() {
    	if ($this->hasErrorMsg()) {
    		echo $this->getHtmlErrorMsg();
	    }
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
