<?php

/**
 * Základní, abstraktní (KT) widget společný pro všechny ostatní
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz
 */
abstract class KT_Widget_Base extends WP_Widget implements KT_Registrable {

    public function __construct($name, $title, $description) {
        parent::__construct(
                $name, // Base ID
                $title, // Name
                array("description" => $description,) // Args
        );
    }

    /**
     * Registrace widgetu v rámci Wordpressu
     */
    public function register() {
        $functionName = "register_widget(\"$this->id_base\");";
        add_action("widgets_init", create_function("", $functionName));
    }

}
