<?php

abstract class KT_Data_Manager_Base {

    private $data = array();

    // --- gettery ------------------

    public function getData() {
        return $this->data;
    }

    // --- settery ------------------

    public function setData(array $data = array()) {
        $this->data = $data;
        return $this;
    }

}
