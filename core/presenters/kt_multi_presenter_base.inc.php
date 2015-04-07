<?php

/**
 * Základní "Multi" presenter pro všechny přehledy, úvodní a další složené stránky apod.
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Multi_Presenter_Base extends KT_Presenter_Base {

    function __construct(KT_Modelable $model = null) {
        parent::__construct($model);
    }

    // --- getry & setry ------------------------ 
    // --- veřejné funkce ---------------------

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @param WP_Query $query
     * @param string $loopName
     */
    public function theLoops(WP_Query $query, $loopName) {
        if (KT::issetAndNotEmpty($query) && $query->have_posts()) {
            while ($query->have_posts()) : $query->the_post();
                global $post;
                include(locate_template("loops/loop-" . $loopName . ".php"));
            endwhile;
            wp_reset_postdata();
        }
    }

    // --- neveřejné funkce ---------------------
}
