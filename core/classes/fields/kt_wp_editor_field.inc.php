<?php

class KT_WP_Editor_Field extends KT_Field {

    const FIELD_TYPE = "wp-editor";

    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->setFilterSanitize(FILTER_DEFAULT);
    }

    // --- gettery a settery ---------------------------

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    protected function getNameAttribute() {
        $afterNameString = static::getAfterNameValue();
        if (KT::issetAndNotEmpty($this->getPostPrefix())) {
            return "{$this->getPostPrefix()}[{$this->getName()}]$afterNameString";
        } else {
            return "{$this->getName()}$afterNameString";
        }
    }

    public function getField() {
        $this->renderField();
    }

    // --- veřejné metody ---------------------------

    public function renderField() {
        wp_editor($this->getValue(), $this->getName(), array(
            "media_buttons" => false,
            "textarea_name" => $this->getNameAttribute(),
            "textarea_rows" => 10,
            "teeny" => false,
            "quicktags" => false
        ));
    }

}
