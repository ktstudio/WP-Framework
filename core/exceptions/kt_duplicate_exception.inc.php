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
        $message = __("Duplicate entry \"$targetName\"", "KT_CORE_DOMAIN");
        parent::__construct($message, $code, $previous);
    }

    public function getTargetName() {
        return $this->targetName;
    }

    public function __toString() {
        return __("Duplicate entry: ", "KT_CORE_DOMAIN") . $this->getTargetName() . " (key, name etc.)\n" . parent::__toString();
    }

}
