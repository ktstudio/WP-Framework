<?php

class KT_WP_Post_File_List extends KT_WP_Post_Attachments_Base {

    CONST DEFAULT_FILE_ICON = "dashicons-media-document";

    private $printIcon = true;
    private $dashIcons = self::DEFAULT_FILE_ICON;
    private $customIcon = null;

    /**
     * Objekt pro práci s kolekcí souborů (application type) nahrané u příspěvku
     * Použití dashIcons vyžaduje implementaci dashIcons styles do front-endu
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @param WP_Post $post
     */
    public function __construct(WP_Post $post) {
        parent::__construct($post);
    }

    // --- gettery ------------

    /**
     * @return boolean
     */
    private function getPrintIcon() {
        return $this->printIcon;
    }

    /**
     * @return string
     */
    private function getDashIcons() {
        return $this->dashIcons;
    }

    /**
     * @return string|null
     */
    private function getCustomIcon() {
        return $this->customIcon;
    }

    // --- settery ------------

    /**
     * Nastaví, zda se má při výpisu souborů vypisovat na začátku popisku ikonka
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @param boolean $printIcon
     * @return \KT_WP_Post_File_List
     */
    public function setPrintIcon($printIcon = false) {
        $this->printIcon = $printIcon;
        return $this;
    }

    /**
     * Nastaví, která dashicons se má pro výpis použít
     * DEFAULTNĚ: dashicons-media-document
     * 
     * Pokud bude nastavená custom icons, nebude se používat dashicons
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @param string $dashIcons - název class pro dashicons
     * @return \KT_WP_Post_File_List
     */
    public function setDashIcons($dashIcons) {
        $this->dashIcons = $dashIcons;
        return $this;
    }

    /**
     * Nastaví vlastní iconku z URL, která bude použitá při výpisu souborů
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @param string $customIcon - url na zdroj ikony
     * @return \KT_WP_Post_File_List
     */
    public function setCustomIcon($customIcon) {
        $this->customIcon = $customIcon;
        return $this;
    }

    // --- veřejné funkce ------------

    /**
     * Vytvoří HTML content s výpisem všech souborů, které 
     * 
     * @param type $id
     * @param type $class
     * @return string
     */
    public function getFileList($id = "ktFileListContainer", $class = "ktFiles") {

        if (!$this->hasFiles()) {
            return "";
        }

        $html = $this->getContainerHeader();

        $html .= "<ul id=\"$id postFilesId-{$this->getPost()->ID}\" class=\"$class {$this->getPost()->post_type}\">";

        foreach ($this->getFiles() as $image) {
            /* @var $image \WP_Post */

            $fileUrl = wp_get_attachment_url($image->ID);

            $html .= "<li class=\"file-item\">";
            $html .= "<a href=\"$fileUrl\" title=\"{$image->post_title}\" target=\"_blank\">{$this->getFileIconContent()} <span class=\"file-title\">{$image->post_title}</span></a>";
            $html .= "</li>";
        }

        $html .= "</ul>";

        return $html;
    }

    /**
     * Provede načtení všech souborů typu application u postu a přidá je do kolekce souborů
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @return \KT_WP_Post_File_List
     */
    protected function initialize() {

        $args = array(
            "post_type" => KT_WP_ATTACHMENT_KEY,
            "post_parent" => $this->getPost()->ID,
            "posts_per_page" => $this->getNumberFiles(),
            "post_mime_type" => "application",
            "orderby" => $this->getOrderby(),
            "order" => $this->getOrder(),
            "post_status" => "inherit"
        );

        $fileQuery = new WP_Query($args);

        $this->setFiles($fileQuery->posts);

        return $this;
    }

    // --- privátní funkce ------------

    /**
     * Dle nastavení objektu vrátí HTML tag s ikonou
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
     * @link www.ktstudio.cz
     * 
     * @return string
     */
    private function getFileIconContent() {

        if ($this->getPrintIcon() === false) {
            return "";
        }

        if (kt_isset_and_not_empty($this->getCustomIcon())) {
            return $html = "<span class=\"kt-custom-icon\"><img src=\"{$this->getCustomIcon()}\" alt=\"file-icon\"></span>";
        }

        if (kt_isset_and_not_empty($this->getDashIcons())) {
            return $html = "<span class=\"dashicon {$this->getDashIcons()}\"></span>";
        }

        return "";
    }

}
