<?php

/**
 * Základní "Multi" presenter pro všechny přehledy, úvodní a další složené stránky apod.
 * Původní funkce pro výpis loop přesunuty staticky na @see \KT_Presenter_Base
 * 
 * @deprecated since version 1.2
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
    // --- neveřejné funkce ---------------------

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @param WP_Query $query
     * @param string $loopName
     */
    protected function theQueryLoops(WP_Query $query, $loopName) {
        if (KT::issetAndNotEmpty($query) && $query->have_posts()) {
            while ($query->have_posts()) : $query->the_post();
                global $post;
                include(locate_template("loops/loop-" . $loopName . ".php"));
            endwhile;
            wp_reset_postdata();
        }
    }

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @param WP_Query $query
     * @param string $loopName
     * @param mixed int|null $count
     * @param mixed int|null $offset
     */
    protected function theItemsLoops(array $items, $loopName, $count = null, $offset = null) {
        if (KT::arrayIssetAndNotEmpty($items)) {
            $index = 0;
            if (KT::tryGetInt($offset) > 0) {
                $items = array_slice($items, $offset);
            }
            if (KT::tryGetInt($count) > 0) {
                $items = array_slice($items, 0, $count);
            }
            foreach ($items as $item) {
                global $post;
                $post = $item;
                include(locate_template("loops/loop-$loopName.php"));
                $index++;
            }
            wp_reset_postdata();
        }
    }

}
