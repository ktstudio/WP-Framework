<?php

/**
 * Interface pro config, který pracuje s dynamickými fieldsety.
 * 
 * @author Jan Pokorný
 */
interface KT_Dynamic_Configable {

    /**
     * @return array Pole [FieldsetName => Fieldset] dynamických fieldsetů
     */
    public static function getAllDynamicFieldsets();
}
