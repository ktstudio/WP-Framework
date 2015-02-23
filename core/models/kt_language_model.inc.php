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
        if (KT::issetAndNotEmpty($decimalPoint)) {
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
        if (KT::issetAndNotEmpty($thousandsSeparator)) {
            $this->thousands_separator = $thousandsSeparator;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("thousandsSeparator");
    }

    /**
     * Zformátování zadaného čísla podle aktuálních parametrů jazyka
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param integer $number
     * @param integer $decimals počet požadovaných desetinných míst
     * @return mixed numeric|null
     */
    public function getCurrentFormatedNumber($number, $decimals) {
        return self::getFormatedNumber($number, $decimals, $this->getDecimalPoint(), $this->getThousandsSeparator());
    }

    /**
     * Zformátování zadaného čísla podle zadných parametrů
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param float $number
     * @param integer $decimals počet požadovaných desetinných míst
     * @param char $decimalPoint
     * @param char $thousandsSeparator
     * @return mixed float|null
     */
    public static function getFormatedNumber($number, $decimals = KT_CURRENCY_DECIMAL_COUNT, $decimalPoint = KT_LANGUAGE_DECIMAL_POINT, $thousandsSeparator = KT_LANGUAGE_THOUSANDS_SEPARATOR) {
        if (KT::issetAndNotEmpty($number) && is_numeric($number) && is_numeric($decimals)) {
            return number_format($number, intval($decimals), $decimalPoint, $thousandsSeparator);
        }
        return null;
    }

}
