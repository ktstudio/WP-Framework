<?php

/**
 * Pole formuláře typu WP nonce
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Nonce_Field extends KT_Field {

    const FIELD_TYPE = "nonce";
    const DEFAULT_NONCE_NAME = "kt_wpnonce";

    private $action = null;
    protected $visible = false;

    /**
     * @param string $action - WP nonce akce
     * @param string $name - hash v poli
     * @param string $label - popisek v HTML
     */
    public function __construct($action, $name, $label) {
        parent::__construct($name, $label);
        if (KT::issetAndNotEmpty($action)) {
            $this->action = $action;
        } else {
            throw new KT_Not_Supported_Exception("Empty Nonce Action");
        }
    }

    // --- getry & setry ------------------------

    /**
     * Vrátí identifikátor pole
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
     * Vrátí WP nonce akci (klíč)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    // --- veřejné metody ------------------------

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     * @example echo $this->getField();
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderField() {
        echo $this->getField();
    }

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getField() {
        if (!defined("DONOTCACHEPAGE")) {
            define("DONOTCACHEPAGE", true);
        }
        $html = wp_nonce_field($this->getAction(), $this->getName() ? : self::DEFAULT_NONCE_NAME, true, false);
        return $html;
    }

    /**
     * Provede validaci fieldu na základě zadaných podmínek a WP nonce kontroly
     * V případě, že se jedná o chybu, nastaví automaticky fieldu hlášku s errorem z validatoru
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function validate() {
        return $this->nonceValidate() && parent::Validate();
    }

    /**
     * WP nonce validace pole 
     * @see wp_verify_nonce
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function nonceValidate() {
        $value = $this->getValue();
        if (wp_verify_nonce($value, $this->getAction())) {
            return true;
        }
        $this->setError(__("Error processing - call", "KT_CORE_DOMAIN"));
        return false;
    }

}
