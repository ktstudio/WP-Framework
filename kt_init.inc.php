<?php

define("KT_LOADED", TRUE);

define("KT_VERSION", "1.0-rc3");

define("KT_BASE_PATH", path_join(TEMPLATEPATH, "kt"));
define("KT_BASE_URL", get_template_directory_uri() . "/kt");
define("KT_FILE_PREFIX", "kt_");
define("KT_PREFIX", "kt_");
define("KT_FORM_PREFIX", "kt-");
define("KT_DOMAIN", "KT");
define("KT_PHP_FILE_SUFFIX", ".inc.php");
define("KT_INIT_MODULE_FILE", "kt_init.inc.php");
define("KT_BASE_STATIC_CLASS", "KT");
define("KT_EMPTY_SYMBOL", __("---", KT_DOMAIN));
define("KT_EMPTY_TEXT", __("<Prázdné>", KT_DOMAIN));
define("KT_ALL_TEXT", __("<Vše>", KT_DOMAIN));
define("KT_SELECT_TEXT", __("<Vybrat>", KT_DOMAIN));
define("KT_SELECT_SYMBOL", __("...", KT_DOMAIN));
define("KT_BASE_CLASS_SUFFIX", "base");
define("KT_INTERFACES_FOLDER", "interfaces");
define("KT_CLASSES_FOLDER", "classes");
define("KT_EXCEPTIONS_FOLDER", "exceptions");
define("KT_PRESENTERS_FOLDER", "presenters");
define("KT_CONFIGS_FOLDER", "configs");
define("KT_MODELS_FOLDER", "models");
define("KT_ENUMS_FOLDER", "enums");
define("KT_WIDGETS_FOLDER", "widgets");
define("KT_SHORTCODES_FOLDER", "shortcodes");

/**
 * @return array
 */
global $ktModules;
$ktModules = array("core");

/**
 * @return array
 */
global $ktSpecialFolders;
$ktSpecialFolders = array(
    KT_EXCEPTIONS_FOLDER,
    KT_PRESENTERS_FOLDER,
    KT_CONFIGS_FOLDER,
    KT_MODELS_FOLDER,
    KT_ENUMS_FOLDER,
    KT_WIDGETS_FOLDER,
    KT_SHORTCODES_FOLDER,
);

spl_autoload_register("kt_class_autoloader_init");

kt_load_all_modules();

/**
 * Kontrola, zda je načten KT framework pro šablony apod.
 *
 * @return empty|exit
 */
function kt_check_loaded() {
    if (KT_LOADED === true)
        return;
    exit;
}

/**
 * Načtení všech inicializačních souborů pro všechny podsložky na hlavní podúrovni, resp. moduly
 * Tato jediná funkce by se měla volat ze šablony, všech ostatní dostupný zbytek se načte sám...
 */
function kt_load_all_modules() {
    $submodules = kt_get_subdirs_names(KT_BASE_PATH);
    foreach ($submodules as $module) {
        $modulePath = path_join(KT_BASE_PATH, $module);
        // chceme inicializační soubor modulu
        $initModuleFile = path_join($modulePath, KT_INIT_MODULE_FILE);
        if (file_exists($initModuleFile)) {
            require_once ($initModuleFile);
        }
    }
}

/**
 * Získání všech názvů podadresářů pro zadaný adresář
 * @return array
 */
function kt_get_subdirs_names($dirPath, $checkForDirExists = true) {
    if (isset($dirPath) && is_dir($dirPath)) {
        $subdirsNames = array();
        $names = scandir($dirPath);
        foreach ($names as $name) { // procházíme základní adresáře v KT, tedy moduly
            if ($name == "." || $name == ".." || $name == ".git" || $name == ".gitignore") {
                continue;
            }
            if ($checkForDirExists === true) {
                if (is_dir(path_join($dirPath, $name))) {
                    array_push($subdirsNames, $name);
                }
            } else {
                array_push($subdirsNames, $name);
            }
        }
        return $subdirsNames;
    }
    return null;
}

/**
 * Autoload určený pro strukturu KT modulů uvnitř
 * @param string $name třída nebo interface k auto načtení
 */
function kt_class_autoloader_init($name) {
    if (kt_is_prefixed($name) || $name === KT_BASE_STATIC_CLASS) {
        /**
         * @return array
         */
        global $ktModules;
        $fileName = kt_get_include_file_name($name);
        // detekce pro speciální třídy (mimo adresář classes)
        if (kt_special_class_autoloader_init($name, $fileName, $ktModules)) {
            return;
        }
        foreach ($ktModules as $moduleName) { // projetí všech (sub)modulů
            $modulePath = path_join(KT_BASE_PATH, $moduleName);
            // detekce pro případný interface
            $interfacesPath = path_join($modulePath, KT_INTERFACES_FOLDER);
            $interfacePath = path_join($interfacesPath, $fileName);
            if (file_exists($interfacePath)) {
                require_once($interfacePath);
                return;
            }
            // detekce pro případnou klasickou třídu (v adresáři classes)
            $classesPath = path_join($modulePath, KT_CLASSES_FOLDER);
            $classPath = path_join($classesPath, $fileName);
            if (file_exists($classPath)) { // třídy na základní úrovni adresáře
                require_once($classPath);
                return;
            } else { // pro třídy ještě případně hledáme všechny pod úrovně (hlouby 1)
                $classesSubdirsNames = kt_get_subdirs_names($classesPath);
                if (KT::issetAndNotEmpty($classesSubdirsNames) && count($classesSubdirsNames) > 0) {
                    foreach ($classesSubdirsNames as $classSubdirName) {
                        $subClassesPath = path_join($classesPath, $classSubdirName);
                        $subClassPath = path_join($subClassesPath, $fileName);
                        if (file_exists($subClassPath)) {
                            require_once($subClassPath);
                            return;
                        }
                    }
                } else {
                    continue;
                }
            }
        }
    }
}

function kt_special_class_autoloader_init($name, $fileName, $moduleNames) {
    /**
     * @return array
     */
    global $ktSpecialFolders;
    $nameParts = explode("_", $name);
    $lastNamePart = strtolower((string) array_pop($nameParts));
    if (strtolower("$lastNamePart") === KT_BASE_CLASS_SUFFIX) {
        $lastNamePart = strtolower((string) array_pop($nameParts));
    }
    $suffix = "{$lastNamePart}s";
    if (in_array($suffix, $ktSpecialFolders)) {
        foreach ($moduleNames as $moduleName) {
            $modulePath = path_join(KT_BASE_PATH, $moduleName);
            $specialClassesPath = path_join($modulePath, $suffix);
            $specialClassPath = path_join($specialClassesPath, $fileName);
            if (file_exists($specialClassPath)) {
                require_once($specialClassPath);
                return true;
            }
        }
    }
    return false;
}

/**
 * Převede název na malá písmena a přidá vkládací příponu (pro php)
 * @param string $name název třídy nebo interfacu
 * @return string zformátovaný název pro include nebo require
 */
function kt_get_include_file_name($name) {
    $fileName = strtolower($name) . KT_PHP_FILE_SUFFIX;
    return $fileName;
}

/**
 * Registrace modulu do "systému" podle klíče - relativní cesta uvnitř KT adresáře
 * 
 * @global array $ktModules
 */
function kt_register_module($key) {
    /**
     * @return array
     */
    global $ktModules;
    if (!in_array($key, $ktModules)) {
        array_push($ktModules, $key);
    }
}

/**
 * Inicializace, resp. definice potřebných konstant pro modul na základě 
 * názvu adresáře a klíče, resp. prefixu (pro sestavní konstant). 
 * Inicializuje ***_PATH a ***_URL konstanty por vnitřní a další použití. 
 * Ve výchozím nastavní se automaticky načte i adresář Requires. 
 * 
 * @param string $modulePrefix (constant prefix)
 * @param string $folder (name)
 * @param boolean $withIncludeAll (auto load requires)
 * @param boolean $withRegistration (auto registrace modulu)
 */
function kt_initialize_module($modulePrefix, $folder = "yours", $withIncludeAll = true, $withRegistration = true) {
    if ($withRegistration) {
        kt_register_module($folder);
    }
    // PATH
    $pathKey = "KT_{$modulePrefix}_PATH";
    define("KT_{$modulePrefix}_PATH", path_join(KT_BASE_PATH, $folder));
    $pathValue = constant($pathKey);
    define("KT_{$modulePrefix}_ASSETS_PATH", path_join($pathValue, "assets"));
    define("KT_{$modulePrefix}_CLASSES_PATH", path_join($pathValue, "classes"));
    define("KT_{$modulePrefix}_CSS_PATH", path_join($pathValue, "css"));
    define("KT_{$modulePrefix}_IMAGES_PATH", path_join($pathValue, "images"));
    define("KT_{$modulePrefix}_INTERFACES_PATH", path_join($pathValue, "interfaces"));
    define("KT_{$modulePrefix}_JS_PATH", path_join($pathValue, "js"));
    define("KT_{$modulePrefix}_REQUIRES_PATH", path_join($pathValue, "requires"));
    define("KT_{$modulePrefix}_TEMPLATES_PATH", path_join($pathValue, "templates"));
    // URL
    $urlKey = "KT_{$modulePrefix}_URL";
    define("KT_{$modulePrefix}_URL", path_join(KT_BASE_URL, $folder));
    $urlValue = constant($urlKey);
    define("KT_{$modulePrefix}_ASSETS_URL", path_join($urlValue, "assets"));
    define("KT_{$modulePrefix}_CLASSES_URL", path_join($urlValue, "classes"));
    define("KT_{$modulePrefix}_CSS_URL", path_join($urlValue, "css"));
    define("KT_{$modulePrefix}_IMAGES_URL", path_join($urlValue, "images"));
    define("KT_{$modulePrefix}_INTERFACES_URL", path_join($urlValue, "interfaces"));
    define("KT_{$modulePrefix}_JS_URL", path_join($urlValue, "js"));
    define("KT_{$modulePrefix}_REQUIRES_URL", path_join($urlValue, "requires"));
    define("KT_{$modulePrefix}_TEMPLATES_URL", path_join($urlValue, "templates"));
    // include all
    if ($withIncludeAll) {
        kt_include_all(constant("KT_{$modulePrefix}_REQUIRES_PATH"));
    }
}

/**
 * Načtení všech PHP souborů s KT předponou a inc příponou v zadaném adresáři a všech jeho podadresářích
 * @param string $folder - uri (cesta) ke složce
 */
function kt_include_all($folder) {
    if (isset($folder) && is_dir($folder)) {
        $subdirsNames = kt_get_subdirs_names($folder, false);
        foreach ($subdirsNames as $subdirName) {
            $modulePath = path_join($folder, $subdirName);
            if (is_dir($modulePath)) {
                kt_include_all($modulePath); // zanořování do hloubky
            } elseif (is_file($modulePath)) {
                $moduleBaseName = basename($modulePath); // název souboru včetně koncovky
                $startsWith = (strpos($moduleBaseName, KT_FILE_PREFIX) === 0); // kontrola jestli soubor začíná KT prefixem
                $endsWith = (strpos(strrev($moduleBaseName), strrev(KT_PHP_FILE_SUFFIX)) === 0); // kontrola jestli soubor končí inc příponou (php)
                if ($startsWith && $endsWith) {
                    require_once $modulePath;
                }
            }
        }
    } else {
        throw new InvalidArgumentException(__("Hodnota \"$folder\" nesmí být prázdná a musí být adresář.", KT_DOMAIN));
    }
}

/**
 * Přidá k zadanému textu KT_PREFIX (na začátek)
 * @param string $text
 * @return string|null
 */
function kt_get_prefixed($text) {
    if (isset($text) && !empty($text)) {
        return KT_PREFIX . $text;
    }
    return null;
}

/**
 * Přidá k zadanému textu KT_FORM_PREFIX (na začátek)
 * @param string $text
 * @return string|null
 */
function kt_get_form_prefixed($text) {
    if (isset($text) && !empty($text)) {
        return KT_FORM_PREFIX . $text;
    }
    return null;
}

/**
 * Kontrola, zda zadaný text začíná KT prefixem
 * @param string $text
 * @return boolean
 */
function kt_is_prefixed($text) {
    if (isset($text) && !empty($text)) {
        $result = strtolower(substr($text, 0, 3)) === KT_PREFIX;
        if ($result === true) {
            return true;
        }
    }
    return false;
}

add_filter("template_include", "kt_load_template_from_subdir");

/**
 * Funkce umožní načítání template souborů z předem definovaných adresářů,
 * ale neprochází široký rozsah složek, ale přesné složky v rootu šablony
 * dir - singles - všechny single soubory
 * dir - pages - všechny template pro složky a page.php samotné
 * dir - taxonomies - všechny taxonomy včetně taxonomy.php
 */
function kt_load_template_from_subdir($template) {
    global $post;
    global $taxonomy;
    global $cat;

    // --- single ---------------------------
    if (is_single()) {
        $ktTemplate = KT::getSingleTemplate($post);
        if ($ktTemplate) {
            return $ktTemplate;
        }
    }

    // --- attachment ---------------------------
    if (is_attachment()) {
        $ktTemplate = KT::getAttachmentTemplate();
        if ($ktTemplate) {
            return $ktTemplate;
        }
    }

    // --- page ---------------------------
    if (is_page()) {
        $ktTemplate = KT::getPageTemplate($post);
        if ($ktTemplate) {
            return $ktTemplate;
        }
    }

    // --- category ---------------------------
    if (is_category()) {
        $ktTemplate = KT::getCategoryTemplate($cat);
        if ($ktTemplate) {
            return $ktTemplate;
        }
        return $template;
    }

    // --- search ---------------------------
    if (is_search()) {
        return $template;
    }

    // --- taxonomy ---------------------------
    if (KT::issetAndNotEmpty($taxonomy)) {
        $ktTemplate = KT::getTaxonomyTemplate($taxonomy);
        if ($ktTemplate) {
            return $ktTemplate;
        }
    }

    // --- author -----------------------------
    if (is_author()) {
        return $template;
    }

    // --- archive ---------------------------
    /*
     * Musí být načítán vždy poslední kvůli WP Query
     */
    if (is_archive()) {
        $ktTemplate = KT::getArchiveTemplate();
        if ($ktTemplate) {
            return $ktTemplate;
        }
    }

    return $template;
}
