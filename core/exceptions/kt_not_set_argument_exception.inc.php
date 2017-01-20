<?php

/**
 * Výjímka určující, že zadaný argument je nepřiřazený nebo NULL
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Not_Set_Argument_Exception extends Exception {

    private $argumentName;

    public function __construct($argumentName, $code = 0, Exception $previous = null) {
        $this->argumentName = $argumentName;
        $message = __("The argument is unassigned or NULL!", "KT_CORE_DOMAIN");
        parent::__construct($message, $code, $previous);
    }

    public function getArgumentName() {
        return $this->argumentName;
    }

    public function __toString() {
        return sprintf(__("Argument: %s \n %s", "KT_CORE_DOMAIN"), $this->getArgumentName(), parent::__toString());
    }

}
