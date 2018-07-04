<?php

/**
 * Obecný config pro definici kontakního formuláře
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Contact_Form_Base_Config {

    const FORM_PREFIX = "kt-contact-form";
    const FIRST_NAME = "kt-contact-form-first-name";
    const LAST_NAME = "kt-contact-form-last-name";
    const NAME = "kt-contact-form-name";
    const EMAIL = "kt-contact-form-email";
    const PHONE = "kt-contact-form-phone";
    const MESSAGE = "kt-contact-form-message";
    const AGREEMENT = "kt-contact-form-agreement";
    const FAVOURITE = "kt-contact-form-favourite";
    const NONCE = "kt-contact-form-nonce";

    /**
     * Vrátí výchozí fieldset kontaktního formuláře
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Form_Fieldset
     */
    public static function getFieldset($splittedName = false, $exactedPhone = true, $requiredPhone = true) {
        $fieldset = new KT_Form_Fieldset(self::FORM_PREFIX, __("Contact", "KT_CORE_DOMAIN"));
        $fieldset->setPostPrefix(self::FORM_PREFIX);

        if ($splittedName) {
            $fieldset->addText(self::FIRST_NAME, __("First name*:", "KT_CORE_DOMAIN"))
                    ->setPlaceholder(__("First name*", "KT_CORE_DOMAIN"))
                    ->addAttribute("maxlength", 30)
                    ->addRule(KT_Field_Validator::REQUIRED, __("First name is required", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("The first can be up to 30 characters", "KT_CORE_DOMAIN"), 30);
            $fieldset->addText(self::LAST_NAME, __("Last name*:", "KT_CORE_DOMAIN"))
                    ->setPlaceholder(__("Last name*", "KT_CORE_DOMAIN"))
                    ->addAttribute("maxlength", 30)
                    ->addRule(KT_Field_Validator::REQUIRED, __("Last name is required", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("The last name can be up to 30 characters", "KT_CORE_DOMAIN"), 30);
        } else {
            $fieldset->addText(self::NAME, __("Name*:", "KT_CORE_DOMAIN"))
                    ->setPlaceholder(__("Name*", "KT_CORE_DOMAIN"))
                    ->addAttribute("maxlength", 30)
                    ->addRule(KT_Field_Validator::REQUIRED, __("Name is required", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("The name can be up to 30 characters", "KT_CORE_DOMAIN"), 30);
        }

        $fieldset->addText(self::EMAIL, __("E-mail*:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("E-mail*", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 100)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail is required", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::EMAIL, __("Invalid e-mail address", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("The e-mail can be up to 100 characters", "KT_CORE_DOMAIN"), 100);

        $phoneField = $fieldset->addText(self::PHONE, __("Phone*:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("Phone*", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 30)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("The phone number can be up to 30 characters", "KT_CORE_DOMAIN"), 30);
        if ($requiredPhone) {
            $phoneField->addRule(KT_Field_Validator::REQUIRED, __("Phone is required", "KT_CORE_DOMAIN"));
        }
        if ($exactedPhone) {
            $phoneField->addRule(KT_Field_Validator::REGULAR, __("Invalid phone number", "KT_CORE_DOMAIN"), "^((\+|0)(420|421) ?)?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$");
        }

        $fieldset->addTextarea(self::MESSAGE, __("Message*:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("Message*", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 1000)
                ->addRule(KT_Field_Validator::REQUIRED, __("Message is required", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("The message can be up to 1000 characters", "KT_CORE_DOMAIN"), 1000);

        $privacyPolicySuffix = null;
        $privacyPolicyPageId = get_option("wp_page_for_privacy_policy");
        if (KT::isIdFormat($privacyPolicyPageId)) {
            $privacyPolicyPost = get_post($privacyPolicyPageId);
            if (KT::issetAndNotEmpty($privacyPolicyPost)) {
                $privacyPolicyModel = new KT_WP_Post_Base_Model($privacyPolicyPost);
                $privacyPolicySuffix = " <span class=\"privacy-policy-link\">(<a href=\"{$privacyPolicyModel->getPermalink()}\" title=\"{$privacyPolicyModel->getTitleAttribute()}\" target=\"_blank\">{$privacyPolicyModel->getTitle()}</a>)</span>";
            }
        }
        $agreementField = $fieldset->addCheckbox(self::AGREEMENT, __("Agreement:", "KT_CORE_DOMAIN"))
                ->setOptionsData([KT_Switch_Field::YES => __("I agree with the processing of personal data", "KT_CORE_DOMAIN") . $privacyPolicySuffix]);
        if (KT::issetAndNotEmpty($privacyPolicySuffix)) {
            $agreementField->setFilterSanitize(FILTER_DEFAULT);
        }

        $fieldset->addText(self::FAVOURITE, __("Checker:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("Do not fill if your are human", "KT_CORE_DOMAIN"))
                ->addAttrClass("hidden")
                ->addAttribute("maxlength", 30);

        $fieldset->addWpNonce(self::NONCE, __("Checker:", "KT_CORE_DOMAIN"));

        return $fieldset;
    }

}
