<?php

/**
 * Abstraktní třída pro definici a zakládní Assetů v rámci Wordpressu
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
abstract class KT_WP_Asset_Definition_Base {

    const VERSION_CACHE_PREFIX = "kt-wp-asset-version-";
    const DEFAULT_VERSION_EXPIRATION = 86400; // 1 den

    private $id = null;
    private $source = null;
    private $deps = array();
    private $version = null;
    private $isAutoVersion = false;
    private $autoVersion = null;
    private $autoVersionExpiration = null;
    private $enqueue = false;
    private $forBackEnd = false;

    /**
     * Abstraktní třída pro definici a zakládní Assetů v rámci Wordpressu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $id
     * @param string $source
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

    // --- getry ---------------------------

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
        if (KT::issetAndNotEmpty($this->version)) {
            return $this->version;
        }
        if ($this->getIsAutoVersion()) {
            return $this->getAutoVersion();
        }
        return null;
    }

    /**
     * @return boolean
     */
    public function getIsAutoVersion() {
        return $this->isAutoVersion;
    }

    /**
     * @return string
     */
    public function getAutoVersion() {
        if (KT::issetAndNotEmpty($this->autoVersion)) {
            return $this->autoVersion;
        }
        $autoVersionExpiration = $this->getAutoVersionExpiration();
        $isAutoVersionExpiration = ($autoVersionExpiration > 0);
        if ($isAutoVersionExpiration) {
            $cachedAutoVersionKey = self::VERSION_CACHE_PREFIX . $this->getId();
            $cachedAutoVersion = KT::arrayTryGetValue($_COOKIE, $cachedAutoVersionKey);
            if (KT::issetAndNotEmpty($cachedAutoVersion)) {
                return $this->autoVersion = $cachedAutoVersion;
            }
        }
        $source = $this->getSource();
        if (KT::issetAndNotEmpty($source)) {
            $sourceHeaders = get_headers($source, 1);
            if (KT::arrayIssetAndNotEmpty($sourceHeaders)) {
                if (stristr($sourceHeaders[0], "200")) {
                    $lastModified = KT::arrayTryGetValue($sourceHeaders, "Last-Modified");
                    if (KT::issetAndNotEmpty($lastModified)) {
                        $lastModifiedDateTime = new \DateTime($lastModified);
                        $autoVersion = $lastModifiedDateTime->getTimestamp();
                        if ($isAutoVersionExpiration) {
                            setcookie($cachedAutoVersionKey, "$autoVersion", time() + $autoVersionExpiration, "/");
                        }
                        return $this->autoVersion = $autoVersion;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @return int
     */
    public function getAutoVersionExpiration() {
        return $this->autoVersionExpiration;
    }

    /**
     * @return boolean
     */
    public function getEnqueue() {
        return $this->enqueue;
    }

    /**
     * @deprecated since version 1.6
     * @see getForBackEnd
     */
    public function getBackEndScript() {
        return $this->forBackEnd;
    }

    /**
     * @return boolean
     */
    public function getForBackEnd() {
        return $this->forBackEnd;
    }

    // --- setry ---------------------------

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
        $this->autoVersion = null; // pro případný reset
        return $this;
    }

    /**
     * Nastaví sadu scriptů, které musí být načteny před přidaním scriptu
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
     * Pozn.: má přednost před aplikací auto version
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
     * @param boolean $enqueue
     * @return \KT_WP_Asset_Definition_Base
     */
    public function setEnqueue($enqueue = true) {
        $this->enqueue = $enqueue;
        return $this;
    }

    /**
     * @deprecated since version 1.6
     * @see setForBackEnd
     */
    public function setBackEndScript($isBackEndScript = true) {
        $this->forBackEnd = $isBackEndScript;
        return $this;
    }

    /**
     * Nastaví, zda se má script / styl volat v hlavičce administrace namísto front-endu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $forBackEnd
     * @return \KT_WP_Asset_Definition_Base
     */
    public function setForBackEnd($forBackEnd = true) {
        $this->forBackEnd = $forBackEnd;
        return $this;
    }

    // --- veřejné metody ---------------------------

    /**
     * Povolí aplikaci automatická verze - vhodné pro cachování
     * Pozor: pokud není aktivní cachování, tak hrozí zpomalení potencionálně v řádu desítek až stovek milisekund s každým souborem, především pro externí soubory mimo vlastní server
     * Pozn.: aplikuje se pouze, pokud není zadána verze explicitně
     * Pozn.: pro cachování využívá cookies nebo zadejte NULL (= bez cache)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed int|null $expiration
     * @return \kt_wp_script_handle
     */
    public function enableAutoVersion($expiration = self::DEFAULT_VERSION_EXPIRATION) {
        $this->isAutoVersion = true;
        $this->autoVersionExpiration = KT::tryGetInt($expiration);
        return $this;
    }

}
