<?php

/**
 * Základní presenter pro všechny presentery
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
abstract class KT_Presenter_Base implements KT_Presentable {

    private $model = null;
    private static $currentQueryLoopIndex;
    private static $currentItemsLoopIndex;

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
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return KT_Modelable
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Nastavení jiného modelu v obecné podobě
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param KT_Modelable $model
     * @return \KT_Presenter_Base
     */
    public function setModel(KT_Modelable $model) {
        $this->model = $model;
        return $this;
    }

    /**
     * Vrátí aktuální index v rámci výpisu šablon pomocí @see theQueryLoops
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public static function getCurrentQueryLoopIndex() {
        return self::$currentQueryLoopIndex;
    }

    /**
     * Vrátí aktuální index v rámci výpisu šablon pomocí @see theItemsLoops
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public static function getCurrentItemsLoopIndex() {
        return self::$currentItemsLoopIndex;
    }

    // --- veřejné funkce ---------------------

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param WP_Query $query
     * @param string $loopName
     * @param array $clearfixes pole clearfixů k printu podle klíče (modulo)
     */
    public static function theQueryLoops(WP_Query $query, $loopName, array $clearfixes = null) {
        if (KT::issetAndNotEmpty($query) && $query->have_posts()) {
            $isClearfixes = KT::arrayIssetAndNotEmpty($clearfixes);
            self::$currentQueryLoopIndex = 0;
            while ($query->have_posts()) : $query->the_post();
                global $post;
                include(locate_template("loops/loop-" . $loopName . ".php"));
                self::$currentQueryLoopIndex++;
                if ($isClearfixes) {
                    self::theClearfixes($clearfixes, $index);
                }
            endwhile;
            self::$currentQueryLoopIndex = null;
            wp_reset_postdata();
        }
    }

    /**
     * Výpis postů podle zadané query v zadané loopě
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $items
     * @param string $loopName
     * @param mixed int|null $count
     * @param mixed int|null $offset
     * @param array $clearfixes pole clearfixů k printu podle klíče (modulo)
     */
    public static function theItemsLoops(array $items, $loopName, $count = null, $offset = null, array $clearfixes = null) {
        if (KT::arrayIssetAndNotEmpty($items)) {
            $isClearfixes = KT::arrayIssetAndNotEmpty($clearfixes);
            self::$currentItemsLoopIndex = 0;
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
                self::$currentItemsLoopIndex++;
                if ($isClearfixes) {
                    self::theClearfixes($clearfixes, $index);
                }
            }
            self::$currentItemsLoopIndex = null;
            wp_reset_postdata();
        }
    }

    /**
     * Vypíše clearfixy podle (zadaného) indexu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $clearfixes
     * @param int $index
     */
    public static function theClearfixes(array $clearfixes, $index) {
        foreach ($clearfixes as $clearfixModulo => $clearfixOutput) {
            if ($index % $clearfixModulo === 0) {
                echo $clearfixOutput;
            }
        }
    }

    // --- neveřejné funkce ---------------------
}
