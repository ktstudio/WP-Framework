<?php

class KT_Admin_URL_Param_Checker {
    
    private $paramCollection = array();
    private $result = true;
    
    // --- gettery a settery ------------------
    
    /**
     * @return array
     */
    private function getParamCollection() {
        return $this->paramCollection;
    }
    
    /**
     * Nastaví kolekcy kontrolvaných parametrů
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param array $paramCollection
     * @return \KT_Admin_URL_Param_Checker
     */
    private function setParamCollection(array $paramCollection = array()) {
        $this->paramCollection = $paramCollection;
        return $this;
    }
    
    /**
     * @return boolean
     */
    private function getResult() {
        return $this->result;
    }

    /**
     * Nastaví výsledek knotroly
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param bollean $result
     * @return \KT_Admin_URL_Param_Checker
     */
    private function setResult($result) {
        $this->result = $result;
        return $this;
    }

        
    // --- veřejné funkce ------------------
    
    /**
     * Do kolekce kontrolovaných parametrů URL přidá $key => $value
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return \KT_Admin_URL_Param_Checker
     */
    public function addParamValue($paramName, $paramValue = null){
        $this->paramCollection[$paramName] = $paramValue;
        return $this;
    }
    
    /**
     * Odstraní z kolekce kontrolovaných parametrů URL parametr na základě jeho názvu
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $paramName
     * @return \KT_Admin_URL_Param_Checker
     */
    public function removeParamValue($paramName){
        if(isset($this->paramCollection[$paramName])){
            unset($this->paramCollection);
        }
        
        return $this;
    }
    
    /**
     * Zkontroluje, zda všechny zadané parametry odpovídají požadavkům
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @return boolean
     */
    public function getCheckedResult(){
        
        if( ! is_admin() ){
            return false;
        }
        
        if(KT::notIssetOrEmpty($this->getParamCollection())){
            return $this->getResult();
        }
        
        $paramCollection = $this->getParamCollection();
        
        foreach($paramCollection as $paramName => $paramValue){
            $paramCheckedResult = $this->isParamSet($paramName, $paramValue);
            if($paramCheckedResult == false){
                return $this->setResult(false)->getResult();
            }
        }
        
        return $this->getResult();
    }
    
    // --- privátní funkce ------------------
    
    /**
     * Zkontroluje, zda zadaný parametr je nastavený správně dle požadavků
     * 
     * @author Tomáš Kocifaj
     * @link http://www.ktstudio.cz
     * 
     * @param string $paramName
     * @param string $paramValue
     * @return boolean
     */
    private function isParamSet($paramName, $paramValue){
        if( ! isset($_GET[$paramName])){
            return false;
        }
        
        if(KT::notIssetOrEmpty($paramValue)){
            return true;
        }
        
        if($_GET[$paramName] == $paramValue){
            return true;
        }
        
        return false;
    }




    
}

