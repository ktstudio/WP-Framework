<?php

class KT_WP_Term_Base_Model extends KT_Model_Base {

    const TERM_SLUG = "slug";
    const TERM_ID = "id";

    private $term = null;

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
    public function __construct($term, $taxonomy = null) {
        if ($term instanceof stdClass) {
            $this->setTerm($term);
            return;
        }

        if (kt_not_isset_or_empty($taxonomy)) {
            throw new KT_Not_Set_Argument_Exception("Taxonomy must be added if term is not stdClass term object");
        }

        if (kt_is_id_format($term)) {
            $this->initializeByTermid($term, $taxonomy);
            return;
        }

        if (is_string($term)) {
            $this->initializeByTermSlug($term, $taxonomy);
            return;
        }

        throw new KT_Not_Supported_Exception("Initializace of term is not correct");
    }

    // --- gettery -----------------

    /**
     * @return object stdClass
     */
    public function getTerm() {
        return $this->term;
    }

    // --- settery -----------------

    /**
     * Provede nastavení zadaného termu na základě celého objektu stdClass
     * Probíhá ověření na term_id - jiné vyjímky se musí ošetřit jinde.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param stdClass $term
     * @return \KT_WP_Term_Base_Model
     */
    public function setTerm(stdClass $term) {
        if (kt_isset_and_not_empty($term->term_id)) {
            $this->term = $term;
        }

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

    public function getTermGroup() {
        return $this->getTerm()->term_group;
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
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return type
     */
    public function getPermalink() {
        return $permalink = get_term_link($this->getTerm());
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
     * @param WP_POst $post
     * @return type
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
        if (!kt_is_id_format($termId)) {
            throw new KT_Not_Supported_Exception("First parametr $termId is not an ID format");
        }

        $term = get_term_by(self::TERM_ID, $termId, $taxonomy);

        if (kt_isset_and_not_empty($term)) {
            $this->setTerm($term);
            return $this;
        }

        throw new InvalidArgumentException("Taxonomy : $taxonomy doesn't exist");
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

        if (kt_isset_and_not_empty($term)) {
            $this->setTerm($term);
            return $this;
        }

        throw new InvalidArgumentException("Taxonomy : $taxonomy doesn't exist");
    }

    // --- statické funkce ---------

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
    public static function getModels($taxonomy, $args) {

        $modelCollection = array();
        $terms = get_terms($taxonomy, $args);

        if (kt_not_isset_or_empty($terms)) {
            return $modelCollection;
        }

        foreach ($terms as $term) {
            array_push($modelCollection, new KT_WP_Term_Base_Model($term));
        }

        return $modelCollection;
    }

}
