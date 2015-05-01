<?php

/**
 * Výčet typů logů pro KT_Logger
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz 
 */
class KT_Log_Level_Enum extends KT_Enum {

    const NONE = 0;
    const TRACE = 1;
    const DEBUG = 5;
    const INFO = 10;
    const WARNING = 15;
    const ERROR = 20;

    function __construct($value = null) {
        parent::__construct($value ? : self::NONE );
    }

}
