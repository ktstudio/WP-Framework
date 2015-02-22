<?php

/**
 * Base pro práci s attributy v rámci HTML tagů 
 *
 * @author Tomáš Kocifaj
 */
abstract class KT_HTML_Tag_Base {

    const CLASS_KEY = "class";

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
     * Připraví string se zadanými attributy
     * 
     * @author Tomáš Kocifaj
     * 
     * @return string
     */
    protected function getAttributeString() {
        $html = "";
        $attrCollection = $this->getAttributes();

        if (KT::notIssetOrEmpty($attrCollection)) {
            return $html;
        }

        foreach ($attrCollection as $key => $value) {
            if($key == self::CLASS_KEY){
                continue;
            }
            
            if (KT::issetAndNotEmpty($value)) {
                $html .= $key . "=\"" . htmlspecialchars($value) . "\" ";
            } else {
                $html .= $key . " ";
            }
        }
        
        $html .= $this->getAttrClassString();

        return $html;
    }
    
    /**
     * Vrítí string se všemi CSS class zadané danému elementu.
     * 
     * @author Tomáš Kocifaj
     * 
     * @return string
     */
    protected function getAttrClassString(){
        $html = "";
        if(array_key_exists(self::CLASS_KEY, $this->getAttributes())){
            $classString = "";

            foreach($this->getClasses() as $class){
                $classString .= $class . " ";
            }
            
            $html .= self::CLASS_KEY . "=\"$classString\"";
        }
        
        return $html;
    }

    /**
     * Vykreslí string se zadanýma hodnotama
     * 
     * @author Tomáš Kocifaj
     */
    protected function renderAttributeString() {
        echo $this->getAttributeString();
    }
    
    /**
     * Vrátí hodnotu nastaveného attributu
     * 
     * @author Tomáš Kocifaj
     * 
     * @param string $attrName
     * @return string | array
     */
    protected function getAttrValueByName($attrName){ 
        if(array_key_exists($attrName, $this->getAttributes())){
            return $this->attributes[$attrName];
        }
    }

    // --- veřejné funkce ------------------

    /**
     * Přidá html attribute do tagu
     *
     * @author Tomáš Kocifaj
     *
     * @param string $name - nazev attributu
     * @param string $value - hodnota
     * @return \KT_HTML_Tag_Base
     */
    public function addAttribute($name, $value = null) {
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
    public function removeAttribute($name) {
        unset($this->attributes[$name]);

        return $this;
    }

    /**
     * Nastaví HTML attr ID danému elementu
     * 
     * @author Tomáš Kocifaj
     * 
     * @param string $id
     * @return \KT_HTML_Tag_Base
     */
    public function setAttrId($id) {
        $this->addAttribute("id", $id);
        return $this;
    }

    /**
     * Nastaví HTML attr TITLE danému elementu
     * 
     * @author Tomáš Kocifaj
     * 
     * @param string $title
     * @return \KT_HTML_Tag_Base
     */
    public function setAttrTitle($title) {
        $this->addAttribute("title", $title);
        return $this;
    }

    /**
     * Přidá jednu class do HTML attr CLASS danému elementu
     * 
     * @author Tomáš Kocifaj
     * 
     * @param string $class
     * @return \KT_HTML_Tag_Base
     */
    public function addAttrClass($class) {
        $classes = explode(" ", $class);
        
        if(KT::issetAndNotEmpty($classes)){
            $currentClasses = $this->getClasses();
            $newClasses = array_merge($classes, $currentClasses);
            $this->setClasses($newClasses);
            return $this;
        }
        
        if (array_key_exists(self::CLASS_KEY, $this->attributes)) {
            array_push($this->attributes[self::CLASS_KEY], $class);
            return $this;
        }

        $this->attributes[self::CLASS_KEY][] = $class;
        return $this;
    }

    /**
     * Odstraní jednu CSS Class z kolekce všech class, které patří danému elementu
     * 
     * @author Tomáš Kocifaj
     * 
     * @param string $class
     */
    public function removeAttrClass($class) {
        $classes = $this->getClasses();
        $flipedClasses = array_flip($classes);
        unset($flipedClasses[$class]);
        $this->setClasses(array_flip($flipedClasses));
    }

    // --- privátní funkce ------------------

    /**
     * Vrátí kolekci všech CSS tříd, které byly elementu přidány
     * 
     * @author Tomáš Kocifaj
     * 
     * @return array
     */
    private function getClasses() {
        if(array_key_exists(self::CLASS_KEY, $this->getAttributes())){
            return $this->attributes[self::CLASS_KEY];
        }
        
        return array();
    }
    
    /**
     * Nastaví kolekci všech CSS tříd, které danému elementu patří.
     * 
     * @author Tomáš Kocifaj
     * 
     * @param array $classes
     */
    private function setClasses(array $classes = array()){
        $this->attributes[self::CLASS_KEY] = $classes;
    }

}
