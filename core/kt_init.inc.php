<?php

define( "KT_CORE_PATH", path_join( KT_BASE_PATH, "core" ) );
define( "KT_CORE_CLASSES_PATH", path_join( KT_CORE_PATH, "classes" ) );
define( "KT_CORE_CSS_PATH", path_join( KT_CORE_PATH, "css" ) );
define( "KT_CORE_EXCEPTIONS_PATH", path_join( KT_CORE_PATH, "exceptions" ) );
define( "KT_CORE_IMAGES_PATH", path_join( KT_CORE_PATH, "images" ) );
define( "KT_CORE_INTERFACES_PATH", path_join( KT_CORE_PATH, "interfaces" ) );
define( "KT_CORE_JS_PATH", path_join( KT_CORE_PATH, "js" ) );
define( "KT_CORE_REQUIRES_PATH", path_join( KT_CORE_PATH, "requires" ) );

define( "KT_CORE_URL", path_join( KT_BASE_URL, "core" ) );
define( "KT_CORE_CLASSES_URL", path_join( KT_CORE_URL, "classes" ) );
define( "KT_CORE_CSS_URL", path_join( KT_CORE_URL, "css" ) );
define( "KT_CORE_EXCEPTIONS_URL", path_join( KT_CORE_URL, "exceptions" ) );
define( "KT_CORE_IMAGES_URL", path_join( KT_CORE_URL, "images" ) );
define( "KT_CORE_INTERFACES_URL", path_join( KT_CORE_URL, "interfaces" ) );
define( "KT_CORE_JS_URL", path_join( KT_CORE_URL, "js" ) );
define( "KT_CORE_REQUIRES_URL", path_join( KT_CORE_URL, "requires" ) );

// --- WORDPRESS ----------------------------
// post types
define( "KT_WP_POST_KEY", "post" );
define( "KT_WP_PAGE_KEY", "page" );
define( "KT_WP_ATTACHMENT_KEY", "attachment" );
define( "KT_WP_REVISION_KEY", "revision" );
define( "KT_WP_NAV_MENU_ITEM_KEY", "nav_menu_item" );
// taxonomies
define( "KT_WP_TAG_KEY", "post_tag" );
define( "KT_WP_CATEGORY_KEY", "category" );
// images size
define( "KT_WP_IMAGE_SIZE_THUBNAIL", "thumbnail");
define( "KT_WP_IMAGE_SIZE_MEDIUM", "medium");
define( "KT_WP_IMAGE_SIZE_LARGE", "large");
define( "KT_WP_IMAGE_SIZE_ORIGINAL", "original");
// scripts
define( "KT_WP_JQUERY_SCRIPT" , "jquery");
define( "KT_WP_JQUERY_UI_DATEPICKER_SCRIPT" , "jquery-ui-datepicker");
define( "KT_WP_JQUERY_UI_TOOLTIP_SCRIPT", "jquery-ui-tooltip" );

kt_include_all( KT_CORE_REQUIRES_PATH );
