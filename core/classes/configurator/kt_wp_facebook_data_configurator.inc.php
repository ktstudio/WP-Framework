<?php

/**
 * Třída pro nastavení Facebook dat v rámci KT WP konfigurátoru
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_WP_Facebook_Data_Configurator {

    const OG_LOCALE = "og:locale";
    const OG_SITE_NAME = "og:site_name";
    const OG_TITLE = "og:title";
    const OG_IMAGE = "og:image";
    const OG_URL = "og:url";
    const OG_DESCRIPTION = "og:description";

    private $local = "cs_CZ";
    private $siteName = null;
    private $title = null;
    private $imageUrl = null;
    private $url = null;
    private $description = null;
    private $moduleEnabled = false;

    // --- gettery a settery ------------------

    /**
     * @return string - url
     */
    private function getImageUrl() {
        return $this->imageUrl;
    }

    /**
     * Nastaví URL cestu defaultního obrázku pro sdílení na facebooku.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $defaultImage
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function setImageUrl($defaultImage) {
        $this->imageUrl = $defaultImage;
        return $this;
    }

    /**
     * @return string
     */
    private function getLocal() {
        return $this->local;
    }

    /**
     * Nastaví zkratku jazyka pro danou stránku
     * defaultně - "cs_CZ"
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $local
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function setLocal($local) {
        $this->local = $local;
        return $this;
    }

    /**
     * @return string
     */
    private function getSiteName() {
        return $this->siteName;
    }

    /**
     * Nastaví pevný titulek stránky - v případě null se použije ten v nastaví WP
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $siteName
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function setSiteName($siteName) {
        $this->siteName = $siteName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Nastaví titulek pro stránku
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $title
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Nastaví url stránky
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $url
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Nastaví popisek pro danou stránku
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $description
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getModuleEnabled() {
        return $this->moduleEnabled;
    }

    /**
     * Provede vypnutí celého modulu s výpisem facebook og tagů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $modulEnable
     * @return \KT_WP_Facebook_Data_Configurator
     */
    public function setModuleEnabled($modulEnable = false) {
        $this->moduleEnabled = $modulEnable;
        return $this;
    }

    // --- veřejné funkce ---------------

    /**
     * Vyrendruje všechny potřebné og tagy pro facebook v rámci daného obsahu
     * seznam tagů: title, site_name, url, description, local, image
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function renderHeaderTags() {
        $this->initialize();

        $this->renderMetaTag(self::OG_SITE_NAME, $this->getSiteName());
        $this->renderMetaTag(self::OG_TITLE, $this->getTitle());
        $this->renderMetaTag(self::OG_IMAGE, $this->getImageUrl());
        $this->renderMetaTag(self::OG_DESCRIPTION, $this->getDescription());
        $this->renderMetaTag(self::OG_LOCALE, $this->getLocal());
        $this->renderMetaTag(self::OG_URL, $this->getUrl());
    }

    // --- privátní funkce ---------------

    /**
     * Inicializace celého objktu. Rozdělení na jednotlivé typy obsahu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function initialize() {
        $this->defaultValuesInit();

        if (is_single() || is_page()) {
            $this->singleDataInit();
            return $this;
        }

        if (is_search()) {
            $this->setTitle(sprintf(__("Search for: %s", "KT_CORE_DOMAIN"), trim(esc_attr(get_search_query()))));
            return $this;
        }

        if (is_category() || is_tax() || is_tag()) {
            $this->termDataInit();
            return $this;
        }

        if (is_archive()) {
            $this->archiveDataInit();
            return $this;
        }

        if (is_author()) {
            $this->authorDataInit();
            return $this;
        }

        if (is_404()) {
            $this->setTitle(sprintf(__("Error 404 - %s", "KT_CORE_DOMAIN"), $this->getTitle()));
            return $this;
        }

        return $this;
    }

    /**
     * Provede základní načtení údajů pro facebook
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function defaultValuesInit() {
        $wpModel = new KT_WP_Info();

        if (KT::notIssetOrEmpty($this->getDescription())) {
            $this->setDescription($wpModel->getDescription());
        }

        if (KT::notIssetOrEmpty($this->getSiteName())) {
            $this->setSiteName($wpModel->getName());
        }

        if (KT::notIssetOrEmpty($this->getUrl())) {
            $this->setUrl($wpModel->getUrl());
        }

        return $this;
    }

    /**
     * Provede načtení dat pro typ obsahu - single / page
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @global type $post
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function singleDataInit() {
        global $post;
        $model = new KT_WP_Post_Base_Model($post);



        $this->setTitle($model->getTitle())
                ->setUrl($model->getPermalink())
                ->setDescription($model->getExcerpt(false));

        if ($model->hasThumbnail()) {
            $imageUrlData = wp_get_attachment_image_src($model->getThumbnailId(), KT_WP_IMAGE_SIZE_MEDIUM);
            $this->setImageUrl($imageUrlData[0]);
        }

        return $this;
    }

    /**
     * Provede načtení dat pro cat / term / tag
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function termDataInit() {
        $model = new KT_WP_Term_Base_Model(get_queried_object());
        $this->setTitle($model->getName())
                ->setUrl($model->getPermalink());

        if (KT::issetAndNotEmpty($model->getDescription())) {
            $this->setDescription($model->getDescription());
        }

        return $this;
    }

    /**
     * Provede načtení dat pro uživatele - author
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function authorDataInit() {
        $model = new KT_WP_User_Base_Model(get_queried_object());
        $this->setTitle($model->getDisplayName())
                ->setDescription($model->getDescription())
                ->setUrl($model->getPermalink());

        return $this;
    }

    /**
     * Provede načtení dat pro archiv - post_type
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Facebook_Data_Configurator
     */
    private function archiveDataInit() {
        $postType = get_queried_object();
        $this->setTitle($postType->labels->name);
        return $this;
    }

    /**
     * Provede vykreslení OG tagu na základě parametru a jeho obsahu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $tagType
     * @param string $content
     */
    private function renderMetaTag($tagType, $content) {
        if (KT::notIssetOrEmpty($content)) {
            return;
        }
        $content = strip_tags($content);
        echo "<meta property=\"$tagType\" content=\"$content\" />\n";
    }

}
