<?php

class KT_Fieldset_Field extends KT_Field {

    const FIELD_TYPE = "fieldset";
    const COUNT_FIELD = "ff_count";
    const AJAX_HOOK = "kt_generate_fieldset";
    const AJAX_CB = "ajaxGenerate";

    private $fieldsetRecipy;
    private $value;
    private $defaultValue;

    /**
     *
     * @var KT_Hidden_Field
     */
    private $coutField;

    public function __construct($name, $label, array $fieldsetRecipy) {
        parent::__construct($name, $label);
        $this->fieldsetRecipy = $fieldsetRecipy;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    public function getFieldset() {
        return self::generateFieldset($this->fieldsetRecipy);
    }

    private function getCoutField() {
        if (!isset($this->coutField)) {
            $this->coutField = new KT_Hidden_Field(self::COUNT_FIELD . "-" . $this->getName(), "");
            $this->coutField->setPostPrefix($this->getName())
                    ->setDefaultValue(($this->getDefaultValue()) ? count($this->getDefaultValue()) : 1)
                    ->addAttrClass(self::COUNT_FIELD);
        }
        return $this->coutField;
    }

    public function setDefaultValue($value) {
        $this->defaultValue = maybe_unserialize($value);
        return $this;
    }

    public function getDefaultValue() {
        return $this->defaultValue;
    }

    public function getField() {
        $fieldWrapp = "<div class=\"fieldset-field\" data-fieldset=\"{$this->fieldsetRecipy[1]}\" data-config=\"{$this->fieldsetRecipy[0]}\" >";
        $i = 0;
        $fieldWrapp .= "<table>";
        $fieldWrapp .= "<thead><tr>";
        foreach ($this->getFieldset()->getFields() as $field) {
            $fieldWrapp .= "<td>{$field->getLabel()}</td>";
        }
        $fieldWrapp .= "<td></td>";
        $fieldWrapp .= "</tr></thead><tbody class=\"sets\">";
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
        $fieldWrapp .= $this->getCoutField()->getField();
        $fieldWrapp .= "<a href = \"javascript:void(0);\" class=\"kt-add-fieldset button\">" . __("Přidat kolekci", "KT_CORE_ADMIN") . "</a>";
        $fieldWrapp .= "</div>";
        return $fieldWrapp;
    }

    public function getValue() {
        if (!isset($this->value)) {
            $this->value = $this->prepareValue();
        }
        return $this->value;
    }

    public function getCleanValue() {
        return $this->getValue();
    }

    public function renderField() {
        echo $this->getField();
    }

    public function Validate() {
        $fieldset = $this->getFieldset();
        foreach ($fieldset->getFields() as $field) {
            if (!$field->Validate()) {
                return false;
            }
        }
        return true;
    }

    private function prepareValue() {
        $count = KT::tryGetInt($this->getCoutField()->getValue());
        for ($i = 0; $i < $count; $i++) {
            $fieldset = $this->getFieldset();
            $postPrefix = $fieldset->getName() . "-" . $i;
            if (!isset($_REQUEST[$postPrefix])) {
                continue;
            }
            $fieldset->setPostPrefix($postPrefix);
            $fieldsetValues = [];
            foreach ($fieldset->getFields() as $field) {
                $fieldsetValues[$field->getName()] = $field->getValue();
            }
            $finalValue[] = $fieldsetValues;
        }
        return $finalValue;
    }

    private static function generateFieldset(array $recipy) {
        $fieldsets = call_user_func([$recipy[0], "getAllGeneratableFieldsets"]);
        if (!$fieldsets) {
            throw new Exception("Cannot find getAllGeneratableFieldsets() method on {$recipy[0]}");
        }
        if (!isset($fieldsets[$recipy[1]])) {
            throw new Exception("Cannot find fieldset {$recipy[0]} on {$recipy[1]}");
        }
        return $fieldsets[$recipy[1]];
    }

    private static function getFieldsetHtml(KT_Form_Fieldset $fieldset) {
        $fieldWrapp = "<tr class = \"set\">";
        foreach ($fieldset->getFields() as $field) {
            /* @var $field \KT_Field */
            $fieldWrapp .= "<td>{$field->getField()}</td>";
        }
        $fieldWrapp .= "<td><a href = \"javascript:void(0);\" class=\"kt-remove-fieldset\">" . __("Odebrat kolekci", "KT_CORE_ADMIN") . "</a><td>";
        $fieldWrapp .= "</tr>";
        return $fieldWrapp;
    }

    public static function ajaxGenerate() {
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

}
