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
    private static $currentQueryLoopCount;
    private static $currentItemsLoopIndex;
    private static $currentItemsLoopCount;
    private static $isFrontPageHome;

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
     * Vrátí aktuální číslo v rámci výpisu šablon pomocí @see theQueryLoops
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public static function getCurrentQueryLoopNumber() {
        return self::getCurrentQueryLoopIndex() + 1;
    }

    /**
     * Vrátí aktuální počet postů v rámci výpisu šablon pomocí @see theQueryLoops
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int
     */
    public static function getCurrentQueryLoopCount() {
        return self::$currentQueryLoopCount;
    }

    /**
     * Ověření, zda je aktuální Query loopa první @see theQueryLoops
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int
     */
    public static function getIsCurrentQueryLoopFirst() {
        return self::getCurrentQueryLoopIndex() === 0;
    }

    /**
     * Ověření, zda je aktuální Query loopa poslední @see theQueryLoops
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public static function getIsCurrentQueryLoopLast() {
        return self::getCurrentQueryLoopNumber() === self::getCurrentQueryLoopCount();
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

    /**
     * Vrátí aktuální číslo v rámci výpisu šablon pomocí @see theItemsLoops
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return int
     */
    public static function getCurrentItemsLoopNumber() {
        return self::getCurrentItemsLoopIndex() + 1;
    }

    /**
     * Vrátí aktuální počet postů v rámci výpisu šablon pomocí @see theItemsLoops
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int
     */
    public static function getCurrentItemsLoopCount() {
        return self::$currentItemsLoopCount;
    }

    /**
     * věření, zda je aktuální Item(s) loopa první @see theItemsLoops
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int
     */
    public static function getIsCurrentItemsLoopFirst() {
        return self::getCurrentItemsLoopIndex() === 0;
    }

    /**
     * Ověření, zda je aktuální Item(s) loopa poslední @see theItemsLoops
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return int
     */
    public static function getIsCurrentItemsLoopLast() {
        return self::getCurrentItemsLoopNumber() === self::getCurrentItemsLoopCount();
    }

    /**
     * Vrátí označení zda je právě aktivní Front nebo Home page
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public static function getIsFrontPageHome() {
        if (isset(self::$isFrontPageHome)) {
            return self::$isFrontPageHome;
        }
        return self::$isFrontPageHome = (is_front_page() || is_home());
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
            self::$currentQueryLoopCount = count($query->get_posts());
            while ($query->have_posts()) : $query->the_post();
                global $post;
                include(locate_template("loops/loop-" . $loopName . ".php"));
                self::$currentQueryLoopIndex++;
                if ($isClearfixes) {
                    self::theClearfixes($clearfixes, self::$currentQueryLoopIndex);
                }
            endwhile;
            self::$currentQueryLoopIndex = null;
            self::$currentQueryLoopCount = null;
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
            self::$currentItemsLoopCount = count($items);
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
                    self::theClearfixes($clearfixes, self::$currentItemsLoopIndex);
                }
            }
            self::$currentItemsLoopIndex = null;
            self::$currentItemsLoopCount = null;
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
        if (isset($index)) {
            foreach ($clearfixes as $clearfixModulo => $clearfixOutput) {
                if ($index % $clearfixModulo === 0) {
                    echo $clearfixOutput;
                }
            }
        }
    }

    // --- neveřejné funkce ---------------------
}
