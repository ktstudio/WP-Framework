<?php

/**
 * Výjímka určující, že název (php) souboru neodpovídá stanoveným (KT) konvencím
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Bad_File_Name_Exception extends Exception {

    private $fileName;

    public function __construct($fileName, $code = 0, Exception $previous = null) {
        $this->fileName = $fileName;
        $message = __("Název souboru neodpovídá stanoveným konvencím, viz. KT předpona a INC přípona...", KT_DOMAIN);
        parent::__construct($message, $code, $previous);
    }

    public function getFileName() {
        return $this->fileName;
    }

    public function __toString() {
        return __("Název souboru: ", KT_DOMAIN) . $this->getFileName() . "\n" . parent::__toString();
    }

}
