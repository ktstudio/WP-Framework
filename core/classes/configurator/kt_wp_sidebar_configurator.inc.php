<?php

final class KT_WP_Sidebar_Configurator {

    private $data = array();

    // --- magic functions ------------

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }

    // --- gettery ----------------------

    /**
     * Vrátí nastavení sidebaru pro funkci
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @return array
     */
    public function getSidebarData() {
        return $this->data;
    }

    // --- settery ----------------------

    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    public function setBeforeWidget($beforeWidget) {
        $this->before_widget = $beforeWidget;

        return $this;
    }

    public function setAfterWidget($afterWidget) {
        $this->after_widget = $afterWidget;

        return $this;
    }

    public function setBeforeTitle($beforeTitle) {
        $this->before_title = $beforeTitle;

        return $this;
    }

    public function setAfterTitle($afterTitle) {
        $this->after_title = $afterTitle;

        return $this;
    }

    public function setClass($class) {
        $this->class = $class;

        return $this;
    }

}
