<?php

class KT_User_Profile_Config {

    const USER_PROFILE_FIELDSET = "kt-user-profile";
    const FIRST_NAME = "first_name";
    const LAST_NAME = "last_name";
    const EMAIL = "user_email";
    const PHONE = "user_phone";
    const PASSWORD = "user_pass";
    const PASSWORD_CONFIRM = "kt-user-profile-password-confirm";

    // --- fieldsets ---------------------------

    public static function getUserProfileFieldset(WP_User $currentUser = null, $withPhone = true, $isPhoneRequired = true) {
        $fieldset = new KT_Form_Fieldset(self::USER_PROFILE_FIELDSET);
        $fieldset->setPostPrefix(self::USER_PROFILE_FIELDSET);

        if ($currentUser === null) {
            $currentUser = wp_get_current_user();
        }

        $fieldset->addText(self::FIRST_NAME, __("Jméno:", KT_DOMAIN))
                ->setValue($currentUser->user_firstname)
                ->addRule(KT_Field_Validator::REQUIRED, __("Jméno je povinná položka.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Jméno může mít maximálně 30 znaků.", KT_DOMAIN), 30);
        $fieldset->addText(self::LAST_NAME, __("Příjmení:", KT_DOMAIN))
                ->setValue($currentUser->user_lastname)
                ->addRule(KT_Field_Validator::REQUIRED, __("Příjmení je povinná položka.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Příjmení může mít maximálně 30 znaků.", KT_DOMAIN), 30);
        $fieldset->addText(self::EMAIL, __("E-mail:", KT_DOMAIN))
                ->setValue($currentUser->user_email)
                ->addRule(KT_Field_Validator::REQUIRED, __("E-mail je povinná položka.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::EMAIL, __("E-mail musí být ve správném tvaru.", KT_DOMAIN))
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("E-mail může mít maximálně 30 znaků.", KT_DOMAIN), 30);
        $userPhoneKey = KT_User_Profile_Config::PHONE;
        if ($withPhone) {
            $phoneField = $fieldset->addText(self::PHONE, __("Telefon:", KT_DOMAIN))
                    ->setValue($currentUser->$userPhoneKey)
                    ->setPlaceholder(__("+420 606 707 808", KT_DOMAIN))
                    ->setToolTip(__("Telefon by měl být v mezinárodní formě, např. \"+420 606 707 808\"...", KT_DOMAIN))
                    ->addRule(KT_Field_Validator::MAX_LENGTH, __("Telefon může mít maximálně 16 znaků.", KT_DOMAIN), 16);
            if ($isPhoneRequired) {
                $phoneField->addRule(KT_Field_Validator::REQUIRED, __("Telefon je povinná položka.", KT_DOMAIN));
            }
        }
        $fieldset->addText(self::PASSWORD, __("Heslo:", KT_DOMAIN))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Heslo musí mít mininálně 7 znaků.", KT_DOMAIN), 7)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Heslo může mít maximálně 20 znaků.", KT_DOMAIN), 20);
        $fieldset->addText(self::PASSWORD_CONFIRM, __("Potvrzení hesla:", KT_DOMAIN))
                ->setInputType(KT_Text_Field::INPUT_PASSWORD)
                ->addRule(KT_Field_Validator::MIN_LENGTH, __("Potvrzení hesla musí mít mininálně 7 znaků.", KT_DOMAIN), 7)
                ->addRule(KT_Field_Validator::MAX_LENGTH, __("Potvrzení hesla může mít maximálně 20 znaků.", KT_DOMAIN), 20)
                ->setTooltip(__("Zadejte ještě jednou vaše nové heslo.", KT_DOMAIN));

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
