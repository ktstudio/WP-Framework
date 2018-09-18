<?php

/**
 * Třída nahrazující obecné soubory s funkcemi a klasickým volání pomocí statických metod
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT {

    const CRAWLERS = "Bloglines subscriber|Dumbot|Sosoimagespider|QihooBot|FAST-WebCrawler|Superdownloads Spiderman|LinkWalker|msnbot|ASPSeek|WebAlta Crawler|Lycos|FeedFetcher-Google|Yahoo|YoudaoBot|AdsBot-Google|Googlebot|Scooter|Gigabot|Charlotte|eStyle|AcioRobot|GeonaBot|msnbot-media|Baidu|CocoCrawler|Google|Charlotte t|Yahoo! Slurp China|Sogou web spider|YodaoBot|MSRBOT|AbachoBOT|Sogou head spider|AltaVista|IDBot|Sosospider|Yahoo! Slurp|Java VM|DotBot|LiteFinder|Yeti|Rambler|Scrubby|Baiduspider|accoona";
    const CHARS = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    private static $dateGmtOffset;

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
            throw new KT_Duplicate_Exception($index);
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
            throw new KT_Duplicate_Exception($index);
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
     * Přidání (přiřazené) hodnoty do zadaného pole, pokud tato hodnota ještě není v poli obsažena
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $haystack
     * @param mixed $value
     * @return array
     */
    public static function arrayAdd(array &$haystack, $value) {
        if (isset($value) && !in_array($value, $haystack)) {
            array_push($haystack, $value);
        }
        return $haystack;
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
     * @param mixed $needle
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
     * @param array $array
     * @return boolean
     */
    public static function arrayIssetAndNotEmpty($array = null) {
        return isset($array) && is_array($array) && count($array) > 0;
    }

    /**
     * Kontrola, zda je zadaný řetězec zaserializované pole
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $string
     * @return boolean
     */
    public static function arrayIsSerialized($string) {
        if (KT::issetAndNotEmpty($string) && is_string($string)) {
            return (@unserialize($string) !== false || $string == "b:0;");
        }
        return false;
    }

    /**
     * Vrátí pole na základě hodnoty zadaného parametru, pokud je to možné
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $paramName
     * @param char $delimiter
     * @return array
     */
    public static function arrayFromUrlParam($paramName, $delimiter = ",") {
        $paramValue = filter_input(INPUT_GET, $paramName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (KT::issetAndNotEmpty($paramValue)) {
            if (is_serialized($paramValue)) {
                return unserialize($paramValue);
            }
            return explode($delimiter, $paramValue);
        }
        return array();
    }

    /**
     * Vrátí pole IDs na základě hodnoty zadaného parametru, pokud je to možné
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $idsParamName
     * @param char $delimiter
     * @return array
     */
    public static function arrayIdsFromUrlParam($idsParamName, $delimiter = ",") {
        $ids = array();
        $values = self::arrayFromUrlParam($idsParamName, $delimiter);
        if (KT::arrayIssetAndNotEmpty($values)) {
            foreach ($values as $value) {
                if (KT::isIdFormat($value)) {
                    array_push($ids, $value);
                }
            }
        }
        return $ids;
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

    public static function arrayGetLastValue(array $array) {
        foreach ($array as $key => $value)
            ;
        return $value;
    }

    public static function arrayGetLastKey(array $array) {
        foreach ($array as $key => $value)
            ;
        return $key;
    }

    /**
     * Vrátí hodnotu pro zadaný klíč pokud existuje nebo výchozí zadanou hodnotu (NULL)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $array
     * @param string $key
     * @param string $defaultValue
     * @return mixed type|null
     */
    public static function arrayTryGetValue(array $array, $key, $defaultValue = null) {
        if (isset($key)) {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            }
        }
        return $defaultValue;
    }

    /**
     * Vrátí hodnotu z objektu, který je pole pro zadaný klíč pokud existuje nebo výchozí zadanou hodnotu (NULL)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $array
     * @param string $key
     * @param string $defaultValue
     * @return mixed type|null
     */
    public static function arrayObjectTryGetValue($array, $key, $defaultValue = null) {
        if (isset($array) && is_array($array)) {
            return self::arrayTryGetValue($array, $key, $defaultValue );
        }
        return $defaultValue;
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

    /**
     * Rozdělí zadané pole na požadovaný počet částí s přibližně stejným počtem záznamů
     * 
     * @author Sebastiaan de Jonge
     * @link http://blog.sebastiaandejonge.com/articles/2010/december/28/php-dividing-an-array-into-equal-pieces/
     * 
     * @param array $items
     * @param int $segmentsCount
     * @param boolean $preserveKeys
     * @return array
     */
    public static function arrayDivide(array $items, $segmentsCount, $preserveKeys = true) {
        $itemsCount = count($items);
        if (($itemsCount === 0) || ($segmentsCount < 1)) {
            return null;
        }
        $segmentLimit = ceil($itemsCount / $segmentsCount);
        $segments = array_chunk($items, $segmentLimit, $preserveKeys);
        return $segments;
    }

    // --- DATUMY - DATES ---------------------------

    /**
     * Vrátí aktuální datum a čas v obecném tvaru
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $format
     * @return date
     */
    public static function dateNow($format = "Y-m-d H:i:s") {
        return date($format, current_time("timestamp"));
    }

    /**
     * Převede zadaný datum na požadovaný (nový) formát
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $value (datum)
     * @param string $format
     * @param boolean $withGmt
     * @return string (datum)
     */
    public static function dateConvert($value, $format = "d.m.Y", $withGmt = false) {
        if (KT::issetAndNotEmpty($value)) {
            $timeStamp = strtotime($value);
            if ($withGmt) {
                $timeStamp += (self::dateGmtOffset() * HOUR_IN_SECONDS);
            }
            return date($format, $timeStamp);
        }
        return null;
    }

    /**
     * Vrátí časové pásmo, resp. časovou zónu zadanou v administraci (cachovanou per request)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public static function dateGmtOffset() {
        $dateGmtOffset = self::$dateGmtOffset;
        if (isset($dateGmtOffset)) {
            return $dateGmtOffset;
        }
        return self::$dateGmtOffset = KT::tryGetInt(get_option("gmt_offset"));
    }

    // --- GENERÁLNÍ FUNKCE ---------------------------

    /**
     * Kontrola na isset a ! empty v "jednom" kroku
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed $value
     * @return boolean
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
     * @return boolean
     */
    public static function notIssetOrEmpty($value) {
        return !isset($value) || empty($value);
    }

    /**
     * Kontrola, zda je právě prováděn WP ajax (na základě konstanty DOING_AJAX)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public static function isWpAjax() {
        return defined("DOING_AJAX") && DOING_AJAX;
    }

    /**
     * Odhad zda se provadí ajax
     * 
     * @author Jan Pokorný
     * @return bool
     */
    public static function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
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
	 * Die Dump (pr)
	 *
	 * @author Martin Hlaváč
	 * @link http://www.ktstudio.cz
	 *
	 * @param mixed $value
	 */
	public static function dd($value) {
		wp_die(var_dump(self::pr($value)));
		exit;
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
            wp_die(__("You do not have sufficient privileges to access this page.", "KT_CORE_DOMAIN"));
            return false;
        }
    }

    /**
     * Základní kontrola, zda je zadaný user agent (ze serverového pole) znamý robot
     * 
     * @author Jay Paroline, Mike
     * @link http://wanderr.com/jay/detect-crawlers-with-php-faster/2009/04/08/
     * 
     * @param string $userAgent
     * @return boolean
     */
    public static function checkIsCrawler($userAgent) {
        $crawlers = self::CRAWLERS;
        $isCrawler = (preg_match("/$crawlers/i", $userAgent) > 0); // i - case-insensitive
        return $isCrawler;
    }

    /**
     * Vrátí náhodný řetezec poskládaný s malých a velkých písmen a čísel zadané délky
     * Pozn.: vhodné pro jednoduché kódy nebo hesla...
     * 
     * @author BSQ <http://stackoverflow.com/users/1008675/bsq>
     * @link http://stackoverflow.com/a/12210409
     * 
     * @param int $length
     * @return string
     */
    public static function getRandomString($length = 6) {
        return substr(str_shuffle(self::CHARS), 0, $length);
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
     * @param boolean $fullUrl - true i s pametry, false bez
     * @return string
     */
    public static function getRequestUrl($fullUrl = true) {
        $requestUrl = "http";
        if (self::arrayTryGetValue($_SERVER, "HTTPS") == "on") {
            $requestUrl .= "s";
        }
        $requestUrl .= "://";
        $serverPort = $_SERVER["SERVER_PORT"];
        $serverName = $_SERVER["SERVER_NAME"];
        $httpHost = $_SERVER["HTTP_HOST"];
        $serverKey = (self::stringEndsWith($httpHost, $serverName)) ? $httpHost : $serverName;
        $serverUri = ($fullUrl) ? $_SERVER["REQUEST_URI"] : $_SERVER["REDIRECT_URL"];
        if ($serverPort == "80" || $serverPort == "443") {
            $requestUrl .= "{$serverKey}{$serverUri}";
        } else {
            $requestUrl .= "{$serverKey}:{$serverPort}{$serverUri}";
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
        $refererUrl = $_SERVER["HTTP_REFERER"];
        if (filter_var($refererUrl, FILTER_VALIDATE_URL)) {
            return $refererUrl;
        }
        return get_home_url();
    }

    /**
     * Vrátí hodnotu zadaného URL paramteru pokud existuje
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $key
     * @return mixed string|null
     */
    public static function getUrlParamValue($key) {
        if (KT::arrayIssetAndNotEmpty($_GET) && array_key_exists($key, $_GET)) {
            return $_GET[$key];
        }
        return null;
    }

    /**
     * Vrátí hodnotu pro zadanou URL dle wp_remote_get pokud existuje nebo výchozí zadanou hodnotu (NULL)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $url
     * @param string $defaultValue
     * @return mixed type|null
     */
    public static function tryGetWpRemote($url, $defaultValue = null) {
        $response = wp_remote_get( "$url/" );
        if (KT::arrayIssetAndNotEmpty($response)) {
            if ($response["response"]["message"] === "OK") {
                return json_decode($response["body"]);
            }
        }
        return $defaultValue;
    }

    /**
     * Kontrola, zda je právě aktivní localhost (na základě SERVER - REMOTE_ADDR)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $customWhitelist
     * @return boolean
     */
    public static function isLocalhost($customWhitelist = null) {
        $whitelist = array("127.0.0.1", "::1");
        if (KT::arrayIssetAndNotEmpty($customWhitelist)) {
            $whitelist = array_merge($whitelist, $customWhitelist);
        }
        $ip = self::getIpAddress();
        if (in_array($ip, $whitelist)) {
            return true;
        }
        return false;
    }

    /**
     * Vrátí (aktuální) IP adresu z pole $_SERVER
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public static function getIpAddress() {
        $ip = self::arrayTryGetValue($_SERVER, "HTTP_CLIENT_IP")
                ? : self::arrayTryGetValue($_SERVER, "HTTP_X_FORWARDED_FOR")
                ? : self::arrayTryGetValue($_SERVER, "REMOTE_ADDR");
        return $ip;
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

    /**
     * Převede zadnou hodnotu na des. číslo pro formát GPS, pokud je to možné
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed float|int|string $coordinate
     * @return number
     */
    public static function clearGpsNumberCoordinate($coordinate) {
        if (KT::issetAndNotEmpty($coordinate)) {
            $coordinateNumber = KT::tryGetFloat(preg_replace("/[^0-9,.\/-\/+]/", "", trim($coordinate)));
            return number_format($coordinateNumber, 6, ".", "");
        }
        return null;
    }

    /**
     * Vyčistí zadané souřadnice o nevhodné znaky, tj. nechá jen číslice, tečky a čárky
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $coordinates
     * @return string
     */
    public static function clearGpsCoordinates($coordinates) {
        if (KT::issetAndNotEmpty($coordinates)) {
            return preg_replace("/[^0-9,.\/-\/+]/", "", trim($coordinates));
        }
        return null;
    }

    // --- MULTI SITE(S) ---------------------------

    /**
     * Přepne multi situ dle zadaného (blog) ID a vyvolá callback funkci, jejíž výsledek následně vrátí
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int $blogId
     * @param callable $callback
     * @return mixed
     */
    public static function tryGetWpSiteData($blogId, callable $callback) {
        switch_to_blog($blogId);
        $result = call_user_func($callback);
        restore_current_blog();
        return $result;
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
                if ($size !== KT_WP_IMAGE_SIZE_LARGE) {
                    $large = wp_get_attachment_image_src($id, KT_WP_IMAGE_SIZE_LARGE);
                    $linkUrl = $large[0];
                }

                // Defaultní atributy                
                $title = get_the_title($id);
                $defualtLinkArgs = ["title" => $title];
                $defualtImgArgs = ["alt" => $title];
                $linkArgs = array_merge($defualtLinkArgs, $linkArgs);
                $imageArgs = array_merge($defualtImgArgs, $imageArgs);
                //*******

                $linkAttributes = "";
                foreach ($linkArgs as $key => $value) {
                    $linkAttributes .= " $key=\"$value\"";
                }

                $output .= self::getTabsIndent($tabsCount, "<a href=\"$linkUrl\"$linkAttributes>", true);
                $output .= self::getTabsIndent($tabsCount + 1, self::imageGetHtml($imageUrl, $imageWidth, $imageHeight, $imageArgs));
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
     * @param bool $withSrcset
     * @return string
     */
    public static function imageReplaceLazySrc($html, $withSrcset = false) {
        if (self::issetAndNotEmpty($html) && !KT::isAjax()) { // @todo možnost provádět i při ajaxu, avšak je třeba dodělat javascript trigger
            $libxmlInternalErrorsState = libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($html);
            $imageTags = $dom->getElementsByTagName("img");
            $keys = ["src" => "data-src"];
            $processedImages = ["src" => []];
            if ($withSrcset) {
                $keys["srcset"] = "data-srcset";
                $processedImages["srcset"] = [];
            }
            $newSource = self::imageGetTransparent();
            foreach ($imageTags as $imageTag) {
                foreach ($keys as $key => $lazyKey) {
                    $oldSource = $imageTag->getAttribute($key);
                    if (empty($oldSource) || in_array($oldSource, $processedImages[$key])) {
                        continue; // tento obrázek byl již zpracován anebo je prázdný
                    }
                    array_push($processedImages[$key], $oldSource);
                    if ($oldSource !== $newSource) {
                        $html = str_replace("$key=\"$oldSource\"", "$key=\"$newSource\" $lazyKey=\"$oldSource\"", $html);
                    }
                }
            }
            libxml_clear_errors();
            libxml_use_internal_errors($libxmlInternalErrorsState);
        }
        return $html;
    }

    /**
     * Vrátí průhledný ("systémový") obrázek (včetně URL)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public static function imageGetTransparent() {
        return "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"; // return KT_CORE_IMAGES_URL . "/transparent.png";
    }

    /**
     * Vratí html tag img připravený na lazy load
     * 
     * @author Jan Pokorný
     * @deprecated
     * @see KT::imageGetHtmlByUrl()
     * @see KT::imageGetHtmlByFileName()
     * @param string $file URL nebo cesta k souboru ve složce images
     * @param int $width Šířka obrázku
     * @param int $height Výška obrázku
     * @param array $attrs Další html atributy
     * 
     */
    public static function imageGetHtml($file, $width, $height, array $attrs = null) {
        if (filter_var($file, FILTER_VALIDATE_URL) === false) {
            $fileUrl = KT::imageGetUrlFromTheme($file);
        } else {
            $fileUrl = $file;
        }
        $htmlAttrs = "";
        if ($attrs) {
            foreach ($attrs as $param => $value) {
                $htmlAttrs .= sprintf(' %s="%s"', $param, $value);
            }
        }
        $html = sprintf('<img src="%s" width="%d" height="%d" %s />', $fileUrl, $width, $height, $htmlAttrs);
        return apply_filters("kt_image_prepare_lazyload", $html);
    }

    /**
     * Vratí html tag img připravený na lazy load
     * 
     * @author Jan Pokorný
     * @param string $url URL adresa obrázku
     * @param int $width Šířka obrázku
     * @param int $height Výška obrázku
     * @param array $attrs Další html atributy
     * 
     */
    public static function imageGetHtmlByUrl($url, $width, $height, array $attrs = []) {
        $attrs = array_merge($attrs, ["width" => $width, "height" => $height]);
        $htmlAttrs = "";
        foreach ($attrs as $param => $value) {
            $htmlAttrs .= sprintf(' %s="%s"', $param, esc_attr($value));
        }

        $html = sprintf('<img src="%s"%s />', esc_url($url), $htmlAttrs);
        return apply_filters("kt_image_prepare_lazyload", $html);
    }

    /**
     * Vratí html tag img připravený na lazy load
     * 
     * @author Jan Pokorný
     * @param string $fileName path themes/{your-theme}/images/{$fileName}
     * @param int $width Šířka obrázku
     * @param int $height Výška obrázku
     * @param array $attrs Další html atributy
     * 
     */
    public static function imageGetHtmlByFileName($fileName, $width, $height, array $attrs = []) {
        return self::imageGetHtmlByUrl(self::imageGetUrlFromTheme($fileName), $width, $height, $attrs);
    }

    /**
     * Dekorátor pro funkci wp_get_attachment_image. Přidán lazyload
     * 
     * @see wp_get_attachment_image()
     * @author Jan Pokorný
     * @param int $attachment_id
     * @param string $size
     * @param bools $icon
     * @param array $attr
     * @return string HTML <img>
     */
    public static function imageGetHtmlByAttachmentId($attachment_id, $size = 'thumbnail', $icon = false, $attr = []) {
        $html = wp_get_attachment_image($attachment_id, $size, $icon, $attr);
        return apply_filters("kt_image_prepare_lazyload", $html);
    }

    /**
     * Vytvoří set pro html tag picture
     * 
     * @author Jan Pokorný
     * @param WP_Post $post Attachment
     * @param string $defaultSize Wordpress velikost obrázku pro <img>
     * @param int $width Šířka - nutné pro lazyload
     * @param int $height Výška - nutné pro lazyload
     * @param array min-width => wordpress velikost - 1024 => KT_IMG_SIZE_SLIDER
     * @param array $imgAttrs Attributy pro img tag atribute => hodnota
     * @return string Kolekce tagů <img> x * <source>
     */
    public static function imageGetPictureSet(WP_Post $post, $defaultSize, $width, $height, $sizes = [], $imgAttrs = []) {
        $picture = "";
        foreach ($sizes as $minWidth => $size) {
            $picture .= sprintf('<source srcset="%s" media="(min-width:%spx)">', wp_get_attachment_image_url($post->ID, $size), $minWidth);
        }
        $imgAttrs = array_merge(["alt" => $post->post_title], $imgAttrs);
        $picture .= KT::imageGetHtmlByUrl(wp_get_attachment_image_url($post->ID, $defaultSize), $width, $height, $imgAttrs);
        return $picture;
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
        $menuLocation = KT::arrayTryGetValue($locations, $location);
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
     * Vrátí (term) ID menu podle jeho lokace nebo null
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $location
     *
     * @return string
     */
    public static function getCustomMenuIdByLocation($location) {
        $locations = get_nav_menu_locations();
        $menuLocation = $locations[$location];
        if (self::issetAndNotEmpty($menuLocation)) {
            $menuObject = wp_get_nav_menu_object($menuLocation);
            if (self::issetAndNotEmpty($menuObject)) {
                $menuId = $menuObject->term_id;
                if (self::isIdFormat($menuId)) {
                    return $menuId;
                }
            }
        }
        return null;
    }

    /**
     * Vypíše požadované menu bez "obalujícího" divu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $themeLocation
     * @param int $depth
     * @param Walker_Nav_Menu $customWalker
     * @param array $customArgs
     */
    public static function theWpNavMenu($themeLocation, $depth = 0, Walker_Nav_Menu $customWalker = null, array $customArgs = null) {
        $defaults = array(
            "theme_location" => $themeLocation,
            "container" => false,
            "depth" => $depth,
            "items_wrap" => '%3$s',
            "fallback_cb" => false,
        );
        if (KT::issetAndNotEmpty($customWalker)) {
            $defaults["walker"] = $customWalker;
        }
        $args = wp_parse_args($customArgs, $defaults);
        wp_nav_menu($args);
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
        if ($id > 0) {
            return true;
        }
        return false;
    }

    /**
     * Prověří, zda zadaný parametr je ve formátu pro index v poli apod.
     * Je: Setnutý, není prázdný a je větší nebo roven 0
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param mixed $value
     * @return boolean
     */
    public static function isIndexFormat($value) {
	    $index = self::tryGetInt($value);
        if ($index === 0 || $index > 0) {
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
        if (isset($value) && is_numeric($value)) {
            if (is_int($value)) {
                return $value;
            }
            return (int) $value;
        }
        if ($value === "0") {
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
        if (isset($value) && is_numeric($value)) {
            if (is_float($value)) {
                return $value;
            }
            return (float) $value;
        }
        if ($value === "0") {
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
        if (isset($value) && is_numeric($value)) {
            if (is_double($value)) {
                return $value;
            }
            return (double) $value;
        }
        if ($value === "0") {
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

    /**
     * Vyčistí zadané telefonní číslo + nahradí za 00 a ostatní zbytečné znaky odstraní
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed string|int $phoneNumber
     * @return string
     */
    public static function clearPhoneNumber($phoneNumber) {
        if (KT::issetAndNotEmpty($phoneNumber)) {
            $before = ["+", "(", ")", " "];
            $after = ["00", "", "", ""];
            return $phoneNumber = str_replace($before, $after, $phoneNumber);
        }
        return null;
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
        if (isset($value)) {
            if (is_bool($value)) {
                return $value;
            }
            switch (strtolower($value)) {
                case "1":
                case "true":
                case "ano":
                case "yes":
                case "on":
                    return true;
                case "0":
                case "false":
                case "ne":
                case "no":
                case "off":
                    return false;
            }
        }
        return null;
    }

    // --- STRÁNKOVÁNÍ ---------------------------

    /**
     * Vytvoří stránkování (odkazy)
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @global WP_Query $wp_query
     * @param WP_Query $wpQuery
     * @param array $userArgs // pro paginate_links (@link http://codex.wordpress.org/Function_Reference/paginate_links)
     * @return string
     */
    public static function getPaginationLinks(WP_Query $wp_query = null, $userArgs = array()) {
        if (KT::notIssetOrEmpty($wp_query)) {
            global $wp_query;
        }

        $paged = $wp_query->get("paged");

        if (KT::notIssetOrEmpty($paged)) {
            $paged = htmlspecialchars($paged);
        }

        $defaultArgs = array(
            "format" => "page/%#%",
            "current" => max(1, $paged),
            "total" => $wp_query->max_num_pages,
            "prev_text" => __("&laquo; Previous", "KT_CORE_DOMAIN"),
            "next_text" => __("Next &raquo;", "KT_CORE_DOMAIN")
        );

        $argsPagination = wp_parse_args($userArgs, $defaultArgs);
        return paginate_links($argsPagination);
    }

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
                    if ($paged > 2) {
                        self::theTabsIndent(1, "<li><a href='" . get_pagenum_link(1) . "'>&laquo;</a></li>", true);
                    } else {
                        self::theTabsIndent(1, "<li class=\"disabled\"><span>&laquo;</span></li>", true);
                    }
                    if ($paged > 1) {
                        self::theTabsIndent(1, "<li><a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a></li>", true);
                    } else {
                        self::theTabsIndent(1, "<li class=\"disabled\"><span>&lsaquo;</span></li>", true);
                    }
                }

                for ($i = 1; $i <= $pages; $i ++) {
                    $pagenumlink = get_pagenum_link($i);
                    $activeClass = ($i == $paged) ? 'class="active"' : "";
                    self::theTabsIndent(1, "<li $activeClass><a href=\"$pagenumlink\">$i</a></li>", true);
                }

                if ($previousNext) {
                    if ($paged < $pages) {
                        self::theTabsIndent(1, "<li><a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a></li>", true);
                    } else {
                        self::theTabsIndent(1, "<li class=\"disabled\"><span>&rsaquo;</span></li>", true);
                    }
                    if ($paged < ($pages - 1)) {
                        self::theTabsIndent(1, "<li><a href='" . get_pagenum_link($pages) . "'>&raquo;</a></li>", true);
                    } else {
                        self::theTabsIndent(1, "<li class=\"disabled\"><span>&raquo;</span></li>", true);
                    }
                }

                self::theTabsIndent(0, "</ul>", true, true);
            }
        }
    }

    /**
     * Vrátí odkazy předchozího a následujícího článku, pokud jsou k dispozici
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $class
     * @param string $maxLength
     * @param string $separator
     * @param string $taxonomy
     * @param boolean $inSameTerm
     * @param string $excludedTerms
     * @return string
     */
    public static function getPreviousNextPostLinks($class = null, $maxLength = 30, $separator = " | ", $taxonomy = KT_WP_CATEGORY_KEY, $inSameTerm = false, $excludedTerms = "") {
        $links = array();
        $previousPost = get_previous_post($inSameTerm, $excludedTerms, $taxonomy);
        if (KT::issetAndNotEmpty($previousPost)) {
            $previousUrl = get_permalink($previousPost);
            $previousTitle = KT::stringCrop($previousPost->post_title, $maxLength);
            array_push($links, "<a href=\"$previousUrl\" title=\"{$previousPost->post_title}\" class=\"prev $class\">$previousTitle</a>");
        }
        $nextPost = get_next_post($inSameTerm, $excludedTerms, $taxonomy);
        if (KT::issetAndNotEmpty($nextPost)) {
            $nextUrl = get_permalink($nextPost);
            $nextTitle = KT::stringCrop($nextPost->post_title, $maxLength);
            array_push($links, "<a href=\"$nextUrl\" title=\"{$nextPost->post_title}\" class=\"next $class\">$nextTitle</a>");
        }
        if (KT::arrayIssetAndNotEmpty($links)) {
            return implode($separator, $links);
        }
        return null;
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
     * Ořízně zadaný text (řetezec), pokud je delší než požadovaná maximální délka včetně případné přípony
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text - text ke zkrácení
     * @param int $maxLength - požadovaná maxiální délka (ořezu)
     * @param boolean $fromBeginOrEnd - true od začátku, false od konce
     * @param string $suffixPrefix - ukončovácí přípona/předpona navíc (podle parametru $fromBeginOrEnd)
     * @return string
     */
    public static function stringCrop($text, $maxLength, $fromBeginOrEnd = true, $suffixPrefix = "...") {
        $maxLength = self::tryGetInt($maxLength);
        $currentLength = strlen($text);
        if ($maxLength > 0 && $currentLength > $maxLength) {
            if ($fromBeginOrEnd) {
                $text = mb_substr($text, 0, $maxLength) . $suffixPrefix;
            } else {
                $text = $suffixPrefix . mb_substr($text, ($currentLength - $maxLength), $currentLength);
            }
        }
        return $text;
    }

    /**
     * Odstranění všech mezer ze zadaného textu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @return string
     */
    public static function stringRemoveSpaces($text) {
        if (KT::issetAndNotEmpty($text)) {
            return str_replace(" ", "", trim($text));
        }
        return null;
    }

    /**
     * Konverze textu (zpět) do HTML (entit) vč. uvozovek
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @return string
     */
    public static function stringHtmlDecode($text) {
        if (self::issetAndNotEmpty($text)) {
            return html_entity_decode(stripslashes($text), ENT_COMPAT | ENT_HTML401, "UTF-8");
        }
        return $text;
    }

    /**
     * Provede aplikaci (nových HTML) řádků na zadaný text včetně náhrady případných tagů za zastupné
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @param array $tags [$tag => $wildcard]
     * @return string
     */
    public static function stringLineFormat($text, array $tags = array()) {
        if (self::issetAndNotEmpty($text)) {
            foreach ($tags as $tag => $wildcard) {
                $text = str_replace($tag, $wildcard, $text);
            }
            return nl2br($text);
        }
        return null;
    }

    /**
     * Na základě zadaného pole hodnot vrátí odpovídající SQL placeholdery jako string 
     * Pozn. vhodné pro @see WPDB a prepare IN
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $values
     * @param string $placeholder
     * @return string
     */
    public static function stringWpDbPlaceholders(array $values, $placeholder = "s") {
        return implode(",", array_fill(0, count($values), "%{$placeholder}"));
    }

    /**
     * Escapování HTML atribuntů v zadaném textu (+ trim) nebo null
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @return string
     */
    public static function stringEscape($text) {
        if (self::issetAndNotEmpty($text)) {
            return esc_attr(trim($text));
        }
        return null;
    }

    /**
     * Na základě odřádkování rozdělí zadaný text do pole (tzn. po řádcích)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @return string
     */
    public static function textLinesToArray($text) {
        if (KT::issetAndNotEmpty($text)) {
            return explode(PHP_EOL, $text);
        }
        return null;
    }

    /**
     * Na základě odřádkování (tzn. po řádcích) rozdělí zadaný text a vrátí jako HTML seznam zadaného tagu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @param string $tag HTML tag pro jednotlivé řádky
     * @param string $class CSS třída pro jednotlivé HTML tagy
     * @return string (HTML)
     */
    public static function textLinesToHtml($text, $tag, $class = null) {
        $lines = self::textLinesToArray($text);
        if (KT::arrayIssetAndNotEmpty($lines)) {
            $classPart = null;
            if (KT::issetAndNotEmpty($class)) {
                $classPart = " class=\"{$class}\"";
            }
            $output = null;
            foreach ($lines as $line) {
                $output .= "<{$tag}{$classPart}>{$line}</{$tag}>";
            }
            return $output;
        }
        return null;
    }

    /**
     * Provede zvýraznění v textu. Syntaxe převzdata z Markdown.
     * *text* -> kurzíva, **text** -> tučný text, ~~text~~ -> přeškrtnutý text
     *
     * @deprecated use KT_String_Markdown
     * @author Jan Pokorný
     *
     * @param string $text Vstupní text
     * @return string Zvýrazněný výstupní text
     */
    public static function textMarkdownEmphasis($text) {
        $patterns = [
            "/\*\*(.+?)\*\*/i",
            "/\*(.+?)\*/i",
            "/\~\~(.+?)\~\~/i",
        ];
        $replaces = [
            "<b>$1</b>",
            "<i>$1</i>",
            "<del>$1</del>"
        ];
        return preg_replace($patterns, $replaces, $text);
    }

    /**
     * Na zadaný text provede aplikaci MarkDownu, odřádkování a vložení do HTML containeru (výchozí odstavec - <p>)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $text text k aplikaci formátu
     * @param string $containerElement obalující HTML element, výchozí odstavec - <p>
     * @param string $containerClass volitelná CSS class container elementu
     *
     * @return string (HTML)
     */
    public static function formatText($text, $containerElement = "p", $containerClass = null) {
        if (KT::issetAndNotEmpty($text)) {
            $output = KT::stringLineFormat(KT_String_Markdown::doMarkdownEmphasis($text));
            $classAttribute = KT::issetAndNotEmpty($containerClass) ? " class=\"$containerClass\"" : "";
            return "<{$containerElement}{$classAttribute}>{$output}</{$containerElement}>";
        }
        return null;
    }

    /**
     * Fotmát čísla do textové podoby počesku, tj. oddělovače tisíců mezery a případná desetinná čárka
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int|float $number
     * @param int $decimals počet desetinných míst, výchozí 0
     *
     * @return string
     */
    public static function formatNumber($number, $decimals = 0) {
        if (isset($number) && is_numeric($number)) {
            return number_format($number, $decimals, ",", " ");
        }
        return null;
    }

    // --- cURL ---------------------------

    /**
     * Zpracování URL (callu) obecně pomocí cURL
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *  
     * @param string $url
     * @return string
     */
    public static function curlGetContents($url) {
        if (self::issetAndNotEmpty($url)) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            $data = curl_exec($curl);
            curl_close($curl);
            return $data;
        }
        return null;
    }

    // --- TEMPLATE LOAD ---------------------------

    /**
     * Funkce vrátí single templatu ze subdir - singles
     *
     * @author Tomáš Kocifaj, Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Post $post
     * @return string - template path
     */
    public static function getSingleTemplate(WP_Post $post) {
        $templatePart = null;
        $template = get_post_meta($post->ID, KT_META_KEY_SINGLE_TEMPLATE, true);
        if (KT::issetAndNotEmpty($template)) {
            $templatePart = "-{$template}";
        }
        if ($post->post_type != KT_WP_POST_KEY) {
            $file = TEMPLATEPATH . "/singles/single-{$post->post_type}{$templatePart}.php";
            if (file_exists($file)) {
                return $file;
            }
        }
        $file = TEMPLATEPATH . "/singles/single{$templatePart}.php";
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
     * @global WP_Query $wp_query
     * @param WP_Post $post
     * @return string - template path
     */
    public static function getArchiveTemplate() {
        global $wp_query;
        $postType = $wp_query->query_vars["post_type"];
        $file = TEMPLATEPATH . "/archives/archive-{$postType}.php";
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
     * @param string $categorySlug = slug zobrazované category
     * @return string - template path
     */
    public static function getCategoryTemplate($categorySlug) {
        $file = TEMPLATEPATH . "/categories/category-{$categorySlug}.php";
        if (file_exists($file)) {
            return $file;
        }
        $category = get_category($categorySlug);
        if (KT::issetAndNotEmpty($category) && !$category instanceof WP_Error) {
            $file = TEMPLATEPATH . "/categories/category-{$category->slug}.php";
        }
        if (file_exists($file)) {
            return $file;
        }
        $file = TEMPLATEPATH . "/categories/category.php";
        if (file_exists($file)) {
            return $file;
        }
        return false;
    }

    // --- TAXONOMY ---------------------

    /**
     * Funkce vrátí taxonomy templatu ze subdir - taxonomies
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $taxonomyName - slug zobrazené taxonomy
     * @return string - template path
     */
    public static function getTaxonomyTemplate($taxonomyName) {
        $term = get_queried_object();
        $file = TEMPLATEPATH . "/taxonomies/taxonomy-{$taxonomyName}-{$term->slug}.php";
        if (file_exists($file)) {
            return $file;
        }
        $file = TEMPLATEPATH . "/taxonomies/taxonomy-{$taxonomyName}-{$term->term_id}.php";
        if (file_exists($file)) {
            return $file;
        }
        $file = TEMPLATEPATH . "/taxonomies/taxonomy-{$taxonomyName}.php";
        if (file_exists($file)) {
            return $file;
        }
        if (file_exists(TEMPLATEPATH . "/taxonomies/taxonomy.php")) {
            return TEMPLATEPATH . "/taxonomies/taxonomy.php";
        }
        return false;
    }

    // --- TAXONOMY ---------------------

    /**
     * Funkce vrátí tag templatu ze subdir - taxonomies
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $tagName - slug zobrazeného tagu
     * @return string - template path
     */
    public static function getTagTemplate($tagName) {
        $term = get_queried_object();
        $file = TEMPLATEPATH . "/tags/tag-{$tagName}-{$term->slug}.php";
        if (file_exists($file)) {
            return $file;
        }
        $file = TEMPLATEPATH . "/tags/tag-{$tagName}-{$term->term_id}.php";
        if (file_exists($file)) {
            return $file;
        }
        $file = TEMPLATEPATH . "/tags/tag-{$tagName}.php";
        if (file_exists($file)) {
            return $file;
        }
        if (file_exists(TEMPLATEPATH . "/tags/tag.php")) {
            return TEMPLATEPATH . "/tags/tag.php";
        }
        return false;
    }

    // --- TEMPLATE PART ---------------------

    /**
     * @author Jan Pokorný
     * @param string $partName
     * @param array $args Pole proměných dostupných v template partě
     */
    public static function getTemplatePart($partName, $args = []) {
        $template = TEMPLATEPATH . '/' . $partName . '.php';
        global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

        if (is_array($wp_query->query_vars)) {
            extract($wp_query->query_vars, EXTR_SKIP);
        }
        extract($args);
        require $template;
    }

    // --- TERMS ---------------------

    /**
     * Vrátí ID rodičovského termu pro ten zadaný pokud je to možné
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $termId
     * @param string $taxonomy
     * @return int
     */
    public static function getTermParentId($termId, $taxonomy) {
        $term = get_term($termId, $taxonomy);
        if (KT::issetAndNotEmpty($term)) {
            $parentId = $term->parent;
            if ($parentId > 0) {
                return self::getTermParentId($parentId, $taxonomy);
            }
            return $term->term_id;
        }
        return null;
    }

    /**
     * Naplní výčet větve termů na pro zadané ID termu směrem nahoru (k rodiči)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $termId
     * @param string $taxonomy
     * @param array $results
     */
    public static function fillTermTreeNode($termId, $taxonomy, array &$results) {
        $term = get_term($termId, $taxonomy);
        if (KT::issetAndNotEmpty($term)) {
            array_push($results, $term);
            $parentId = $term->parent;
            if ($parentId > 0) {
                self::fillTermTreeNode($parentId, $taxonomy, $results);
            }
        }
    }

    /**
     * Kontrola, zda je ID termu v zadaném výčtu poslední (nejníže v hierarchii)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $termId
     * @param array $terms
     * @return boolean
     */
    public static function isLastTerm($termId, array $terms) {
        if (KT::issetAndNotEmpty($termId)) {
            foreach ($terms as $id => $term) {
                if ($id == $termId) {
                    continue;
                }
                if ($term->parent == $termId) {
                    return false;
                }
            }
            return true;
        }
        return null;
    }

}
