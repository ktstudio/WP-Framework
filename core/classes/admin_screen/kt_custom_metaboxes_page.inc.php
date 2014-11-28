<?php

class KT_Custom_Metaboxes_Page extends KT_Custom_Metaboxes_Base {

    const TOP_LVL_PAGE_PREFIX = "toplevel_page_";

    private $title = null;
    private $menuTitle = null;
    private $capability = null;
    private $slug = null;
    private $icon = null;
    private $position = null;
    private $page = null;

    /**
     * Založení WP_Screen pro zadávání metaboxů na vlastním layoutu a přídání nové stránky do navigace Wordpress v admin sekci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * Parametry dle funkce: add_menu_page()
     * @link http://codex.wordpress.org/Function_Reference/add_menu_page
     * 
     * @param type $title - Titulek v menu (attr title)
     * @param type $menuTitle - Název v menu
     * @param type $capability - Právo editace
     * @param type $slug - slug v url adrese při volán íbase
     * @param type $icon - url iconka v menu
     * @param type $position - pozice v menu - defaultně 55
     */
    public function __construct($title, $menuTitle, $capability, $slug, $iconUrl, $position = 55) {
        $this->setTitle($title)
                ->setMenuTitle($menuTitle)
                ->setCapability($capability)
                ->setSlug($slug)
                ->setIcon($iconUrl)
                ->setPosition($position);

        $this->setPage(self::TOP_LVL_PAGE_PREFIX . $this->getSlug());

        parent::__construct();
    }

    // --- gettery ----------------------

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
    public function getIcon() {
        return $this->icon;
    }

    /**
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * @return string;
     */
    public function getPage() {
        return $this->page;
    }

    // --- settery ----------------------

    /**
     * Nastaví attr title odkazu v menu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @param string $title
     */
    private function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Nastaví text, který se zobrazí jako odkaz v menu administrace
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $menuTitle
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
    public function setCapability($capability) {
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
     * Nastavení ikonu, která se bude v menu u odkazu zobrazovat
     * 
     * Lze zadat URL na obrázek nebo od WP 3.9 použít dashicons
     * @link https://make.wordpress.org/core/2014/04/16/dashicons-in-wordpress-3-9/
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     * 
     * @param string $icon
     */
    private function setIcon($icon) {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Nastavení pozici odkazu v menu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz 
     * 
     * @param type $position
     */
    public function setPosition($position) {
        $this->position = $position;

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

    /**
     * Provede inicializaci definované stránky v a založení báse pro metaboxy.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.KTStudio.cz
     */
    public function initPage() {

        $callBackScreenFunction = $this->getCallbackFunctionByActionScreen();

        $screenId = add_menu_page(
                $this->getTitle(), $this->getMenuTitle(), $this->getCapability(), $this->getSlug(), $callBackScreenFunction, $this->getIcon(), $this->getPosition()
        );

        if (is_array($callBackScreenFunction)) {
            add_action('load-' . $screenId, array($this, 'doPageAction'), 9);
            add_action('admin_footer-' . $screenId, array($this, 'renderFooterScripts'));
        }

        return $this;
    }

    // --- statické funkce ------------

    public static function getCustomMetaboxPageScreenName($slug) {
        return $pageScreenName = self::TOP_LVL_PAGE_PREFIX . $slug;
    }

}
