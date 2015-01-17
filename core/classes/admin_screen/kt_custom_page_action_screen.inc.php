<?php

class KT_Custom_Page_Action_Screen {

    private $actionName = null;
    private $actionValue = null;
    private $callBackFunction = null;

    /**
     * Základní třída pro práci a definici parametrů pro Custom Metaboxes Screens
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $actionName - název GET parametru
     * @param type $actionValue - hodnota GET parametru
     * @param type $callBackFunction - funkce, která se zavolá pro vykreslení obsahu
     */
    public function __construct($actionName, $actionValue, $callBackFunction = null) {
        $this->setActionName($actionName);
        $this->setActionValue($actionValue);
        $this->setCallBackFunction($callBackFunction);
    }

    // --- gettery -----------------

    /**
     * @return string
     */
    public function getActionName() {
        return $this->actionName;
    }

    /**
     * @return string
     */
    public function getActionValue() {
        return $this->actionValue;
    }

    /**
     * @return mixed string || array
     */
    public function getCallBackFunction() {
        return $this->callBackFunction;
    }

    // --- settery -----------------

    /**
     * Nastaví název GET parametru pro identifikace screenu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $actionName
     * @return \KT_Custom_Page_Action_Screen
     */
    public function setActionName($actionName) {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * Nastavení hodnotu GET parametru pro identifikaci callback funkce
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $actionValue
     * @return \KT_Custom_Page_Action_Screen
     */
    public function setActionValue($actionValue) {
        $this->actionValue = $actionValue;

        return $this;
    }

    /**
     * Nastavení callback funkci, která se bude volat při schodě název a hodnoty nastavených GET parametrů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param mixed array || string - $callBackFunction
     * @return \KT_Custom_Page_Action_Screen
     */
    public function setCallBackFunction($callBackFunction) {
        $this->callBackFunction = $callBackFunction;

        return $this;
    }

}
