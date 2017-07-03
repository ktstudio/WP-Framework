<?php

/**
 * Třída pro odstraňování (existujících) stránek z menu v WP v rámci KT WP konfigurátoru
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
final class KT_WP_Page_Remover_Configurator implements KT_WP_IConfigurator {

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

    public function initialize() {
        add_action("admin_menu", array($this, "registerPageRemoverAction"));
        add_action("admin_init", array($this, "registerSubPageRemoverAction"));
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
        $collection = $this->getMenuCollection();
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
        if (KT::issetAndNotEmpty($this->getSubMenuCollectoin())) {
            foreach ($this->getSubMenuCollectoin() as $subMenuPageDef) {
                remove_submenu_page($subMenuPageDef[KT_WP_Page_Remover_Configurator::PAGE_KEY], $subMenuPageDef[KT_WP_Page_Remover_Configurator::SUBPAGE_KEY]);
            }
        }

        return $this;
    }

}
