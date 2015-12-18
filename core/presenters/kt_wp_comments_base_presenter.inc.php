<?php

/**
 * Základní presenter pro výpis komentářů pro post
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Comments_Base_Presenter extends KT_Presenter_Base {

    private $postId;
    private $post;
    private $comments;
    private $commentsEnabled;

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
     * Vrátí případný post podle zadaného ID
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return mixed WP_Post|null
     */
    public function getPost() {
        if (KT::issetAndNotEmpty($this->post)) {
            return $this->post;
        }
        return $this->post = get_post($this->getPostId());
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
        if (KT::issetAndNotEmpty($this->comments)) {
            return $this->comments;
        }
        return $this->initComments();
    }

    /**
     * Označení zda jsou komentáře povoleny (obecně i pro příspěvek)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function getCommentsEnabled() {
        if (KT::issetAndNotEmpty($this->commentsEnabled)) {
            return $this->commentsEnabled;
        }
        return $this->commentsEnabled = comments_open($this->getPostId()) && post_type_supports(KT_PRODUCT_KEY, "comments") && !post_password_required($this->getPost());
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
