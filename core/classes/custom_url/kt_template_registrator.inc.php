<?php

class KT_Template_Registrator {

    private $urls = array();

    /**
     * Založí nové rewrite rules do WP domena.cz/pagename
     * Po každém přidání nutné aktualizovat permalinks Nastaveni -> Trvalá Odkazy ->Uložit změny
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     */
    public function __construct() {
        add_filter("rewrite_rules_array", array($this, "register"));
        add_filter("template_include", array($this, "getCustomTemplate"));
    }

    /**
     * Přidá nové URL do WP front-endu
     *
     * @param string $pagename
     * @param string $title
     * @return \KT_Template_Registrator
     * @throws KT_Not_Set_Argument_Exception
     */
    public function addUrl($pagename, $filename, $path, $title = '') {
        if (KT::issetAndNotEmpty($pagename)) {
            $url = new KT_Custom_Template($pagename, $filename, $path, $title);
            $this->urls[$url->getPageName()] = $url;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception($pagename);
    }

    /**
     * Zavede všechny definované URL do rewrite rules
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $aRules
     * @return type
     */
    public function register($aRules) {

        $aNewRules = array();

        foreach ($this->urls as $url) {
            $pageName = $url->getPageName();
            $aNewRules[] = array("$pageName/?$" => "index.php?pagename=$pageName");
        }

        foreach ($aNewRules as $val) {
            $aRules = $val + $aRules;
        }

        return $aRules;
    }

    /**
     * Akce vrátí do WP Front-endu příslušnou templatu z uvedené path URL objektu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @global WP_Query $wp_query
     * @param string $template
     * @return string
     */
    public function getCustomTemplate($template) {
        global $wp_query;
        $pagename = $wp_query->query_vars["pagename"];

        if (KT::notIssetOrEmpty($pagename)) {
            return $template;
        }

        if (!in_array($pagename, array_keys($this->urls))) {
            return $template;
        }

        $wp_query->is_404 = '';
        $wp_query->is_kt_template = 1;

        $url = $this->getUrlObject($pagename);

        $kt_template = $url->getFullFilePath();

        if (is_file($kt_template)) {
            add_filter('wp_title', array($this, 'customUrlTitle'), 10, 1);
            return $kt_template;
        }

        return $template;
    }

    /**
     * Vrátí URL Object KT_Custom_Template na základě registrovaného pagename
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @return KT_Custom_Template
     * @throws KT_Not_Set_Argument_Exception
     */
    public function getUrlObject($name) {
        if (KT::issetAndNotEmpty($this->urls[$name])) {
            return $this->urls[$name];
        }
        throw new KT_Not_Set_Argument_Exception('name');
    }

    /**
     * Funkce pro zavedení HTML titulku pro custom page - nutné pro wp_title filter
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @global WP_Query $wp_query
     * @param string $title
     * @return string
     */
    public function customUrlTitle($title) {
        global $wp_query;
        $pagename = $wp_query->query_vars["pagename"];
        $url = $this->getUrlObject($pagename);
        return $pageTitle = $url->getTitle();
    }

}
