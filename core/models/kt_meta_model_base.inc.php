<?php

/**
 * Obecný základ pro modely, která obsahují meta informace
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Meta_Model_Base extends KT_Model_Base {

    private $metas = array();
    private $metaPrefix;

    public function __construct($metaPrefix = null) {
        $this->metaPrefix = $metaPrefix;
    }

    // --- getry & setry ---------------------

    /**
     * Vrátí případný meta prefix
     * 
     * @return string
     */
    public final function getMetaPrefix() {
        return $this->metaPrefix;
    }

    /**
     * Vrátí pole s metas
     * 
     * @return array
     */
    public final function getMetas() {
        if (KT::notIssetOrEmpty($this->metas)) {
            $this->initMetas();
        }
        return $this->metas;
    }

    /**
     * Nastavení pole s metas
     * 
     * @param array $metas
     * @return \KT_Meta_Model_Base
     */
    protected final function setMetas(array $metas) {
        $this->metas = $metas;
        return $this;
    }

    // --- veřejné metody ---------------------

    /**
     * Vrátí hodnotu z pole metas na základě zadaného (meta) klíče
     *
     * @param array $metas
     * @param string $key
     * @return string
     */
    public function getMetaValue($key) {
        $metas = $this->getMetas();
        if (array_key_exists($key, $metas)) {
            $value = $metas[$key];
            if (isset($value)) {
                return $value;
            }
        }
        return null;
    }

    /**
     * @deprecated since version 1.3
     * @see getMetaValue
     */
    public function getMetaValueByKey($key) {
        return $this->getMetaValue($key);
    }

    // --- neveřejné metody ---------------------

    /**
     * Inicializace pole metas @see setMetas
     */
    protected abstract function initMetas();
}
