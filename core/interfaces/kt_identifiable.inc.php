<?php

/**
 * Základní společný (KT) interface pro všechny identifikovatelné objekty (třídy) pomocí ID
 * Pozn.: je velmi vhodné implemenovat minimálně pro databázové entity
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
interface KT_Identifiable {

    /**
     * Metoda, která by měla vracet ID konkrétní instance
     * @return integer
     */
    public function getId();
}
