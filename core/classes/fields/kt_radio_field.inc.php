<?php

class KT_Radio_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = 'radio';

    /**
     * Založení objektu typeu Radio
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);

        return $this;
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

        $html = "";

        if (kt_not_isset_or_empty($this->getOptionsData())) {
            return $html = KT_EMPTY_TEXT;
        }

        foreach ($this->getOptionsData() as $key => $value) {

            $html .= "<span class=\"input-wrap radio\">";
            $html .= "<input type=\"radio\" ";
            $html .= $this->getBasicHtml();
            $html .= "value=\"$key\" ";

            if ($key == $this->getValue()) {
                $html .= "checked=\"checked\"";
            }

            $html .= "> <span class=\"radio radio-name-{$this->getId()} radio-key-$key \">$value</span> ";

            $html .= "</span>";
        }

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

}
