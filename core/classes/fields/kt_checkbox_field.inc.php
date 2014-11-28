<?php

class KT_Checkbox_Field extends KT_Options_Field_Base {

    const FIELD_TYPE = 'checkbox';

    /**
     * Založení objektu typu Checkbox
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);

        return $this;
    }

    // --- veřejné funkce ------------

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

        if (kt_not_isset_or_empty($this->getOptionsData())) {
            return "<span class=\"input-wrap checkbox\">" . KT_EMPTY_TEXT . "</span>";
        }

        $html = "";

        foreach ($this->getOptionsData() as $key => $val) {
            $html .= "<span class=\"input-wrap\">";
            $html .= "<input type=\"checkbox\" ";
            $html .= $this->getBasicHtml();
            $html .= "value=\"$key\" ";

            $data = $this->getValue();

            if (kt_isset_and_not_empty($data) && is_array($data)) {
                if (in_array($key, $data)) {
                    $html .=" checked=\"checked\"";
                }
            }

            if (kt_isset_and_not_empty($data) && !is_array($data)) {
                $html .= " checked=\"checked\"";
            }

            $html .= "> <span class=\"desc-checkbox-{$this->getId()}\">$val</span> ";

            if ($this->hasErrorMsg()) {
                $html .= parent::getHtmlErrorMsg();
            }

            $html .= "</span>";
        }

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

}
