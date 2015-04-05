<?php

class KT_WP_Post_Base_Model extends KT_Model_Base {

    private $post = null;
    private $author = null;
    private $metas = array();
    private $metaPrefix;
    private $gallery = null;
    private $files = null;
    private $data = array();
    private $permalink = null;

    /**
     * Základní model pro práci s daty post_typu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     * @return \KT_WP_Post_Base_Model
     */
    function __construct(WP_Post $post = null, $metaPrefix = null) {
        if (KT::issetAndNotEmpty($post)) {
            $this->setPost($post);
        }
        $this->metaPrefix = $metaPrefix;
    }

    // --- magic functions -----

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __unset($name) {
        unset($this->data[$name]);
    }

    // --- gettery -------------

    /**
     * @return \WP_Post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * @return \KT_WP_User_Base_Model
     */
    public function getAuthor() {
        if (KT::notIssetOrEmpty($this->author)) {
            $this->initAuthor();
        }

        return $this->author;
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

    /**
     * @return \KT_WP_Post_File_List
     */
    public function getFiles() {
        if (KT::notIssetOrEmpty($this->files)) {
            $this->initFiles();
        }

        return $this->files;
    }

    /**
     *
     * @return \KT_WP_Post_Gallery
     */
    public function getGallery() {
        if (KT::notIssetOrEmpty($this->gallery)) {
            $this->initGallery();
        }

        return $this->gallery;
    }

    // --- settery ------------------------

    /**
     * Nastaví objektu WP_Postu pro model
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     * @return \KT_WP_Post_Base_Model
     */
    private function setPost(WP_Post $post) {
        $this->post = $post;
        return $this;
    }

    /**
     * Nastaví KT_WP_User_Base_Model objekt autora příspěvku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_WP_User_Base_Model $author
     * @return \KT_WP_Post_Base_Model
     */
    private function setAuthor(KT_WP_User_Base_Model $author) {
        $this->author = $author;
        return $this;
    }

    /**
     * Nastavení (post) metas daného příspěvku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array $metas
     * @return \KT_WP_Post_Base_Model
     */
    private function setMetas(array $metas) {
        $this->metas = $metas;
        return $this;
    }

    /**
     * Nastaví galerii obrázků daného příspěvku
     *
     * @author Tomáš Kocifaj
     *
     * @param KT_WP_Post_Gallery $gallery
     * @return \KT_WP_Post_Base_Model
     */
    private function setGallery(KT_WP_Post_Gallery $gallery) {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Nastaví seznam souborů daného příspěvku
     * 
     * @author Tomáš Kocifaj
     * 
     * @param KT_WP_Post_File_List $files
     * @return \KT_WP_Post_Base_Model
     */
    private function setFiles(KT_WP_Post_File_List $files) {
        $this->files = $files;
        return $this;
    }

    // --- Veřejné funkce -------

    /**
     * Vrátí ID WP_Postu v rámci modelu
     *
     * @author Tomáš Kocifaj
     *
     * @return int
     */
    public function getPostId() {
        return $this->getPost()->ID;
    }

    /**
     * Vrátí titulek modelu na základě post_title a aplikovaného filtru
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getTitle() {
        return $title = apply_filters('the_title', $this->getPost()->post_title, $this->getPostId());
    }

    /**
     * Vrátí obsah modelu na základě post_content a aplikovaného filtru
     * 
     * ----------------------------------------------------------------------------------
     * POZOR ! 
     * Metoda nezajišťuje všechny potřebné náležitosti jako the_content();
     * Pokud potřebujete pracovat se stránkováním, heslem či jinými funkcemi, použijte
     * běžnou funkci the_content();
     * ----------------------------------------------------------------------------------
     * 
     * Metoda šetří SQL requesty při prostém výpisu obsahu.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getContent() {
        return $content = apply_filters("the_content", $this->getPost()->post_content);
    }

    /**
     * Vrátí stručný popis modelu - pokud není zadán, vezme část obsahu modelu
     * 
     * ----------------------------------------------------------------------------------
     * POZOR ! 
     * Metoda nezajišťuje všechny potřebné náležitosti jako the_excerpt();
     * Pokud potřebujete pracovat s originálem, použijte funkci the_excrept();
     * ----------------------------------------------------------------------------------
     * 
     * Metoda šetří SQL requesty při prostém výpisu stručného popisku
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getExcerpt($withTheFilter = true) {
        if ($this->hasExcrept()) {
            $excerpt = $this->getPost()->post_excerpt;
        } else {
            $excerptLength = apply_filters('excerpt_length', 55);
            $excerptMore = apply_filters('excerpt_more', ' ' . '[&hellip;]');
            $excerpt = wp_trim_words($this->getPost()->post_content, $excerptLength, $excerptMore);
        }
        $excerptFilterered = apply_filters('get_the_excerpt', $excerpt);
        if ($withTheFilter) {
            return apply_filters('the_excerpt', $excerptFilterered);
        }
        return $excerptFilterered;
    }

    /**
     * Vrátí URL pro zobrazení detailu postu
     *
     * @author Martin Hlaváč
     *
     * @return string
     */
    public function getPermalink() {
        $permalink = $this->permalink;
        if (KT::issetAndNotEmpty($permalink)) {
            return $permalink;
        }
        return $this->permalink = get_the_permalink($this->getPostId());
    }

    /**
     * Vrátí titulek postu ošetřen tak, aby mohl být součástí některého z HTML attributu
     * Hlavní pro title=""
     * 
     * @author Tomáš Kocifaj
     *
     * @return string
     */
    public function getTitleAttribute() {
        return $titleAttributeContent = esc_attr(strip_tags($this->getTitle()));
    }

    /**
     * Vrátí ID náhledového obrázku. Pokud není přiřazen, vrátí Null
     *
     * @author Tomáš Kocifaj
     *
     * @return mixed null || int
     */
    public function getThumbnailId() {
        $thumbnailId = $this->getMetaValue("_thumbnail_id");

        if (KT::issetAndNotEmpty($thumbnailId)) {
            return $thumbnailId;
        }

        return null;
    }

    /**
     * Vrátí datum publikace příspěvku v základním formátu "d.m.Y"
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $dateFormat
     * @return string
     */
    public function getPublishDate($dateFormat = "d.m.Y") {
        return mysql2date($dateFormat, $this->getPost()->post_date);
    }

    /**
     * Vrátí uběhnutý čas od datumu publikace příspěvku
     * 
     * @author Tomáš Kocifaj
     * 
     * @return string
     */
    public function getElapsedTime() {
        $now = new DateTime();
        $orderCreated = new DateTime($this->getPost()->post_date);
        $diff = $now->diff($orderCreated);

        switch ($diff->d) {
            case 1:
                $dayString = __("den", KT_DOMAIN);
                break;
            case 2:
            case 3:
            case 4:
                $dayString = _("dny", KT_DOMAIN);

            default:
                $dayString = _("dní", KT_DOMAIN);
        }

        if ($diff->m > 0) {
            $diffTimeFormat = $diff->m . __(' měs', KT_DOMAIN) . ' ';
            $diffTimeFormat .= $diff->d . $dayString . ' ';
            $diffTimeFormat .= $diff->h . __(' hod', KT_DOMAIN) . ' ';
            $diffTimeFormat .= $diff->i . __(' min', KT_DOMAIN) . ' ';
        } elseif ($diff->d > 0) {
            $diffTimeFormat = $diff->d . $dayString . ' ';
            $diffTimeFormat .= $diff->h . __(' hod', KT_DOMAIN) . ' ';
            $diffTimeFormat .= $diff->i . __(' min', KT_DOMAIN) . ' ';
        } elseif ($diff->h > 0) {
            $diffTimeFormat .= $diff->h . __(' hod', KT_DOMAIN) . ' ';
            $diffTimeFormat .= $diff->i . __(' min', KT_DOMAIN) . ' ';
        } else {
            $diffTimeFormat .= $diff->i . __(' min', KT_DOMAIN) . ' ';
        }

        return $diffTimeFormat;
    }

    /**
     * Vrátí (za/daný) post type
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getPostType() {
        return $this->getPost()->post_type;
    }

    /**
     * Vrátí hodnotu z $wpdb->postmeta na základě zadaného meta_key
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
                return $value;
            }
        }
        return null;
    }

    /**
     * Vrátí kolekci všech termů, kam je post zařazen na základě zadané taxonomy
     * Pokud ještě nebyly načteny, uloží je do proměnné $this->data->{$taxonomy} a znovu se na ně nedotazuje
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $taxonomy
     * @param array $args // wp_get_object_terms
     * 
     * @return mixed null|array
     */
    public function getTerms($taxonomy, array $args = array()) {
        if (KT::notIssetOrEmpty($this->$taxonomy)) {
            $termCollection = self::getTermCollectionByPost($this->getPost(), $taxonomy, $args);
            $this->$taxonomy = $termCollection;
        }
        return $this->$taxonomy;
    }

    /**
     * Vrátí pole ve tvaru term ID => name pro zadanou taxonomii a podle parametrů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $taxonomy
     * @param array $args // wp_get_object_terms
     * 
     * @return array
     */
    public function getTermsNames($taxonomy, array $args = array()) {
        $terms = $this->getTerms($taxonomy, $args);
        $termsNames = array();
        if (KT::arrayIssetAndNotEmpty($terms)) {
            foreach ($terms as $term) {
                $termsNames[$term->term_id] = $term->name;
            }
        }
        return $termsNames;
    }

    /**
     * Vrátí pole ve tvaru term ID => slug pro zadanou taxonomii a podle parametrů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $taxonomy
     * @param array $args // wp_get_object_terms
     * 
     * @return array
     */
    public function getTermsSlugs($taxonomy, array $args = array()) {
        $terms = $this->getTerms($taxonomy, $args);
        $termsNames = array();
        if (KT::arrayIssetAndNotEmpty($terms)) {
            foreach ($terms as $term) {
                $termsNames[$term->term_id] = $term->slug;
            }
        }
        return $termsNames;
    }

    /**
     * Vrátí, zda daný model má nebo nemá vyplněný post_excerpt v DB tabulce.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasExcrept() {
        if (KT::issetAndNotEmpty($this->getPost()->post_excerpt)) {
            return true;
        }
        return false;
    }

    /**
     * Zjistí, zda má model zadaný meta hodnotu na klíči - _thumbnail_id
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasThumbnail() {
        if (KT::issetAndNotEmpty($this->getMetaValue("_thumbnail_id"))) {
            return true;
        }
        return false;
    }

    // --- private function ----

    /**
     * Inicializuje WP_User objekt na základě post_author
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_Post_Type_Presenter_Base
     */
    private function initAuthor() {
        $authorId = $this->getPost()->post_author;

        if (KT::isIdFormat($authorId)) {
            $author = new KT_WP_User_Base_Model($authorId);
            $this->setAuthor($author);
        }

        return $this;
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
        $metaNamePrefix = $this->getMetaPrefix();
        $metas = self::getPostMetas($this->getPost()->ID, $metaNamePrefix);
        $this->setMetas($metas);
        return $this;
    }

    /**
     * Inicializuje objekt WP_Post_Gallery s kolekcí obrázků u postu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_WP_Post_Base_Model
     */
    private function initGallery() {
        $postGallery = new KT_WP_Post_Gallery($this->getPost());
        $this->setGallery($postGallery);
        return $this;
    }

    /**
     * Inicializuje objekt KT_WP_Post_File_List s kolekců souborů u postu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Post_Base_Model
     */
    private function initFiles() {
        $fileList = new KT_WP_Post_File_List($this->getPost());
        $this->setFiles($fileList);
        return $this;
    }

    // --- (veřejné) statické funkce --

    /**
     * Vrátí term podle ID pro zadanou taxonomie
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param integer $termId
     * @param string $taxonomy
     * @return mixed|null|WP_Error Term Row from database
     */
    public static function getTaxonomyTerm($termId, $taxonomy) {
        if (KT::issetAndNotEmpty($termId)) {
            $term = get_term($termId, $taxonomy);
            if (KT::issetAndNotEmpty($term)) {
                return $term;
            }
        }
        return null;
    }

    /**
     * Vrátí název termu podle ID pro zadanou taxonomie
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param integer $termId
     * @param string $taxonomy
     * @return string
     */
    public static function getTaxonomyTermName($termId, $taxonomy) {
        $term = self::getTaxonomyTerm($termId, $taxonomy);
        if (KT::issetAndNotEmpty($term)) {
            return $term->name;
        }
        return null;
    }

    /**
     * Vrátí slug termu podle ID pro zadanou taxonomie
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param integer $termId
     * @param string $taxonomy
     * @return string
     */
    public static function getTaxonomyTermSlug($termId, $taxonomy) {
        $term = self::getTaxonomyTerm($termId, $taxonomy);
        if (KT::issetAndNotEmpty($term)) {
            return $term->slug;
        }
        return null;
    }

    /**
     * Vrátí všechny termy, kam daný post patří na základě zvolené taxonomy
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     * @param string $taxonomy
     * @param array $args
     * @return mixed null|array
     */
    public static function getTermCollectionByPost(WP_Post $post, $taxonomy = KT_WP_CATEGORY_KEY, $args = array()) {
        $terms = wp_get_object_terms($post->ID, $taxonomy, $args);

        if (KT::issetAndNotEmpty($terms) && !is_wp_error($terms)) {
            return $terms;
        }

        return null;
    }

    /**
     * Funkcí vrátí všechny parametry příspěvku a to všechny nebo s prefixem
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @global WP_Database $wpdb
     * @param int $postId
     * @param string $prefix
     * @return array
     */
    public static function getPostMetas($postId = null, $prefix = null) {
        global $wpdb;
        $results = array();

        $post = KT::setupPostObject($postId); // nastaví post object
        if (is_object($post)) {
            $query = "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d";
            $prepareData[] = $post->ID;
            if (isset($prefix)) {
                $query .= " AND meta_key LIKE '%s' OR meta_key = '_thumbnail_id'";
                $prepareData[] = $prefix . "%";
            }
            $postMetas = $wpdb->get_results($wpdb->prepare($query, $prepareData), ARRAY_A);
            foreach ($postMetas as $postMeta) {
                $results[$postMeta["meta_key"]] = $postMeta["meta_value"];
            }
            return $results;
        }
        return null;
    }

    /**
     * Získání případní konrétní hodnoty meta podle klíče pro konkrétní příspěvěk (ID)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @global WP_Database $wpdb
     * @param int $postId
     * @param string $metaKey
     * @return string|null
     */
    public static function getPostMetaValue($postId, $metaKey) {
        global $wpdb;
        $value = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %s AND meta_key = %s LIMIT 1", $postId, $metaKey));
        return $value;
    }

    /**
     * Získání případné konkrétní hodnoty meta podle klíče nebo KT_EMPTY_TEXT, či null
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int $postId
     * @param string $metaKey
     * @param boolean $emptyText
     * @return string
     */
    public static function getPostMeta($postId, $metaKey, $emptyText = true) {
        $metaValue = self::getPostMetaValue($postId, $metaKey);
        if (KT::issetAndNotEmpty($metaValue)) {
            return $metaValue;
        }
        if ($emptyText === true) {
            return KT_EMPTY_SYMBOL;
        }
        return null;
    }

}
