<?php

/**
 * Podle Custom Post Types columns
 * TODO: upravit více podle svého :) hlavně definice paramterů opačně taxonomy => post_types
 * @author Ohad Raz <admin@bainternet.info>
 * @link http://en.bainternet.info/2013/custom-post-types-columns Bainternet
 */
class KT_Admin_Columns {

    const DEFAULT_THUMBNAIL_SIZE = 32;
    const THUMBNAIL_TYPE_KEY = "thumbnail";
    const POST_PROPERTY_TYPE_KEY = "post_property";
    const POST_META_TYPE_KEY = "post_meta";
    const TAXONOMY_TYPE_KEY = "taxonomy";
    const LABEL_PARAM_KEY = "label";
    const SIZE_PARAM_KEY = "size";
    const TAXONOMY_PARAM_KEY = "taxonomy";
    const PROPERTY_PARAM_KEY = "property";
    const METAKEY_PARAM_KEY = "meta_key";
    const FILTER_FUNCTION = "filter_function";
    const SORTABLE_PARAM_KEY = "sortable";
    const TYPE_PARAM_KEY = "type";
    const ORDERBY_PARAM_KEY = "orderby";
    const INDEX_PARAM_KEY = "index";

    private $columns = array();
    private $removeColumns = array();
    private $sortableColumns = array();
    private $postType;

    function __construct($postType) {
        if (kt_not_isset_or_empty($postType)) {
            throw new KT_Not_Set_Argument_Exception("postType");
        }
        $this->postType = $postType;
        add_filter("manage_{$postType}_posts_columns", array($this, "addColumns"), 50);
        add_filter("manage_{$postType}_posts_columns", array($this, "removeColumns"), 60);
        add_action("manage_{$postType}_posts_custom_column", array($this, "customColumn"), 50, 2);
        add_filter("manage_edit-{$postType}_sortable_columns", array($this, "sortableColumns"), 50);
        add_filter("pre_get_posts", array($this, "orderbyColumn"), 50);
    }

    public function addColumns($defaults) {
        global $typenow;
        if ($this->postType == $typenow) {
            $columns = array();
            $indexes = array();
            foreach ($this->columns as $key => $args) {
                $columns[$key] = $args[self::LABEL_PARAM_KEY];
                $index = kt_try_get_int($args[self::INDEX_PARAM_KEY]);
                if (is_numeric($index) && $index >= 0) {
                    $indexes[$key] = $index;
                }
            }
            $defaults = array_merge($defaults, $columns);
            foreach ($indexes as $key => $index) { // případné repozicování na základě zadaných indexů
                $column = $defaults[$key]; // mezipaměť pro vložení
                $defaults = kt_array_remove_by_key($defaults, $key); // odstranění ze současné pozice
                $defaults = kt_array_insert($defaults, $index, $key, $column); // nová požadovaná pozice
            }
        }
        return $defaults;
    }

    public function removeColumns($columns) {
        foreach ($this->removeColumns as $key) {
            if (isset($columns[$key])) {
                unset($columns[$key]);
            }
        }
        return $columns;
    }

    public function sortableColumns($columns) {
        global $typenow;
        if ($this->postType == $typenow) {
            foreach ($this->sortableColumns as $key => $args) {
                if ($args[self::SORTABLE_PARAM_KEY]) {
                    $columns[$key] = $key;
                } else {
                    kt_array_remove_by_key($columns, $key);
                }
            }
        }
        return $columns;
    }

    public function customColumn($columnName, $postId) {
        if (isset($this->columns[$columnName])) {
            $this->theColumn($postId, $this->columns[$columnName]);
        }
    }

    private function theColumn($postId, $args) {
        $columnType = $args[self::TYPE_PARAM_KEY];
        switch ($columnType) {
            case self::THUMBNAIL_TYPE_KEY:
                if (has_post_thumbnail($postId)) {
                    the_post_thumbnail($args[self::SIZE_PARAM_KEY]);
                } else {
                    echo KT_EMPTY_TEXT;
                }
                break;
            case self::POST_PROPERTY_TYPE_KEY:
                $post = get_post($postId);
                if (kt_isset_and_not_empty($post)) {
                    $property = $args[self::PROPERTY_PARAM_KEY];
                    $value = $post->$property;
                    $filterFunction = $args[self::FILTER_FUNCTION];
                    if (kt_isset_and_not_empty($filterFunction)) {
                        $value = apply_filters("$filterFunction", $value);
                    }
                    echo $value;
                } else {
                    echo KT_EMPTY_TEXT;
                }
                break;
            case self::POST_META_TYPE_KEY:
                $postMeta = get_post_meta($postId, $args[self::METAKEY_PARAM_KEY], true);
                if (kt_isset_and_not_empty($postMeta)) {
                    $value = $postMeta;
                    if (kt_isset_and_not_empty($filterFunction)) {
                        $value = apply_filters($filterFunction, $value);
                    }
                    echo $value;
                } else {
                    echo KT_EMPTY_TEXT;
                }
                break;
            case self::TAXONOMY_TYPE_KEY:
                $postType = get_post_type($postId);
                $terms = get_the_terms($postId, $args[self::TAXONOMY_PARAM_KEY]);
                if (!empty($terms)) {
                    foreach ($terms as $term) {
                        $href = "edit.php?post_type={$postType}&{$args[self::TAXONOMY_PARAM_KEY]}={$term->slug}";
                        $name = esc_html(sanitize_term_field("name", $term->name, $term->term_id, $args[self::TAXONOMY_PARAM_KEY], "edit"));
                        $post_terms[] = '<a href="' . $href . '">' . $name . '</a>';
                    }
                    echo join(", ", $post_terms);
                } else {
                    echo KT_EMPTY_TEXT;
                }
                break;
            default:
                throw new KT_Not_Supported_Exception(__("Typ sloupce: $columnType", KT_DOMAIN));
        }
    }

    public function orderbyColumn($query) {
        if (is_admin()) {
            $orderby = $query->get(self::ORDERBY_PARAM_KEY);
            $keys = array_keys((array) $this->sortableColumns);
            if (in_array($orderby, $keys)) {
                if ($this->sortableColumns[$orderby][self::TYPE_PARAM_KEY] == self::POST_META_TYPE_KEY) {
                    $query->set(self::METAKEY_PARAM_KEY, $orderby);
                    $query->set(self::ORDERBY_PARAM_KEY, $this->sortableColumns[$orderby][self::ORDERBY_PARAM_KEY]);
                }
            }
        }
    }

    public function addColumn($key, $args) {
        $defaults = array(
            self::LABEL_PARAM_KEY => KT_EMPTY_TEXT,
            self::SIZE_PARAM_KEY => array(self::DEFAULT_THUMBNAIL_SIZE, self::DEFAULT_THUMBNAIL_SIZE),
            self::TAXONOMY_PARAM_KEY => "",
            self::METAKEY_PARAM_KEY => "",
            self::SORTABLE_PARAM_KEY => false,
            self::TYPE_PARAM_KEY => self::POST_META_TYPE_KEY,
            self::ORDERBY_PARAM_KEY => self::METAKEY_PARAM_KEY,
            self::INDEX_PARAM_KEY => null,
        );
        $this->columns[$key] = array_merge($defaults, $args);

        if ($this->columns[$key][self::SORTABLE_PARAM_KEY]) {
            $this->sortableColumns[$key] = $this->columns[$key];
        }
    }

    public function removeColumn($key) {
        $this->removeColumns[] = $key;
    }

}
