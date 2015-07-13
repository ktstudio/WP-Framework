<?php

/**
 * Výčet typů metaboxů, resp. jejich dat pro KT_Metabox
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz 
 */
class KT_MetaBox_Data_Type_Enum extends KT_Enum {

    const NONE = 0;
    const POST_META = 1;
    const OPTIONS = 3;
    const CRUD = 5;
    const COMMENT_META = 7;
    const CUSTOM = 10;

    function __construct($value = null) {
        parent::__construct($value ? : self::NONE );
    }

}
