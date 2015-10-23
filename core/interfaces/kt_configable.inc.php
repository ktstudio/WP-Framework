<?php

/**
 * Základní společný (KT) interface pro všechny configy (používané pro (KT) MetaBoxy)
 * Pozn.: je velmi vhodné implemenovat minimálně pro každý Config používaný v rámci KT_Mateboxu
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
interface KT_Configable {

    public static function getAllGenericFieldsets();

    public static function getAllNormalFieldsets();

    public static function getAllSideFieldsets();
}
