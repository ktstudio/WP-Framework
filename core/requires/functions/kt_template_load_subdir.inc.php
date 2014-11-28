<?php

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
    $page_template = get_post_meta($post->ID, '_wp_page_template', true);

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
