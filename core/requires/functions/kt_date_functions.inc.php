<?php

/**
 * Vrátí aktuální datum a čas v obecném tvaru
 * @param string $format
 * @param string $timeStampText
 * @return date
 */
function kt_date_get_now($format = "Y-m-d H:i:s", $timeStampText = null) {
    if (kt_isset_and_not_empty($timeStampText)) {
        return date($format, strtotime($timeStampText));
    }
    return date($format);
}
