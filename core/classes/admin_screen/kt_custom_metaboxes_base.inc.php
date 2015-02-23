<?php

abstract class KT_Custom_Metaboxes_Base {

    const KT_METABOX_SCREEN = "metaboxes";
    const KT_CRUD_LIST_SCREEN = "crud-list";
    const KT_COLUMN_ONE = 1;
    const KT_COLUMN_TWO = 2;

    private $renderSaveButton = false;
    private $NumberColumns = self::KT_COLUMN_TWO;
    private $screenCollection = array();
    private $defaultCallbackFunction = array();
    private $metaboxCollection = array();
    private $crudList = null;

    /**
     * Abstraktní třída pro zakládání a definování stránke v rámci WP administrace
     * A přidávání metaboxů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     */
    public function __construct() {
        $this->defaultCallbackFunction = array($this, 'renderPage');
    }

    // --- gettery ----------------------

    /**
     * @return boolean
     */
    public function getRenderSaveButton() {
        return $this->renderSaveButton;
    }

    /**
     * @return int
     */
    public function getNumberColumns() {
        return $this->NumberColumns;
    }

    /**
     * @return array
     */
    public function getScreenCollection() {
        return $this->screenCollection;
    }

    /**
     * @return array
     */
    public function getDefaultCallbackFunction() {      
        return $this->defaultCallbackFunction;
    }

    /**
     * @return array
     */
    public function getMetaboxCollection() {
        return $this->metaboxCollection;
    }
    
    /**
     * @return \KT_CRUD_Admin_List
     */
    public function getCrudList() {
        return $this->crudList;
    }

    
    // --- settery ----------------------

    /**
     * Nastaví, zda se má nebo nemá vykreslit metabox se submit buttonem
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $renderSaveButton
     * @return \KT_Custom_Metaboxes_Base
     */
    public function setRenderSaveButton($renderSaveButton = true) {

        if (!is_bool($renderSaveButton)) {
            return $this;
        }

        $this->renderSaveButton = $renderSaveButton;

        return $this;
    }

    /**
     * Nastaví, jaký počet sloupců pro vykreslení metaboxů má použít
     * Akceptuje pouze 1 nebo 2
     * Konstaty self::KT_COLUMN_ONE || self::KT_COLUMN_TWO
     *  
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return \KT_Custom_Metaboxes_Base
     */
    public function setNumberColumns($numberColumns) {

        if (!KT::isIdFormat($numberColumns) && $numberColumns > self::KT_COLUMN_TWO) {
            return $this;
        }

        $this->NumberColumns = $numberColumns;

        return $this;
    }

    /**
     * Nastaví kolekci všechn parametrů, které ovlivní vykreslní obsahu
     * samotný obsah layoutu je definovaný pomocí callback funkce
     * 
     * Used : $this->addScreenFunctionForAction();
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $screenCollection
     * @return \KT_Custom_Metaboxes_Base
     */
    public function setScreenCollection(array $screenCollection) {
        $this->screenCollection = $screenCollection;

        return $this;
    }

    /**
     * Nastaví pole, kde je definovaná defaultní callback funkce v případě, že nad 
     * stránkou nejsou volány žádné URL parametry.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $defaultCallbackFunction
     * @return \KT_Custom_Metaboxes_Base
     */
    public function setDefaultCallbackFunction($defaultCallbackFunction) {
        $this->defaultCallbackFunction = $defaultCallbackFunction;
        return $this;
    }

    /**
     * Nastaví kolekci metaboxů, které budou na stránkce rendrovány. Kolekce musí obsahovat
     * objekty typ KT_Metabox
     * 
     * @author Tomáš Kocifaj
     * @link http//www.KTstudio.cz
     * 
     * @param array $metaboxCollection
     * @return \KT_Custom_metaboxes_Base
     */
    public function setMetaboxCollection($metaboxCollection) {
        $this->metaboxCollection = $metaboxCollection;

        return $this;
    }
    
    /**
     * Nastaví KT_CRUD_List objekt pro zobrazení tabulky s daty
     * 
     * @author Tomáš Kocifaj
     * @link http//www.KTstudio.cz
     * 
     * @param KT_CRUD_Admin_List $crudList
     * @return \KT_Custom_Metaboxes_Base
     */
    public function setCrudList(KT_CRUD_Admin_List $crudList){
        $this->crudList = $crudList;
        return $this;
    }

    // --- abstraktní funkce ------------

    abstract function initPage();

    abstract function getTitle();

    abstract function getPage();

    abstract function getSlug();

    // --- veřejné funkce funkce ------------
    
    /**
     * Přidá
     * 
     * @param type $className
     * @param type $tableName
     * @return type
     */
    public function addCrudList($className, $tableName){
        $crudList = new KT_CRUD_Admin_List($className, $tableName);
        $this->setCrudList($crudList);
        return $this->getCrudList();
    }

    /**
     * Funkce přidá stránkce nový parametr, který bude volat v případě jeho nastavení jinou
     * callback funkci pro výpis obsahu na stránce.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param string $actionName - název GET parametru
     * @param string $actionValue - hodnota GET parametru
     * @param string $callbackFunction - callback funkce, která se při vykreslení zavolá
     * @return \KT_Custom_Metaboxes_Base
     */
    public function addScreenFunctionForAction($actionName, $actionValue, $callbackFunction = null) {
        $screenAction = new KT_Custom_Page_Action_Screen($actionName, $actionValue, $callbackFunction);

        $this->screenCollection[] = $screenAction;

        return $this;
    }

    /**
     * Do kolekce metaboxů přidá jeden metabox.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param KT_Metabox $metabox
     * @return \KT_Custom_Metaboxes_Base
     */
    public function addMetabox(KT_Metabox $metabox) {
        $currentMetaboxCollection = $this->getMetaboxCollection();
        $currentMetaboxCollection[$metabox->getId()] = $metabox;

        $this->setMetaboxCollection($currentMetaboxCollection);

        return $this;
    }

    /**
     * Provede kontrolu, zda předaná kolekce obsahuje pouze objekty typu KT_Metabox.
     * Pokud ano, přidá kolekci k ostatním metaboxům, které jsou přidány.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz 
     * 
     * @param array $metaboxCollection
     * @return \KT_Custom_Metaboxes_Base
     * @throws KT_Not_Supported_Exception
     */
    public function addMetaboxCollection(array $metaboxCollection) {
        foreach ($metaboxCollection as $metabox) {
            if (!$metabox instanceof KT_Metabox) {
                throw new KT_Not_Supported_Exception("One of item in metaboxCollection is not instnace of KT_Metabox");
            }
        }

        $currentMetaboxCollection = $this->getMetaboxCollection();

        if (empty($currentMetaboxCollection)) {
            $this->setMetaboxCollection($metaboxCollection);

            return $this;
        }

        $newMetaboxCollection = array_merge($currentMetaboxCollection, $metaboxCollection);
        $this->setMetaboxCollection($newMetaboxCollection);

        return $this;
    }

    /**
     * Provede registraci stránky. Pokud jsou definované metaboxy, provede jejich registraci
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     */
    public function register() {
        add_action('admin_menu', array($this, 'initPage'));

        if (KT::issetAndNotEmpty($this->getMetaboxCollection())) {
            KT_metabox::registerMultiple($this->getMetaboxCollection());
        }
    }

    /**
     * Vypíše potřebný script pro funkčnost metaboxů v rámci layoutu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz   
     */
    public function renderFooterScripts() {
        echo "<script> postboxes.add_postbox_toggles(pagenow);</script>";
    }

    /**
     * Provede potřebné akce, které jsou nutné pro funkčnost layoutu s metaboxy
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     */
    public function doPageAction() {
        do_action('add_meta_boxes_' . $this->getPage(), null);
        $this->renderSaveMetabox();
        add_screen_option('layout_columns', array('max' => 2, 'default' => $this->NumberColumns));
        wp_enqueue_script('postbox');
        wp_enqueue_media();
    }

    /**
     * Provede kontrolu $_GET parametru a vrátí potřebnou callback funkci.
     * Pokud není GET parametr spárován, vrátí defaultní callback funkci.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz   
     * 
     * @return array
     */
    public function getCallbackFunctionByActionScreen() {
        $screenActionCollection = $this->getScreenCollection();

        if (KT::issetAndNotEmpty($screenActionCollection)) {
            foreach ($screenActionCollection as $screenAction) {

                $actionValue = $screenAction->getActionValue();
                $actionName = $screenAction->getActionName();

                if (!isset($_GET[$actionName])) {
                    continue;
                }

                $getValue = $_GET[$actionName];

                if (KT::issetAndNotEmpty($getValue) && $actionValue == $getValue) {
                    $callbackFunction = $screenAction->getCallBackFunction();
                    if ($callbackFunction == self::KT_METABOX_SCREEN){
                        return array($this, 'renderPage');
                    }

                    return $callbackFunction;
                }
            }
        }
        
        if($this->getDefaultCallbackFunction() == self::KT_CRUD_LIST_SCREEN){
            return array($this, "renderCrudListPage");
        }

        return $this->getDefaultCallbackFunction();
    }

    /**
     * Vykreslí layout stránky s metaboxama.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     */
    public function renderPage() {
        ?>
        <div class="wrap kt-custom-screen-page">
            <h2> <?php echo esc_html($this->getTitle()); ?> </h2>
            <form id="kt-custom-page-screen" class="<?php echo $this->getSlug(); ?>" name="kt-custom-page-screen" method="post">
                <input type="hidden" name="kt-action" value="kt-action-<?php $this->getSlug(); ?>">
                <?php
                wp_nonce_field('kt-action-nonce');

                wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
                wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
                ?>

                <div id="poststuff">

                    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">

                        <div id="postbox-container-1" class="postbox-container">
                            <?php do_meta_boxes('', 'side', null); ?>
                        </div>

                        <div id="postbox-container-2" class="postbox-container">
                            <?php do_meta_boxes('', 'normal', null); ?>
                            <?php do_meta_boxes('', 'advanced', null); ?>
                        </div>

                    </div> <!-- #post-body -->

                </div> <!-- #poststuff -->

            </form>

        </div><!-- .wrap -->
        <?php
    }
    
    /**
     * Vykreslí obsah pro stránku s KT_CRUD_List
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     */
    public function renderCrudListPage(){
        if(KT::notIssetOrEmpty($this->getCrudList())){
            throw new KT_Not_Set_Argument_Exception("KT_CRUD_List is emapty fro CRUD List page screen");
        }
        
        echo "<div class=\"wrap kt-custom-screen-page\">";
        echo $this->getCrudList()->getContent();
        echo "</div>";
    }

    /**
     * V případě nastavení tlačítka pro uložení vyklresí samotný metabox
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     * @return \KT_Custom_Metaboxes_Base
     */
    public function renderSaveMetabox() {
        if ($this->getRenderSaveButton() == false) {
            return $this;
        }

        add_meta_box(
                'kt-save-custom-page', __('Uložit nastavení', KT_DOMAIN), array($this, 'saveMetaboxCallback'), $this->getPage(), 'side'
        );

        return $this;
    }

    /**
     * Provede echo HTML s tlačítkem pro odeslání formuláře
     * 
     * Před buttonem se se volá akce do_action("kt_theme_setting_box_appearance_page_kt-theme-setting);
     * 
     * USED : renderSaveMetabox();
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz  
     * 
     */
    public function saveMetaboxCallback() {
        do_action("kt_theme_setting_box_" . KT_WP_Configurator::getThemeSettingSlug());
        echo "<button type=\"submit\" class=\"button button-primary button-large\">" . __('Uložit nastavení', KT_DOMAIN) . "</button>";
    }

}
