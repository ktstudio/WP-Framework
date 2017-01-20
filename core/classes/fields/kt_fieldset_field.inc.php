<?php

/**
 * Field sloužící pro generování dynamických fieldsetů
 * Field na základě receptu na fieldset generuje fieldsety. 
 * Recept se skláda z config třídy a jméno fieldsetu. 
 * Na config třídě je třeba mít metodu getAllDynamicFieldsets a zde mít fieldset registrovaný.
 * Výsledná kolekce je pak uložena v sežazené poli. Řadit ji lze v administaci pomocí drag and drop.
 * Pro práci je nutný mít vložený a lokalizovaný javascript wp_enqueue_script(KT_DYNAMIC_FIELDSET_SCRIPT);
 * Určeno a testováno pro backend do metaboxů.
 * Rekurzivní definice není dodělaná. Pull request vítán.
 * Třída extenduje kvůli zpětné kompatibilitě třídu KT_Field, avšak mnoho metod z KT_Field nemají efekt, je třeba dávat si na to pozor.
 * 
 * 
 * @author Jan Pokorný
 */
class KT_Fieldset_Field extends KT_Field {

    const FIELD_TYPE = "fieldset";

    /**
     * Recept pro vygenerování fieldsetu. 
     * Pole [config, fieldset] 
     * Př. ["KT_ZZZ_Post_Config", KT_ZZZ_Post_Config::DYNAMIC_FIELDSET"]
     * 
     * @var array 
     */
    private $fieldsetRecipy;

    /**
     * Seřazené pole hodnot jednotlivých fieldsetů
     *
     * @var array 
     */
    private $value;

    /**
     * Výchozí seřazené pole hodnot jednotlivých fieldsetů
     *
     * @var array 
     */
    private $defaultValue;

    /**
     *
     * @var array 
     */
    private $predefinedValue;

    /**
     * Field který počítá počet dynamických fieldsetů.
     * Nutná informace při ukládaní.
     *
     * @var KT_Hidden_Field
     */
    private $coutField;

    /**
     * 
     * @param string $name
     * @param string $label 
     * @param array $fieldsetRecipy Recept na fieldset
     */
    public function __construct($name, $label, array $fieldsetRecipy) {
        parent::__construct($name, $label);
        $this->fieldsetRecipy = $fieldsetRecipy;
    }

    /**
     * Vraté field type
     * 
     * @return string
     */
    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * Vygeneruje fieldset na základě receptu
     * 
     * @return \KT_Form_Fieldset
     */
    public function getFieldset() {
        return self::generateFieldset($this->fieldsetRecipy);
    }

    /**
     * Vrátí defaultní hodnotu
     * 
     * @return array
     */
    public function getDefaultValue() {
        if (KT::arrayIssetAndNotEmpty($this->defaultValue)) {
            return $this->defaultValue;
        } else {
            return $this->getPredefinedValue();
        }
    }

    public function getPredefinedValue() {
        if (!isset($this->predefinedValue)) {
            $values = get_option($this->getName());
            $this->predefinedValue = (is_array($values) ? $values : []);
        }
        return $this->predefinedValue;
    }

    /**
     * Provede deserializaci a setne defaultní hodnotu 
     * 
     * @param array $value
     * @return \KT_Fieldset_Field
     */
    public function setDefaultValue($value) {
        $this->defaultValue = maybe_unserialize($value);
        return $this;
    }

    /**
     * Vrátí hodnoty fieldetů
     * POZOR sanitizase probíhá na úrovní filedů ve generované fieldsetu 
     * 
     * @return array
     */
    public function getValue() {
        if (!isset($this->value)) {
            $this->value = $this->prepareValue();
        }
        return $this->value;
    }

    /**
     * Vrátí hodnoty fieldetů
     * POZOR sanitizase probíhá na úrovní filedů ve generované fieldsetu 
     * 
     * @return array
     */
    public function getCleanValue() {
        return $this->getValue();
    }

    /**
     * Vykreslí field
     */
    public function renderField() {
        echo $this->getField();
    }

    /**
     *
     * @return boolean
     */
    public function Validate() {
        $count = KT::tryGetInt($this->getCoutField()->getValue());
        for ($i = 0; $i < $count; $i++) {
            // Vygenerování příslušného fieldsetu
            $fieldset = $this->getFieldset();
            $postPrefix = $fieldset->getName() . "-" . $i;
            $fieldset->setPostPrefix($postPrefix);
            // Kontrola odelasní dat
            if (!isset($_REQUEST[$postPrefix])) {
                continue;
            }
            foreach ($fieldset->getFields() as $field) {
                if (!$field->Validate()) {
                    $this->setError(__("Error at dynamic form", "KT_CORE_DOMAIN"));
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Vratí field pro vykreslení
     * @return string
     */
    public function getField() {
        return $this->getFieldHeader() . $this->getFieldBody() . $this->getFieldFooter();
    }

    /**
     * Ajax callback pro generování fieldsetů
     */
    public static function ajaxGenerateFieldset() {
        $class = filter_input(INPUT_GET, "config", FILTER_SANITIZE_STRING);
        $fieldSet = filter_input(INPUT_GET, "fieldset", FILTER_SANITIZE_STRING);
        $number = filter_input(INPUT_GET, "number", FILTER_SANITIZE_NUMBER_INT);
        if ($class && $fieldSet && $number) {
            $fieldSet = self::generateFieldset([$class, $fieldSet]);
            $fieldSet->setPostPrefix($fieldSet->getName() . "-" . ($number - 1));
            echo self::getFieldsetHtml($fieldSet);
        }
        wp_die();
    }

    /**
     * Vratí záhlaví fieldu
     * 
     * @return string
     */
    private function getFieldHeader() {
        $fieldWrapp = "<div class=\"fieldset-field\" id=\"{$this->getName()}\" data-fieldset=\"{$this->fieldsetRecipy[1]}\" data-config=\"{$this->fieldsetRecipy[0]}\" >";
        $fieldWrapp .= "<table>";
        $fieldWrapp .= "<thead><tr>";
        $fieldWrapp .= "<td style=\"width:10px\" ></td>"; // Drag and drop sloupec
        foreach ($this->getFieldset()->getFields() as $field) {
            $fieldWrapp .= "<td>{$field->getLabel()}</td>";
        }
        $fieldWrapp .= "<td></td>"; // odebrat tlačíko
        $fieldWrapp .= "</tr></thead>";
        return $fieldWrapp;
    }

    /**
     * Vratí tělo fieldu
     * @return string
     */
    private function getFieldBody() {
        $fieldWrapp = "<tbody class = \"sets\">";
        // Vygeneruje fieldsety na základě defaultValues nebo alepoň jeden prazdný 
        $i = 0;
        do {
            $fieldSet = $this->getFieldset();
            $fieldSet->setPostPrefix($fieldSet->getName() . "-" . $i);
            if (isset($this->getDefaultValue()[$i])) {
                $fieldSet->setFieldsData($this->getDefaultValue()[$i]);
            }
            $fieldWrapp .= self::getFieldsetHtml($fieldSet);
            $i++;
        } while ($i < count($this->getDefaultValue()));
        $fieldWrapp .= "</tbody></table>";
        return $fieldWrapp;
    }

    /**
     * Vratí konec fieldu
     * 
     * @return string
     */
    private function getFieldFooter() {
        $fieldWrapp = $this->getCoutField()->getField();
        $fieldWrapp .= "<a href = \"javascript:void(0);\" class=\"kt-add-fieldset button\">" . __("Add new line", "KT_CORE_DOMAIN") . "</a>";
        $fieldWrapp .= "</div>";
        return $fieldWrapp;
    }

    /**
     * Vratí seřazené pole s odeslanými hodnoty
     * @return array
     */
    private function prepareValue() {
        $finalValue = [];
        // Počet vygenerovaných fieldů uživatelem
        $count = KT::tryGetInt($this->getCoutField()->getValue());
        for ($i = 0; $i < $count; $i++) {
            // Vygenerování příslušného fieldsetu
            $fieldset = $this->getFieldset();
            $postPrefix = $fieldset->getName() . "-" . $i;
            $fieldset->setPostPrefix($postPrefix);
            // Kontrola odelasní dat
            if (!isset($_REQUEST[$postPrefix])) {
                continue;
            }
            //Sběr dat
            $fieldsetValues = [];
            foreach ($fieldset->getFields() as $field) {
                $fieldsetValues[$field->getName()] = $field->getValue();
            }
            $finalValue[] = $fieldsetValues;
        }
        return $finalValue;
    }

    /**
     * Vygeneruje fieldset na základě receptu
     * 
     * @param array $recipy
     * @return type
     * @throws Exception
     */
    private static function generateFieldset(array $recipy) {
        $fieldsets = call_user_func([$recipy[0], "getAllDynamicFieldsets"]);
        if (!$fieldsets) {
            throw new Exception("Cannot find getAllDynamicFieldsets() method on {$recipy[0]}");
        }
        if (!isset($fieldsets[$recipy[1]])) {
            throw new Exception("Cannot find fieldset {$recipy[0]} on {$recipy[1]}");
        }
        return $fieldsets[$recipy[1]];
    }

    /**
     * Vygenruje fieldy z fieldsetu do tabulky fieldů
     * @param KT_Form_Fieldset $fieldset
     * @return string
     */
    private static function getFieldsetHtml(KT_Form_Fieldset $fieldset) {
        $fieldWrapp = "<tr class = \"set\">";
        $fieldWrapp .= "<td style=\"width:10px\"><span class=\"dashicons dashicons-move\"></span></td>";
        foreach ($fieldset->getFields() as $field) {
            /* @var $field \KT_Field */
            $fieldWrapp .= "<td>{$field->getField()}</td>";
        }
        $fieldWrapp .= "<td><a href = \"javascript:void(0);\" class=\"kt-remove-fieldset\">" . __("Remove", "KT_CORE_DOMAIN") . "</a><td>";
        $fieldWrapp .= "</tr>";
        return $fieldWrapp;
    }

    /**
     * Vratí hidden field pro počétaní vygenrovaný fieldsetů
     * @return KT_Hidden_Field
     */
    private function getCoutField() {
        if (!isset($this->coutField)) {
            $this->coutField = new KT_Hidden_Field("ff_count-" . $this->getName(), "");
            $this->coutField->setPostPrefix($this->getName())
                    ->setDefaultValue(($this->getDefaultValue()) ? count($this->getDefaultValue()) : 1)
                    ->addAttrClass("ff_count");
        }
        return $this->coutField;
    }

}
