<?php

// --- registrace ------------------------

define("KT_CORE_PATH", path_join(KT_BASE_PATH, "core"));
define("KT_CORE_CLASSES_PATH", path_join(KT_CORE_PATH, "classes"));
define("KT_CORE_CSS_PATH", path_join(KT_CORE_PATH, "css"));
define("KT_CORE_EXCEPTIONS_PATH", path_join(KT_CORE_PATH, "exceptions"));
define("KT_CORE_IMAGES_PATH", path_join(KT_CORE_PATH, "images"));
define("KT_CORE_INTERFACES_PATH", path_join(KT_CORE_PATH, "interfaces"));
define("KT_CORE_JS_PATH", path_join(KT_CORE_PATH, "js"));
define("KT_CORE_REQUIRES_PATH", path_join(KT_CORE_PATH, "requires"));

define("KT_CORE_URL", path_join(KT_BASE_URL, "core"));
define("KT_CORE_CLASSES_URL", path_join(KT_CORE_URL, "classes"));
define("KT_CORE_CSS_URL", path_join(KT_CORE_URL, "css"));
define("KT_CORE_EXCEPTIONS_URL", path_join(KT_CORE_URL, "exceptions"));
define("KT_CORE_IMAGES_URL", path_join(KT_CORE_URL, "images"));
define("KT_CORE_INTERFACES_URL", path_join(KT_CORE_URL, "interfaces"));
define("KT_CORE_JS_URL", path_join(KT_CORE_URL, "js"));
define("KT_CORE_REQUIRES_URL", path_join(KT_CORE_URL, "requires"));

// --- export ------------------------
define("KT_WP_UPLOADS_PATH", path_join(WP_CONTENT_DIR, "uploads"));
define("KT_WP_UPLOADS_URL", path_join(WP_CONTENT_URL, "uploads"));
define("KT_UPLOADS_EXPORT_PATH", path_join(KT_WP_UPLOADS_PATH, "kt"));
define("KT_UPLOADS_EXPORT_URL", path_join(KT_WP_UPLOADS_URL, "kt"));
// --- WORDPRESS ------------------------
// --- post types ------------------------
define("KT_WP_POST_KEY", "post");
define("KT_WP_PAGE_KEY", "page");
define("KT_WP_ATTACHMENT_KEY", "attachment");
define("KT_WP_REVISION_KEY", "revision");
define("KT_WP_NAV_MENU_ITEM_KEY", "nav_menu_item");
// --- post meta ------------------------
define("KT_WP_META_KEY_PAGE_TEMPLATE", "_wp_page_template");
// --- theme support ------------------------
define("KT_WP_THEME_SUPPORT_POST_THUMBNAILS_KEY", "post-thumbnails");
// --- post type support ------------------------
define("KT_WP_POST_TYPE_SUPPORT_TITLE_KEY", "title");
define("KT_WP_POST_TYPE_SUPPORT_EDITOR_KEY", "editor");
define("KT_WP_POST_TYPE_SUPPORT_AUTHOR_KEY", "author");
define("KT_WP_POST_TYPE_SUPPORT_THUMBNAIL_KEY", "thumbnail");
define("KT_WP_POST_TYPE_SUPPORT_EXCERPT_KEY", "excerpt");
define("KT_WP_POST_TYPE_SUPPORT_TRACKBACKS_KEY", "trackbacks");
define("KT_WP_POST_TYPE_SUPPORT_CUSTOM_FIELDS_KEY", "custom-fields");
define("KT_WP_POST_TYPE_SUPPORT_COMMENTS_KEY", "comments");
define("KT_WP_POST_TYPE_SUPPORT_REVISIONS_KEY", "revisions");
define("KT_WP_POST_TYPE_SUPPORT_PAGE_ATTRIBUTES_KEY", "page-attributes");
define("KT_WP_POST_TYPE_SUPPORT_POST_FORMATS_KEY", "post-formats");
// --- taxonomies ------------------------
define("KT_WP_TAG_KEY", "post_tag");
define("KT_WP_CATEGORY_KEY", "category");
// --- images size ------------------------
define("KT_WP_IMAGE_SIZE_THUBNAIL", "thumbnail");
define("KT_WP_IMAGE_SIZE_MEDIUM", "medium");
define("KT_WP_IMAGE_SIZE_LARGE", "large");
define("KT_WP_IMAGE_SIZE_ORIGINAL", "original");
// --- scripts ------------------------
define("KT_WP_JQUERY_SCRIPT", "jquery");
define("KT_WP_JQUERY_UI_DATEPICKER_SCRIPT", "jquery-ui-datepicker");
define("KT_WP_JQUERY_UI_SLIDER_SCRIPT", "jquery-ui-slider");
define("KT_WP_JQUERY_UI_TOOLTIP_SCRIPT", "jquery-ui-tooltip");

// --- inicializace ------------------------

kt_include_all(KT_CORE_REQUIRES_PATH);
