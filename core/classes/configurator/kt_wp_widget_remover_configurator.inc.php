<?php

/**
 * Nástroj na odstraňování (systémových) widgetů 
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
final class KT_WP_Widget_Remover_Configurator {

    private $data = array();

    // --- gettery ----------------------

    /**
     * Vrátí zadené widgety k odstranění
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getWidgetRemoverData() {
        return $this->data;
    }

    // --- veřejné funkce ---------------

    /**
     * Zruší widget na základě názvu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $widgetName
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeWidget($widgetName) {
        if (KT::issetAndNotEmpty($widgetName)) {
            array_push($this->data, $widgetName);
        }
        return $this;
    }

    /**
     * Odstraní widget stránek
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removePagesWidget() {
        $this->removeWidget("WP_Widget_Pages");
        return $this;
    }

    /**
     * Odstraní widget kalendáře
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeCalendarWidget() {
        $this->removeWidget("WP_Widget_Calendar");
        return $this;
    }

    /**
     * Odstraní widget archivů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeArchivesWidget() {
        $this->removeWidget("WP_Widget_Archives");
        return $this;
    }

    /**
     * Odstraní widget odkazů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeLinksWidget() {
        $this->removeWidget("WP_Widget_Links");
        return $this;
    }

    /**
     * Odstraní meta widget
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeMetaWidget() {
        $this->removeWidget("WP_Widget_Meta");
        return $this;
    }

    /**
     * Odstraní vyhledávací widget
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeSearchWidget() {
        $this->removeWidget("WP_Widget_Search");
        return $this;
    }

    /**
     * Odstraní textový widget
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeTextWidget() {
        $this->removeWidget("WP_Widget_Text");
        return $this;
    }

    /**
     * Odstraní widget kategorií
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeCategoriesWidget() {
        $this->removeWidget("WP_Widget_Categories");
        return $this;
    }

    /**
     * Odstraní widget posledních příspěvků
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeRecentPostsWidget() {
        $this->removeWidget("WP_Widget_Recent_Posts");
        return $this;
    }

    /**
     * Odstraní widget posledních komentářů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeRecentCommentsWidget() {
        $this->removeWidget("WP_Widget_Recent_Comments");
        return $this;
    }

    /**
     * Odstraní widget RSS
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeRssWidget() {
        $this->removeWidget("WP_Widget_RSS");
        return $this;
    }

    /**
     * Odstraní widget tagů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeTagCloudWidget() {
        $this->removeWidget("WP_Widget_Tag_Cloud");
        return $this;
    }

    /**
     * Odstraní widget navigace - menu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeNavMenuWidget() {
        $this->removeWidget("WP_Nav_Menu_Widget");
        return $this;
    }

    /**
     * Odstraní widget audia
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeMediaAudioWidget() {
        $this->removeWidget("WP_Widget_Media_Audio");
        return $this;
    }

    /**
     * Odstraní widget obrázku
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeMediaImageWidget() {
        $this->removeWidget("WP_Widget_Media_Image");
        return $this;
    }

    /**
     * Odstraní widget videa
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeMediaVideoWidget() {
        $this->removeWidget("WP_Widget_Media_Video");
        return $this;
    }

    /**
     * Odstraní widget galerie
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeMediaGalleryWidget() {
        $this->removeWidget("WP_Widget_Media_Gallery");
        return $this;
    }

    /**
     * Odstraní widget vlastního HTML
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeCustomHtmlWidget() {
        $this->removeWidget("WP_Widget_Custom_HTML");
        return $this;
    }

    /**
     * Odstraní všechny systémové widgety
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $keepText - nechat text
     * @param boolean $keepMenu - nechat menu
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeAllSystemWidgets($keepText = false, $keepMenu = false, $keepCustomHtml = false) {
        $this->removePagesWidget()
                ->removeCalendarWidget()
                ->removeArchivesWidget()
                ->removeLinksWidget()
                ->removeMetaWidget()
                ->removeSearchWidget()
                ->removeCategoriesWidget()
                ->removeRecentPostsWidget()
                ->removeRecentCommentsWidget()
                ->removeRssWidget()
                ->removeTagCloudWidget()
                ->removeMediaAudioWidget()
                ->removeMediaImageWidget()
                ->removeMediaVideoWidget()
                ->removeMediaGalleryWidget();
        if (!$keepText) {
            $this->removeTextWidget();
        }
        if (!$keepMenu) {
            $this->removeNavMenuWidget();
        }
        if (!$keepCustomHtml) {
            $this->removeCustomHtmlWidget();
        }
        return $this;
    }

}
