<?php

class KT_WP_User_Data_Manager extends KT_Data_Manager_Base {

    private $allUserRoles = array();
    private $userMetaQuery = array();

    // --- gettery -------------

    /**
     * Přepis původní funkce getData za účelem inicializace dat
     * 
     * @return array
     */
    public function getData() {

        if (KT::notIssetOrEmpty(parent::getData())) {
            $this->dataInit();
        }

        return parent::getData();
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

    // --- settery --------------


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
    public function setAllUserRoles(array $allUserRoles) {
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

    // --- privátní funkce --------

    /**
     * Načte všechny uživatele na základě vybraných rolí
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * 
     * @return \KT_WP_User_Data_Manager
     */
    private function dataInit() {
        $userData = array();
        $userData = $this->getAllUsersData();
        $this->setData($userData);
        return $this;
    }

    /**
     * Vrátí kolekci všech uživatelů včetně
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    private function getAllUsersData() {

        $data = array();

        if (KT::issetAndNotEmpty($this->getAllUserRoles())) {
            foreach ($this->getAllUserRoles() as $roleSlug => $roleName) {
                $newUsersData = $this->getDataOfUserRole($roleSlug);
                $data += $newUsersData;
            }
        }

        return $data;
    }

    /**
     * Vráti data v rámci jedné uživatelské role
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @param type $userRoleName
     * @return string
     */
    private function getDataOfUserRole($userRoleName) {

        $data = array();

        $usersByRole = $this->getUsersByRole($userRoleName);

        if (KT::issetAndNotEmpty($usersByRole)) {
            foreach ($usersByRole as $user) {
                $data[$user->ID] = $user->display_name . " [$user->user_login]";
            }
        }


        return $data;
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
