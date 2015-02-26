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
     * @return array
     */
    public function getWidgetRemoverData() {
        return $this->data;
    }

    // --- veřejné funkce ---------------

    /**
     * Zruší widget na základě názvu
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
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removePagesWidget() {
        $this->removeWidget("WP_Widget_Pages");
        return $this;
    }

    /**
     * Odstraní widget kalendáře
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
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeArchivesWidget() {
        $this->removeWidget("WP_Widget_Archives");
        return $this;
    }

    /**
     * Odstraní widget odkazů
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
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeMetaWidget() {
        $this->removeWidget("WP_Widget_Meta");
        return $this;
    }

    /**
     * Odstraní vyhledávací widget
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
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeTextWidget() {
        $this->removeWidget("WP_Widget_Text");
        return $this;
    }

    /**
     * Odstraní widget kategorií
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
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeRecentPostsWidget() {
        $this->removeWidget("WP_Widget_Recent_Posts");
        return $this;
    }

    /**
     * Odstraní widget posledních komentářů
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
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeRssWidget() {
        $this->removeWidget("WP_Widget_RSS");
        return $this;
    }

    /**
     * Odstraní widget tagů
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeTagCloudWidget() {
        $this->removeWidget("WP_Widget_Tag_Cloud");
        return $this;
    }

    /**
     * Odstraní widget naivgace - menu
     *
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeNavMenuWidget() {
        $this->removeWidget("WP_Nav_Menu_Widget");
        return $this;
    }

    /**
     * Odstraní všechny systémové widgety
     *
     * @param bool $withoutText - vše kromě textového widgetu
     * @return \KT_WP_Widget_Remover_Configurator
     */
    public function removeAllSystemWidgets($withoutText = false) {
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
                ->removeNavMenuWidget();
        if (!$withoutText) {
            $this->removeTextWidget();
        }
        return $this;
    }

}
