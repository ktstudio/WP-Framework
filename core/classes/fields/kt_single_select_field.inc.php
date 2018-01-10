<?php

/**
 * Třída pro single výběrový prvek (select) za pomocí knihovny chosen
 * 
 * @deprecated ve vývoji
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Single_Select_Field extends KT_Select_Field {

    const FIELD_TYPE = "singleselect";
    const PRIMARY_CLASS_IDENTIFICATOR = "singleSelect";
    const SECONDARY_CLASS_IDENTIFICATOR = "singleSelectDeselect";

    private $allowDeselect = false;

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
        $this->addAttrClass(self::PRIMARY_CLASS_IDENTIFICATOR);
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
     * Vrátí označení, zda je možné odvybírat hodnoty = rušit výběr
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function getAllowDeselect() {
        return $this->allowDeselect;
    }

    /**
     * Nastavení označení, zda je možné odvybírat hodnoty = rušit výběr
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $allowDeselect
     * @return \KT_Single_Select_Field
     */
    public function setAllowDeselect($allowDeselect = true) {
        $this->isInAllowDeselectChange = true;
        $this->allowDeselect = $allowDeselect;
        if ($allowDeselect) {
            parent::removeAttrClass(self::PRIMARY_CLASS_IDENTIFICATOR);
            $this->addAttrClass(self::SECONDARY_CLASS_IDENTIFICATOR);
            if ($this->getFirstEmpty() !== "") {
                $this->setFirstEmpty("");
            }
        } else {
            parent::removeAttrClass(self::SECONDARY_CLASS_IDENTIFICATOR);
            $this->addAttrClass(self::PRIMARY_CLASS_IDENTIFICATOR);
        }
        $this->isInAllowDeselectChange = false;
        return $this;
    }

    // --- veřejné funkce -----------------

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
        if ($class == self::PRIMARY_CLASS_IDENTIFICATOR) {
            throw new KT_Not_Supported_Exception("Remove Class " . self::PRIMARY_CLASS_IDENTIFICATOR);
        }
        if ($class == self::SECONDARY_CLASS_IDENTIFICATOR) {
            throw new KT_Not_Supported_Exception("Remove Class " . self::SECONDARY_CLASS_IDENTIFICATOR);
        }
        return parent::removeAttrClass($class);
    }

    // --- neveřejné funkce ------------------
}
