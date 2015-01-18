<?php

/**
 * Base pro práci s attributy v rámci HTML tagů 
 *
 * @author Tomáš Kocifaj
 */

abstract class KT_HTML_Tag_Base {
    
    private $attributes = array();
    
    // --- gettery a settery ----------------
    
    /**
     * @return array
     */
    protected function getAttributes() {
        return $this->attributes;
    }
    
    /**
     * Nastavení kolekci attributů
     * array( "attrName" => "attrValue")
     * 
     * @author Tomáš Kocifaj
     * 
     * @param array $attributes
     * @return \KT_HTML_Tag_Base
     */
    protected function setAttributes(array $attributes = array()) {
        $this->attributes = $attributes;
        return $this;
    }
    
    // --- protected funkce ------------------
    
    /**
     * Přidá html attribute do tagu
     *
     * @author Tomáš Kocifaj
     *
     * @param string $name - nazev attributu
     * @param string $value - hodnota
     * @return \KT_HTML_Tag_Base
     */
    protected function addAttribute($name, $value = null) {
        $this->attributes[$name] = $value;

        return $this;
    }
    
    /**
     * Odstraní attribute fieldu z kolekce na základě názvu
     * 
     * @author Tomáš Kocifaj
     * 
     * @param string $name
     * @return \KT_HTML_Tag_Base
     */
    protected function removeAttribute($name){
        unset($this->attributes[$name]);
        
        return $this;
    }
    
    /**
     * Připraví string se zadanými attributy
     * 
     * @author Tomáš Kocifaj
     * 
     * @return string
     */
    protected function getAttributeString(){
        $html = "";
        $attrCollection = $this->getAttributes();
        
        if(kt_not_isset_or_empty($attrCollection)){
            return $html;
        }
        
        foreach ($attrCollection as $key => $value) {
            if (kt_isset_and_not_empty($value)) {
                $html .= $key . "=\"" . htmlspecialchars($value) . "\" ";
            } else {
                $html .= $key . " ";
            }
        }
        
        return $html;
    }
    
    /**
     * Vykreslí string se zadanýma hodnotama
     * 
     * @author Tomáš Kocifaj
     */
    protected function renderAttributeString(){
        echo $this->getAttributeString();
    }
}

