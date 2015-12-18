<?php

/**
 * Třída pro definici CRUD sloupce (z tabulky v DB)
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_CRUD_Column {

    const TEXT = "text";
    const INT = "int";
    const BIGINT = "bigint";
    const FLOAT = "float";
    const DATE = "date";
    const DATETIME = "datetime";

    private $name = null;
    private $type = null;
    private $nullable = false;
    private $value = null;
    private $allowStripSlashed = false;

    public function __construct($name, $type = self::TEXT, $nullable = false) {
        $this->setName($name)
                ->setType($type)
                ->setNullable($nullable);
    }

    // --- gettery a settery ------------------

    /**
     * Vrátí název sloupce modelu v DB
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Nastaví název sloupce modelu v DB
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $name
     * @return \KT_CRUD_Column
     */
    protected function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Vrátí typ sloupce v DB - řídí se konstanty třídy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Vrátí, zda je na sloupci povoleno ukládání hodnoty s uvozovkami
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getAllowStripSlashed() {
        return $this->allowStripSlashed;
    }

    /**
     * Nastaví, zda se má na sloupci povolit ukládání hodnoty s uvozovkami
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $allowStripSlashed
     * @return \KT_CRUD_Column
     */
    public function setAllowStripSlashed($allowStripSlashed) {
        $this->allowStripSlashed = $allowStripSlashed;
        return $this;
    }

    /**
     * Nastaví typ sloupce v DB - řídí se konstanty třídy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $type
     * @return \KT_CRUD_Column
     */
    protected function setType($type) {
        switch ($type) {
            case self::INT:
            case self::BIGINT:
            case self::FLOAT:
            case self::DATE:
            case self::DATETIME:
                $this->type = $type;
                break;

            default:
                $this->type = self::TEXT;
                break;
        }

        return $this;
    }

    /**
     * Vrátí, zda může být / má být sloupec v případě nevyplněné hodnoty
     * nastaven jako null.
     * 
     * @return boolean
     */
    public function getNullable() {
        return $this->nullable;
    }

    /**
     * Nastaví, zda může být / má být sloupec v případě nevyplněné hodnoty
     * nastaven jako null.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $nullable
     * @return \KT_CRUD_Column
     */
    protected function setNullable($nullable) {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * Nastaví hodnotu sloupce která je v DB nebo má být nastavena v DB
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Nastaví hodnotu sloupce která je v DB nebo má být nastavena v DB
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param mixed $value
     * @return \KT_CRUD_Column
     */
    public function setValue($value) {
        if ($this->getNullable() && $value === "") {
            $this->value = null;
        } else {
            if ($this->getAllowStripSlashed()) {
                $this->value = stripslashes($value);
            } else {
                $this->value = $value;
            }
        }
        return $this;
    }

}
