<?php

class KT_Simple_Data_Manager extends KT_Data_Manager_Base {

    public function __construct(array $data = array()) {
        if (kt_isset_and_not_empty($data)) {
            $this->setData($data);
        }

        return $this;
    }

}
