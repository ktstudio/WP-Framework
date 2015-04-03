<?php

/**
 * Základní presenter pro všechny presentery
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
abstract class KT_Presenter_Base implements KT_Presentable {

    private $model = null;

    public function __construct(KT_Modelable $model = null) {
        kt_check_loaded(); // kontrola KT Frameworku
        if (KT::issetAndNotEmpty($model)) {
            $this->setModel($model);
        }
    }

    // --- getry & setry ------------------------ 

    /**
     * Vrátí zadaný v obecné podobě
     * 
     * @return KT_Modelable
     */
    public function getModel() {
        return $this->model;
    }

    /**
     * Nastavení jiného modelu v obecné podobě
     * 
     * @param KT_Modelable $model
     * @return \KT_Presenter_Base
     */
    public function setModel(KT_Modelable $model) {
        $this->model = $model;
        return $this;
    }
    
    // --- veřejné funkce ---------------------
    // --- neveřejné funkce ---------------------

}
