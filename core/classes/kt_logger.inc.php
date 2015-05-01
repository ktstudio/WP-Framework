<?php

/**
 * Základní logger do DB (tabulka kt_logs)
 * 
 * @author Martin Hlaváč
 * @link http://www.ktstudio.cz 
 */
class KT_Logger {
    // --- veřejné metody ------------------------

    /**
     * Zalogování zprávy typu, respl levelu TRACE
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
     * @param string $message
     * @return boolean
     */
    public static function debug($message) {
        return self::log(KT_Log_Level_Enum::DEBUG, $message);
    }

    /**
     * Zalogování zprávy typu, respl levelu INFO
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
     * @param string $message
     * @return boolean
     */
    public static function warning($message) {
        return self::log(KT_Log_Level_Enum::WARNING, $message);
    }

    /**
     * Zalogování zprávy typu, respl levelu ERROR
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
     * @global \WPDB $wpdb
     * @param int $level
     * @param string $message
     * @return boolean
     */
    private static function log($level, $message) {
        if ($level >= KT_CORE_LOG_MIN_LEVEL) { // kontrola minimální povolené úrovně logování
            $user = wp_get_current_user();
            $isUserSigned = $user->exists();
            if (KT_CORE_LOG_ONLY_SIGNED_USERS && !$isUserSigned) {
                return null; // uživatel není přihlášen a je to požadováno
            }
            if (KT::issetAndNotEmpty($message)) {
                $args = array(
                    KT_Log_Model::LEVEL_ID_COLUMN => $level,
                    KT_Log_Model::MESSAGE_COLUMN => htmlspecialchars(trim($message)),
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
