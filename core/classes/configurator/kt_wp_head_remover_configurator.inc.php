<?php

/**
 * Nástroj na odstraňování (systémových) head (attributů)
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
final class KT_WP_Head_Remover_Configurator {

    private $data = array();

    // --- gettery ----------------------

    /**
     * Vrátí zadené head (attributy) k odstranění
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getHeadRemoverData() {
        return $this->data;
    }

    // --- veřejné funkce ---------------

    /**
     * Zruší head na základě názvu a případných argumentů
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $headName
     * @param array $args
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeHead($headName, array $args = null) {
        if (KT::issetAndNotEmpty($headName)) {
            $this->data["$headName"] = $args;
        }
        return $this;
    }

    /**
     * Odstraní head attribut - really simple discovery link
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeRsdLinkHead() {
        $this->removeHead("rsd_link");
        return $this;
    }

    /**
     * Odstraní head attribut - wordpress verze
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeWpGeneratorHead() {
        $this->removeHead("wp_generator");
        return $this;
    }

    /**
     * Odstraní head attribut - rss feed odkazy
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeFeedLinksHead() {
        $this->removeHead("feed_links", array("priority" => 2));
        return $this;
    }

    /**
     * Odstraní head attribut - extra rss feed odkazy
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeExtraFeedLinksHead() {
        $this->removeHead("feed_links_extra", array("priority" => 3));
        return $this;
    }

    /**
     * Odstraní head attribut - odkaz na index
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeIndexRelLinkHead() {
        $this->removeHead("index_rel_link");
        return $this;
    }

    /**
     * Odstraní head attribut - wlwmanifest.xml
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeWLWManifestLinkHead() {
        $this->removeHead("wlwmanifest_link");
        return $this;
    }

    /**
     * Odstraní head attribut - náhodný post odkaz
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeStartPostRelLinkHead() {
        $this->removeHead("start_post_rel_link", array("priority" => 10, "params" => 0));
        return $this;
    }

    /**
     * Odstraní head attribut - rodičovský post odkaz
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeParentPostRelLinkHead() {
        $this->removeHead("parent_post_rel_link", array("priority" => 10, "params" => 0));
        return $this;
    }

    /**
     * Odstraní head attribut - předchozí a následující post odkaz
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeAdjacentPostsRelLinkHead() {
        $this->removeHead("adjacent_posts_rel_link", array("priority" => 10, "params" => 0));
        return $this;
    }

    /**
     * Odstraní head attribut - předchozí a následující post odkaz
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeAdjacentPostsRelLinkWpHead() {
        $this->removeHead("adjacent_posts_rel_link_wp_head", array("priority" => 10, "params" => 0));
        return $this;
    }

    /**
     * Odstraní head attribut - zkrácený post odkaz
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeShortLinkWpHead() {
        $this->removeHead("wp_shortlink_wp_head", array("priority" => 10, "params" => 0));
        return $this;
    }

    /**
     * Odstraní všechny systémové heady
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeAllSystemHeads() {
        $this->removeRsdLinkHead()
                ->removeWpGeneratorHead()
                ->removeFeedLinksHead()
                ->removeExtraFeedLinksHead()
                ->removeIndexRelLinkHead()
                ->removeWLWManifestLinkHead()
                ->removeStartPostRelLinkHead()
                ->removeParentPostRelLinkHead()
                ->removeAdjacentPostsRelLinkHead()
                ->removeAdjacentPostsRelLinkWpHead()
                ->removeShortLinkWpHead();
        return $this;
    }

    /**
     * Odstraní nedoporučené systémové heady
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function removeRecommendSystemHeads() {
        $this->removeRsdLinkHead()
                ->removeWpGeneratorHead()
                ->removeExtraFeedLinksHead()
                ->removeWLWManifestLinkHead()
                ->removeShortLinkWpHead();
        return $this;
    }

    /**
     * Provede odstraní všech zadaných head hodnot
     * Pozn.: netřeba volat veřejně - provede se automaticky v rámci @see \KT_WP_Configurator
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Head_Remover_Configurator
     */
    public function doRemoveHeads() {
        foreach ($this->getHeadRemoverData() as $head => $args) {
            if (KT::arrayIssetAndNotEmpty($args)) {
                $priority = KT::arrayTryGetValue($args, "priority");
                if (KT::tryGetInt($priority)) {
                    $params = KT::arrayTryGetValue($args, "params");
                    if (KT::tryGetInt($priority)) {
                        remove_action("wp_head", "$head", $priority, $params);
                    } else {
                        remove_action("wp_head", "$head", $priority);
                    }
                }
            } else {
                remove_action("wp_head", "$head");
            }
        }
    }

}
