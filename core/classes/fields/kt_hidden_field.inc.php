<?php

class KT_Hidden_Field extends KT_Field {

    const FIELD_TYPE = "hidden";

    /**
     * Založení objektu typu Hidden
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     */
    public function renderField() {
        echo $this->getField();
    }

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return string
     */
    public function getField() {

        $html .= "<input type=\"hidden\" ";
        $html .= $this->getBasicHtml();
        $html .= "value=\"{$this->getValue()}\" ";
        $html .= "/>";

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

}
