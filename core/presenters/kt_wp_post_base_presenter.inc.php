<?php

class KT_WP_Post_Base_Presenter extends KT_Presenter_Base {

    /**
     * Základní presenter pro práci s daty post_typu
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param WP_Post $post
     * @param int $postId
     * @return \kt_post_type_presenter_base
     */
    function __construct(WP_Post $post = null) {
        if (kt_isset_and_not_empty($post)) {
            $postModel = new KT_WP_Post_Base_Model($post);
            $this->setModel($postModel);
        }
    }

    // gettery

    /**
     * @return \KT_WP_Post_Base_Model
     */
    public function getModel() {
        return parent::getModel();
    }

    // --- veřejné funkce --

    /**
     * Vypíše kolekci všech termů, kam je post zařazen na základě taxonomy
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param string $taxonomy
     * @param array $args // wp_get_object_terms
     * @param string $before // string || html před názvem termu
     * @param string $after // string || html za názvem termu
     * @return string
     */
    public function getListOfTermsName($taxonomy, array $args = array(), $before = "", $after = " ") {

        $html = "";
        $terms = $this->getModel()->getTermCollection($taxonomy, $args);

        if (kt_isset_and_not_empty($terms)) {
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param type $taxonomy
     * @param array $args
     * @param type $before
     * @param type $after
     * @return string
     */
    public function getListOfLinksToTerms($taxonomy, array $args = array(), $before = "", $after = " ") {
        $html = "";
        $terms = $this->getModel()->getTermCollection($taxonomy, $args);

        if (kt_isset_and_not_empty($terms)) {
            foreach ($terms as $term) {
                $termUrl = get_term_link($term);
                $html .= $before . "<a href=\"$termUrl\" class=\"kt-term-link $term->slug $taxonomy term-id-{$term->term_id}\" title=\"$term->name\">" . $term->name . "</a>" . $after;
            }
        }

        return $html;
    }

    // --- public function -----

    /**
     * Vrátí HTML tag img s náhledovým obrázkem zadaného postu dle specifikace parametrů
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     *
     * @param string $imageSize
     * @param array $imageAttr // parametry obrázky $key => $value
     * @param string $defaultImageSrc
     * @return mixed null || string
     */
    public function getThumbnailImage($imageSize, array $imageAttr = array(), $defaultImageSrc = null, $isLazyLoading = true) {
        return self::getThumbnailImageByPost($this->getModel()->getPost(), $imageSize, $imageAttr, $defaultImageSrc, $isLazyLoading);
    }

    // --- static public function

    /**
     * Vrátí HTML tag img s náhledovým obrázkem zadaného postu dle specifikace parametrů
     *
     * @author Tomáš Kocifaj, Martin Hlaváč
     * @link www.ktstudio.cz
     *
     * @param WP_Post $post
     * @param string $imageSize
     * @param array $imageAttr // parametry obrázky $key => $value
     * @param string $defaultImageSrc
     * @param boolean $isLazyLoading
     * @return mixed string || null
     */
    public static function getThumbnailImageByPost(WP_Post $post, $imageSize, array $imageAttr = array(), $defaultImageSrc = null, $isLazyLoading = true) {
        if (has_post_thumbnail($post->ID)) {
            $thumbnailId = get_post_thumbnail_id($post->ID);
            $image = wp_get_attachment_image_src($thumbnailId, $imageSize);
            $imageSrc = $image[0];
            $imageAttr["width"] = $image[1];
            $imageAttr["height"] = $image[2];
            $imageAttr["alt"] = $post->post_title;
        } else {
            $imageSrc = $defaultImageSrc;
        }
        return self::getImageHtmlTag($imageSrc, $imageAttr, $isLazyLoading);
    }

    /**
     * Sestavení HTML tagu obrázku na základě zadaných parametrů
     * 
     * @author Martin Hlaváč
     * @link www.ktstudio.cz
     * 
     * @param string $imageSrc
     * @param array $imageAttr
     * @param boolean $isLazyLoading
     * @return mixed string|null
     */
    public static function getImageHtmlTag($imageSrc, array $imageAttr = array()) {
        $attr = "";
        
        if (kt_isset_and_not_empty($imageSrc)) {
            $parseAttr = wp_parse_args($imageAttr);
            if (kt_isset_and_not_empty($parseAttr)) {
                foreach ($parseAttr as $attrName => $attrValue) {
                    $attr .= " $attrName=\"$attrValue\"";
                }
            }
            return apply_filters("post_thumbnail_html", "<img src=\"$imageSrc\"$attr />");
        }
        return null;
    }

}
