<?php

class KT_WP_Script_Definition extends KT_WP_Asset_Definition_Base {

    private $inFooter = false;
    private $localizationData = array();

    /**
     * Objekt, pro zakládání a registraci JS scriptů pro přidávání
     * Řídí se:
     * @link http://codex.wordpress.org/Function_Reference/wp_register_script
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $id
     * @param string $source
     */
    public function __construct($id, $source = null) {
        parent::__construct($id, $source);
    }

    // --- gettery a settery ------------

    /**
     * @return boolean
     */
    public function getInFooter() {
        return $this->inFooter;
    }

    /**
     * Nastaví, zda se má script načítat v hlavičce nebo v patičce
     * Defaultně: false 
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $inFooter
     * @return \kt_ith_wp_script_handle
     */
    public function setInFooter($inFooter = true) {
        $this->inFooter = $inFooter;
        return $this;
    }

    /**
     * Vríté sadu lokalizovaných dat pro připravený script
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getLocalizationData() {
        return $this->localizationData;
    }

    /**
     * Nastaví lokalizované scripty
     * 
     * @param type $localizationData
     * @return \KT_ITH_WP_Script_Definition
     */
    private function setLocalizationData(array $localizationData) {
        $this->localizationData = $localizationData;
        return $this;
    }

    // --- veřejné funkce ------------------

    /**
     * Přidá do lokalizovaných dat pro script sadu dat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstuio.cz
     * 
     * @param string $name
     * @param array $localizationData
     * @return \KT_ITH_WP_Script_Definition
     */
    public function addLocalizationData($name, array $localizationData) {
        $this->localizationData[$name] = $localizationData;
        return $this;
    }
}   