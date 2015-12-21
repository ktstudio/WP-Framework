<?php

/**
 * Třída pro multi výběrový prvek (select) za pomocí knihovny chosen
 * 
 * @deprecated ve vývoji
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Multi_Select_Field extends KT_Select_Field {

    const FIELD_TYPE = "multiselect";
    const CLASS_IDENTIFICATOR = "multiSelect";

    /**
     * Založení objektu typu Select
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->addAttrClass(self::CLASS_IDENTIFICATOR);
        $this->setFilterSanitize(FILTER_SANITIZE_STRING);
    }

    // --- getry & settery ------------------------

    /**
     * Vrátí unikátní název typu prvku v rámci formulářů ve FW
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * Vrátí field value na základě zaslaného postu, getu, prefixu nebo nastaveného value
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public function getValue() {
        $value = parent::getCleanValue();
        if (KT::arrayIsSerialized($value)) {
            return unserialize($value);
        }
        return $value;
    }

    // --- veřejné funkce -----------------

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getField() {
        $html = "<select multiple=\"true\" {$this->getBasicHtml()}>";
        $html .= $this->getOptionsContent();
        $html .= "</select>";
        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }
        return $html;
    }

    /**
     * Odebrání třídy vč. kontroly na povinnou třídu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $class
     * @return string
     * @throws KT_Not_Supported_Exception
     */
    public function removeAttrClass($class) {
        if ($class == self::CLASS_IDENTIFICATOR) {
            throw new KT_Not_Supported_Exception("Remove Class " . self::CLASS_IDENTIFICATOR);
        }
        return parent::removeAttrClass($class);
    }

    // --- neveřejné funkce ------------------

    /**
     * Zajistí, aby hodnoty byly zpracovány jako pole
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    protected function getAfterNameValue() {
        return "[]";
    }

    /**
     * Vrátí HTML s jedním option pro celou kolekci
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $optionKey
     * @param string $optionValue
     * @return string
     */
    protected function getSignleOptionItem($optionKey, $optionValue) {
        $selected = null;
        $values = $this->getValue();
        if (KT::arrayIssetAndNotEmpty($values)) {
            if (in_array($optionKey, $values)) {
                $selected = " selected=\"selected\"";
            }
        }
        return $html = "<option value=\"$optionKey\"$selected>$optionValue</option>";
    }

}
