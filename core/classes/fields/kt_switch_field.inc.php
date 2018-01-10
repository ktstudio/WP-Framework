<?php

class KT_Switch_Field extends KT_Field {

    const FIELD_TYPE = "switch switch-toggle";
    const YES = "1";
    const NO = "0";

    /**
     * Založení objektu typu Switch
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);

        $this->addAttrClass(self::FIELD_TYPE);
        $this->setDefaultValue(self::NO);
        $this->setFilterSanitize(FILTER_SANITIZE_NUMBER_INT);

        return $this;
    }

    // --- gettery -----------------------

    /**
     * Vrátí typ fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    public function getValue() {
        $value = parent::getValue();
        if (self::isSwitchValue($value)) {
            return $value;
        }
        return self::NO;
    }

    /**
     * Vrátí hodnotu ve fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param bolean $original - má vrátít originální hodnotu v DB nebo hodnotou pro zobrazení
     * @return null
     */
    public function getConvertedValue() {
        $value = parent::getValue();
        return self::getSwitchConvertedValue($value);
    }

    // --- veřejné funkce -----------------------

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
        $dataOn = __("Yes", "KT_CORE_DOMAIN");
        $dataOff = __("No", "KT_CORE_DOMAIN");
        
        $html = "<div {$this->getAttrClassString()}>";
        $html .= "<span for=\"{$this->getAttrValueByName("id")}\" {$this->getAttrClassString()} title=\"{$this->getToolTip()}\" data-on=\"$dataOn\" data-off=\"$dataOff\"></span>";
        $html .= "<input type=\"hidden\" ";
        $html .= $this->getBasicHtml();
        $html .= " value=\"{$this->getValue()}\" />";
        $html .= "</div>";
        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }
        return $html;
    }

    // --- statické funkce -----------------

    /**
     * Převod logického hodnoty na hodnotu pro KT_Switch_Field
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $value
     * @return string
     * @throws InvalidArgumentException
     */
    public static function convertBooleanToSwitch($value) {
        if (KT::issetAndNotEmpty($value)) {
            if ($value == true) {
                return KT_Switch_Field::YES;
            } elseif ($value == false) {
                return KT_Switch_Field::NO;
            }
            throw new InvalidArgumentException(sprintf(__("Value \"%s\" is not a logical type", "KT_CORE_DOMAIN", $value)));
        }
        return null;
    }

    /**
     * Převod KT_Switch_Field hodnoty na logickou hodnotu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $value
     * @return boolean
     * @throws InvalidArgumentException
     */
    public static function convertSwitchToBoolean($value) {
        if (KT::issetAndNotEmpty($value)) {
            if ($value == KT_Switch_Field::YES) {
                return true;
            } elseif ($value == KT_Switch_Field::NO) {
                return false;
            }
            throw new InvalidArgumentException(sprintf(__("Value \"%s\" is not type of KT Switch array", "KT_CORE_DOMAIN"), $value));
        }
        return null;
    }

    /**
     * Vypíše hodnotu KT_Switch_Field, či boolean jako text, tedy Ano/Ne
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string|boolean $value
     * @throws InvalidArgumentException
     */
    public static function getSwitchConvertedValue($value) {
        if ($value == KT_Switch_Field::YES || $value === true || $value === 1) {
            return __("Yes", "KT_CORE_DOMAIN");
        } elseif ($value == KT_Switch_Field::NO || $value === false || $value === 0) {
            return __("No", "KT_CORE_DOMAIN");
        } else {
            echo KT_EMPTY_SYMBOL;
        }
    }

    /**
     * Zkontroluje, zda se jedná o (přímou) hodnotu KT_Switch_Field (0 nebo 1)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string|int $value
     * @return boolean
     */
    public static function isSwitchValue($value) {
        if ($value == KT_Switch_Field::YES || $value === 1 || $value == KT_Switch_Field::NO || $value === 0) {
            return true;
        }
        return false;
    }

}
