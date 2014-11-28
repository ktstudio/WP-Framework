<?php

class KT_WP_Script_Definition extends KT_WP_Asset_Definition_Base {

    private $inFooter = false;

    /**
     * Objekt, pro zakládání a registraci JS scriptů pro přidávání
     * Řídí se:
     * @link http://codex.wordpress.org/Function_Reference/wp_register_script
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     * 
     * @param string $id
     * @param string $source
     */
    public function __construct($id, $source = null) {
        parent::__construct($id, $source);
    }

    // --- gettery ------------

    /**
     * @return boolean
     */
    public function getInFooter() {
        return $this->inFooter;
    }

    // --- settery ------------

    /**
     * Nastaví, zda se má script načítat v hlavičce nebo v patičce
     * Defaultně: false 
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     * 
     * @param string $inFooter
     * @return \kt_wp_script_handle
     */
    public function setInFooter($inFooter = true) {
        $this->inFooter = $inFooter;
        return $this;
    }

}
