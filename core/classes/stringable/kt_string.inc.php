<?php

/**
 * Třída pro práci se stringem.
 * Obstará sanitizace.
 * Metody převzaty od Martin Hlaváč
 *
 * @author Jan Pokorný
 */
class KT_String extends KT_String_Base implements KT_Stringable {

    public function __construct($value) {
        parent::__construct($value);
    }

    /**
     * Kontrola, zda první zadaný textový řetezec někde uvnitř sebe obsahuje ten druhý zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $substring hledaný podřetězec
     * @return boolean true, pokud $substring se nachází v $string, jinak false
     */
    public function contains($substring) {
        $position = strpos($this->getUnsafeValue(), $substring);
        return !($position === false);
    }

    /**
     * Kontrola, zda první zadaný textový řetezec obsahuje na svém konci ten druhý zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * @param string $ending
     * @return boolean
     */
    public function endsWith($ending) {
        $length = strlen($ending);
        $string_end = substr($this->getUnsafeValue(), strlen($this->getUnsafeValue()) - $length);
        return $string_end === $ending;
    }

    /**
     * Kontrola, zda první zadaný textový řetezec obsahuje na svém začátku ten druhý zadaný
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $starting
     * @return boolean
     */
    public function startsWith($starting) {
        $length = strlen($starting);
        return (substr($this->getUnsafeValue(), 0, $length) === $starting);
    }

    /**
     * Ořízně text (řetezec), pokud je delší než požadovaná maximální délka včetně případné přípony
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param int $maxLength - požadovaná maxiální délka (ořezu)
     * @param boolean $fromBeginOrEnd - true od začátku, false od konce
     * @param string $suffixPrefix - ukončovácí přípona/předpona navíc (podle parametru $fromBeginOrEnd)
     * @return KT_String
     */
    public function crop($maxLength, $fromBeginOrEnd = true, $suffixPrefix = "...") {
        $text = $this->getUnsafeValue();
        $maxLength = KT::tryGetInt($maxLength);
        $currentLength = strlen($text);
        if ($maxLength > 0 && $currentLength > $maxLength) {
            if ($fromBeginOrEnd) {
                $text = mb_substr($text, 0, $maxLength) . $suffixPrefix;
            } else {
                $text = $suffixPrefix . mb_substr($text, ($currentLength - $maxLength), $currentLength);
            }
        }
        $this->unsafeValue = $text;
        return $this;
    }

    /**
     * Odstranění všech mezer ze zadaného textu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return KT_String
     */
    public function removeSpaces() {
        $this->unsafeValue = str_replace(" ", "", trim($this->getUnsafeValue()));
        return $this;
    }

    /**
     * Konverze textu (zpět) do HTML (entit) vč. uvozovek
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $text
     * @return string
     */
    public static function htmlDecode($text) {
        if (self::issetAndNotEmpty($text)) {
            return html_entity_decode(stripslashes($text), ENT_COMPAT | ENT_HTML401, "UTF-8");
        }
        return $text;
    }

}
