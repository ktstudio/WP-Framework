<?php

/**
 * Presenter pro editaci a výpis aktuálního (přihlášeného) uživatele v rámci WP
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_User_Profile_Presenter extends KT_Current_User_Presenter_Base {

    private $form;
    private $fieldset;
    private $permalink;

    /**
     * Inicializační formulář s povinnými parametry pro editaci a výpis
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_Form $form
     * @param string $permalink
     */
    public function __construct(KT_Form $form, $permalink) {
        parent::__construct();
        $this->initForm($form);
        $this->initPermalink($permalink);
    }

    /**
     * Vrátí (základní) formulář uživatelského profilu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Vypíše (základní) formulář uživatelského profilu vč. tlačítka
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function theForm() {
        echo $this->getForm()
                ->setButtonValue(__("Uložit nastavení", KT_DOMAIN))
                ->setButtonClass("kt-form-submit button red")
                ->getFormToHtml();
    }

    /**
     * Vrátí (základní) fieldset uživatelského profilu
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
     * Vypíše (základní) fieldset uživatelského profilu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function theFieldset() {
        echo $this->getFieldset()->getInputsToTable();
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
        return $this->permalink;
    }

    /**
     * Výpis informací pod čarou pro editaci uživatelského profilu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $tabsCount
     * @param string $class
     */
    public function theInfo($tabsCount = 0, $class = "textCenter") {
        echo KT::getTabsIndent($tabsCount, '<p class="' . $class . '">', true);
        echo KT::getTabsIndent($tabsCount + 1, __("*) Změní se pouze ty hodnoty, které jsou vyplněné a změněné.", KT_DOMAIN) . "<br />", true);
        echo KT::getTabsIndent($tabsCount + 1, __("**) Hesla musejí být v případě změny shodná.", KT_DOMAIN), true);
        echo KT::getTabsIndent($tabsCount, "</p>", true, true);
    }

    /**
     * Kontrola hesla a případná editace po postu, je třeba volat hlavičce stránky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function checkPostPassword() {
        if (KT::issetAndNotEmpty($_POST)) {
            $form = $this->getForm();
            $form->validate();
            if (!$form->hasError()) {

                $currentUser = $this->getCurrentUser();
                $args = array("ID" => $this->getCurrentUserId(),);

                $password = $this->getPasword($_POST, $form);
                if (KT::issetAndNotEmpty($password)) {
                    $args[KT_User_Profile_Config::PASSWORD] = $password;
                    $result = wp_update_user($args);
                    if (is_wp_error($result)) {
                        $form->setErrorMessage(__("Chyba při změně hesla...", KT_DOMAIN));
                        $form->setError(true);
                        wp_redirect($this->getPermalink());
                        return false;
                    } else {
                        $this->checkPostParams(); // případná aktualizace ostatních hodnot před přesměrovýním
                        wp_redirect($this->getPermalink());
                        return true;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Kontrola a případná editace parametrů po postu, je třeba volat hlavičce stránky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function checkPostParams() {
        if (KT::issetAndNotEmpty($_POST)) {
            $form = $this->getForm();
            $form->validate();
            if (!$form->hasError()) {
                $currentUser = $this->getCurrentUser();
                $args = array("ID" => $this->getCurrentUser(),);

                $firstName = $_POST[KT_User_Profile_Config::USER_PROFILE_FIELDSET][KT_User_Profile_Config::FIRST_NAME];
                if (KT::issetAndNotEmpty($firstName)) {
                    if ($currentUser->first_name != $firstName) {
                        $args[KT_User_Profile_Config::FIRST_NAME] = $firstName;
                    }
                }

                $lastName = $_POST[KT_User_Profile_Config::USER_PROFILE_FIELDSET][KT_User_Profile_Config::LAST_NAME];
                if (KT::issetAndNotEmpty($lastName)) {
                    if ($currentUser->last_name != $lastName) {
                        $args[KT_User_Profile_Config::LAST_NAME] = $lastName;
                    }
                }

                $email = $_POST[KT_User_Profile_Config::USER_PROFILE_FIELDSET][KT_User_Profile_Config::EMAIL];
                if (KT::issetAndNotEmpty($email)) {
                    if ($currentUser->user_email != $email) {
                        $args[KT_User_Profile_Config::EMAIL] = $email;
                    }
                }

                $phone = $_POST[KT_User_Profile_Config::USER_PROFILE_FIELDSET][KT_User_Profile_Config::PHONE];
                $userPhoneKey = KT_User_Profile_Config::PHONE;
                if (KT::issetAndNotEmpty($phone)) {
                    if ($currentUser->$userPhoneKey != $phone) {
                        $args[KT_User_Profile_Config::PHONE] = $phone;
                    }
                }

                $this->getPasword($_POST, $form); // kvůli validaci

                if (count($args) > 1) {

                    $result = wp_update_user($args);

                    if (is_wp_error($result)) {
                        $form->setErrorMessage(__("Chyba při ukládání uživatelského profilu...", KT_DOMAIN));
                        $form->setError(true);
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }
        return null;
    }

    private function getPasword(array $post, KT_Form $form) {
        $password = $post[KT_User_Profile_Config::USER_PROFILE_FIELDSET][KT_User_Profile_Config::PASSWORD];
        if (KT::issetAndNotEmpty($password)) {
            $fieldset = $form->getFieldSetByName(KT_User_Profile_Config::USER_PROFILE_FIELDSET);
            $passwordConfirm = $post[KT_User_Profile_Config::USER_PROFILE_FIELDSET][KT_User_Profile_Config::PASSWORD_CONFIRM];
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

    private function initForm(KT_Form $form) {
        $fieldset = $this->fieldset = KT_User_Profile_Config::getUserProfileFieldset($this->getCurrentUser());
        $form->addFieldSetByObject($fieldset);
        return $this->form = $form;
    }

    private function initPermalink($permalink) {
        if (KT::issetAndNotEmpty($permalink)) {
            return $this->permalink = $permalink;
        } else {
            throw new KT_Not_Set_Argument_Exception("permalink");
        }
    }

}
