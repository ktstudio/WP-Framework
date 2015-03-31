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
        $this->setValue(self::NO);

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

        $html .= "<div {$this->getAttrClassString()}>";
        $html .= "<span for=\"{$this->getAttrValueByName("id")}\" {$this->getAttrClassString()} title=\"{$this->getAfterNameValue("title")}\"></span>";
        $html .= "<input type=\"hidden\" ";
        $html .= $this->getBasicHtml();
        $html .= " value=\"{$this->getValue()}\" ";
        $html .= "/>";
        $html .= "</div>";
        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
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
        $fieldValue = parent::getValue();

        return self::getSwitchConvertedValue($fieldValue);
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
        return self::FIELD_TYPE;
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
            throw new InvalidArgumentException(__("Hodnota \"$value\" není logického typu", KT_DOMAIN));
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
            throw new InvalidArgumentException(__("Hodnota \"$value\" není typu KT Switch pole", KT_DOMAIN));
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
            return __("Ano", KT_DOMAIN);
        } elseif ($value == KT_Switch_Field::NO || $value === false || $value === 0) {
            return __("Ne", KT_DOMAIN);
        } else {
            echo KT_EMPTY_SYMBOL;
        }
    }

}
