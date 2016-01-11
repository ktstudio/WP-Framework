<?php

/**
 * Základní config, pomocí kterého je možné sestavit formulář (pro přidání) komentáře
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Comment_Config {

    const COMMENT_FIELDSET = "kt-comment";
    const AUTHOR = "author";
    const EMAIL = "email";
    const URL = "url";
    const COMMENT = "comment";
    const FAVOURITE = "kt-comment-favourite";
    const NONCE = "kt-comment-nonce";

    // --- fieldsets ---------------------------

    /**
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param WP_User $currentUser
     * @return \KT_Form_Fieldset
     */
    public static function getCommentFieldset(WP_User $currentUser = null) {
        $fieldset = new KT_Form_Fieldset(self::COMMENT_FIELDSET);
        $fieldset->setPostPrefix(self::COMMENT_FIELDSET);

        if ($currentUser === null) {
            $currentUser = wp_get_current_user();
        }

        $fieldset->addText(self::FIRST_NAME, __("Autor*:", "KT_CORE_DOMAIN"))
                ->setDefaultValue($currentUser->display_name)
                ->setAttrMaxlength(30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Autor je povinná položka.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Autor může mít maximálně 30 znaků.", "KT_CORE_DOMAIN"), 30);
        $fieldset->addText(self::EMAIL, __("E-mail*:", "KT_CORE_DOMAIN"))
                ->setDefaultValue($currentUser->user_email)
                ->setAttrMaxlength(50)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail je povinná položka.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::EMAIL, __("E-mail musí být ve správném tvaru.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail může mít maximálně 50 znaků.", "KT_CORE_DOMAIN"), 50);
        $fieldset->addText(self::URL, __("URL:", "KT_CORE_DOMAIN"))
                ->setAttrMaxlength(100)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("URL může mít maximálně 100 znaků.", "KT_CORE_DOMAIN"), 100);
        $fieldset->addTextarea(self::COMMENT, __("Komentář*:", "KT_CORE_DOMAIN"))
                ->setAttrMaxlength(1000)
                ->addRule(KT_Field_Validator::REQUIRED, __("Komentář je povinná položka.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Komentář může mít maximálně 1000 znaků.", "KT_CORE_DOMAIN"), 1000);
        $fieldset->addField(self::getCommentFavouriteField(__("Kontrola:", "KT_CORE_DOMAIN")));
        $fieldset->addField(self::getCommentNonceField($fieldset->getName(), __("Kontrola:", "KT_CORE_DOMAIN")));

        return $fieldset;
    }

    /**
     * Vrátí kontrolní (skrytý) field typu nezadávejte text (pro roboty)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $label
     * @param string $postPrefix
     * @param string $name
     * @return \KT_Text_Field
     */
    public static function getCommentFavouriteField($label = "", $postPrefix = self::COMMENT_FIELDSET, $name = self::FAVOURITE) {
        $field = new KT_Text_Field($name, $label);
        $field->setPostPrefix($postPrefix);
        $field->setPlaceholder(__("Nevyplňujte, pokud jste člověk", "KT_CORE_DOMAIN"))
                ->addAttrClass("hidden")
                ->addAttribute("maxlength", 30);
        return $field;
    }

    /**
     * Vrátí kontrolní (skrytý) field typu WP nonce
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $action
     * @param string $label
     * @param string $postPrefix
     * @param string $name
     * @return \KT_WP_Nonce_Field
     */
    public static function getCommentNonceField($action = self::COMMENT_FIELDSET, $label = "", $postPrefix = self::COMMENT_FIELDSET, $name = self::NONCE) {
        $field = new KT_WP_Nonce_Field($action, $name, $label);
        $field->setPostPrefix($postPrefix);
        return $field;
    }

}
