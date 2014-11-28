<?php

/**
 * Vypíše obrázek podle ID  v případné požadované velikosti
 * @param int $id
 * @param string $alt
 * @param string $size
 */
function kt_the_image_by_source($id, $alt, $size = "thumbnail") {
    if (kt_isset_and_not_empty($id) && $id > 0) {
        $source = wp_get_attachment_image_src($id, $size);
        if (kt_isset_and_not_empty($source) && is_array($source)) {
            $url = $source[0];
            if ($size !== "large") {
                $large = wp_get_attachment_image_src($id, "large");
                $url = $large[0];
            }
            echo "\n<a href=\"$url\" title=\"$alt\" class=\"image-popup-vertical-fit\">\n";
            echo '<img src="' . $source[0] . '" width="' . $source[1] . '" height="' . $source[2] . '" alt="' . $alt . '" class="wp-post-image" />';
            echo "\n</a>\n";
        }
    }
}

/**
 * Vrátí odkaz na obrázek, který je ve složce images v rootu šablony
 * 
 * @author Tomáš Kocifaj <kocifaj@ktstudio.cz>
 * @link http://www.ktstudio.cz
 * 
 * @param string $fileName
 * @return string
 */
function kt_get_image_theme($fileName) {
    return $url = get_template_directory_uri() . "/images/" . $fileName;
}
