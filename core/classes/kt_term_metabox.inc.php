<?php

/**
 * Třída slouží k přidaní fieldsetu k taxonomiím do metaboxu
 * 
 * @author Jan Pokorný
 */
class KT_Term_Metabox implements KT_Registrable {

    private $fieldset;
    private $taxonomies = array();

    /**
     * @author Jan Pokorný
     * @param KT_Form_Fieldset $fieldset
     * @param array | string $taxonomy
     * @throws Exception
     */
    public function __construct(KT_Form_Fieldset $fieldset, $taxonomy = KT_WP_CATEGORY_KEY) {
        if (KT_Termmeta::getIsActive() == false) {
            trigger_error(__("KT Termmeta are not active...", "KT_CORE_DOMAIN"), E_USER_NOTICE);
        }
        $this->setTaxonomy($taxonomy);
        if (!kt::issetAndNotEmpty($fieldset->getPostPrefix())) {
            throw new KT_Not_Supported_Exception(__("If you want work with term meta, you must set postPrefix to your fieldset.", "KT_CORE_DOMAIN"));
        }
        $this->fieldset = $fieldset;
    }

    // --- getry & setry ---------------------

    /**
     * @author Jan Pokorný
     * @return KT_Form_Fieldset $fieldset
     */
    public function getFieldset() {
        return $this->fieldset;
    }

    /**
     * Vrátí zadané taxonomie 
     * 
     * @author Martin Hlaváč
     * @return array
     */
    public function getTaxonomies() {
        return $this->taxonomies;
    }

    /**
     * Privatní setter, ověřuje existenci taxonomie a string array konverzi
     * 
     * @author Jan Pokorný
     * @param mixed array | string $taxonomy
     * @throws Exception
     */
    private function setTaxonomy($value) {
        if (is_string($value)) {
            $taxonomies = array($value);
        } else {
            $taxonomies = $value;
        }
        foreach ($taxonomies as $taxonomy) {
            if (!taxonomy_exists($taxonomy)) {
                trigger_error(sprintf(__("This taxonomy \"%s\" is not exists!", "KT_CORE_DOMAIN"), $taxonomy), E_USER_NOTICE);
            }
        }
        $this->taxonomies = $taxonomies;
    }

    /**
     * Nastaví kolekci zadaných taxonomies
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param array $taxonomies
     * @return \KT_Term_Metabox
     */
    private function setTaxonomies(array $taxonomies) {
        $this->taxonomies = $taxonomies;
        return $this;
    }

    // --- veřejné metody ---------------------

    /**
     * Vykreslí fieldset ve formuláři u výpisu všech termů
     * VOLÁ SE V HOOCE
     * 
     * @author Jan Pokorný
     */
    public function renderFieldsetAdd() {
        wp_enqueue_media();
        $fieldset = $this->getFieldset();
        $fieldset->setTitle("");
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
        $fieldsData = ($fieldset->getSerializeSave()) ? get_term_meta($termId, $fieldset->getName(), true) : KT_WP_Term_Base_Model::getTermsMetas($termId);
        $fieldset->setFieldsData($fieldsData);
        foreach ($fieldset->getFields() as $field) {
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
        if (KT::arrayIssetAndNotEmpty($_POST)) {
            $fieldset = $this->getFieldset();
            $form = new KT_form();
            $form->addFieldSetByObject($fieldset);
            $form->validate();
            if (!$form->hasError()) {
                $form->saveFieldsetToTermMetaTable($termId);
            } elseif (KT::isWpAjax()) { // Použito u formuláři u výpisu všech termů. Pracuje ajaxem.
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
                    "what" => "taxonomy",
                    "data" => new WP_Error("error", $errorMessage)
                ));
                $ajaxResponse->send();
            }
        }
    }

    /**
     * Registrace, resp. zavedení potřebných hook
     * 
     * @author Jan Pokorný
     */
    public function register() {
        foreach ($this->getTaxonomies() as $taxonomy) {
            add_action("{$taxonomy}_edit_form_fields", array($this, "renderFieldsetEdit"), 10, 2);
            add_action("{$taxonomy}_add_form_fields", array($this, "renderFieldsetAdd"), 10, 2);
            add_action("edited_{$taxonomy}", array($this, "saveFieldset"), 10, 2);
            add_action("create_{$taxonomy}", array($this, "saveFieldset"), 10, 2);
        }
    }

    /**
     * Vytvoření nového (KT) Term Metaboxu dle zadaných metaboxů vč. případné registrace 
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param KT_Form_Fieldset $fieldset
     * @param string $taxonomy
     * @return \KT_Term_Metabox
     */
    public static function create(KT_Form_Fieldset $fieldset, $taxonomy = KT_WP_CATEGORY_KEY, $register = true) {
        $metabox = new KT_Term_Metabox($fieldset, $taxonomy);
        if ($register) {
            $metabox->register();
        }
        return $metabox;
    }

    /**
     * Vytvoření nových (KT) Term Metaboxů dle zadaných metaboxů vč. případné registrace 
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param array $fieldset
     * @param string $taxonomy
     * @return array
     */
    public static function createMultiple(array $fieldsets, $taxonomy = KT_WP_CATEGORY_KEY, $register = true) {
        $metaboxes = array();
        foreach ($fieldsets as $fieldset) {
            array_push($metaboxes, self::create($fieldset, $taxonomy, $register));
        }
        return $metaboxes;
    }
}
