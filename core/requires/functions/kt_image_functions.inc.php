<?php

/**
 * Vypíše obrázek podle ID  v případné požadované velikosti
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * 
 * @param int $id
 * @param string $alt
 * @param string $size
 */
function kt_get_attachment_image_html($id, array $linkArgs = array(), array $imageArgs = array(), $size = KT_WP_IMAGE_SIZE_THUBNAIL, $tabsCount = 0) {
    $output = null;
    if (kt_is_id_format($id) > 0) {
        $source = wp_get_attachment_image_src($id, $size);
        if (kt_array_isset_and_not_empty($source)) {
            $imageUrl = $linkUrl = $source[0];
            $imageWidth = $source[1];
            $imageHeight = $source[2];
            if ($size !== KT_WP_IMAGE_SIZE_ORIGINAL) {
                $original = wp_get_attachment_image_src($id, KT_WP_IMAGE_SIZE_ORIGINAL);
                $linkUrl = $original[0];
            }
            foreach ($linkArgs as $key => $value) {
                $linkAttributes .= " $key=\"$value\"";
            }
            foreach ($imageArgs as $key => $value) {
                $imageAttributes .= " $key=\"$value\"";
            }
            $output .= kt_get_tabs_indent($tabsCount, "<a href=\"$linkUrl\"$linkAttributes>", true);
            $output .= kt_get_tabs_indent($tabsCount + 1, "<img src=\"$imageUrl\" width=\"$imageWidth\" height=\"$imageHeight\"$imageAttributes />", true);
            $output .= kt_get_tabs_indent($tabsCount, "</a>", true, true);
        }
    }
    return $output;
}

/**
 * Vrátí odkaz na obrázek, který je ve složce images v rootu šablony
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 * 
 * @param string $fileName
 * @return string
 */
function kt_get_image_theme($fileName) {
    return $url = get_template_directory_uri() . "/images/" . $fileName;
}

/**
 * Nahrazení všech datových zdrojů tagů obrázků v zadaném HTML kódu za lazy (na základě skriptu unveil)
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 * 
 * @param string $html
 * @return string
 */
function kt_replace_images_lazy_src($html) {
    if (kt_isset_and_not_empty($html)) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML($html);
        $imageTags = $dom->getElementsByTagName("img");
        $processedImages = array();
        foreach ($imageTags as $imageTag) {
            $oldSrc = $imageTag->getAttribute("src");
            if (in_array($oldSrc, $processedImages)) {
                continue; // tento obrázek byl již zpracován
            }
            array_push($processedImages, $oldSrc);
            $newSrc = KT_CORE_IMAGES_URL . "/transparent.png";
            $html = str_replace("src=\"$oldSrc\"", "src=\"$newSrc\" data-src=\"$oldSrc\"", $html);
        }
    }
    return $html;
}
