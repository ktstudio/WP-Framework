<?php

/**
 * Základní struktura určená pro obecné výpisy z databáze - číselníky apod.
 * Obsahuje titulek, popis, kód a viditelnost, přičemž ideálně by v rámci tabulky
 * měly být implementovány právě všechny tyto 4 položky, nicméně v případě potřeby
 * je možné použít jen některé, pak se ale nesmí v kódu připřazovat ty nepoužité...
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Catalog_Model_Base extends KT_Crud implements KT_Modelable {

    const ID_COLUMN = "id";
    const TITLE_COLUMN = "title";
    const DESCRIPTION_COLUMN = "description";
    const CODE_COLUMN = "code";
    const MENU_ORDER_COLUMN = "menu_order";
    const VISIBILITY_COLUMN = "visibility";

    /**
     * Výchozí konstruktor ala @see KT_Crud = DB table (row)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $table
     * @param string $primaryKeyColumn
     * @param string $tablePrefix
     * @param integer $rowId
     */
    function __construct($table, $primaryKeyColumn, $tablePrefix = null, $rowId = null) {
        parent::__construct($table, $primaryKeyColumn, $tablePrefix, $rowId);
    }

    /**
     * Vrátí (povinný) titulek
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getTitle() {
        return $title = $this->getColumnValue(self::TITLE_COLUMN);
    }

    /**
     * Nastaví (povinný) titulek
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $title
     * @return \KT_Catalog_Model_Base
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setTitle($title) {
        if (KT::issetAndNotEmpty($title)) {
            $this->addNewColumnToData(self::TITLE_COLUMN, $title);
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("title");
    }

    /**
     * Vrátí (nepovinný) popis(ek)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getDescription() {
        return $description = $this->getColumnValue(self::DESCRIPTION_COLUMN);
    }

    /**
     * Nastaví (nepovinný) popis(ek)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $description
     * @return \KT_Catalog_Model_Base
     */
    public function setDescription($description = null) {
        $this->addNewColumnToData(self::DESCRIPTION_COLUMN, $description);
        return $this;
    }

    /**
     * Vrátí (povinný) kód (pokud je používán)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string|integer
     */
    public function getCode() {
        return $code = $this->getColumnValue(self::CODE_COLUMN);
    }

    /**
     * Nastaví (povinný) kód
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string|integer $code
     * @return \KT_Catalog_Model_Base
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setCode($code) {
        if (KT::issetAndNotEmpty($code)) {
            $this->addNewColumnToData(self::CODE_COLUMN, $code);
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("code");
    }

    /**
     * Vrátí (povinné volitelné) pořadí (pokud je používáno)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getMenuOrder() {
        return $menuOrder = $this->getColumnValue(self::MENU_ORDER_COLUMN);
    }

    /**
     * Nastaví (povinné volitelné) pořadí (pokud je používáno)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $menuOrder
     * @return \KT_Catalog_Model_Base
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setMenuOrder($menuOrder) {
        $menuOrder = KT::tryGetInt($menuOrder);
        if (is_integer($menuOrder)) {
            $this->addNewColumnToData(self::MENU_ORDER_COLUMN, $menuOrder);
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("menu_order");
    }

    /**
     * Vrátí (povinnou) viditelnost (pokud je používána)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function getVisibility() {
        return $visibility = $this->getColumnValue(self::VISIBILITY_COLUMN);
    }

    /**
     * Nastaví (povinnou) viditelnost
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $visibility
     * @return \KT_Catalog_Model_Base
     * @throws InvalidArgumentException
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setVisibility($visibility) {
        if (isset($visibility)) {
            if ($visibility === true || $visibility == 1) {
                $this->addNewColumnToData(self::VISIBILITY_COLUMN, 1);
                return $this;
            } elseif ($visibility === false || $visibility == 0) {
                $this->addNewColumnToData(self::VISIBILITY_COLUMN, 0);
                return $this;
            }
            throw new InvalidArgumentException("visibility");
        }
        throw new KT_Not_Set_Argument_Exception("visibility");
    }

}
