<?php

class KT_CRUD_Admin_List {
    
    const GET_ACTION = "action";
    const GET_ACTION_CREATE = "create";
    
    private $className = null;
    private $columnList = array();
    private $newItemButton = false;
    private $repository = null;
    private $templateTitle = null;
    
    
    /**
     * @param string $className - jméno objektu, který se má v rámci tabulky zobrazovat. Musí být KT_CRUD
     * @param type $tableName - náze tabulky, kde jsou záznamy uloženy
     */
    public function __construct( $className, $tableName ) {
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
        usort($this->columnList, function($a, $b){
            if ($a->getPosition() == $b->getPosition()) return 0;
            return ($a->getPosition() > $b->getPosition()) ? 1 : -1;
        });
        
        return $this->columnList;
    }
    
    /**
     * Nastaví kolekci sloupců, která se bude v rámci tabulky zobrazovat
     * kolekce objektů KT_CRUD_Column
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param \KT_Repository $repository
     * @return \KT_CRUD_Admin_List
     */
    public function setRepository( KT_Repository $repository) {
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param type $templateTitle
     * @return \KT_CRUD_Admin_List
     */
    public function setTemplateTitle($templateTitle) {
        $this->templateTitle = $templateTitle;
        return $this;
    }
        
    // --- veřejné funkce ------------------
    
    /**
     * Do kolekce sloupců přidá nový sloupec
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $name
     * @return \KT_CRUD_Admin_Column $name
     */
    public function addColumn($name){
        $column = $this->columnList[$name] = new KT_CRUD_Admin_Column($name);
        $maxColumnCount = count($this->columnList);
        $column->setPosition($maxColumnCount);
        return $column;
    }
    
    /**
     * Do stávající kolekce sloupců přidá další kolekci (provede merge - nepřepíše původní)
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param array $columnCollection
     * @return \KT_CRUD_Admin_List
     */
    public function addColumnsToCollection(array $columnCollection){
        $currentColumnCollection = $this->getColumnList();
        if(kt_isset_and_not_empty($columnCollection)){
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $columnName
     * @return \KT_CRUD_Admin_List
     */
    public function removeColumnFromCollection($columnName){
        if(isset($this->columnList[$columnName])){
            unset($this->columnList[$columnName]);
        }
        
        return $this;
    }
    
    /**
     * Vrátí sloupec z kolekce na základě jeho názvu
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @param string $columnName
     * @return \KT_CRUD_Admin_Column
     */
    public function getColumnByName($columnName){
        if(isset($this->columnList[$columnName])){
            return $this->columnList[$columnName];
        }
        
        return null;
    }
    
    /**
     * Vrátí obsah celé stránky
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return html
     */
    public function getContent(){
        $html = "";
        
        $html .= $this->getTamplageTitleContent(); // Titulek stránky
        $html .= $this->getAddButtonContent(); // Tlačítko pro přidání nového záznamu
        
        if(array_key_exists("page", $_GET)){
            $pageName = $_GET["page"];
            $string = apply_filters("kt_crud_admin_list_before_table_" . $pageName, $html);
            $html .= $string;
        }
        
        $html .= $this->getTable(); // Tabulka s daty
        
        return $html;
    }
    
    // --- protected funkce ------------------
    
    /**
     * Vrátí titulek layoutu, pokud byl definován.
     * 
     @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getTamplageTitleContent(){
        if(kt_not_isset_or_empty($this->getTemplateTitle())){
            return "";
        }
        
        return $html = "<h2>". $this->getTemplateTitle() ."</h2>";
    }
    
    /**
     * Vrátí odkaz v podobě tlačítko pro přidání nového odkazu na CRUD listu
     * do aktuálního odkazu přidá ACTION = CREATE
     * 
     * Prověří, zda je nový button vyžadován. Pokud ano, vykreslí pokud ne, vrátí prázdný string
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getAddButtonContent(){
        if( ! $this->getNewItemButton()){
            return "";
        }
        
	$createUrl = add_query_arg( array( self::GET_ACTION => self::GET_ACTION_CREATE ));
	return $html = "<a href=\"$createUrl\" id=\"addCrudButtonList\" class=\"button\">". __("Přidat nový záznam", KT_DOMAIN) ."</a>";
    }
    
    /**
     * Vrátí HTML s celou tabulku
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getTable(){
        $html = "";
        
        if( ! $this->hasListColumns()){
            return $html;
        }
        
        $tableId = strtolower($this->getClassName());
        
        $html .= "<table id=\"{$tableId}\" class=\"wp-list-table widefat fixed item-list\" cellspacing=\"0\">";
        $html .= $this->getTableHeader();
        $html .= $this->getTableBody();
        $html .= "</table>";
        
        return $html;
    }
    
    /**
     * Vrátí HTML hlavičku na základě zadané kolekce sloupců
     * 
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return string
     */
    protected function getTableHeader(){
        $html = "";
        $columnList = $this->getColumnList();
        
        $html .= "<thead>";
        $html .= "<tr>";
        foreach($columnList as $column){
            /** @var $column \KT_CRUD_Column */
            $class = kt_isset_and_not_empty($column->getCssClass()) ? " class=\"{$column->getCssClass()}\"" : "";
            $html .= "<th$class>{$column->getLabel()}</th>";
        }
        $html .= "</tr>";
        $html .= "</thead>";
        
        return $html;
    }
    
    protected function getTableBody(){
        $html = "";
        $columnCollection = $this->getColumnList();
        $repository = $this->getRepository()->selectData();       
        
        if( ! $repository->haveItems()){
            return $html;
        }
        
        $html .= "<tbody>";
        
        while($repository->haveItems()) : $item = $repository->theItem();
            $html .= "<tr id=\"row-{$item->getId()}\">";
            foreach($columnCollection as $column){
                
                $class = kt_isset_and_not_empty($column->getCssClass()) ? " class=\"{$column->getCssClass()}\"" : "";
                
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
     * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
     * @link www.ktstduio.cz
     * 
     * @return boolean
     */
    protected function hasListColumns(){
        $columnList = $this->getColumnList();
        if(kt_isset_and_not_empty($columnList)){
            return true;
        }
        
        return false;
    }
}