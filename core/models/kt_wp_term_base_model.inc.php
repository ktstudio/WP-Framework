<?php

/**
 * Základní model pro práci s (taxonomy) termy
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_WP_Term_Base_Model extends KT_Model_Base implements KT_Termable {

    const TERM_SLUG = "slug";
    const TERM_ID = "id";

    private $term = null;
    private $metas = array();
    private $metaPrefix;
    private $permalink;
    private $editTermLink;

    /**
     * Základní model pro práci s termem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string|int|stdClass $term
     * @param string $taxonomy - povinná v případě, že $term je ID nebo Slug
     * @throws KT_Not_Set_Argument_Exception
     * @throws KT_Not_Supported_Exception
     */
    public function __construct($term, $taxonomy = null, $metaPrefix = null) {
        $this->metaPrefix = $metaPrefix;
        if ($term instanceof WP_Term) {
            $this->setTerm($term);
            return;
        }
        if ($term instanceof stdClass) {
            $this->setTerm($term);
            return;
        }
        if (KT::notIssetOrEmpty($taxonomy)) {
            throw new KT_Not_Set_Argument_Exception("Taxonomy must be added if term is not stdClass term object");
        }
        if (KT::isIdFormat($term)) {
            $this->initializeByTermid($term, $taxonomy);
            return;
        }
        if (is_string($term)) {
            $this->initializeByTermSlug($term, $taxonomy);
            return;
        }
        throw new KT_Not_Supported_Exception("Initializace of term is not correct");
    }

    /**
     * Provádí odchychycení funkcí se začátkem názvu "get", který následně prověří
     * existenci metody. Následně vrátí dle klíče konstanty hodnotu uloženou v DB
     * v opačném případě neprovede nic nebo nechá dokončit existující funkci.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $functionName
     * @param array $attributes
     * @return mixed
     */
    public function __call($functionName, array $attributes) {
        $autoIsserKey = $this->getAutoIsserKey($functionName);
        if (KT::issetAndNotEmpty($autoIsserKey)) {
            return KT::issetAndNotEmpty($this->getMetaValue($autoIsserKey));
        }
        $autoGetterKey = $this->getAutoGetterKey($functionName);
        if (KT::issetAndNotEmpty($autoGetterKey)) {
            return $this->getMetaValue($autoGetterKey);
        }
    }

    // --- gettery -----------------

    /**
     * @return WP_Term
     */
    public function getTerm() {
        return $this->term;
    }

    /**
     * @return array
     */
    public function getMetas() {
        if (KT::notIssetOrEmpty($this->metas)) {
            $this->initMetas();
        }

        return $this->metas;
    }

    /**
     * @return string
     */
    public function getMetaPrefix() {
        return $this->metaPrefix;
    }

    // --- settery -----------------

    /**
     * Provede nastavení zadaného termu na základě celého objektu stdClass
     * Probíhá ověření na term_id - jiné vyjímky se musí ošetřit jinde.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Term $term
     * @return \KT_WP_Term_Base_Model
     */
    public function setTerm($term) {
        if (KT::issetAndNotEmpty($term->term_id)) {
            $this->term = $term;
        }

        return $this;
    }

    /**
     * Nastavení (post) metas daného příspěvku
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $metas
     * @return \KT_WP_Post_Base_Model
     */
    private function setMetas(array $metas) {
        $this->metas = $metas;
        return $this;
    }

    // --- veřejné funkce ----------

    public function getId() {
        return $this->getTerm()->term_id;
    }

    public function getName() {
        return $this->getTerm()->name;
    }

    public function getSlug() {
        return $this->getTerm()->slug;
    }

    public function getTermTaxonomyId() {
        return $this->getTerm()->term_taxonomy_id;
    }

    public function getTaxonomy() {
        return $this->getTerm()->taxonomy;
    }

    public function getDescription() {
        return $this->getTerm()->description;
    }

    public function getParentId() {
        return $this->getTerm()->parent;
    }

    public function getPostCount() {
        return $this->getTerm()->count;
    }

    /**
     * Vrátí URL pro zobrazení detailu termu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    public function getPermalink() {
        if (isset($this->permalink)) {
            return $this->permalink;
        }
        return $this->permalink = get_term_link($this->getTerm());
    }

    /**
     * Vrátí URL pro editaci termu v administraci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getEditTermLink() {
        if (isset($this->editTermLink)) {
            return $this->editTermLink;
        }
        return $this->editTermLink = get_edit_term_link($this->getId());
    }

    /**
     * Vrátí URL pro zobrazení feedu z daného termu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $feed
     * @return string
     */
    public function getFeedLink($feed = "rss2") {
        return get_term_feed_link($this->getId(), $this->getTaxonomy(), $feed);
    }

    /**
     * Vrátí hodnotu z $wpdb->kttermmeta na základě zadaného meta_key
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $key
     * @return string|null
     */
    public function getMetaValue($key) {
        $metas = $this->getMetas();
        if (array_key_exists($key, $metas)) {
            $value = $metas[$key];
            if (isset($value)) {
                $value = (is_serialized($value)) ? unserialize($value) : $value;
                return $value;
            }
        }
        return null;
    }

    /**
     * Označení, zda je k dispozici, resp. vyplněn popisek
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return boolean
     */
    public function isDescription() {
        return KT::issetAndNotEmpty($this->getDescription());
    }

    /**
     * Vrátí, zda má term přiřazené některé posty
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return boolean
     */
    public function hasPosts() {
        if ($this->getPostCount() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Přidá zadanému příspěvku do kolekce termu tento term.
     * Neprovede replace, ale přidá další term!
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @param WP_Post $post
     * @return \KT_WP_Term_Base_Model
     */
    public function addMeToPost(WP_Post $post) {
        wp_set_object_terms($post->ID, $this->getSlug(), $this->getTaxonomy(), true);

        return $this;
    }

    /**
     * Nastaví poustu tento term a ostatní zruší.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param WP_Post $post
     */
    public function setMeToPost(WP_Post $post) {
        wp_set_object_terms($post->ID, $this->getSlug(), $this->getTaxonomy(), false);
    }

    /**
     * Vrátí, za předaný post v parametru má tento term přiřazen.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param WP_Post $post
     * @return boolean
     */
    public function hasMePost(WP_POst $post) {
        return has_term($this->getId(), $this->getTaxonomy(), $post);
    }

    // --- privátní funkce ---------

    /**
     * Provede načtení termu do modelu pomocí jeho ID a taxonomy, kde se term nachází.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $termId
     * @param string $taxonomy
     * @return \KT_WP_Term_Base_Model
     * @throws KT_Not_Supported_Exception
     * @throws InvalidArgumentException
     */
    private function initializeByTermid($termId, $taxonomy) {
        if (!KT::isIdFormat($termId)) {
            throw new KT_Not_Supported_Exception("First parametr $termId is not an ID format");
        }

        $term = get_term_by(self::TERM_ID, $termId, $taxonomy);

        if (KT::issetAndNotEmpty($term)) {
            $this->setTerm($term);
            return $this;
        }
    }

    /**
     * Provede načtení termu do modelu pomocí jeho slug a taxonomy, kde se term nachází.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $termSlug
     * @param string $taxonomy
     * @return \KT_WP_Term_Base_Model
     * @throws KT_Not_Supported_Exception
     * @throws InvalidArgumentException
     */
    private function initializeByTermSlug($termSlug, $taxonomy) {
        $term = get_term_by(self::TERM_SLUG, $termSlug, $taxonomy);

        if (KT::issetAndNotEmpty($term)) {
            $this->setTerm($term);
            return $this;
        }
    }

    /**
     * Inicializuje pole (post) metas na základě prefixu nebo všechny
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return \KT_Post_Type_Presenter_Base
     */
    private function initMetas() {
        $metas = self::getTermsMetas($this->getId(), $this->getMetaPrefix());
        if (KT::arrayIssetAndNotEmpty($metas)) {
            $this->setMetas($metas);
        } else {
            $this->setMetas(array());
        }
        return $this;
    }

    // --- statické funkce ---------

    /**
     * Funkcí vrátí všechny parametry příspěvku a to všechny nebo s prefixem
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @global WP_DB $wpdb
     * @param int $postId
     * @param string $prefix
     * @return array
     */
    public static function getTermsMetas($termId = null, $prefix = null) {
        global $wpdb;
        if (KT::isIdFormat($termId)) {
            $results = array();
            $query = "SELECT meta_key, meta_value FROM {$wpdb->termmeta} WHERE term_id = %d";
            $prepareData[] = $termId;
            if (isset($prefix)) {
                $query .= " AND meta_key LIKE '%s'";
                $prepareData[] = "{$prefix}%";
            }
            $metas = $wpdb->get_results($wpdb->prepare($query, $prepareData), ARRAY_A);
            foreach ($metas as $meta) {
                $results[$meta["meta_key"]] = $meta["meta_value"];
            }
            return $results;
        }
        return null;
    }

    /**
     * Stejně jako funkce get_terms vrátí kolekci všech termů ve formě stdClass, funkce
     * vrátí kolekci všech KT_WP_Term_Base_Modelů.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * @link http://codex.wordpress.org/Function_Reference/get_terms
     * 
     * @param string $taxonomy
     * @param array $args
     * @return array
     */
    public static function getModels($taxonomy, $args = []) {
        $args["taxonomy"] = $taxonomy;
        $modelCollection = array();
        $terms = get_terms($args);

        if (KT::notIssetOrEmpty($terms)) {
            return $modelCollection;
        }

        foreach ($terms as $term) {
            array_push($modelCollection, new KT_WP_Term_Base_Model($term));
        }

        return $modelCollection;
    }

}
