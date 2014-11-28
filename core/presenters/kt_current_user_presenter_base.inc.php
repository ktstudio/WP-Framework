<?php

/**
 * Obecný presenter pro práci s aktuálním (přihlášeným) uživatelem v rámci WP
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Current_User_Presenter_Base {

    private $currentUser;

    /**
     * Výchozí konstruktor s kontrolou na KT FW
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function __construct() {
        kt_check_loaded();
    }

    /**
     * Aktuální (přihlášený) uživatel (načítání až v případě prvního volání)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_User_Base_Model
     */
    public function getCurrentUser() {
        if (kt_isset_and_not_empty($this->currentUser)) {
            return $this->currentUser;
        } else {
            return $this->initCurrentUser();
        }
    }

    /**
     * ID aktuálního (přihlášeného) uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int|null
     */
    public function getCurrentUserId() {
        $currentUser = getCurrentUser();
        if (kt_isset_and_not_empty($currentUser)) {
            return $currentUser->ID;
        }
        return null;
    }

    /**
     * Jméno aktuálního (přihlášeného) uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|null
     */
    public function getCurrentUserFirstName() {
        $currentUser = getCurrentUser();
        if (kt_isset_and_not_empty($currentUser)) {
            return $currentUser->first_name;
        }
        return null;
    }

    /**
     * Příjmení aktuálního (přihlášeného) uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|null
     */
    public function getCurrentUserLastName() {
        $currentUser = getCurrentUser();
        if (kt_isset_and_not_empty($currentUser)) {
            return $currentUser->last_name;
        }
        return null;
    }

    /**
     * E-mail aktuálního (přihlášeného) uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|null
     */
    public function getCurrentUserEmail() {
        $currentUser = getCurrentUser();
        if (kt_isset_and_not_empty($currentUser)) {
            return $currentUser->user_email;
        }
        return null;
    }

    /**
     * Vrátí buď jméno a příjmení anebo zobrazované jméno uživatele, případně i login
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $withLogin
     * @return string
     */
    public function getCurrentUserName($withLogin = false) {
        return self::getLoggedUserName($withLogin, $this->getCurrentUser());
    }

    /**
     * Vrátí buď jméno a příjmení anebo zobrazované jméno uživatele, případně i login
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @global WP_User $current_user
     * @param boolean $withLogin
     * @param WP_User $currentUser
     * @return string
     */
    public static function getLoggedUserName($withLogin = false, WP_User $currentUser = null) {
        if ($currentUser === null) {
            global $current_user;
            get_currentuserinfo();
            $currentUser = $current_user;
        }
        $suffix = null;
        if ($withLogin) {
            $userLogin = $currentUser->user_login;
            $suffix = " [$userLogin]";
        }
        $firstName = $currentUser->first_name;
        $lastName = $currentUser->last_name;
        if (kt_isset_and_not_empty($firstName) && kt_isset_and_not_empty($lastName)) {
            return "$firstName $lastName$suffix";
        }
        //$nickName = $currentUser->nickname;
        //if ( kt_isset_and_not_empty( $nickName ) ) {
        //	return "$nickName $suffix";
        //}
        $displayName = $currentUser->display_name;
        return "$displayName$suffix";
    }

    private function initCurrentUser() {
        $userId = get_current_user_id();
        return $this->currentUser = new KT_WP_User_Base_Model($userId);
    }

}
