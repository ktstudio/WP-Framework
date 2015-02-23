<?php

/**
 * Základ pro configy určené pomocí options (např. Theme) za účelem cachování option hodnot pro případný prefix
 *
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
class KT_WP_Options_Base_Model extends KT_Model_Base {

    private $optionsPrefix;
    private $options = array();
    private $initialized;

    public function __construct($metaPrefix) {
        $this->setOptionsPrefix($metaPrefix);
    }

    /**
     * Vrátí prefix pro vyčtení (jen konkrétních) options z DB
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return string
     */
    public function getOptionsPrefix() {
        return $this->optionsPrefix;
    }

    /**
     * Nastaví prefix pro vyčtení (jen konkrétních) options z DB
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $optionPrefix
     */
    protected function setOptionsPrefix($optionPrefix) {
        $this->optionsPrefix = $optionPrefix;
        $this->setInitialized(false);
    }

    /**
     * Vrátí kolekci options z DB podle případného profixu ve tvaru name->value
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return array
     */
    public function getOptions() {
        if (!$this->getInitialized()) {
            $this->initialize();
        }
        return $this->options;
    }

    /**
     * (Pře)nastavení kolekce options vlasntím zpsůobem
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param array $options
     */
    public function setOptions(array $options) {
        $this->options = $options;
    }

    /**
     * Označení, zda již proběhlo načtení options do CACHE
     * Pozn.: v případě vlastní inicializace, je třeba aktulizovat "ručně"
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @return bool
     */
    private function getInitialized() {
        return $this->initialized;
    }

    /**
     * Nastavení označení, zda již proběhlo načtení options do CACHE
     * Pozn.: v případě vlastní inicializace, je třeba aktulizovat "ručně"
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param bool $initialized
     */
    private function setInitialized($initialized) {
        $this->initialized = $initialized;
    }

    /**
     * Provode novou inicializaci options hodnot na základě dříve zadaného případného prefixu
     * Pozn.: volá se automaticky v @see getCurrentOptionMeta
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    protected function initialize() {
        $optionsPrefix = $this->getOptionsPrefix();
        $this->setOptions(self::getWpOptions($optionsPrefix));
        $this->setInitialized(true);
    }

    /**
     * Vrátí hodnotu pro zadaný název (klíč) pokud existuje ve výčtu získaných options hodnot (podle dříve zadaného případného prefixu)
     * Pozn.: načtení options hodnot (na prefixu) se provádí až při prvním volání
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param string $name
     * @return string|null
     */
    public function getOption($name) {
        $options = $this->getOptions();
        if (KT::arrayIssetAndNotEmpty($options)) {
            foreach ($options as $optionName => $optionValue) {
                if ($optionName == $name) {
                    return $optionValue;
                }
            }
        }
        return null;
    }

    /**
     * Funkcí vrátí všechny option podle případného prefixu ve tvaru název (klíč) => hodnota
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @global WP_Database $wpdb
     * @param int $postId
     * @param string $prefix
     * @return array
     */
    public static function getWpOptions($prefix) {
        global $wpdb;
        $query = "SELECT option_name, option_value FROM {$wpdb->options}";
        if (isset($prefix)) {
            $query .= " WHERE option_name LIKE '%s'";
            $options = $wpdb->get_results($wpdb->prepare($query, $prefix . "%"), ARRAY_A);
        } else {
            $options = $wpdb->get_results($query);
        }
        if (kt_isset_and_not_empty($options) && is_array($options)) {
            foreach ($options as $option) {
                $results[$option["option_name"]] = $option["option_value"];
            }
            return $results;
        } else {
            return array();
        }
    }

    /**
     * Získání případné konkrétní hodnoty option podle názvu (klíče) nebo KT_EMPTY_TEXT, či null
     *
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     *
     * @param int $postId
     * @param string $optionName
     * @param boolean $emptyText
     * @return string
     */
    public static function getWpOption($optionName, $emptyText = true) {
        $optionValue = get_option($optionName);
        if (KT::issetAndNotEmpty($optionValue)) {
            return $optionValue;
        }
        if ($emptyText === true) {
            return KT_EMPTY_SYMBOL;
        }
        return null;
    }

}
