<?php

/**
 * Základní logger do DB (tabulka kt_logs)
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz 
 */
class KT_Logger {

    private static $minLevel = KT_Log_Level_Enum::INFO;
    private static $onlyForSignedUsers = true;
    private static $allowToolsAdminPage = true;

    // --- getry & setry ------------------------

    /**
     * Vrátí minimální povolený level
     * @see KT_Log_Level_Enum
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return int
     */
    public static function getMinLevel() {
        return self::$minLevel;
    }

    /**
     * Vrátí označení povolení pouze pro přihlášení uživatele
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return boolean
     */
    public static function getOnlyForSignedUsers() {
        return self::$onlyForSignedUsers;
    }

    /**
     * Vrátí označení povolení administrační stránky v nástrojích
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @return boolean
     */
    public static function getAllowToolsAdminPage() {
        return self::$allowToolsAdminPage;
    }

    /**
     * Nastaví minimální povolený level
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param int $minLevel
     */
    public static function setMinLevel($minLevel) {
        self::$minLevel = KT::tryGetInt($minLevel);
    }

    /**
     * Nastaví označení povolení pouze pro přihlášení uživatele
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param boolean $onlyForSignedUsers
     */
    public static function setOnlyForSignedUsers($onlyForSignedUsers) {
        self::$onlyForSignedUsers = KT::tryGetBool($onlyForSignedUsers);
    }

    /**
     * Nastaví označení povolení administrační stránky v nástrojích
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param boolean $allowToolsAdminPage
     */
    public static function setAllowToolsAdminPage($allowToolsAdminPage) {
        self::$allowToolsAdminPage = KT::tryGetBool($allowToolsAdminPage);
    }

    // --- veřejné metody ------------------------

    /**
     * Zalogování zprávy typu, respl levelu TRACE
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string $message
     * @return boolean
     */
    public static function trace($message) {
        return self::log(KT_Log_Level_Enum::TRACE, $message);
    }

    /**
     * Zalogování zprávy typu, respl levelu DEBUG
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string $message
     * @return boolean
     */
    public static function debug($message) {
        return self::log(KT_Log_Level_Enum::DEBUG, $message);
    }

    /**
     * Zalogování zprávy typu, respl levelu INFO
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string $message
     * @return boolean
     */
    public static function info($message) {
        return self::log(KT_Log_Level_Enum::INFO, $message);
    }

    /**
     * Zalogování zprávy typu, respl levelu WARNING
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string $message
     * @return boolean
     */
    public static function warning($message) {
        return self::log(KT_Log_Level_Enum::WARNING, $message);
    }

    /**
     * Zalogování zprávy typu, respl levelu ERROR
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @param string $message
     * @return boolean
     */
    public static function error($message) {
        return self::log(KT_Log_Level_Enum::ERROR, $message);
    }

    // --- neveřejné metody ------------------------

    /**
     * Interní zalogování do DB
     * 
     * @author Martin Hlaváč
     * @link http://www.ktstudio.cz 
     * 
     * @global \WPDB $wpdb
     * @param int $level
     * @param string $message
     * @return boolean
     */
    private static function log($level, $message) {
        if ($level >= self::getMinLevel()) { // kontrola minimální povolené úrovně logování
            $user = wp_get_current_user();
            $isUserSigned = $user->exists();
            if (self::getOnlyForSignedUsers() && !$isUserSigned) {
                return null; // uživatel není přihlášen a je to požadováno
            }
            if (KT::issetAndNotEmpty($message)) {
                $args = array(
                    KT_Log_Model::LEVEL_ID_COLUMN => $level,
                    KT_Log_Model::MESSAGE_COLUMN => filter_var($message, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                    KT_Log_Model::DATE_COLUMN => KT::dateNow(),
                );
                if ($isUserSigned) {
                    $args[KT_Log_Model::LOGGED_USER_NAME_COLUMN] = $user->user_login;
                }
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                if (KT::arrayIssetAndNotEmpty($backtrace)) {
                    $lastBacktrace = $backtrace[1]; // první index je vždy právě KT_Logger
                    $args[KT_Log_Model::FILE_COLUMN] = $lastBacktrace["file"];
                    $args[KT_Log_Model::LINE_COLUMN] = $lastBacktrace["line"];
                }
                /* @var $wpdb \WPDB */
                global $wpdb;
                $logId = $wpdb->insert(KT_Log_Model::TABLE, $args);
                return $logId > 0;
            }
        }
        return null;
    }

}
