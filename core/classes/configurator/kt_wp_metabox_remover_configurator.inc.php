<?php

/**
 * Třída pro odstraňování (existujících) metaboxů z WP v rámci KT WP konfigurátoru
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
final class KT_WP_Metabox_Remover_Configurator {

    private $data = array();

    // --- gettery ----------------------

    /**
     * @return array
     */
    public function getMetaboxRemoverData() {
        return $this->data;
    }

    // --- veřejné funkce ---------------

    /**
     * Zruší metabox na základě názvu, stránky a contextu
     *
     * @param string $metaboxId
     * @param string $postType
     * @param string $context
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeMetabox($metaboxId, $postType, $context) {
        if (KT::issetAndNotEmpty($metaboxId) && KT::issetAndNotEmpty($postType) && KT::issetAndNotEmpty($context)) {
            array_push($this->data, array($metaboxId, $postType, $context));
        }
        return $this;
    }

    /**
     * Zruší dashboard metabox na základě názvu (a contextu)
     *
     * @param string $metaboxId
     * @param string $context
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardMetabox($metaboxId, $context = "core") {
        return $this->removeMetabox($metaboxId, "dashboard", $context);
    }

    /**
     * Odstraní meatbox s volbou tagů u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removePostTagMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("tagsdiv-post_tag", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s volbou kategorií u přislušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeCategoryMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("categorydiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s možností zadání excerptu u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeExcerptMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("postexcerpt", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s možností ovládání Track backů u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeTrackBackMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("trackbacksdiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s možností ovládání komentářů u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeCommentStatusMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("commentstatusdiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s komentáři u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeCommentsMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("commentsdiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní metabox s revisions u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeRevisionsMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("revisionsdiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s autorem u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeAuthorMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("authordiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní meatbox s vlastními inputy u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeCustomFieldsMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("postcustom", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní metabox se zadáním vlastního url slugu u příslušného post type
     * Default post_type = post
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeSlugMetabox($postType = KT_WP_POST_KEY) {
        $this->removeMetabox("slugdiv", $postType, "normal");
        return $this;
    }

    /**
     * Odstraní metabox s vlastnostní stránky u příslušného post type
     * Default post_type = page
     *
     * @param string $postType
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removePageParentMetabox($postType = KT_WP_PAGE_KEY) {
        $this->removeMetabox("pageparentdiv", $postType, "side");
        return $this;
    }

    /**
     * Odstraní "Right Now" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardRightNow() {
        $this->removeDashboardMetabox("dashboard_right_now", "normal");
        return $this;
    }

    /**
     * Odstraní "Recent Comments" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardRecentComments() {
        $this->removeDashboardMetabox("dashboard_recent_comments", "normal");
        return $this;
    }

    /**
     * Odstraní "Incoming Links" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardIncomingLinks() {
        $this->removeDashboardMetabox("dashboard_incoming_links", "normal");
        return $this;
    }

    /**
     * Odstraní "Plugins" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardPlugins() {
        $this->removeDashboardMetabox("dashboard_plugins", "normal");
        return $this;
    }

    /**
     * Odstraní "Activity" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardActivity() {
        $this->removeDashboardMetabox("dashboard_activity", "side");
        return $this;
    }

    /**
     * Odstraní "Quick Press" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardQuickPress() {
        $this->removeDashboardMetabox("dashboard_quick_press", "side");
        return $this;
    }

    /**
     * Odstraní "Recent Drafts" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardRecentDrafts() {
        $this->removeDashboardMetabox("dashboard_recent_drafts", "side");
        return $this;
    }

    /**
     * Odstraní "WordPress blog" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardPrimary() {
        $this->removeDashboardMetabox("dashboard_primary", "side");
        return $this;
    }

    /**
     * Odstraní "Other WordPress News" metabox z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardSecondary() {
        $this->removeDashboardMetabox("dashboard_secondary", "side");
        return $this;
    }

    /**
     * Odstraní všechny metabox z nástěnky Wordpressu, které jsou nativně na nástěnku zařazeny
     *
     * @param boolean $withoutRightNow - vše kromě aktuálního přehledu
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function clearWordpressDashboard($withoutRightNow = false) {
        $this->removeDashboardRecentComments()
                ->removeDashboardIncomingLinks()
                ->removeDashboardPlugins()
                ->removeDashboardActivity()
                ->removeDashboardQuickPress()
                ->removeDashboardRecentDrafts()
                ->removeDashboardPrimary()
                ->removeDashboardSecondary();
        if (!$withoutRightNow) {
            $this->removeDashboardRightNow();
        }
        return $this;
    }

}
