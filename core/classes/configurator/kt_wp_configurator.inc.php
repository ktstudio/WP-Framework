<?php

/**
 * Třída pro nastavení základních funkcí Wordpressu
 * Po zadání nastavení a různých parametrů je
 * potřeba configurátor initializovat
 * ->initialize()
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
final class KT_WP_Configurator {

    // theme support
    const TS_POST_FORMATS = "post-formats";
    const TS_POST_THUMBNAIL = "post-thumbnails";
    const THEME_SUBPAGE_PREFIX = "appearance_page_";
    const THEME_SETTING_PAGE_SLUG = "kt-theme-setting";
    const POST_TYPE_ARCHIVE_OBJECT_KEY = "kt-post-type-archive";

    private $wpMenuCollection = array();
    private $widgetsCollection = array();
    private $sidebarCollection = array();
    private $postTypesFeatures = array();
    private $excerptLenght = null;
    private $excerptText = null;
    private $metaboxRemover = null;
    private $pageRemover = null;
    private $widgetRemover = null;
    private $themeSettingPage = false;
    private $deleteImagesWithPost = false;
    private $displayLogo = true;
    private $assetsConfigurator = null;
    private $imagesLazyLoading = null;
    private $postArchiveMenu = null;
    private $sessionEnable = false;

    // --- gettery ----------------------

    /**
     * @return array
     */
    private function getMenusCollection() {
        return $this->wpMenuCollection;
    }

    /**
     * @return array
     */
    private function getWidgetsCollection() {
        return $this->widgetsCollection;
    }

    /**
     *
     * @return array
     */
    private function getSidebarCollection() {
        return $this->sidebarCollection;
    }

    /**
     * @return array
     */
    private function getPostTypesFeatures() {
        return $this->postTypesFeatures;
    }

    /**
     * @return int
     */
    public function getExcerptLength() {
        return $this->excerptLenght;
    }

    /**
     * @return str
     */
    public function getExcerptText() {
        return $this->excerptText;
    }

    /**
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    private function getMetaboxRemover() {
        return $this->metaboxRemover;
    }

    /**
     * @return \KT_WP_Page_Remover_Configurator
     */
    private function getPageRemover() {
        return $this->pageRemover;
    }

    /**
     * @return \KT_WP_Widget_Remover_Configurator
     */
    private function getWidgetRemover() {
        return $this->widgetRemover;
    }

    /**
     * @return boolean
     */
    private function getThemeSettingPage() {
        return $this->themeSettingPage;
    }

    /**
     * @return boolean
     */
    private function getDeleteImagesWithPost() {
        return $this->deleteImagesWithPost;
    }

    /**
     * @return boolean
     */
    private function getDisplayLogo() {
        return $this->displayLogo;
    }

    /**
     * @return boolean
     */
    private function getImagesLazyLoading() {
        return $this->imagesLazyLoading;
    }

    /**
     * @return boolean
     */
    private function getPostArchiveMenu() {
        return $this->postArchiveMenu;
    }

    /**
     * @return \KT_WP_Asset_Configurator
     */
    private function getAssetsConfigurator() {
        return $this->assetsConfigurator;
    }

    /**
     * @return boolean
     */
    private function getSessionEnable() {
        return $this->sessionEnable;
    }

    // --- settery ----------------------

    /**
     * Nastaví KT_WP_Metabox_Remover_Configurátor do objektu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param KT_WP_Metabox_Remover_Configurator $metaboxRemover
     * @return \KT_WP_Configurator
     */
    private function setMetaboxRemover(KT_WP_Metabox_Remover_Configurator $metaboxRemover) {
        $this->metaboxRemover = $metaboxRemover;

        return $this;
    }

    /**
     * Nastaví KT_WP_Page_Remover_Configurator do objektu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param KT_WP_Page_Remover_Configurator $pageRemover
     * @return \KT_WP_Configurator
     */
    private function setPageRemover(KT_WP_Page_Remover_Configurator $pageRemover) {
        $this->pageRemover = $pageRemover;

        return $this;
    }

    /**
     * Nastaví KT_WP_Widget_Remover_Configurator do objektu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param KT_WP_Widget_Remover_Configurator $widgetRemover
     * @return \KT_WP_Configurator
     */
    private function setWidgetRemover(KT_WP_Widget_Remover_Configurator $widgetRemover) {
        $this->widgetRemover = $widgetRemover;
        return $this;
    }

    /**
     * Nastavení délku excreptu pří výpisu entit.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param int $excerptLenght
     * @return \KT_WP_Configurator
     */
    public function setExcerptLength($excerptLenght) {
        $excerptLenght = KT::tryGetInt($excerptLenght);

        if (KT::issetAndNotEmpty($excerptLenght)) {
            $this->excerptLenght = $excerptLenght;
        }

        return $this;
    }

    /**
     * Nastaví ukončovací text pří výpisu excreptu entit.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $excerptText
     * @return \KT_WP_Configurator
     */
    public function setExcerptText($excerptText) {
        $this->excerptText = $excerptText;

        return $this;
    }

    /**
     * Nastaví, zda se má automaticky založit stránky pro nastavení šablony s metaboxy
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param boolean $themeSettingPage
     * @return \KT_WP_Configurator
     */
    public function setThemeSettingPage($themeSettingPage = true) {
        $this->themeSettingPage = $themeSettingPage;
        return $this;
    }

    /**
     * Nastaví, aby se při smazání postu (i custom post_type) došlo k smazání všch attachmentů, které jsou u postu nahrané
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param boolean $deleteImagesWithPost
     * @return \KT_WP_Configurator
     */
    public function setDeleteImagesWithPost($deleteImagesWithPost = true) {
        $this->deleteImagesWithPost = $deleteImagesWithPost;
        return $this;
    }

    /**
     * Nastaví / zruší zobrazení KT Loga na login stránce Wordpress
     * POKUD NÁS CHCETE PODPOŘIT, TUTO FUNKCI NEPOUŽÍVEJTE :-) DĚKUJEME !
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param boolean $displayLogo
     * @return \KT_WP_Configurator
     */
    public function setDisplayLogo($displayLogo = false) {
        $this->displayLogo = $displayLogo;
        return $this;
    }

    /**
     * Nastaví KT_WP_Asset_Configurator do objektu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param \KT_WP_Asset_Configurator $assetsConfigurator
     * @return \KT_WP_Configurator
     */
    public function setAssetsConfigurator(KT_WP_Asset_Configurator $assetsConfigurator) {
        $this->assetsConfigurator = $assetsConfigurator;
        return $this;
    }

    /**
     * Nastaví, zda se má v rámci šablony zapnout SESSION pro WP
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $sessionEnable
     * @return \KT_WP_Configurator
     */
    public function setSessionEnable($sessionEnable = true) {
        $this->sessionEnable = $sessionEnable;
        return $this;
    }

    /**
     * Aktivace MetaBoxu s archivy post (typů) v/do menu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $postArchiveMenu
     * @return \KT_WP_Configurator
     */
    public function setPostArchiveMenu($postArchiveMenu = true) {
        $this->postArchiveMenu = $postArchiveMenu;
        return $this;
    }

    /**
     * Aktivace automatické aplikace lazy loadingu na obrázky pomocí skriptu unveil
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function setImagesLazyLoading($imagesLazyLoading) {
        $this->imagesLazyLoading = $imagesLazyLoading;
    }

    // --- veřejné funkce ---------------

    /**
     * Provede inicializaci celého nastavení. Nuté volat při zadání nastavení
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     */
    public function initialize() {
        // registrace menu
        add_action("init", array($this, "registerMenusAction"));

        // registrace widgetů
        add_action("widgets_init", array($this, "registerWidgetsAction"));

        // registrace sidebar
        add_action("widgets_init", array($this, "registersSidebarsAction"));

        // registrace post type support (features)
        add_action("init", array($this, "registerPostTypeSupportAction"));

        // délka excreptu
        if (KT::issetAndNotEmpty($this->getExcerptLength()) && $this->getExcerptLength() > 0) {
            add_filter("excerpt_length", array($this, "getExcerptLength"));
        }

        // text excreptu
        if (KT::issetAndNotEmpty($this->getExcerptText())) {
            add_filter("excerpt_more", array($this, "getExcerptText"));
        }

        // metabox remover
        if (KT::issetAndNotEmpty($this->getMetaboxRemover())) {
            if (KT::issetAndNotEmpty($this->getMetaboxRemover()->getMetaboxRemoverData())) {
                add_action("admin_menu", array($this, "registerMetaboxRemoverAction"));
            }
        }

        // page remover
        if (KT::issetAndNotEmpty($this->getPageRemover())) {
            add_action("admin_menu", array($this, "registerPageRemoverAction"));
            add_action("admin_init", array($this, "registerSubPageRemoverAction"));
        }

        // widget remover
        if (KT::issetAndNotEmpty($this->getWidgetRemover())) {
            add_action("widgets_init", array($this, "registerWidgetRemoverAction"));
        }

        // mazání attachmentu se smazáním postu
        if ($this->getDeleteImagesWithPost()) {
            add_action("delete_before_post", array($this, "registerDeleteAttachmentWithPostAction"));
        }

        // změna login url - loga a URL redirect
        if ($this->getDisplayLogo()) {
            add_filter('login_headerurl', array($this, "registerLoginLogoUrlFilter"), 10, 4);
            add_action('login_head', array($this, "registerLoginLogoImageAction"));
        }

        // registrace a načítání scriptů zavedené v configurátoru
        if (KT::issetAndNotEmpty($this->getAssetsConfigurator())) {
            add_action("init", array($this, "registerScriptsAction"));
            add_action("init", array($this, "registerStyleAction"));
            add_action("wp_enqueue_scripts", array($this, "enqueueScriptAction"));
            add_action("wp_enqueue_scripts", array($this, "enqueueStyleAction"));
            add_action("admin_enqueue_scripts", array($this, "enqueueScriptActionForAdmin"));
            add_action("admin_enqueue_scripts", array($this, "enqueueStyleActionForAdmin"));
        }

        // stránka nastavení šablony
        if (KT::issetAndNotEmpty($this->getThemeSettingPage())) {
            $themeSettings = new KT_Custom_Metaboxes_Subpage("themes.php", __("Nastavení šablony", KT_DOMAIN), __("Nastavení šablony", KT_DOMAIN), "update_core", self::THEME_SETTING_PAGE_SLUG);
            $themeSettings->setRenderSaveButton()->register();
        }

        $postArchiveMenu = $this->getPostArchiveMenu();
        // aplikace archivy post typů v menu
        if ($postArchiveMenu === true) {
            add_filter("wp_get_nav_menu_items", array($this, "postArchivesMenuFilter"), 10);
        } elseif ($postArchiveMenu === false) {
            add_filter("wp_get_nav_menu_items", array($this, "postArchivesMenuFilter"), 10);
        }
        if (is_admin()) {
            // archivy post typů v menu
            if ($postArchiveMenu === true) {
                add_action("admin_head-nav-menus.php", array($this, "addPostArchivesMenuMetaBox"));
            } elseif ($postArchiveMenu === false) {
                add_action("admin_head-nav-menus.php", array($this, "addPostArchivesMenuMetaBox"));
            }
        } else {
            // (images) lazy loading
            $imagesLazyLoading = $this->getImagesLazyLoading();
            if ($imagesLazyLoading === true) {
                add_filter("post_thumbnail_html", array($this, "htmlImageLazyLoadingFilter"), 11);
                add_filter("get_avatar", array($this, "htmlImageLazyLoadingFilter"), 11);
                add_filter("the_content", array($this, "htmlImageLazyLoadingFilter"), 99);
            } elseif ($imagesLazyLoading === false) {
                remove_filter("post_thumbnail_html", array($this, "htmlImageLazyLoadingFilter"), 11);
                remove_filter("get_avatar", array($this, "htmlImageLazyLoadingFilter"), 11);
                remove_filter("the_content", array($this, "htmlImageLazyLoadingFilter"), 99);
            }
        }

        // session
        if ($this->getSessionEnable() === true) {
            add_action('init', array($this, 'startSesson'), 1);
            add_action('wp_logout', array($this, 'endSession'));
            add_action('wp_login', array($this, 'endSession'));
        }
    }

    /**
     * Do kolekce registrovaných WP Menu přidá další položku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $key
     * @param string $label
     */
    public function addWpMenu($key, $label) {
        $this->wpMenuCollection[$key] = $label;

        return $this;
    }

    /**
     * Do kolekce registrovaných widgetů přidá další položku
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @return \KT_WP_Configurator
     */
    public function addWidget($name) {
        array_push($this->widgetsCollection, $name);
        return $this;
    }

    /**
     * Přidá novou velikost obrázků
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $key
     * @param int $width
     * @param int $height
     * @param boolean $crop
     */
    public function addImageSize($key, $width, $height, $crop = true) {
        add_image_size($key, $width, $height, $crop);

        return $this;
    }

    /**
     * Přidá Theme Support do Wordpressu
     * $postTypes - dle WP Codexu - v případě použití thumbnails a post-formats se definuje pole post_types
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $feature
     * @param array $postTypes
     */
    public function addThemeSupport($feature, array $postTypes) {
        add_theme_support($feature, $postTypes);
        return $this;
    }

    /**
     * Přidá Post Type Support do Wordpressu, resp. zadanou vlastnost pro zadaní post typy
     * $postTypes - pole post_types
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $feature
     * @param array $postTypes
     */
    public function addPostTypeSupport($feature, array $postTypes) {
        $this->postTypesFeatures[$feature] = $postTypes;
        return $this;
    }

    /**
     * Do kolekce registrovaných sidebarů přidá nový
     * Sidebaru se automaticky nastavi $slug jako jeho ID
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $slug
     * @return \KT_WP_Sidebar_Configurator
     */
    public function addSidebar($slug) {
        $newSidebar = new KT_WP_Sidebar_Configurator();
        $newSidebar->setId($slug);

        $this->sidebarCollection[$slug] = $newSidebar;
        return $this->sidebarCollection[$slug];
    }

    /**
     * Aktivuje metabox remover v rámci configu, který následně umožní odstranění metaboxů
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function metaboxRemover() {

        if (KT::notIssetOrEmpty($this->getMetaboxRemover())) {
            $metaboxRemover = new KT_WP_Metabox_Remover_Configurator();
            $this->setMetaboxRemover($metaboxRemover);
        }

        return $this->getMetaboxRemover();
    }

    /**
     * Aktivuje page remover v rámci configu, který následně umožní odstranění stránek z WP Adminu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function pageRemover() {

        $pageRemover = $this->getPageRemover();
        if (KT::notIssetOrEmpty($pageRemover)) {
            $pageRemover = new KT_WP_Page_Remover_Configurator();
            $this->setPageRemover($pageRemover);
        }

        return $pageRemover;
    }

    /**
     * Aktivuje widget remover v rámci configu, který následně umožní odstranění widgetu z WP Adminu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function widgetRemover() {

        $widgetRemover = $this->getWidgetRemover();
        if (KT::notIssetOrEmpty($widgetRemover)) {
            $widgetRemover = new KT_WP_Widget_Remover_Configurator();
            $this->setWidgetRemover($widgetRemover);
        }

        return $widgetRemover;
    }

    /**
     * Přidá do user_contactmethods, tzn. na uživatelském profilu, novou položku pro telefon
     * Definice pomocí, resp. na základě @see KT_User_Profile_Config::PHONE
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function addUserProfilePhone() {
        add_filter("user_contactmethods", array($this, "registerUserProfilePhone"));
    }

    /**
     * Založí configurátoru možnost přidat assety k registraci a případnému začlenění do frontendu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return type
     */
    public function assetsConfigurator() {

        if (KT::issetAndNotEmpty($this->getAssetsConfigurator())) {
            return $this->getAssetsConfigurator();
        }

        $assetsConfigurator = new KT_WP_Asset_Configurator();
        $this->setAssetsConfigurator($assetsConfigurator);

        return $this->getAssetsConfigurator();
    }

    // --- registrační funkce ---------------------------

    /**
     * Přidá do pole s položkami profilu telefonní číslo
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $profileFields
     * @return array
     */
    public function registerUserProfilePhone($profileFields) {
        $profileFields[KT_User_Profile_Config::PHONE] = __("Telefon", KT_DOMAIN);
        return $profileFields;
    }

    /**
     * Provede registrace WP Menu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerMenusAction() {
        $menus = $this->getMenusCollection();
        if (KT::issetAndNotEmpty($menus)) {
            register_nav_menus($menus);
        }
        return $this;
    }

    /**
     * Provede registraci widgetů
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerWidgetsAction() {
        $widgets = $this->getWidgetsCollection();
        if (KT::issetAndNotEmpty($widgets)) {
            foreach ($widgets as $widget) {
                register_widget($widget);
            }
        }
        return $this;
    }

    /**
     * Provede registrace Sidebar dle jejich nastavení
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registersSidebarsAction() {
        $siderbars = $this->getSidebarCollection();
        if (KT::issetAndNotEmpty($siderbars)) {
            foreach ($siderbars as $sidebar) {
                register_sidebar($sidebar->getSidebarData());
            }
        }
        return $this;
    }

    /**
     * Provede registraci post type features (support)
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerPostTypeSupportAction() {
        $features = $this->getPostTypesFeatures();
        if (KT::issetAndNotEmpty($features)) {
            foreach ($features as $feature => $postTypes) {
                foreach ($postTypes as $postType) {
                    add_post_type_support("$postType", "$feature");
                }
            }
        }
        return $this;
    }

    /**
     * Provede inicializaci smazání metaboxů dle nastavení configu - není potřeba volat veřejně
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerMetaboxRemoverAction() {
        foreach ($this->getMetaboxRemover()->getMetaboxRemoverData() as $removingMetaboxData) {
            remove_meta_box($removingMetaboxData[0], $removingMetaboxData[1], $removingMetaboxData[2]);
        }

        return $this;
    }

    /**
     * Provede inicializaci odstranění stránek z Wordpress menu dle nastavení configu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerPageRemoverAction() {
        $collection = $this->getPageRemover()->getMenuCollection();
        if (KT::issetAndNotEmpty($collection)) {
            foreach ($collection as $menuSlug) {
                remove_menu_page($menuSlug);
            }
        }

        return $this;
    }

    /**
     * Provede inicializaci odstranění podstránekstránek z Wordpress menu dle nastavení configu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerSubPageRemoverAction() {
        if (KT::issetAndNotEmpty($this->getPageRemover()->getSubMenuCollectoin())) {
            foreach ($this->getPageRemover()->getSubMenuCollectoin() as $subMenuPageDef) {
                remove_submenu_page($subMenuPageDef[KT_WP_Page_Remover_Configurator::PAGE_KEY], $subMenuPageDef[KT_WP_Page_Remover_Configurator::SUBPAGE_KEY]);
            }
        }

        return $this;
    }

    /**
     * Provede inicializaci odstranění widgetů z Wordpress menu dle nastavení configu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerWidgetRemoverAction() {
        foreach ($this->getWidgetRemover()->getWidgetRemoverData() as $removingWidgetData) {
            unregister_widget($removingWidgetData);
        }
        return $this;
    }

    /**
     * Provede registraci defaultní stránky pro nastavení šablony
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @param string $capability
     * @return \KT_WP_Configurator
     */
    public function registerThemeSettingPageAction($capability = "update_core") {
        if (KT::notIssetOrEmpty($this->getThemeSettingPage())) {
            return;
        }

        $themeSettings = new KT_Custom_Metaboxes_Subpage("themes.php", __("Nastavení šablony", KT_DOMAIN), __("Nastavení šablony", KT_DOMAIN), $capability, self::THEME_SETTING_PAGE_SLUG);
        $themeSettings->setRenderSaveButton()->register();

        return $this;
    }

    /**
     * Provede inicializace odstranění attachmentů společně se smazání postu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function registerDeleteAttachmentWithPostAction() {
        $args = array(
            "post_type" => "attachment",
            "numberposts" => -1,
            "post_status" => null,
            "post_parent" => $post_id
        );

        $attachments = get_posts($args);

        if ($attachments) {
            foreach ($attachments as $attachment) {
                wp_delete_attachment($attachment->ID, true);
            }
        }

        return $this;
    }

    /**
     * Provede inicializaci KT Logo s redirectem na stránky ktstudio.cz
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function registerLoginLogoUrlFilter() {
        return 'http://www.ktstudio.cz';
    }

    /**
     * Provede inicializaci změny loga na logovací stránce
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function registerLoginLogoImageAction() {
        wp_enqueue_style('kt-core-style');
    }

    /**
     * Provede registraci všech scriptů, které byly přidáno do assetConfigurátoru
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function registerScriptsAction() {

        if (KT::notIssetOrEmpty($this->getAssetsConfigurator()->getScriptCollection())) {
            return;
        }

        foreach ($this->getAssetsConfigurator()->getScriptCollection() as $script) {
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
        if (KT::notIssetOrEmpty($this->getAssetsConfigurator()->getStyleCollection())) {
            return null;
        }

        foreach ($this->getAssetsConfigurator()->getStyleCollection() as $style) {
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
        if (KT::notIssetOrEmpty($this->getAssetsConfigurator()->getScriptCollection())) {
            return null;
        }

        foreach ($this->getAssetsConfigurator()->getScriptCollection() as $script) {
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
        if (KT::notIssetOrEmpty($this->getAssetsConfigurator()->getStyleCollection())) {
            return null;
        }

        foreach ($this->getAssetsConfigurator()->getStyleCollection() as $style) {
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
     * Provede vložení scriptů, které mají nastaveno načtení, do admin sekce
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function enqueueScriptActionForAdmin() {
        if (KT::notIssetOrEmpty($this->getAssetsConfigurator()->getScriptCollection())) {
            return null;
        }

        foreach ($this->getAssetsConfigurator()->getScriptCollection() as $script) {
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

    /**
     * Provede registraci všechy stylů, které byly přidáno do assetConfigurátoru v rámci admin sekce
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function enqueueStyleActionForAdmin() {
        if (KT::notIssetOrEmpty($this->getAssetsConfigurator()->getStyleCollection())) {
            return null;
        }

        foreach ($this->getAssetsConfigurator()->getStyleCollection() as $style) {
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
     * Zpracování filtru za účelem aplikace lazy loadingu pro obrázky, resp. post thumbnaily (kromě administrace)
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $html
     */
    public function htmlImageLazyLoadingFilter($html) {
        return KT::imageReplaceLazySrc($html);
    }

    /**
     * Přidání metaboxu pro archivy post typů v menu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $html
     */
    public function addPostArchivesMenuMetaBox() {
        add_meta_box("kt-post-archive-nav-menu", __("Archivy", KT_DOMAIN), array($this, "postArchivesMenuMetaBoxCallBack"), "nav-menus", "side", "default");
    }

    /**
     * Zpracování metaboxu pro archivy post typů v menu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $html
     */
    public function postArchivesMenuMetaBoxCallBack() {
        $postTypes = get_post_types(array("show_in_nav_menus" => true, "has_archive" => true), "object");
        if (KT::arrayIssetAndNotEmpty($postTypes)) {
            foreach ($postTypes as $postType) {
                $postType->classes = array();
                $postType->type = $postType->name;
                $postType->object_id = $postType->name;
                $postType->title = $postType->labels->name . " " . __("Archiv", KT_DOMAIN);
                $postType->object = self::POST_TYPE_ARCHIVE_OBJECT_KEY;
            }

            $walker = new Walker_Nav_Menu_Checklist(array());

            KT::theTabsIndent(0, "<div id=\"kt-archive\" class=\"posttypediv\">", true);
            KT::theTabsIndent(1, "<div id=\"tabs-panel-kt-archive\" class=\"tabs-panel tabs-panel-active\">", true);
            KT::theTabsIndent(2, "<ul id=\"kt-archive-checklist\" class=\"categorychecklist form-no-clear\">", true);
            KT::theTabsIndent(3, walk_nav_menu_tree(array_map("wp_setup_nav_menu_item", $postTypes), 0, (object) array("walker" => $walker)), true);
            KT::theTabsIndent(2, "</ul>", true);
            KT::theTabsIndent(1, "</div>", true);
            KT::theTabsIndent(0, "</div>", true, true);

            $addMenuTitle = htmlspecialchars(__("Add to Menu"));

            KT::theTabsIndent(0, "<p class=\"button-controls\">", true);
            KT::theTabsIndent(1, "<span class=\"add-to-menu\">", true);
            KT::theTabsIndent(2, "<input type=\"submit\" id=\"submit-kt-archive\" name=\"kt-add-archive-menu-item\" class=\"button-secondary submit-add-to-menu\" value=\"$addMenuTitle\" />", true);
            KT::theTabsIndent(1, "</span>", true);
            KT::theTabsIndent(0, "</p>", true, true);
        } else {
            KT::theTabsIndent(0, KT_EMPTY_SYMBOL, true, true);
        }
    }

    /**
     * Filter metaboxu pro archivy post typů v menu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $items
     */
    public function postArchivesMenuFilter($items) {
        if (KT::arrayIssetAndNotEmpty($items)) {
            foreach ($items as $item) {
                if ($item->object === self::POST_TYPE_ARCHIVE_OBJECT_KEY) {
                    $item->url = get_post_type_archive_link($item->type);
                    if (get_query_var("post_type") == $item->type) {
                        $item->classes [] = "current-menu-item";
                        $item->current = true;
                    }
                }
            }
        }
        return $items;
    }

    /**
     * Povolení a zahájení SESSION v rámci WP
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function startSesson() {
        if (!session_id()) {
            session_start();
        }
    }

    /**
     * Obsluha ukončení SESSION v rámci WP
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function endSession() {
        session_destroy();
    }

    // --- statické funkce --------------

    /**
     * Vrátí název WP_Screen base pro založenou stránku theme setting.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public static function getThemeSettingSlug() {
        return $baseName = self::THEME_SUBPAGE_PREFIX . self::THEME_SETTING_PAGE_SLUG;
    }

}
