<?php

abstract class KT_WP_Post_Attachments_Base extends KT_HTML_Tag_Base{

    const DEFAULT_ORDERBY = "ID";
    const DEFAULT_ORDER = "ASC";
    const DEFAULT_IMAGES_NUMBER = "-1";
    const DEFAULT_CONTAINER_TITLE_HTML_TAG = "h2";

    private $post = null;
    private $orderby = self::DEFAULT_ORDERBY;
    private $order = self::DEFAULT_ORDER;
    private $numberFiles = self::DEFAULT_IMAGES_NUMBER;
    private $files = null;
    private $linkClass = null;
    private $containerTitle = null;
    private $containerTitleHtmlTag = self::DEFAULT_CONTAINER_TITLE_HTML_TAG;

    /**
     * Abstraktní klása se základní sadou funkcí pro práci s post attachmentama a jejich výpisem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param WP_Post $post
     */
    public function __construct(WP_Post $post) {
        $this->setPost($post);
    }

    // --- abstraktní funkce ------------

    abstract protected function initialize();

    // --- gettery -------------------

    /**
     * @return \WP_Post
     */
    protected function getPost() {
        return $this->post;
    }

    /**
     * @return string
     */
    protected function getOrderby() {
        return $this->orderby;
    }

    /**
     * @return string
     */
    protected function getOrder() {
        return $this->order;
    }

    /**
     * @return int
     */
    public function getNumberFiles() {
        return $this->numberFiles;
    }

    /**
     * @return string
     */
    protected function getLinkClass() {
        return $this->linkClass;
    }

    /**
     * @return string
     */
    private function getGalleryTitle() {
        return $this->containerTitle;
    }

    /**
     * @return string
     */
    private function getGalleryTitleContainer() {
        return $this->containerTitleHtmlTag;
    }

    /**
     * @return array
     */
    public function getFiles() {
        if ($this->files === null) {
            $this->initialize();
        }

        return $this->files;
    }

    // --- settery ------------------------

    /**
     * Nastaví objekt postu, z kterého se budou attachmenty do kolekce načítat
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param WP_Post $post
     * @return \KT_WP_Post_Attachments_Base
     */
    public function setPost(WP_Post $post) {
        $this->post = $post;

        return $this;
    }

    /**
     * Nastaví, podle ktérého klíče se budou attachmenty řadit
     * Parametry dle WP_Query : @link http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $orderby
     */
    public function setOrderby($orderby) {
        $this->orderby = $orderby;
    }

    /**
     * Nastaví řarezení ASC nebo DESC
     * Parametry dle WP_Query : @link http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $order
     */
    public function setOrder($order) {
        $this->order = $order;
    }

    /**
     * Nastaví maximální počet attachmentů, který se má zobrazit.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param int $numberImages
     */
    public function setNumberFiles($numberImages) {
        $this->numberFiles = $numberImages;
    }

    /**
     * Nastaví třídu každému odkazu, který směřuje na výsledký attachment
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $linkClass
     * @return \KT_WP_Post_Attachments_Base
     */
    public function setLinkClass($linkClass) {
        $this->linkClass = $linkClass;

        return $this;
    }

    /**
     * Nastaví titulek výpisu attachmentu - zobrazí se jako nadpis
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $galleryTitle
     * @return \KT_WP_Post_Attachments_Base
     */
    public function setContainerTitle($galleryTitle) {
        $this->containerTitle = $galleryTitle;

        return $this;
    }

    /**
     * Nastaví název HTML tagu, který se použije jako obal titulku
     * Např.: div, span, h1, h2, atd. - bez tag závorek
     * 
     * Defaultně : h2
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param type $galleryTitleContainer
     * @return \KT_WP_Post_Attachments_Base
     */
    public function setContainerTitleHtmlTag($galleryTitleContainer) {
        $this->containerTitleHtmlTag = $galleryTitleContainer;

        return $this;
    }

    /**
     * Nastaví kolekci všech attachmentů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $files
     * @return \KT_WP_Post_Attachments_Base
     */
    protected function setFiles(array $files) {
        $this->files = $files;

        return $this;
    }

    // --- veřejné funkce ------------

    /**
     * Vrátí, zda má kolekce načtené nějaké attachmenty
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return boolean
     */
    public function hasFiles() {
        if (KT::issetAndNotEmpty($this->getFiles())) {
            return true;
        }

        return false;
    }

    /**
     * Na základě definovaných parametrů vytvoří hlavičku - začátek - výpisu attachmentů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @return string
     */
    protected function getContainerHeader() {

        $html = "";
        $title = $this->getGalleryTitle();

        if (KT::notIssetOrEmpty($title)) {
            return null;
        }

        if (KT::issetAndNotEmpty($this->getGalleryTitleContainer())) {
            $galleryTitleContainer = $this->getGalleryTitleContainer();
            $html .= "<$galleryTitleContainer>";
            $html .= $title;
            $html .= "</$galleryTitleContainer>";

            return $html;
        }

        return $title;
    }

}
