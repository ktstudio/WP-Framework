<?php

define( "KT_LOADING", TRUE );

define( "KT_BASE_PATH", path_join( TEMPLATEPATH, "kt" ) );
define( "KT_BASE_URL", get_template_directory_uri() . "/kt" );
define( "KT_FILE_PREFIX", "kt_" );
define( "KT_PREFIX", "kt_" );
define( "KT_FORM_PREFIX", "kt-" );
define( "KT_DOMAIN", "KT" );
define( "KT_PHP_FILE_SUFFIX", ".inc.php" );
define( "KT_INIT_MODULE_FILE", "kt_init.inc.php" );
define( "KT_EMPTY_TEXT", __( "---", KT_DOMAIN ) );
define( "KT_ALL_TEXT", __( "Vše", KT_DOMAIN ) );
define( "KT_SIMPLE_CODE", __( "praha", KT_DOMAIN ) );
define( "KT_INTERFACES_FOLDER", "interfaces" );
define( "KT_EXCEPTIONS_FOLDER", "exceptions" );
define( "KT_PRESENTERS_FOLDER", "presenters" );
define( "KT_CONFIGS_FOLDER", "configs" );
define( "KT_MODELS_FOLDER", "models" );
define( "KT_ENUMS_FOLDER", "enums" );
define( "KT_WIDGETS_FOLDER", "widgets" );
define( "KT_CLASSES_FOLDER", "classes" );


spl_autoload_register( "kt_class_autoloader_init" );

kt_load_all_modules();

/**
 * Kontrola, zda je načten KT framework pro šablony apod.
 *
 * @return empty|exit
 */
function kt_check_loaded() {
	if ( KT_LOADING === true )
		return;
	exit;
}

/**
 * Načtení všech inicializačních souborů pro všechny podsložky na hlavní podúrovni, resp. moduly
 * Tato jediná funkce by se měla volat ze šablony, všech ostatní dostupný zbytek se načte sám...
 */
function kt_load_all_modules() {
	foreach ( kt_get_all_modules_names() as $module ) {
		$modulePath = path_join( KT_BASE_PATH, $module );
		// chceme inicializační soubor modulu
		$initModuleFile = path_join( $modulePath, KT_INIT_MODULE_FILE );
		if ( file_exists( $initModuleFile ) ) {
			require_once ($initModuleFile);
		}
	}
}

/**
 * Získání všech názvů modulů (resp. podadresářů)
 * @return array
 */
function kt_get_all_modules_names() {
	return kt_get_subdirs_names( KT_BASE_PATH );
}

/**
 * Získání všech názvů podadresářů pro zadaný adresář
 * @return array
 */
function kt_get_subdirs_names( $dirPath, $checkForDirExists = true ) {
	if ( isset( $dirPath ) && is_dir( $dirPath ) ) {
		$subdirsNames = array();
		$names = scandir( $dirPath );
		foreach ( $names as $name ) { // procházíme základní adresáře v KT, tedy moduly
			if ( $name == "." || $name == ".." || $name == ".git" || $name == ".gitignore" ) {
				continue;
			}
			if ( $checkForDirExists === true ) {
				if ( is_dir( path_join( $dirPath, $name ) ) ) {
					array_push( $subdirsNames, $name );
				}
			} else {
				array_push( $subdirsNames, $name );
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
function kt_class_autoloader_init( $name ) {
	if ( kt_is_prefixed( $name ) ) {
		$fileName = kt_get_include_file_name( $name );

		$modulesNames = kt_get_all_modules_names();
		foreach ( $modulesNames as $moduleName ) { // projetí všech (sub)modulů
			$modulePath = path_join( KT_BASE_PATH, $moduleName );

			// detekce pro případný interface
			$interfacesPath = path_join( $modulePath, KT_INTERFACES_FOLDER );
			$interfacePath = path_join( $interfacesPath, $fileName );
			if ( file_exists( $interfacePath ) ) {
				require_once($interfacePath);
				return;
			}

			// detekce pro případný výjimku
			$exceptionsPath = path_join( $modulePath, KT_EXCEPTIONS_FOLDER );
			$exceptionPath = path_join( $exceptionsPath, $fileName );
			if ( file_exists( $exceptionPath ) ) {
				require_once($exceptionPath);
				return;
			}

			// detekce pro případné presentery
			$presentersPath = path_join( $modulePath, KT_PRESENTERS_FOLDER );
			$presenterPath = path_join( $presentersPath, $fileName );
			if ( file_exists( $presenterPath ) ) {
				require_once($presenterPath);
				return;
			}

			// detekce pro případné configy
			$configsPath = path_join( $modulePath, KT_CONFIGS_FOLDER );
			$configPath = path_join( $configsPath, $fileName );
			if ( file_exists( $configPath ) ) {
				require_once($configPath);
				return;
			}

			// detekce pro případné modely
			$modelsPath = path_join( $modulePath, KT_MODELS_FOLDER );
			$modelPath = path_join( $modelsPath, $fileName );
			if ( file_exists( $modelPath ) ) {
				require_once($modelPath);
				return;
			}

			// detekce pro případné enumy
			$enumsPath = path_join( $modulePath, KT_ENUMS_FOLDER );
			$enumPath = path_join( $enumsPath, $fileName );
			if ( file_exists( $enumPath ) ) {
				require_once($enumPath);
				return;
			}

			// detekce pro případné widgety
			$widgetsPath = path_join( $modulePath, KT_WIDGETS_FOLDER );
			$widgetPath = path_join( $widgetsPath, $fileName );
			if ( file_exists( $widgetPath ) ) {
				require_once($widgetPath);
				return;
			}

			// detekce pro případnou třídu
			$classesPath = path_join( $modulePath, KT_CLASSES_FOLDER );
			$classPath = path_join( $classesPath, $fileName );
			if ( file_exists( $classPath ) ) {
				require_once($classPath);
				return;
			} else { // pro třídy ještě případně hledáme všechny pod úrovně (hlouby 1)
				$classesSubdirsNames = kt_get_subdirs_names( $classesPath );
				if ( kt_isset_and_not_empty( $classesSubdirsNames ) && count( $classesSubdirsNames ) > 0 ) {
					foreach ( $classesSubdirsNames as $classSubdirName ) {
						$subClassesPath = path_join( $classesPath, $classSubdirName );
						$subClassPath = path_join( $subClassesPath, $fileName );
						if ( file_exists( $subClassPath ) ) {
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

/**
 * Převede název na malá písmena a přidá vkládací příponu (pro php)
 * @param string $name název třídy nebo interfacu
 * @return string zformátovaný název pro include nebo require
 */
function kt_get_include_file_name( $name ) {
	$fileName = strtolower( $name ) . KT_PHP_FILE_SUFFIX;
	return $fileName;
}

/**
 * Inicializace, resp. definice potřebných konstant pro modul na základě 
 * názvu adresáře a klíče, resp. prefixu (pro sestavní konstant). 
 * Inicializuje ***_PATH a ***_URL konstanty por vnitřní a další použití. 
 * Ve výchozím nastavní se automaticky načte i adresář Requires. 
 * 
 * @param string $key (constant prefix)
 * @param string $folder (name)
 * @param boolean $withIncludeAll (auto load requires)
 */
function kt_initialize_module( $key, $folder = "yours", $withIncludeAll = true ) {
        // PATH
        $pathKey = "KT_{$key}_PATH";
        define( "KT_{$key}_PATH", path_join( KT_BASE_PATH, $folder ) );
        $pathValue = constant( $pathKey ) ;
        define( "KT_{$key}_ASSETS_PATH", path_join( $pathValue, "assets" ) );
        define( "KT_{$key}_CLASSES_PATH", path_join( $pathValue, "classes" ) );
        define( "KT_{$key}_CSS_PATH", path_join( $pathValue, "css" ) );
        define( "KT_{$key}_EXCEPTIONS_PATH", path_join( $pathValue, "exceptions" ) );
        define( "KT_{$key}_IMAGES_PATH", path_join( $pathValue, "images" ) );
        define( "KT_{$key}_INTERFACES_PATH", path_join( $pathValue, "interfaces" ) );
        define( "KT_{$key}_JS_PATH", path_join( $pathValue, "js" ) );
        define( "KT_{$key}_REQUIRES_PATH", path_join( $pathValue, "requires" ) );
        define( "KT_{$key}_TEMPLATES_PATH", path_join( $pathValue, "templates" ) );
        // URL
        $urlKey = "KT_{$key}_URL";        
        define( "KT_{$key}_URL", path_join( KT_BASE_URL, $folder ) );
        $urlValue = constant( $urlKey ) ;
        define( "KT_{$key}_ASSETS_URL", path_join( $urlValue, "assets" ) );
        define( "KT_{$key}_CLASSES_URL", path_join( $urlValue, "classes" ) );
        define( "KT_{$key}_CSS_URL", path_join( $urlValue, "css" ) );
        define( "KT_{$key}_EXCEPTIONS_URL", path_join( $urlValue, "exceptions" ) );
        define( "KT_{$key}_IMAGES_URL", path_join( $urlValue, "images" ) );
        define( "KT_{$key}_INTERFACES_URL", path_join( $urlValue, "interfaces" ) );
        define( "KT_{$key}_JS_URL", path_join( $urlValue, "js" ) );
        define( "KT_{$key}_REQUIRES_URL", path_join( $urlValue, "requires" ) );
        define( "KT_{$key}_TEMPLATES_URL", path_join( $urlValue, "templates" ) );
        // include all
        if( $withIncludeAll ) {
            kt_include_all( constant( "KT_{$key}_REQUIRES_PATH" ) );
        }
}

/**
 * Načtení všech PHP souborů s KT předponou a inc příponou v zadaném adresáři a všech jeho podadresářích
 * @param string $folder - uri (cesta) ke složce
 */
function kt_include_all( $folder ) {
	if ( isset( $folder ) && is_dir( $folder ) ) {
		$subdirsNames = kt_get_subdirs_names( $folder, false );
		foreach ( $subdirsNames as $subdirName ) {
			$modulePath = path_join( $folder, $subdirName );
			if ( is_dir( $modulePath ) ) {
				kt_include_all( $modulePath ); // zanořování do hloubky
			} elseif ( is_file( $modulePath ) ) {
				$moduleBaseName = basename( $modulePath ); // název souboru včetně koncovky
				$startsWith = (strpos( $moduleBaseName, KT_FILE_PREFIX ) === 0); // kontrola jestli soubor začíná KT prefixem
				$endsWith = (strpos( strrev( $moduleBaseName ), strrev( KT_PHP_FILE_SUFFIX ) ) === 0); // kontrola jestli soubor končí inc příponou (php)
				if ( $startsWith && $endsWith ) {
					require_once $modulePath;
				}
			}
		}
	} else {
		throw new InvalidArgumentException( __( "Hodnota \"$folder\" nesmí být prázdná a musí být adresář.", KT_DOMAIN ) );
	}
}

/**
 * Přidá k zadanému textu KT_PREFIX (na začátek)
 * @param string $text
 * @return string|null
 */
function kt_get_prefixed( $text ) {
	if ( kt_isset_and_not_empty( $text ) ) {
		return KT_PREFIX . $text;
	}
	return null;
}

/**
 * Přidá k zadanému textu KT_FORM_PREFIX (na začátek)
 * @param string $text
 * @return string|null
 */
function kt_get_form_prefixed( $text ) {
	if ( kt_isset_and_not_empty( $text ) ) {
		return KT_FORM_PREFIX . $text;
	}
	return null;
}

/**
 * Kontrola, zda zadaný text začíná KT prefixem
 * @param string $text
 * @return boolean
 */
function kt_is_prefixed( $text ) {
	if ( kt_isset_and_not_empty( $text ) ) {
		$result = strtolower( substr( $text, 0, 3 ) ) === KT_PREFIX;
		if ( $result === true ) {
			return true;
		}
	}
	return false;
}

add_filter( "template_include", "kt_load_template_from_subdir" );

/**
 * Funkce umožní načítání template souborů z předem definovaných adresářů,
 * ale neprochází široký rozsah složek, ale přesné složky v rootu šablony
 * dir - singles - všechny single soubory
 * dir - pages - všechny template pro složky a page.php samotné
 * dir - taxonomies - všechny taxonomy včetně taxonomy.php
 */
function kt_load_template_from_subdir( $template ) {
	global $post;
	global $taxonomy;
	global $cat;

	// --- single ---------------------------
	if ( is_single() ) {
		$kt_template = kt_get_single_template( $post );
		if ( $kt_template )
			return $kt_template;
	}

	// --- attachment ---------------------------
	if ( is_attachment() ) {
		$kt_template = kt_get_attachment_template( $post );
		if ( $kt_template )
			return $kt_template;
	}

	// --- page ---------------------------
	if ( is_page() ) {
		$kt_template = kt_get_page_template( $post );
		if ( $kt_template )
			return $kt_template;
	}

	// --- category ---------------------------
	if ( is_category() ) {
		$kt_template = kt_get_category_template( $cat );
		if ( $kt_template )
			return $kt_template;
		return $template;
	}

	// --- search ---------------------------
	if ( is_search() )
		return $template;

	// --- taxonomy ---------------------------
	if ( kt_isset_and_not_empty( $taxonomy ) ) {
		$kt_template = kt_get_taxonomy_template( $taxonomy );
		if ( $kt_template )
			return $kt_template;
	}

	// --- author -----------------------------
	if ( is_author() ) {
		return $template;
	}

	// --- archive ---------------------------
	/*
	 * Musí být načítán vždy poslední kvůli WP Query
	 */
	if ( is_archive() ) {
		$kt_template = kt_get_archive_template();
		if ( $kt_template )
			return $kt_template;
	}

	return $template;
}
