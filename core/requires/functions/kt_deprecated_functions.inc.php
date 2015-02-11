<?php

/**
 * Vložení nového klíč-hodnota do pole za zadaný index na základně číselného indexu
 * @param array $input
 * @param int $index
 * @param int|string $newKey
 * @param object $newValue
 * @return array
 * @throws KT_Not_Supported_Exception
 */
function kt_array_insert(array $input, $index, $newKey, $newValue) {
    $index = kt_try_get_int($index);
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
 * @param array $input
 * @param int|string $index
 * @param int|string $newKey
 * @param object $newValue
 * @return array
 * @throws KT_Duplicate_Exception
 */
function kt_array_insert_before(array $input, $index, $newKey, $newValue) {
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
 * @param array $input
 * @param int|string $index
 * @param int|string $newKey
 * @param object $newValue
 * @return array
 * @throws KT_Duplicate_Exception
 */
function kt_array_insert_after(array $input, $index, $newKey, $newValue) {
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
 * @return array
 */
function kt_array_remove(array $haystack, $needle) {
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
 * @param array $input - pole, kde se smažou hodnoty
 * @param array $delete_keys - které klíče se mají smazat z $input
 * @return array
 */
function kt_array_keys_remove($input, $delete_keys) {
    foreach ($delete_keys as $value) {
        unset($input[$value]);
    }

    return $input;
}

/**
 * Ze zadaného pole odstraní zadanou hodnotu
 * @return array
 */
function kt_array_remove_by_value(array $haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if ($value == $needle) {
            unset($haystack[$key]);
        }
    }
    return $haystack;
}

/**
 * Ze zadaného pole odstraní zadaný klíč (i s hodnotou)
 * @return array
 */
function kt_array_remove_by_key(array $haystack, $needle) {
    foreach ($haystack as $key => $value) {
        if ($key == $needle) {
            unset($haystack[$key]);
        }
    }
    return $haystack;
}

/**
 * Vrátí, zda má pole více než jednu úroveň
 * @param array $array
 * @return boolean
 */
function kt_array_is_multi(array $array) {
    if (count($array) == count($array, COUNT_RECURSIVE)) {
        return true;
    }
    return false;
}

/**
 * Kontrola, zda je zadaný parameter přiřezený, typu pole a má jeden nebo více záznamů
 * 
 * @param array|type $array
 * @return boolean
 */
function kt_array_isset_and_not_empty($array) {
    return kt_isset_and_not_empty($array) && is_array($array) && count($array) > 0;
}

/**
 * Vrátí první klíč v poli
 * 
 * @param array
 * @return string|int
 */
function kt_array_get_first_key(array $array) {
    foreach ($array as $key => $value) {
        return $key;
    }
}

/**
 * Vrátí první hodnotu v poli
 * 
 * @param array
 * @return type
 */
function kt_array_get_first_value(array $array) {
    foreach ($array as $key => $value) {
        return $value;
    }
}

/**
 * Očistí pole od hodnot "." a ".." při scandiru
 *
 * @param array $input
 * @return array
 */
function kt_array_clear_dir($input) {
    foreach ($input as $key => $value) {
        if ($value == '.' || $value == '..') {
            unset($input[$key]);
        }
    }

    return array_values($input);
}

/**
 * Vrátí aktuální datum a čas v obecném tvaru
 * @param string $format
 * @param string $timeStampText
 * @return date
 */
function kt_date_get_now($format = "Y-m-d H:i:s", $timeStampText = null) {
    if (kt_isset_and_not_empty($timeStampText)) {
        return date($format, strtotime($timeStampText));
    }
    return date($format);
}

/**
 * Kontrola na isset a ! empty v "jednom" kroku
 * @return bool
 */
function kt_isset_and_not_empty($value) {
    return isset($value) && !empty($value);
}

/**
 * Kontrola na ! isset nebo empty v "jednom" kroku
 * @return bool
 */
function kt_not_isset_or_empty($value) {
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
function kt_pr($objekt, $name = '', $return = false) {
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
 * @link http://www.ktstudio.cz
 *
 * @global object WP_Post $post - post object: Použitelný pouze v loopě
 * @param int $post_id - Id postu, který se nastaveí
 * @return ojbect WP_post
 */
function kt_setup_post_object($post_id = null) {

    if (kt_is_id_format($post_id)) {
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
 * @return boolean
 */
function kt_check_current_user_can_manage_options() {
    if (current_user_can("manage_options")) {
        return true;
    } else {
        wp_die(__("Nemáte dostatečná oprávnění k přístupu na tuto stránku.", KT_DOMAIN));
        return false;
    }
}

/**
 * Na základě zadaných parametrů vrátí řetezec pro programové odsazení tabulátorů s případnými novými řádky
 * @param integer $tabsCount
 * @param string $content
 * @param boolean $newLineBefore
 * @param boolean $newLineAfter
 * @return string
 */
function kt_get_tabs_indent($tabsCount, $content = null, $newLineBefore = false, $newLineAfter = false) {
    $result = "";
    if ($newLineBefore == true) {
        $result .= "\n";
    }
    $result .= str_repeat("\t", $tabsCount);
    if (kt_isset_and_not_empty($content)) {
        $result .= $content;
    }
    if ($newLineAfter == true) {
        $result .= "\n";
    }
    return $result;
}

/**
 * Na základě zadaných parametrů vypíše řetezec pro programové odsazení tabulátorů s případnými novými řádky
 * @param integer $tabsCount
 * @param string $content
 * @param boolean $newLineBefore
 * @param boolean $newLineAfter
 * @return string
 */
function kt_the_tabs_indent($tabsCount, $content = null, $newLineBefore = false, $newLineAfter = false) {
    echo kt_get_tabs_indent($tabsCount, $content, $newLineBefore, $newLineAfter);
}

/**
 * Vrátí aktuální URL na základě nastavení APACHE HTTP_HOST a REQUEST_URI
 * @param bool $fullUrl - true i s pametry, false bez
 * @return string
 */
function kt_get_request_url($fullUrl = true) {
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
 * @return string
 */
function kt_get_backlink_url() {
    $refererUrl = $_SERVER['HTTP_REFERER'];
    if (filter_var($refererUrl, FILTER_VALIDATE_URL)) {
        return $refererUrl;
    }
    return get_home_url();
}

/**
 * Na základě zadané adresy vrátí GPS souřadnice pomocí Google API pokud je možné
 * @param string $address
 * @return string|null
 */
function kt_get_google_maps_gps($address) {
    if (kt_isset_and_not_empty($address) && is_string($address)) {
        $address = urlencode(trim($address));
        $googleApiLink = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";
        $googleApiResult = file_get_contents($googleApiLink);
        if ($googleApiResult) {
            $googleApiResultJson = json_decode($googleApiResult);
            if (kt_isset_and_not_empty($googleApiResultJson)) {
                $gpsLatitude = (float) $googleApiResultJson->results[0]->geometry->location->lat;
                $gpsLongtitude = (float) $googleApiResultJson->results[0]->geometry->location->lng;
                if (kt_isset_and_not_empty($gpsLatitude) && kt_isset_and_not_empty($gpsLongtitude)) {
                    $coordinates = $gpsLatitude . ", " . $gpsLongtitude;
                    return $coordinates;
                }
            }
        }
    }
    return null;
}

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
function kt_get_attachment_image_html($id, array $linkArgs = array(), array $imageArgs = array(), $size = KT_WP_IMAGE_SIZE_THUBNAIL, $tabsCount = 0) {
    $output = null;
    if (kt_is_id_format($id) > 0) {
        $source = wp_get_attachment_image_src($id, $size);
        if (kt_array_isset_and_not_empty($source)) {
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
            $output .= kt_get_tabs_indent($tabsCount, "<a href=\"$linkUrl\"$linkAttributes>", true);
            $output .= kt_get_tabs_indent($tabsCount + 1, "<img src=\"$imageUrl\" width=\"$imageWidth\" height=\"$imageHeight\"$imageAttributes />", true);
            $output .= kt_get_tabs_indent($tabsCount, "</a>", true, true);
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
function kt_get_image_theme($fileName) {
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
function kt_replace_images_lazy_src($html) {
    if (kt_isset_and_not_empty($html)) {
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
function kt_get_custom_menu_name($location, $defaultTitle = null) {
    $locations = get_nav_menu_locations();
    $menuLocation = $locations[$location];
    if (kt_isset_and_not_empty($menuLocation)) {
        $menuObject = wp_get_nav_menu_object($menuLocation);
        if (kt_isset_and_not_empty($menuObject)) {
            $menuName = $menuObject->name;
            if (kt_isset_and_not_empty($menuName)) {
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
function kt_the_wp_nav_menu($themeLocation, $depth = 0) {
    wp_nav_menu(array(
        "theme_location" => $themeLocation,
        "container" => false,
        "depth" => $depth,
        "items_wrap" => '%3$s',
        "fallback_cb" => false
    ));
}

/**
 * Prověří, zda zadaný parametr je ve formátu pro ID v databázi
 * Je: Setnutý, není prázdný a je větší než 0
 *
 * @param mixed $value
 * @return boolean
 */
function kt_is_id_format($value) {
    $id = kt_try_get_int($value);
    if (kt_isset_and_not_empty($id) && $id > 0) {
        return true;
    }
    return false;
}

/**
 * Kontrola hodnoty, jestli je číselného typu, resp. int a případné přetypování nebo rovnou návrat, jinak null
 * @param number $value
 * @return integer|null
 */
function kt_try_get_int($value) {
    if (kt_isset_and_not_empty($value) && is_numeric($value)) {
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
 * @param number $value
 * @return float|null
 */
function kt_try_get_float($value) {
    if (kt_isset_and_not_empty($value) && is_numeric($value)) {
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
 * @param number $value
 * @return double|null
 */
function kt_try_get_double($value) {
    if (kt_isset_and_not_empty($value) && is_numeric($value)) {
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
 * Obecné zaokrouhlení podle celých nebo destinných čísel
 * @param number $value
 * @return number
 */
function kt_round($value) {
    if ((kt_isset_and_not_empty($value) && is_numeric($value) || $value === "0")) {
        if (is_int($value)) {
            return round($value, 0, PHP_ROUND_HALF_UP);
        } else {
            return round($value, 2, PHP_ROUND_HALF_UP);
        }
    }
    return $value;
}

/**
 * Vypíše stránkování určené pro WP loopu v bootstrap stylu
 *
 * @global integer $paged
 * @global WP_Query $wp_query
 * @param boolean $previousNext
 * @param string $customClass
 */
function kt_pagination($previousNext = true, $customClass = "pagination-centered") {
    global $paged;
    $paged = kt_try_get_int($paged) ? : 1;
    if (kt_isset_and_not_empty($paged) && $paged > 0) {
        global $wp_query;
        $pages = kt_try_get_int($wp_query->max_num_pages);
        if (kt_isset_and_not_empty($pages) && $pages > 1 && $paged >= $paged) {
            echo kt_the_tabs_indent(0, "<ul class=\"pagination $customClass\">", true);

            if ($previousNext) {
                $firstClass = $paged > 2 ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $firstClass><a href='" . get_pagenum_link(1) . "'>&laquo;</a></li>", true);
                $secondClass = $paged > 1 ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $secondClass><a href='" . get_pagenum_link($paged - 1) . "'>&lsaquo;</a></li>", true);
            }

            for ($i = 1; $i <= $pages; $i ++) {
                $pagenumlink = get_pagenum_link($i);
                $activeClass = ($i == $paged) ? 'class="active"' : "";
                echo kt_the_tabs_indent(1, "<li $activeClass><a href=\"$pagenumlink\">$i</a></li>", true);
            }

            if ($previousNext) {
                $penultimateClass = $paged < $pages ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $penultimateClass><a href='" . get_pagenum_link($paged + 1) . "'>&rsaquo;</a></li>", true);
                $latestClass = $paged < $pages - 1 ? "" : 'class="disabled"';
                echo kt_the_tabs_indent(1, "<li $latestClass><a href='" . get_pagenum_link($pages) . "'>&raquo;</a></li>", true);
            }

            kt_the_tabs_indent(0, "</div>", true, true);
        }
    }
}

/**
 * Kontrola, zda první zadaný textový řetezec někde uvnitř sebe obsahuje ten druhý zadaný
 * @param string $string řetězec k prohledání
 * @param string $substring hledaný podřetězec
 * @return boolean true, pokud $substring se nachází v $string, jinak false
 */
function kt_string_contains($string, $substring) {
    $position = strpos($string, $substring);
    if ($position === false) {
        return false;
    }
    return true;
}

/**
 * Kontrola, zda první zadaný textový řetezec obsahuje na svém konci ten druhý zadaný
 * @param string $string
 * @param string $ending
 * @return boolean
 */
function kt_string_ends_with($string, $ending) {
    $length = strlen($ending);
    $string_end = substr($string, strlen($string) - $length);
    return $string_end === $ending;
}

/**
 * Kontrola, zda první zadaný textový řetezec obsahuje na svém začátku ten druhý zadaný
 * @param string $string
 * @param string $starting
 * @return boolean
 */
function kt_string_starts_with($string, $starting) {
    $length = strlen($starting);
    return (substr($string, 0, $length) === $starting);
}

/**
 * Odstranění html ze zadaného textu + převod speciálních znaků
 * @param string $text
 * @return string
 */
function kt_string_clear_html($text) {
    return htmlspecialchars(strip_tags($text));
}

/**
 * Ořízně zadaný řetezec, pokud je delší než požadovaná maximální délka včetně případné přípony
 * @param string $text
 * @param int $maxLength
 * @param string $suffix
 * @return string
 */
function kt_string_crop($text, $maxLength, $suffix = "...") {
    $maxLength = kt_try_get_int($maxLength);
    $currentLength = strlen($text);
    if ($maxLength > 0 && $currentLength > $maxLength) {
        $text = strip_tags($text);
        $text = mb_substr($text, 0, $maxLength);
        $text .= $suffix;
    }
    return $text;
}

/**
 * Funkce vrátí single templatu ze subdir - singles
 *
 * @param WP_Post $post
 * @return string - template path
 */
function kt_get_single_template(WP_Post $post) {
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
 * @param WP_Post $post
 * @return string|boolean - template path
 */
function kt_get_attachment_template() {
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
 * @param WP_Post $post
 * @return string - template path
 */
function kt_get_page_template(WP_Post $post) {
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
 * @param WP_Post $post
 * @return string - template path
 */
function kt_get_archive_template() {
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
 * @param string $cat = slug zobrazované category
 * @return string - template path
 */
function kt_get_category_template($cat) {
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
 * @param string $taxonomy - slug zobrazené taxonomy
 * @return string - template path
 */
function kt_get_taxonomy_template($taxonomy) {
    $file = TEMPLATEPATH . '/taxonomies/taxonomy-' . $taxonomy . '.php';
    if (file_exists($file)) {
        return $file;
    }
    if (file_exists(TEMPLATEPATH . '/taxonomies/taxonomy.php')) {
        return TEMPLATEPATH . '/taxonomies/taxonomy.php';
    }

    return false;
}
