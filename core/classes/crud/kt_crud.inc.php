<?php

abstract class KT_Crud implements KT_Identifiable, KT_Modelable, ArrayAccess {

    private $table = null; // Název tabulky, kde se bude provádět CRUD
    private $tablePrefix = ""; // Prefix sloupců v tabulce
    private $primaryKeyValue = null; // ID záznamu z tabulky v DB
    private $primaryKeyColumn = null; // Název sloupce, který je v tabulce určen jako primární klíč
    private $errors = array(); // Pole s chybami
    private $columns = array(); // Seznam sloupců v DB

    /**
     * Rozšíření objektu o možnost CRUD pro práci s WP DB
     * Pro komunikaci s DB je použitý WP object global $wpdb;
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @global wpdb $wpdb
     * @param string $table  // název tabulky, kterou bude CRUD obsluhovat
     * @param string $primaryKeyColumn // sloupec primárního klíču pro výběr objektu pomocí ID
     * @param string $tablePrefix // v případě používání table prefixu
     * @param int $rowId // pří použití selectu zadání ID v rámci selectu ve sloupci $primaryKeyColumn
     */

    public function __construct($table, $primaryKeyColumn, $tablePrefix = null, $rowId = null) {
        if (is_string($table)) {
            $this->setTable($table);
        } else {
            throw new KT_Not_Supported_Exception("Table is not a string");
        }

        if (is_string($primaryKeyColumn)) {
            $this->setPrimaryKeyColumn($primaryKeyColumn);
        } else {
            throw new KT_Not_Set_Argument_Exception("Primary key column is not a string");
        }

        if (is_string($tablePrefix) && KT::issetAndNotEmpty($tablePrefix)) {
            $this->setTablePrefix($tablePrefix);
        }

        $this->initColumns();

        if (KT::issetAndNotEmpty($rowId)) {
            $this->setId($rowId);
            $this->rowDataInit();
        }
    }

    // --- magic functions ------------

    public function __set($name, $value) {
        $this->setColumnValue($name, $value);
        return $this;
    }

    public function __get($name) {
        $key = $this->getColumnNameWithTablePrefix($name);
        if (array_key_exists($key, $this->columns)) {
            return $this->getColumnByName($key)->getValue();
        }
	    if (!method_exists($this,"get" . ucfirst($name))) {
		    trigger_error("Undefined CRUD property via __get(): \"$name\" for table \"{$this->getTable()}\"", E_USER_NOTICE);
	    }
        return null;
    }

    public function __isset($name) {
        $key = $this->getColumnNameWithTablePrefix($name);
        return isset($this->columns[$key]);
    }

    public function __unset($name) {
        $key = $this->getColumnNameWithTablePrefix($name);
        unset($this->columns[$key]);
    }

    // --- arrayAcces -----------------------------

    public function offsetExists($offset) {
        if (array_key_exists($offset, $this->columns)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $offset
     * @return \KT_CRUD_Column
     */
    public function offsetGet($offset) {
        return $this->getColumnByName($offset);
    }

    public function offsetSet($offset, $value) {
        
    }

    public function offsetUnset($offset) {
        
    }

    // --- abstraktní funkce ------------------

    protected abstract function initColumns();

    // --- gettery ---------------------------

    /**
     * Vrátí data objektu ze všech sloupců v DB, které se u daného záznamu nacházejí
     * 
     * @return array
     */
    public function getData() {
        if (!KT::arrayIssetAndNotEmpty($this->columns)) {
            return array();
        }
        $columnsData = array();
        foreach ($this->columns as $column) {
            $columnsData[$column->getName()] = $column->getValue();
        }
        return $columnsData;
    }

    /**
     * Vrátí id záznamu v DB v rámci definované tabulky
     * 
     * @return int|null
     */
    public function getId() {
        return $this->primaryKeyValue;
    }

    /**
     * Vrátí náze tabulky, který bude využívána při selekci dat v rámci DB
     * 
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Vrátí table column prefix v případě, že chcete používát globálního table column prefixu
     * 
     * @return string
     */
    public function getTablePrefix() {
        return $this->tablePrefix;
    }

    /**
     * Vrátí název sloupce, který reprezentuje Primary KEY column name
     * 
     * @return string
     */
    public function getPrimaryKeyColumn() {
        return $this->getTablePrefix() . $this->primaryKeyColumn;
    }

    /**
     * Vrátí sadu chyb, které byly při práci s objektem vyvolány
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Vrátí kolekci všech sloupců v DB, které danému modelu patří
     * 
     * @return type
     */
    protected function getColumns() {
        return $this->columns;
    }

    // --- settery ---------------------------

    /**
     * Nastaví data objektu ze všech sloupců v DB, které se u daného záznamu nacházejí
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $data
     * @return \KT_Crud
     */
    public function setData($data = array()) {

        if (KT::notIssetOrEmpty($data) || !is_array($data)) {
            return;
        }

        $rowIdFromData = null;

        if (array_key_exists($this->getPrimaryKeyColumn(), $data)) {
            $rowIdFromData = $data[$this->getPrimaryKeyColumn()];
        }

        if (KT::issetAndNotEmpty($rowIdFromData)) {
            $this->setId($rowIdFromData);
        }

        /* @var $column KT_CRUD_Column */
        foreach ($this->getColumns() as $column) {
            if (array_key_exists($column->getName(), $data)) {
                $column->setValue($data[$column->getName()]);
                continue;
            }
        }

        return $this;
    }

    /**
     * Nastaví id záznamu v DB, musí být jasný identifikátor záznamu.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $rowId
     * @return \KT_Crud
     * @throws KT_Not_Supported_Exception
     */
    public function setId($rowId) {

        if ($rowId === null) {
            $this->primaryKeyValue = null;
            return $this;
        }

        if (KT::isIdFormat($rowId)) {
            $rowId = KT::tryGetInt($rowId);
            $this->primaryKeyValue = $rowId;
            return $this;
        }

        throw new KT_Not_Supported_Exception("Id must be ID format or null");
    }

    /**
     * Nastaví náze tabulky, který bude využívána při selekci dat v rámci DB
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $table
     * @return \KT_Crud
     */
    protected function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * Nastaví table column prefix v případě, že chcete používat globální table column prefixu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param type $tablePrefix
     * @return \KT_Crud
     */
    protected function setTablePrefix($tablePrefix) {
        $this->tablePrefix = $tablePrefix;
        return $this;
    }

    /**
     * Nástaví název sloupce, který reprezentuje Primary KEY column name
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $column
     * @return \KT_Crud
     */
    protected function setPrimaryKeyColumn($column) {
        $this->primaryKeyColumn = $column;
        return $this;
    }

    /**
     * Nastaví sadu chyb, které byly při práci s objektem vyvolány
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $errors
     * @return \KT_Crud
     */
    protected function setErrors(array $errors) {
        if (KT::issetAndNotEmpty($errors)) {
            $this->errors = $errors;
        } else {
            $this->errors = null;
        }

        return $this;
    }

    /**
     * Nastaví kolekci sloupců CRUD modelů pro DB
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $columns
     * @return \KT_Crud
     */
    protected function setColumns(array $columns) {
        if (KT::arrayIssetAndNotEmpty($columns)) {
            return $this;
        }

        $this->columns = $columns;

        foreach ($columns as $column) {
            if ($column->getName() == $this->getPrimaryKeyColumn()) {
                $this->setId($column->getValue());
                break;
            }
        }

        return $this;
    }

    // --- veřejné funkce ----------------------

    /**
     * Do kolekce sloupců přidá nový sloupec pro komunikaci s DB
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $name - název sloupce v DB
     * @param string $type - typ sloupce @see KT_CRUD_Column
     * @param boolean $nullable
     * @return \KT_CRUD_Column
     */
    public function addColumn($name, $type = KT_CRUD_Column::TEXT, $nullable = false) {
        return $this->columns[$name] = new KT_CRUD_Column($name, $type, $nullable);
    }

    /**
     * Vrátí DB sloupec na základě jeho jméno
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $name
     * @return \KT_CRUD_Column
     */
    public function getColumnByName($name) {
        if (array_key_exists($name, $this->columns)) {
            return $this->columns[$name];
        }

        return null;
    }

    /**
     * Přidá jeden sloupec s hodnotou pro uložení dat
     * Nepřepisuje původní data, pouze přidává další do
     * kolekce dat - column => value
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @param string $value
     * @return \KT_Crud
     */
    public function setColumnValue($name, $value = null) {
        $column = $this->getColumnByName($name);
        if (isset($column)) {
            if ($column->getName() == $this->getPrimaryKeyColumn()) {
                $this->setId($value);
            }

            if ($column->getType() == KT_CRUD_Column::TEXT && KT::issetAndNotEmpty($value) && is_array($value)) {
                $value = serialize($value);
            }

            $column->setValue($value);
        }

        return $this;
    }

    /**
     * Staré volání, použijte @see setColumnValue
     * 
     * @deprecated since version 1.1
     */
    public function addNewColumnValue($name, $value = null) {
        return $this->setColumnValue($name, $value);
    }

    /**
     * Přiřadí novou sadu sloupců do kolekce stávajících dat.
     * Nepřepíše původní, provede merge dat. Přepíše hodnoty stejných sloupců
     * které jsou již v kolekci přiřazeny
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array $columns
     */
    public function addNewColumnsToValue(array $columns) {
        if (!KT::arrayIssetAndNotEmpty($columns)) {
            return $this;
        }

        foreach ($columns as $columnName => $columnValue) {
            $this->addNewColumnValue($columnName, $columnValue);
        }

        return $this;
    }

    /**
     * Na základě předaného názvu sloupce jeho uloženou hodnotu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $column
     * @return string || int
     */
    public function getColumnValue($column) {
        if (KT::arrayIsSerialized($column)) {
            return unserialize($column);
        }
        return $this->$column;
    }

    /**
     * Smaže záznam v DB na základě setWhere
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return type
     */
    public function deleteRow() {

        if (!$this->isInDatabase()) {
            return null;
        }

        global $wpdb;

        $sql = $wpdb->delete($this->getTable(), array($this->getPrimaryKeyColumn() => $this->getId()));

        if (KT::issetAndNotEmpty($sql)) {
            return $sql;
        }

        $error = $wpdb->last_error;
        $this->addError("Došlo k chybě při mazání dat v DB", $error);
        KT_Logger::error($error);
        return false;
    }

    /**
     * Funkce provede Update nebo Insert na základě nastaveného $this->rowID;
     * Pokud je funkce nastavena, provede se update v opačném případě dojde k insertu záznamu
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function saveRow() {
        if ($this->isInDatabase()) {
            return $this->updateRow();
        } else {
            return $this->insertRow();
        }
    }

    /**
     * Vrácí, zda v objektu došlo k nějaké chybě nebo ne
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return boolean
     */
    public function hasError() {
        if (KT::issetAndNotEmpty($this->getErrors())) {
            return true;
        }
        return false;
    }

    /**
     * Kontrola, zda je záznam v DB podle ID - zda má ID nastaveno.
     * Neprovádí znovu kontrolu dotazu pomocí selectu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function isInDatabase() {
        if (KT::issetAndNotEmpty($this->getId())) {
            return true;
        }

        return false;
    }

    /**
     * Vrátí požadovaný název sloupce a doplní k němu prefix
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $columnName
     * @return string
     */
    public function getColumnNameWithTablePrefix($columnName) {
        return $this->getTablePrefix() . $columnName;
    }

    public function nullUpdateFilterCallback($query) {
        return str_ireplace("'NULL'", "NULL", $query);
    }

    // --- privátní funkce ------------

    /**
     * inicializace dat na základě předaného hodnoty v construktoru
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return \KT_Crud
     */
    private function rowDataInit() {

        if (!KT::isIdFormat($this->getId())) {
            return;
        }

        global $wpdb;

        $query = "SELECT * FROM {$this->getTable()} WHERE {$this->getPrimaryKeyColumn()} = %d";

        $result = $wpdb->get_row($wpdb->prepare($query, $this->getId()), ARRAY_A);

        if ($result === null) {
            $this->addError("Došlo k chybě při výběru dat z DB", $wpdb->last_error);
            $this->setId(null);
            return;
        }

        $this->setData($result);

        return $this;
    }

    /**
     * Naplní data do DB a nastavení $this->rowID dle nově vloženého řádku
     * Dojde ke vložení všech dat $this->data
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return mixed boolean | int - 1 při bezchybném vložení
     */
    private function insertRow() {

        global $wpdb;

        $updateValue = $this->getColumnsWithFormatsData();

        // Povolení filtru, který ze "NULL" strinogové hodnoty udělá v SQL dotazu běžný NULL pro nullable sloupce
        add_filter("query", array($this, "nullUpdateFilterCallback"));
        $sql = $wpdb->insert($this->getTable(), $updateValue->columns, $updateValue->formats);
        remove_filter("query", array($this, "nullUpdateFilterCallback")); // Zrušení předešlého filtru

        if (KT::issetAndNotEmpty($sql)) {
            $this->setId($wpdb->insert_id);
            return $this->getId();
        }

        $error = $wpdb->last_error;
        $this->addError("Došlo k chybě při vkládání dat do DB", $error);
        KT_Logger::error($error);
        return false;
    }

    /**
     * Provede update řádku na základě setWhere clausule
     * updatuje všechny parametry v $this->data
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return mixed int | false
     */
    private function updateRow() {

        if (!$this->isInDatabase()) {
            return false;
        }

        global $wpdb;

        $updateValue = $this->getColumnsWithFormatsData();

        // Povolení filtru, který ze "NULL" strinogové hodnoty udělá v SQL dotazu běžný NULL pro nullable sloupce
        add_filter("query", array($this, "nullUpdateFilterCallback"));
        $sql = $wpdb->update($this->getTable(), $updateValue->columns, array($this->getPrimaryKeyColumn() => $this->getId()), $updateValue->formats);
        remove_filter("query", array($this, "nullUpdateFilterCallback")); // Zrušení předešlého filtru

        if ($sql) {
            return true;
        }
        if (KT::issetAndNotEmpty($wpdb->last_error)) {
            $this->addError("Došlo k chybě při změně dat v DB", $wpdb->last_error);
            return false;
        }
        return true; // nedošlo ke změnám
    }

    /**
     * Vrátí pole s formáty pro bezpečnější práci s daty
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    private function getColumnsWithFormatsData() {
        $formats = array();
        $columns = array();

        foreach ($this->getColumns() as $column) {
            $name = $column->getName();
            if ($name == $this->getPrimaryKeyColumn()) {
                continue;
            }

            $type = $column->getType();
            $value = $column->getValue();

            $isNullable = $column->getNullable();
            if (is_null($value) && !$isNullable) {
                continue; // může "zachránit", resp. vyřešit výchozí hodnota v DB
            }
            if ($isNullable && $value == "") {
                $formats[] = "%s";
                $columns[$column->getName()] = "NULL";
                continue;
            }

            switch ($type) {
                case KT_CRUD_Column::INT:
                    $formats[] = "%d";
                    $columns[$column->getName()] = KT::tryGetInt($value);
                    break;
                case KT_CRUD_Column::BIGINT:
                    $formats[] = "%f";
                    $columns[$column->getName()] = floor(KT::tryGetFloat($value)); // simulace "long"
                    break;
                case KT_CRUD_Column::FLOAT:
                    $formats[] = "%f";
                    $columns[$column->getName()] = KT::tryGetFloat($value);
                    break;
                case KT_CRUD_Column::DATE:
                    $formats[] = "%s";
                    $columns[$column->getName()] = KT::dateConvert($value, "Y-m-d");
                    break;
                case KT_CRUD_Column::DATETIME:
                    $formats[] = "%s";
                    $columns[$column->getName()] = KT::dateConvert($value, "Y-m-d H:i:s");
                    break;
                default:
                    $formats[] = "%s";
                    $columns[$column->getName()] = $value;
                    break;
            }
        }

        $data = new stdClass();
        $data->formats = $formats;
        $data->columns = $columns;
        return $data;
    }

    /**
     * Přidá objektu Error msg na základě předanýách parametrů
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $message - kód / hash chyby
     * @param mixed $content - popis chyby
     * @return \KT_Crud
     */
    private function addError($message, $content) {
        $this->errors[] = array(
            'message' => $message,
            'data' => $content
        );

        return $this;
    }

}
