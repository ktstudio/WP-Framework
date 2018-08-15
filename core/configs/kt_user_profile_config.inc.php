<?php

/**
 * Základní config, pomocí kterého je možné sestavit formulář uživatelského profilu
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_User_Profile_Config {

    const FORM_PREFIX = "kt-user";
    const USER_PROFILE_FIELDSET = "kt-user-profile";
    const FIRST_NAME = "first_name";
    const LAST_NAME = "last_name";
    const EMAIL = "user_email";
    const PHONE = "user_phone";
    const PASSWORD = "user_pass";
    const NONCE = "kt-user-nonce";

    // --- fieldsets ---------------------------

    public static function getSimpleProfileFieldset(WP_User $currentUser = null, $withPhone = true, $isPhoneRequired = true) {
        $fieldset = new KT_Form_Fieldset(self::USER_PROFILE_FIELDSET);
        $fieldset->setPostPrefix(self::USER_PROFILE_FIELDSET);

        $firstNameField = $fieldset->addText(self::FIRST_NAME, __("First name*:", "KT_CORE_DOMAIN"))
            ->setAttrMaxlength(30)
            ->addRule(KT_Field_Validator::REQUIRED, __("First name is required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The first name can be up to 30 characters.", "KT_CORE_DOMAIN"), 30);
        $lastNameField = $fieldset->addText(self::LAST_NAME, __("Last name*:", "KT_CORE_DOMAIN"))
            ->setAttrMaxlength(30)
            ->addRule(KT_Field_Validator::REQUIRED, __("Last name is required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The last name can be up to 30 characters.", "KT_CORE_DOMAIN"), 30);
        $emailField = $fieldset->addText(self::EMAIL, __("E-mail*:", "KT_CORE_DOMAIN"))
            ->setAttrMaxlength(50)
            ->addRule(KT_Field_Validator::REQUIRED, __("E-mail is required.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::EMAIL, __("Invalid e-mail address.", "KT_CORE_DOMAIN"))
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The e-mail address can be up to 50 characters.", "KT_CORE_DOMAIN"), 50);

        if ($withPhone) {
            $phoneLabel = ($isPhoneRequired) ? __("Phone*:", "KT_CORE_DOMAIN") : __("Phone:", "KT_CORE_DOMAIN");
            $phoneField = $fieldset->addText(self::PHONE, $phoneLabel)
                ->setAttrMaxlength(16)
                ->setPlaceholder(__("+420 606 707 808", "KT_CORE_DOMAIN"))
                ->setToolTip(__("The phone should be in international form, for example: \"+420 606 707 808\"...", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("The phone can have a maximum of 16 characters.", "KT_CORE_DOMAIN"), 16);
            if ($isPhoneRequired) {
                $phoneField->addRule(KT_Field_Validator::REQUIRED, __("Phone is required.", "KT_CORE_DOMAIN"));
            }
        }
        $fieldset->addWpNonce(self::NONCE);

        if ($currentUser === null && is_user_logged_in()) {
            $currentUser = wp_get_current_user();
        }
        if (KT::issetAndNotEmpty($currentUser)) {
            $firstNameField->setDefaultValue($currentUser->user_firstname);
            $lastNameField->setDefaultValue($currentUser->user_lastname);
            $emailField->setDefaultValue($currentUser->user_email);
            if ($withPhone) {
                $userPhoneKey = KT_User_Profile_Config::PHONE;
                $phoneField->setDefaultValue($currentUser->$userPhoneKey);
            }
        }

        return $fieldset;
    }

    public static function getUserProfileFieldset(WP_User $currentUser = null, $withPhone = true, $isPhoneRequired = true) {
        $fieldset = self::getSimpleProfileFieldset($currentUser, $withPhone, $isPhoneRequired);

        $fieldset->addText(self::PASSWORD, __("Password*:", "KT_CORE_DOMAIN"))
            ->setInputType(KT_Text_Field::INPUT_PASSWORD)
            ->setAttrMaxlength(20)
            ->addRule(KT_Field_Validator::MIN_LENGTH, __("The password can have a minimum of 7 characters.", "KT_CORE_DOMAIN"), 7)
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The password can have a maximum of 30 characters.", "KT_CORE_DOMAIN"), 30);
        $fieldset->addText(self::PASSWORD_CONFIRM, __("Password confirmation*:", "KT_CORE_DOMAIN"))
            ->setInputType(KT_Text_Field::INPUT_PASSWORD)
            ->setAttrMaxlength(20)
            ->addRule(KT_Field_Validator::MIN_LENGTH, __("The password confirmation can have a minimum of 7 characters.", "KT_CORE_DOMAIN"), 7)
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The password confirmation can have a maximum of 30 characters.", "KT_CORE_DOMAIN"), 30)
            ->setTooltip(__("Type again your new password.", "KT_CORE_DOMAIN"));

        return $fieldset;
    }

    const PASSWORD_FIELDSET = "kt-user-profile-password-fieldset";
    const PASSWORD_OLD = "kt-user-profile-password-old";
    const PASSWORD_NEW = "kt-user-profile-password-new";
    const PASSWORD_CONFIRM = "kt-user-profile-password-confirm";

    public static function getPasswordFieldset() {
        $fieldset = new KT_Form_Fieldset(self::PASSWORD_FIELDSET, __("Password change", "KT_CORE_DOMAIN"));
        $fieldset->setPostPrefix(self::PASSWORD_FIELDSET);

        $fieldset->addText(self::PASSWORD_OLD, __("Current password*:", "KT_CORE_DOMAIN"))
            ->setInputType(KT_Text_Field::INPUT_PASSWORD)
            ->setAttrMaxlength(20)
            ->addRule(KT_Field_Validator::MIN_LENGTH, __("The current password can have a minimum of 7 characters.", "KT_CORE_DOMAIN"), 7)
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The current password can have a maximum of 30 characters.", "KT_CORE_DOMAIN"), 30);

        $fieldset->addText(self::PASSWORD_NEW, __("New password*:", "KT_CORE_DOMAIN"))
            ->setInputType(KT_Text_Field::INPUT_PASSWORD)
            ->setAttrMaxlength(20)
            ->addRule(KT_Field_Validator::MIN_LENGTH, __("The new password can have a minimum of 7 characters.", "KT_CORE_DOMAIN"), 7)
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The new password can have a maximum of 30 characters.", "KT_CORE_DOMAIN"), 30);
        $fieldset->addText(self::PASSWORD_CONFIRM, __("Password confirmation*:", "KT_CORE_DOMAIN"))
            ->setInputType(KT_Text_Field::INPUT_PASSWORD)
            ->setAttrMaxlength(20)
            ->addRule(KT_Field_Validator::MIN_LENGTH, __("The password confirmation can have a minimum of 7 characters.", "KT_CORE_DOMAIN"), 7)
            ->addRule(KT_Field_Validator::MAX_LENGTH, __("The password confirmation can have a maximum of 30 characters.", "KT_CORE_DOMAIN"), 30)
            ->setTooltip(__("Type again your new password.", "KT_CORE_DOMAIN"));

        return $fieldset;
    }

    // --- meta (values) ---------------------------

    public static function getCurrentDisplayName() {
        $currentUser = wp_get_current_user();
        return $currentUser->display_name;
    }

    public static function theCurrentDisplayName() {
        return self::getCurrentDisplayName();
    }

    public static function getCurrentFirstName() {
        $currentUser = wp_get_current_user();
        return $currentUser->user_firstname;
    }

    public static function theCurrentFirstName() {
        return self::getCurrentFirstName();
    }

    public static function getCurrentLastName() {
        $currentUser = wp_get_current_user();
        return $currentUser->user_lastname;
    }

    public static function theCurrentLastName() {
        return self::getCurrentLastName();
    }

    public static function getCurrentEmail() {
        $currentUser = wp_get_current_user();
        return $currentUser->user_email;
    }

    public static function theCurrentEmail() {
        return self::getCurrentEmail();
    }

    public static function getCurrentPhone() {
        return get_the_author_meta(self::PHONE);
    }

    public static function theCurrentPhone() {
        return self::getCurrentPhone();
    }

}
