<?php

/**
 * Výjímka určující, že volaná konstanta není na configu dostupná
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Not_Exist_Config_Constant_Exception extends Exception {

    public function __construct($constantName, $configName, $functionName, $code = 0, Exception $previous = null) {
        $message = sprintf(__("Nonexistent constant in the config \"% s \" based on the function call: \"% s ->% s \"", "KT_CORE_DOMAIN"), $constantName, $configName, $functionName);
        parent::__construct($message, $code, $previous);
    }

}
