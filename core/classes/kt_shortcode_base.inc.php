<?php

/**
 * Základní struktura pro definici a registraci vlastních (KT) shortcodů v administraci
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Shortcode_Base implements KT_Registrable {

    private $tag;
    private $buttonKey;
    private $buttonScriptPath;

    /**
     * Vytvoření nového shortcodu s (povinnými) parametry
     * Pozn. registrace tlačítka v editoru je možná, jen když je zadán klíč i cesta skriptu pro tlačítko v editoru
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $tag
     */
    function __construct($tag, $buttonKey = null, $buttonScriptPath = null) {
        if (KT::issetAndNotEmpty($tag)) {
            $this->tag = $tag;
        } else {
            throw new KT_Not_Set_Argument_Exception("tag");
        }
        $this->buttonKey = $buttonKey;
        $this->buttonScriptPath = $buttonScriptPath;
    }

    // --- getry & setry ---------------------------

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
     * Vrátí klíč tlačítka, pod kterým je/bude shortcode definován v editoru, pokud byl zadán při definici
     * Pozn. registrace tlačítka v editoru je možná, jen když je zadán klíč i cesta skriptu pro tlačítko v editoru
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getButtonKey() {
        return $this->buttonKey;
    }

    /**
     * Vrátí cestu skriptu tlačítka, pro editor, pokud byla zadána při definici
     * Pozn. registrace tlačítka v editoru je možná, jen když je zadán klíč i cesta skriptu pro tlačítko v editoru
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getButtonScriptPath() {
        return $this->buttonScriptPath;
    }

    // --- veřejné metody ---------------------------

    /**
     * Událost zpracovávající vyvolání shortcodu v rámci WP
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array|null $attributes
     * @param string $content
     * 
     * @return string
     */
    public abstract function handler($attributes, $content = null);

    /**
     * Registrace shortcodu v rámci WP na základě zadaných parametrů
     */
    public function register() {
        add_shortcode($this->getTag(), array(&$this, "handler"));
        $buttonKey = $this->getButtonKey();
        $buttonScriptPath = $this->getButtonScriptPath();
        if (KT::issetAndNotEmpty($buttonKey) && KT::issetAndNotEmpty($buttonScriptPath)) {
            add_action("admin_init", array(&$this, "adminInitAction"));
        }
    }

    // --- hooky a filtry ---------------------------

    /**
     * Akce pro inicializaci tlačítko shortcodu v editoru
     * Pozn.: NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function adminInitAction() {
        if (current_user_can("edit_posts") && current_user_can("edit_pages")) {
            add_filter("mce_external_plugins", array(&$this, "editorButtonPlugin"));
            add_filter("mce_buttons", array(&$this, "editorButtonFilter"));
        }
    }

    /**
     * Filtr pro tlačítko shortcodu v editoru
     * Pozn.: NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $buttons
     * @return type
     */
    public function editorButtonFilter(array $buttons) {
        $buttonKey = $this->getButtonKey();        
        array_push($buttons, "|", $buttonKey);
        return $buttons;
    }

    /**
     * Filtr pro skript tlačítka shortcodu v editoru
     * Pozn.: NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $plugins
     * @return array
     */
    public function editorButtonPlugin(array $plugins) {
        $buttonKey = $this->getButtonKey();
        $buttonScriptPath = $this->getButtonScriptPath();
        $plugins[$buttonKey] = $buttonScriptPath;
        return $plugins;
    }

}
