<?php

/**
 * Základní společný (KT) interface pro všechny presentery, který pracují se základním modelem
 * Pozn.: je velmi vhodné implemenovat minimálně pro každý presenter, který pracuje s určitým modelem
 * 
 * @author Tomáš Kocifaj
 * @link http://www.ktstudio.cz
 */
interface KT_Presentable {

    /**
     * Metodá, která by měla vracet základní model presenteru
     * 
     * @return mixed
     */
    public function getModel();

    /**
     * Metoda, která má nastavit základní model presenteru
     * 
     * @param type $model
     */
    public function setModel(KT_Modelable $model);
}
