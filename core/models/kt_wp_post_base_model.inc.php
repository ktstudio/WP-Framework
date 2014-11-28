<?php

class KT_WP_Post_Base_Model extends KT_Model_Base {

    private $post = null;
    private $author = null;
    private $metas = array();
    private $gallery = null;
    private $files = null;
    private $data = array();

    /**
     * Základní model pro práci s daty post_typu
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     * @param int $postId
     * @return \KT_WP_Post_Base_Model
     */
    function __construct(WP_Post $post = null, $postId = null) {
        if (kt_isset_and_not_empty($post)) {
            $this->setPost($post);

            return $this;
        }

        if (kt_is_id_format($postId)) {
            $this->initPostFromId($postId);

            return $this;
        }
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
        if (kt_not_isset_or_empty($this->author)) {
            $this->initAuthor();
        }

        return $this->author;
    }

    /**
     * @return array
     */
    public function getMetas() {
        if (kt_not_isset_or_empty($this->metas)) {
            $this->initMetas();
        }

        return $this->metas;
    }

    /**
     * @return \KT_WP_Post_File_List
     */
    public function getFiles() {
        if (kt_not_isset_or_empty($this->files)) {
            $this->initFiles();
        }

        return $this->files;
    }

    /**
     *
     * @return \KT_WP_Post_Gallery
     */
    public function getGallery() {
        if (kt_not_isset_or_empty($this->gallery)) {
            $this->initGallery();
        }

        return $this->gallery;
    }

    // --- settery ------------------------

    /**
     * Nastaví objektu WP_Postu pro model
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.ktstudio.cz
     *
     * @param KT_WP_Post_Gallery $gallery
     * @return \KT_WP_Post_Base_Model
     */
    private function setGallery(KT_WP_Post_Gallery $gallery) {
        $this->gallery = $gallery;

        return $this;
    }

    private function setFiles(KT_WP_Post_File_List $files) {
        $this->files = $files;

        return $this;
    }

    // --- Veřejné funkce -------

    /**
     * Vrátí ID WP_Postu v rámci modelu
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @return int
     */
    public function getPostId() {
        return $this->getPost()->ID;
    }

    /**
     * Vrátí titulek modelu na základě post_title a aplikovaného filtru
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
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

        if ($withTheFilter == false) {
            return $excerptFilterered;
        }

        return apply_filters('the_excerpt', $excerptFilterered);
    }

    /**
     * Vrátí URL pro zobrazení detailu postu
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @return string
     */
    public function getPermalink() {
        return $permalink = get_the_permalink($this->getPost());
    }

    /**
     * Vrátí titulek postu ošetřen tak, aby mohl být součástí některého z HTML attributu
     * Hlavní pro title=""
     *
     * @return type
     */
    public function getTitleAttribute() {
        return $titleAttributeContent = esc_attr(strip_tags($this->getTitle()));
    }

    /**
     * Vrátí ID náhledového obrázku. Pokud není přiřazen, vrátí Null
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @return mixed null || int
     */
    public function getThumbnailId() {
        $thumbnailId = $this->getMetaValue("_thumbnail_id");

        if (kt_isset_and_not_empty($thumbnailId)) {
            return $thumbnailId;
        }

        return null;
    }

    /**
     * Vrátí datum publikace příspěvku v základním formátu "d.m.Y"
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param string $dateFormat
     * @return string
     */
    public function getPublishDate($dateFormat = "d.m.Y") {
        return mysql2date($dateFormat, $this->getPost()->post_date);
    }

    /**
     * Vrátí hodnotu z $wpdb->postmeta na základě zadaného meta_key
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param type $key
     * @return null
     */
    public function getMetaValue($key) {
        if (kt_not_isset_or_empty($this->metas)) {
            $this->initMetas();
        }

        if (!isset($this->metas[$key])) {
            return null;
        }

        $meta = $this->metas[$key];

        if (kt_isset_and_not_empty($meta)) {
            return $meta;
        }

        return null;
    }

    /**
     * Vrátí kolekci všech termů, kam je post zařazen na základě zadané taxonomy
     * Pokud ještě nebyly načteny, uloží je do proměnné $this->data->{$taxonomy} a znovu se na ně nedotazuje
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param string $taxonomy
     * @param array $args // wp_get_object_terms
     * @return type
     */
    public function getTermCollection($taxonomy, array $args = array()) {

        if (kt_not_isset_or_empty($this->$taxonomy)) {
            $termCollection = self::getTermCollectionByPost($this->getPost(), $taxonomy, $args);
            $this->$taxonomy = $termCollection;
        }

        return $this->$taxonomy;
    }

    /**
     * Vrátí, zda daný model má nebo nemá vyplněný post_excerpt v DB tabulce.
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasExcrept() {
        if (kt_isset_and_not_empty($this->getPost()->post_excerpt)) {
            return true;
        }

        return false;
    }

    /**
     * Zjistí, zda má model zadaný meta hodnotu na klíči - _thumbnail_id
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasThumbnail() {
        if (kt_isset_and_not_empty($this->getMetaValue("_thumbnail_id"))) {
            return true;
        }

        return false;
    }

    // --- private function ----

    /**
     * Inicializuje WP_Post objekt na základě zadaného id
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param type $postId
     * @return \KT_Post_Type_Presenter_Base
     * @throws KT_Null_Reference_Exception
     */
    private function initPostFromId($postId) {
        $postId = kt_try_get_int($postId);
        $post = get_post($postId);

        if (kt_isset_and_not_empty($post)) {
            $this->setPost($post);
        } else {
            throw new KT_Null_Reference_Exception("post");
        }

        return $this;
    }

    /**
     * Inicializuje WP_User objekt na základě post_author
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @return \KT_Post_Type_Presenter_Base
     */
    private function initAuthor() {
        $authorId = $this->getPost()->post_author;

        if (kt_is_id_format($authorId)) {
            $author = new KT_WP_User_Base_Model($authorId);
            $this->setAuthor($author);
        }

        return $this;
    }

    /**
     * Inicializuje pole (post) metas na na základě prefixu nebo všechny
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param string $metaNamePrefix
     * @return \KT_Post_Type_Presenter_Base
     */
    private function initMetas($metaNamePrefix = null) {
        $metas = self::getPostMetas($this->getPost()->ID, $metaNamePrefix);

        $this->setMetas($metas);

        return $this;
    }

    /**
     * Inicializuje objekt WP_Post_Gallery s kolekcí obrázků u postu
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
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
     * Vrátí všechny termy, kam daný post patří na základě zvolené taxonomy
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param WP_Post $post
     * @param string $taxonomy
     * @param array $args
     * @return mixed null || array
     */
    public static function getTermCollectionByPost(WP_Post $post, $taxonomy = KT_WP_CATEGORY_KEY, $args = array()) {
        $terms = wp_get_object_terms($post->ID, $taxonomy, $args);

        if (kt_isset_and_not_empty($terms) && !is_wp_error($terms)) {
            return $terms;
        }

        return null;
    }

    /**
     * Funkcí vrátí všechny parametry příspěvku a to všechny nebo s prefixem
     *
     * @author Martin Hlaváč
     * @link www.ktstudio.cz
     *
     * @global WP_Database $wpdb
     * @param int $postId
     * @param string $prefix
     * @return array
     */
    public static function getPostMetas($postId = null, $prefix = null) {
        global $wpdb;
        $results = array();

        $post = kt_setup_post_object($postId); // nastaví post object
        if (is_object($post)) {
            $query = "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d";
            $prepareData[] = $post->ID;
            if (isset($prefix)) {
                $query .= " AND meta_key LIKE '%s'";
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
     * @link www.ktstudio.cz
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
     * @link www.ktstudio.cz
     *
     * @param int $postId
     * @param string $metaKey
     * @param boolean $emptyText
     * @return string
     */
    public static function getPostMeta($postId, $metaKey, $emptyText = true) {
        $metaValue = self::getPostMetaValue($postId, $metaKey);
        if (kt_isset_and_not_empty($metaValue)) {
            return $metaValue;
        }
        if ($emptyText === true) {
            return KT_EMPTY_TEXT;
        }
        return null;
    }

}
