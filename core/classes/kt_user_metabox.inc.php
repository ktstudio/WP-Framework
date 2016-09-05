<?php

/**
 * Třída slouží k přidaní fieldsetu k user meataboxu
 * 
 * @author Jan Pokorný
 */
class KT_User_Metabox implements KT_Registrable {

    /**
     *
     * @var KT_Form_Fieldset 
     */
    private $fieldset;

    /**
     *
     * @var array 
     */
    private $roles = array();

    /**
     * @author Jan Pokorný
     * @param KT_Form_Fieldset $fieldset
     * @param array $roles Pole user rolí u kterých se má metabox zobrazit. Defaultně všechny role.
     */
    public function __construct(KT_Form_Fieldset $fieldset, $roles = null) {
        $this->roles = ($roles) ? : array_keys(wp_roles()->roles);
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
     * 
     * @return array
     */
    public function getRoles() {
        return $this->roles;
    }

// --- veřejné metody ---------------------

    /**
     * Vykreslí fieldset
     * VOLÁ SE V HOOCE
     * 
     * @author Jan Pokorný
     * @param WP_User $user
     */
    public function renderFieldset(WP_User $user) {
        if (count(array_intersect($this->roles, $user->roles)) < 1) { // Průnik rolí
            return;
        }
        wp_enqueue_media();
        $fieldset = $this->getFieldset();
        $fieldsData = ($fieldset->getSerializeSave()) ? get_user_meta($user->ID, $fieldset->getName(), true) : KT_WP_User_Base_Model::getUserMetas($user->ID);
        $fieldset->setFieldsData($fieldsData);
        echo $fieldset->getInputsToTable();
    }

    /**
     * Uloží fieldset
     * VOLÁ SE V HOOCE
     * 
     * @author Jan Pokorný
     * @param int $user_id
     */
    public function saveFieldset($user_id) {
        if (get_current_user_id() != $user_id && !current_user_can('edit_users')) {
            return;
        }
        $fieldset = $this->getFieldset();
        $form = new KT_form();
        $form->addFieldSetByObject($fieldset);
        $form->validate();
        if ($form->hasError()) {
            // TODO has error
        }
        if ($fieldset->getSerializeSave()) {
            $fieldsetData = $form->getSavableFieldsetGroupValue($fieldset);
            if (KT::arrayIssetAndNotEmpty($fieldsetData)) {
                update_user_meta($user_id, $fieldset->getName(), $fieldsetData);
            } else {
                delete_user_meta($user_id, $fieldset->getName());
            }
        } else {
            foreach ($fieldset->getFields() as $field) {
                $fieldValue = $form->getSavableFieldValue($field);
                if ($field && $fieldValue !== "") {
                    update_user_meta($user_id, $field->getName(), $fieldValue);
                } else {
                    delete_user_meta($user_id, $field->getName());
                }
            }
        }
    }

    /**
     * Registrace, resp. zavedení potřebných hook
     * 
     * @author Jan Pokorný
     */
    public function register() {
        add_action("show_user_profile", array($this, "renderFieldset"), 10, 2);
        add_action("edit_user_profile", array($this, "renderFieldset"), 10, 2);
        add_action("personal_options_update", array($this, "saveFieldset"), 10, 2);
        add_action("edit_user_profile_update", array($this, "saveFieldset"), 10, 2);
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
    public static function create(KT_Form_Fieldset $fieldset, $roles = null, $register = true) {
        $metabox = new self($fieldset, $roles);
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
    public static function createMultiple(array $fieldsets, $roles = null, $register = true) {
        $metaboxes = array();
        foreach ($fieldsets as $fieldset) {
            array_push($metaboxes, self::create($fieldset, $roles, $register));
        }
        return $metaboxes;
    }

}
