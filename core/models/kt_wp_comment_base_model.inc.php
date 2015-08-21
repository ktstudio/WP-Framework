<?php

/**
 * Základní model pro práci s komentáři a jeho daty
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Comment_Base_Model extends KT_Meta_Model_Base {

    private $comment;
    private $post;
    private $author;
    private $permalink;
    private $avatar;
    private $avatarUrl;

    /**
     * Sestavení základního modelu pro práci s komentáři na základě postu (ID)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int $comment
     * @param string $metaPrefix
     */
    function __construct($comment, $metaPrefix = null) {
        parent::__construct($metaPrefix);
        $this->initComment($comment);
    }

    // --- gettery ------------------------

    /**
     * ID přiřazeného postu
     * 
     * @return int
     */
    public function getCommentId() {
        return $this->getComment()->comment_ID;
    }

    /**
     * Vrátí objekt s komentářem
     * 
     * @return stdClass
     */
    public function getComment() {
        return $this->comment;
    }

    /**
     * Natavení ID přiřazeného postu
     * 
     * @param STD_Class $comment
     * @return \KT_WP_Comments_Base_Model
     * @throws KT_Not_Supported_Exception
     */
    private function setComment($comment) {
        if (KT::issetAndNotEmpty($comment)) {
            $this->comment = $comment;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("comment");
    }

    /**
     * Vrátí ID přiřazeného postu
     *
     * @return int
     */
    public function getPostId() {
        return $this->getComment()->comment_post_ID;
    }

    /**
     * Vrátí přiřazený post
     * 
     * @return \WP_Post
     */
    public function getPost() {
        $post = $this->post;
        if (KT::issetAndNotEmpty($post)) {
            return $post;
        }
        $postId = $this->getPostId();
        if (KT::isIdFormat($postId)) {
            return $this->post = get_post($postId);
        }
        return $this->post = null;
    }

    /**
     * Vrátí ID uživatele/autora komentáře
     *
     * @return string
     */
    public function getUsetId() {
        return $this->getComment()->user_id;
    }

    /**
     * Vrátí přiřazeného uživatele/autora
     * 
     * @return \WP_User
     */
    public function getUser() {
        $author = $this->author;
        if (KT::issetAndNotEmpty($author)) {
            return $author;
        }
        $userId = $this->getUsetId();
        if (KT::isIdFormat($userId)) {
            return $this->author = get_userdata($userId);
        }
        return $this->author = null;
    }

    /**
     * Vrátí jméno autora komentáře
     *
     * @return string
     */
    public function getAuthorName() {
        return $this->getComment()->comment_author;
    }

    /**
     * Vrátí e-mail autora komentáře
     *
     * @return string
     */
    public function getAuthorEmail() {
        return $this->getComment()->comment_author_email;
    }

    /**
     * Vrátí url autora komentáře
     *
     * @return string
     */
    public function getAuthorUrl() {
        return $this->getComment()->comment_author_url;
    }

    /**
     * Vrátí ip autora komentáře
     *
     * @return string
     */
    public function getAuthorIp() {
        return $this->getComment()->comment_author_IP;
    }

    /**
     * Vrátí datum komentáře
     * 
     * @param type $format
     * @return type
     */
    public function getDate($format = "d.m.Y H:i:s") {
        return KT::dateConvert($this->getComment()->comment_date, $format);
    }

    /**
     * Vrátí datum komentáře (GMT)
     * 
     * @param type $format
     * @return type
     */
    public function getDateGmt($format = "d.m.Y H:i:s") {
        return KT::dateConvert($this->getComment()->comment_date_gmt, $format);
    }

    /**
     * Vrátí obsah komentáře
     *
     * @return string
     */
    public function getContent() {
        return $this->getComment()->comment_content;
    }

    /**
     * Vrátí karmu komentáře
     *
     * @return string
     */
    public function getKarma() {
        return $this->getComment()->comment_karma;
    }

    /**
     * Vrátí povolení komentáře
     *
     * @return bool
     */
    public function getApproved() {
        return KT::tryGetBool($this->getComment()->comment_approved);
    }

    /**
     * Vrátí agenta (prohlížeče) komentáře
     *
     * @return string
     */
    public function getAgent() {
        return $this->getComment()->comment_agent;
    }

    /**
     * Vrátí typ komentáře
     *
     * @return string
     */
    public function getType() {
        return $this->getComment()->comment_type;
    }

    /**
     * Vrátí ID rodiče komentáře
     *
     * @return string
     */
    public function getParentId() {
        return $this->getComment()->comment_parent;
    }

    /**
     * Vrátí URL adresu na detail komentáře
     * 
     * @return string
     */
    public function getPermalink() {
        $permalink = $this->permalink;
        if (KT::issetAndNotEmpty($permalink)) {
            return $permalink;
        }
        return $this->permalink = get_comment_link($this->getCommentId());
    }

    /**
     * Vrátí (gr)avatar pohle emailu autora (jako HTML img)
     * 
     * @param int $size
     * 
     * @return string 
     */
    public function getAvatar($size = 70) {
        if (KT::issetAndNotEmpty($this->avatar)) {
            return $this->avatar;
        }
        return $this->avatar = get_avatar($this->getAuthorEmail(), $size, "", $this->getAuthorName());
    }

    /**
     * Vrátí URL (gr)avatara pohle emailu autora
     * 
     * @param int $size
     * 
     * @return string 
     */
    public function getAvatarUrl($size = 70) {
        if (KT::issetAndNotEmpty($this->avatarUrl)) {
            return $this->avatarUrl;
        }
        return $this->avatarUrl = get_avatar_url($this->getAuthorEmail(), array("size" => $size));
    }

    // --- veřejné metody ------------------------

    /**
     * Kontrola, zda je k dispoizici rodičovský komentář
     *
     * @return bool
     */
    public function hasParent() {
        return KT::isIdFormat($this->getParentId());
    }

    /**
     * Vrátí všechny comment metas k danému komentáři - v případě volby prefixu probíhá LIKE dotaz
     *
     * @author Tomáš Kocifaj
     * @url www.ktstudio.cz
     *
     * @global WP_DB $wpdb
     * @param int $commentId
     * @param string $prefix
     * @return array
     */
    public static function getCommentMetas($commentId, $prefix = null) {
        global $wpdb;

        $query = "SELECT meta_key, meta_value FROM {$wpdb->commentmeta} WHERE comment_id = %d";

        if (KT::issetAndNotEmpty($prefix)) {
            $query .= " AND meta_key LIKE '$prefix%'";
        }

        $results = $wpdb->get_results($wpdb->prepare($query, $commentId), ARRAY_A);

        if (KT::issetAndNotEmpty($results)) {
            foreach ($results as $result) {
                $output[$result["meta_key"]] = $result["meta_value"];
            }
        } else {
            $output = array();
        }

        return $output;
    }

    // --- privátní metody ------------------------

    private function initComment($comment) {
        if (KT::isIdFormat($comment)) {
            $this->setComment(get_comment($comment));
        } else {
            $this->setComment($comment);
        }
    }

    /**
     * Provede inicializaci všech uživatelo comment metas a nastaví je do objektu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    protected function initMetas() {
        $metas = self::getCommentMetas($this->getCommentId(), $this->getMetaPrefix());
        $this->setMetas($metas);
        return $this;
    }

}
