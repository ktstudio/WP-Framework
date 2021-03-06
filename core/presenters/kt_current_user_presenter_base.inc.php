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
        if (KT::issetAndNotEmpty($this->currentUser)) {
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
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $currentUser->getId();
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
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $currentUser->getFirstName();
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
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $currentUser->getLastName();
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
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $currentUser->getEmail();
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
     * Vrátí permalink na požadovaný nebo výchozí (RSS) feed podle aktuálního uživatele (ID)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $feed
     * @return mixed string|null
     */
    public function getCurrentUserFeedLink($feed = "") {
        $id = $this->getCurrentUserId();
        if (KT::isIdFormat($id)) {
            return get_author_feed_link($id, $feed);
        }
        return null;
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
        if (KT::issetAndNotEmpty($firstName) && KT::issetAndNotEmpty($lastName)) {
            return "$firstName $lastName$suffix";
        }
        //$nickName = $currentUser->nickname;
        //if ( KT::issetAndNotEmpty( $nickName ) ) {
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
