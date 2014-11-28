<?php

/**
 * Základní Presenter pro všechny presentery
 * 
 * @author Tomáš Kocifaj <kocifaj@ktstudio.c>
 * @link http://www.ktstudio.cz
 */
abstract class KT_Presenter_Base implements KT_Presentable {

    private $model = null;

    public function __construct(KT_Modelable $model) {
        $this->setModel($model);
    }

    // --- gettery ------------
    public function getModel() {
        return $this->model;
    }

    // --- settery ------------

    public function setModel(KT_Modelable $model) {
        $this->model = $model;
        return $this;
    }

}
