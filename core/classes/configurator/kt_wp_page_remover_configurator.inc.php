<?php

final class KT_WP_Page_Remover_Configurator {

    const PAGE_KEY = "main-page";
    const SUBPAGE_KEY = "sub-page";

    private $menuCollection = array();
    private $subMenuCollectoin = array();

    // --- gettery ----------------------

    /**
     * Vrátí kolekci hlavních stránek, které mají být z Wordpress odstraněny
     * @return array
     */
    public function getMenuCollection() {
        return $this->menuCollection;
    }

    /**
     * Vrátí kolekcí všech podstránek, které mají být z wordpress odstraněny
     * @return array
     */
    public function getSubMenuCollectoin() {
        return $this->subMenuCollectoin;
    }

    // --- veřejné funkce -----------------

    /**
     * Do kolekce stránek přidá nový záznam pro odstranění stránky
     *
     * @param string $pageSlug
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removePage($pageSlug) {
        array_push($this->menuCollection, $pageSlug);

        return $this;
    }

    /**
     * Do kolekce podstránek přidá nový záznam po odstranění podstránky
     *
     * @param type $pageSlug
     * @param type $subPageSlug
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeSubPage($pageSlug, $subPageSlug) {
        array_push($this->subMenuCollectoin, array(
            self::PAGE_KEY => $pageSlug,
            self::SUBPAGE_KEY => $subPageSlug
        ));

        return $this;
    }

    /**
     * Z menu WP odstraní stránku nástěnky
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeDashbord() {
        $this->removePage("index.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s příspěvky
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removePosts() {
        $this->removePage("edit.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s mediální knihovnou
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeMedia() {
        $this->removePage("upload.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku se strankami
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removePages() {
        $this->removePage("edit.php?post_type=page");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s komentáři
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeComments() {
        $this->removePage("edit-comments.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku se vzhledem šablony
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeAppearance() {
        $this->removePage("themes.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s pluginy
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removePlugins() {
        $this->removePage("plugins.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s uživateli
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeUsers() {
        $this->removePage("users.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s nástroji
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeTools() {
        $this->removePage("tools.php");

        return $this;
    }

    /**
     * Z menu WP odstraní stránku s nastavením
     *
     * @return \KT_WP_Page_Remover_Configurator
     */
    public function removeSettings() {
        $this->removePage("options-general.php");

        return $this;
    }

}
