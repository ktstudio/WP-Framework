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
    public static function getFieldset($splittedName = false, $exactedPhone = true) {
        $fieldset = new KT_Form_Fieldset(self::FORM_PREFIX, __("Kontakt", "KT_CORE_DOMAIN"));
        $fieldset->setPostPrefix(self::FORM_PREFIX);

        if ($splittedName) {
            $fieldset->addText(self::FIRST_NAME, __("Jméno*:", "KT_CORE_DOMAIN"))
                    ->setPlaceholder(__("Jméno*", "KT_CORE_DOMAIN"))
                    ->addAttribute("maxlength", 30)
                    ->addRule(KT_Field_Validator::REQUIRED, __("Jméno je povinná položka", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("Jméno může mít maximálně 30 znaků", "KT_CORE_DOMAIN"), 30);
            $fieldset->addText(self::LAST_NAME, __("Příjmení*:", "KT_CORE_DOMAIN"))
                    ->setPlaceholder(__("Příjmení*", "KT_CORE_DOMAIN"))
                    ->addAttribute("maxlength", 30)
                    ->addRule(KT_Field_Validator::REQUIRED, __("Příjmení je povinná položka", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("Příjmení může mít maximálně 30 znaků", "KT_CORE_DOMAIN"), 30);
        } else {
            $fieldset->addText(self::NAME, __("Jméno*:", "KT_CORE_DOMAIN"))
                    ->setPlaceholder(__("Jméno*", "KT_CORE_DOMAIN"))
                    ->addAttribute("maxlength", 30)
                    ->addRule(KT_Field_Validator::REQUIRED, __("Jméno je povinná položka", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("Jméno může mít maximálně 30 znaků", "KT_CORE_DOMAIN"), 30);
        }

        $fieldset->addText(self::EMAIL, __("E-mail*:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("E-mail*", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 100)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail je povinná položka", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::EMAIL, __("E-mail je ve špatném tvaru", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail může mít maximálně 100 znaků", "KT_CORE_DOMAIN"), 100);

        $phoneField = $fieldset->addText(self::PHONE, __("Telefon*:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("Telefon*", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Telefon je povinná položka", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Telefon může mít maximálně 30 znaků", "KT_CORE_DOMAIN"), 30);
        if ($exactedPhone) {
            $phoneField->addRule(KT_Field_Validator::REGULAR, __("Telefon je ve špatném tvaru", "KT_CORE_DOMAIN"), "^((\+|0)(420|421) ?)?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$");
        }

        $fieldset->addTextarea(self::MESSAGE, __("Zpráva*:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("Zpráva*", "KT_CORE_DOMAIN"))
                ->addAttribute("maxlength", 1000)
                ->addRule(KT_Field_Validator::REQUIRED, __("Zpráva je povinná položka", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Zpráva může mít maximálně 1000 znaků", "KT_CORE_DOMAIN"), 1000);

        $fieldset->addText(self::FAVOURITE, __("Kontrola:", "KT_CORE_DOMAIN"))
                ->setPlaceholder(__("Nevyplňujte, pokud jste člověk", "KT_CORE_DOMAIN"))
                ->addAttrClass("hidden")
                ->addAttribute("maxlength", 30);

        $fieldset->addWpNonce(self::NONCE, __("Kontrola:", "KT_CORE_DOMAIN"));

        return $fieldset;
    }

}
