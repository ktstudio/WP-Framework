<?php

abstract class KT_Options_Field_Base extends KT_Field {

    private $dataManager = null;

    /**
     * Abstraktní třída pro práci s fieldy, které mají definovaný options
     * a jejich skladba se stává z řady položek.
     *
     * @param string $name - hash v poli
     * @param string $label - popisek v html
     * @return self
     */
    public function __construct($name, $label) {
        parent::__construct($name, $label);
    }

    // --- gettery ---------------------------

    /** @return \KT_Data_Manager_Base */
    public function getDataManager() {
        return $this->dataManager;
    }

    /** @return array */
    public function getOptionsData() {
        if (KT::issetAndNotEmpty($this->dataManager)) {
            return $this->getDataManager()->getData();
        }
        return [];
    }

    // --- settery ---------------------------

    public function setDataManager(KT_Data_Manager_Base $dataManager) {
        $this->dataManager = $dataManager;

        return $this;
    }

    public function setOptionsData(array $options = []) {
        $dataManager = new KT_Simple_Data_Manager($options);
        $this->setDataManager($dataManager);
        return $this;
    }

    public function setOptionsEnum(KT_Enum $enum, $exceptKey = null) {
        $dataManager = new KT_Simple_Data_Manager($enum->getTranslates($exceptKey));
        $this->setDataManager($dataManager);
        return $this;
    }

    // --- statické metody ---------------------------

    /**
     * Vrátí pole po select, nebo radio které obsahuje (3) možnosti výběru: (prázdnou), ano, ne
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param boolean $withFirtEmpty
     * @return array
     */
    public static function getSwitchOptions($withFirtEmpty = true) {
        if ($withFirtEmpty === true) {
            $options[KT_EMPTY_SYMBOL] = KT_EMPTY_SYMBOL;
        }
        $options[KT_Switch_Field::YES] = __("Yes", "KT_CORE_DOMAIN");
        $options[KT_Switch_Field::NO] = __("No", "KT_CORE_DOMAIN");
        return $options;
    }

}
