<?php

/**
 * Třída pro obsluhu vlastních (KT) term meta na základě metadata API
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz 
 */
class KT_Termmeta {

    const TYPE = "ktterm";

    private static $isActive = false;

    // --- getry & setry ------------------------

    /**
     * Kontrola, zda jsou (KT) term meta aktivní, @see activate()
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return boolean
     */
    public static function getIsActive() {
        return self::$isActive;
    }

    // --- veřejné metody ------------------------

    /**
     * Aktivuje vlastní (KT) term meta (v rámci WPDB a metadata API) 
     * 
     * Pozn.: je třeba mít založenou příslušnou tabulku v DB, viz kt_core.sql
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @global \WP_DB $wpdb
     */
    public static function activate() {
        if (!self::getIsActive()) {
            global $wpdb;
            $id = self::TYPE . "meta";
            $wpdb->$id = "kt_termmeta";
            self::$isActive = true;
        }
    }

    /**
     * Vrátí všechny meta (podle zadaného prefixu) ve tvaru klíč => hodnota
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @global \WP_DB $wpdb
     * @param string $prefix
     * @return array
     */
    public static function getAllData($termId, $prefix = null) {
        if (self::getIsActive()) {
            global $wpdb;
            $results = array();
            $prepareData[] = $termId;
            $id = self::TYPE . "meta";
            $query = "SELECT meta_key, meta_value FROM {$wpdb->$id} WHERE ktterm_id = %d";
            if (isset($prefix)) {
                $query .= " AND meta_key LIKE '%s'";
                $prepareData[] = "{$prefix}%";
            }
            $metas = $wpdb->get_results($wpdb->prepare($query, $prepareData), ARRAY_A);
            foreach ($metas as $meta) {
                $results[$meta["meta_key"]] = $meta["meta_value"];
            }
            return $results;
        }
        return null;
    }

    /**
     * Wrapper metody get_metadata z metadata API pro zjednodušení práce v rámci (KT) term meta
     * 
     * Pozn.: výchozí hodnota posledního parametru $single je zde prohozena false->true
     * 
     * @see get_metadata
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $termId
     * @param type $metaKey
     * @param type $single
     * @return mixed string|array
     */
    public static function getData($termId, $metaKey = "", $single = true) {
        if (self::getIsActive()) {
            return get_metadata(self::TYPE, $termId, $metaKey, $single);
        }
        return null;
    }

    /**
     * Wrapper metody add_metadata z metadata API pro zjednodušení práce v rámci (KT) term meta
     * 
     * @see add_metadata
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $termId
     * @param string $metaKey
     * @param string $metaValue
     * @param boolean $unique
     * @return int|bool
     */
    public static function addData($termId, $metaKey, $metaValue, $unique = false) {
        if (self::getIsActive()) {
            return add_metadata(self::TYPE, $termId, $metaKey, $metaValue, $unique);
        }
        return null;
    }

    /**
     * Wrapper metody update_metadata z metadata API pro zjednodušení práce v rámci (KT) term meta
     * 
     * @see update_metadata
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $termId
     * @param string $metaKey
     * @param string $metaValue
     * @param boolean $previousValue
     * @return int|bool
     */
    public static function updateData($termId, $metaKey, $metaValue, $previousValue = "") {
        if (self::getIsActive()) {
            return update_metadata(self::TYPE, $termId, $metaKey, $metaValue, $previousValue);
        }
        return null;
    }

    /**
     * Wrapper metody delete_metadata z metadata API pro zjednodušení práce v rámci (KT) term meta
     * 
     * @see delete_metadata
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $termId
     * @param string $metaKey
     * @param string $metaValue
     * @param boolean $deleteAll
     * @return boolean
     */
    public static function deleteData($termId, $metaKey, $metaValue = "", $deleteAll = false) {
        if (self::getIsActive()) {
            return delete_metadata(self::TYPE, $termId, $metaKey, $metaValue, $deleteAll);
        }
        return null;
    }

}
