<?php

abstract class KT_Slider_Field_Base extends KT_Field {

    private $minValue = 0;
    private $maxValue = null;
    private $step = 1;

    /**
     * Obecná abstractní třída pro field vycházející z jQuery UI Slider pro výběr čísla
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return KT_Text_Field
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    // --- gettery a settery ---------------

    /**
     * @return int
     */
    public function getMinValue() {
        return $this->minValue;
    }

    /**
     * Nastaví minimální číslo, na kterém bude slider začínat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $minNumber
     * @return \KT_Slider_Field
     */
    public function setMinValue($minNumber) {
        $this->minValue = $minNumber;
        $this->addRule(KT_Field_Validator::MIN_NUMBER, sprintf(__("Minimální hodnota je %d"), $minNumber), $minNumber);
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxValue() {
        return $this->maxValue;
    }

    /**
     * Nastaví maximální číslo, na kterém bude slider končit
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $maxNumber
     * @return \KT_Slider_Field
     */
    public function setMaxValue($maxNumber) {
        $this->maxValue = $maxNumber;
        $this->addRule(KT_Field_Validator::MAX_NUMBER, sprintf(__("Maximální hodnota je %d"), $maxNumber), $maxNumber);
        return $this;
    }

    /**
     * @return int
     */
    public function getStep() {
        return $this->step;
    }

    /**
     * Nastaví velikost "kroku", po kterém bude bude možné zvyšovat / zmenšovat hodnotu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $step
     * @return \KT_Slider_Field
     */
    public function setStep($step) {
        $this->step = $step;
        return $this;
    }
}