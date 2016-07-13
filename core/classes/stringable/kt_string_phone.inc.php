<?php

/**
 * Třída pro práci s telefonem
 *
 * @author Jan Pokorný
 */
class KT_String_Phone extends KT_String_Base implements KT_Stringable {

    /**
     * 
     * @param string $value
     */
    public function __construct($value) {
        parent::__construct($value);
    }

    /**
     * Vratí hodnotu vhodou pro href
     * 
     * @return string
     */
    public function getHrefValue() {
        $phone = $this->getValue();
        $before = ["/[^\d|\-|\+]/"];
        $after = [""];
        return "tel:" . preg_replace($before, $after, $phone);
    }

    /**
     * Pokusí se obalit předvolbu a zbytek spany
     * Funguje pouze s českou předvolbou
     * 
     * @return string HTML
     */
    public function tryGetHighlighted() {
        $phone = $this->getValue();
        $regepx = "/(^\(?(?:\+|00?)?42(?:0|1)\)?)\s?(.+)?/";
        $replacement = "<span class=\"prefix\">$1</span><span class=\"rest\">$2</span>";
        return preg_replace($regepx, $replacement, $phone);
    }

}
