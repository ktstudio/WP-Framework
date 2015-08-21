<?php

/**
 * Základní model pro práci s uživatelem a jeho daty
 *
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
class KT_WP_User_Base_Model extends KT_Meta_Model_Base {

    private $wpUser = null;

    /**
     * Sestavení základního modelu pro práci s uživatelem a jeho daty podle ID
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param integer $userId
     */
    function __construct($userId, $metaPrefix = null) {
        parent::__construct($metaPrefix);
        $this->wpUserInitById($userId);
    }

    // --- getry & setry ------------------------

    /*
     * @return \WP_User
     */
    public function getWpUser() {
        return $this->wpUser;
    }

    /**
     * Nastaví modelu objekt WP_Userera
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param WP_User $wpUser
     */
    public function setWpUser(WP_User $wpUser) {
        $this->wpUser = $wpUser;
    }

    /**
     * Vrátí ID uživatele
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return int
     */
    public function getId() {
        return $this->getWpUser()->ID;
    }

    /**
     * Vrátí niceName (přezdívku) uživatele vyplněnou v profilu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getNiceName() {
        return $this->getWpUser()->user_nicename;
    }

    /**
     * Vrátí URL stránky zadanou v profilu uživatele
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * return string
     */
    public function getWebUrl() {
        return $this->getWpUser()->user_url;
    }

    /**
     * Nastaví název, jakým chce být uživatel pojmenován dle nastavení v profili uživatele
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * return string;
     */
    public function getDisplayName() {
        return $this->getWpUser()->display_name;
    }

    /**
     * Vrátí email uživatele
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getEmail() {
        return $this->getWpUser()->user_email;
    }

    /**
     * Vrátí datum registrace uživatele dle zadaného formářu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $format
     * @return type
     */
    public function getRegistredDate($format = "d.m.Y") {
        return KT::dateConvert($this->getWpUser()->user_registered, $format);
    }

    /**
     * Vrátí jméno uživatele
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getFirstName() {
        return $this->getMetaValue(KT_User_Profile_Config::FIRST_NAME);
    }

    /**
     * Vrátí přijímení uživatele
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getLastName() {
        return $this->getMetaValue(KT_User_Profile_Config::LAST_NAME);
    }

    /**
     * Vrátí string složeny s jména a příjmení uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getFullName() {
        if (KT::issetAndNotEmpty($this->getFirstName()) && KT::issetAndNotEmpty($this->getLastName())) {
            return $this->getFirstName() . " " . $this->getLastName();
        }
        return null;
    }

    /**
     * Vrátí buď jméno a příjmení nebo zobrazované jméno
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getName() {
        return $this->getFullName() ? : $this->getDisplayName();
    }

    /**
     * Vrátí titulek autora ošetřen tak, aby mohl být součástí některého z HTML attributů
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getTitleAttribute() {
        return $titleAttributeContent = esc_attr(strip_tags(sprintf(__("Autor: %s", KT_DOMAIN), $this->getName())));
    }

    /**
     * Vrátí telefon uživatele - dle rozšíření WP Frameworku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return type
     */
    public function getPhone() {
        return $this->getMetaValue(KT_User_Profile_Config::PHONE);
    }

    /**
     * Vrátí popis uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return mixed string|null
     */
    public function getDescription() {
        return $this->getMetaValue("description");
    }

    /**
     * Vrátí avatar uživatele
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return mixed string|null (HTML)
     */
    public function getAvatar() {
        return get_avatar($this->getId(), 96, "", $this->getDisplayName());
    }

    /**
     * Vrátí URL adresu na detail autora (author.php)
     * 
     * @author Tomáš Kocifaj
     * 
     * @return string
     */
    public function getPermalink() {
        return get_author_posts_url($this->getId());
    }

    // --- veřejné metody ------------------------

    /**
     * Vráti počet příspěvků autora na základě předaných parametrů
     *
     * Pokud není požadavkem dělat rozdíl v post_type nebo post_status, stčí poslat
     * parametry jako null - u jednoho u druhého nebo u obou.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @global type $wpdb
     * @param string $postType - název post_typu - DEFAULTNĚ : post
     * @param string $postStatus - nazev statusu příspěvků - DEFAULTNĚ : publish
     * @return mixed
     */
    public function getUserPostCount($postType = KT_WP_POST_KEY, $postStatus = "publish") {
        global $wpdb;
        $preparData = array();

        array_push($preparData, $this->getId());

        $query = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d";

        if (KT::issetAndNotEmpty($postType)) {
            $query .= " AND post_type = %s";
            array_push($preparData, $postType);
        }

        if (KT::issetAndNotEmpty($postStatus)) {
            $query .= " AND post_status = %s";
            array_push($preparData, $postStatus);
        }

        $authorPostCount = $wpdb->get_var($wpdb->prepare($query, $preparData));

        if (KT::issetAndNotEmpty($authorPostCount)) {
            return $authorPostCount;
        }

        return null;
    }

    /**
     * Vrátí objekt WP_Query se všemi příspěvky, určitého typu a statusu, které uživatel vytvořil
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string|array $postType
     * @param string $postStatus
     * @param array $args - další argumenty pro WP_Query
     * @return \WP_Query
     */
    public function getQueryWithUserPosts($postType = KT_WP_POST_KEY, $postStatus = "publish", array $args = array()) {
        $basicArgs = array(
            "author" => $this->getId(),
            "posts_per_page" => -1,
            "post_type" => $postType,
            "post_status" => $postStatus
        );

        $basicArgs = array_merge($basicArgs, $args);

        return $postQuery = new WP_Query($basicArgs);
    }

    /**
     * Prověří, zda uživatel může nebo nemůže provádět změny v příslušné capability nebo roleName
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $capability
     * @return boolean
     */
    public function canUser($capability) {
        if (user_can($this->getWpUser(), $capability)) {
            return true;
        }

        return false;
    }

    /**
     * Načte data uživatele na základě příspěvku a jeho přiřazeného autora
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param WP_Post $post
     * @return \KT_WP_User_Base_Model
     * @throws KT_Not_Supported_Exception
     */
    public function wpUserInitByPostAuthor(WP_Post $post) {
        $userId = $post->post_author;

        if (KT::notIssetOrEmpty($userId)) {
            throw new KT_Not_Supported_Exception("Post has no post_author");
        }

        if (KT::issetAndNotEmpty($userId)) {
            $this->wpUserInitById($userId);
        }

        return $this;
    }

    /**
     * Vrátí všechny user metas k danému uživateli - v případě volby prefixu probíhá LIKE dotaz
     *
     * @author Tomáš Kocifaj
     * @url www.ktstudio.cz
     *
     * @global WP_DB $wpdb
     * @param int $userId
     * @param string $prefix
     * @return array
     */
    public static function getUserMetas($userId, $prefix = null) {
        global $wpdb;

        $query = "SELECT meta_key, meta_value FROM {$wpdb->usermeta} WHERE user_id = %d";

        if (KT::issetAndNotEmpty($prefix)) {
            $query .= " AND meta_key LIKE '$prefix%'";
        }

        $results = $wpdb->get_results($wpdb->prepare($query, $userId), ARRAY_A);

        if (KT::issetAndNotEmpty($results)) {
            foreach ($results as $result) {
                $clearResult[$result["meta_key"]] = $result["meta_value"];
            }
        } else {
            $clearResult = array();
        }

        return $clearResult;
    }

    // --- privátní metody ------------------------

    /**
     * Provede inicializaci uživatele a jeho dat.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param int $userId
     * @return \KT_WP_User_Base_Model
     * @throws KT_Not_Supported_Exception
     */
    private function wpUserInitById($userId) {
        if (KT::isIdFormat($userId)) {
            $wpUser = get_user_by("id", $userId);
            if ($wpUser) {
                $this->setWpUser($wpUser);
            } else {
                throw new KT_Not_Supported_Exception(__("ID uživatele neexistuje (ve WP databázi).", KT_DOMAIN));
            }
        }
        return $this;
    }

    /**
     * Provede inicializaci všech uživatelo usermetas a nastaví je do objektu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    protected function initMetas() {
        $metas = self::getUserMetas($this->getId(), $this->getMetaPrefix());
        $this->setMetas($metas);
        return $this;
    }

}
