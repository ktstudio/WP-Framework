<?php

class KT_Page_Field extends KT_Select_Field {

    const FIELD_TYPE = "pages";
    const DEFAUL_PAGE_COUNT = -1;

    private $parentPage = null;

    /**
     * Field typu select, který jako <option> načte sadu všech stránek
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $name
     * @param type $label
     */
    public function __construct($name, $label) {
        $this->pageQueryArgsInit();
        parent::__construct($name, $label);
        return $this;
    }

    // -- gettery ----------------

    /**
     * @return int
     */
    private function getParentPage() {
        return $this->parentPage;
    }

    // -- settery ----------------

    /**
     * Nastaví případnou parent_page, jejížš děti budou nabídnuty v selectu
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

    // --- veřejné funkce ----------

    public function getFieldType() {
        return self::FIELD_TYPE;
    }

    // --- privátní funkce ----------

    /**
     * Objektu automaticky nastaví query po selekci stránek
     * 
     * @return \KT_Page_Field
     */
    private function pageQueryArgsInit() {
        $args = array(
            "post_type" => KT_WP_PAGE_KEY,
            "posts_per_page" => self::DEFAUL_PAGE_COUNT,
            "post_status" => "publish"
        );

        if (KT::issetAndNotEmpty($this->getParentPage())) {
            $args["post_parent"] = $this->getParentPage();
        }

        $dataManager = new KT_Custom_Post_Data_Manager();
        $dataManager->setQueryArgs($args);

        $this->setDataManager($dataManager);
    }

}
