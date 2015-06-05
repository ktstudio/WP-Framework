<?php

/**
 * Základní presenter pro všechny presentery
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
abstract class KT_Presenter_Base implements KT_Presentable {

    private $model = null;

    public function __construct(KT_Modelable $model = null) {
        kt_check_loaded(); // kontrola KT Frameworku
        if (KT::issetAndNotEmpty($model)) {
            $this->setModel($model);
        }
    }

    // --- getry & setry ------------------------ 

    /**
     * Vrátí zadaný v obecné podobě
     * 
     * @return KT_Modelable
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Nastavení jiného modelu v obecné podobě
     * 
     * @param KT_Modelable $model
     * @return \KT_Presenter_Base
     */
    public function setModel(KT_Modelable $model) {
        $this->model = $model;
        return $this;
    }

    // --- veřejné funkce ---------------------

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @param WP_Query $query
     * @param string $loopName
     * @param array $clearfixes pole clearfixů k printu podle klíče (modulo)
     */
    public static function theQueryLoops(WP_Query $query, $loopName, array $clearfixes = null) {
        if (KT::issetAndNotEmpty($query) && $query->have_posts()) {
            $isClearfixes = KT::arrayIssetAndNotEmpty($clearfixes);
            $index = 0;
            while ($query->have_posts()) : $query->the_post();
                global $post;
                include(locate_template("loops/loop-" . $loopName . ".php"));
                $index++;
                if ($isClearfixes) {
                    foreach ($clearfixes as $clearfixModulo => $clearfixOutput) {
                        if ($index % $clearfixModulo === 0) {
                            echo $clearfixOutput;
                        }
                    }
                }
            endwhile;
            wp_reset_postdata();
        }
    }

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @param array $items
     * @param string $loopName
     * @param mixed int|null $count
     * @param mixed int|null $offset
     */
    public static function theItemsLoops(array $items, $loopName, $count = null, $offset = null) {
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

    // --- neveřejné funkce ---------------------
}
