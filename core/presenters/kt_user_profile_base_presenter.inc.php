<?php

/**
 * Presenter pro editaci a výpis aktuálního (přihlášeného) uživatele v rámci WP
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_User_Profile_Base_Presenter extends KT_Current_User_Presenter_Base
{
    const FORM_ID = "kt-user-profile-form";
    const PROCESSED_PARAM = "profile-processed";

    private $form;
    private $fieldset;
    private $permalink;
    private $wasProcessed;

    /**
     * Inicializační formulář s povinnými parametry pro editaci a výpis
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form $form
     * @param string $permalink
     */
    public function __construct($withProcessing = true) {
        parent::__construct();
        if ($withProcessing) {
            $this->process();
            $processedParam = filter_input(INPUT_GET, self::PROCESSED_PARAM, FILTER_SANITIZE_ENCODED);
            if (isset($processedParam)) {
                $processed = substr($processedParam, 0, 1);
                if ($processed === "1") {
                    $this->wasProcessed = true;
                    add_action(KT_PROJECT_NOTICES_ACTION, array(&$this, "renderSuccessNotice"));
                } elseif ($processed === "0") {
                    $this->wasProcessed = false;
                    add_action(KT_PROJECT_NOTICES_ACTION, array(&$this, "renderErrorNotice"));
                }
            }
        }
    }

    // --- getry & setry ------------------------------

    /**
     * Vrátí ID formuláře, možné přepsat, výchozí se je konstanta FORM_ID
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getFormId() {
        return self::FORM_ID;
    }

    /**
     * Kontrola, zda byl kontaktní formulář již zpracován
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function getWasProcessed() {
        return $this->wasProcessed;
    }

    /**
     * Vrátí (základní) formulář uživatelského profilu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return KT_Form
     */
    public function getForm() {
        if (isset($this->form)) {
            return $this->form;
        }
        return $this->form = $this->initForm();
    }

    /**
     * Vrátí (základní) fieldset uživatelského profilu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return KT_Form_Fieldset
     */
    public function getFieldset() {
        if (isset($this->fieldset)) {
            return $this->fieldset;
        }
        return $this->fieldset = $this->initFieldset();
    }

    /**
     * Vrátí vložený permalink na uživatelský profil
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getPermalink() {
        if (isset($this->permalink)) {
            return $this->permalink;
        }
        return $this->initPermalink();
    }

    // --- veřejné metody ------------------------------

    /**
     * Vykreselní formuláře (obecně)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderForm() {
        $form = $this->getForm();
        echo $form->getFormHeader();
        echo $form->getInputsToTable();
        echo KT_Form::getSubmitButton($form->getButtonValue(), $form->getButtonClass());
        echo $form->getFormFooter();
    }

    /**
     * Výpis informací pod čarou pro editaci uživatelského profilu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $class
     */
    public function renderInformation($class = null) {
        $classAttribute = KT::issetAndNotEmpty($class) ? " class=\"$class\"" : "";
        echo KT::getTabsIndent(0, "<p$classAttribute>", true);
        echo KT::getTabsIndent(1, __("*) Změní se pouze ty hodnoty, které jsou vyplněné a změněné.", "KT_CORE_DOMAIN") . "<br />", true);
        echo KT::getTabsIndent(1, __("**) Hesla musejí být v případě změny shodná.", "KT_CORE_DOMAIN"), true);
        echo KT::getTabsIndent(0, "</p>", true, true);
    }

    /**
     * Kontrola a zpracování (odeslaných) dat (z POSTu), pokud je to možné, je třeba v rámci hlavičky stránky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function process() {
        if (KT::arrayIssetAndNotEmpty($_POST)) {
            $form = $this->getForm();
            if (!$form->nonceValidate()) {
                wp_die(__("Chyba zpracování zdrojové adresy...", "KT_CORE_DOMAIN"));
                exit;
            }
            $form->validate();
            if (!$form->hasError()) {
                $allValues = array();
                foreach ($_POST as $key => $values) {
                    if (KT::stringStartsWith($key, KT_User_Profile_Config::FORM_PREFIX)) {
                        if (KT::arrayIssetAndNotEmpty($values)) {
                            $allValues = array_merge($allValues, $values);
                        }
                    }
                }
                if (KT::arrayIssetAndNotEmpty($allValues)) {
                    $result = ($this->checkPostParams($allValues) || $this->checkPostPassword($allValues)) && ($this->checkAdditionalPostParams() !== false);
                    if (!$result) {
                        $form->setErrorMessage(__("Chyba při ukládání uživatelského profilu...", "KT_CORE_DOMAIN"));
                        $form->setError(true);
                    } else {
                        wp_redirect(add_query_arg(self::PROCESSED_PARAM, "1", KT::getRequestUrl()));
                        exit;
                    }
                }
            }
            add_action(KT_PROJECT_NOTICES_ACTION, array(&$this, "renderErrorNotice"));
        }
    }

    /**
     * Vykreslení hlášky (HTML) po úspěšném zpracování formuláře
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderSuccessNotice() {
        echo "<p class=\"success\">{$this->getSuccessMessage()}</p>";
    }

    /**
     * Vykreslení hlášky (HTML) po neúspěšném zpracování formuláře
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderErrorNotice() {
        echo "<p class=\"error\">";
        echo $this->getErrorMessage();
        $repairLinkTitle = $this->getRepairTitle();
        echo " <a id=\"contactFormLink\" href=\"#{$this->getFormId()}\" data-kt-target=\"#{$this->getFormId()}\" title=\"$repairLinkTitle\">$repairLinkTitle</a>";
        echo "</p>";
    }

    // --- neveřejné metody ------------------------------

    /**
     * Případné vlastní dodteční pracování uložení např. user meta
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    protected function checkAdditionalPostParams() {
        return null;
    }

    /**
     * Hláška pro úspěšné zpracování formuláře
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    protected function getSuccessMessage() {
        return __("Váš profil byl úspěšně upraven a změny uloženy.", "KT_CORE_DOMAIN");
    }

    /**
     * Hláška pro neúspěšné zpracování formuláře
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    protected function getErrorMessage() {
        return __("Při zpracování profilu došlo k chybě. Je třeba zadat správně všechny údaje...", "KT_CORE_DOMAIN");
    }

    /**
     * Popisek pro opravu úspěšného zpracování formuláře
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    protected function getRepairTitle() {
        return __("Opravit", "KT_CORE_DOMAIN");
    }

    /**
     * Kontrola a případná editace parametrů po postu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    protected function checkPostParams(array $values) {
        $form = $this->getForm();
        $form->validate();
        if (!$form->hasError()) {
            $currentUser = $this->getCurrentUser();
            $args = array("ID" => $this->getCurrentUserId(),);

            $firstName = KT::arrayTryGetValue($values, KT_User_Profile_Config::FIRST_NAME);
            if (KT::issetAndNotEmpty($firstName)) {
                if ($currentUser->getFirstName() != $firstName) {
                    $args[KT_User_Profile_Config::FIRST_NAME] = $firstName;
                }
            }

            $lastName = KT::arrayTryGetValue($values, KT_User_Profile_Config::LAST_NAME);
            if (KT::issetAndNotEmpty($lastName)) {
                if ($currentUser->getLastName() != $lastName) {
                    $args[KT_User_Profile_Config::LAST_NAME] = $lastName;
                }
            }

            $email = KT::arrayTryGetValue($values, KT_User_Profile_Config::EMAIL);
            if (KT::issetAndNotEmpty($email)) {
                if ($currentUser->getEmail() != $email) {
                    $args[KT_User_Profile_Config::EMAIL] = $email;
                }
            }

            $phone = KT::arrayTryGetValue($values, KT_User_Profile_Config::PHONE);
            if (KT::issetAndNotEmpty($phone)) {
                if ($currentUser->getPhone() != $phone) {
                    $args[KT_User_Profile_Config::PHONE] = $phone;
                }
            }

            $this->getPassword($values); // kvůli validaci

            if (count($args) > 1) {
                $result = wp_update_user($args);
                if (is_wp_error($result)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
        return null;
    }

    /**
     * Kontrola hesla
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    protected function checkPostPassword(array $values) {
        $form = $this->getForm();
        $form->validate();
        if (!$form->hasError()) {
            $args = array("ID" => $this->getCurrentUserId(),);
            $password = $this->getPassword($values);
            if (KT::issetAndNotEmpty($password)) {
                $args[KT_User_Profile_Config::PASSWORD] = $password;
                $result = wp_update_user($args);
                if (is_wp_error($result)) {
                    $form->setErrorMessage(__("Chyba při změně hesla...", "KT_CORE_DOMAIN"));
                    $form->setError(true);
                    wp_redirect($this->getPermalink());
                    return false;
                } else {
                    return true;
                }
            }
        }
        return null;
    }

    protected function getPassword(array $values) {
        $form = $this->getForm();
        $password = KT::arrayTryGetValue($values, KT_User_Profile_Config::PASSWORD);
        if (KT::issetAndNotEmpty($password)) {
            $fieldset = $form->getFieldSetByName(KT_User_Profile_Config::USER_PROFILE_FIELDSET);
            $passwordConfirm = KT::arrayTryGetValue($values, KT_User_Profile_Config::PASSWORD_CONFIRM);
            if (KT::issetAndNotEmpty($passwordConfirm)) {
                if ($password === $passwordConfirm) { // OK
                    return $password;
                } else { // hesla se nerovnají
                    $passwordConfirmField = $fieldset->getFieldByName(KT_User_Profile_Config::PASSWORD_CONFIRM);
                    $passwordConfirmField->setErrorMsg("Heslo a jeho potvrzení musejí být stejné.");
                    $form->setError(true);
                }
            } else { // heslo je zadané, ale potvrzení ne
                $passwordField = $fieldset->getFieldByName(KT_User_Profile_Config::PASSWORD);
                $passwordField->setErrorMsg("Pro změnu hesla musí být zadané heslo i jeho potvrzení.");
                $form->setError(true);
            }
        }
        return null;
    }

    /** @return KT_Form */
    protected function initForm() {
        $form = new KT_Form();
        $form->setAttrId($this->getFormId());
        $form->setButtonValue(__("Uložit nastavení", "KT_CORE_DOMAIN"));
        $form->setButtonClass("kt-form-submit button button-primary");
        $form->addFieldSetByObject($this->getFieldset());
        return $this->form = $form;
    }

    /** @return KT_Form_Fieldset */
    protected function initFieldset() {
        return $this->fieldset = KT_User_Profile_Config::getUserProfileFieldset($this->getCurrentUser()->getWpUser());
    }

    /** @return string */
    protected function initPermalink() {
        return $this->permalink = KT::getRequestUrl();
    }

}
