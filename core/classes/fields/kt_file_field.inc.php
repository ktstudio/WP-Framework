<?php

class KT_File_Field extends KT_Field {

    const FIELD_TYPE = "file";

    private $acceptFileTypeString = null;

    /**
     * Založení objektu typu file
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    // --- gettery -------------------

    /**
     * @return string
     */
    private function getAcceptFileTypeString() {
        return $this->acceptFileTypeString;
    }

    // --- settery -------------------

    /**
     * Nastavení, jaké typy souborů field přijímá
     * př.: file_extension|audio/*|video/*|image/*|media_type @link http://www.w3schools.com/tags/att_input_accept.asp
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param type $acceptFileTypeString
     * @return \KT_File_Field
     */
    public function setAcceptFileTypeString($acceptFileTypeString) {
        $this->acceptFileTypeString = $acceptFileTypeString;
        return $this;
    }

    // --- veřejné funkce ------------

    public function renderField() {
        echo $this->getField();
    }

    public function getField() {

        $fieldValue = $this->getValue();
        $accept = $this->getAcceptFileTypeString();

        $html .= "<input type=\"file\"";
        $html .= $this->getBasicHtml();
        if (kt_isset_and_not_empty($fieldValue)) {
            $html .= "value=\"$fieldValue\" ";
        }

        if (kt_isset_and_not_empty($accept)) {
            $html .= "accept=\"$accept\" ";
        }
        $html .= "/>";

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

    /**
     * Vrátí typ fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

}
