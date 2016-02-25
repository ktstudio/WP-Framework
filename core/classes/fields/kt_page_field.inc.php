<?php

/**
 * Field typu select, který jako <option> načte sadu všech stránek
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_Page_Field extends KT_Select_Field {

    const FIELD_TYPE = "pages";
    const DEFAUL_PAGE_COUNT = -1;

    private $parentPage = null;
    private $pageTemplate = null;

    /**
     * Field typu select, který jako <option> načte sadu všech stránek
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $name
     * @param type $label
     */
    public function __construct($name, $label, $parentPage = null, $pageTemplate = null) {
        parent::__construct($name, $label);
        $this->setParentPage($parentPage);
        $this->setPageTemplate($pageTemplate);
        $this->pageQueryArgsInit();
    }

    // --- gettery & settery ---------------------

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    /** @return int */
    private function getParentPage() {
        return $this->parentPage;
    }

    /**
     * Nastaví případnou parent_page, jejíž děti budou nabídnuty v selectu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param int $parentPage
     */
    public function setParentPage($parentPage) {
        if (KT::isIdFormat($parentPage)) {
            $this->parentPage = $parentPage;
        }
        return $this;
    }

    /** @return string */
    private function getPageTemplate() {
        return $this->pageTemplate;
    }

    /**
     * Nastaví případný page template pro omezení výpisu dat
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $pageTemplate
     */
    public function setPageTemplate($pageTemplate) {
        $this->pageTemplate = $pageTemplate;
        return $this;
    }

    // --- neveřejné metody ---------------------

    /**
     * Objektu automaticky nastaví query po selekci stránek
     * 
     * @return \KT_Page_Field
     */
    private function pageQueryArgsInit() {
        $args = array(
            "post_type" => KT_WP_PAGE_KEY,
            "posts_per_page" => self::DEFAUL_PAGE_COUNT,
            "post_status" => "publish",
            "orderby" => "parent title",
            "order" => KT_Repository::ORDER_ASC,
        );

        $parentPage = $this->getParentPage();
        if (KT::issetAndNotEmpty($parentPage)) {
            $args["post_parent"] = $parentPage;
        }

        $pageTemplate = $this->getPageTemplate();
        if (KT::issetAndNotEmpty($pageTemplate)) {
            $args["meta_query"] = array(
                array(
                    "key" => KT_WP_META_KEY_PAGE_TEMPLATE,
                    "value" => "$pageTemplate"
                )
            );
        }

        $dataManager = new KT_Custom_Post_Data_Manager();
        $dataManager->setQueryArgs($args);

        $this->setDataManager($dataManager);
    }

}
