<?php

class KT_WP_User_Field extends KT_Select_Field {

    const FIELD_TYPE = "users";

    private $userRole = null;
    private $allUserRoles = array();
    private $userMetaQuery = array();

    /**
     * Založení objektu typu List WP Users
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
        return $this;
    }

    // --- gettery -------------

    private function getUserRole() {
        return $this->userRole;
    }

    private function getAllUserRoles() {
        if (KT::notIssetOrEmpty($this->allUserRoles)) {
            $this->allUserRolesInit();
        }

        return $this->allUserRoles;
    }

    private function getUserMetaQuery() {
        return $this->userMetaQuery;
    }

    // --- setter --------------

    /**
     * Nastaví fieldu, zda chcete vybrat uživatele pouze s příslušnou rolí a ostatní bude ignorovat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $userRole
     * @return \KT_WP_User_Field
     */
    public function setUserRole($userRole) {
        $this->userRole = $userRole;

        return $this;
    }

    /**
     * Proměnná obsahuje všechny dostupné use roles v rámci Wordpressu
     * Řidící se interně třídou
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $allUserRoles
     * @return \KT_WP_User_Field
     */
    private function setAllUserRoles(array $allUserRoles) {
        $this->allUserRoles = $allUserRoles;

        return $this;
    }

    /**
     * Nastavení parametry user meta query pro případnou selekci uživatelů dle vlastní potřeby
     * WP_User_Query @link http://codex.wordpress.org/Class_Reference/WP_User_Query
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $userMetaQuery
     * @return \KT_WP_User_Field
     */
    public function setUserMetaQuery(array $userMetaQuery) {
        if (KT::issetAndNotEmpty($userMetaQuery)) {
            $this->userMetaQuery = $userMetaQuery;
        }

        return $this;
    }

    // --- veřejné funkce --------

    /**
     * Zavede do selektu příslušný html string pro prezentaci <option> v rámci selektu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @return type
     */
    public function getOptionContent() {
        if (KT::issetAndNotEmpty($this->getUserRole())) {
            return $html = $this->getSelectOptionByUserRole($this->getUserRole());
        }

        return $html = $this->getSelectOptionOfAllUser();
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    // --- privátní funkce --------

    /**
     * Vrátí html string s <option> kolekcí na základě uživatelské role.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @param string $userRoleName
     * @param string $optionHead
     * @return string
     */
    private function getSelectOptionByUserRole($userRoleName, $optionHead = null) {

        $html = "";

        $usersList = $this->getUsersByRole($userRoleName);
        $fieldValue = $this->getValue();
        if (KT::issetAndNotEmpty($usersList) && count($usersList) > 0) {
            if (KT::issetAndNotEmpty($optionHead)) {
                $html .= "<optgroup label=\"$optionHead\">";
            }
            foreach ($usersList as $user) {
                if ($fieldValue == $user->ID) {
                    $selected = "selected=\"selected\"";
                }
                $html .= "<option value=\"$user->ID\" $selected>$user->display_name [$user->user_login]</option>";
            }
            if (KT::issetAndNotEmpty($optionHead)) {
                $html .= "</optgroup>";
            }
        }

        return $html;
    }

    /**
     * Vrátí kolekci všech uživatelů včetně hlaviček skupiny
     * S použítím <optgroup> = název skupiny uživatelů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    private function getSelectOptionOfAllUser() {

        $html = "";

        if (KT::issetAndNotEmpty($this->getAllUserRoles())) {
            foreach ($this->getAllUserRoles() as $roleSlug => $roleName) {
                $html .= $this->getSelectOptionByUserRole($roleSlug, $roleName);
            }
        }

        return $html;
    }

    /**
     * Zkontroluje, zda požadované role uživatelů je ve WP zavedena.
     * Pokud ano, všechny je stáhne a vrátí je v poli potřeném pro
     * funkce s výpisem <optin> kolekce
     * 
     * Pokud požadovaná role není v rámci Wordpress registrovná, vrátí automaticky prázdné pole
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @param string $role
     * @return string
     */
    private function getUsersByRole($role) {
        if (!in_array($role, array_keys($this->getAllUserRoles()))) {
            return array();
        }

        $userQueryParams = array(
            "role" => $role,
            "fields" => array("ID", "display_name", "user_login")
        );
        $userMetaQuery = $this->getUserMetaQuery();

        if (KT::issetAndNotEmpty($userMetaQuery) && is_array($userMetaQuery) && count($userMetaQuery) > 0) {
            $userQueryParams["meta_query"] = $userMetaQuery;
        }

        $userQuery = new WP_User_Query($userQueryParams);

        return $userQuery->results;
    }

    /**
     * Provede inicilizaci všech registrovaných uživatelských rolí založené ve Wordpress
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return \KT_WP_User_Field
     */
    private function allUserRolesInit() {
        require_once(ABSPATH . "/wp-admin/includes/user.php");
        require_once(ABSPATH . "/wp-admin/includes/template.php");

        $editableRoles = array_reverse(get_editable_roles());

        foreach ($editableRoles as $role => $details) {
            $name = translate_user_role($details["name"]);
            $allRoles[$role] = $name;
        }

        $this->setAllUserRoles($allRoles);

        return $this;
    }

}
