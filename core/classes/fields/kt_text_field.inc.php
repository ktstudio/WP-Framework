<?php

class KT_Text_Field extends KT_Field {

    const FIELD_TYPE = "text";
    const INPUT_NUMBER = "number";
    const INPUT_EMAIL = "email";
    const INPUT_DATE = "date";
    const INPUT_PASSWORD = "password";
    const INPUT_URL = "url";

    private $inputType = self::FIELD_TYPE;

    /**
     * Založení objektu typu input type="text || number || email || password"
     * V případě date založen type="text" a doplněny classy pro jQuery datapicker
     * 
     * DEFAULT TEXT
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return KT_Text_Field
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    // --- settery ------------------------

    /**
     * Přidá typ textu, zle zvolit z constatny třídy
     * NUMBER || EMAIL || DATE || PASSWORD
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param type $type
     * @return \KT_Text_Field
     * @throws InvalidArgumentException
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setInputType($type) {
        if (kt_isset_and_not_empty($type)) {

            if ($type == self::INPUT_DATE) {
                $this->addClass("datapicker");
            }

            $this->inputType = $type;

            return $this;
        }

        throw new KT_Not_Set_Argument_Exception("type");
    }

    // --- veařejné funkce -----------------

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

        $fieldType = $this->getInputType() == self::INPUT_DATE ? "text" : $this->getInputType();

        $html .= "<input type=\"{$fieldType}\" ";
        $html .= $this->getBasicHtml();
        $html .= "value=\"{$this->getValue()}\" ";
        $html .= "placeholder=\"{$this->getPlaceholder()}\" ";
        $html .= "/>";

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

    /**
     * Vrátí typ vstupu - NUMBER || EMAIL || DATE || PASSWORD
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return mixed string || null
     */
    public function getInputType() {
        return $this->inputType;
    }

    /**
     * Vrátí přeconvertovanou hodnotu ve fieldu, kdy bere ohled na date Field
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param bolean $original - má vrátít originální hodnotu v DB nebo hodnotou pro zobrazení
     * @return null
     */
    public function getConvertedValue() {
        $fieldValue = parent::getConvertedValue();

        if ($this->getInputType() == self::INPUT_DATE && kt_isset_and_not_empty($fieldValue)) {
            return $newFieldValue = date("d.m.Y", $fieldValue);
        }

        return $fieldValue;
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
