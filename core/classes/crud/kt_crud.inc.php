<?php

abstract class KT_Crud implements KT_Identifiable, KT_Modelable {

    private $table = null; // Název tabulky, kde se bude provádět CRUD
    private $tablePrefix = ""; // Prefix sloupců v tabulce
    private $primaryKeyValue = null; // ID záznamu z tabulky v DB
    private $primaryKeyColumn = null; // Název sloupce, který je v tabulce určen jako primární klíč
    private $data = array(); // Pole s daty 'column_name' => 'value'
    private $errors = array(); // Pole s chybami

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

        if (KT::issetAndNotEmpty($rowId)) {
            $this->setId($rowId);
            $this->rowDataInit();
        }
    }

    // --- magic functions ------------

    public function __set($name, $value) {
        $key = $this->getColumnNameWithTablePrefix($name);
        $this->data[$key] = $value;
    }

    public function __get($name) {
        $key = $this->getColumnNameWithTablePrefix($name);
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        $trace = debug_backtrace();
        trigger_error(
                'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'], E_USER_NOTICE);
        return null;
    }

    public function __isset($name) {
        $key = $this->getColumnNameWithTablePrefix($name);
        return isset($this->data[$key]);
    }

    public function __unset($name) {
        $key = $this->getColumnNameWithTablePrefix($name);
        unset($this->data[$key]);
    }

    // --- gettery ---------------------------

    /**
     * Vrátí data objektu ze všech sloupců v DB, které se u daného záznamu nacházejí
     * 
     * @return array
     */
    public function getData() {
        return $this->data;
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

        $this->data = wp_parse_args($data, $this->data);

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

    // --- veřejné funkce ----------------------

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
    public function addNewColumnToData($name, $value = null) {
        $currentDataCollection = $this->getData();
        $currentDataCollection[$name] = $value;
        $this->setData($currentDataCollection);

        return $this;
    }

    /**
     * Přiřadí novou sadu sloupců do kolekce stávajících dat.
     * Nepřepíše původní, provede merge dat.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param array $columns
     */
    public function addNewColumnsToData(array $columns) {
        if (KT::issetAndNotEmpty($columns)) {
            $currentDataCollection = $this->getData();
            $newDataCollection = array_merge($currentDataCollection, $columns);
            $this->setData($newDataCollection);
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
            return;
        }

        global $wpdb;

        $sql = $wpdb->delete($this->getTable(), array($this->getPrimaryKeyColumn() => $this->getId()));

        if (KT::issetAndNotEmpty($sql)) {
            return $sql;
        }

        $this->addError("Došlo k chybě při vkládání dat do DB", $wpdb->last_error);
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
            $this->updateRow();
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
            $this->addError("Došlo k chybě při výběru dat z DB", $wpdb->db->last_error);
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

        $sql = $wpdb->insert($this->getTable(), $this->getData(), $this->getArrayOfFieldsFormat());

        if (KT::issetAndNotEmpty($sql)) {
            $this->setId($wpdb->insert_id);
            return $this->getId();
        }

        $this->addError("Došlo k chybě při vkládání dat do DB", $wpdb->last_error);
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
            return;
        }

        global $wpdb;

        $sql = $wpdb->update($this->getTable(), $this->getData(), array($this->getPrimaryKeyColumn() => $this->getId()));

        if ($sql) {
            return $sql;
        }

        $this->addError("Došlo k chybě při změně dat v DB", $wpdb->last_error);
        return false;
    }

    /**
     * Vrátí pole s formáty pro bezpečnější práci s daty
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getArrayOfFieldsFormat() {

        $formats = array();

        foreach ($this->data as $key => $value) {
            if (is_int($value)) {
                $formats[] = "%d";
                continue;
            } elseif (is_float($value)) {
                $formats[] = "%f";
                continue;
            } elseif (is_string($value)) {
                $formats[] = "%s";
                continue;
            }
            throw new InvalidArgumentException($key);
        }

        return $formats;
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
