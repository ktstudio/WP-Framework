<?php

/**
 * Pomocná třída pro vytažení informací o/z WP ve stylu OOP
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Info {

    private $currentUser;

    public function getName() {
        return get_bloginfo("name");
    }

    public function getDescription() {
        return get_bloginfo("description");
    }

    public function getWpUrl() {
        return get_bloginfo("wpurl");
    }

    public function getUrl() {
        return get_bloginfo("url");
    }

    public function getAdminEmail() {
        return get_bloginfo("admin_email");
    }

    public function getUserLogin() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $this->getCurrentUser()->user_login;
        }
        return null;
    }

    public function getUserEmail() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $this->getCurrentUser()->user_email;
        }
        return null;
    }

    public function getUserFirstName() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $this->getCurrentUser()->user_firstname;
        }
        return null;
    }

    public function getUserLastName() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $this->getCurrentUser()->user_lastname;
        }
        return null;
    }

    public function getUserFullName() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $currentUser->first_name . " " . $currentUser->last_name;
        }
        return null;
    }

    public function getUserDisplayName() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $this->getCurrentUser()->display_name;
        }
        return null;
    }

    public function getUserId() {
        $currentUser = $this->getCurrentUser();
        if (KT::issetAndNotEmpty($currentUser)) {
            return $this->getCurrentUser()->ID;
        }
        return null;
    }

    public function getCurrentUser() {
        if (KT::issetAndNotEmpty($this->currentUser)) {
            return $this->currentUser;
        }
        return ($this->currentUser = wp_get_current_user());
    }

}
