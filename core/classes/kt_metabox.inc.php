<?php

/**
 * Základní struktura pro definici a registraci vlastních (KT) metaboxů v administraci
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_MetaBox implements KT_Registrable {

    private $id;
    private $title;
    private $screen;
    private $context = "normal";
    private $priority = "default";
    private $dataType;
    private $fieldset;
    private $isDefaultAutoSave = true;
    private $customCallback;
    private $className;
    private $idParamName;

    /**
     * Vytvoření nového MetaBoxu s (povinnými) parametry
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $id
     * @param string $title
     * @param string $screen
     * @param integer $dataType @see KT_MetaBox_Data_Types
     * @param KT_Form_Fieldset $fieldset - nepovinný pouze v případě "custom" modu, jinak je třeba zadat
     */
    public function __construct($id, $title, $screen, $dataType, KT_Form_Fieldset $fieldset = null) {
        $this->setId($id);
        $this->setTitle($title);
        $this->setScreen($screen);
        $this->setDataType(new KT_MetaBox_Data_Types($dataType));
        if (kt_isset_and_not_empty($fieldset)) {
            $this->setFieldset($fieldset);
        }
    }

    /**
     * Vrátí identifikátor MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Nastaví identifikátor MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $id
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    private function setId($id) {
        if (kt_isset_and_not_empty($id)) {
            $this->id = $id;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("id");
    }

    /**
     * Vrátí název MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Nastaví název MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $title
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    private function setTitle($title) {
        if (kt_isset_and_not_empty($title)) {
            $this->title = $title;
            return $this;
        }
        //throw new KT_Not_Set_Argument_Exception( "title" );
    }

    /**
     * Vrátí obrazovku, resp. umístění MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getScreen() {
        return $this->screen;
    }

    /**
     * Nastaví obrazovku, resp. umístění MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $screen
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    private function setScreen($screen) {
        if (kt_isset_and_not_empty($screen)) {
            $this->screen = $screen;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("screen");
    }

    /**
     * Vrátí kontext (uvnitř obrazovky, resp. umístění) MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getContext() {
        return $this->context;
    }

    /**
     * Nastaví kontext (uvnitř obrazovky, resp. umístění) MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $context
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setContext($context) {
        if (kt_isset_and_not_empty($context)) {
            $this->context = $context;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("context");
    }

    /**
     * Vrátí prioritu (uvnitř obrazovky, resp. umístění) MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getPriority() {
        return $this->priority;
    }

    /**
     * Nastaví prioritu (uvnitř obrazovky, resp. umístění) MetaBoxu v rámci WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $priority
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setPriority($priority) {
        if (kt_isset_and_not_empty($priority)) {
            $this->priority = $priority;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("priority");
    }

    /**
     * Vrátí datový typ pro MetaBox, resp. @see KT_MetaBox_Data_Types s konkrétní vybranou hodnotou
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return KT_MetaBox_Data_Types
     */
    public function getDataType() {
        return $this->dataType;
    }

    /**
     * Nastaví datový typ pro MetaBox, musí se jednat o konkrétní hodnotu @see KT_MetaBox_Data_Types
     * Pozn. dochází k automatickému "překladu" na @see KT_MetaBox_Data_Types
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_MetaBox_Data_Types $dataType
     */
    private function setDataType(KT_MetaBox_Data_Types $dataType) {
        $this->dataType = $dataType;
        return $this;
    }

    /*
     * Vrátí (form) fieldset formuláře pro MetaBox
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return KT_Form_Fieldset
     */

    public function getFieldset() {
        return $this->fieldset;
    }

    /**
     * Nastaví (form) fieldset formuláře pro MetaBox
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    private function setFieldset(KT_Form_Fieldset $fieldset) {
        if (kt_isset_and_not_empty($fieldset)) {
            $this->fieldset = $fieldset;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("fieldset");
    }

    /**
     * Vrátí označení, zda se má provádět u výchozích akcí automatické uložené dat
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    function getIsDefaultAutoSave() {
        return $this->isDefaultAutoSave;
    }

    /**
     * Nastaví označení, zda se má provádět u výchozích akcí automatické uložené dat
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $isDefaultAutoSave
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    function setIsDefaultAutoSave($isDefaultAutoSave) {
        if (is_bool($isDefaultAutoSave)) {
            $this->isDefaultAutoSave = $isDefaultAutoSave;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("isDefaultAutoSave");
    }

    /**
     * Vrátí název případné vlastní funkce pro callback
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getCustomCallback() {
        return $this->customCallback;
    }

    /**
     * Nastaví název případné vlastní funkce pro callback
     * Pozn.: funkce musí mít 2 parametry (typu): integer (post ID) a buď KT_Form v případě, že je zadán @see fieldset anebo array
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $customCallback
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setCustomCallback($customCallback) {
        if (kt_isset_and_not_empty($customCallback)) {
            $this->customCallback = $customCallback;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("customCallback");
    }

    /**
     * Vrátí název případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Types::CRUD
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Nastaví název případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Types::CRUD
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $className
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setClassName($className) {
        if (kt_isset_and_not_empty($className)) {
            $this->className = $className;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("className");
    }

    /**
     * Vrátí název ID (URL) parametru případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Types::CRUD
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getIdParamName() {
        return $this->idParamName;
    }

    /**
     * Nastaví název ID (URL) parametru případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Types::CRUD
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $idParamName
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setIdParamName($idParamName) {
        if (kt_isset_and_not_empty($idParamName)) {
            $this->idParamName = $idParamName;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("idParamName");
    }

    /**
     * Registrace MetaBoxu do WP, tj. do systému
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function register() {
        $screen = $this->getScreen();
        add_action("add_meta_boxes_$screen", array(&$this, "add"));
        if ($this->getDataType()->getCurrentValue() === KT_MetaBox_Data_Types::POST_META) {
            add_action("save_post_$screen", array(&$this, "savePost"));
        }
    }

    /**
     * Vyvolání přidání MetaBoxu do WP, tj. do systému
     * Pozn.1: není třeba volat "ručně", jedná se o automatickou systémovou funkci
     * Pozn.2: v případě vlastního použití, je třeba vyvolávat v rámci WP akce add_meta_boxes
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function add() {
        add_meta_box(
                $this->getId(), $this->getTitle(), array(&$this, "metaboxCallback"), $this->getScreen(), $this->getContext(), $this->getPriority(), array($this->getFieldset())
        );
    }

    /**
     * Callback pro akci save_post pokud je aktivní datový typ příspěvků @see KT_MetaBox_Data_Types::POST
     * Pozn.: není třeba volat "ručně", jedná se o automatickou systémovou funkci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $postId
     */
    public function savePost($postId) {
        if (wp_is_post_revision($postId)) {
            return;
        }
        $isDefaultAutoSave = $this->getIsDefaultAutoSave();
        if ($isDefaultAutoSave) {
            $form = new KT_form();
            $form->addFieldSetByObject($this->getFieldset());
            $form->saveFieldsetToPostMeta($postId);
        }
    }

    /**
     * Výchozí callback MetaBoxu, který zpracuje zpracování dat a zobrazení formuláře, či vyvolá dále vlastní callback
     * Pozn.: není třeba volat "ručně", jedná se o automatickou systémovou funkci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post|null $post
     * @param array $args
     * @throws KT_Not_Implemented_Exception
     */
    public function metaboxCallback($post, $args) {

        $dataType = $this->getDataType();
        $currentValue = $dataType->getCurrentValue();
        $fieldset = $args["args"][0];

        if (kt_isset_and_not_empty($fieldset) && $fieldset instanceof KT_Form_Fieldset) {
            $fieldset->setTitle("");
            $form = new KT_Form();
            $form->addFieldsetByObject($fieldset);
            $form->validate();
        }

        $isDefaultAutoSave = $this->getIsDefaultAutoSave();

        switch ($currentValue) {
            case KT_MetaBox_Data_Types::POST_META:
                $form->loadDataFromPostMeta($post->ID);
                break;
            case KT_MetaBox_Data_Types::OPTIONS:
                if ($isDefaultAutoSave) {
                    $form->saveFieldsetToOptionTable();
                }
                $form->loadDataFromWpOption();
                break;
            case KT_MetaBox_Data_Types::CRUD:
                $crudInstance = $this->getCrudInstance();
                if (kt_isset_and_not_empty($crudInstance)) {
                    $canCrudSave = $form->isFormSend() || !$form->hasError();
                    foreach ($form->getFieldsets() as $fieldset) {
                        $postPrefix = $fieldset->getPostPrefix();
                        if (kt_isset_and_not_empty($postPrefix)) {
                            if ($isDefaultAutoSave && $canCrudSave) {
                                foreach ($fieldset->getFields() as $field) {
                                    $crudInstance->addNewColumnToData($field->getName(), $field->getValue());
                                }
                                $crudInstance->saveRow();
                            }
                            $fieldset->setFieldsData($crudInstance->getData());
                        } else {
                            throw new KT_Not_Implemented_Exception(__("Zatím jsou podporované pouze formuláře se zadaným PostPrefixem", KT_DOMAIN));
                        }
                    }
                }
                break;
            case KT_MetaBox_Data_Types::CUSTOM:
                $customCallback = $this->getCustomCallback();
                call_user_func_array("$customCallback", array($post, $form ? : $args));
                return;
            default:
                throw new KT_Not_Implemented_Exception(__("Datový typ MetaBoxu: $currentValue", KT_DOMAIN));
        }
        if ($form->isFormSend() && $form->getShowNotice()) {
            echo $form->getFormNotice();
        }

        echo $form->getInputsToTable();
    }

    /**
     * Vytvoří metabox na základě @see KT_Form_Fieldset a dalších pouze nutných parametrů i s případnou registrací
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @param string $screen
     * @param integer $dataType @see KT_MetaBox_Data_Types
     * @param boolean $register Označení, zda se po vytvoření MetaBoxů rovnou zavolat i registrace (do systému)
     * @return \KT_MetaBox
     */
    public static function create(KT_Form_Fieldset $fieldset, $screen, $dataType, $register = true) {
        $id = $fieldset->getName();
        $title = $fieldset->getTitle();
        $metaBox = new KT_MetaBox($id, $title, $screen, $dataType, $fieldset);
        if ($register) {
            $metaBox->Register();
        }
        return $metaBox;
    }

    /**
     * Vytvoří metaboxy hromadně na základě kolekce @see KT_Form_Fieldset a dalších pouze nutných parametrů i s případnou registrací
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $fieldsets @see KT_Form_Fieldset
     * @param string $screen
     * @param integer $dataType @see KT_MetaBox_Data_Types
     * @param boolean $register Označení, zda se po vytvoření MetaBoxů rovnou zavolat i registrace (do systému)
     * @return array @see \KT_MetaBox
     */
    public static function createMultiple(array $fieldsets, $screen, $dataType, $register = true) {
        $metaBoxes = array();
        $index = 0;
        foreach ($fieldsets as $fieldset) {
            $metaBoxes[$index] = self::create($fieldset, $screen, $dataType, $register);
            $index ++;
        }
        return $metaBoxes;
    }

    /**
     * Vytvoří metabox na základě @see KT_Form_Fieldset a dalších pouze nutných parametrů pro datový typ KT_MetaBox_Data_Types::CRUD i s případnou registrací
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @param string $screen
     * @param string $className
     * @param string $idParamName
     * @param boolean $register Označení, zda se po vytvoření MetaBoxů rovnou zavolat i registrace (do systému)
     * @return \KT_MetaBox
     */
    public static function createCrud(KT_Form_Fieldset $fieldset, $screen, $className, $idParamName, $register = true) {
        $id = $fieldset->getName();
        $title = $fieldset->getTitle();
        $metaBox = new KT_MetaBox($id, $title, $screen, KT_MetaBox_Data_Types::CRUD, $fieldset);
        $metaBox->setClassName($className);
        $metaBox->setIdParamName($idParamName);
        if ($register) {
            $metaBox->Register();
        }
        return $metaBox;
    }

    /**
     * Vytvoří metabox na základě vlastní callback funkce a dalších pouze nutných parametrů i s případnou registrací
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $id
     * @param string $title
     * @param string $screen
     * @param string $customCallback Název vlastní callback funkce
     * @param boolean $register Označení, zda se po vytvoření MetaBoxů rovnou zavolat i registrace (do systému)
     * @return \KT_MetaBox
     */
    public static function createCustom($id, $title, $screen, $customCallback, $register = true) {
        $metaBox = new KT_MetaBox($id, $title, $screen, KT_MetaBox_Data_Types::CUSTOM);
        $metaBox->setCustomCallback($customCallback);
        if ($register) {
            $metaBox->Register();
        }
        return $metaBox;
    }

    /**
     * Hromadně vyvolá registraci všech zadaných metaboxů v poli
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $metaBoxes @see KT_MetaBox
     */
    public static function registerMultiple(array $metaBoxes) {
        foreach ($metaBoxes as $metaBox) {
            $metaBox->register();
        }
    }

    /**
     * Vrátí novou instance CRUDu na základě zadaného názvu třídy a ID parametru
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Crud
     */
    private function getCrudInstance() {
        $dataType = $this->getDataType();
        $currentValue = $dataType->getCurrentValue();
        if ($currentValue === KT_MetaBox_Data_Types::CRUD) {
            $idparamName = $this->getIdParamName();
            $idParamValue = $_GET["$idparamName"];
            if (kt_isset_and_not_empty($idParamValue)) {
                $className = $this->getClassName();
                $instance = new $className($idParamValue);
                return $instance;
            }
        }
        return null;
    }

}
