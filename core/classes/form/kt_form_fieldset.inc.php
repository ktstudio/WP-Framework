<?php

class KT_Form_Fieldset {

    private $title = null;
    private $description = null;
    private $name = null;
    private $postPrefix = null;
    private $classes = array('kt_fieldset');
    private $id = null;
    private $fields = array();
    private $serializeSave = false;

    public function __construct($name, $title = null, $description = null) {
        $this->setName($name);
        $this->setTitle($title);
        $this->setDescription($description);

        return $this;
    }

    // --- gettery ------------------

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPostPrefix() {
        return $this->postPrefix;
    }

    /**
     * @return boolean
     */
    public function getSeralizeSave() {
        return $this->serializeSave;
    }

    // --- settery ------------------

    /**
     * Nastaveí titulek fieldsetu, který se bude v případě výpisu zobrazovat jako nadpis před sadou fildů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param type $title
     * @return \KT_Form_Fieldset
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Nastaveí popisek fieldsetu, který se bude v případě výpisu zobrazovat jako text pod nadpisem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param type $description
     * @return \KT_Form_Fieldset
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Nastavení název (hash) fieldsetu pro použití v PhP - je použit jako identifikátor v kolekci fieldsetů ve formu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param type $name
     * @return \KT_Form_Fieldset
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Nastavení prefix pro svou kolekci fieldsetů - postu pak budou dostupné pole pod tímto hashem, kde budou všechny  hodnoty fieldsety
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @param type $postPrefix
     * @return \KT_Form_Fieldset
     */
    public function setPostPrefix($postPrefix) {
        $this->postPrefix = $postPrefix;

        $this->setPostPrefixToAllFields();

        return $this;
    }

    /**
     * Nastaví pole všech class, které budou ve fieldsetu zobrazeny
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz  
     * 
     * @param type $classes
     * @return \KT_Form_Fieldset
     */
    public function setClasses(array $classes) {
        $this->classes = $classes;
        return $this;
    }

    /**
     * Nastaví id tagu fieldsetu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz   
     * 
     * @param type $id
     * @return \KT_Form_Fieldset
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Nastaveí kolekci fieldů - nepřidá, pouze nastavuje!
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz  
     * 
     * @param type $fields
     * @return \KT_Form_Fieldset
     */
    public function setFields(array $fields) {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Pokud je nastaveno true, všechny fieldy se uloží jako serializované pole pod názvem fieldsetu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param type $serializeSave
     * @return \KT_Form_Fieldset
     */
    public function setSerializeSave($serializeSave = true) {
        if (is_bool($serializeSave)) {
            $this->serializeSave = $serializeSave;
        }

        return $this;
    }

    // --- veřejné funkce -------------------------

    /**
     * Vrátí, zda některý z fieldů má nastavenou chybovou hlášku a neprošel tedy validací.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @return boolean
     */
    public function hasFieldsError() {
        if (!$this->hasFields()) {
            return false;
        }

        foreach ($this->getFields() as $field) {
            /* @var $field \KT_Field */

            if ($field->hasErrorMsg()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vrátí field objekt na základě zvoleného názvu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @return \KT_Field
     */
    public function getFieldByName($name) {
        $fieldsCollection = $this->getFields();
        return $field = $fieldsCollection[$name];
    }

    /**
     * Odstraní field z kolekce filedsetu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param type $name
     * @return \KT_Form_Fieldset
     */
    public function removeFieldByName($name) {
        $fieldsCollection = $this->getFields();

        unset($fieldsCollection[$name]);

        $this->setFields($fieldsCollection);

        return $this;
    }

    /**
     * Načte data k fieldům na zakladě předaného pole $field->getName() => $value
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param array $fieldsData
     */
    public function setFieldsData($fieldsData) {

        if (kt_not_isset_or_empty($fieldsData) || !is_array($fieldsData)) {
            return $this;
        }

        foreach ($this->getFields() as $field) {
            /** @var $field \KT_Field */
            if (!isset($fieldsData[$field->getName()])) {
                continue;
            }

            $fieldValue = $fieldsData[$field->getName()];

            if ($field->getFieldType() == KT_Text_Field::FIELD_TYPE) {
                if ($field->getInputType() == KT_Text_Field::INPUT_DATE && kt_isset_and_not_empty($fieldValue)) {
                    $fieldValue = date("d.m.Y", $fieldValue);
                }
            }

            if (kt_isset_and_not_empty($fieldValue) || $fieldValue === "0" || $fieldValue === 0) {
                $field->setValue($fieldValue);
            }
        }

        return $this;
    }

    /**
     * Přidá do kolekce fieldů další fieldy na základě předaného pole
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param array $fields
     * @return \KT_Form
     */
    public function addFieldCollection(array $fieldsCollection) {
        if ($this->hasFields()) {
            $mergeColelctions = array_merge($this->fields, $fields);
            $this->setFields($mergeColelctions);
        } else {
            $this->setFields($fieldsCollection);
        }

        return $this;
    }

    /**
     * Vrátí HTML v podobě tabulky s inputy dle field setu, nadpisem a kontainerem
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return string - HTML
     *
     */
    public function getInputsToTable() {

        $html = "";

        if ($this->hasFields()) {

            $html .= $this->getStartHtmlOfFieldSet();

            $html .= "<table class=\"kt-form-table\">";

            foreach ($this->getFields() as $field) {
                if ($field->getFieldType() != KT_Hidden_Field::FIELD_TYPE) {
                    $html .= $this->getInputToTr($field);
                }
            }

            $html .= "</table>";

            foreach ($this->getFields() as $field) {
                if ($field->getFieldType() == KT_Hidden_Field::FIELD_TYPE) {
                    $fieldHtml = $field->getField();
                    $html .= $fieldHtml . "\n";
                }
            }

            $html .= $this->getEndHtmlOfFieldSet();
        }

        return $html;
    }

    /**
     * Vrátí řádek TR pro tabulku s lablem a fieldem
     * Usage: $this->getInputsToTable()
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param KT_Field $field
     * @return string
     */
    public function getInputToTr(KT_Field $field) {
        $html = "<tr>";

        if (kt_isset_and_not_empty($field->getLabel())) {
            $html .= "<td><label for=\" {$field->getName()} \"> {$field->getLabel()} </label></td>";
        }

        $html .= "<td>{$field->getField()}</td>";
        $html .= "</tr>";

        return $html;
    }

    /**
     * Vrátí HTML string s inputy bez tabulky - vypíše pouze inputy bez struktury
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return string - HTML
     */
    public function getInputsToSimpleHtml($displayLables = false) {

        $html = "";

        if (!$this->hasFields()) {
            return $html;
        }

        $html .= $this->getStartHtmlOfFieldSet();

        foreach ($this->getFields() as $field) {
            $fieldLabel = $field->getLabel();

            $html .= "<div class=\"kt-field-simple-container\">";

            if (kt_isset_and_not_empty($fieldLabel) && $displayLables) {
                $html .= "<label for=\"{$field->getName()}\">{$fieldLabel}</label>";
            }

            $html .= $field->getField() . "\n";

            $html .= "</div>";
        }

        $html .= $this->getEndHtmlOfFieldSet();

        return $html;
    }

    /**
     * Vrátí HTML hlavičku s nastavení fieldSetu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return string HTML
     */
    public function getStartHtmlOfFieldSet() {
        $html = "<div id=\"{$this->getId()}\" class=\"kt_fieldset {$this->getClassesString()} panel panel-default\">";

        if (kt_isset_and_not_empty($this->getTitle())) {
            $html .= "<div class=\"panel-heading\"><h2 class=\"panel-title\">{$this->getTitle()}</h2></div>";
        }

        $html .= "<fieldset class=\"panel-body\">";
        if (kt_isset_and_not_empty($this->getDescription())) {
            $html .= "<p class=\"fieldset-description\">{$this->getDescription()}</p>";
        }

        return $html;
    }

    /**
     * Vrátí ukončení HTML pro hlavičku FieldSetu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return string HTML
     */
    public function getEndHtmlOfFieldSet() {
        return "</fieldset></div>";
    }

    /**
     * Vypíše tabulku s daty v rámci fieldsetu. Pokud je field prázdný, nebude ho zobrazovat.
     * Pokud nemá fieldset žádné fieldy vrací null
     * Pokud jsou všechny fieldy bez dat vrací null
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @return mixed
     */
    public function getInputsDataToTable() {

        if (!$this->hasFields()) {
            return null;
        }

        $html = "";
        $fieldContent = "";
        $unit = "";
        $value = "";

        foreach ($this->getFields() as $field) {
            /* @var $field \KT_Field */

            $value = $field->getValue();

            if (kt_not_isset_or_empty($value)) {
                continue;
            }

            if (kt_isset_and_not_empty($field->getUnit())) {
                $unit = $field->getUnit();
            }

            $fieldContent .= "<tr>";
            $fieldContent .= "<td>{$field->getLabel()}</td>";
            $fieldContent .= "<td>$value $unit</td>";
            $fieldContent .= "<tr>";
        }

        if (kt_isset_and_not_empty($fieldContent)) {
            $html = "<table id=\"{$this->getName()}\" class=\"kt-fieldset-data-table\">";
            $html .= $fieldContent;
            $html .= "</table>";

            return $html;
        }

        return null;
    }

    /**
     * Vrátí true zda fieldset obsahuje nějakou kolekci fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return bol
     */
    public function hasFields() {
        if (kt_isset_and_not_empty($this->getFields())) {
            return true;
        }

        return false;
    }

    // --- přidávání fieldů --------------------------

    /**
     * Přidá nový field KT_Text_Field
     * Lze rozšířit o addType() - NUMBER, EMAIL, DATE
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Text_Field
     */
    public function addText($name, $label) {

        $field = $this->fields[$name] = new KT_Text_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový field KT_File_Field
     * Lze rozšířit o addType() - NUMBER, EMAIL, DATE
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_File_Field
     */
    public function addFile($name, $label) {

        $field = $this->fields[$name] = new KT_File_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový field KT_Hidden_Field
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Hidden_Field
     */
    public function addHidden($name, $label = NULL) {
        $field = $this->fields[$name] = new KT_Hidden_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový field KT_Checkbox_Field
     * Lez rozšířit i o taxonomy - addTaxonomy()
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Checkbox_Field
     */
    public function addCheckbox($name, $label) {
        $field = $this->fields[$name] = new KT_Checkbox_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový field KT_Radio_Field
     * Lze rozšířit i o taxonomy - addTaxonomy()
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Radio_Field
     */
    public function addRadio($name, $label) {
        $field = $this->fields[$name] = new KT_Radio_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Funkce přidá nový typ fieldu KT_Select_Field
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Select_Field
     */
    public function addSelect($name, $label = "") {
        $field = $this->fields[$name] = new KT_Select_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový typ fieldu KT_Media_field
     * Field umožní upload obrázku / souboru v admin screenu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Image_Field
     */
    public function addMedia($name, $label) {
        $field = $this->fields[$name] = new KT_Media_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový typ fieldu KT_Switch_Field
     * Přepínání mezi ANO || NE
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Switch_Field
     */
    public function addSwitch($name, $label) {
        $field = $this->fields[$name] = new KT_Switch_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový typ fieldu - KT_Textarea_Field
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Textarea_Field
     */
    public function addTextarea($name, $label) {
        $field = $this->fields[$name] = new KT_Textarea_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový typ fieldu KT_Page_Field - reprezentuje všechny stránky ve WP
     * Možné použít addParentPage() pro zadání rodiče stránek
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Page_Field
     */
    public function addWpPage($name, $label) {
        $field = $this->fields[$name] = new KT_Page_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    /**
     * Přidá nový KT_Select_Field s přiřazeným datovým zdrojem se všemi kategoriemi
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_Category_Field
     */
    public function addWpCategory($name, $label) {
        $categoryManager = new KT_Taxonomy_Data_Manager();
        $categoryManager->setTaxonomy(KT_WP_CATEGORY_KEY);
        $field = $this->fields[$name] = new KT_Select_Field($name, $label);
        $field->setDataManager($categoryManager);
        return $field;
    }

    /**
     * Přidá nový typ fieldu KT_WP_User_Field - reprezentuje všechny uživatele v rámci Wordpressu
     * Možné použít výpis pouze určité role setUserRole();
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @param string $name
     * @param string $label
     * @return \KT_WP_User_Field
     */
    public function addWpUsers($name, $label) {
        $field = $this->fields[$name] = new KT_WP_User_Field($name, $label);
        $field->setPostPrefix($this->postPrefix);

        return $field;
    }

    // --- privátní metody ----------------------

    /**
     * Všem fieldům v kolekci nastavení post_prefix dle zadaného do FieldSetu
     *
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     *
     * @return \KT_Form_Fieldset
     */
    private function setPostPrefixToAllFields() {
        if ($this->hasFields() && kt_isset_and_not_empty($this->postPrefix)) {
            foreach ($this->fields as $field) {
                $field->setPostPrefix($this->postPrefix);
            }
        }

        return $this;
    }

    /**
     * Vrátí všechny definované classy filedstu a zhotoví z nich prostý string pro zápis
     * do class atributu elementu.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @return string
     */
    private function getClassesString() {
        return $classString = implode(" ", $this->getClasses());
    }

}
