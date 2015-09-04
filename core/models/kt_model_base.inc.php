<?php

/**
 * Společný základ pro všechny modely
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Model_Base implements KT_Modelable {

    /**
     * Vypíše (HTML) hodnotu, pokud je zadána a to dle zadaných parametrů 
     * (tzn. případně vč. tagu. labelu, itemprop a class)
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param mixed $value
     * @param string $tag
     * @param string $itemprop
     * @param string $label
     * @param string $class
     */
    public function theItem($value, $tag = null, $itemprop = null, $label = null, $class = null) {
        if (KT::issetAndNotEmpty($value)) {
            if (KT::issetAndNotEmpty($label)) {
                echo "{$label}: ";
            }
            if (KT::issetAndNotEmpty($tag)) {
                $itempropPart = null;
                if (KT::issetAndNotEmpty($itemprop)) {
                    $itempropPart = " itemprop=\"{$itemprop}\"";
                }
                $classPart = null;
                if (KT::issetAndNotEmpty($class)) {
                    $classPart = " class=\"{$class}\"";
                }
                echo "<{$tag}{$itempropPart}{$classPart}>";
            }
            echo $value;
            if (KT::issetAndNotEmpty($tag)) {
                echo "</{$tag}>";
            }
        }
    }

}
