<?php

/**
 * Základní config, pomocí kterého je možné sestavit formulář (pro přidání) komentáře
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Comment_Base_Config {

    const COMMENT_FIELDSET = "kt-comment";
    const AUTHOR = "author";
    const EMAIL = "email";
    const URL = "url";
    const COMMENT = "comment";

    // --- fieldsets ---------------------------

    public static function getCommentFieldset(WP_User $currentUser = null) {
        $fieldset = new KT_Form_Fieldset(self::COMMENT_FIELDSET);
        $fieldset->setPostPrefix(self::COMMENT_FIELDSET);

        if ($currentUser === null) {
            $currentUser = wp_get_current_user();
        }

        $fieldset->addText(self::FIRST_NAME, __("Autor*:", KT_DOMAIN))
                ->setValue($currentUser->display_name)
                ->setAttrMaxlength(30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Autor je povinná položka.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Autor může mít maximálně 30 znaků.", KT_DOMAIN), 30);
        $fieldset->addText(self::EMAIL, __("E-mail*:", KT_DOMAIN))
                ->setValue($currentUser->user_email)
                ->setAttrMaxlength(50)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail je povinná položka.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::EMAIL, __("E-mail musí být ve správném tvaru.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail může mít maximálně 50 znaků.", KT_DOMAIN), 50);
        $fieldset->addText(self::URL, __("URL:", KT_DOMAIN))
                ->setAttrMaxlength(100)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("URL může mít maximálně 100 znaků.", KT_DOMAIN), 100);
        $fieldset->addTextarea(self::COMMENT, __("Komentář*:", KT_DOMAIN))
                ->setAttrMaxlength(1000)
                ->addRule(KT_Field_Validator::REQUIRED, __("Komentář je povinná položka.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Komentář může mít maximálně 1000 znaků.", KT_DOMAIN), 1000);

        return $fieldset;
    }

}
