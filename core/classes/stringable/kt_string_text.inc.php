<?php

/**
 * Třida (adaptér) pro práci s textem. Metody převzaty od Martin Hlaváč
 *
 * @author Jan Pokorný
 */
class KT_String_Text {

    /**
     *
     * @var KT_Stringable 
     */
    private $string;

    /**
     * 
     * @param KT_Stringable $string
     */
    public function __construct(KT_Stringable $string) {
        $this->string = $string;
    }

    /**
     * 
     * @return KT_Stringable
     */
    public function getString() {
        return $this->string;
    }

    public function __toString() {
        return $this->getLined();
    }

    /**
     * Provede aplikaci (nových HTML) řádků na zadaný text
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    public function getLined($unsafe = false) {
        $text = ($unsafe) ? $this->getString()->getUnsafeValue() : $this->getString()->getValue();
        return nl2br($text);
    }

    /**
     * Na základě odřádkování rozdělí zadaný text do pole 
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return array
     */
    public function getLinesArray($unsafe = false) {
        $text = ($unsafe) ? $this->getString()->getUnsafeValue() : $this->getString()->getValue();
        return explode(PHP_EOL, $text);
    }

    /**
     * Na základě odřádkování (tzn. po řádcích) rozdělí zadaný text a vrátí jako HTML seznam zadaného tagu
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param string $format @see sprintf()
     * @return string (HTML)
     */
    public function getList($format = "%s", $unsafe = false) {
        $lines = $this->getLinesArray($unsafe);
        if (!KT::arrayIssetAndNotEmpty($lines)) {
            return;
        }
        $output = "";
        foreach ($lines as $line) {
            $output .= sprintf($format, $line);
        }
        return $output;
    }

}
