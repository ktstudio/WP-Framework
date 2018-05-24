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

    protected $form;
    protected $fieldset;
    protected $wasProcessed;

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
            $processedParam = filter_input(INPUT_GET, $this->getProcessdParam(), FILTER_SANITIZE_ENCODED);
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
        if (isset($this->form)) {
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
        if (isset($this->fieldset)) {
            return $this->fieldset;
        }
        return $this->initFieldset();
    }

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
     * Vrátí URL parametr formuláře pro/po zpracování, možné přepsat, výchozí se je konstanta PROCESSED_PARAM
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getProcessdParam() {
        return self::PROCESSED_PARAM;
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
        if (KT::arrayIssetAndNotEmpty($_POST) && array_key_exists(KT_Contact_Form_Base_Config::FORM_PREFIX, $_POST)) {
            $form = $this->getForm();
            if (!$form->nonceValidate()) {
                wp_die(__("Error processing resource addresses...", "KT_CORE_DOMAIN"));
                exit;
            }
            $form->validate();
            if (!$form->hasError()) {
                $values = filter_input(INPUT_POST, KT_Contact_Form_Base_Config::FORM_PREFIX, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
                if (KT::arrayIssetAndNotEmpty($values)) {
                    $spam = filter_var(KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::FAVOURITE), FILTER_SANITIZE_STRING);
                    if (KT::issetAndNotEmpty($spam)) {
                        wp_die(__("You filled out unauthorized control element...", "KT_CORE_DOMAIN"));
                        exit;
                    }
                    if ($this->processMail($values)) {
                        wp_redirect(add_query_arg($this->getProcessdParam(), "1", KT::getRequestUrl()));
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
        echo "<button type=\"submit\" class=\"{$form->getButtonClass()}\">{$form->getButtonValue()}</button>";
        echo $form->getFormFooter();
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
        echo " <a id=\"contact-form-link\" href=\"#{$this->getFormId()}\" data-kt-target=\"#{$this->getFormId()}\" title=\"$repairLinkTitle\">$repairLinkTitle</a>";
        echo "</p>";
    }

    // --- neveřejné metody ------------------------

    /**
     * Hláška pro úspěšné zpracování formuláře
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    protected function getSuccessMessage() {
        return __("Your question was sent successfully and will be discharged as soon as possible, thank you.", "KT_CORE_DOMAIN");
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
        return __("When preparing and sending the contact form error. You need to enter all your information correctly...", "KT_CORE_DOMAIN");
    }

    /**
     * Titulek odeslaného e-mailu
     * Pozn. přijímá při formátování parametr název webu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    protected function getEmailTitle() {
        return __("%s - contact", "KT_CORE_DOMAIN");
    }
    
    /**
     * Podpis, resp. hláška pod čarou e-mailu
     * Pozn. přijímá při formátování parametr URL stránky
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    protected function getEmailSignature() {
        return __("The e-mail was generated by using the contact form on the Website: %s", "KT_CORE_DOMAIN");
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
        return __("Repair", "KT_CORE_DOMAIN");
    }

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
            $agreementValue = KT::arrayTryGetValue($values, KT_Contact_Form_Base_Config::AGREEMENT);
            $agreement = KT_Switch_Field::getSwitchConvertedValue(KT::arrayIssetAndNotEmpty($agreementValue) ? KT_Switch_Field::YES : KT_Switch_Field::NO);

            $fullName = $name ? : "$firstName $lastName";

            if (KT::issetAndNotEmpty($fullName) && KT::issetAndNotEmpty($email) && KT::issetAndNotEmpty($phone) && KT::issetAndNotEmpty($message) && is_email($email)) {
                $ktWpInfo = new KT_WP_Info();
                $requestUrl = KT::getRequestUrl();
                $requestLink = "<a href=\"$requestUrl\">$requestUrl</a>";

                $content = sprintf(__("Name: %s", "KT_CORE_DOMAIN"), $fullName) . "<br>";
                $content .= sprintf(__("E-mail: %s", "KT_CORE_DOMAIN"), $email) . "<br>";
                $content .= sprintf(__("Phone: %s", "KT_CORE_DOMAIN"), $phone) . "<br><br>";
                $content .= __("Message:", "KT_CORE_DOMAIN") . "<br><br>$message<br><br>";
                $content .= __("Agreement with the processing of personal data:", "KT_CORE_DOMAIN") . " $agreement<br><br>";
                $content .= sprintf(__("Done by URL: %s", "KT_CORE_DOMAIN"), $requestLink) . "<br><br>---<br>";
                $content .= sprintf($this->getEmailSignature(), $ktWpInfo->getUrl());

                $contactFormEmail = apply_filters("kt_contact_form_email_filter", $this->getFormEmail());

                $mailer = new KT_Mailer($contactFormEmail, $ktWpInfo->getName(), sprintf($this->getEmailTitle(), $ktWpInfo->getName()));
                $mailer->setReplyToEmail($email);
                $mailer->setContent($content);
                $sendResult = $mailer->send();
                $this->logMailProcessed($sendResult, sprintf(__("E-mail for %s <%s> from URL %s done by: %s.", "KT_CORE_DOMAIN"), $fullName, $email, $requestUrl, $sendResult));
                return $sendResult;
            }
        }
        return false;
    }

    /**
     * Pomocná funkce pro zápis výsledku odeslání mailu do KT Logu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    protected function logMailProcessed($sendResult, $logMessage) {
        $onlyForSignedUsers = KT_Logger::getOnlyForSignedUsers();
        KT_Logger::setOnlyForSignedUsers(false);
        if ($sendResult) {
            KT_Logger::info($logMessage);
        } else {
            KT_Logger::warning($logMessage);
        }
        KT_Logger::setOnlyForSignedUsers($onlyForSignedUsers);
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
        $form->setAttrId($this->getFormId());
        $form->setButtonClass("submitButton");
        $form->setButtonValue(__("Send request", "KT_CORE_DOMAIN"));
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
