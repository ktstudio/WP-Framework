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
    const TOOLS_SUBPAGE_PREFIX = "tools_page_";
    const WP_CRON_PAGE_SLUG = "kt-wp-cron";
    const POST_TYPE_ARCHIVE_OBJECT_KEY = "kt-post-type-archive";
    const COOKIE_STATEMENT_KEY = "kt-cookie-statement-key";

    private $wpMenuCollection = array();
    private $widgetsCollection = array();
    private $sidebarCollection = array();
    private $postTypesFeaturesToAdd = array();
    private $postTypesFeaturesToRemove = array();
    private $excerptLength = null;
    private $excerptText = null;
    private $metaboxRemover = null;
    private $pageRemover = null;
    private $widgetRemover = null;
    private $headRemover = null;
    private $themeSettingsPage = false;
    private $themeSettingsCapability = "update_core";
    private $deleteImagesWithPost = false;
    private $displayLogo = true;
    private $assetsConfigurator = null;
    private $imagesLazyLoading = null;
    private $imagesLinkClasses = null;
    private $postArchiveMenu = null;
    private $postsArchiveSlug = null;
    private $allowSession = false;
    private $allowCookieStatement = false;
    private $allowSanitizeFileNames = false;
    private $facebookManager = null;
    private $emojiSwitch = false;
    private $autoRemoveShortcodesParagraphs = false;
    private $enableDynamicFieldsets = false;
    private $disableOembed = false;
    private $disableJson = false;
    private $disableRelNext = false;
    private $disableDefaultGalleryInlineStyle = false;

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
    private function getPostTypesFeaturesToAdd() {
        return $this->postTypesFeaturesToAdd;
    }

    /**
     * @return array
     */
    private function getPostTypesFeaturesToRemove() {
        return $this->postTypesFeaturesToRemove;
    }

    /**
     * @return int
     */
    public function getExcerptLength() {
        return $this->excerptLength;
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
     * @return \KT_WP_Head_Remover_Configurator
     */
    private function getHeadRemover() {
        return $this->headRemover;
    }

    /**
     * @return boolean
     */
    private function getThemeSettingsPage() {
        return $this->themeSettingsPage;
    }

    /**
     * @return boolean
     */
    private function getThemeSettingsCapability() {
        return $this->themeSettingsCapability;
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
    private function getImagesLinkClasses() {
        return $this->imagesLinkClasses;
    }

    /**
     * @return boolean
     */
    private function getPostArchiveMenu() {
        return $this->postArchiveMenu;
    }

    /**
     * @return string
     */
    private function getPostsArchiveSlug() {
        return $this->postsArchiveSlug;
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
    private function getAllowSession() {
        return $this->allowSession;
    }

    /**
     * @return boolean
     */
    private function getAllowCookieStatement() {
        return $this->allowCookieStatement;
    }

    /**
     * @return boolean
     */
    private function getAllowSanitizeFileNames() {
        return $this->allowSanitizeFileNames;
    }

    /**
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function getFacebookManager() {
        if (KT::notIssetOrEmpty($this->facebookManager)) {
            $this->setFacebookManager(new KT_WP_Facebook_Data_Configurator());
        }

        return $this->facebookManager;
    }

    /** @return boolean */
    public function getEmojiSwitch() {
        return $this->emojiSwitch;
    }

    /** @return boolean */
    public function getAutoRemoveShortcodesParagraphs() {
        return $this->autoRemoveShortcodesParagraphs;
    }

    /** @return boolean */
    public function getEnableDynamicFieldsets() {
        return $this->enableDynamicFieldsets;
    }

    /** @return boolean */
    public function getDisableJsonOembed() {
        return $this->disableOembed;
    }

    /** @return boolean */
    public function getDisableJson() {
        return $this->disableJson;
    }

    /** @return boolean */
    public function getDisableRelNext() {
        return $this->disableRelNext;
    }

    /** @return boolean */
    public function getDisableDefaultGalleryInlineStyle() {
        return $this->disableDefaultGalleryInlineStyle;
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
     * Nastaví KT_WP_Head_Remover_Configurator do objektu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param KT_WP_Head_Remover_Configurator $headRemover
     * @return \KT_WP_Configurator
     */
    private function setHeadRemover(KT_WP_Head_Remover_Configurator $headRemover) {
        $this->headRemover = $headRemover;
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
            $this->excerptLength = $excerptLenght;
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
     * @param boolean $themeSettingsPage
     * @return \KT_WP_Configurator
     */
    public function setThemeSettingsPage($themeSettingsPage = true) {
        $this->themeSettingsPage = $themeSettingsPage;
        return $this;
    }

    /**
     * @deprecated since version 1.10
     * @param boolean $themeSettingsPage
     * @return \KT_WP_Configurator
     */
    public function setThemeSettingPage($themeSettingsPage = true) {
        return $this->setThemeSettingsPage($themeSettingsPage);
    }

    /**
     * Nastaví (vlastní) oprávnění pro stránku s nastavením šablony s metaboxy
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $themeSettingsCapability
     * @return \KT_WP_Configurator
     */
    public function setThemeSettingsCapability($themeSettingsCapability = "update_core") {
        $this->themeSettingsCapability = $themeSettingsCapability;
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
    public function setDisplayLogo($displayLogo = true) {
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
     * @param boolean $allowSession
     * @return \KT_WP_Configurator
     */
    public function setAllowSession($allowSession = true) {
        $this->allowSession = $allowSession;
        return $this;
    }

    /**
     * @deprecated since version 1.4
     * @see setAllowSession
     * @param boolean $sessionEnable
     * @return \KT_WP_Configurator
     */
    public function setSessionEnable($sessionEnable = true) {
        $this->allowSession = $sessionEnable;
        return $this;
    }

    /**
     * Nastaví, zda se má v rámci šablony zapnout odsouhlasení cookie
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $allowCookieStatement
     * @return \KT_WP_Configurator
     */
    public function setAllowCookieStatement($allowCookieStatement = true) {
        $this->allowCookieStatement = $allowCookieStatement;
        return $this;
    }

    /**
     * Nastaví, zda se má v rámci šablony zapnout sanitizace názvů nahrávaných souborů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $allowSanitizeFileNames
     * @return \KT_WP_Configurator
     */
    public function setAllowSanitizeFileNames($allowSanitizeFileNames = true) {
        $this->allowSanitizeFileNames = $allowSanitizeFileNames;
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
     * Aktivace archivu pro příspěvky na základě vlastního slugu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $postsArchiveSlug
     * @return \KT_WP_Configurator
     */
    public function setPostsArchiveSlug($postsArchiveSlug = "blog") {
        $this->postsArchiveSlug = $postsArchiveSlug;
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
        return $this;
    }

    /**
     * Aktivace aplikace (css) class na odkazy obrázků při editaci v administraci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function setImagesLinkClasses($imagesLinkClasses) {
        $this->imagesLinkClasses = $imagesLinkClasses;
        return $this;
    }

    /**
     * Nastaví facebook data manager do configurátoru
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_WP_Facebook_Data_Configurator $facebookManager
     * @return \KT_WP_Configurator
     */
    private function setFacebookManager(KT_WP_Facebook_Data_Configurator $facebookManager) {
        $this->facebookManager = $facebookManager;
        return $this;
    }

    /**
     * Zapne / vypne emoji smajlíky a vše s nimi spojené
     *
     * @author Jan Pokorný
     * @param boolean $switch
     * @return \KT_WP_Configurator
     */
    public function setEmojiSwitch($switch = true) {
        $this->emojiSwitch = KT::tryGetBool($switch);
        return $this;
    }

    /**
     * Zapne / vypne elimininaci nechtěného odřádkování v contentu se shortcody
     *
     * @author Martin Hlaváč
     * @param boolean $enabled
     * @return \KT_WP_Configurator
     */
    public function setAutoRemoveShortcodesParagraphs($enabled = true) {
        $this->autoRemoveShortcodesParagraphs = KT::tryGetBool($enabled);
        return $this;
    }

    /**
     *
     * @param boolean $enableAdmin
     * @return \KT_WP_Configurator
     */
    public function setEnableDynamicFieldsets($enableAdmin = true) {
        $this->enableDynamicFieldsets = $enableAdmin;
        return $this;
    }

    /**
     * Vypne / ponechá funkce WP JSON Oembed
     *
     * @author Martin Hlaváč
     * @param boolean $disable
     * @return \KT_WP_Configurator
     */
    public function setDisableJsonOembed($disable = true) {
        $this->disableOembed = KT::tryGetBool($disable);
        return $this;
    }

    /**
     * Vypne / ponechá funkce WP JSON (API)
     *
     * @author Martin Hlaváč
     * @param boolean $disable
     * @return \KT_WP_Configurator
     */
    public function setDisableJson($disable = true) {
        $this->disableJson = KT::tryGetBool($disable);
        return $this;
    }

    /**
     * Vypne / ponechá funkce rel="next" atribut v hlavičce
     *
     * @author Martin Hlaváč
     * @param boolean $disable
     * @return \KT_WP_Configurator
     */
    public function setDisableRelNext($disable = true) {
        $this->disableRelNext = KT::tryGetBool($disable);
        return $this;
    }

    /**
     * Zruší výpis a tím pádem i aplikaci výchozího inline stylu u WP gallerií na FE
     *
     * @author Martin Hlaváč
     * @param boolean $disable
     * @return \KT_WP_Configurator
     */
    public function setDisableDefaultGalleryInlineStyle($disable = true) {
        $this->disableDefaultGalleryInlineStyle = KT::tryGetBool($disable);
        return $this;
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

        if (KT::arrayIssetAndNotEmpty($this->getPostTypesFeaturesToAdd())) {
            // přidání post type supports (features)
            add_action("init", array($this, "addPostTypeSupportAction"));
        }

        if (KT::arrayIssetAndNotEmpty($this->getPostTypesFeaturesToRemove())) {
            // odebrání post type supports (features)
            add_action("init", array($this, "removePostTypeSupportAction"));
        }

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

        // head remover
        if (KT::issetAndNotEmpty($this->getHeadRemover())) {
            $this->getHeadRemover()->doRemoveHeads();
        }

        // mazání attachmentu se smazáním postu
        if ($this->getDeleteImagesWithPost()) {
            add_action("delete_before_post", array($this, "registerDeleteAttachmentWithPostAction"));
        }

        // změna login url - loga a URL redirect
        if ($this->getDisplayLogo()) {
            add_filter("login_headerurl", array($this, "registerLoginLogoUrlFilter"), 10, 4);
            add_action("login_head", array($this, "registerLoginLogoImageAction"));
        }

        if ($this->getEnableDynamicFieldsets()) {
            add_action("admin_enqueue_scripts", array($this, "registerDynamicFieldsetScript"));
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
        if (KT::issetAndNotEmpty($this->getThemeSettingsPage())) {
            $themeSettings = new KT_Custom_Metaboxes_Subpage("themes.php", __("Theme Settings", "KT_CORE_DOMAIN"), __("Theme Settings", "KT_CORE_DOMAIN"), $this->getThemeSettingsCapability(), self::THEME_SETTING_PAGE_SLUG);
            $themeSettings->setRenderSaveButton()->register();
        }

        $postArchiveMenu = $this->getPostArchiveMenu();
        // aplikace archivy post typů v menu
        if ($postArchiveMenu === true) {
            add_filter("wp_get_nav_menu_items", array($this, "postArchivesMenuFilter"), 10);
        } elseif ($postArchiveMenu === false) {
            add_filter("wp_get_nav_menu_items", array($this, "postArchivesMenuFilter"), 10);
        }
        if (KT::issetAndNotEmpty($this->getPostsArchiveSlug())) {
            add_action("init", array($this, "addPostsArchiveDefinitionRewrite"));
        }
        if (is_admin()) {
            // archivy post typů v menu
            if ($postArchiveMenu === true) {
                add_action("admin_head-nav-menus.php", array($this, "addPostArchivesMenuMetaBox"));
            } elseif ($postArchiveMenu === false) {
                add_action("admin_head-nav-menus.php", array($this, "addPostArchivesMenuMetaBox"));
            }
            // (iamges) link classes
            $imageLinkClass = $this->getImagesLinkClasses();
            if ($imageLinkClass === true) {
                add_filter("image_send_to_editor", array($this, "htmlImageLinkClassFilter"), 10, 8);
            } elseif ($imageLinkClass === false) {
                remove_filter("image_send_to_editor", array($this, "htmlImageLinkClassFilter"), 10, 8);
            }
        } else {
            // (images) lazy loading
            $imagesLazyLoading = $this->getImagesLazyLoading();
            if ($imagesLazyLoading === true) {
                add_filter("post_thumbnail_html", array($this, "htmlImageLazyLoadingFilter"), 11);
                add_filter("get_avatar", array($this, "htmlImageLazyLoadingFilter"), 11);
                add_filter("the_content", array($this, "htmlImageLazyLoadingFilter"), 99);
                add_filter("kt_image_prepare_lazyload", array($this, "htmlImageLazyLoadingFilter"), 42);
            } elseif ($imagesLazyLoading === false) {
                remove_filter("post_thumbnail_html", array($this, "htmlImageLazyLoadingFilter"), 11);
                remove_filter("get_avatar", array($this, "htmlImageLazyLoadingFilter"), 11);
                remove_filter("the_content", array($this, "htmlImageLazyLoadingFilter"), 99);
                remove_filter("kt_image_prepare_lazyload", array($this, "htmlImageLazyLoadingFilter"), 42);
            }
        }

        // session
        if ($this->getAllowSession() === true) {
            add_action("init", array($this, "startSesson"), 1);
            add_action("wp_logout", array($this, "endSession"));
            add_action("wp_login", array($this, "endSession"));
        }

        // cookie statement
        if ($this->getAllowCookieStatement() === true) {
            add_action("wp_footer", array($this, "renderCookieStatement"), 99);
        }

        // sanitize file names
        if ($this->getAllowSanitizeFileNames() === true) {
            add_action("sanitize_file_name", array($this, "sanitizeFileName"), 99);
        }

        // facebookManager
        if ($this->getFacebookManager()->getModuleEnabled()) {
            add_action("wp_head", array($this, "facebookTagsInit"), 99);
        }

        // emoji
        if ($this->getEmojiSwitch() === false) {
            add_action("init", array($this, "removeEmoji"), 99);
        }

        // Auto Remove Shortcodes Paragraphs
        if ($this->getAutoRemoveShortcodesParagraphs() === true) {
            remove_filter("the_content", "wpautop");
            add_filter("the_content", "wpautop", 99);
            add_filter("the_content", array($this, "autoRemoveShortcodesParagraphs"), 100);
        }

        // JSON Oembed
        if ($this->getDisableJsonOembed() === true) {
            add_action("init", array($this, "disableJsonOembed"), 99);
        }

        // JSON (API)
        if ($this->getDisableJson() === true) {
            add_action("init", array($this, "disableJson"), 99);
        }

        // rel next
        if ($this->getDisableRelNext() === true) {
            add_action("init", array($this, "disableRelNext"), 99);
        }

        // default gallery inline style
        if ($this->getDisableDefaultGalleryInlineStyle() === true) {
            add_action("init", array($this, "disableDefaultGalleryInlineStyle"), 99);
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
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $feature
     * @param array $args
     * @return \KT_WP_Configurator
     */
    public function addThemeSupport($feature, array $args = null) {
        if (KT::arrayIssetAndNotEmpty($args)) {
            add_theme_support($feature, $args);
        } else {
            add_theme_support($feature);
        }
        return $this;
    }

    /**
     * Přidá Post Type Support do Wordpressu, resp. zadanou vlastnost pro zadaní post typy
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $feature
     * @param array $postTypes
     */
    public function addPostTypeSupport($feature, array $postTypes) {
        $this->postTypesFeaturesToAdd[$feature] = $postTypes;
        return $this;
    }

    /**
     * Odebere Post Type Support z Wordpressu, resp. zadanou vlastnost pro zadaní post typy
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $feature
     * @param array $postTypes
     */
    public function removePostTypeSupport($feature, array $postTypes) {
        $this->postTypesFeaturesToRemove[$feature] = $postTypes;
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
     * Aktivuje head remover v rámci configu, který následně umožní odstranění headu z WP Adminu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function headRemover() {
        $headRemover = $this->getHeadRemover();
        if (KT::notIssetOrEmpty($headRemover)) {
            $headRemover = new KT_WP_Head_Remover_Configurator();
            $this->setHeadRemover($headRemover);
        }
        return $headRemover;
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
        $profileFields[KT_User_Profile_Config::PHONE] = __("Phone", "KT_CORE_DOMAIN");
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
     * Provede přidání post type features (support)
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function addPostTypeSupportAction() {
        $features = $this->getPostTypesFeaturesToAdd();
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
     * Provede odebrání post type features (support)
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Configurator
     */
    public function removePostTypeSupportAction() {
        $features = $this->getPostTypesFeaturesToRemove();
        if (KT::issetAndNotEmpty($features)) {
            foreach ($features as $feature => $postTypes) {
                foreach ($postTypes as $postType) {
                    remove_post_type_support("$postType", "$feature");
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
        if (KT::notIssetOrEmpty($this->getThemeSettingsPage())) {
            return;
        }

        $themeSettings = new KT_Custom_Metaboxes_Subpage("themes.php", __("Theme Settings", "KT_CORE_DOMAIN"), __("Theme Settings", "KT_CORE_DOMAIN"), $capability, self::THEME_SETTING_PAGE_SLUG);
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
    public function registerDeleteAttachmentWithPostAction($postId) {
        $args = array(
            "post_type" => "attachment",
            "numberposts" => -1,
            "post_status" => null,
            "post_parent" => $postId
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
        return "http://www.wpframework.cz";
    }

    /**
     * Provede inicializaci změny loga na logovací stránce
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function registerLoginLogoImageAction() {
        wp_enqueue_style(KT_WPFW_LOGIN_STYLE);
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
     * Zpracování filtru za účelem aplikace css classy na linky pro obrázky
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function htmlImageLinkClassFilter($html, $id, $caption, $title, $align, $url, $size, $alt = "") {
        $class = "kt-img-link";
        if (preg_match('/<a.*? class=".*?">/', $html)) {
            $html = preg_replace('/(<a.*? class=".*?)(".*?>)/', "$1 {$class}$2", $html);
        } else {
            $html = preg_replace('/(<a.*?)>/', "$1 class=\"$class\" >", $html);
        }
        return $html;
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
        add_meta_box("kt-post-archive-nav-menu", __("Archives", "KT_CORE_DOMAIN"), array($this, "postArchivesMenuMetaBoxCallBack"), "nav-menus", "side", "default");
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
                $postType->type = "custom"; //$postType->name;
                $postType->object_id = $postType->name;
                $postType->title = $postType->labels->name;
                $postType->description = $postType->labels->name;
                $postType->object = self::POST_TYPE_ARCHIVE_OBJECT_KEY;
                $postType->menu_item_parent = null;
                $postType->parent = null;
                $postType->db_id = null;
                $postType->url = get_post_type_archive_link($postType->name);
                $postType->target = null;
                $postType->attr_title = $postType->labels->name;
                $postType->xfn = null;
            }

            $walker = new Walker_Nav_Menu_Checklist(array());

            KT::theTabsIndent(0, "<div id=\"kt-archive\" class=\"posttypediv\">", true);
            KT::theTabsIndent(1, "<div id=\"tabs-panel-kt-archive\" class=\"tabs-panel tabs-panel-active\">", true);
            KT::theTabsIndent(2, "<ul id=\"kt-archive-checklist\" class=\"categorychecklist form-no-clear\">", true);
            KT::theTabsIndent(3, walk_nav_menu_tree(array_map("wp_setup_nav_menu_item", $postTypes), 0, (object) array("walker" => $walker)), true);
            KT::theTabsIndent(2, "</ul>", true);
            KT::theTabsIndent(1, "</div>", true);
            KT::theTabsIndent(0, "</div>", true, true);

            $addMenuTitle = htmlspecialchars(__("Add to menu", "KT_CORE_DOMAIN"));

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
     * Provede inicializaci definice post typu a rewritu pro archiv příspěvků
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function addPostsArchiveDefinitionRewrite() {
        global $wp_post_types;

        $wp_post_types["post"]->has_archive = $this->getPostsArchiveSlug();
        $wp_post_types["post"]->rewrite = array("with_front" => true, "feeds" => false);

        add_rewrite_rule("{$this->getPostsArchiveSlug()}/?$", sprintf("index.php?post_type=%s", KT_WP_POST_KEY), "top");
    }

    /**
     * Provede inicializaci facebook modulu a výpíše OG tagy do hlavičky webu
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function facebookTagsInit() {
        $this->getFacebookManager()->renderHeaderTags();
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

    /**
     * Provede povolení, resp. inicializaci proužku s potvrzením cookie (v patičce)
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function renderCookieStatement() {
        echo "<div id=\"ktCookieStatementContainer\"></div>";
    }

    /**
     * Provede sanitizaci názvu souboru, resp. odstraní accent
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function sanitizeFileName($fileName) {
        return remove_accents($fileName);
    }

    /**
     * Vrátí obsah proužku s potvrzením cookie (v patičce)
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public static function getCookieStatementHtml() {
        $cookueStatementKey = KT::arrayTryGetValue($_COOKIE, self::COOKIE_STATEMENT_KEY);
        if (KT::notIssetOrEmpty($cookueStatementKey)) {
            $moreInfoUrl = "https://policies.google.com/technologies/cookies";
            $privacyPolicyPageId = get_option(KT_WP_OPTION_KEY_PRIVACY_POLICY_PAGE);
            if (KT::isIdFormat($privacyPolicyPageId)) {
                $privacyPolicyPermalink = get_permalink($privacyPolicyPageId);
                if (KT::issetAndNotEmpty($privacyPolicyPermalink)) {
                    $moreInfoUrl = $privacyPolicyPermalink;
                }
            }

            $text = __("This site uses cookies. By using this site you consent to the use of Cookies.", "KT_CORE_DOMAIN");
            $moreInfoTitle = __("Find out more", "KT_CORE_DOMAIN");
            $moreInfoUrl = apply_filters("kt_cookie_statement_more_info_url_filter", $moreInfoUrl);
            $confirmTitle = __("OK, I understand", "KT_CORE_DOMAIN");

            $html = "<span id=\"ktCookieStatementText\">$text</span>";
            $html .= "<span id=\"ktCookieStatementMoreInfo\"><a href=\"$moreInfoUrl\" title=\"$moreInfoTitle\" target=\"_blank\">$moreInfoTitle</a></span>";
            $html .= "<span id=\"ktCookieStatementConfirm\">$confirmTitle</span>";

            $content = apply_filters("kt_cookie_statement_content_filter", $html);

            $output = "<div id=\"ktCookieStatement\">$content</div>";
            $output .= "<noscript><style scoped>#ktCookieStatement { display:none; }</style></noscript>";
            return $output;
        }
        return null;
    }

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

    /**
     * Vrátí název WP_Screen base pro (založenou) stránku (KT) WP Cron
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public static function getWpCronSlug() {
        return $baseName = self::TOOLS_SUBPAGE_PREFIX . self::WP_CRON_PAGE_SLUG;
    }

    /**
     * Smaže akce spojené s emoji
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Jan Pokorný
     */
    public function removeEmoji() {
        remove_action("admin_print_styles", "print_emoji_styles");
        remove_action("wp_head", "print_emoji_detection_script", 7);
        remove_action("admin_print_scripts", "print_emoji_detection_script");
        remove_action("wp_print_styles", "print_emoji_styles");
        remove_filter("wp_mail", "wp_staticize_emoji_for_email");
        remove_filter("the_content_feed", "wp_staticize_emoji");
        remove_filter("comment_text_rss", "wp_staticize_emoji");

        // filter to remove TinyMCE emojis
        add_filter("tiny_mce_plugins", array($this, "disableEmojiInTinymc"));
        add_filter("emoji_svg_url", array($this, "disableEmojiSvgUrl"));
    }

    /**
     * Filtr pro odstranění emoji z Tinymc
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Jan Pokorný
     * @param array $plugins
     * @return array
     */
    public function disableEmojiInTinymc($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        } else {
            return array();
        }
    }

    /**
     * Filtr pro odstranění emoji SVG URL
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     * @param string $url
     * @return array
     */
    public function disableEmojiSvgUrl($url) {
        return null;
    }

    /**
     * Don't auto-p wrap shortcodes that stand alone
     * Ensures that shortcodes are not wrapped in <<p>>...<</p>>.
     *
     * @author Paulund
     * @link https://paulund.co.uk/remove-line-breaks-in-shortcodes
     *
     * @param string $content The content.
     * @return string The filtered content.
     */
    public function autoRemoveShortcodesParagraphs($content) {
        global $shortcode_tags;
        if (empty($shortcode_tags) || !is_array($shortcode_tags)) {
            return $content;
        }
        $tagregexp = join("|", array_map("preg_quote", array_keys($shortcode_tags)));
        $pattern = '/'
                . '<p>'                              // Opening paragraph
                . '\\s*+'                            // Optional leading whitespace
                . '('                                // 1: The shortcode
                . '\\['                          // Opening bracket
                . "($tagregexp)"                 // 2: Shortcode name
                . '(?![\\w-])'                   // Not followed by word character or hyphen
                // Unroll the loop: Inside the opening shortcode tag
                . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
                . '(?:'
                . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
                . '[^\\]\\/]*'               // Not a closing bracket or forward slash
                . ')*?'
                . '(?:'
                . '\\/\\]'                   // Self closing tag and closing bracket
                . '|'
                . '\\]'                      // Closing bracket
                . '(?:'                      // Unroll the loop: Optionally, anything between the opening and closing shortcode tags
                . '[^\\[]*+'             // Not an opening bracket
                . '(?:'
                . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
                . '[^\\[]*+'         // Not an opening bracket
                . ')*+'
                . '\\[\\/\\2\\]'         // Closing shortcode tag
                . ')?'
                . ')'
                . ')'
                . '\\s*+'                            // optional trailing whitespace
                . '<\\/p>'                           // closing paragraph
                . '/s';
        return preg_replace($pattern, '$1', $content);
    }

    /**
     * Registrace scriptu pro dynamické fieldsety
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     * @author Jan Pokorný
     */
    public function registerDynamicFieldsetScript() {
        wp_enqueue_script(KT_DYNAMIC_FIELDSET_SCRIPT);
    }

    /**
     * Zruší WP JSON Oembed
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Jentan Bernardus <http://wordpress.stackexchange.com/a/212472>
     *
     * @author Martin Hlaváč
     */
    public function disableJsonOembed() {
        if (!is_admin()) {
            remove_action("wp_head", "rest_output_link_wp_head");
            remove_action("wp_head", "wp_oembed_add_discovery_links");
            remove_action("rest_api_init", "wp_oembed_register_route");
            add_filter("embed_oembed_discover", "__return_false");
            remove_filter("oembed_dataparse", "wp_filter_oembed_result");
            remove_action("wp_head", "wp_oembed_add_discovery_links");
            remove_action("wp_head", "wp_oembed_add_host_js");
        }
    }

    /**
     * Zruší WP JSON Oembed
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     */
    public function disableJson() {
        if (!is_admin()) {
            add_filter("json_enabled", "__return_false");
            add_filter("json_jsonp_enabled", "__return_false");
        }
    }

    /**
     * Zruší rel next v head
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     */
    public function disableRelNext() {
        if (!is_admin()) {
            remove_action("wp_head", "wp_shortlink_wp_head", 10);
            remove_action("wp_head", "adjacent_posts_rel_link_wp_head", 10);
            add_filter("wpseo_next_rel_link", "__return_false");
        }
    }

    /**
     * Zruší výchozí inline style WP galerií
     * NENÍ POTŘEBA VOLAT VEŘEJNĚ
     *
     * @author Martin Hlaváč
     */
    public function disableDefaultGalleryInlineStyle() {
        if (!is_admin()) {
            add_filter("use_default_gallery_style", "__return_false");
        }
    }
}
