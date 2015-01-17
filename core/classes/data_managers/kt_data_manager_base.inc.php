<?php

/**
 * Základ pro datové managery 
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
abstract class KT_Data_Manager_Base {

    private $data = array();

    /**
     * Vrátí data
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Nastaví data
     * 
     * @param array $data
     * @return \KT_Data_Manager_Base
     */
    public function setData(array $data = array()) {
        $this->data = $data;
        return $this;
    }

}
