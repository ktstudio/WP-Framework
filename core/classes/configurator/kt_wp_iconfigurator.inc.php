<?php

/**
 * Rozhraní pro konfigurátory, které je možé registrovat do WP_Configuratoru
 */
interface KT_WP_IConfigurator {

    /**
     *  Inicializační metoda, volá se ve WP_Configuratoru automaticky
     */
    public function initialize();
}
