<?php

class KT_CRUD_Admin_List {

    const GET_ACTION = "action";
    const GET_ACTION_CREATE = "create";

    private $className = null;
    private $columnList = array();
    private $newItemButton = false;
    private $repository = null;
    private $sortable = false;
    private $templateTitle = null;

    /**
     * @param string $className - jméno objektu, který se má v rámci tabulky zobrazovat. Musí být KT_CRUD
     * @param type $tableName - náze tabulky, kde jsou záznamy uloženy
     */
    public function __construct($className, $tableName) {
        $repository = new KT_Repository($className, $tableName);
        $this->setRepository($repository)
                ->setClassName($className);

        return $this;
    }

    // --- gettery a settery ------------------

    /**
     * @return string
     */
    public function getClassName() {
        return $this->className;
    }

    /**
     * Nástaví nazev CRUD class, s kterou se bude pracovat
     * 
     * @param string $className
     * @return \KT_CRUD_Admin_List
     */
    private function setClassName($className) {
        $this->className = $className;
        return $this;
    }

    /**
     * @return array
     */
    private function getColumnList() {
        usort($this->columnList, function($a, $b) {
            if ($a->getPosition() == $b->getPosition())
                return 0;
            return ($a->getPosition() > $b->getPosition()) ? 1 : -1;
        });

        return $this->columnList;
    }

    /**
     * Nastaví kolekci sloupců, která se bude v rámci tabulky zobrazovat
     * kolekce objektů KT_CRUD_Column
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param array $columnList
     * @return \KT_CRUD_Admin_List
     */
    public function setColumnList(array $columnList) {
        $this->columnList = $columnList;
        return $this;
    }

    /**
     * @return boolean
     */
    private function getNewItemButton() {
        return $this->newItemButton;
    }

    /**
     * Nastaví, zda se má nad tabulkou zobrazit tlačítko, pro přidání nového CRUD záznamu
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param boolean $newItemButton
     * @return \KT_CRUD_Admin_List
     */
    function setNewItemButton($newItemButton = true) {
        $this->newItemButton = $newItemButton;
        return $this;
    }

    /**
     * @return \KT_Repository
     */
    public function getRepository() {
        return $this->repository;
    }

    /**
     * Nastaví repositář, který se bude provádět selekci dat.
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param \KT_Repository $repository
     * @return \KT_CRUD_Admin_List
     */
    public function setRepository(KT_Repository $repository) {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateTitle() {
        return $this->templateTitle;
    }

    /**
     * Nastaví titulek pro templatu, který bude list vykreslovat
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param type $templateTitle
     * @return \KT_CRUD_Admin_List
     */
    public function setTemplateTitle($templateTitle) {
        $this->templateTitle = $templateTitle;
        return $this;
    }
    
    /**
     * @return type
     */
    private function getSortable() {
        return $this->sortable;
    }

    /**
     * Nastaví, zda se má být seznam řaditelný pomocí DragAndDrop technologie
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstduio.cz
     * 
     * @param boolean $sortable
     * @return \KT_CRUD_Admin_List
     */
    public function setSortable($sortable = true) {
        $this->sortable = $sortable;
        return $this;
    }

    
    // --- veřejné funkce ------------------

    /**
     * Do kolekce sloupců přidá nový sloupec
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param string $name
     * @return \KT_CRUD_Admin_Column $name
     */
    public function addColumn($name) {
        $column = $this->columnList[$name] = new KT_CRUD_Admin_Column($name);
        $maxColumnCount = count($this->columnList);
        $column->setPosition($maxColumnCount);
        return $column;
    }

    /**
     * Do stávající kolekce sloupců přidá další kolekci (provede merge - nepřepíše původní)
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param array $columnCollection
     * @return \KT_CRUD_Admin_List
     */
    public function addColumnsToCollection(array $columnCollection) {
        $currentColumnCollection = $this->getColumnList();
        if (KT::issetAndNotEmpty($columnCollection)) {
            $mergedColumnCollection = array_merge($columnCollection, $currentColumnCollection);
            $this->setColumnList($mergedColumnCollection);
        } else {
            $this->setColumnList($columnCollection);
        }

        return $this;
    }

    /**
     * Odstraní z kolekce sloupců na základě jeho názvu
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param string $columnName
     * @return \KT_CRUD_Admin_List
     */
    public function removeColumnFromCollection($columnName) {
        if (isset($this->columnList[$columnName])) {
            unset($this->columnList[$columnName]);
        }

        return $this;
    }

    /**
     * Vrátí sloupec z kolekce na základě jeho názvu
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @param string $columnName
     * @return \KT_CRUD_Admin_Column
     */
    public function getColumnByName($columnName) {
        if (isset($this->columnList[$columnName])) {
            return $this->columnList[$columnName];
        }

        return null;
    }

    /**
     * Vrátí obsah celé stránky
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return html
     */
    public function getContent() {
        $html = "";

        $html .= $this->getTamplageTitleContent(); // Titulek stránky
        $html .= $this->getAddButtonContent(); // Tlačítko pro přidání nového záznamu

        if (array_key_exists("page", $_GET)) {
            $pageName = $_GET["page"];
            $html = apply_filters("kt_crud_admin_list_before_table_" . $pageName, $html);
        }

        $html .= $this->getTable(); // Tabulka s daty

        return $html;
    }
    
    /**
     * Vrátí, TRUE | FALSE zda je aktivované řazení položek pomocí DragAndDrop
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return type
     */
    public function isSortable(){
        return $this->getSortable();
    }

    // --- protected funkce ------------------

    /**
     * Vrátí titulek layoutu, pokud byl definován.
     * 
      @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getTamplageTitleContent() {
        if (KT::notIssetOrEmpty($this->getTemplateTitle())) {
            return "";
        }

        return $html = "<h2>" . $this->getTemplateTitle() . "</h2>";
    }

    /**
     * Vrátí odkaz v podobě tlačítko pro přidání nového odkazu na CRUD listu
     * do aktuálního odkazu přidá ACTION = CREATE
     * 
     * Prověří, zda je nový button vyžadován. Pokud ano, vykreslí pokud ne, vrátí prázdný string
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getAddButtonContent() {
        if ($this->getNewItemButton()) {
            if (array_key_exists("page", $_GET)) {
                $pageSlug = $_GET["page"];
                $createUrl = menu_page_url($pageSlug, false) . "&" . self::GET_ACTION . "=" . self::GET_ACTION_CREATE;
                return "<a href=\"$createUrl\" id=\"addCrudButtonList\" class=\"button\">" . __("Přidat nový záznam", KT_DOMAIN) . "</a>";
            }
        }
        return null;
    }

    /**
     * Vrátí HTML s celou tabulku
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getTable() {
        $html = "";
        $sortableActivate = "sortableTable";

        if (!$this->hasListColumns()) {
            return $html;
        }
        
        if($this->isSortable()){
            $sortableActivate = "data-sortable=\"true\"";
        }

        $tableId = strtolower($this->getClassName());

        $html .= "<table id=\"{$tableId}\" class=\"wp-list-table widefat item-list\" data-class-name=\"{$this->getClassName()}\" $sortableActivate cellspacing=\"0\">";
        $html .= $this->getTableHeader();
        $html .= $this->getTableBody();
        $html .= "</table>";

        return $html;
    }

    /**
     * Vrátí HTML hlavičku na základě zadané kolekce sloupců
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getTableHeader() {
        $html = "";
        $columnList = $this->getColumnList();

        $html .= "<thead>";
        $html .= "<tr>";
        
        if($this->isSortable()){
            $html .= "<th>". __("Pořadí", KT_DOMAIN)."</th>";
        }
        
        foreach ($columnList as $column) {
            /** @var $column \KT_CRUD_Column */
            $class = KT::issetAndNotEmpty($column->getCssClass()) ? " class=\"{$column->getCssClass()}\"" : "";
            $html .= "<th$class>{$column->getLabel()}</th>";
        }
        $html .= "</tr>";
        $html .= "</thead>";

        return $html;
    }

    /**
     * Vrátí HTML tělo na základě zadané kolekce sloupců
     * 
     * @return string
     */
    protected function getTableBody() {
        $html = "";
        $updatedRowId = null;
        $columnCollection = $this->getColumnList();
        $repository = $this->getRepository()->selectData();

        if (!$repository->haveItems()) {
            return $html;
        }

        $className = $this->getClassName();

        if (array_key_exists($className::ID_COLUMN, $_GET)) {
            $updatedRowId = $_GET[$className::ID_COLUMN];
        }

        $html .= "<tbody>";

        while ($repository->haveItems()) : $item = $repository->theItem();

            $updatedClass = $item->getId() == $updatedRowId ? " class=\"updated\"" : "";

            $html .= "<tr id=\"row-{$item->getId()}\"$updatedClass data-item-id=\"{$item->getId()}\">";
            
            if($this->isSortable()){
                $html .= "<td class=\"sortable\"><span class=\"dashicons dashicons-menu\"></span></td>";
            }
            
            foreach ($columnCollection as $column) {

                $class = KT::issetAndNotEmpty($column->getCssClass()) ? " class=\"{$column->getCssClass()}\"" : "";

                $html .= "<td$class>";
                $html .= $column->getCellContent($item);
                $html .= "</td>";
            }
            $html .= "</tr>";
        endwhile;

        $html .= "</tbody>";

        return $html;
    }

    /**
     * Zjistí, zda jsou definované některé sloupce pro vykreslení
     * 
     * @author Tomáš Kocifaj
     * @link www.ktstduio.cz
     * 
     * @return boolean
     */
    protected function hasListColumns() {
        $columnList = $this->getColumnList();
        if (KT::issetAndNotEmpty($columnList)) {
            return true;
        }

        return false;
    }

}
