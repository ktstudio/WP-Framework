<?php

/**
 * Základní společný (KT) interface pro všechny třídy kvůli případné zautomatizované registraci do systému
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
interface KT_Initializable {

    public function initialize(array $parameters = null);
}
