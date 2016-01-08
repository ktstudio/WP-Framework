<?php

/**
 * Obecný presenter pro obsluhu kontakního formuláře
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Contact_Form_Base_Presenter extends KT_Presenter_Base {

    const FORM_ID = "kt-contact-form";
    const PROCESSED_PARAM = "contact-processed";

    private $form;
    private $fieldset;
    private $wasProcessed;

    /**
     * Obecný presenter pro obsluhu kontakního formuláře
     * POZOR: pokud je parametr $withProcessing = true, tak by se měl presenter inicializovat ještě před sestavením hlavičky stránky
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $withProcessing true pokud má ihned dojít ke zpracování, false pouze pro definici bez zpracování
     */
    public function __construct($withProcessing = true) {
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

    // --- getry & setry ------------------------

    /**
     * Vrátí kontaktní formulář
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Form
     */
    public function getForm() {
        if (KT::issetAndNotEmpty($this->form)) {
            return $this->form;
        }
        return $this->initForm();
    }

    /**
     * Vrátí kontaktní fieldset (formuláře)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Form_Fieldset
     */
    public function getFieldset() {
        if (KT::issetAndNotEmpty($this->fieldset)) {
            return $this->fieldset;
        }
        return $this->initFieldset();
    }

    /**
     * Vrátí email, na který se bude zpracovaný formulář odesílat, výchozí je administrační e-mail
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function getFormEmail() {
        return get_bloginfo("admin_email");
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

    // --- veřejné metody ------------------------

    /**
     * Kontrola a zpracování (odeslaných) dat (z POSTu), pokud je to možné
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
                $values = filter_input(INPUT_POST, KT_Contact_Form_Base_Config::FORM_PREFIX, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                if (KT::arrayIssetAndNotEmpty($values)) {
                    $spam = KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::FAVOURITE);
                    if (KT::issetAndNotEmpty($spam)) {
                        wp_die(__("Vyplnili jste nepovolený kontrolní prvek...", "KT_CORE_DOMAIN"));
                        exit;
                    }
                    if ($this->processMail($values)) {
                        wp_redirect(add_query_arg(self::PROCESSED_PARAM, "1", KT::getRequestUrl()));
                        exit;
                    }
                }
            }
            add_action(KT_PROJECT_NOTICES_ACTION, array(&$this, "renderErrorNotice"));
        }
    }

    /**
     * Vykreselní formuláře (obecně)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderForm() {
        $form = $this->getForm();
        echo $form->getFormHeader();
        echo $form->getInputsToSimpleHtml();
        echo "<button type=\"submit\">{$form->getButtonValue()}</button>";
        echo "</form>";
    }

    /**
     * Vykreslení hlášky (HTML) po úspěšném zpracování formuláře
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderSuccessNotice() {
        echo "<p class=\"success\">";
        echo __("Váš dotaz byl úspěšně odeslán a bude co nejdříve vyřízen, děkujeme.", "KT_CORE_DOMAIN");
        echo "</p>";
    }

    /**
     * Vykreslení hlášky (HTML) po neúspěšném zpracování formuláře
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderErrorNotice() {
        echo "<p class=\"error\">";
        _e("Při zpracování a odesílání kontaktního formuláře došlo k chybě. Je třeba zadat správně všechny údaje...", "KT_CORE_DOMAIN");
        $formId = self::FORM_ID;
        $repairLinkTitle = __("Opravit", "KT_CORE_DOMAIN");
        echo " <a id=\"contactFormLink\" href=\"#$formId\" data-target=\"#$formId\" title=\"$repairLinkTitle\">$repairLinkTitle</a>";
        echo "</p>";
    }

    // --- neveřejné metody ------------------------

    /**
     * Zpracování údajů z POSTu (bez spam validace) a případné zodeslání mailu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $values
     * @return boolean
     */
    protected function processMail(array $values) {
        if (count($values) > 0) {
            $firstName = htmlspecialchars(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::FIRST_NAME));
            $lastName = htmlspecialchars(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::LAST_NAME));
            $name = htmlspecialchars(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::NAME));
            $email = htmlspecialchars(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::EMAIL));
            $phone = htmlspecialchars(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::PHONE));
            $message = htmlspecialchars(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::MESSAGE));

            $fullName = $name ? : "$firstName $lastName";

            if (KT::issetAndNotEmpty($fullName) && KT::issetAndNotEmpty($email) && KT::issetAndNotEmpty($phone) && KT::issetAndNotEmpty($message) && is_email($email)) {
                $ktWpInfo = new KT_WP_Info();

                $content = sprintf(__("Jméno: %s", "KT_CORE_DOMAIN"), $fullName) . "<br />";
                $content .= sprintf(__("E-mail: %s", "KT_CORE_DOMAIN"), $email) . "<br />";
                $content .= sprintf(__("Telefon: %s", "KT_CORE_DOMAIN"), $phone) . "<br /><br />";
                $content .= sprintf(__("Zpráva:", "KT_CORE_DOMAIN"), $message) . "<br /><br />";
                $content .= $message;
                $content .= "<br /><br />---<br />";
                $content .= sprintf(__("Tento e-mail byl vygenerován pomocí kontaktního formuláře na webu: %s", "KT_CORE_DOMAIN"), $ktWpInfo->getUrl());

                $contactFormEmail = apply_filters("kt_contact_form_email_filter", $this->getFormEmail());

                $mailer = new KT_Mailer($contactFormEmail, $ktWpInfo->getName(), sprintf(__("%s - kontakt", "KT_CORE_DOMAIN"), $ktWpInfo->getName()));
                $mailer->setSenderEmail($email);
                $mailer->setSenderName($fullName);
                $mailer->setContent($content);
                return $sendResult = $mailer->send();
            }
        }
        return false;
    }

    /**
     * Základní inicializace formuláře
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Form
     */
    protected function initForm() {
        $form = new KT_Form();
        $form->setAttrId(self::FORM_ID);
        //$form->setButtonClass("");
        $form->setButtonValue(__("Odeslat dotaz", "KT_CORE_DOMAIN"));
        $form->addFieldSetByObject($this->getFieldset());
        return $this->form = $form;
    }

    /**
     * Základní inicializace fieldsetu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Form_Fieldset
     */
    protected function initFieldset() {
        $fieldset = KT_Contact_Form_Base_Config::getFieldset();
        return $this->fieldset = $fieldset;
    }

}
