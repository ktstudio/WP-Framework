<?php

class KT_WP_Editor_Field extends KT_Field {

    const FIELD_TYPE = "wp-editor";

    private $settings = array();

    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->setFilterSanitize(FILTER_DEFAULT);
        $this->setDefaultSettings();
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

    /**
     * Set default WP editor settings on field init
     */
    public function setDefaultSettings() {
        $defaultSettings = array(
            "media_buttons" => false,
            "textarea_name" => $this->getNameAttribute(),
            "textarea_rows" => 10,
            "teeny" => false,
            "quicktags" => false
        );
        $this->setWpEditorSettings($defaultSettings);
    }

    /**
     * Allows to replace current WP editor settings with new values
     * 
     * @param array $settings
     */
    public function setWpEditorSettings($settings) {
        $this->settings = array_replace($this->settings, $settings);
    }

    // --- veřejné metody ---------------------------

    public function renderField() {
        wp_editor($this->getValue(), $this->getName(), $this->settings);
    }

}
