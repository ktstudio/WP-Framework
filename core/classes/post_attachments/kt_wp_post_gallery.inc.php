<?php

class KT_WP_Post_Gallery extends KT_WP_Post_Attachments_Base {

    const DEFAULT_THUMBNAIL_SIZE = "thumbnail";
    const DEFAULT_LARGE_SIZE = "large";

    private $excludeThumbnail = true;
    private $customImageIds;
    private $thumbnailSize = self::DEFAULT_THUMBNAIL_SIZE;
    private $largeSize = self::DEFAULT_LARGE_SIZE;

    /**
     * Objekt pro základní práci s obrázky přiřazené k postu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     */
    public function __construct(WP_Post $post) {
        parent::__construct($post);
    }

    // --- gettery -------------------

    /**
     * @return boolean
     */
    private function getExcludeThumbnail() {
        return $this->excludeThumbnail;
    }

    /**
     * @return array
     */
    private function getCustomImageIds() {
        return $this->customImageIds;
    }

    /**
     * @return string
     */
    private function getThumbnailSize() {
        return $this->thumbnailSize;
    }

    /**
     * @return string
     */
    private function getLargeSize() {
        return $this->largeSize;
    }

    // --- settery ------------------------

    /**
     * Nastaví, zda se má z kolekce obrázků odstranit náhledový obrázek zadaného postu.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param type $bool
     * @return \KT_WP_Post_Gallery
     */
    public function setExcludeThumbnail($bool) {
        if (is_bool($bool)) {
            $this->excludeThumbnail = $bool;
        }

        return $this;
    }

    /**
     * Nastaví pole vlastních (výhradních) IDs obrázků do galerie (WP_Query -> post__in)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param array $customImageIds
     */
    public function setCustomImageIds(array $customImageIds) {
        $this->customImageIds = $customImageIds;
    }

    /**
     * Nastaví velikost obrázku, která bude použita jaké náhled
     * Pouze Wordpress image size - add_image_size();
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $thumbnailSize
     */
    public function setThumbnailSize($thumbnailSize) {
        $this->thumbnailSize = $thumbnailSize;
    }

    /**
     * Nastaveí velikost obrázku, která se bude použivát jako cíl odkazu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $largeSize
     * @return \KT_WP_Post_Gallery
     */
    public function setLargeSize($largeSize) {
        $this->largeSize = $largeSize;

        return $this;
    }

    // --- veřejné funkce -----------------------

    /**
     * Vrátí HTML s celou galerií včetně obrázků, linků a atributů - viz parametry
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $id // html id containeru
     * @param type $class // html class containeru
     * @param type $imgClass // class pro img tag
     * @param type $attr // případný attribuy pro každý obrázek
     * @param type $withSelfLink // mají mít obrázky odkaz sami na sebe pro large velikost?
     * @return string
     */
    public function getImageGallery($imgClass = null, $attr = array(), $withSelfLink = true) {

        $html = "";

        if (KT::notIssetOrEmpty($this->getFiles())) {
            return $html;
        }
        
        if(KT::notIssetOrEmpty($this->getAttrValueByName("id"))){
            $this->setAttrId("ktGalleryContainer");
        }
        
        if(KT::notIssetOrEmpty($this->getAttrClassString())){
            $this->addAttrClass("ktGallery");
        }
        
        $this->addAttrClass("postGalleryId-" . $this->getPost()->ID)
                ->addAttrClass($this->getPost()->post_type);

        $html .= $this->getContainerHeader();

        $html .= "<div {$this->getAttributeString()}>";

        foreach ($this->getFiles() as $image) {
            /* @var $image \WP_Post */
            $thumbnail = wp_get_attachment_image_src($image->ID, $this->getThumbnailSize());
            $large = wp_get_attachment_image_src($image->ID, $this->getLargeSize());

            $html .= "<dl class=\"gallery-item\"><dt>";

            if ($withSelfLink) {
                $html .= $this->getLinkTagToLargeImage($large, $attr, $image->post_title);
            }

            $html .= $this->getImageTag($image, $thumbnail, $imgClass);

            if ($withSelfLink) {
                $html .= "</a>";
            }
            $html .= "</dt></dl>";
        }

        $html .= "</div>";

        return $html;
    }

    /**
     * Provede inicializaci a načtení všech obrázků dle zadaného Post objektu a nastavení
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return \KT_WP_Post_Gallery
     */
    protected function initialize() {

        $queryArgs = array(
            "post_type" => KT_WP_ATTACHMENT_KEY,
            "post_status" => "inherit",
            "posts_per_page" => $this->getNumberFiles(),
            "post_mime_type" => "image",
            "orderby" => $this->getOrderby(),
            "order" => $this->getOrder()
        );

        $customImageIds = $this->getCustomImageIds();
        $isCustomImageIds = KT::arrayIssetAndNotEmpty($customImageIds);
        if ($this->getExcludeThumbnail()) {
            $thumbnailId = get_post_thumbnail_id($this->getPost()->ID);
            $queryArgs["post__not_in"] = array($thumbnailId);
            if ($isCustomImageIds) {
                $customImageIds = KT::arrayRemoveByValue($customImageIds, $thumbnailId);
            }
        }
        if ($isCustomImageIds) {
            $queryArgs["post__in"] = $customImageIds;
        } else {
            $queryArgs["post_parent"] = $this->getPost()->ID;
        }

        $images = new WP_Query($queryArgs);

        $this->setFiles($images->posts);

        return $this;
    }

    // --- privátní funkce --

    /**
     * Na základě předaných parametrů zhotoví <a> tag, který odkazuje na large velikost
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $large // data obrázku wp_get_attachment_image_src()
     * @param array $attr // atributy obrázky
     * @param string $title // attribut title
     * @return string
     */
    private function getLinkTagToLargeImage(array $large, array $attr = array(), $title) {

        if (KT::issetAndNotEmpty($attr) && is_array($attr)) {
            foreach ($attr as $attrName => $attrValue) {
                $htmlAttr = " $attrName = \"$attrValue\" ";
            }
        }

        return $html = "<a href=\"{$large[0]}\" title=\"$title\" class=\"{$this->getLinkClass()}\" $htmlAttr >";
    }

    /**
     * Na základě předaných parametrů zhotoví <img> tag s obrázek dle zvolené velikosti
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param WP_Post $image // objektu obrázku
     * @param array $thumbnail // data obrázku wp_get_attachment_image_src()
     * @param string $imgClass // atribut class obrázku
     * @return string
     */
    private function getImageTag(WP_Post $image, array $thumbnail, $imgClass = null) {
        return $html .= "<img src=\"$thumbnail[0]\" alt=\"$image->post_title\" class=\"$imgClass\">";
    }

}
