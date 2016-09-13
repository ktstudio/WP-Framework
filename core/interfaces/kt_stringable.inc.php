<?php

/**
 * Interface pro třídy které reprezentují datový typ string.
 * 
 * @author Jan Pokorný
 */
interface KT_Stringable {

    public function getValue();

    public function getUnsafeValue();

    public function __toString();
}
