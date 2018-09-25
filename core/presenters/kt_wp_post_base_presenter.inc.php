<?php

/**
 * Základní presenter pro práci s příspěvky (posty)
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_WP_Post_Base_Presenter extends KT_Presenter_Base {

    const DEFAULT_OTHER_POSTS_LIMIT = 4;

    private $thumbnailImagePermalink;
    private $otherPostsQuery;
    private $otherPostsLimit;
    private $isRenderingOtherPosts = false;

    /**
     * Základní presenter pro práci s daty postu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param KT_Modelable|WP_Post $item
     * @param int $otherPostsLimit
     * 
     * @return \kt_post_type_presenter_base
     */
    function __construct($item = null, $otherPostsLimit = self::DEFAULT_OTHER_POSTS_LIMIT) {
        if (KT::issetAndNotEmpty($item)) {
            if ($item instanceof KT_Postable) {
                parent::__construct($item);
            } elseif ($item instanceof WP_Post) {
                /**
                 * Kvůli zpětné kompatibilitě, časem bude zrušeno -> používejte modely...
                 */
                parent::__construct(new KT_WP_Post_Base_Model($item));
                if (is_singular($item->post_type)) {
                    static::singularDetailPostProcess();
                }
            } else {
                throw new KT_Not_Supported_Exception("KT WP Post Base Presenter - Type of $item");
            }
        } else {
            parent::__construct();
        }
        $this->otherPostsLimit = KT::tryGetInt($otherPostsLimit);
    }

    // --- gettery ---------------------------

    /**
     * Vrátí KT WP Post Model
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_WP_Post_Base_Model
     */
    public function getModel() {
        return parent::getModel();
    }

    /**
     * Vrátí WP Query s ostatními příspěvky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \WP_Query
     */
    public function getOtherPostsQuery() {
        if (KT::issetAndNotEmpty($this->otherPostsQuery)) {
            return $this->otherPostsQuery;
        }
        return $this->otherPostsQuery = $this->initOtherPostsQuery();
    }

    /**
     * Počet ostatních příspěvků (především v rámci sestavení WP Query)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public function getOtherPostsLimit() {
        return $this->otherPostsLimit ?: self::DEFAULT_OTHER_POSTS_LIMIT;
    }

    /**
     * Indikátor, zda právě probíhá vykreslování ostatních příspěvků
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return bool
     */
    public function getIsRenderingOtherPosts()
    {
        return $this->isRenderingOtherPosts;
    }

    // --- veřejné metody ---------------------------

    /**
     * Kontrola, zda jsou k dispozici ostatní příspěvky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function hasOtherPosts() {
        $otherPostsQuery = $this->getOtherPostsQuery();
        return KT::issetAndNotEmpty($otherPostsQuery) && $otherPostsQuery->have_posts();
    }

    /**
     * Použijte hasOtherPosts
     * 
     * @deprecated since version 1.6
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function haveOtherPosts() {
        return $this->hasOtherPosts();
    }

    /**
     * Vypíše ostatní příspěvky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $loopName
     */
    public function theOtherPosts($loopName = KT_WP_POST_KEY) {
        if ($this->hasOtherPosts()) {
            $this->isRenderingOtherPosts = true;
            self::theQueryLoops($this->getOtherPostsQuery(), $loopName);
            $this->isRenderingOtherPosts = false;
        }
    }

    /**
     * Vypíše kolekci všech termů, kam je post zařazen na základě taxonomy
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $taxonomy
     * @param array $args // wp_get_object_terms
     * @param string $before // string || html před názvem termu
     * @param string $after // string || html za názvem termu
     * @return string
     */
    public function getListOfTermsName($taxonomy, array $args = array(), $before = "", $after = " ") {

        $html = "";
        $terms = $this->getModel()->getTerms($taxonomy, $args);

        if (KT::issetAndNotEmpty($terms)) {
            foreach ($terms as $term) {
                $html .= $before . $term->name . $after;
            }
        }

        return $html;
    }

    /**
     * Vypíše kolekci všech termů včetně odkazu na výpis na základě zadané taxonomy
     * Odkaz má vždy class = term {$term->slug} {$taxonomy} term-id-{$term->term_id}
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $taxonomy
     * @param array $args
     * @param type $before
     * @param type $after
     * @return string
     */
    public function getListOfLinksToTerms($taxonomy, array $args = array(), $before = "", $after = " ", $class = "kt-term-link") {
        $html = "";
        $terms = $this->getModel()->getTerms($taxonomy, $args);

        if (KT::issetAndNotEmpty($terms)) {
            foreach ($terms as $term) {
                $termUrl = get_term_link($term);
                $html .=  "$before<a href=\"$termUrl\" class=\"$class {$term->slug} $taxonomy data-term-id-{$term->term_id}\" title=\"{$term->name}\">{$term->name}</a>$after";
            }
        }

        return $html;
    }

    /**
     * Vrátí excerpt pokud je k dispozici a v HTML formátu (odstavec)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return mixed null|string (HTML)
     */
    public function getExcerpt() {
        if ($this->getModel()->hasExcerpt()) {
            return $html = "<p class=\"perex\">{$this->getModel()->getExcerpt(false)}</p>";
        }
        return null;
    }

    /**
     * Vrátí informace o autorovi, pokud jsou dostupné (nadpis + popis + případně i avatar)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $withAvatar
     * @return mixed null|string (HTML)
     */
    public function getAuthorBio($withAvatar = false) {
        $description = $this->getModel()->getAuthor()->getDescription();
        if (KT::issetAndNotEmpty($description)) {
            $title = sprintf(__("About author: %s", "KT_CORE_DOMAIN"), $this->getModel()->getAuthor()->getDisplayName());
            $html = "<h2>$title</h2>";
            if ($withAvatar) {
                $avatar = $this->getModel()->getAuthor()->getAvatar();
                $html .= "<div class=\"author-avatar\">$avatar</div>";
            }
            return $html .= "<p class=\"author-description\">$description</p>";
        }
        return null;
    }

    /**
     * Vrátí odkaz na předchozí příspěvěk
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $inSameCategory
     * @return mixed null|string (HTML)
     */
    public function getPreviousPostLink($inSameCategory = false, $excluded_terms = array(), $taxonomy = KT_WP_CATEGORY_KEY) {
        return previous_post_link("&laquo; %link", "%title", $inSameCategory, $excluded_terms, $taxonomy);
    }

    /**
     * Vrátí odkaz na následující příspěvěk
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $inSameCategory
     * @return mixed null|string (HTML)
     */
    public function getNextPostLink($inSameCategory = false, $excluded_terms = array(), $taxonomy = KT_WP_CATEGORY_KEY) {
        return next_post_link("%link &raquo;", "%title", $inSameCategory, $excluded_terms, $taxonomy);
    }

    /**
     * Vrátí HTML tag img s náhledovým obrázkem zadaného postu dle specifikace parametrů
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $imageSize
     * @param array $imageAttr // parametry obrázky $key => $value
     * @param string $defaultImageSrc
     * @return mixed null|string (HTML)
     */
    public function getThumbnailImage($imageSize, array $imageAttr = array(), $defaultImageSrc = null, $isLazyLoading = true) {
        return self::getThumbnailImageByPost($this->getModel()->getPost(), $imageSize, $imageAttr, $defaultImageSrc, $isLazyLoading);
    }

    /**
     * Vrátí odkaz a image tag na náhledový obrázek v Large velikosti.
     *
     * @deprecated since version 1.11
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $imageSize
     * @param string $tagId
     * @param string $tagClass
     * @param string $imageAttr

     * 
     * @return mixed null|string (HTML)
     */
    public function getThumbnailImageWithSelfLink($imageSize = KT_WP_IMAGE_SIZE_MEDIUM, $tagId = "thumbImage", $tagClass = "gallery", $imageAttr = array("class" => "img-responsive")) {
        if ($this->getModel()->hasThumbnail()) {
            $titleAttribute = $this->getModel()->getTitleAttribute();
            $attachment = get_post($this->getModel()->getThumbnailId());

            if (KT::issetAndNotEmpty($attachment->post_title)) {
                $imageAttr["alt"] = $attachment->post_title;
            }
            if (!array_key_exists("alt", $imageAttr)) {
                $imageAttr["alt"] = $titleAttribute;
            }
            $image = $this->getThumbnailImage($imageSize, $imageAttr);
            $linkImage = $this->getThumbnailImagePermalink();
            $isTagContainer = (KT::issetAndNotEmpty($tagId) && KT::issetAndNotEmpty($tagClass));
            $html = null;
            if ($isTagContainer) {
                $html .= KT::getTabsIndent(0, "<div id=\"$tagId\" class=\"$tagClass\">", true);
            }
            $html .= KT::getTabsIndent(1, "<a href=\"$linkImage\" title=\"$titleAttribute\" class=\"fbx-link\" rel=\"lightbox\">", true);
            $html .= KT::getTabsIndent(2, $image, true);
            $html .= KT::getTabsIndent(1, "</a>", true);
            if ($isTagContainer) {
                $html .= KT::getTabsIndent(0, "</div>", true, true);
            }
            return $html;
        }
        return null;
    }

    /**
     * Vrátí URL pro odkaz na obrázek (thumb)
     * Defaultní velikost - Large
     * 
     * @return string (URL)
     */
    public function getThumbnailImagePermalink($size = KT_WP_IMAGE_SIZE_LARGE) {
        if (KT::issetAndNotEmpty($this->thumbnailImagePermalink)) {
            return $this->thumbnailImagePermalink;
        }
        $src = wp_get_attachment_image_src($this->getModel()->getThumbnailId(), $size);
        if (KT::arrayIssetAndNotEmpty($src)) {
            return $this->thumbnailImagePermalink = $src[0];
        }
        return $this->thumbnailImagePermalink = null;
    }

    // --- neveřejné metody ------------------

    /**
     * Funkce je volána v konstruktoru presenteru a zavolá se pouze tehdy, pokud se jedná
     * o detail daného modelu. Funkce je připravená pro automatické zavádění funkcí právě
     * pro danou entitu.
     * 
     * @deprecated since version 1.5
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    protected function singularDetailPostProcess() {
        
    }

    /**
     * Vrátí a nastaví WP Query s ostatními články
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return \WP_Query
     */
    protected function initOtherPostsQuery() {
        $args = array(
            "post_type" => $this->getModel()->getPostType(),
            "post_status" => "publish",
            "post_parent" => 0,
            "posts_per_page" => $this->getOtherPostsLimit(),
            "orderby" => "date",
            "order" => "DESC",
        );
        if (KT::issetAndNotEmpty($this->getModel())) {
            $args["post__not_in"] = array($this->getModel()->getPostId());
        }
        return $this->otherPostsQuery = new WP_Query($args);
    }

    // --- statické metody ---------------------------

    /**
     * Vrátí HTML tag img s náhledovým obrázkem zadaného postu dle specifikace parametrů
     *
     * @author Tomáš Kocifaj, Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     * @param string $imageSize
     * @param array $imageAttr // parametry obrázky $key => $value
     * @param string $defaultImageSrc
     * @param boolean $isLazyLoading
     * @return mixed string || null
     */
    public static function getThumbnailImageByPost(WP_Post $post, $imageSize, array $imageAttr = array(), $defaultImageSrc = null, $isLazyLoading = true) {
        $defaults = array("alt" => esc_attr($post->post_title));
        if (has_post_thumbnail($post->ID)) {
            $thumbnailId = get_post_thumbnail_id($post->ID);
            $image = wp_get_attachment_image_src($thumbnailId, $imageSize);
            $imageSrc = $image[0];
            //if (!array_key_exists("class", $imageAttr) || !KT::stringContains($imageAttr["class"], "img-responsive")) { // pro responzivní obrázky nechceme pevné rozměry
            $defaults["width"] = $image[1];
            $defaults["height"] = $image[2];
            //}
        } else {
            $imageSrc = $defaultImageSrc;
        }
        $imageAttr = wp_parse_args($imageAttr, $defaults);
        return self::getImageHtmlTag($imageSrc, $imageAttr, $isLazyLoading);
    }

    /**
     * Sestavení HTML tagu obrázku na základě zadaných parametrů
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $imageSrc
     * @param array $imageAttr
     * @param boolean $isLazyLoading
     * @return mixed string|null
     */
    public static function getImageHtmlTag($imageSrc, array $imageAttr = array(), $isLazyLoading = true) {
        $image = new KT_Image($imageSrc);
        $image->setSrc($imageSrc);
        $image->setIsLazyLoading($isLazyLoading);
        $image->initialize($imageAttr);
        return $image->buildHtml();
    }

}
