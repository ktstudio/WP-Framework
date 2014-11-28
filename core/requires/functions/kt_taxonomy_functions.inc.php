<?php

/**
 * Vytažení všech termů pro zadané post (ID) podle taxonomy vlastním způsobem ve formátu [ID, slug, name]
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 *
 * @global $wpdb
 * @param integer $postId
 * @param string $taxonomy
 * @param integer $parentId
 * @return array
 */
function kt_get_post_terms_by_taxonomy($postId, $taxonomy, $parentId = null) {
    global $wpdb;
    $parentPart = null;
    $isParentPart = kt_isset_and_not_empty($parentId);
    if ($isParentPart) {
        $parentPart = " AND {$wpdb->term_taxonomy}.parent = %d";
    }
    $query = "SELECT DISTINCT {$wpdb->terms}.term_id as ID, {$wpdb->terms}.slug as slug, {$wpdb->terms}.name as name
		      FROM {$wpdb->terms}
			  LEFT JOIN {$wpdb->term_taxonomy}
			  ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
			  LEFT JOIN {$wpdb->term_relationships}
			  ON {$wpdb->terms}.term_id = {$wpdb->term_relationships}.term_taxonomy_id
			  WHERE {$wpdb->term_taxonomy}.taxonomy = '%s' AND {$wpdb->term_relationships}.object_id = %d $parentPart
		      ORDER BY {$wpdb->terms}.name";
    if ($isParentPart) {
        $results = $wpdb->get_results($wpdb->prepare($query, $taxonomy, $postId, $parentId), ARRAY_A);
    } else {
        $results = $wpdb->get_results($wpdb->prepare($query, $taxonomy, $postId), ARRAY_A);
    }
    return $results;
}

/**
 * Vytažení všech termů pro zadané post (ID) vlastním způsobem ve formátu [ID, slug, name]
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 *
 * @global $wpdb
 * @param integer $postId
 * @param integer $parentId
 * @return array
 */
function kt_get_all_post_terms($postId, $parentId = null) {
    global $wpdb;
    $parentPart = null;
    $isParentPart = kt_isset_and_not_empty($parentId);
    if ($isParentPart) {
        $parentPart = " AND {$wpdb->term_taxonomy}.parent = %d";
    }
    $query = "SELECT DISTINCT {$wpdb->terms}.term_id as ID, {$wpdb->term_taxonomy}.taxonomy as taxonomy, {$wpdb->terms}.slug as slug, {$wpdb->terms}.name as name
		      FROM {$wpdb->terms}
			  LEFT JOIN {$wpdb->term_taxonomy}
			  ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
			  LEFT JOIN {$wpdb->term_relationships}
			  ON {$wpdb->terms}.term_id = {$wpdb->term_relationships}.term_taxonomy_id
			  WHERE {$wpdb->term_relationships}.object_id = %d $parentPart
		      ORDER BY {$wpdb->terms}.name";
    if ($isParentPart) {
        $results = $wpdb->get_results($wpdb->prepare($query, $postId, $parentId), ARRAY_A);
    } else {
        $results = $wpdb->get_results($wpdb->prepare($query, $postId), ARRAY_A);
    }
    return $results;
}
