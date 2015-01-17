<?php

abstract class KT_WP_Asset_Definition_Base {

    private $id = null;
    private $source = null;
    private $deps = array();
    private $version = null;
    private $enqueue = false;
    private $backEndScript = false;

    /**
     * Abstraktní třída pro definici a zakládní Assetů v rámci Wordpressu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $id
     * @param type $source
     * @return \KT_WP_Asset_Definition_Base
     * @throws KT_Not_Supported_Exception
     */
    public function __construct($id, $source = null) {
        if (is_string($id)) {
            $this->setId($id);
        }

        if ($source != null && is_string($source)) {
            $this->setSource($source);
        }

        return $this;
    }

    // --- gettery ------------

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @return array
     */
    public function getDeps() {
        return $this->deps;
    }

    /**
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return Boolean
     */
    public function getEnqueue() {
        return $this->enqueue;
    }
    
    /**
     * @return Boolean
     */
    public function getBackEndScript(){
        return $this->backEndScript;
    }

    // --- settery ------------

    /**
     * Nastaví id scriptu (identifikátor), pod kterým bude script registrován
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $id
     * @return \kt_wp_script_handle
     */
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    /**
     * Nastaví zdroj, odkdud má být script načten - URL
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $source
     * @return \kt_wp_script_handle
     */
    public function setSource($source) {
        $this->source = $source;
        return $this;
    }

    /**
     * Nastaví sadu scriptů, které musí být načteny před přidaném scriptu
     * 
     * array("jquery", "kt-core")...
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $deps
     * @return \kt_wp_script_handle
     */
    public function setDeps($deps) {
        $this->deps = $deps;
        return $this;
    }

    /**
     * Nastaví aktuální verzi scriptu - vhodné pro cashování
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $version
     * @return \kt_wp_script_handle
     */
    public function setVersion($version) {
        $this->version = $version;
        return $this;
    }

    /**
     * Nastaví, zda se má daný asset automaticky načíst do frontendu
     * DEFAUTLNĚ : false
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $enqueue
     * @return \KT_WP_Asset_Definition_Base
     */
    public function setEnqueue($enqueue = true) {
        $this->enqueue = $enqueue;
        return $this;
    }
    
    /**
     * Nastaví, zda se má script / styl volat v hlavičce administrace namísto Front-endu
     * 
     * @param Boolean $isBackEndScript
     * @return \KT_WP_Asset_Definition_Base
     */
    public function setBackEndScript($isBackEndScript = true){
        $this->backEndScript = $isBackEndScript;
        return $this;
    }

}
