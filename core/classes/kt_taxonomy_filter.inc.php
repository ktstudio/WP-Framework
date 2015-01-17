<?php

/**
 * Podle Add Taxonomy filter to Custom Post Type
 * TODO: upravit více podle svého :) hlavně definice paramterů opačně taxonomy => post_types
 * 
 * @author Ohad Raz <admin@bainternet.info>
 * @link http://en.bainternet.info/2013/add-taxonomy-filter-to-custom-post-type Bainternet
 */
class KT_Taxonomy_Filter {

    private $parameters;
    private $showAllText;

    function __construct($parameters = array(), $showAllText = null) {
        $this->parameters = $parameters;
        $this->showAllText = $showAllText ? : KT_ALL_TEXT;
        // Adding a Taxonomy Filter to Admin List for a Custom Post Type
        add_action("restrict_manage_posts", array($this, "kt_taxonomy_filter_restrict_manage_posts"));
    }

    public function kt_taxonomy_filter_restrict_manage_posts() {
        // only display these taxonomy filters on desired custom post_type listings
        global $typenow;
        $types = array_keys($this->parameters);
        if (in_array($typenow, $types)) {
            // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
            $filters = $this->parameters[$typenow];
            foreach ($filters as $tax_slug) {
                // retrieve the taxonomy object
                $tax_obj = get_taxonomy($tax_slug);
                $tax_name = $tax_obj->labels->name;

                // output html for taxonomy dropdown filter
                echo "<select name='" . strtolower($tax_slug) . "' id='" . strtolower($tax_slug) . "' class='postform'>";
                echo "<option value=''>{$this->showAllText}</option>";
                $this->generate_taxonomy_options($tax_slug, 0, 0, (isset($_GET[strtolower($tax_slug)]) ? $_GET[strtolower($tax_slug)] : null));
                echo "</select>";
            }
        }
    }

    /**
     * generate_taxonomy_options generate dropdown
     * @param  string  $tax_slug
     * @param  string  $parent
     * @param  integer $level
     * @param  string  $selected
     */
    public function generate_taxonomy_options($tax_slug, $parent = "", $level = 0, $selected = null) {
        $args = array("show_empty" => 1);
        if (!is_null($parent)) {
            $args = array("parent" => $parent);
        }
        $terms = get_terms($tax_slug, $args);
        $tab = "";
        for ($i = 0; $i < $level; $i ++) {
            $tab .= "-";
        }
        foreach ($terms as $term) {
            // output each select option line, check against the last $_GET to show the current option selected
            echo '<option value=' . $term->slug, $selected == $term->slug ? ' selected="selected"' : '', '>' . $tab . $term->name . ' (' . $term->count . ')</option>';
            $this->generate_taxonomy_options($tax_slug, $term->term_id, $level + 1, $selected);
        }
    }

}
