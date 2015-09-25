<?php

/**
 * Třída slouží k přidaní fieldsetu k taxonomiim
 * @author Jan Pokorný
 */
class KT_Metabox_Term {

    private $fieldset;
    private $taxonomies;

    /**
     * 
     * @author Jan Pokorný
     * @param KT_Form_Fieldset $fieldset
     * @param array | string $taxonomy
     * @throws Exception
     */
    public function __construct(KT_Form_Fieldset $fieldset, $taxonomy = KT_WP_CATEGORY_KEY) {
        if (KT_Termmeta::getIsActive() == false) {
            throw new Exception("Termmeta is not actvie");
        }
        $this->setTaxonomy($taxonomy);
        $this->fieldset = $fieldset;
        $this->prepareHooks();
    }

    /**
     * 
     * @author Jan Pokorný
     * @return KT_Form_Fieldset $fieldset
     */
    public function getFieldset() {
        return $this->fieldset;
    }

    /**
     * Vykreslí fieldset
     * VOLÁ SE V HOOCE
     * 
     * @author Jan Pokorný
     */
    public function renderFieldsetAdd() {
        wp_enqueue_media();
        $termId = filter_input(INPUT_GET, "tag_ID", FILTER_SANITIZE_NUMBER_INT);
        $fieldset = $this->getFieldset();
        $fieldset->setTitle("");
        foreach ($fieldset->getFields() as $field) {
            if ($termId) {
                $value = KT_Termmeta::getData($termId, $field->getName(), true);
                $field->setValue($value);
            }
        }
        echo $fieldset->getInputsToTable();
    }

    /**
     * Vykreslí fieldset
     * VOLÁ SE V HOOCE
     * 
     * @author Jan Pokorný
     */
    public function renderFieldsetEdit() {
        wp_enqueue_media();
        $termId = filter_input(INPUT_GET, "tag_ID", FILTER_SANITIZE_NUMBER_INT);
        $fieldset = $this->getFieldset();
        $fieldset->setTitle("");
        foreach ($fieldset->getFields() as $field) {
            if ($termId) {
                $value = KT_Termmeta::getData($termId, $field->getName(), true);
                $field->setValue($value);
            }
            echo $fieldset->getInputToTr($field);
        }
    }

    /**
     * Uloží fieldset
     * VOLÁ SE V HOOCE
     * 
     * @author Jan Pokorný
     */
    public function saveFieldset($termId) {
        $fieldset = $this->getFieldset();
        if (isset($_POST[$fieldset->getPostPrefix()])) {
            $fieldset = $this->getFieldset();
            $form = new KT_form();
            $form->addFieldSetByObject($fieldset);
            $form->validate();
            if (!$form->hasError()) {
                $form->saveFieldsetToTermMetaTable($termId);
            } elseif (KT::isWpAjax()) {
                $taxonomy = filter_input(INPUT_POST, "taxonomy", FILTER_SANITIZE_STRING);
                wp_delete_term($termId, $taxonomy);
                $errorMessage = "";
                foreach ($fieldset->getFields() as $field) {
                    if ($field->hasErrorMsg()) {
                        $errorMessage .= sprintf("%s - %s <br>", $field->getLabel(), $field->getError());
                    }
                }
                $ajaxResponse = new WP_Ajax_Response();
                $ajaxResponse->add(array(
                    'what' => 'taxonomy',
                    'data' => new WP_Error('error', $errorMessage)
                ));
                $ajaxResponse->send();
            }
        }
    }

    /**
     * Privatní setter, ověřuje existenci taxonomie a string array konverzi
     * 
     * @author Jan Pokorný
     * @param array | string $taxonomy
     * @throws Exception
     */
    private function setTaxonomy($taxonomy) {
        if (is_string($taxonomy)) {
            $taxonomy = array($taxonomy);
        }
        foreach ($taxonomy as $tax) {
            if (!taxonomy_exists($tax)) {
                //   throw new Exception("Taxonomy " . $tax . " does not exist");
            }
        }
        $this->taxonomies = $taxonomy;
    }

    /**
     * Zavede potřebné hooky
     * 
     * @author Jan Pokorný
     */
    private function prepareHooks() {
        foreach ($this->taxonomies as $taxonomy) {
            add_action($taxonomy . '_edit_form_fields', array($this, "renderFieldsetEdit"), 10, 2);
            add_action($taxonomy . '_add_form_fields', array($this, "renderFieldsetAdd"), 10, 2);
            add_action('edited_' . $taxonomy, array($this, "saveFieldset"), 10, 2);
            add_action('create_' . $taxonomy, array($this, "saveFieldset"), 10, 2);
        }
    }

}
