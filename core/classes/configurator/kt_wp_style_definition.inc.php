<?php

class KT_WP_Style_Definition extends KT_WP_Asset_Definition_Base {

    private $media = null;

    /**
     * Objekt, pro zakládání a registraci stylů pro přidávání
     * Řídí se:
     * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_style
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

    // --- gettery ------------

    /**
     * @return string
     */
    public function getMedia() {
        return $this->media;
    }

    // --- settery ------------

    /**
     * Nastaví, jakým způsobem bude styl prezentován
     * 
     * @link http://www.w3.org/TR/CSS2/media.html#media-types
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $media
     * @return \KT_WP_Style_Definition
     */
    public function setMedia($media) {
        $this->media = $media;
        return $this;
    }

}
