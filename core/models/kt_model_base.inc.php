<?php

/**
 * Společný základ pro všechny modely
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Model_Base implements KT_Modelable {

    const MAGIC_GETTER_KEY = "get";

    /**
     * Vytvoří jméno constanty na základě názvu funkce a to tak, že rozdělí
     * string na základě velkých písmen, vloží mezi slovo podtržítka a převed vše na velké písmena.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $functionName
     * @return string
     */
    protected function getConstantFromFunctionName($functionName) {

        if (KT::notIssetOrEmpty($functionName)) {
            return null;
        }

        $parts = preg_split('/(?=[A-Z])/', $functionName, -1, PREG_SPLIT_NO_EMPTY);
        unset($parts[0]);
        $constantName = strtoupper(implode($parts, "_"));

        return $constantName;
    }

    /**
     * Vytvoří název configu, který odpovídá volanému modelu.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return type
     */
    protected function getConfigFromModelName() {
        return $configName = str_replace("Model", "Config", get_called_class());
    }

    /**
     * Metoda prověří, zda se jedná o funkci, která začíná znaky "get" pokud ano
     * provede vyčtení příslušné hodnoty constanty a vrátí její hodnotu na základě
     * volané třídy.
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    protected function getConstantValue($functionName) {
        
        $firstChars = substr($functionName, 0, 3);

        if ($firstChars != self::MAGIC_GETTER_KEY) {
            return null;
        }

        if (method_exists($this, $functionName)) {
            return null;
        }

        $classRef = new ReflectionClass($this->getConfigFromModelName());
        $constantName = $this->getConstantFromFunctionName($functionName);

        if (KT::notIssetOrEmpty($constantName)) {
            throw new KT_Not_Exist_Config_Constant_Exception($constantName);
        }

        return $constValue = $classRef->getConstant($constantName);
    }

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
    public static function theItem($value, $tag = null, $itemprop = null, $label = null, $class = null) {
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
