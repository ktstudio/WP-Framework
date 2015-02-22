<?php

class KT_Textarea_Field extends KT_Placeholder_Field_base {

    const FIELD_TYPE = "textarea";

    /**
     * Založení objektu typeu Textarea
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);

        return $this;
    }

    // --- veřejné funkce -----------

    /**
     * Nastaví textarea počet řádků tag rows=""
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $rows
     * @return \KT_Textarea_Field
     */
    public function setRows($rows) {
        if (KT::issetAndNotEmpty($rows)) {
            $this->addAttribute("rows", $rows);
        }

        return $this;
    }

    /**
     * Nastaví textarea počet sloupců attr cols=""
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $cols
     * @return \KT_Textarea_Field
     */
    public function setCols($cols) {
        if (KT::issetAndNotEmpty($cols)) {
            $this->addAttribute("cols", $cols);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getField() {
        $html = "";
        $html .= "<textarea " . $this->getBasicHtml() . ">";
        $html .= $this->getValue();
        $html .= "</textarea>";

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     */
    public function renderField() {
        echo $this->getField();
    }

}
