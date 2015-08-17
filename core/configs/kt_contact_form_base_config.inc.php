<?php

/**
 * Obecný config pro definici kontakního formuláře
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Contact_Form_Base_Config {

    const FORM_PREFIX = "kt-contact-form";
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
    public static function getFieldset() {
        $fieldset = new KT_Form_Fieldset(self::FORM_PREFIX, __("Jméno", KT_DOMAIN));
        $fieldset->setPostPrefix(self::FORM_PREFIX);

        $fieldset->addText(self::NAME, __("Jméno*:", KT_DOMAIN))
                ->setPlaceholder(__("Jméno*", KT_DOMAIN))
                ->addAttribute("maxlength", 30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Jméno je povinná položka", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Jméno může mít maximálně 30 znaků", KT_DOMAIN), 30);

        $fieldset->addText(self::EMAIL, __("E-mail*:", KT_DOMAIN))
                ->setPlaceholder(__("E-mail*", KT_DOMAIN))
                ->addAttribute("maxlength", 50)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail je povinná položka", KT_DOMAIN))
                ->addRule(KT_Field_Validator::EMAIL, __("E-mail je ve špatném tvaru", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail může mít maximálně 350 znaků", KT_DOMAIN), 50);

        $fieldset->addText(self::PHONE, __("Telefon*:", KT_DOMAIN))
                ->setPlaceholder(__("Telefon*", KT_DOMAIN))
                ->addAttribute("maxlength", 30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Telefon je povinná položka", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Telefon může mít maximálně 30 znaků", KT_DOMAIN), 30);

        $fieldset->addTextarea(self::MESSAGE, __("Zpráva*:", KT_DOMAIN))
                ->setPlaceholder(__("Zpráva*", KT_DOMAIN))
                ->addAttribute("maxlength", 1000)
                ->addRule(KT_Field_Validator::REQUIRED, __("Zpráva je povinná položka", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Zpráva může mít maximálně 1000 znaků", KT_DOMAIN), 1000);

        $fieldset->addText(self::FAVOURITE, __("Kontrola:", KT_DOMAIN))
                ->setPlaceholder(__("Nevyplňujte, pokud jste člověk", KT_DOMAIN))
                ->addAttrClass("hidden")
                ->addAttribute("maxlength", 30);

        $fieldset->addWpNonce(self::NONCE, __("Kontrola:", KT_DOMAIN));

        return $fieldset;
    }

}
