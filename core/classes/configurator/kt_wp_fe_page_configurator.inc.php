<?php

/**
 * Třída pro konfiguraci frontendových stránek
 * @author Jan Pokorný
 */
final class KT_WP_Fe_Page_Configurator implements KT_WP_IConfigurator {

    private $removeAuthorPage;
    private $removeAttachmentPage;

    /**
     *
     * Odstraní stránku s autorem
     *
     * @author Jan Pokorný
     * @param bool $remove
     * @return $this
     */
    public function removeAuthorPage($remove = true) {
        $this->removeAuthorPage = $remove;
        return $this;
    }

    /**
     *
     * Odstraní stránku s přílohami
     *
     * @author Jan Pokorný
     * @param bool $remove
     * @return $this
     */
    public function removeAttachmentPage($remove = true) {
        $this->removeAttachmentPage = $remove;
        return $this;
    }

    public function initialize() {
        add_action('template_redirect', [$this, 'templateRedirectAction']);
    }

    /**
     * Callback akce NEVOLAT VEŘEJNĚ
     */
    public function templateRedirectAction() {
        if ($this->removeAuthorPage && is_author()) {
            self::notFoundAction();
        }

        if ($this->removeAttachmentPage && is_attachment()) {
            self::notFoundAction();
        }
    }

    private static function notFoundAction() {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }

}
