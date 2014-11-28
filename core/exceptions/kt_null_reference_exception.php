<?php

/**
 * Výjímka určující, že zadaná hodnota, resp. referenec je NULL (, či prázdná)
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Null_Reference_Exception extends Exception {

    private $referenceName;

    public function __construct($referenceName, $code = 0, Exception $previous = null) {
        $this->referenceName = $referenceName;
        $message = __("Zadaný argument je nepřiřazený nebo NULL!", KT_DOMAIN);
        parent::__construct($message, $code, $previous);
    }

    public function getReferenceName() {
        return $this->referenceName;
    }

    public function __toString() {
        return __("Prázdná hodnota pro: ", KT_DOMAIN) . $this->getReferenceName() . "\n" . parent::__toString();
    }

}
