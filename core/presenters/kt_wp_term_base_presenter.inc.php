<?php

class KT_WP_Term_Base_Presenter extends KT_Presenter_Base {

    /**
     * Základní presenter pro zobrazení dat termu
     * Pokud je zadanán term jako stdClass není potřeba taxonomy
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param mixed $term - stdClass | string | int
     * @param string $taxonomy
     */
    public function __construct($term, $taxonomy = null) {
        $this->termModelInit($term, $taxonomy);
    }

    // --- getry & setry -----------

    /**
     * @return \KT_WP_Term_Base_Model
     */
    public function getModel() {
        return parent::getModel();
    }

    // --- veřejné funkce -----------

    public function getPaginationLinks(WP_Query $wp_query = null, $userArgs = array()) {
        return KT::getPaginationLinks($wp_query, $userArgs);
    }

    // --- privátní funkce ----------

    /**
     * Inicializace KT_WP_Term_Base_Modelu pro presenter
     * Pokud je zadanán term jako stdClass není potřeba taxonomy
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param mixed $term - stdClass | string | int
     * @param string $taxonomy
     * @return \KT_WP_Term_Base_Presenter
     */
    private function termModelInit($term, $taxonomy = null) {
        $termModel = new KT_WP_Term_Base_Model($term, $taxonomy);
        $this->setModel($termModel);

        return $this;
    }

    // --- statické funkce ----------

    /**
     * Vytažení všech termů pro zadané taxonomy vlastním způsobem ve formátu [ID, slug, name]
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * @link http://codeblow.com/questions/wordpress-get-terms-function-no-longer-working-during-my-wordpress-plugin/
     *
     * @global $wpdb
     * @param string $taxonomyName
     * @return array
     * @throws KT_Not_Set_Argument_Exception
     */
    public static function getAllTermsByTaxonomy($taxonomyName) {
        if (KT::issetAndNotEmpty($taxonomyName)) {
            global $wpdb;
            $query = "SELECT DISTINCT {$wpdb->terms}.term_id as ID, {$wpdb->terms}.slug as slug, {$wpdb->terms}.name as name
					  FROM {$wpdb->terms}
					  LEFT JOIN {$wpdb->term_taxonomy}
					  ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
					  WHERE {$wpdb->term_taxonomy}.taxonomy = '%s'
					  ORDER BY {$wpdb->terms}.name";
            $results = $wpdb->get_results($wpdb->prepare($query, $taxonomyName), ARRAY_A);
            return $results;
        }
        throw new KT_Not_Set_Argument_Exception("taxonomy");
    }

}
