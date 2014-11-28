<?php

/**
 * Základní společný (KT) interface pro všechny třídy kvůli případné zautomatizované odregistraci ze systému
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
interface KT_Unregistrable {

    public function unregister();
}
