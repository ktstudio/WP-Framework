<?php

/**
 * Výjímka určující, že volaná konstanta není na configu dostupná
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Not_Exist_Config_Constant_Exception extends Exception {

    public function __construct($constantName, $configName, $functionName, $code = 0, Exception $previous = null) {
        $message = sprintf(__("Neexistující konstanta na configu: \"%s\" na základě volání funkce: \"%s->%s\"", "KT_CORE_DOMAIN"), $constantName, $configName, $functionName);
        parent::__construct($message, $code, $previous);
    }

}
