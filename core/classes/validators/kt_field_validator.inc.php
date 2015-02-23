<?php

class KT_Field_Validator {

    const REQUIRED = 'required'; // hodnota musí být vyplněna
    const INTEGER = 'integer'; // hodnota musí být celočíselného typu
    const FLOAT = 'float'; // hodnota musí být typu float - desetiné číslo
    const EMAIL = 'email'; // hodnota musí být správný formát emailu
    const URL = 'url'; // hodnota musí být správný formát url
    const RANGE = 'range'; // hodnota musí být jako číslo v rozsahu zadaném pomocí pole v $param
    const LENGTH = 'length'; // hodnota musí být přesného počtu znaků zadaném v $param
    const MAX_LENGTH = 'maxLength'; // hodnota nesmí být větší než v zadaném $param
    const MIN_LENGTH = 'minLength'; // hodnota nesmí být menší než v zadaném $param
    const MAX_NUMBER = 'maxNumber'; // hodnota nesmí být větší číslo než v zadaném $param
    const MIN_NUMBER = 'minNumber'; // hodnota nesmí být menší číslo než v zadaném $param
    const REGULAR = "regular"; // Hodnota musí odpovídat regulárnímu výrazu

    private $condition = null;
    private $message = null;
    private $param = null;

    /**
     * Základní validátor pro validaci dat chodící prostřednictvím KT_Formu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $condition - pouze constanty třídy
     * @param string $message - chybová hlášky
     * @param type $param - v případě potřeby předat validátoru parametry pro validaci
     * @return \KT_Field_Validator
     * @throws KT_Not_Set_Argument_Exception
     */
    public function __construct($condition, $message, $param = null) {

        $this->setCondition($condition)
                ->setMessage($message)
                ->setParam($param);

        return $this;
    }

    // --- gettery -----------------------

    /**
     * @return string
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return mixed - int || array
     */
    public function getParam() {
        return $this->param;
    }

    // --- settery -----------------------

    /**
     * Která podmínká a validace bude na fieldu aplikována
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $condition
     */
    private function setCondition($condition) {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Která zpráva bude uživateli zobrazena při nesplnění podmínky validace
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $message
     */
    private function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * V případě nutnosti předat validátoru parametr
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param mixed $param int || array
     */
    private function setParam($param = null) {
        $this->param = $param;

        return $this;
    }

    // --- veřejné metody ----------------

    /**
     * Dle nastavení podmínky validace zavolá příslušnou funkci a vrátí její výsledek
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $value - validovaná hodnota
     * @return boolean - zda je validní nebo ne
     */
    public function validate($value) {
        $function = $this->getCondition();
        return self::$function($value);
    }

    // --- privátní metody ------------------

    /**
     * Musí být hodnota vyplněna
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     */
    private function required($value) {
        if ($value != '') {
            return true;
        }

        return false;
    }

    /**
     * Je hodnota integer?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     */
    private function integer($value) {
        if (empty($value)) {
            return true;
        }

        $value = (int) $value;

        if ($value == 0) {
            return false;
        }

        if (is_int((int) $value)) {
            return true;
        }

        return false;
    }

    /**
     * Je hodnota float?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     */
    private function float($value) {
        if ($value == '') {
            return true;
        }

        if (is_numeric($value)) {
            return true;
        }

        return false;
    }

    /**
     * Je hodnota správný formát emailu?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     */
    private function email($value) {
        if (empty($value)) {
            return true;
        }

        if (is_email($value)) {
            return true;
        }

        return false;
    }

    /**
     * Je zadaná hodnota sprývný url formát?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     */
    private function url($value) {
        if (empty($value)) {
            return true;
        }

        if (preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $value)) {
            return true;
        }

        return false;
    }

    /**
     * Je hodnota v definovaném rozsahu od max do min?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     * @throws InvalidArgumentException
     */
    private function range($value) {
        if ($value == '') {
            return true;
        }

        if (!is_array($this->param)) {
            throw new InvalidArgumentException(__("Hodnota pro kontrolu rozsahu musí být typu pole.", KT_DOMAIN));
        }

        if (($value >= $this->param[0]) && ($value <= $this->param[1])) {
            return true;
        }

        return false;
    }

    /**
     * Je hodnota přesné délky znaků?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     * @throws InvalidArgumentException
     */
    private function length($value) {

        if ($value == '') {
            return true;
        }

        if (KT::issetAndNotEmpty($this->param)) {
            if (strlen($value) == $this->param) {
                return true;
            }

            return false;
        }

        throw new InvalidArgumentException(__("Hodnota pro kontrolu délky musí být přiřazena.", KT_DOMAIN));
    }

    /**
     * není délka hodnoty vyšší než nastavený parametr?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $value - validovaná hodnota
     * @return boolean
     * @throws KT_Not_Set_Argument_Exception
     */
    private function maxLength($value) {
        if ($value == '') {
            return true;
        }

        $param = $this->getParam();

        if (KT::issetAndNotEmpty($param) && self::integer((int) $param)) {
            if (strlen($value) <= $param) {
                return true;
            }

            return false;
        }

        throw new KT_Not_Set_Argument_Exception(__("Parametr pro kontrolu maximální délky musí být přiřazen a číselného typu.", KT_DOMAIN));
    }

    /**
     * Není délka hodnoty menší než nastavený parametr?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $value
     * @return boolean
     * @throws KT_Not_Set_Argument_Exception
     */
    private function minLength($value) {
        if ($value == '') {
            return true;
        }

        $param = $this->getParam();

        if (KT::issetAndNotEmpty($param) && self::integer((int) $param)) {
            if (strlen($value) >= $param) {
                return true;
            }

            return false;
        }

        throw new KT_Not_Set_Argument_Exception(__("Parametr pro kontrolu minimální délky musí být přiřazen a číselného typu.", KT_DOMAIN));
    }

    /**
     * Není hodnota vyšší než maximální číslo v parametru?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $value
     * @return boolean
     * @throws KT_Not_Set_Argument_Exception
     */
    private function maxNumber($value) {
        if ($value == '') {
            return true;
        }

        $param = $this->getParam();

        if (!self::integer($param)) {
            throw new KT_Not_Set_Argument_Exception(__("Parametr pro kontrolu maximálního čísla musí být číselného typu.", KT_DOMAIN));
        }

        if (!self::float($value)) {
            return false;
        }

        if ($value <= $param) {
            return true;
        }

        return false;
    }

    /**
     * Je hodnta větší číslo než nastavený parametr?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $value
     * @return boolean
     * @throws KT_Not_Set_Argument_Exception
     */
    private function minNumber($value) {
        if ($value == '') {
            return true;
        }

        $param = $this->getParam();

        if (!self::integer($param)) {
            throw new KT_Not_Set_Argument_Exception(__("Parametr pro kontrolu minimálního čísla musí být číselného typu.", KT_DOMAIN));
        }

        if (!self::float($value)) {
            return false;
        }

        if ($value >= $param) {
            return true;
        }

        return false;
    }

    /**
     * Odpovídá hodnota zadanému regulárnímu výrazu?
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $value
     * @return boolean
     */
    public function regular($value) {
        $param = $this->getParam();

        $regResult = preg_match("/" . $param . "/", $value);

        switch ($regResult) {
            case 1:
                return true;

            default:
                return false;
        }
    }

}
