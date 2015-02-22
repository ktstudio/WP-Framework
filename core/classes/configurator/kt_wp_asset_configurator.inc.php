<?php

final class KT_WP_Asset_Configurator {

    private $scriptCollection = array();
    private $styleCollection = array();

    // --- gettery ------------

    /**
     * @return array
     */
    public function getScriptCollection() {
        return $this->scriptCollection;
    }

    /**
     * @return array
     */
    public function getStyleCollection() {
        return $this->styleCollection;
    }

    // --- settery ------------

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

    // --- veřejné funkce ------------

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

    // --- privátní funkce ------------
}
