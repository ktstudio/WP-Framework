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
class KT_Catalog_Base_Model extends KT_Crud implements KT_Modelable {

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
        return $this->title;
    }

    /**
     * Nastaví (povinný) titulek
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $title
     * @return \KT_Catalog_Base_Model
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setTitle($title) {
        if (kt_isset_and_not_empty($title)) {
            $this->title = $title;
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
        return $this->description;
    }

    /**
     * Nastaví (nepovinný) popis(ek)
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $description
     * @return \KT_Catalog_Base_Model
     */
    public function setDescription($description) {
        $this->description = $description;
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
        return $this->code;
    }

    /**
     * Nastaví (povinný) kód
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string|integer $code
     * @return \KT_Catalog_Base_Model
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setCode($code) {
        if (kt_isset_and_not_empty($code)) {
            $this->code = $code;
            return $this;
        }
        throw new KT_Not_Set_Argument_Exception("code");
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
        return $this->visibility;
    }

    /**
     * Nastaví (povinnou) viditelnost
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param boolean $visibility
     * @return \KT_Catalog_Base_Model
     * @throws InvalidArgumentException
     * @throws KT_Not_Set_Argument_Exception
     */
    public function setVisibility($visibility) {
        if (isset($visibility)) {
            if ($visibility === true || $visibility == 1) {
                $this->visibility = 1;
                return $this;
            } elseif ($visibility === false || $visibility == 0) {
                $this->visibility = 0;
                return $this;
            }
            throw new InvalidArgumentException("visibility");
        }
        throw new KT_Not_Set_Argument_Exception("visibility");
    }

}
