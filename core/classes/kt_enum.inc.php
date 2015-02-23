<?php

/**
 * Základ pro vlastní pevné (programové) výčtové typy
 *
 * Na vlastním třídě je možné definovat vlastní konstanty typu klíč->hodnota (např. VALUE1 = 1; atd.)
 * (a)nebo zadat vlastní hodnoty a klíče pomocí @see CustomKeyValues
 *
 * Inspirace v: Enum support in PHP, Johan Ohlin, 15 Nov 2013, http://www.codeproject.com/Articles/683009/Enum-support-in-PHP
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Enum {

    private $currentValue;
    private $currentKey;
    private $customKeyValues;
    private $translates = array();

    function __construct($currentValue = null, $currentKey = null, array $customKeyValues = null) {
        if (KT::issetAndNotEmpty($customKeyValues)) {
            $this->setCustomKeyValues($customKeyValues);
        }
        if (KT::issetAndNotEmpty($currentKey)) {
            $this->setCurrentKey($currentKey);
        }
        if (KT::issetAndNotEmpty($currentValue)) {
            $this->setCurrentValue($currentValue);
        }
    }

    /**
     * Aktuální (vybraný) hodnota klíče (názvu, resp. konstanty)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int|string
     */
    public final function getCurrentValue() {
        return $this->currentValue;
    }

    /**
     * Nastavení aktuální (vybrané) hodnoty klíče (názvu, resp. konstanty) a zároveň klíče pro hodnotu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int|string $newValue
     * @return KT_Enum
     * @throws KT_Not_Supported_Exception
     */
    public final function setCurrentValue($newValue) {
        foreach ($this->getAllKeyValues() as $key => $value) {
            if ($newValue == $value) {
                $this->currentKey = $key;
                $this->currentValue = $value;
                return $this;
            }
        }
        throw new KT_Not_Supported_Exception(__("Zadána neplatná hodnota výčtu: $newValue!", KT_DOMAIN));
    }

    /**
     * Aktuální (vybraný) klíč (název, resp. konstanta)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public final function getCurrentKey() {
        return $this->currentKey;
    }

    /**
     * Vrátí překlad pro vybranou hodnotu pokud je/jsou překlad(y) k dispozici
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|null|KT_EMPTY_TEXT
     */
    public function getCurrentTranslate() {
        if ($this->isTranslates()) {
            $translates = $this->getTranslates();
            return $translates[$this->getCurrentValue()];
        }
        return KT_EMPTY_SYMBOL;
    }

    /**
     * Nastavení aktuálního (vybraného) klíče (názvu, resp. konstanty) a zároveň hodnoty pro klíč
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int|string $newKey
     * @return KT_Enum
     * @throws KT_Not_Supported_Exception
     */
    public final function setCurrentKey($newKey) {
        foreach ($this->getAllKeyValues() as $key => $value) {
            if ($newKey == $key) {
                $this->currentKey = $key;
                $this->currentValue = $value;
                return $this;
            }
        }
        throw new KT_Not_Supported_Exception(__("Zadán neplatný klíč výčtu: $newKey!", KT_DOMAIN));
    }

    /**
     * Vrátí pole pouze s vlastními hodnotami s klíči pokud je zadáno
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    function getCustomKeyValues() {
        return $this->customKeyValues;
    }

    /**
     * Nastaví pole pouze s vlastními hodnotami s klíči pokud je zadáno
     * Pozn.: mělo by se jednat o tvar: key (string) -> hodnota (int|string)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $customKeys
     */
    function setCustomKeyValues(array $customKeys) {
        $this->customKeyValues = $customKeys;
    }

    /**
     * Kontrola, zda se jedná o platnou hodnotu klíče (názvu, reps. konstanty, či vlastní hodnoty)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int|string $checkedValue
     * @return boolean
     */
    public final function isValidValue($checkedValue) {
        $customKeyValues = $this->getCustomKeyValues();
        if (KT::issetAndNotEmpty($customKeyValues) && count($customKeyValues) > 0) {
            if (in_array($checkedValue, $customKeyValues)) {
                return true;
            }
        }
        $reflectionClass = new ReflectionClass(get_class($this));
        foreach ($reflectionClass->getConstants() as $existingValue) {
            if ($checkedValue == $existingValue) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kontrola, zda se jedná o platný klíč (název, reps. konstantu, či vlastní klíč)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $checkedKey
     * @return boolean
     */
    public final function isValidKey($checkedKey) {
        $customKeyValues = $this->getCustomKeyValues();
        if (KT::issetAndNotEmpty($customKeyValues) && count($customKeyValues) > 0) {
            if (array_key_exists($checkedKey, $customKeyValues)) {
                return true;
            }
        }
        $reflectionClass = new ReflectionClass(get_class($this));
        foreach ($reflectionClass->getConstants() as $key => $value) {
            if ($checkedKey == $key) {
                return true;
            }
        }
        return false;
    }

    /**
     * Vrací pole ve tvaru hodnota => klíč (název) podle konstant a vlastních hodnot a klíčů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public final function getAllValueKeys() {
        $values = array();
        $customKeyValues = $this->getCustomKeyValues();
        if (KT::issetAndNotEmpty($customKeyValues) && count($customKeyValues) > 0) {
            foreach ($customKeyValues as $key => $value) {
                $values[$value] = $key;
            }
        }
        $reflectionClass = new ReflectionClass(get_class($this));
        foreach ($reflectionClass->getConstants() as $key => $value) {
            $values[$value] = $key;
        }
        return $values;
    }

    /**
     * Vrací pole ve tvaru klíč (název) => hodnota podle konstant a vlastních hodnot a klíčů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public final function getAllKeyValues() {
        $values = array();
        $customKeyValues = $this->getCustomKeyValues();
        if (KT::issetAndNotEmpty($customKeyValues) && count($customKeyValues) > 0) {
            foreach ($customKeyValues as $key => $value) {
                $values[$key] = $value;
            }
        }
        $reflectionClass = new ReflectionClass(get_class($this));
        foreach ($reflectionClass->getConstants() as $key => $value) {
            $values[$key] = $value;
        }
        return $values;
    }

    /**
     * Označení, či kontrola, zda jsou k dispozici, resp. zadané překlady hodnot
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function isTranslates() {
        return count($this->getTranslates()) > 0;
    }

    /**
     * Vrátí překlady hodnot, pokud jsou zadané
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public function getTranslates() {
        return $this->translates;
    }

    /**
     * Nastaví překlady hodnot, které by měly být ve tvaru "hodnota" => "překlad",
     * kde "hodnota" je hodnota buď konstanty nebo v kolekce @see customKeyValues
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $translates
     */
    protected function setTranslates(array $translates) {
        $this->translates = $translates;
    }

    /**
     * Přepasaný systémový ToString tak, aby vracel lepší výpis pro ladění apod.
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function __toString() {
        $className = get_class($this);
        $currentKey = $this->getCurrentKey();
        $currentValue = $this->getCurrentValue();
        return "[$className: $currentKey => $currentValue]";
    }

}
