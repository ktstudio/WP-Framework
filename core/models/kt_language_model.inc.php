<?php

/**
 * Základní struktura pro jednotlivé jazyky pro další použití v rámci systému
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_Language_Model extends KT_Catalog_Base_Model {

    const TABLE = "kt_shop_languages";
    const ORDER_COLUMN = self::TITLE_COLUMN;
    const PREFIX = "kt_shop_languages";
    const FORM_PREFIX = "kt-shop-languages";
    
    // --- DB Sloupce ----------------
    
    const DECIMAL_POINTS_COLUMN = "decimal_point";
    const THOUSANDS_SEPARATOR = "thousands_separator";

    /**
     * Výchozí konstruktor, který dodá údaje pro @see KT_Crud
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $rowId
     */
    public function __construct($rowId = null) {
        parent::__construct(self::TABLE, self::ID_COLUMN, null, $rowId);
    }

    /**
     * Vrátí (vlastní) znak desetinné čárky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|char
     */
    public function getDecimalPoint() {
        return $this->decimal_point;
    }

    /**
     * Nastaví (vlastní) znak desetinné čárky
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string|char $decimalPoint
     * @return \KT_Language_Model
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setDecimalPoint($decimalPoint) {
        if (kt_isset_and_not_empty($decimalPoint)) {
            $this->decimal_point = $decimalPoint;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("decimalPoint");
    }

    /**
     * Vrátí (vlastní) znak oddělovače tisíců
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|char
     */
    public function getThousandsSeparator() {
        return $this->thousands_separator;
    }

    /**
     * Nastaví (vlastní) znak oddělovače tisíců
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string|char $thousandsSeparator
     * @return \KT_Language_Model
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setThousandsSeparator($thousandsSeparator) {
        if (kt_isset_and_not_empty($thousandsSeparator)) {
            $this->thousands_separator = $thousandsSeparator;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("thousandsSeparator");
    }

    /**
     * Zformátování zadaného čísla, resp. doplnění správné des. čárky a tečky podle jazyka
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $number
     * @param integer $decimals počet požadovaných desetinných míst
     * @return numeric|null
     */
    public function getNumberFormat($number, $decimals) {
        if (kt_isset_and_not_empty($number) && is_numeric($number) && kt_isset_and_not_empty($decimals) && is_numeric($decimals)) {
            $decimals = $this->getDecimals();
            return number_format($number, $decimals, $this->getDecimalPoint(), $this->getThousandsSeparator());
        }
        return null;
    }

    /**
     * Výpis zformátování zadaného čísla, resp. doplnění správné des. čárky a tečky podle jazyka
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $number
     * @param integer $decimals počet požadovaných desetinných míst
     * @return integer|null
     */
    public function theNumberFormat($number, $decimals) {
        echo $this->getNumberFormat($number, $decimals);
    }

}
