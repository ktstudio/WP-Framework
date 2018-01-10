<?php

/**
 * Prvek formuláře pro výběr obrázku nebo souboru, resp. přílohy 
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Media_Field extends KT_Field {

    const FIELD_TYPE = "media";

    private $isMultiple = false;

    /**
     * Založení objektu typu image loader - pouze Admin sekce
     * 
     * Pro funkčnost je ve stránce je nutné mít načteno:
     * wp_enqueue_media();
     * wp_enqueue_script("kt-img-loader");
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
        $this->addAttrClass("kt-file-loader button");
    }

    // --- getry & setry ---------------------

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /**
     * Vrátí hodnotu jako pole s ID(s)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getValues() {
        $value = $this->getValue();
        if (KT::issetAndNotEmpty($value)) {
            $ids = explode(",", $value);
            return $ids;
        }
        return null;
    }

    /**
     * Vrátí označení, zda se jedné o multi výběr
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function getIsMultiple() {
        return $this->isMultiple;
    }

    /**
     * Nastaví označení, zda se jedné o multi výběr
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $isMultiple
     * @return \KT_Media_Field
     */
    public function setIsMultiple($isMultiple) {
        $this->isMultiple = KT::tryGetBool($isMultiple) ? : false;
        return $this;
    }

    // --- veřejné metody ---------------------

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function renderField() {
        echo $this->getField();
    }

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getField() {
        $html = "<div class=\"file-load-box\">";
        $html .= $this->getFullSpanUrl();
        $html .= "<input type=\"hidden\" {$this->getBasicHtml()} value=\"{$this->getValue()}\" />";
        $multiple = ($this->getIsMultiple()) ? "true" : "false";
        $html .= "<span id=\"{$this->getAttrValueByName("id")}\" {$this->getAttrClassString()} data-multiple=\"$multiple\">" . __("Select file", "KT_CORE_DOMAIN") . "</span>";
        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }
        return $html;
    }

    // --- neveřejné metody ---------------------

    /**
     * Vrátí obsah s daty pro field v případě, že je attachment nastaven
     * V případě obrázku vrátí jeho thumbnail v případě souboru pouze název
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    private function getFullSpanUrl() {
        $html = null;
        $value = $this->getValue();
        if (KT::issetAndNotEmpty($value)) {
            $html .= "<span class=\"{$this->getAttrValueByName("id")} span-url full\">";
            $ids = $this->getValues();
            if (KT::arrayIssetAndNotEmpty($ids)) {
                foreach ($ids as $id) {
                    $attachment = get_post($id);
                    if (KT::issetAndNotEmpty($attachment)) {
                        if (self::isFileImageType($attachment)) {
                            $imageData = wp_get_attachment_image_src($attachment->ID, KT_WP_IMAGE_SIZE_THUBNAIL);
                            $fileTag = "<img class=\"file\" src=\"{$imageData[0]}\">";
                        }
                        if (!isset($fileTag)) {
                            $fileTag = "<span class=\"file\">{$attachment->post_title}</span>";
                        }
                        $removeFileTag = "<a class=\"remove-file\" data-id=\"{$attachment->ID}\"><span class=\"dashicons dashicons-no\"></span></a>";
                        $html .= "$fileTag $removeFileTag";
                    } else {
                        $html .= "<span class=\"file\">" . __("File was removed", "KT_CORE_DOMAIN") . "</span>";
                    }
                }
            } else {
                $html = "<span class=\"file\">" . __("Files were removed", "KT_CORE_DOMAIN") . "</span>";
            }
            $html .= "</span>";
        } else {
            $html = "<span class=\"{$this->getAttrValueByName("id")} span-url\"></span>";
        }
        return $html;
    }

    // --- statické funkce ---------------------

    /**
     * Funkce provede kontrolu, zda předaný attachment je typu obrázek
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Post $attachment
     * @return boolean
     */
    public static function isFileImageType(WP_Post $attachment) {
        $file = get_attached_file($attachment->ID);
        $matches = array();
        $ext = preg_match('/\.([^.]+)$/', $file, $matches) ? strtolower($matches[1]) : false;
        $image_exts = array("jpg", "jpeg", "gif", "png", "bmp", "tiff");
        if ("image/" == substr($attachment->post_mime_type, 0, 6) || $ext && "import" == $attachment->post_mime_type && in_array($ext, $image_exts)) {
            return true;
        }
        return false;
    }

}
