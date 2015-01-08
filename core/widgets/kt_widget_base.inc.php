<?php

/**
 * Základní, abstraktní (KT) widget společný pro všechny ostatní
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Widget_Base extends WP_Widget implements KT_Registrable {

    private $description;

    public function __construct($name, $title, $description) {
        parent::__construct(
                $name, // Base ID
                $title, // Name
                array("description" => $description,) // Args
        );
        $this->description = $description;
    }

    /**
     * Vrátí zadaný název, či v terminologie \WP_Widget base_id
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    function getName() {
        return $this->id_base;
    }

    /**
     * Vrátí zadaný titulek, či v terminologie \WP_Widget name
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    function getTitle() {
        return $this->name;
    }

    /**
     * Vrátí zadaný zadaný popisek
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @return string
     */
    function getDescription() {
        return $this->description;
    }

    /**
     * Základní (ne)formulář, resp. výpis zadaného popisku a informace, že není dostupná konfigurace widgetu
     * Pozn.: pokud chcete vlastní konfiguraci widgetu, tak je třeba tuto metodu přepsat a udělat vlastní formulář
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     * 
     * @param array $instance
     * @return string
     */
    public function form($instance) {
        echo "<p>{$this->getDescription()}</p>";
        return parent::form($instance);
    }

    /**
     * Registrace widgetu v rámci Wordpressu podle zadaných hodnot a parametrů
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz
     */
    public function register() {
        $functionName = "register_widget(\"$this->id_base\");";
        add_action("widgets_init", create_function("", $functionName));
    }

}
