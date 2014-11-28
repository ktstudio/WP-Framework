<?php

class KT_Media_Field extends KT_Field {

    const FIELD_TYPE = 'media';

    /**
     * Založení objektu typu image loader - pouze Admin sekce
     * 
     * Pro funkčnost je ve stránce je nutné mít načteno:
     * wp_enqueue_media();
     * wp_enqueue_script("kt-img-loader");
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    /**
     * Provede výpis fieldu pomocí echo $this->getField()
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     */
    public function renderField() {
        echo $this->getField();
    }

    /**
     * Vrátí HTML strukturu pro zobrazní fieldu
     *
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     *
     * @return string
     */
    public function getField() {

        $html = "";

        $classes = $this->getClassAttributeContent();

        $html .= "<div class=\"file-load-box\">";

        if (kt_isset_and_not_empty($this->getValue())) {
            $html .= $this->getFullSpanUrl();
        } else {
            $html .= $this->getEmptySpanUrl();
        }

        $html .= "<input type=\"hidden\" ";
        $html .= $this->getBasicHtml();
        $html .= "value=\"{$this->getValue()}\" ";
        $html .= "/>";

        $html .= "<span class=\"kt-file-loader button $classes\" id=\"{$this->getId()}\">" . __('Vybrat soubor', KT_DOMAIN) . "</span>";

        if ($this->hasErrorMsg()) {
            $html .= parent::getHtmlErrorMsg();
        }

        return $html;
    }

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    // --- privátní funkce --------------

    /**
     * Vrátí prázdný content pro field bez nastaveného attachmentu
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     * 
     * @return string
     */
    private function getEmptySpanUrl() {
        return $html = "<span class=\"{$this->getId()} span-url\"></span>";
    }

    /**
     * Vrátí obsah s daty pro field v případě, že je attachment nastaven
     * V případě obrázku vrátí jeho thumbnail v případě souboru pouze název
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     * 
     * @return string
     */
    private function getFullSpanUrl() {

        $attachment = get_post($this->getValue());
        $removeFileTag = "<a class=\"remove-file\"><span class=\"dashicons dashicons-no\"></span></a>";

        if (self::isFileImageType($attachment)) {
            $imageData = wp_get_attachment_image_src($attachment->ID, KT_WP_IMAGE_SIZE_THUBNAIL);
            $fileTag = "<img class=\"file\" src=\"{$imageData[0]}\">";
        }

        if (kt_not_isset_or_empty($fileTag)) {
            $fileTag = "<span class=\"file\">{$attachment->post_title}</span>";
        }

        return $html = "<span class=\"{$this->getId()} span-url full\">$fileTag $removeFileTag</span>";
    }

    // --- statické funkce ------------

    /**
     * Funkce provede kontrolu, zda předaný attachment je typu obrázek
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link http://www.KTStudio.cz
     * 
     * @param WP_Post $attachment
     * @return boolean
     */
    public static function isFileImageType(WP_Post $attachment) {

        $file = get_attached_file($attachment->ID);

        $ext = preg_match('/\.([^.]+)$/', $file, $matches) ? strtolower($matches[1]) : false;

        $image_exts = array('jpg', 'jpeg', 'jpe', 'gif', 'png');

        if ('image/' == substr($attachment->post_mime_type, 0, 6) || $ext && 'import' == $attachment->post_mime_type && in_array($ext, $image_exts)) {
            return true;
        }

        return false;
    }

}
