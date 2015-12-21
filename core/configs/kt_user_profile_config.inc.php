<?php

/**
 * Základní config, pomocí kterého je možné sestavit formulář uživatelského profilu
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_User_Profile_Config {

    const USER_PROFILE_FIELDSET = "kt-user-profile";
    const FIRST_NAME = "first_name";
    const LAST_NAME = "last_name";
    const EMAIL = "user_email";
    const PHONE = "user_phone";
    const PASSWORD = "user_pass";

    // --- fieldsets ---------------------------

    public static function getSimpleProfileFieldset(WP_User $currentUser = null, $withPhone = true, $isPhoneRequired = true) {
        $fieldset = new KT_Form_Fieldset(self::USER_PROFILE_FIELDSET);
        $fieldset->setPostPrefix(self::USER_PROFILE_FIELDSET);

        if ($currentUser === null) {
            $currentUser = wp_get_current_user();
        }

        $fieldset->addText(self::FIRST_NAME, __("Jméno*:", "KT_CORE_DOMAIN"))
                ->setValue($currentUser->user_firstname)
                ->setAttrMaxlength(30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Jméno je povinná položka.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Jméno může mít maximálně 30 znaků.", "KT_CORE_DOMAIN"), 30);
        $fieldset->addText(self::LAST_NAME, __("Příjmení*:", "KT_CORE_DOMAIN"))
                ->setValue($currentUser->user_lastname)
                ->setAttrMaxlength(30)
                ->addRule(KT_Field_Validator::REQUIRED, __("Příjmení je povinná položka.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Příjmení může mít maximálně 30 znaků.", "KT_CORE_DOMAIN"), 30);
        $fieldset->addText(self::EMAIL, __("E-mail*:", "KT_CORE_DOMAIN"))
                ->setValue($currentUser->user_email)
                ->setAttrMaxlength(50)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail je povinná položka.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::EMAIL, __("E-mail musí být ve správném tvaru.", "KT_CORE_DOMAIN"))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail může mít maximálně 30 znaků.", "KT_CORE_DOMAIN"), 50);
        $userPhoneKey = KT_User_Profile_Config::PHONE;
        if ($withPhone) {
            $phoneLabel = ($isPhoneRequired) ? __("Telefon*:", "KT_CORE_DOMAIN") : __("Telefon:", "KT_CORE_DOMAIN");
            $phoneField = $fieldset->addText(self::PHONE, $phoneLabel)
                    ->setValue($currentUser->$userPhoneKey)
                    ->setAttrMaxlength(16)
                    ->setPlaceholder(__("+420 606 707 808", "KT_CORE_DOMAIN"))
                    ->setToolTip(__("Telefon by měl být v mezinárodní formě, např. \"+420 606 707 808\"...", "KT_CORE_DOMAIN"))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("Telefon může mít maximálně 16 znaků.", "KT_CORE_DOMAIN"), 16);
            if ($isPhoneRequired) {
                $phoneField->addRule(KT_Field_Validator::REQUIRED, __("Telefon je povinná položka.", "KT_CORE_DOMAIN"));
            }
        }

        return $fieldset;
    }

    public static function getUserProfileFieldset(WP_User $currentUser = null, $withPhone = true, $isPhoneRequired = true) {
        $fieldset = self::getSimpleProfileFieldset($currentUser, $withPhone, $isPhoneRequired);

        $fieldset->addText(self::PASSWORD, __("Heslo*:", "KT_CORE_DOMAIN"))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->setAttrMaxlength(20)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Heslo musí mít mininálně 7 znaků.", "KT_CORE_DOMAIN"), 7)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Heslo může mít maximálně 20 znaků.", "KT_CORE_DOMAIN"), 20);
        $fieldset->addText(self::PASSWORD_CONFIRM, __("Potvrzení hesla*:", "KT_CORE_DOMAIN"))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->setAttrMaxlength(20)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Potvrzení hesla musí mít mininálně 7 znaků.", "KT_CORE_DOMAIN"), 7)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Potvrzení hesla může mít maximálně 20 znaků.", "KT_CORE_DOMAIN"), 20)
                ->setTooltip(__("Zadejte ještě jednou vaše nové heslo.", "KT_CORE_DOMAIN"));

        return $fieldset;
    }

    const PASSWORD_FIELDSET = "kt-user-profile-password-fieldset";
    const PASSWORD_OLD = "kt-user-profile-password-old";
    const PASSWORD_NEW = "kt-user-profile-password-new";
    const PASSWORD_CONFIRM = "kt-user-profile-password-confirm";

    public static function getPasswordFieldset() {
        $fieldset = new KT_Form_Fieldset(self::PASSWORD_FIELDSET, __("Změna hesla", "KT_CORE_DOMAIN"));
        $fieldset->setPostPrefix(self::PASSWORD_FIELDSET);

        $fieldset->addText(self::PASSWORD_OLD, __("Staré heslo*:", "KT_CORE_DOMAIN"))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->setAttrMaxlength(20)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Staré heslo musí mít mininálně 5 znaků.", "KT_CORE_DOMAIN"), 5)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Staré heslo může mít maximálně 20 znaků.", "KT_CORE_DOMAIN"), 20);

        $fieldset->addText(self::PASSWORD_NEW, __("Heslo*:", "KT_CORE_DOMAIN"))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->setAttrMaxlength(20)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Nové heslo musí mít mininálně 7 znaků.", "KT_CORE_DOMAIN"), 7)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Nové heslo může mít maximálně 20 znaků.", "KT_CORE_DOMAIN"), 20);
        $fieldset->addText(self::PASSWORD_CONFIRM, __("Potvrzení hesla*:", "KT_CORE_DOMAIN"))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->setAttrMaxlength(20)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Potvrzení hesla musí mít mininálně 7 znaků.", "KT_CORE_DOMAIN"), 7)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Potvrzení hesla může mít maximálně 20 znaků.", "KT_CORE_DOMAIN"), 20)
                ->setTooltip(__("Zadejte ještě jednou vaše nové heslo.", "KT_CORE_DOMAIN"));

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
