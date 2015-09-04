<?php

/**
 * Základní presenter pro výpis komentářů pro post
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Comments_Base_Presenter extends KT_Presenter_Base {

    private $postId;
    private $comments;

    /**
     * Založení základního presenteru pro výpis komentářů postu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $postId
     */
    public function __construct($postId) {
        parent::__construct();
        $this->postId = KT::tryGetInt($postId);
    }

    // --- getry & setry ------------------------

    /**
     * ID postu pro načtení komentářů
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public function getPostId() {
        return $this->postId;
    }

    /**
     * Kolekce komentářů pro zadaný post (ID)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getComments() {
        $query = $this->comments;
        if (KT::issetAndNotEmpty($query)) {
            return $query;
        }
        return $this->initComments();
    }

    // --- veřejné metody ------------------------

    /**
     * Kontrola, zda jsou k dispozici komentáře pro zadaný post (ID)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function haveComments() {
        return KT::arrayIssetAndNotEmpty($this->getComments());
    }

    /**
     * Vrátí kolekci pouze rodičovských komentářů 
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getParentComments() {
        return $this->getChildrenComments(0);
    }

    /**
     * Vrátí kolekci dceřiných komentářů 
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getChildrenComments($parentId) {
        $parents = array();
        if ($this->haveComments()) {
            foreach ($this->getComments() as $commentModel) {
                if ($commentModel->getParentId() == $parentId) {
                    $parents[$commentModel->getCommentId()] = $commentModel;
                }
            }
        }
        return $parents;
    }

    // --- neveřejné metody ------------------------

    /**
     * Inicializace a přiřazení kolekce komentářů pro zadaný post (ID)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    private function initComments() {
        $results = get_comments("post_id={$this->getPostId()}&orderby=comment_date&order=ASC");
        $comments = array();
        if (KT::arrayIssetAndNotEmpty($results)) {
            foreach ($results as $result) {
                $comments[$result->comment_ID] = new KT_WP_Comment_Base_Model($result);
            }
        }
        return $this->comments = $comments;
    }

}
