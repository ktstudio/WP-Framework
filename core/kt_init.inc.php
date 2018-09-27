<?php

// --- registrace ------------------------

define("KT_CORE_PATH", path_join(KT_BASE_PATH, "core"));
define("KT_CORE_CLASSES_PATH", path_join(KT_CORE_PATH, "classes"));
define("KT_CORE_CSS_PATH", path_join(KT_CORE_PATH, "css"));
define("KT_CORE_EXCEPTIONS_PATH", path_join(KT_CORE_PATH, "exceptions"));
define("KT_CORE_IMAGES_PATH", path_join(KT_CORE_PATH, "images"));
define("KT_CORE_INTERFACES_PATH", path_join(KT_CORE_PATH, "interfaces"));
define("KT_CORE_JS_PATH", path_join(KT_CORE_PATH, "js"));
define("KT_CORE_LANGUAGES_PATH", path_join(KT_BASE_PATH, "languages"));
define("KT_CORE_REQUIRES_PATH", path_join(KT_CORE_PATH, "requires"));

define("KT_CORE_URL", path_join(KT_BASE_URL, "core"));
define("KT_CORE_CLASSES_URL", path_join(KT_CORE_URL, "classes"));
define("KT_CORE_CSS_URL", path_join(KT_CORE_URL, "css"));
define("KT_CORE_EXCEPTIONS_URL", path_join(KT_CORE_URL, "exceptions"));
define("KT_CORE_IMAGES_URL", path_join(KT_CORE_URL, "images"));
define("KT_CORE_INTERFACES_URL", path_join(KT_CORE_URL, "interfaces"));
define("KT_CORE_JS_URL", path_join(KT_CORE_URL, "js"));
define("KT_CORE_LANGUAGES_URL", path_join(KT_CORE_URL, "languages"));
define("KT_CORE_REQUIRES_URL", path_join(KT_CORE_URL, "requires"));

// --- logování ------------------------
/* @deprecated deprecated since version 1.6 */
define("KT_CORE_LOG_MIN_LEVEL", KT_Log_Level_Enum::INFO);
/* @deprecated deprecated since version 1.6 */
define("KT_CORE_LOG_ONLY_SIGNED_USERS", true);
/* @deprecated deprecated since version 1.6 */
define("KT_CORE_LOG_TOOLS_ADMIN_PAGE", true);

// --- export ------------------------
define("KT_WP_UPLOADS_PATH", path_join(WP_CONTENT_DIR, "uploads"));
define("KT_WP_UPLOADS_URL", path_join(WP_CONTENT_URL, "uploads"));
define("KT_UPLOADS_EXPORT_PATH", path_join(KT_WP_UPLOADS_PATH, "kt"));
define("KT_UPLOADS_EXPORT_URL", path_join(KT_WP_UPLOADS_URL, "kt"));

// --- WORDPRESS ------------------------
define("KT_PROJECT_NOTICES_ACTION", "kt_project_notices");
define("KT_META_KEY_SINGLE_TEMPLATE", "kt-single-template");
// --- post types ------------------------
define("KT_WP_POST_KEY", "post");
define("KT_WP_PAGE_KEY", "page");
define("KT_WP_TAXONOMY_KEY", "taxonomy");
define("KT_WP_COMMENT_KEY", "comment");
define("KT_WP_ATTACHMENT_KEY", "attachment");
define("KT_WP_REVISION_KEY", "revision");
define("KT_WP_NAV_MENU_ITEM_KEY", "nav_menu_item");
define("KT_WP_NAV_MENU_ITEM_PARENT_META_KEY", "_menu_item_menu_item_parent");
// --- post meta ------------------------
define("KT_WP_META_KEY_PAGE_TEMPLATE", "_wp_page_template");
define("KT_WP_META_KEY_THUMBNAIL_ID", "_thumbnail_id");
// --- options ------------------------
define("KT_WP_OPTION_KEY_FRONT_PAGE", "page_on_front");
define("KT_WP_OPTION_KEY_POSTS_PAGE", "page_for_posts");
define("KT_WP_OPTION_KEY_PRIVACY_POLICY_PAGE", "wp_page_for_privacy_policy");
// --- theme support ------------------------
define("KT_WP_THEME_SUPPORT_POST_FORMATS_KEY", "post-formats");
define("KT_WP_THEME_SUPPORT_POST_THUMBNAILS_KEY", "post-thumbnails");
define("KT_WP_THEME_SUPPORT_CUSTOM_BACKGROUND_KEY", "custom-background");
define("KT_WP_THEME_SUPPORT_CUSTOM_HEADER_KEY", "custom-header");
define("KT_WP_THEME_SUPPORT_AUTOMATIC_FEED_LINKS_KEY", "automatic-feed-links");
define("KT_WP_THEME_SUPPORT_HTML5_KEY", "html5");
define("KT_WP_THEME_SUPPORT_TITLE_TAG_KEY", "title-tag");
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
// --- post formats ------------------------
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_ASIDE", "aside");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_GALLERY", "gallery");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_LINK", "link");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_IMAGE", "image");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_QUOTE", "quote");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_STATUS", "status");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_VIDEO", "video");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_AUDIO", "audio");
define("KT_WP_POST_TYPE_SUPPORTED_FORMAT_CHAT", "chat");
// --- taxonomies ------------------------
define("KT_WP_POST_FORMAT_KEY", "post_format");
define("KT_WP_TAG_KEY", "post_tag");
define("KT_WP_CATEGORY_KEY", "category");
// --- images size ------------------------
define("KT_WP_IMAGE_SIZE_THUBNAIL", "thumbnail");
define("KT_WP_IMAGE_SIZE_MEDIUM", "medium");
define("KT_WP_IMAGE_SIZE_LARGE", "large");
define("KT_WP_IMAGE_SIZE_ORIGINAL", "original");
// --- schedule event ------------------------
define("KT_WP_RECURRENCE_HOURLY", "hourly");
define("KT_WP_RECURRENCE_TWICEDAILY", "twicedaily");
define("KT_WP_RECURRENCE_DAILY", "daily");
// --- scripts ------------------------
define("KT_WP_JQUERY_SCRIPT", "jquery");
define("KT_WP_JQUERY_UI_DATEPICKER_SCRIPT", "jquery-ui-datepicker");
define("KT_WP_JQUERY_UI_SLIDER_SCRIPT", "jquery-ui-slider");
define("KT_WP_JQUERY_UI_TOOLTIP_SCRIPT", "jquery-ui-tooltip");

// --- inicializace ------------------------

kt_include_all(KT_CORE_REQUIRES_PATH);

kt_load_textdomain("KT_CORE_DOMAIN", KT_CORE_PATH);
