<?php

/**
 * Třída nahrazující obecné soubory s funkcemi a klasickým volání pomocí statických metod
 */
class KT {
    // --- POLE - ARRAY ---------------------------

    /**
     * Vložení nového klíč-hodnota do pole za zadaný index na základně číselného indexu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $input
     * @param int $index
     * @param int|string $newKey
     * @param object $newValue
     * @return array
     * @throws KT_Not_Supported_Exception
     */
    public static function arrayInsert(array $input, $index, $newKey, $newValue) {
        $index = self::tryGetInt($index);
        $count = count($input);
        if ($index < 0 || $index >= $count) {
            throw new KT_Not_Supported_Exception("Index mimo rozsah: $index");
        }
        $output = array();
        $currentIndex = 0;
        foreach ($input as $key => $value) {
            if ($currentIndex === $index) {
                $output[$newKey] = $newValue;
            }
            $output[$key] = $value;
            $currentIndex ++;
        }
        return $output;
    }

    /**
     * Vložení nového klíč-hodnota do pole za zadaný index na základě klíče
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $input
     * @param int|string $index
     * @param int|string $newKey
     * @param object $newValue
     * @return array
     * @throws KT_Duplicate_Exception
     */
    public static function arrayInsertBefore(array $input, $index, $newKey, $newValue) {
        if (!array_key_exists($index, $input)) {
            throw new KT_Duplicate_Exception($key);
        }
        $output = array();
        foreach ($input as $key => $value) {
            if ($key === $index) {
                $output[$newKey] = $newValue;
            }
            $output[$key] = $value;
        }
        return $output;
    }

    /**
     * Vložení nového klíč-hodnota do pole před zadaný index na základě klíče
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $input
     * @param int|string $index
     * @param int|string $newKey
     * @param object $newValue
     * @return array
     * @throws KT_Duplicate_Exception
     */
    public static function arrayInsertAfter(array $input, $index, $newKey, $newValue) {
        if (!array_key_exists($index, $input)) {
            throw new KT_Duplicate_Exception($key);
        }
        $output = array();
        foreach ($input as $key => $value) {
            $output[$key] = $value;
            if ($key === $index) {
                $output[$newKey] = $newValue;
            }
        }
        return $output;
    }

    /**
     * Ze zadaného pole odstraní zadanou hodnotu a vrátí pole hodnot
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $haystack
     * @param int|string $needle
     * @return array
     */
    public static function arrayRemove(array $haystack, $needle) {
        foreach ($haystack as $key => $value) {
            if ($value == $needle) {
                unset($haystack[$key]);
            }
        }
        return array_values($haystack);
    }

    /**
     * Funkce smaže hodnoty z pole na základně předaného pole s klíčem
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $input - pole, kde se smažou hodnoty
     * @param array $deleteKeys - které klíče se mají smazat z $input
     * @return array
     */
    public static function arrayKeysRemove($input, array $deleteKeys = null) {
        if (!self::arrayIssetAndNotEmpty($deleteKeys)) {
            return $input;
        }

        foreach ($deleteKeys as $value) {
            unset($input[$value]);
        }

        return $input;
    }

    /**
     * Ze zadaného pole odstraní zadanou hodnotu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $haystack
     * @param int|string $needle
     * @return array
     */
    public static function arrayRemoveByValue(array $haystack = null, $needle) {
        if (!self::arrayIssetAndNotEmpty($haystack)) {
            return $haystack;
        }

        foreach ($haystack as $key => $value) {
            if ($value == $needle) {
                unset($haystack[$key]);
            }
        }
        return $haystack;
    }

    /**
     * Ze zadaného pole odstraní zadaný klíč (i s hodnotou)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $haystack
     * @param int|string $needle
     * @return array
     */
    public static function arrayRemoveByKey(array $haystack, $needle) {
        if (array_key_exists($needle, $haystack)) {
            unset($haystack[$needle]);
        }
        return $haystack;
    }

    /**
     * Vrátí, zda má pole více než jednu úroveň (zda existuje pole v poli)
     * 
     * @author Tomáš Kocifaj
     * @linkw http://www.ktstudio.cz
     * 
     * @param array $array
     * @return boolean
     */
    public static function arrayIsMulti(array $array) {
        if (count($array) == count($array, COUNT_RECURSIVE)) {
            return true;
        }
        return false;
    }

    /**
     * Kontrola, zda je zadaný parameter přiřezený, typu pole a má jeden nebo více záznamů
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array|type $array
     * @return boolean
     */
    public static function arrayIssetAndNotEmpty($array) {
        return self::issetAndNotEmpty($array) && is_array($array) && count($array) > 0;
    }

    /**
     * Vrátí první klíč v poli
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array
     * @return string|int
     */
    public static function arrayGetFirstKey(array $array) {
        foreach ($array as $key => $value) {
            return $key;
        }
    }

    /**
     * Vrátí první hodnotu v poli
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array
     * @return type
     */
    public static function arrayGetFirstValue(array $array) {
        foreach ($array as $key => $value) {
            return $value;
        }
    }

    /**
     * Očistí pole od hodnot "." a ".." při scandiru
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $input
     * @return array
     */
    public static function arrayClearDir($input) {
        foreach ($input as $key => $value) {
            if ($value == '.' || $value == '..') {
                unset($input[$key]);
            }
        }

        return array_values($input);
    }

    // --- DATUMY - DATES ---------------------------

    /**
     * Vrátí aktuální datum a čas v obecném tvaru
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $format
     * @param string $timeStampText
     * @return date
     */
    public static function dateGetNow($format = "Y-m-d H:i:s", $timeStampText = null) {
        if (self::issetAndNotEmpty($timeStampText)) {
            return date($format, strtotime($timeStampText));
        }
        return date($format);
    }

    // --- GENERÁLNÍ FUNKCE ---------------------------

    /**
     * Kontrola na isset a ! empty v "jednom" kroku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed $value
     * @return bool
     */
    public static function issetAndNotEmpty($value) {
        return isset($value) && !empty($value);
    }

    /**
     * Kontrola na ! isset nebo empty v "jednom" kroku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed $value
     * @return bool
     */
    public static function notIssetOrEmpty($value) {
        return !isset($value) || empty($value);
    }

    /**
     * Vypise obsah cehokoliv (pole, objektu, ...)
     * Slouzi jako pomucka pri programovani
     *
     * @author Ing. Martin Dostál
     * @link http://www.dysoft.cz
     *
     * @param mixed $objekt
     * @param string $name
     * @param booelan $return
     * @return string
     */
    public static function pr($objekt, $name = '', $return = false) {
        if (is_string($objekt))
            $objekt .= "\n";
        if ($return)
            return '<pre>' . $name . (print_r($objekt, true)) . '</pre>';
        echo '<pre>' . $name . ' ' . (print_r($objekt, true)) . '</pre>';
    }

    /**
     * Funkce vrátí post object na základě předaného parametru post_id = null
     *
     * @author Tomáš Kocifaj
     * @link www.ktstudio.cz
     *
     * @global object WP_Post $post - post object: Použitelný pouze v loopě
     * @param int $post_id - Id postu, který se nastaveí
     * @return ojbect WP_post
     */
    public static function setupPostObject($post_id = null) {
        if (self::isIdFormat($post_id)) {
            if (!$post_id instanceof WP_Post) {
                return get_post($post_id);
            }
            $post = $post_id;
        } else {
            global $post;
        }

        return $post;
    }

    /**
     * Kontrola, zda má aktuální uživatel práva pro "manage_options"
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public static function checkCurrentUserCanManageOptions() {
        if (current_user_can("manage_options")) {
            return true;
        } else {
            wp_die(__("Nemáte dostatečná oprávnění k přístupu na tuto stránku.", KT_DOMAIN));
            return false;
        }
    }

    /**
     * Na základě zadaných parametrů vrátí řetezec pro programové odsazení tabulátorů s případnými novými řádky
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param integer $tabsCount
     * @param string $content
     * @param boolean $newLineBefore
     * @param boolean $newLineAfter
     * @return string
     */
    public static function getTabsIndent($tabsCount, $content = null, $newLineBefore = false, $newLineAfter = false) {
        $result = "";
        if ($newLineBefore == true) {
            $result .= "\n";
        }
        $result .= str_repeat("\t", $tabsCount);
        if (self::issetAndNotEmpty($content)) {
            $result .= $content;
        }
        if ($newLineAfter == true) {
            $result .= "\n";
        }
        return $result;
    }

    /**
     * Na základě zadaných parametrů vypíše řetezec pro programové odsazení tabulátorů s případnými novými řádky
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param integer $tabsCount
     * @param string $content
     * @param boolean $newLineBefore
     * @param boolean $newLineAfter
     * @return string
     */
    public static function theTabsIndent($tabsCount, $content = null, $newLineBefore = false, $newLineAfter = false) {
        echo self::getTabsIndent($tabsCount, $content, $newLineBefore, $newLineAfter);
    }

    /**
     * Vrátí aktuální URL na základě nastavení APACHE HTTP_HOST a REQUEST_URI
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param bool $fullUrl - true i s pametry, false bez
     * @return string
     */
    public static function getRequestUrl($fullUrl = true) {
        $requestUrl = "http";
        if ($_SERVER["HTTPS"] == "on") {
            $requestUrl .= "s";
        }
        $requestUrl .= "://";
        $serverPort = $_SERVER["SERVER_PORT"];
        $serverName = $_SERVER["SERVER_NAME"];
        $serverUri = ($fullUrl) ? $_SERVER["REQUEST_URI"] : $_SERVER["REDIRECT_URL"];
        if ($serverPort != "80") {
            $requestUrl .= "$serverName:$serverPort$serverUri";
        } else {
            $requestUrl .= "$serverName$serverUri";
        }
        return $requestUrl;
    }

    /**
     * Vrátí buď zpětnou URL z serverového pole anebo home URL
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public static function getBacklinkUrl() {
        $refererUrl = $_SERVER['HTTP_REFERER'];
        if (filter_var($refererUrl, FILTER_VALIDATE_URL)) {
            return $refererUrl;
        }
        return get_home_url();
    }

    /**
     * Na základě zadané adresy vrátí GPS souřadnice pomocí Google API pokud je možné
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $address
     * @return string|null
     */
    public static function getGoogleMapsGPS($address) {
        if (self::issetAndNotEmpty($address) && is_string($address)) {
            $address = urlencode(trim($address));
            $googleApiLink = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";
            $googleApiResult = file_get_contents($googleApiLink);
            if ($googleApiResult) {
                $googleApiResultJson = json_decode($googleApiResult);
                if (self::issetAndNotEmpty($googleApiResultJson)) {
                    $gpsLatitude = (float) $googleApiResultJson->results[0]->geometry->location->lat;
                    $gpsLongtitude = (float) $googleApiResultJson->results[0]->geometry->location->lng;
                    if (self::issetAndNotEmpty($gpsLatitude) && self::issetAndNotEmpty($gpsLongtitude)) {
                        $coordinates = $gpsLatitude . ", " . $gpsLongtitude;
                        return $coordinates;
                    }
                }
            }
        }
        return null;
    }

    // --- OBRÁZKY - IMAGE ---------------------------

    /**
     * Vypíše obrázek podle ID  v případné požadované velikosti
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $id
     * @param string $alt
     * @param string $size
     */
    public static function imageGetAttachmentHtml($id, array $linkArgs = array(), array $imageArgs = array(), $size = KT_WP_IMAGE_SIZE_THUBNAIL, $tabsCount = 0) {
        $output = null;
        if (self::isIdFormat($id) > 0) {
            $source = wp_get_attachment_image_src($id, $size);
            if (self::arrayIssetAndNotEmpty($source)) {
                $imageUrl = $linkUrl = $source[0];
                $imageWidth = $source[1];
                $imageHeight = $source[2];
                if ($size !== KT_WP_IMAGE_SIZE_ORIGINAL) {
                    $original = wp_get_attachment_image_src($id, KT_WP_IMAGE_SIZE_ORIGINAL);
                    $linkUrl = $original[0];
                }
                foreach ($linkArgs as $key => $value) {
                    $linkAttributes .= " $key=\"$value\"";
                }
                foreach ($imageArgs as $key => $value) {
                    $imageAttributes .= " $key=\"$value\"";
                }
                $output .= self::getTabsIndent($tabsCount, "<a href=\"$linkUrl\"$linkAttributes>", true);
                $output .= self::getTabsIndent($tabsCount + 1, "<img src=\"$imageUrl\" width=\"$imageWidth\" height=\"$imageHeight\"$imageAttributes />", true);
                $output .= self::getTabsIndent($tabsCount, "</a>", true, true);
            }
        }
        return $output;
    }

    /**
     * Vrátí odkaz na obrázek, který je ve složce images v rootu šablony
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $fileName
     * @return string
     */
    public static function imageGetUrlFromTheme($fileName) {
        return $url = get_template_directory_uri() . "/images/" . $fileName;
    }

    /**
     * Nahrazení všech datových zdrojů tagů obrázků v zadaném HTML kódu za lazy (na základě skriptu unveil)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $html
     * @return string
     */
    public static function imageReplaceLazySrc($html) {
        if (self::issetAndNotEmpty($html)) {
            $libxmlInternalErrorsState = libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($html);
            $imageTags = $dom->getElementsByTagName("img");
            $processedImages = array();
            foreach ($imageTags as $imageTag) {
                $oldSrc = $imageTag->getAttribute("src");
                if (in_array($oldSrc, $processedImages)) {
                    continue; // tento obrázek byl již zpracován
                }
                array_push($processedImages, $oldSrc);
                $newSrc = KT_CORE_IMAGES_URL . "/transparent.png";
                $html = str_replace("src=\"$oldSrc\"", "src=\"$newSrc\" data-src=\"$oldSrc\"", $html);
            }
            libxml_clear_errors();
            libxml_use_internal_errors($libxmlInternalErrorsState);
        }
        return $html;
    }

    // --- MENU ---------------------------

    /**
     * Vrátí uživatelský titulek menu podle jeho lokace nebo zadaný vlastní - výchozí
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $location
     * @param string $defaultTitle
     * @return string
     */
    public static function getCustomMenuNameByLocation($location, $defaultTitle = null) {
        $locations = get_nav_menu_locations();
        $menuLocation = $locations[$location];
        if (self::issetAndNotEmpty($menuLocation)) {
            $menuObject = wp_get_nav_menu_object($menuLocation);
            if (self::issetAndNotEmpty($menuObject)) {
                $menuName = $menuObject->name;
                if (self::issetAndNotEmpty($menuName)) {
                    return esc_html($menuName);
                }
            }
        }
        return $defaultTitle;
    }

    /**
     * Vypíše požadované menu bez "obalujícího" divu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $themeLocation
     * @param int $depth
     */
    public static function theWpNavMenu($themeLocation, $depth = 0) {
        wp_nav_menu(array(
            "theme_location" => $themeLocation,
            "container" => false,
            "depth" => $depth,
            "items_wrap" => '%3$s',
            "fallback_cb" => false
        ));
    }

    // --- NUMBERS - ČÍSLA ---------------------------

    /**
     * Prověří, zda zadaný parametr je ve formátu pro ID v databázi
     * Je: Setnutý, není prázdný a je větší než 0
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isIdFormat($value) {
        $id = self::tryGetInt($value);
        if (self::issetAndNotEmpty($id) && $id > 0) {
            return true;
        }
        return false;
    }

    /**
     * Kontrola hodnoty, jestli je číselného typu, resp. int a případné přetypování nebo rovnou návrat, jinak null
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param number $value
     * @return integer|null
     */
    public static function tryGetInt($value) {
        if (self::issetAndNotEmpty($value) && is_numeric($value)) {
            if (is_int($value)) {
                return $value;
            }
            return (int) $value;
        }
        if ($value === "0" || $value === 0) {
            return (int) 0;
        }
        return null;
    }

    /**
     * Kontrola hodnoty, jestli je číselného typu, resp. float a případné přetypování nebo rovnou návrat, jinak null
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param number $value
     * @return float|null
     */
    public static function tryGetFloat($value) {
        if (self::issetAndNotEmpty($value) && is_numeric($value)) {
            if (is_float($value)) {
                return $value;
            }
            return (float) $value;
        }
        if ($value === "0" || $value === 0) {
            return (float) 0;
        }
        return null;
    }

    /**
     * Kontrola hodnoty, jestli je číselného typu, resp. double a případné přetypování nebo rovnou návrat, jinak null
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param number $value
     * @return double|null
     */
    public static function tryGetDouble($value) {
        if (self::issetAndNotEmpty($value) && is_numeric($value)) {
            if (is_double($value)) {
                return $value;
            }
            return (double) $value;
        }
        if ($value === "0" || $value === 0) {
            return (double) 0;
        }
        return null;
    }

    /**
     * Vzájemné porovnání (celočíselných) hodnot
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $a
     * @param int $b
     * @return int
     */
    public static function intCompare($a, $b) {
        $first = self::tryGetInt($a);
        $second = self::tryGetInt($b);
        if ($first == $second) {
            return 0;
        } else if ($first > $second) {
            return 1;
        } else if ($first < $second) {
            return -1;
        }
        return null;
    }

    /**
     * Obecné zaokrouhlení podle celých nebo destinných čísel
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param number $value
     * @return number
     */
    public static function roundNumber($value) {
        if ((self::issetAndNotEmpty($value) && is_numeric($value) || $value === "0")) {
            if (is_int($value)) {
                return round($value, 0, PHP_ROUND_HALF_UP);
            } else {
                return round($value, 2, PHP_ROUND_HALF_UP);
            }
        }
        return $value;
    }

    // --- LOGICKÉ HODNOTY ---------------------------

    /**
     * Kontrola hodnoty, jestli jde o logickou hodnotu, resp. bool a případné přetypování nebo rovnou návrat, jinak null
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param number $value
     * @return integer|null
     */
    public static function tryGetBool($value) {
        if (KT_ITH::issetAndNotEmpty($value)) {
            if (is_bool($value)) {
                return $value;
            }
            return (bool) $value;
        }
        strtolower((string) $value);
        if ($text === "1" || $text === "true" || $text === "ano" || $text === "yes") {
            return false;
        }
        if ($text === "0" || $text === "false" || $text === "ne" || $text === "no") {
            return false;
        }
        return null;
    }

    // --- STRÁNKOVÁNÍ ---------------------------

    /**
     * Vypíše stránkování určené pro WP loopu v bootstrap stylu
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @global integer $paged
     * @global WP_Query $wp_query
     * @param boolean $previousNext
     * @param string $customClass
     */
    public static function bootstrapPagination($previousNext = true, $customClass = "pagination-centered") {
        global $paged;
        $paged = self::tryGetInt($paged) ? : 1;
        if (self::issetAndNotEmpty($paged) && $paged > 0) {
            global $wp_query;
            $pages = self::tryGetInt($wp_query->max_num_pages);
            if (self::issetAndNotEmpty($pages) && $pages > 1 && $paged >= $paged) {
                self::theTabsIndent(0, "<ul class=\"pagination $customClass\">", true);

                if ($previousNext) {
                    $firstClass = $paged > 2 ? "" : 'class="disabled"';
                    self::theTabsIndent(1, "<li $firstClass><a href='" . get_pagenum_link(1) . "'>&laquo;</a></li>", true);
                    $secondClass = $paged > 1 ? "" : 'class="disabled"';
                    self::theTabsIndent(1, "<li $secondClass><a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a></li>", true);
                }

                for ($i = 1; $i <= $pages; $i ++) {
                    $pagenumlink = get_pagenum_link($i);
                    $activeClass = ($i == $paged) ? 'class="active"' : "";
                    self::theTabsIndent(1, "<li $activeClass><a href=\"$pagenumlink\">$i</a></li>", true);
                }

                if ($previousNext) {
                    $penultimateClass = $paged < $pages ? "" : 'class="disabled"';
                    self::theTabsIndent(1, "<li $penultimateClass><a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a></li>", true);
                    $latestClass = $paged < $pages - 1 ? "" : 'class="disabled"';
                    self::theTabsIndent(1, "<li $latestClass><a href='" . get_pagenum_link($pages) . "'>&raquo;</a></li>", true);
                }

                self::theTabsIndent(0, "</div>", true, true);
            }
        }
    }

    // -- STRING - Textové řetězce ------------------

    /**
     * Kontrola, zda první zadaný textový řetezec někde uvnitř sebe obsahuje ten druhý zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $string řetězec k prohledání
     * @param string $substring hledaný podřetězec
     * @return boolean true, pokud $substring se nachází v $string, jinak false
     */
    public static function stringContains($string, $substring) {
        $position = strpos($string, $substring);
        if ($position === false) {
            return false;
        }
        return true;
    }

    /**
     * Kontrola, zda první zadaný textový řetezec obsahuje na svém konci ten druhý zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $string
     * @param string $ending
     * @return boolean
     */
    public static function stringEndsWith($string, $ending) {
        $length = strlen($ending);
        $string_end = substr($string, strlen($string) - $length);
        return $string_end === $ending;
    }

    /**
     * Kontrola, zda první zadaný textový řetezec obsahuje na svém začátku ten druhý zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $string
     * @param string $starting
     * @return boolean
     */
    public static function stringStartsWith($string, $starting) {
        $length = strlen($starting);
        return (substr($string, 0, $length) === $starting);
    }

    /**
     * Odstranění html ze zadaného textu + převod speciálních znaků
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @return string
     */
    public static function stringClearHtml($text) {
        return htmlspecialchars(strip_tags($text));
    }

    /**
     * Ořízně zadaný řetezec, pokud je delší než požadovaná maximální délka včetně případné přípony
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @param int $maxLength
     * @param string $suffix
     * @return string
     */
    public static function stringCrop($text, $maxLength, $suffix = "...") {
        $maxLength = self::tryGetInt($maxLength);
        $currentLength = strlen($text);
        if ($maxLength > 0 && $currentLength > $maxLength) {
            $text = strip_tags($text);
            $text = mb_substr($text, 0, $maxLength);
            $text .= $suffix;
        }
        return $text;
    }

    // --- TEMPLATE LOAD ---------------------------

    /**
     * Funkce vrátí single templatu ze subdir - singles
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Post $post
     * @return string - template path
     */
    public static function getSingleTemplate(WP_Post $post) {
        $file = TEMPLATEPATH . '/singles/single-' . $post->post_type . '.php';
        if ($post->post_type != 'post') {
            if (file_exists($file)) {
                return $file;
            }
        }

        $file = TEMPLATEPATH . '/singles/single.php';
        if (file_exists($file)) {
            return $file;
        }


        return false;
    }

    /**
     * Funkce vrátí attachment template pro detail samotného obrázku
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Post $post
     * @return string|boolean - template path
     */
    public static function getAttachmentTemplate() {
        $file = TEMPLATEPATH . '/singles/single-attachment.php';
        if (file_exists($file)) {
            return $file;
        }

        $file = TEMPLATEPATH . '/singles/attachment.php';
        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Funkce vrátí page templatu ze subdir - pages
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Post $post
     * @return string - template path
     */
    public static function getPageTemplate(WP_Post $post) {
        $page_template = get_post_meta($post->ID, KT_WP_META_KEY_PAGE_TEMPLATE, true);

        if ($page_template != 'default' && $page_template != '') {
            $file = TEMPLATEPATH . '/' . $page_template;
            if (file_exists($file)) {
                return $file;
            }
        } else {
            $file = TEMPLATEPATH . '/pages/page.php';
            if (file_exists($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * Funkce vrátí archive templatu ze subdir - archives
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Post $post
     * @return string - template path
     */
    public static function getArchiveTemplate() {
        global $wp_query;
        $file = TEMPLATEPATH . '/archives/archive-' . $wp_query->query_vars['post_type'] . '.php';
        if (file_exists($file)) {
            return $file;
        }

        $file = TEMPLATEPATH . '/archives/archive.php';
        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Funkce vrátí category templatu ze subdir - categories
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $cat = slug zobrazované category
     * @return string - template path
     */
    public static function getCategoryTemplate($cat) {
        $file = TEMPLATEPATH . '/categories/category-' . $cat . '.php';
        if (file_exists($file)) {
            return $file;
        }
        $category = get_category($cat);

        $file = TEMPLATEPATH . '/categories/category-' . $category->slug . '.php';
        if (file_exists($file)) {
            return $file;
        }

        $file = TEMPLATEPATH . '/categories/category.php';
        if (file_exists($file)) {
            return $file;
        }

        return false;
    }

    /**
     * Funkce vrátí taxonomy templatu ze subdir - taxonomies
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $taxonomy - slug zobrazené taxonomy
     * @return string - template path
     */
    public static function getTaxonomyTemplate($taxonomy) {

        $term = get_queried_object();

        $file = TEMPLATEPATH . "/taxonomies/taxonomy-" . $taxonomy . "-" . $term->slug . ".php";
        if (file_exists($file)) {
            return $file;
        }

        $file = TEMPLATEPATH . "/taxonomies/taxonomy-" . $taxonomy . "-" . $term->term_id . ".php";
        if (file_exists($file)) {
            return $file;
        }

        $file = TEMPLATEPATH . '/taxonomies/taxonomy-' . $taxonomy . '.php';
        if (file_exists($file)) {
            return $file;
        }

        if (file_exists(TEMPLATEPATH . '/taxonomies/taxonomy.php')) {
            return TEMPLATEPATH . '/taxonomies/taxonomy.php';
        }

        return false;
    }

}
