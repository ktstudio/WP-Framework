<?php

/**
 * Základní struktura pro definici a registraci vlastních (KT) metaboxů v administraci
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_MetaBox implements KT_Registrable {

    const CONTEXT_NORMAL = "normal";
    const CONTEXT_ADVANCED = "advanced";
    const CONTEXT_SIDE = "side";
    const PRIORITY_LOW = "low";
    const PRIORITY_HIGH = "high";
    const PRIORITY_DEFAULT = "default";
    const PRIORITY_CORE = "core";

    private $id;
    private $title;
    private $screen;
    private $context = self::CONTEXT_NORMAL;
    private $priority = self::PRIORITY_DEFAULT;
    private $dataType;
    private $fieldset;
    private $isDefaultAutoSave = true;
    private $pageTemplate;
    private $postFormat;
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
     * @param integer $dataType @see KT_MetaBox_Data_Type_Enum
     * @param KT_Form_Fieldset $fieldset - nepovinný pouze v případě "custom" modu, jinak je třeba zadat
     */
    public function __construct($id, $title, $screen, $dataType, KT_Form_Fieldset $fieldset = null) {
        $this->setId($id);
        $this->setTitle($title);
        $this->setScreen($screen);
        $this->setDataType(new KT_MetaBox_Data_Type_Enum($dataType));
        if (KT::issetAndNotEmpty($fieldset)) {
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
        if (KT::issetAndNotEmpty($id)) {
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
        if (KT::issetAndNotEmpty($title)) {
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
        if (KT::issetAndNotEmpty($screen)) {
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
        if (KT::issetAndNotEmpty($context)) {
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
        if (KT::issetAndNotEmpty($priority)) {
            $this->priority = $priority;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("priority");
    }

    /**
     * Vrátí datový typ pro MetaBox, resp. @see KT_MetaBox_Data_Type_Enum s konkrétní vybranou hodnotou
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return KT_MetaBox_Data_Type_Enum
     */
    public function getDataType() {
        return $this->dataType;
    }

    /**
     * Nastaví datový typ pro MetaBox, musí se jednat o konkrétní hodnotu @see KT_MetaBox_Data_Type_Enum
     * Pozn. dochází k automatickému "překladu" na @see KT_MetaBox_Data_Type_Enum
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_MetaBox_Data_Type_Enum $dataType
     */
    private function setDataType(KT_MetaBox_Data_Type_Enum $dataType) {
        $this->dataType = $dataType;
        return $this;
    }

    /*
     * Vrátí (form) fieldset formuláře pro MetaBox
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_Form_Fieldset
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
        if (KT::issetAndNotEmpty($fieldset)) {
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
     * Vrátí (název) šablony stránky, pokud je zadán
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getPageTemplate() {
        return $this->pageTemplate;
    }

    /**
     * Nastaví název šablony stránky
     * Pozn.: tuto funkci je vhodné používat pouze pro metaboxy registrované stránkám, které mají právě zadanou šablonu (template)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $pageTemplate
     * @return \KT_MetaBox
     */
    public function setPageTemplate($pageTemplate) {
        $this->pageTemplate = $pageTemplate;
        return $this;
    }

    /**
     * Vrátí požadovaný post formát, pokud je zadán
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getPostFormat() {
        return $this->postFormat;
    }

    /**
     * Nastaví požadovaný post formát
     * Pozn.: tuto funkci je vhodné používat pouze pro metaboxy registrované příspěvkům, stránkám apod., které mají právě zadaný post formát
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $postFormat
     * @return \KT_MetaBox
     */
    public function setPostFormat($postFormat) {
        $this->postFormat = $postFormat;
        return $this;
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
        if (KT::issetAndNotEmpty($customCallback)) {
            $this->customCallback = $customCallback;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("customCallback");
    }

    /**
     * Vrátí název případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Type_Enum::CRUD
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
     * Nastaví název případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Type_Enum::CRUD
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $className
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setClassName($className) {
        if (KT::issetAndNotEmpty($className)) {
            $this->className = $className;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("className");
    }

    /**
     * Vrátí název ID (URL) parametru případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Type_Enum::CRUD
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
     * Nastaví název ID (URL) parametru případné vlastní třídy ve spojitosti s datovým typem: KT_MetaBox_Data_Type_Enum::CRUD
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $idParamName
     * @return \KT_MetaBox
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setIdParamName($idParamName) {
        if (KT::issetAndNotEmpty($idParamName)) {
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
        $isDefaultAutoSave = $this->getIsDefaultAutoSave();

        add_action("add_meta_boxes_$screen", array($this, "add"));

        if ($this->getDataType()->getCurrentValue() === KT_MetaBox_Data_Type_Enum::POST_META) {
            add_action("save_post_$screen", array($this, "savePost"));
        }

        if ($this->getDataType()->getCurrentValue() === KT_MetaBox_Data_Type_Enum::CRUD && $isDefaultAutoSave) {
            add_filter("kt-custom-metabox-save-$screen", array($this, "saveCrud"));
        }

        if ($this->getDataType()->getCurrentValue() === KT_MetaBox_Data_Type_Enum::OPTIONS && $isDefaultAutoSave) {
            add_filter("kt-custom-metabox-save-$screen", array($this, "saveOptions"));
        }

        if ($this->getDataType()->getCurrentValue() === KT_MetaBox_Data_Type_Enum::COMMENT_META) {
            add_action("edit_comment", array($this, "saveComment"));
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
    public function add($post) {
        if ($this->CheckCanHandlePostRequest($post)) {
            add_meta_box(
                    $this->getId(), $this->getTitle(), array(&$this, "metaboxCallback"), $this->getScreen(), $this->getContext(), $this->getPriority(), array($this->getFieldset())
            );
        }
    }

    /**
     * Callback pro akci save_post pokud je aktivní datový typ příspěvků @see KT_MetaBox_Data_Type_Enum::POST
     * Pozn.: není třeba volat "ručně", jedná se o automatickou systémovou funkci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $postId
     */
    public function savePost($postId) {
        if (wp_is_post_revision($postId) || wp_is_post_autosave($postId)) {
            return $postId;
        }
        if (KT::arrayIssetAndNotEmpty($_POST)) {
            $isDefaultAutoSave = $this->getIsDefaultAutoSave();
            if ($isDefaultAutoSave) {
                $form = new KT_form();
                $form->addFieldSetByObject($this->getFieldset());
                $form->validate();
                if (!$form->hasError()) {
                    $form->saveFieldsetToPostMeta($postId);
                }
            }
        }
        return $postId;
    }

    /**
     * Callback pro akci comment_save_pre pokud je aktivní datový typ příspěvků @see KT_MetaBox_Data_Type_Enum::COMMENT_META
     * Pozn.: není třeba volat "ručně", jedná se o automatickou systémovou funkci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $commentId
     */
    public function saveComment($commentId) {
        if (KT::arrayIssetAndNotEmpty($_POST)) {
            $isDefaultAutoSave = $this->getIsDefaultAutoSave();
            if ($isDefaultAutoSave) {
                $form = new KT_form();
                $form->addFieldSetByObject($this->getFieldset());
                $form->validate();
                if (!$form->hasError()) {
                    $form->saveFieldsetToCommentMetaTable($commentId);
                }
            }
        }
        return $commentId;
    }

    /**
     * Callback pro akci load-$screen pokud je aktivní datový typ CRUD @see KT_MetaBox_Data_Type_Enum::CRUD
     * Pozn.: Není třeba volat "ručně", jedná se o automatickou systémovou funkci
     * 
     * @author Tomáš Kocifaj
     */
    public function saveCrud(array $saveResult) {
        $crudInstance = $this->getCrudInstance();
        $isDefaultAutoSave = $this->getIsDefaultAutoSave();
        $fieldset = $this->getFieldset();
        $fieldset->setTitle("");
        $form = new KT_Form();
        $form->addFieldSetByObject($fieldset);

        $form->validate();

        if ($isDefaultAutoSave && $form->isFormSend() && !$form->hasError()) {
            do_action("kt_before_metabox_save_crud", $crudInstance);

            foreach ($fieldset->getFields() as $field) {
                $fieldValue = $field->getValue();
                $crudInstance->addNewColumnValue($field->getName(), $fieldValue);
            }

            $crudInstance->saveRow();

            if ($crudInstance->hasError()) {
                $saveResult[KT_Custom_Metaboxes_Base::SAVE_RESULT_KEY] = false;
                return $saveResult;
            }

            do_action("kt_after_metabox_save_crud", $crudInstance);
            $saveResult["crud"] = $crudInstance;
            return $saveResult;
        }

        $saveResult[KT_Custom_Metaboxes_Base::SAVE_RESULT_KEY] = false;
        return $saveResult;
    }

    /**
     * Provede uložení dat poslané metaboxem do tabulky wp_options
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function saveOptions($saveResult) {
        $isDefaultAutoSave = $this->getIsDefaultAutoSave();
        $fieldset = $this->getFieldset();
        $form = new KT_Form();
        $form->addFieldSetByObject($fieldset);
        $form->validate();

        if ($form->hasError() || !$isDefaultAutoSave) {
            $saveResult[KT_Custom_Metaboxes_Base::SAVE_RESULT_KEY] = false;
            return $saveResult;
        }

        do_action("kt_before_metabox_save_options", $form);
        $form->saveFieldsetToOptionsTable();
        do_action("kt_after_metabox_save_options", $form);

        $saveResult[KT_Custom_Metaboxes_Base::SAVE_RESULT_KEY] = true;

        return $saveResult;
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
        $form = null;

        if (KT::issetAndNotEmpty($fieldset) && $fieldset instanceof KT_Form_Fieldset) {
            $fieldset->setTitle("");
            $form = new KT_Form();
            $form->addFieldsetByObject($fieldset);

            if ($form->isFormSend()) {
                $form->validate();
            }
        }

        switch ($currentValue) {
            case KT_MetaBox_Data_Type_Enum::POST_META:
                $form->loadDataFromPostMeta($post->ID);
                break;
            case KT_MetaBox_Data_Type_Enum::COMMENT_META:
                $form->loadDataFromCommentMeta($post->comment_ID);
                break;
            case KT_MetaBox_Data_Type_Enum::OPTIONS:
                $form->loadDataFromOptions();
                break;
            case KT_MetaBox_Data_Type_Enum::CRUD:
                $crudInstance = $this->getCrudInstance();
                if (KT::issetAndNotEmpty($crudInstance)) {
                    foreach ($form->getFieldsets() as $fieldset) {
                        $postPrefix = $fieldset->getPostPrefix();
                        if (KT::issetAndNotEmpty($postPrefix)) {
                            $fieldset->setFieldsData($crudInstance->getData());
                        } else {
                            throw new KT_Not_Implemented_Exception(__("Zatím jsou podporované pouze formuláře se zadaným PostPrefixem", KT_DOMAIN));
                        }
                    }
                }
                break;
            case KT_MetaBox_Data_Type_Enum::CUSTOM:
                $customCallback = $this->getCustomCallback();
                call_user_func_array("$customCallback", array($post, $form ? : $args));
                return;
            default:
                throw new KT_Not_Implemented_Exception(__("Datový typ MetaBoxu: $currentValue", KT_DOMAIN));
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
     * @param integer $dataType @see KT_MetaBox_Data_Type_Enum
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
     * @param integer $dataType @see KT_MetaBox_Data_Type_Enum
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
     * Vytvoří metabox na základě @see KT_Form_Fieldset a dalších pouze nutných parametrů pro datový typ KT_MetaBox_Data_Type_Enum::CRUD i s případnou registrací
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
        $metaBox = new KT_MetaBox($id, $title, $screen, KT_MetaBox_Data_Type_Enum::CRUD, $fieldset);
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
        $metaBox = new KT_MetaBox($id, $title, $screen, KT_MetaBox_Data_Type_Enum::CUSTOM);
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
        $idparamName = null;
        $idParamValue = null;
        $dataType = $this->getDataType();
        $currentValue = $dataType->getCurrentValue();
        if ($currentValue === KT_MetaBox_Data_Type_Enum::CRUD) {
            $idparamName = $this->getIdParamName();
            if (array_key_exists($idparamName, $_GET)) {
                $idParamValue = htmlspecialchars($_GET["$idparamName"]);
            }
            $className = $this->getClassName();
            if (KT::issetAndNotEmpty($idParamValue)) {
                $instance = new $className($idParamValue);
            } else {
                $instance = new $className();
            }
            return $instance;
        }
        return null;
    }

    /**
     * Interní kontrola zda je možné zpracovat post, resp. požadevek pro přidání, či uložení MetaBoxu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param integer $postId
     * @return boolean
     */
    private function CheckCanHandlePostRequest($post) {
        if (!$post instanceof WP_Post) {
            return true;
        }
        $pageTemplate = $this->getPageTemplate();
        if (KT::issetAndNotEmpty($pageTemplate)) { // chceme kontrolovat (aktuální) page template
            $currentPageTemplate = get_post_meta($post->ID, KT_WP_META_KEY_PAGE_TEMPLATE, true);
            if ($currentPageTemplate !== $pageTemplate) { // (aktuální) page template nesedí => rušíme přidání metaboxu
                return false;
            }
        }
        $postFormat = $this->getPostFormat();
        if (KT::issetAndNotEmpty($postFormat)) { // chceme kontrolovat (aktuální) post formát
            $currentPostFormat = get_the_terms($post->ID, "post_format");
            if (KT::arrayIssetAndNotEmpty($currentPostFormat)) {
                if (reset($currentPostFormat)->slug !== "post-format-$postFormat") { // (aktuální) post formát nesedí => rušíme přidání metaboxu
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }

}
