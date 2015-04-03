<?php

class KT_Form extends KT_HTML_Tag_Base implements ArrayAccess {

    const DISPLAY_TYPE_TABLE = 'table';
    const DISPLAY_TYPE_SIMPLE = 'simple';
    const BUTTON_DEFAULT_VALUE = 'Odeslat formulář';
    const LOAD_WP_OPTION = "option";
    const LOAD_POST_META = "post_meta";
    const METHOD_POST = "post";
    const METHOD_GET = "get";

    private $fieldsets = array();
    private $error = false;
    private $buttonValue = self::BUTTON_DEFAULT_VALUE;
    private $buttonClass = "kt-form-submit button button-primary";
    private $successMessage = null;
    private $errorMessage = null;
    private $showNotice = true;

    /**
     * Založení nového objetku KT_Form
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $method = default post
     * @param string $action = default ''
     * @param string $id = default kt-form
     *
     */
    function __construct($method = self::METHOD_POST, $action = '#', $id = 'kt-form') {
        $this->setMethod($method)
                ->setAction($action)
                ->setMethod(self::METHOD_POST)
                ->setAttrId($id)
                ->setSuccessMessage(__("Data byla uložena", KT_DOMAIN))
                ->setErrorMessage(__("Ve formuláři se vyskytla chyba", KT_DOMAIN));

        $this->addAttribute("data-validate", "jquery");

        return $this;
    }

    // --- arrayAcces -----------------------------

    public function offsetExists($offset) {
        $fieldsetCollection = $this->getFieldsets();

        foreach ($fieldsetCollection as $fieldset) {
            if (isset($fieldset[$offset])) {
                return true;
            }
        }

        return false;
    }

    public function offsetGet($offset) {
        $fieldsetCollection = $this->getFieldsets();

        foreach ($fieldsetCollection as $fieldset) {
            if (isset($fieldset[$offset])) {
                return $fieldset[$offset];
            }
        }

        return null;
    }

    public function offsetSet($offset, $value) {
        
    }

    public function offsetUnset($offset) {
        
    }

    // ---- gettery -----------------------------

    /**
     * @return array
     */
    public function getFieldsets() {
        return $this->fieldsets;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->getAttrValueByName("method");
    }

    /**
     * @return boolean
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getAction() {
        $this->getAttrValueByName("action");
    }

    /**
     * @return string
     */
    public function getButtonValue() {
        return $this->buttonValue;
    }

    /**
     * @return string
     */
    public function getButtonClass() {
        return $this->buttonClass;
    }

    /**
     * @return string
     */
    public function getSuccessMessage() {
        return $this->successMessage;
    }

    /**
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * @return boolean
     */
    public function getShowNotice() {
        return $this->showNotice;
    }

    /**
     * @return string
     */
    public function getEnctype() {
        return $this->getAttrValueByName("enctype");
    }

    // ---- settery -----------------------------

    /**
     * Nastavení kolekci fiedlsetů - nepřidá další fieldset do kolekce.
     * Pouze nastavuje property třídy
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array $fieldsets
     * @return \KT_Form
     */
    public function setFieldsets(array $fieldsets) {
        $this->fieldsets = $fieldsets;
        return $this;
    }

    /**
     * nastavení metodu <form> - přijmá pouze POST a GET
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $method - POST || GET
     * @return \KT_Form
     * @throws InvalidArgumentException
     */
    public function setMethod($method) {
        if ($method == self::METHOD_POST || $method == self::METHOD_GET) {
            $this->addAttribute("method", $method);
            return $this;
        }

        throw new InvalidArgumentException('method');
    }

    /**
     * Nastaví formuláři chybu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param boolean $error
     * @return \KT_Form
     */
    public function setError($error) {
        $this->error = $error;
        return $this;
    }

    /**
     * Nastavení akci <form> tagu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $action
     * @return \KT_Form
     */
    public function setAction($action) {
        if (KT::issetAndNotEmpty($action)) {
            $this->addAttribute("action", $action);
        }
        return $this;
    }

    /**
     * Nastavení hodnotu submit buttonu při echování formuláře jako celku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $value
     * @return \KT_Form
     */
    public function setButtonValue($value) {
        if (KT::issetAndNotEmpty($value)) {
            $this->buttonValue = $value;
        }

        return $this;
    }

    /**
     * Nastavení class submit buttonu při echování formuláře jako celku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $value
     * @return \KT_Form
     */
    public function setButtonClass($value) {
        if (KT::issetAndNotEmpty($value)) {
            $this->buttonClass = $value;
        }
        return $this;
    }

    /**
     * Nastaví SuccessMesassage,která je vypsána v notice při průchodu validací
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $message
     * @return \KT_Form
     */
    public function setSuccessMessage($message) {
        if (KT::issetAndNotEmpty($message)) {
            $this->successMessage = $message;
        }

        return $this;
    }

    /**
     * Nastaví ErrorMessage, která je vypsáná v notice při chybě ve validaci
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $message
     * @return \KT_Form
     */
    public function setErrorMessage($message) {
        if (KT::issetAndNotEmpty($message)) {
            $this->errorMessage = $message;
        }

        return $this;
    }

    /**
     * Nastaví, zda se má u formuláře zobrazovat notifikace
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param boolean $showNotice
     * @return \KT_Form
     */
    public function setShowNotice($showNotice) {
        $this->showNotice = $showNotice;
        return $this;
    }

    /**
     * Nastaví enctype formuláře
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $enctype
     * @return \KT_Form
     */
    public function setEnctype($enctype) {
        $this->addAttribute("enctype", $enctype);
        return $this;
    }

    // --- veřejné funkce --------------------------

    /**
     * Přidá classu <form> tagu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $class
     * @return \KT_Form
     */
    /* public function addClass($class) {
      if (KT::issetAndNotEmpty($class)) {
      $this->formClasses .= ' ' . $class;
      }

      return $this;
      }
     * 
     */

    /**
     * Přidá další položu fieldsetu do kolekce
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @return \KT_Form_Fieldset
     */
    public function addFieldSet($name) {
        $fieldSet = new KT_Form_Fieldset($name);
        $this->fieldsets[$name] = $fieldSet;

        return $fieldSet;
    }

    /**
     * Přidá objekt KT_Form_Fieldset do kolekce fieldsetů formu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param \KT_Form_Fieldset
     * @return \KT_Form_Fieldset
     */
    public function addFieldSetByObject(KT_Form_Fieldset $fieldSet) {
        $this->fieldsets[$fieldSet->getName()] = $fieldSet;

        return $fieldSet;
    }

    /**
     * Přidá kolekci fieldsetů do formuláře
     * array($fieldsetName => KT_Form_Fieldset)
     *
     * @param array $fieldSetCollection
     * @return \KT_Form
     */
    public function addFieldsetCollection(array $fieldSetCollection) {
        if (KT::issetAndNotEmpty($this->getFieldsets())) {
            $newCollection = array_merge($this->fieldsets, $fieldSetCollection);

            $this->setFieldsets($newCollection);

            return $this;
        }

        $this->setFieldsets($fieldSetCollection);

        return $this;
    }

    /**
     * Vrátí položku jednoho fieldsetu na základě jeho jména.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @return \KT_Form_Fieldset
     */
    public function getFieldSetByName($name) {
        if (KT::issetAndNotEmpty($this->fieldsets[$name])) {
            return $this->fieldsets[$name];
        }

        return null;
    }

    /**
     * Pokud formulář nemá žádné chyby ve validací, vrácí false
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return Boolean
     */
    public function hasError() {
        if ($this->getError() == 0) {
            return false;
        }
        return true;
    }

    /*     * ******* FUNKCE PRO PRÁCI S FIELDY ********* */

    /**
     * Funkce vrátí zda objekt KT_Form ma zadanou nějakou kolekci fieldu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function hasFieldset() {
        if (KT::issetAndNotEmpty($this->fieldsets)) {
            return true;
        }

        return false;
    }

    /**
     * Vytvoří html string pro vypsání Success message
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function returnSuccessMsg($class = "kt-no-error") {
        $html = "<p class=\"$class\">{$this->getSuccessMessage()}</p>";

        return $html;
    }

    /**
     * Vytvoří html string pro vypsání Error message
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function returnErrorMsg($class = "kt-error") {
        $html = "<p class=\"$class\">{$this->getErrorMessage()}</p>";

        return $html;
    }

    /**
     * Na základě erroru formu vrátí html string s message
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getFormNotice() {
        if ($this->getMethod() == self::METHOD_POST && KT::notIssetOrEmpty($_POST)) {
            return;
        }

        if ($this->getMethod() == self::METHOD_GET && KT::notIssetOrEmpty($_GET)) {
            return;
        }

        if ($this->hasError()) {
            return $this->returnErrorMsg();
        } else {
            return $this->returnSuccessMsg();
        }
    }

    /**
     * Vrátí tabulku s formulářem - echo TR TD INPUT
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $class - class tabulky
     */
    public function getInputsToTable($class = null) {

        $html = "";

        if ($this->hasFieldset()) {
            foreach ($this->fieldsets as $fieldSet) {
                $html .= $fieldSet->getInputsToTable($class);
            }
        }

        return $html;
    }

    /**
     * Vrátí html všech fieldů bez všech složitostí a konstrukcí kolem
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string $html
     * @throws KT_Not_Set_Argument_Exception
     */
    public function getInputsToSimpleHtml($displayLables = false) {
        if ($this->hasFieldset()) {
            foreach ($this->fieldsets as $fieldSet) {
                $html .= $fieldSet->getInputsToSimpleHtml($displayLables);
            }

            return $html;
        }
    }

    /**
     * Vrátí HTML s celým formulářem. Včetně form tagu a submitu
     * Může vrátít table strutkru nebo obyčejně neformátovaný form
     *
     *
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string @type = table || simple
     */
    public function getFormToHtml($type = self::DISPLAY_TYPE_TABLE) {

        $html = $this->getFormHeader();

        if ($this->isFormSend() && $this->getShowNotice()) {
            $html .= $this->getFormNotice();
        }

        switch ($type) {
            case self::DISPLAY_TYPE_TABLE:
                $html .= $this->getInputsToTable();
                break;
            case self::DISPLAY_TYPE_SIMPLE:
                $html .= $this->getInputsToSimpleHtml();
                break;

            default:
                throw new InvalidArgumentException('type');
        }

        $html .= self::getSubmitButton($this->getButtonValue(), $this->getButtonClass());
        $html .= "</form>";

        return $html;
    }

    /**
     * Vrátí hlavičku formuláře <form... na základě jeho nastavení
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return type
     */
    public function getFormHeader() {
        $html = "<form " . $this->getAttributeString() . ">";
        return $html;
    }

    /**
     * Vrátí ukončení formuláře </form>
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return type
     */
    public function getEndForm() {
        return $html = "</form>";
    }

    /**
     * Funkce vrátí strukturu pro HTML v podobně tabulky Label -> Value (saved)
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array $excludeFieldsets - které filedy se nebudou zobrazovat $field->name
     * @param string $class
     * @return string
     */
    public function getInputsDataToTable(array $excludeFieldsets = array(), $class = 'meta-info') {

        $html = "";

        if (!$this->hasFieldset()) {
            return $html;
        }

        foreach ($this->getFieldsets() as $fieldset) {
            /* @var $fieldset \KT_Form_Fieldset */
            if (in_array($fieldset->getName(), $excludeFieldsets)) {
                continue;
            }

            $html .= $fieldset->getInputsDataToTable($class);
        }

        return $html;
    }

    /**
     * Načte data do všech fieldsetů na základě názvů fieldů z tabulky wp_option
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param $postId in
     *
     * */
    public function loadDataFromWpOption() {

        if (!$this->hasFieldset()) {
            return;
        }

        foreach ($this->getFieldsets() as $fieldset) {
            /* @var $fieldset \KT_Form_Fieldset */
            if (!$fieldset->hasFields()) {
                continue;
            }

            if ($fieldset->getSeralizeSave()) {

                $fieldsetData = get_option($fieldset->getName());
                $fieldset->setFieldsData($fieldsetData);

                continue;
            }

            foreach ($fieldset->getFields() as $field) {
                $value = get_option($field->getName());
                if (KT::issetAndNotEmpty($value)) {
                    $field->setValue($value);
                }
            }
        }

        return $this;
    }

    /**
     * Načte data do všech fieldsetů na základě postId z tabulky wp_postmeta
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param $postId in
     *
     * */
    public function loadDataFromPostMeta($postId) {

        if (!KT::isIdFormat($postId)) {
            return $this;
        }

        if (!$this->hasFieldset()) {
            return $this;
        }

        if (is_admin()) {
            $transientName = $this->getCurrentTransientName();
            $transientData = get_transient($transientName);
        }

        $postMetas = KT_WP_Post_Base_Model::getPostMetas($postId);

        foreach ($this->getFieldsets() as $fieldset) {
            /* @var $fieldset \KT_Form_Fieldset */

            if (KT::issetAndNotEmpty($transientData[$fieldset->getName()])) {
                $this->fieldsets[$fieldset->getName()] = $transientData[$fieldset->getName()];
                continue;
            }

            if (!$fieldset->hasFields()) {
                continue;
            }

            if ($fieldset->getSeralizeSave() && array_key_exists($fieldset->getName(), $postMetas)) {
                $fieldset->setFieldsData(unserialize($postMetas[$fieldset->getName()]));
                continue;
            }

            $fieldset->setFieldsData($postMetas);
        }

        return $this;
    }

    /**
     * Načte data do všech fieldsetů na základě userId z tabulky wp_usermeta
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param int $userId
     * @return \KT_Form
     */
    public function loadDataFromUserMeta($userId) {
        if (!KT::isIdFormat($userId)) {
            return $this;
        }

        if (!$this->hasFieldset()) {
            return $this;
        }

        $userMetas = KT_WP_User_Base_Model::getAllUserMeta($userId);

        foreach ($this->getFieldsets() as $fieldset) {
            /* @var $fieldset \KT_Form_Fieldset */

            if (!$fieldset->hasFields()) {
                continue;
            }

            if ($fieldset->getSeralizeSave()) {
                $fieldset->setFieldsData($userMetas[$fieldset->getName()]);
                continue;
            }

            $fieldset->setFieldsData($userMetas);
        }
    }

    /**
     * Inicializuje všechny validační metody definované u fieldů
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array - parametry fieldu
     * @return \KT_Form
     */
    public function validate() {
        if (!$this->isFormSend()) {
            $this->setError(true);
            return $this;
        }

        if (!$this->hasFieldset()) {
            $this->setError(false);
            return $this;
        }

        foreach ($this->getFieldsets() as $fieldset) {
            /* @var $fieldset \KT_Form_Fieldset */
            if (!$fieldset->hasFields()) {
                continue;
            }

            foreach ($fieldset->getFields() as $field) {
                $field->Validate();
                if ($field->hasErrorMsg()) {
                    $this->setError(true);
                }
            }
        }

        return $this;
    }

    /**
     * Funkce umístí hodnoty do fieldu fomuláře. Možné definovat pomocí fieldsetu nebo obyčejným polem
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param form_data array() - $fieldSet->name => array($field->name => $fieldValue)
     */
    public function setFormData(array $fieldsetCollectionsData) {

        if (KT::notIssetOrEmpty($fieldsetCollectionsData) || !$this->hasFieldset()) {
            return $this;
        }

        foreach ($this->getFieldsets() as $fieldSet) {
            if ($fieldSet->hasFields()) {
                $fieldSet->setFieldsData($fieldsetCollectionsData[$fieldSet->getName()]);
            }
        }

        return $this;
    }

    /**
     * Funkce uloží všechny fieldy objektu do tabuly wp_postmeta v podobě key => value
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param int $postId = ID postu, v případě nezadání, se bere global $post
     * @param array $excludeFields - kolekci fieldů, které nemají být ukládány [] => $field->getName()
     * @return \KT_Form
     */
    public function saveFieldsetToPostMeta($postId, array $excludeFields = array()) {
        if (is_admin()) {

            if (!current_user_can('edit_post', $postId)) {
                return;
            }

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return $postId;
            }
        }

        if (!KT::isIdFormat($postId) || !$this->hasFieldset()) {
            return $postId;
        }

        /* @var $fieldset \KT_Form_Fieldset */

        $this->updateTransientDataForAdminValidation();

        foreach ($this->getFieldsets() as $fieldset) {

            if (!$fieldset->hasFields()) {
                continue;
            }

            if ($fieldset->getSeralizeSave()) {
                $this->saveFieldsetToPostmetaByGroup($postId, $fieldset, $excludeFields);
                continue;
            }

            $this->saveFieldsetToPostMetaOneByOne($postId, $fieldset, $excludeFields);
        }

        return $this;
    }

    /**
     * Funkce uloží všechny fieldy z formuláře do wp_options klíč field->name
     * Funkce si sama provede kontrolu, zda se jedná o serializovaný save nebo obyčejný
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array $exludeFields - které fieldy se nebudou ukládat
     * @return \KT_Form
     */
    public function saveFieldsetToOptionTable(array $exludeFields = array()) {
        if (!$this->isFormSend() || $this->hasError()) {
            return $this;
        }

        if ($this->hasFieldset()) {
            foreach ($this->fieldsets as $fieldset) {
                /* @var $fieldSet \KT_Form_Fieldset */
                if ($fieldset->hasFields()) {
                    if ($fieldset->getSeralizeSave()) {
                        $this->saveFieldsetToOptionByGroup($fieldset, $exludeFields);
                    } else {
                        $this->saveFieldsetToOptionOneByOne($fieldset, $exludeFields);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Funkce uloží všechny fieldy z formuláře do wp_usermeta klíč field->name
     * Funkce si sama provede kontrolu, zda
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param int $userId - kterému uživateli budou meta uložena
     * @param array $excludeFields - které fieldy se nebudou ukládat
     * @return \KT_Form
     */
    public function saveFieldsetToUserMeta($userId, array $excludeFields = array()) {

        if (!$this->isFormSend() || $this->hasError()) {
            return $this;
        }

        if ($this->hasFieldset()) {
            foreach ($this->fieldsets as $fieldset) {
                /* @var $fieldSet \KT_Form_Fieldset */
                if ($fieldset->hasFields()) {
                    if ($fieldset->getSeralizeSave()) {
                        $this->saveFieldsetToUsermetaByGroup($userId, $fieldset, $excludeFields);
                    } else {
                        $this->saveFieldsetToUsermetaOnyByOne($userId, $fieldset, $excludeFields);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Dodá admin notifikaci s chybovou hlášku na základě nastavené validace ze strany formuláře.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function addErrorNoticeAfterSavePostCallback() {

        if (!is_admin() || $_GET["message"] != 1) {
            return;
        }

        $transientName = self::getCurrentTransientName();
        $transientValue = get_transient($transientName);

        if (KT::issetAndNotEmpty($transientValue)) {
            echo "<div class=\"error\">";
            echo "<p>" . __("Některé data nebyla uložena. Zkontrolujte prosím vstupní data a proces opakujte", KT_DOMAIN) . ".</p>";
            echo "</div>";
        }
    }

    /**
     * Funkce prověří, zda byl formulář odeslán na základě metody a postu nebo getu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function isFormSend() {

        if ($this->getMethod() == self::METHOD_POST && empty($_POST)) {
            return false;
        }

        if ($this->getMethod() == self::METHOD_GET && empty($_GET)) {
            return false;
        }

        if ($this->getMethod() == self::METHOD_POST) {
            if ($this->hasFieldset()) {
                foreach ($this->getFieldsets() as $fieldset) {
                    $fieldSetPrefix = $fieldset->getPostPrefix();
                    if (KT::issetAndNotEmpty($fieldSetPrefix)) {
                        if (array_key_exists($fieldSetPrefix, $_POST))
                            return true;
                    } else {
                        return true;
                    }
                }
            }
        }

        if ($this->getMethod() == self::METHOD_GET) {
            if ($this->hasFieldset()) {
                foreach ($this->getFieldsets() as $fieldset) {
                    $fieldSetPrefix = $fieldset->getPostPrefix();
                    if (KT::issetAndNotEmpty($fieldSetPrefix)) {
                        if (array_key_exists($fieldSetPrefix, $_GET))
                            return true;
                    } else {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /* PRIVÁTNÍ FUNKCE
     * ******************************************************************************************* */

    /**
     * Provede nastavení nebo smazání transient dat pro zobrazení nevalidních fieldů
     * při ukládání při editaci post_type
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @global type $current_screen
     * @return type
     */
    private function updateTransientDataForAdminValidation() {

        $fieldsetWithErrors = array();

        if (!is_admin()) {
            return;
        }

        $transientName = self::getCurrentTransientName();

        if ($this->hasError()) {
            foreach ($this->getFieldsets() as $fieldset) {
                if ($fieldset->hasFieldsError()) {
                    $fieldset->setFieldsData($_POST[$fieldset->getName()]);
                    $fieldsetWithErrors[$fieldset->getName()] = $fieldset;
                }
            }

            if (KT::issetAndNotEmpty($fieldsetWithErrors)) {
                set_transient($transientName, $fieldsetWithErrors, (30));
            }
        } else {
            delete_transient($transientName);
        }
    }

    /**
     * Uloži data poslané postem do tabulky wp_option - každý field jako extra row
     * USED : saveFieldsToOptionTable
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param \KT_Form_Fields $fieldSet
     * @param array $exlude_fieldset
     * @return \KT_Form
     */
    private function saveFieldsetToOptionOneByOne(KT_Form_Fieldset $fieldSet, array $exludeFields = array()) {
        /* @var $field \KT_Field */

        foreach ($fieldSet->getFields() as $field) {
            if (!in_array($field->getName(), $exludeFields)) {
                $fieldValue = $field->getValue();
                if ($fieldValue != "" || $fieldValue === 0 || $fieldValue === "0") {
                    update_option($field->getName(), $field->getValue());
                } else {
                    delete_option($field->getName());
                }
            }
        }

        return $this;
    }

    /**
     * Uloží data poslané postem do tabulky wp_option - celý fieldset jako realizované pole fieldů ($fieldName => $fieldValue)
     * USED : saveFieldsToOptionTable
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param \KT_Form_Fieldset $fieldset
     * @param array $exludeFields
     * @return \KT_Form
     */
    private function saveFieldsetToOptionByGroup(KT_Form_Fieldset $fieldset, array $exludeFields = array()) {
        /* @var $field \KT_Field */

        foreach ($fieldset->getFields() as $field) {
            if (!in_array($field->getName(), $exludeFields) && KT::issetAndNotEmpty($field->getValue())) {
                $fieldValue = $field->getValue();
                if ($fieldValue != "" || $fieldValue === 0 || $fieldValue === "0") {
                    $fieldsetData[$field->getName()] = $field->getValue();
                }
            }
        }

        if (KT::arrayIssetAndNotEmpty($fieldsetData)) {
            update_option($fieldset->getName(), $fieldsetData);
        } else {
            delete_option($fieldset->getName());
        }

        return $this;
    }

    /**
     * Uloží data poslané formulářem do tabulky wp_postmeta - každý field jako extra pole
     * Pokud je třeba nějaký field vyloučit z uložení, stačí jeho getName() zadat do $exludeFieldse
     *
     * USED : saveFormDataToPostMeta
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @param array $exludeFields
     * @return \KT_Form
     */
    private function saveFieldsetToPostMetaOneByOne($postId, KT_Form_Fieldset $fieldset, array $excludeFields = array()) {
        foreach ($fieldset->getFields() as $field) {
            /* @var $field \KT_Field */

            if (in_array($field->getName(), $excludeFields)) {
                continue;
            }

            $old = get_post_meta($postId, $field->getName(), true);
            $new = $field->getValue();

            if ($new === '') {
                delete_post_meta($postId, $field->getName());
                continue;
            }

            if ($new != $old) {
                $fieldType = get_class($field);
                if ($fieldType == "KT_Text_Field") {
                    if ($field->getInputType() == KT_Text_Field::INPUT_DATE) {
                        $new = date("Y-m-d", strtotime($new));
                    }
                }
                update_post_meta($postId, $field->getName(), $new);
                continue;
            } elseif ('' == $new && $old) {
                delete_post_meta($postId, $field->getName(), $old);
            }
        }

        return $this;
    }

    /**
     * Uloží data poslané postem do tabulky wp_postmeta - na základě zadaného fieldsetu, post_id
     * Pokud je třeba nějaký field vyloučit z uložení, stačí jeho getName() zadat do $exludeFieldse
     *
     * USED : saveFormDataToPostMeta
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @param array $exludeFields
     * @return \KT_Form
     */
    private function saveFieldsetToPostmetaByGroup($postId, KT_Form_Fieldset $fieldset, array $excludeFields = array()) {
        foreach ($fieldset->getFields() as $field) {
            /* @var $field \KT_Field */

            if (in_array($field->getName(), $excludeFields) && KT::notIssetOrEmpty($field->getValue())) {
                continue;
            }

            $fieldsetData[$field->getName()] = $field->getValue();
        }

        if (KT::issetAndNotEmpty($fieldsetData)) {
            update_post_meta($postId, $fieldset->getName(), $fieldsetData);
        } else {
            delete_post_meta($postId, $fieldset->getName());
        }

        return $this;
    }

    /**
     * Uloží data poslané formulářem do tabulky wp_usermeta - každý field jako extra pole
     * Pokud je třeba nějaký field vyloučit z uložení, stačí jeho getName() zadat do $exludeFieldse
     *
     * USED : saveFormDataToPostMeta
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @param array $exludeFields
     * @return \KT_Form
     */
    private function saveFieldsetToUsermetaOnyByOne($userId, KT_Form_Fieldset $fieldset, array $excludeFields = array()) {
        foreach ($fieldset->getFields() as $field) {
            /* @var $field \KT_Field */

            if (in_array($field->getName(), $excludeFields)) {
                continue;
            }

            if (KT::issetAndNotEmpty($field->getValue())) {

                $fieldType = get_class($field);
                if ($fieldType == "KT_Text_Field") {
                    if ($field->getInputType() == KT_Text_Field::INPUT_DATE) {
                        $new = date("Y-m-d", strtotime($new));
                    }
                }

                update_user_meta($userId, $field->getName(), $field->getValue());
            } else {
                delete_user_meta($userId, $field->getName());
            }
        }

        return $this;
    }

    /**
     * Uloží data poslané postem do tabulky wp_postmeta - na základě zadaného fieldsetu, post_id
     * Pokud je třeba nějaký field vyloučit z uložení, stačí jeho getName() zadat do $exludeFieldse
     *
     * USED : saveFormDataToPostMeta
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form_Fieldset $fieldset
     * @param array $exludeFields
     * @return \KT_Form
     */
    private function saveFieldsetToUsermetaByGroup($userId, KT_Form_Fieldset $fieldset, array $excludeFields = array()) {
        foreach ($fieldset->getFields() as $field) {
            /* @var $field \KT_Field */

            if (in_array($field->getName(), $excludeFields) && KT::notIssetOrEmpty($field->getValue())) {
                continue;
            }

            $fieldsetData[$field->getName()] = $field->getValue();
        }

        if (KT::issetAndNotEmpty($fieldsetData)) {
            update_user_meta($userId, $fieldset->getName(), $fieldsetData);
        } else {
            delete_user_meta($userId, $fieldset->getName());
        }

        return $this;
    }

    // --- statické metody ---------------

    /**
     * Vypíše submit form button
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $value = html value v submitu
     * @param string $class = html class
     * @param string $id = html id
     *
     * @return html
     */
    public static function getSubmitButton($value = self::BUTTON_DEFAULT_VALUE, $class = 'kt-form-submit button button-primary', $id = 'kt-form-submit') {
        return "<input type=\"submit\" class=\"{$class}\" id=\"{$id}\" value=\"{$value}\">";
    }

    /**
     * Získá jméno transientu v případě validace formuláře z pozice editace post_type
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @global \WP_Screen $current_screen
     * @return string
     */
    public static function getCurrentTransientName() {
        global $current_screen;

        $transtionName = $current_screen->base . "-" . get_current_user_id();

        return $transtionName;
    }

}

?>