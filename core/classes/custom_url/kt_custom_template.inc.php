<?php

class KT_Custom_Template {

    private $pageName;
    private $title;
    private $fileName;
    private $templatePath;

    /**
     * Založení nového Custom URL
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $pageName - bude volán soubor z adresy $path/$pagename.php
     * @param string $path - musí existovat
     * @param string $title - titulek stránky v HTML
     * @return \KT_Custom_Template
     * @throws KT_Not_Set_Argument_Exception
     */
    public function __construct($pageName, $fileName, $path, $title = '') {
        $this->setPageName($pageName);

        if (KT::issetAndNotEmpty($path)) {
            $this->setTemplatePath($path);
        } else {
            throw new KT_Not_Set_Argument_Exception('path');
        }

        $this->setFileName($fileName);

        if (KT::issetAndNotEmpty($title)) {
            $this->setTitle($title);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPageName() {
        return $this->pageName;
    }

    /**
     * Nastaví název stránky - slug v url
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $pageName
     * @return \KT_Custom_Template
     */
    public function setPageName($pageName) {
        $this->pageName = $pageName;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Nastaví HTML titulek pro danou templatu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $title
     * @return \KT_Custom_Template
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * Nastaví název souboru, který identifikuje script, který se bude pro zobrazení stránky volat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $fileName
     * @return \KT_Custom_Template
     * @throws InvalidArgumentException
     */
    public function setFileName($fileName) {
        $file = $this->getFullFilePath();
        if (is_file($file)) {
            $this->fileName = $fileName;
            return $this;
        } else {
            $this->fileName = '';
            throw new InvalidArgumentException(__("Tento soubor $file neexistuje!", KT_DOMAIN));
        }

        return $this;
    }

    /**
     * @return string
     */
    function getTemplatePath() {
        return $this->templatePath;
    }

    /**
     * Nastaví cestu na příslušnou templatu - musí existovat
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param type $path
     * @return \KT_Custom_Template
     * @throws InvalidArgumentException
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setTemplatePath($path) {
        if (KT::notIssetOrEmpty($path)) {
            throw new KT_Not_Set_Argument_Exception('path');
        }

        if (!is_dir($path)) {
            throw new InvalidArgumentException(__("Tato cesta $path neexistuje!", KT_DOMAIN));
        }

        $this->templatePath = $path;
        return $this;
    }

    // --- protected funkce ------------------

    /**
     * Vrátí celou cestu k templatě $path + $pagename.php
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return type
     * @throws InvalidArgumentException
     */
    protected function getFullFilePath() {
        if (is_dir($this->templatePath)) {
            $fileName = "{$this->fileName}.tmp.php";
            return path_join($this->templatePath, $fileName);
        }
        throw new InvalidArgumentException("template_path doesn't exist");
    }

    // --- statické funkce ------------------

    public static function getPermalink($pagename, $id = null) {
        global $wp_rewrite;
        $baseUrl = get_bloginfo('url');
        if ($wp_rewrite->permalink_structure) {
            if (KT::issetAndNotEmpty($id)) {
                $url = $baseUrl . "/{$pagename}/{$id}";
            } else {
                $url = $baseUrl . "/{$pagename}";
            }
        } else {
            if (KT::issetAndNotEmpty($id)) {
                $url = $baseUrl . "/?pagename={$pagename}&id={$id}";
            } else {
                $url = $baseUrl . "/?pagename={$pagename}";
            }
        }

        return $url;
    }

}
