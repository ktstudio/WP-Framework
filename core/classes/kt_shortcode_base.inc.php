<?php

/**
 * Základní struktura pro definici a registraci vlastních (KT) shortcodů v administraci
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Shortcode_Base implements KT_Registrable {

    private $tag;

    /**
     * Vytvoření nového shortcodu s (povinnými) parametry
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $tag
     */
    function __construct($tag) {
        if (kt_isset_and_not_empty($tag)) {
            $this->tag = $tag;
        } else {
            throw new KT_Not_Set_Argument_Exception("tag");
        }
    }

    /**
     * Vrátí název tagu, resp. shortcodu pod kterým je definovám v rámci systému, tj. WP
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getTag() {
        return $this->tag;
    }

    /**
     * Událost zpracovávající vyvolání shortcodu v rámci WP
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $args
     * @param string $content
     * 
     * @return string
     */
    public abstract function handler(array $args, $content = null);

    /**
     * Registrace shortcodu v rámci WP na základě zadaných parametrů
     */
    public function register() {
        add_shortcode($this->getTag(), array(&$this, "handler"));
    }

}
