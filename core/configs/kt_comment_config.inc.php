<?php

/**
 * Základní config, pomocí kterého je možné sestavit formulář (pro přidání) komentáře
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Comment_Config
{

    const COMMENT_FIELDSET = "kt-comment";
    const AUTHOR = "author";
    const EMAIL = "email";
    const URL = "url";
    const COMMENT = "comment";
    const FAVOURITE = "kt-comment-favourite";
    const NONCE = "kt-comment-nonce";

    // --- fieldsets ---------------------------

    /**
     * Vrátí základní fieldset pro komentář(e)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param WP_User $currentUser
     * @param string $postPrefix
     *
     * @return KT_Form_Fieldset
     */
    public static function getCommentFieldset(WP_User $currentUser = null, $postPrefix = self::COMMENT_FIELDSET) {
        $fieldset = new KT_Form_Fieldset(self::COMMENT_FIELDSET);
        $fieldset->setPostPrefix($postPrefix);

        if ($currentUser === null) {
            $currentUser = wp_get_current_user();
        }

        $fieldset->addTextarea(self::COMMENT, __("Comment*:", "KT_CORE_DOMAIN"))
            ->setAttrMaxlength(1000)
            ->addRule(KT_Field_Validator::REQUIRED, __("Comment is required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("Comment can be up to 1000 characters.", "KT_CORE_DOMAIN"), 1000);

        $fieldset->addText(self::AUTHOR, __("Name*:", "KT_CORE_DOMAIN"))
            ->setDefaultValue($currentUser->display_name)
            ->setAttrMaxlength(50)
            ->addRule(KT_Field_Validator::REQUIRED, __("Name is required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("Name can be up to 50 characters.", "KT_CORE_DOMAIN"), 50);

        $fieldset->addText(self::EMAIL, __("E-mail*:", "KT_CORE_DOMAIN"))
            ->setInputType(KT_Text_Field::INPUT_EMAIL)
            ->setDefaultValue($currentUser->user_email)
            ->setAttrMaxlength(100)
            ->addRule(KT_Field_Validator::REQUIRED, __("E-mail is required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::EMAIL, __("Invalid e-mail address.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail can be up to 10 characters.", "KT_CORE_DOMAIN"), 100);

        $fieldset->addText(self::URL, __("Website:", "KT_CORE_DOMAIN"))
            ->setAttrMaxlength(100)
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("Website can be up to 50 characters.", "KT_CORE_DOMAIN"), 100);

        $fieldset->addField(self::getCommentFavouriteField(__("Checker:", "KT_CORE_DOMAIN")));

        $fieldset->addField(self::getCommentNonceField($fieldset->getName(), __("Checker:", "KT_CORE_DOMAIN")));

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
     *
     * @return KT_Textarea_Field
     */
    public static function getCommentTextareaField($label = "", $postPrefix = self::COMMENT_FIELDSET, $name = self::COMMENT) {
        $field = new KT_Textarea_Field($name, $label);
        $field->setPostPrefix($postPrefix);
        $field->setAttrMaxlength(1000)
            ->addRule(KT_Field_Validator::REQUIRED, __("Comments are required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("Comments can be up to 1000 characters.", "KT_CORE_DOMAIN"), 1000);
        return $field;
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
     *
     * @return KT_Text_Field
     */
    public static function getCommentFavouriteField($label = "", $postPrefix = self::COMMENT_FIELDSET, $name = self::FAVOURITE) {
        $field = new KT_Text_Field($name, $label);
        $field->setPostPrefix($postPrefix);
        $field->setPlaceholder(__("Do not fill if you are human", "KT_CORE_DOMAIN"))
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
     *
     * @return KT_WP_Nonce_Field
     */
    public static function getCommentNonceField($action = self::COMMENT_FIELDSET, $label = "", $postPrefix = self::COMMENT_FIELDSET, $name = self::NONCE) {
        $field = new KT_WP_Nonce_Field($action, $name, $label);
        $field->setPostPrefix($postPrefix);
        return $field;
    }

}
