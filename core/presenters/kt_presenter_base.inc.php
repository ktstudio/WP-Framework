<?php

/**
 * Základní Presenter pro všechny presentery
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

    public function getModel() {
        return $this->model;
    }

    public function setModel(KT_Modelable $model) {
        $this->model = $model;
        return $this;
    }

}
