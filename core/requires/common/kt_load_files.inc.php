<?php

define("KT_FORM_VALIDATION_SCRIPT", "kt-form-validation-script");
define("KT_CORE_SCRIPT", "kt-core-script");
define("KT_JQUERY_MAGNIFIC_POPUP_SCRIPT", "jquery-magnific-popup-script");
define("KT_JQUERY_UNVEIL_SCRIPT", "jquery-unveil-script");
define("KT_JQUERY_DATETIMEPICKER_SCRIPT", "jquery-datetimepicker-script");
define("KT_MAGNIFIC_POPUP_SCRIPT", "kt-magnific-popup-script");
define("KT_CHOSEN_SCRIPT", "chosen-script");
define("KT_GOOGLE_MAP_SCRIPT", "kt-google-map-script");
define("KT_DYNAMIC_FIELDSET_SCRIPT", "kt-dynamic-fieldset-script");

define("KT_WPFW_LOGIN_STYLE", "kt-wpfw-login-style");
define("KT_CORE_STYLE", "kt-core-style");
define("KT_JQUERY_UI_STYLE", "jquery-ui-style");
define("KT_JQUERY_DATETIMEPICKER_STYLE", "jquery-datetimepicker-style");
define("KT_MAGNIFIC_POPUP_STYLE", "magnific-popup-style");
define("KT_CHOSEN_STYLE", "chosen-style");

/**
 * Nahrání souborů do admin screen
 */
add_action("admin_enqueue_scripts", "kt_core_admin_scripts_callback");

function kt_core_admin_scripts_callback() {
    // styles
    wp_enqueue_style(KT_CORE_STYLE);
    wp_enqueue_style(KT_JQUERY_UI_STYLE);
    wp_enqueue_style(KT_JQUERY_DATETIMEPICKER_STYLE);
    wp_enqueue_style(KT_CHOSEN_STYLE);
    // sripts
    wp_enqueue_script(KT_WP_JQUERY_UI_SLIDER_SCRIPT);
    wp_enqueue_script(KT_WP_JQUERY_UI_TOOLTIP_SCRIPT);
    wp_enqueue_script(KT_JQUERY_DATETIMEPICKER_SCRIPT);
    wp_enqueue_script(KT_FORM_VALIDATION_SCRIPT);
    wp_enqueue_script(KT_CHOSEN_SCRIPT);
    wp_enqueue_script(KT_CORE_SCRIPT);
}

/**
 * Registrace scriptů a stylů
 */
add_action("init", "kt_core_register_scripts_and_styles_handlers_callback");

function kt_core_register_scripts_and_styles_handlers_callback() {
    // sripts
    wp_register_script(KT_FORM_VALIDATION_SCRIPT, path_join(KT_CORE_JS_URL, "jquery.form-validation.js"), array(KT_WP_JQUERY_SCRIPT), "", true);
    wp_register_script(KT_JQUERY_UNVEIL_SCRIPT, path_join(KT_CORE_JS_URL, "jquery.unveil.min.js"), array(KT_WP_JQUERY_SCRIPT), "", true);
    wp_register_script(KT_JQUERY_MAGNIFIC_POPUP_SCRIPT, path_join(KT_CORE_JS_URL, "jquery.magnific-popup.min.js"), array(KT_WP_JQUERY_SCRIPT), "", true);
	wp_register_script(KT_JQUERY_DATETIMEPICKER_SCRIPT, path_join(KT_CORE_JS_URL, "jquery.datetimepicker.full.min.js"), array(KT_WP_JQUERY_SCRIPT), "", true);
    wp_register_script(KT_MAGNIFIC_POPUP_SCRIPT, path_join(KT_CORE_JS_URL, "kt-magnific-popup.min.js"), array(KT_WP_JQUERY_SCRIPT, KT_JQUERY_MAGNIFIC_POPUP_SCRIPT), "", true);
    wp_register_script(KT_GOOGLE_MAP_SCRIPT, path_join(KT_CORE_JS_URL, "googlemap.min.js"));
    wp_register_script(KT_CHOSEN_SCRIPT, path_join(KT_CORE_JS_URL, "chosen.jquery.min.js"), array(KT_WP_JQUERY_SCRIPT), "", true);
    wp_register_script(KT_CORE_SCRIPT, path_join(KT_CORE_JS_URL, "kt-core.min.js"), array(KT_WP_JQUERY_SCRIPT, KT_JQUERY_DATETIMEPICKER_SCRIPT, KT_WP_JQUERY_UI_TOOLTIP_SCRIPT, KT_FORM_VALIDATION_SCRIPT, KT_CHOSEN_SCRIPT), "", true);
    wp_register_script(KT_DYNAMIC_FIELDSET_SCRIPT, path_join(KT_CORE_JS_URL, "kt-dynamic-fields.js"), [], "", true);
    wp_localize_script(KT_DYNAMIC_FIELDSET_SCRIPT, "kt_urls", ["admin" => admin_url(), "ajaxurl" => admin_url('admin-ajax.php')]);
    // styles
    wp_register_style(KT_WPFW_LOGIN_STYLE, path_join(KT_CORE_CSS_URL, "kt-wpfw-login.min.css"));
    wp_register_style(KT_CORE_STYLE, path_join(KT_CORE_CSS_URL, "kt-core.min.css"));
    wp_register_style(KT_JQUERY_UI_STYLE, path_join(KT_CORE_CSS_URL, "jquery-ui.min.css"));
    wp_register_style(KT_JQUERY_DATETIMEPICKER_STYLE, path_join(KT_CORE_CSS_URL, "jquery.datetimepicker.min.css"));
    wp_register_style(KT_MAGNIFIC_POPUP_STYLE, path_join(KT_CORE_CSS_URL, "magnific-popup.min.css"));
    wp_register_style(KT_CHOSEN_STYLE, path_join(KT_CORE_CSS_URL, "chosen.min.css"));
}
