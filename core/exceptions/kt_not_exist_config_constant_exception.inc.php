<?php

/**
 * Výjímka určující, že volaná konstanta není na configu dostupná
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Not_Exist_Config_Constant_Exception extends Exception {

    private $constantName;

    public function __construct($constantName, $code = 0, Exception $previous = null) {
        $this->targetName = $constantName;
        $message = sprintf(__("Neexistující konstanta na configu: \"%s\"", "KT_CORE_DOMAIN"), $constantName);
        parent::__construct($message, $code, $previous);
    }

    public function getConstantName() {
        return $this->constantName;
    }

    public function __toString() {
        return sprintf(__("Neexistující konstanta na configu: %s \n %s", "KT_CORE_DOMAIN"), $this->getTargetName(), parent::__toString());
    }

}
