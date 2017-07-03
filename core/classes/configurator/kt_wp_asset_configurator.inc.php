<?php

/**
 * Třída pro definici příloh, resp. skiptů, stylů apod. v rámci KT WP konfigurátoru
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
final class KT_WP_Asset_Configurator implements KT_WP_IConfigurator {

    private $scriptCollection = array();
    private $styleCollection = array();

    // --- getry & setry ---------------------------

    /**
     * Vrátí celou kolekci scriptů pro registraci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getScriptCollection() {
        return $this->scriptCollection;
    }

    /**
     * Nastaví celou kolekci scriptů pro registraci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $scriptCollection
     * @return \KT_WP_Scripts_Registrator
     */
    private function setScriptCollection(array $scriptCollection) {
        $this->scriptCollection = $scriptCollection;
        return $this;
    }

    /**
     * Vrátí celou kolekci scriptů pro registraci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getStyleCollection() {
        return $this->styleCollection;
    }

    /**
     * Nastaví celou kolekci scriptů pro registraci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $styleCollection
     * @return \KT_WP_Asset_Configurator
     */
    private function setStyleCollection($styleCollection) {
        $this->styleCollection = $styleCollection;
        return $this;
    }

    // --- veřejné metody ---------------------------

    /**
     * Přidá jeden script k registraci do kolekce dle nastavení
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $id // identifikátor scriptu
     * @param string $source // url cesta ke scriptu
     * 
     * @return \KT_WP_Script_Definition
     */
    public function addScript($id, $source = null) {
        $script = $this->scriptCollection[$id] = new KT_WP_Script_Definition($id, $source);
        return $script;
    }

    /**
     * Přidá jeden style k registraci do kolekce dle nastavení
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $id // identifikátor stylu
     * @param string $source // url cesta ke stylu
     * 
     * @return \KT_WP_Style_Definition
     */
    public function addStyle($id, $source = null) {
        $style = $this->styleCollection[$id] = new KT_WP_Style_Definition($id, $source);
        return $style;
    }

    /**
     * Přidá další kolekci scriptů do koelekce stávající
     * NEPROBÍHÁ OVĚŘENÍ, ZDA KOLEKCE JE OPRAVDU PLNÁ OBJEKTŮ SE SCRIPTY
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $scriptCollection
     * @return \KT_WP_Asset_Configurator
     */
    public function addToScriptCollection(array $scriptCollection) {
        if (KT::notIssetOrEmpty($scriptCollection)) {
            return $this;
        }
        $currentScriptCollection = $this->getScriptCollection();
        if (KT::notIssetOrEmpty($currentScriptCollection)) {
            $this->setScriptCollection($scriptCollection);
            return $this;
        }
        $newScriptCollection = array_merge($currentScriptCollection, $scriptCollection);
        $this->setScriptCollection($newScriptCollection);
        return $this;
    }

    /**
     * 
     * Přidá další kolekci stylů do kolekce stávající
     * NEPROBÍHÁ OVĚŘENÍ, ZDA KOLEKCE JE OPRAVDU PLNÁ OBJEKTŮ SE STYLY
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $styleCollection
     * @return \KT_WP_Asset_Configurator
     */
    public function addToStyleCollection(array $styleCollection) {
        if (KT::notIssetOrEmpty($styleCollection)) {
            return $this;
        }
        $currentStyleCollection = $this->getStyleCollection();
        if (KT::notIssetOrEmpty($currentStyleCollection)) {
            $this->setStyleCollection($styleCollection);
            return $this;
        }
        $newStyleCollection = array_merge($currentStyleCollection, $styleCollection);
        $this->setScriptCollection($newStyleCollection);
        return $this;
    }

    public function initialize() {
        add_action("init", array($this, "registerScriptsAction"));
        add_action("init", array($this, "registerStyleAction"));
        add_action("wp_enqueue_scripts", array($this, "enqueueScriptAction"));
        add_action("wp_enqueue_scripts", array($this, "enqueueStyleAction"));
        add_action("admin_enqueue_scripts", array($this, "enqueueScriptActionForAdmin"));
        add_action("admin_enqueue_scripts", array($this, "enqueueStyleActionForAdmin"));
    }

    // Akce a filtry -- NEVOLAT veřejně

    /**
     * Provede registraci všech scriptů, které byly přidáno do assetConfigurátoru
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function registerScriptsAction() {

        foreach ($this->getScriptCollection() as $script) {
            /* @var $script \KT_WP_Script_Definition */
            if (KT::notIssetOrEmpty($script->getId()) || KT::notIssetOrEmpty($script->getSource())) {
                continue;
            }

            wp_register_script($script->getId(), $script->getSource(), $script->getDeps(), $script->getVersion(), $script->getInFooter());
            if (KT::issetAndNotEmpty($script->getLocalizationData())) {
                foreach ($script->getLocalizationData() as $name => $data) {
                    wp_localize_script($script->getId(), $name, $data);
                }
            }
        }
    }

    /**
     * Provede registraci všechy stylů, které byly přidáno do assetConfigurátoru
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function registerStyleAction() {

        foreach ($this->getStyleCollection() as $style) {
            /* @var $style \KT_WP_Style_Definition */

            if (KT::notIssetOrEmpty($style->getId()) || KT::notIssetOrEmpty($style->getSource())) {
                continue;
            }

            wp_register_style($style->getId(), $style->getSource(), $style->getDeps(), $style->getVersion(), $style->getMedia());
        }
    }

    /**
     * Provede vložení scriptů, které mají nastaveno načtení, do frotnendu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function enqueueScriptAction() {

        foreach ($this->getScriptCollection() as $script) {
            /* @var $script \KT_WP_Script_Definition */
            if (!wp_script_is($script->getId(), "registered")) {
                continue;
            }

            if ($script->getBackEndScript()) {
                continue;
            }

            if ($script->getEnqueue() === true) {
                wp_enqueue_script($script->getId());
            }
        }
    }

    /**
     * Provede registraci všechy stylů, které byly přidáno do assetConfigurátoru
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function enqueueStyleAction() {
    
        foreach ($this->getStyleCollection() as $style) {
            /* @var $style \KT_WP_Style_Definition */

            if (!wp_style_is($style->getId(), "registered")) {
                continue;
            }

            if ($style->getBackEndScript()) {
                continue;
            }

            wp_enqueue_style($style->getId());
        }
    }

    /**
     * Provede registraci všechy stylů, které byly přidáno do assetConfigurátoru v rámci admin sekce
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function enqueueStyleActionForAdmin() {
       

        foreach ($this->getStyleCollection() as $style) {
            /* @var $style \KT_WP_Style_Definition */

            if (!wp_style_is($style->getId(), "registered")) {
                continue;
            }

            if (!$style->getBackEndScript()) {
                continue;
            }

            wp_enqueue_style($style->getId());
        }
    }

    /**
     * Provede vložení scriptů, které mají nastaveno načtení, do admin sekce
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function enqueueScriptActionForAdmin() {
     
        foreach ($this->getScriptCollection() as $script) {
            /* @var $script \KT_WP_Script_Definition */
            if (!wp_script_is($script->getId(), "registered")) {
                continue;
            }

            if (!$script->getBackEndScript()) {
                continue;
            }

            if ($script->getEnqueue() === true) {
                wp_enqueue_script($script->getId());
            }
        }
    }

    // --- neveřejné metody ---------------------------
}
