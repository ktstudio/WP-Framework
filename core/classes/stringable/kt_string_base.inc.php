<?php

/**
 * Třída pro práci se stringem.
 * Obstará sanitizace.
 * Metody převzaty od Martin Hlaváč
 *
 * @author Jan Pokorný
 */
class KT_String_Base implements KT_Stringable {

    /**
     *
     * @var string Čistá hodnota 
     */
    protected $unsafeValue;

    /**
     *
     * @var string Hodnota po sanitizaci 
     */
    protected $value;

    /**
     *
     * @var string Hodnota po sanizizace pro attr 
     */
    protected $attrValue;

    /**
     * 
     * @param string $value
     */
    public function __construct($value) {
        if (empty($value)) {
            $value = "";
        }
        if (!is_string($value)) {
            throw new Exception("Parametr must be string");
        }
        $this->unsafeValue = $value;
    }

    /**
     * 
     * @return string
     */
    public function __toString() {
        return $this->getValue();
    }

    /**
     * Vrátí původní hodnotu
     * 
     * @return string
     */
    public function getUnsafeValue() {
        return $this->unsafeValue;
    }

    /**
     * Vratí hodnotu po sanitizaci
     * 
     * @return string
     */
    public function getValue() {
        if (!$this->value) {
            $this->value = htmlspecialchars($this->unsafeValue);
        }
        return $this->value;
    }

    /**
     * Vrátí hodnotu pro attribute
     * 
     * @return string
     */
    public function getAttrValue() {
        if (!$this->attrValue) {
            $this->attrValue = esc_attr($this->unsafeValue);
        }
        return $this->attrValue;
    }

}
