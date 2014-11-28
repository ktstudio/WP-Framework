<?php

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
     * @param string $page
     * @param string $context
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeMetabox($metaboxId, $page, $context) {
        if (kt_isset_and_not_empty($metaboxId) && kt_isset_and_not_empty($page) && kt_isset_and_not_empty($context)) {
            array_push($this->data, array($metaboxId, $page, $context));
        }

        return $this;
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
    public function removeTrackBacktMetabox($postType = KT_WP_POST_KEY) {
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
     * Odstraní meatbox s komenáři u příslušného post type
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
     * Odstraní meatbox s komenáři u příslušného post type
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
     * Odstraní meatbox s komenáři u příslušného post type
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
     * Odstraní metabox s rychlým příspěvkem z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardQuicPress() {
        $this->removeAuthorMetabox("dashboard_quick_press", "dashboard", "side");

        return $this;
    }

    /**
     * Odstraní metabox s novinkami o Wordpressu z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardPrimary() {
        $this->removeMetabox("dashboard_recent_drafts", "dashboard", "side");

        return $this;
    }

    /**
     * Odstraní metabox s aktivitami z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardActivity() {
        $this->removeMetabox("dashboard_activity", "dashboard", "side");

        return $this;
    }

    /**
     * Odstraní metabox se zpětnými linky z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardIncomingLinks() {
        $this->removeMetabox("dashboard_incoming_links", "dashboard", "normal");

        return $this;
    }

    /**
     * Odstraní metabox s poslendími komentáři z nástěnky Wordpressu
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function removeDashboardComments() {
        $this->removeMetabox("dashboard_recent_comments", "dashboard", "normal");

        return $this;
    }

    /**
     * Odstraní všechny metabox z nástěnky Wordpressu, které jsou nativně na nástěnku zařazeny
     *
     * @return \KT_WP_Metabox_Remover_Configurator
     */
    public function clearWordpressDashboard() {
        $this->removeDashboardActivity()
                ->removeDashboardComments()
                ->removeDashboardIncomingLinks()
                ->removeDashboardPrimary()
                ->removeDashboardQuicPress();

        return $this;
    }

}
