<?php

class KT_Custom_Metaboxes_Subpage extends KT_Custom_Metaboxes_Base {

    private $parentPage;
    private $title;
    private $menuTitle;
    private $capability;
    private $slug;
    private $page;

    /**
     * Založení WP_Screen pro zadávání metaboxů na vlastním layoutu a přídání nové podstránky do navigace Wordpress v admin sekci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * Parametry dle funkce: add_submenu_page()
     * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
     * 
     * @param string $parentPage - nadřazená stránky, pod kterou bude podstránky zozbrazena
     * @param string $title - titulek menu - attr title
     * @param string $menuTitle - Název v menu
     * @param type $capability - Právo editace
     * @param type $slug - slug v url adrese při volán íbase
     * @param type $icon_url - url iconka v menu
     */
    public function __construct($parentPage, $title, $menuTitle, $capability, $slug) {

        $this->setParentPage($parentPage)
                ->setTitle($title)
                ->setMenuTitle($menuTitle)
                ->setCapability($capability)
                ->setSlug($slug);

        parent::__construct();
    }

    // --- gettery -----------------------

    /**
     * @return string
     */
    public function getParentPage() {
        return $this->parentPage;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMenuTitle() {
        return $this->menuTitle;
    }

    /**
     * @return string
     */
    public function getCapability() {
        return $this->capability;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getPage() {
        return $this->page;
    }

    // --- settery -----------------------

    /**
     * Nastavení název nadřazené stránky, kde se má podstránka zobrazit
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $parentPage
     * @return \KT_Custom_Metaboxes_Subpage
     */
    private function setParentPage($parentPage) {
        $this->parentPage = $parentPage;
        return $this;
    }

    /**
     * Nastaví titulek stránky
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $title
     * @return \KT_Custom_Metaboxes_Subpage
     */
    private function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Nastaví název stránky v menu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $menuTitle
     * @return \KT_Custom_Metaboxes_Subpage
     */
    private function setMenuTitle($menuTitle) {
        $this->menuTitle = $menuTitle;
        return $this;
    }

    /**
     * Nastavení capability úrovně stránky pro editace uživatelem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * Řídí se dle @link http://codex.wordpress.org/Roles_and_Capabilities
     * 
     * @param string $capability
     */
    private function setCapability($capability) {
        $this->capability = $capability;
        return $this;
    }

    /**
     * Nastaví paremetr URL, pod kterým bude stránka dostupná
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param type $slug
     */
    private function setSlug($slug) {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Nastavení base name screenu po založení Wordpress stránky
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $page
     */
    private function setPage($page) {
        $this->page = $page;
        return $this;
    }

    // --- veřejné funkce -----------------------

    public function initPage() {

        $callBackScreenFunction = $this->getCallbackFunctionByActionScreen();
        $screenId = add_submenu_page($this->getParentPage(), $this->getTitle(), $this->getMenuTitle(), $this->getCapability(), $this->getSlug(), $callBackScreenFunction);
        $this->setPage($screenId);

        if (is_array($callBackScreenFunction)) {
            add_action('load-' . $this->getPage(), array($this, 'doPageAction'), 9);
            add_action('admin_footer-' . $this->getPage(), array($this, 'renderFooterScripts'));
        }

        return $this;
    }

}
