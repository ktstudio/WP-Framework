<?php

class KT_MetaBox_Data_Types extends KT_Enum {

    const NONE = 0;
    const POST_META = 1;
    const OPTIONS = 3;
    const CRUD = 5;
    const CUSTOM = 10;

    function __construct($value = null) {
        parent::__construct($value ? : self::NONE );
    }

}
