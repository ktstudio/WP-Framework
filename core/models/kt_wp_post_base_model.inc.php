<?php

/**
 * Základní model pro práci s příspěvky (post, page atd.)
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_WP_Post_Base_Model extends KT_Meta_Model_Base implements KT_Postable {

    const DEFAULT_EXCERPT_LENGTH = 55;

    private $post;
    private $postFormat;
    private $author;
    private $gallery;
    private $files;
    private $data = array();
    private $permalink;
    private $editPostLink;
    private $categoriesIds;
    private $wpCommentsCount;
    private $postTypeObject;

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
        parent::__construct($metaPrefix);
        if (KT::issetAndNotEmpty($post)) {
            $this->setPost($post);
        } else {
            trigger_error("Empty post variable in (KT WP) Post (Base) Model!", E_USER_NOTICE); // tato možnost bude úplně zrušena
        }
    }

    // --- magic funkce ---------------------

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

    // --- gettery ---------------------

    /**
     * @return \WP_Post
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * Vrátí případný přiřazený post formát dle postu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function getPostFormat() {
        if (KT::issetAndNotEmpty($this->postFormat)) {
            return $this->postFormat;
        }
        $post = $this->getPost();
        if (KT::issetAndNotEmpty($post)) {
            return $this->postFormat = get_post_format($post);
        }
        return $this->postFormat = null;
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
     * @return int
     */
    public function getAuthorId() {
        return $this->getAuthor()->getId();
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

    /**
     * Vrátí WP comments STD class s počtem komentářů příspěvku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return object Comment stats
     */
    public function getWpCommentsCount() {
        if (KT::issetAndNotEmpty($this->wpCommentsCount)) {
            return $this->wpCommentsCount;
        }
        return $this->wpCommentsCount = wp_count_comments($this->getPostId());
    }

    // --- settery ---------------------

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
     * Nastaví galerii obrázků daného příspěvku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
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
     * @link http://www.ktstudio.cz
     * 
     * @param KT_WP_Post_File_List $files
     * @return \KT_WP_Post_Base_Model
     */
    private function setFiles(KT_WP_Post_File_List $files) {
        $this->files = $files;
        return $this;
    }

    // --- veřejné funkce ---------------------

    /**
     * Vrátí ID WP_Postu v rámci modelu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
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
     * @param boolean $withTheFilter
     * 
     * @return string
     */
    public function getContent($withTheFilter = true) {
        $post = $this->getPost();
        if (KT::issetAndNotEmpty($post)) {
            $content = $post->post_content;
            if (KT::issetAndNotEmpty($content)) {
                if ($withTheFilter) {
                    return apply_filters("the_content", $content);
                }
                return apply_filters("get_the_content", $content);
            }
        }
        return null;
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
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $withTheFilter
     * @param int $customExcerptLength (Words)
     * @param string $customExcerptMore
     * 
     * @return string
     */
    public function getExcerpt($withTheFilter = true, $customExcerptLength = null, $customExcerptMore = null) {
        $post = $this->getPost();
        if (KT::issetAndNotEmpty($post)) {
            if ($this->hasExcerpt()) {
                $excerpt = $post->post_excerpt;
            } else {
                $excerpt = $post->post_content;
            }
            if (KT::issetAndNotEmpty($excerpt)) {
                $excerptMore = $customExcerptMore ? : apply_filters("excerpt_more", " [&hellip;]");
                $excerptLength = $customExcerptLength ? : apply_filters("excerpt_length", self::DEFAULT_EXCERPT_LENGTH);
                $excerpt = wp_trim_words($excerpt, $excerptLength, $excerptMore);
                if (KT::issetAndNotEmpty($excerpt)) {
                    $excerptFiltered = strip_shortcodes(apply_filters("get_the_excerpt", $excerpt));
                    if ($withTheFilter) {
                        return apply_filters("the_excerpt", $excerptFiltered);
                    }
                    return strip_tags($excerptFiltered);
                }
            }
        }
        return null;
    }

    /**
     * Vrátí celý excerpt pokud byl zadán
     * 
     * @author Jan Pokorný
     * @param bool $withTheFilters
     * @return string
     */
    public function getFullExcerpt($withTheFilters = true) {
        if ($this->hasExcerpt()) {
            $excerpt = strip_shortcodes($this->getPost()->post_excerpt);
            if ($withTheFilters) {
                $excerpt = apply_filters("the_excerpt", $excerpt);
            }
            return $excerpt;
        }
        return null;
    }

    /**
     * Vrátí URL pro zobrazení detailu postu
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
        return $this->permalink = get_the_permalink($this->getPostId());
    }

    /**
     * Vrátí URL pro editaci detailu postu v administraci
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getEditPostLink() {
        if (isset($this->editPostLink)) {
            return $this->editPostLink;
        }
        return $this->editPostLink = get_edit_post_link($this->getPostId());
    }

    /**
     * Vrátí titulek postu ošetřen tak, aby mohl být součástí některého z HTML attributu
     * Hlavní pro title=""
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
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
     * @link http://www.ktstudio.cz
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
     * Vrátí datum změny příspěvku v základním formátu "d.m.Y"
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $dateFormat
     * @return string
     */
    public function getModifiedDate($dateFormat = "d.m.Y") {
        return mysql2date($dateFormat, $this->getPost()->post_modified);
    }

    /**
     * Vrátí uběhnutý čas od datumu publikace příspěvku
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getElapsedTime() {
        $now = new DateTime();
        $orderCreated = new DateTime($this->getPost()->post_date);
        $diff = $now->diff($orderCreated);

        switch ($diff->d) {
            case 1:
                $dayString = __("Day", "KT_CORE_DOMAIN");
                break;
            case 2:
            case 3:
            case 4:
                $dayString = _("days", "KT_CORE_DOMAIN");

            default:
                $dayString = _("days", "KT_CORE_DOMAIN");
        }

        if ($diff->m > 0) {
            $diffTimeFormat = $diff->m . __(' month', "KT_CORE_DOMAIN") . ' ';
            $diffTimeFormat .= $diff->d . $dayString . ' ';
            $diffTimeFormat .= $diff->h . __(' hour', "KT_CORE_DOMAIN") . ' ';
            $diffTimeFormat .= $diff->i . __(' min', "KT_CORE_DOMAIN") . ' ';
        } elseif ($diff->d > 0) {
            $diffTimeFormat = $diff->d . $dayString . ' ';
            $diffTimeFormat .= $diff->h . __(' hours', "KT_CORE_DOMAIN") . ' ';
            $diffTimeFormat .= $diff->i . __(' min', "KT_CORE_DOMAIN") . ' ';
        } elseif ($diff->h > 0) {
            $diffTimeFormat = $diff->h . __(' hours', "KT_CORE_DOMAIN") . ' ';
            $diffTimeFormat .= $diff->i . __(' min', "KT_CORE_DOMAIN") . ' ';
        } else {
            $diffTimeFormat = $diff->i . __(' min', "KT_CORE_DOMAIN") . ' ';
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
     * Vrátí (za/daný) post slug, resp. name
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getSlug() {
        return $this->getPost()->post_name;
    }

    /**
     * Vrací post type object
     * 
     * @see https://codex.wordpress.org/Function_Reference/get_post_type_object
     * @author Jan Pokorný
     * @return stdClass
     */
    public function getPostTypeObject() {
        if (!isset($this->postTypeObject)) {
            $postType = $this->getPostType();
            $this->postTypeObject = get_post_type_object($postType);
        }
        return $this->postTypeObject;
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
     * Vrátí pole IDček kategorií příspěvku
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $args případné pole argumentů (budou "zacachovány")
     * 
     * @return array
     */
    public function getCategoriesIds($args = array()) {
        if (KT::issetAndNotEmpty($this->categoriesIds)) {
            return $this->categoriesIds;
        }
        return $this->categoriesIds = wp_get_post_categories($this->getPostId(), $args);
    }

    /**
     * Vrátí, zda daný model má nebo nemá vyplněný post_excerpt v DB tabulce.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasExcerpt() {
        if (KT::issetAndNotEmpty($this->getPost()->post_excerpt)) {
            return true;
        }
        return false;
    }

    /**
     * @deprecated since version 1.7
     * @see hasExcerpt()
     */
    public function hasExcrept() {
        return $this->hasExcerpt();
    }

    /**
     * Vrátí, zda daný model má nebo nemá vyplněný post_content v DB tabulce.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasContent() {
        if (KT::issetAndNotEmpty($this->getPost()->post_content)) {
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

    /**
     * Kontrola, zda má příspěvek vybraný požadovaný formát
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $format
     * @return boolean
     */
    public function hasPostFormat($format) {
        return has_post_format($format);
    }

    /**
     * Kontrola, zda je k dispozici WP comments STD class s počtem komentářů příspěvku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return object Comment stats
     */
    public function isWpCommentsCount() {
        return KT::issetAndNotEmpty($this->getWpCommentsCount());
    }

    /**
     * Vrátí počet povolených komentářů příspěvku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public function getApprovedCommentsCount() {
        if ($this->isWpCommentsCount()) {
            return $this->getWpCommentsCount()->approved;
        }
        return 0;
    }

    /**
     * Vrátí celkový počet komentářů příspěvku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public function getTotalCommentsCount() {
        if ($this->isWpCommentsCount()) {
            return $this->getWpCommentsCount()->total_comments;
        }
        return 0;
    }

    /**
     * Nahraje a nastaví postu thumbnail ze zadané URL adresy
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $thumbnailUrl
     * @return boolean
     */
    public function setThumbnailFromUrl($thumbnailUrl) {
        $postId = $this->getPostId();
        if (KT::issetAndNotEmpty($thumbnailUrl) && KT::isIdFormat($postId)) {
            $thumbnailData = file_get_contents($thumbnailUrl);
            if (KT::issetAndNotEmpty($thumbnailData)) {
                $fileName = sanitize_file_name($this->getSlug()) . "-{$postId}." . strtolower(pathinfo($thumbnailUrl, PATHINFO_EXTENSION));
                $uploadDir = wp_upload_dir();
                $uploadDirPath = $uploadDir["path"];
                if (wp_mkdir_p($uploadDirPath)) {
                    $file = path_join($uploadDirPath, $fileName);
                } else {
                    $file = path_join($uploadDir["basedir"], $fileName);
                }
                file_put_contents($file, $thumbnailData);
                $fileType = wp_check_filetype($fileName, null);
                $args = array(
                    "post_mime_type" => $fileType["type"],
                    "post_title" => $fileName,
                    "post_content" => $this->getTitleAttribute(),
                    "post_status" => "inherit"
                );
                $attachmentId = wp_insert_attachment($args, $file, $postId);
                require_once(ABSPATH . "wp-admin/includes/image.php");
                $attachmentMetadata = wp_generate_attachment_metadata($attachmentId, $file);
                wp_update_attachment_metadata($attachmentId, $attachmentMetadata);
                return set_post_thumbnail($postId, $attachmentId);
            }
        }
        return null;
    }

    // --- neveřejné funkce ---------------------

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
    protected function initMetas() {
        $metas = self::getPostMetas($this->getPostId(), $this->getMetaPrefix());
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

    // --- (veřejné) statické funkce ---------------------

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
     * @global WP_DB $wpdb
     * @param int $postId
     * @param string $prefix
     * @return array
     */
    public static function getPostMetas($postId = null, $prefix = null) {
        global $wpdb;
        $post = KT::setupPostObject($postId); // nastaví post object
        if (is_object($post)) {
            $results = array();
            $query = "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d";
            $prepareData[] = $post->ID;
            if (isset($prefix)) {
                $query .= " AND (meta_key LIKE '%s' OR meta_key = '" . KT_WP_META_KEY_THUMBNAIL_ID . "' OR meta_key = '" . KT_META_KEY_SINGLE_TEMPLATE . "')";
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
