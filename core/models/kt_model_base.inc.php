<?php

/**
 * Společný základ pro všechny modely
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Model_Base implements KT_Modelable {

    const MAGIC_ISSER_PREFIX = "is";
    const MAGIC_GETTER_PREFIX = "get";

    /**
     * Vytvoří jméno constanty na základě názvu funkce a to tak, že rozdělí string
     * na základě velkých písmen, vloží mezi slovo podtržítka a převed vše na velké písmena.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $functionName
     * @return string
     */
    protected function getConstantNameFromFunction($functionName) {
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
     * @return string
     */
    protected function getConfigNameFromModel() {
        return $configName = str_replace("Model", "Config", get_called_class());
    }

    /**
     * Metoda prověří, zda se jedná o funkci, která začíná znaky "is" pokud ano provede
     * vyčtení příslušné hodnoty constanty a vrátí její hodnotu na základě volané třídy.
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $functionName
     * @return string
     * @throws KT_Not_Exist_Config_Constant_Exception
     */
    protected function getAutoIsserKey($functionName) {
        return $this->getAutoMethodKey($functionName, self::MAGIC_ISSER_PREFIX);
    }

    /**
     * Metoda prověří, zda se jedná o funkci, která začíná znaky "get" pokud ano provede
     * vyčtení příslušné hodnoty constanty a vrátí její hodnotu na základě volané třídy.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $functionName
     * @return string
     * @throws KT_Not_Exist_Config_Constant_Exception
     */
    protected function getAutoGetterKey($functionName) {
        return $this->getAutoMethodKey($functionName, self::MAGIC_GETTER_PREFIX);
    }

    /**
     * Metoda prověří, zda se jedná o funkci, která začíná zadaným prefixem a pokud ano, tak provede
     * vyčtení příslušné hodnoty constanty a vrátí její hodnotu na základě volané třídy.
     *
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     *
     * @param string $functionName
     * @param string $prefix
     * @return string
     * @throws KT_Not_Exist_Config_Constant_Exception
     */
    private function getAutoMethodKey($functionName, $prefix) {
        $firstChars = substr($functionName, 0, strlen($prefix));
        if ($firstChars !== $prefix) {
            return null;
        }

        if (method_exists($this, $functionName)) {
            return null;
        }

        $configName = $this->getConfigNameFromModel();
        if (!class_exists($configName)) {
            return null;
        }

        $reflectionClass = new ReflectionClass($configName);
        $constantName = $this->getConstantNameFromFunction($functionName);
        $constantValue = $reflectionClass->getConstant($constantName);
        if (KT::notIssetOrEmpty($constantValue)) {
            throw new KT_Not_Exist_Config_Constant_Exception($constantName, $configName, $functionName);
        }
        return $constantValue;
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
