<?php

class KT_Simple_Data_Manager extends KT_Data_Manager_Base {

    public function __construct(array $data = array()) {
        if (KT::issetAndNotEmpty($data)) {
            $this->setData($data);
        }
    }

}
