<?php

/**
 * Výjímka určující, že název, klíč apod. jsou duplicitní
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Duplicate_Exception extends Exception {

    private $targetName;

    public function __construct($targetName, $code = 0, Exception $previous = null) {
        $this->targetName = $targetName;
        $message = __("Duplicitní položka: \"$targetName\"", KT_DOMAIN);
        parent::__construct($message, $code, $previous);
    }

    public function getTargetName() {
        return $this->targetName;
    }

    public function __toString() {
        return __("Duplicitní položka: ", KT_DOMAIN) . $this->getTargetName() . " (klíč, název apod.)\n" . parent::__toString();
    }

}
