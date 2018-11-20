<?php

$ktWpCronPage = new KT_Custom_Metaboxes_Subpage("tools.php", __("(KT) WP CRON", "KT_CORE_DOMAIN"), __("(KT) WP CRON", "KT_CORE_DOMAIN"), "edit_theme_options", KT_WP_Configurator::WP_CRON_PAGE_SLUG);
$ktWpCronPage->register();

KT_Metabox::createCustom(KT_WP_Configurator::WP_CRON_PAGE_SLUG . "-events-metabox", __("Scheduled events", "KT_CORE_DOMAIN"), KT_WP_Configurator::getWpCronSlug(), "kt_cron_events_metabox_callback");

function kt_cron_events_metabox_callback() {
    $crons = _get_cron_array();
    if (KT::arrayIssetAndNotEmpty($crons)) {
        echo "<ol>";
        foreach ($crons as $time => $events) {
            if (KT::arrayIssetAndNotEmpty($events)) {
                foreach ($events as $name => $data) {
                    if (KT::arrayIssetAndNotEmpty($data)) {
                        foreach ($data as $key => $values) {
                            if (KT::arrayIssetAndNotEmpty($values)) {
                                $schedule = KT::arrayTryGetValue($values, "schedule");
                                //$interval = KT::arrayTryGetValue($values, "interval");
                                $date = date("H:i:s", $time);
                                echo "<li><b>$name</b> - <i>$schedule</i> @ $date</li>";
                            }
                        }
                    }
                }
            }
        }
        echo "</ol>";
    }
}

$intervalsMetabox = KT_Metabox::createCustom(KT_WP_Configurator::WP_CRON_PAGE_SLUG . "-intervals-metabox", __("Established intervals", "KT_CORE_DOMAIN"), KT_WP_Configurator::getWpCronSlug(), "kt_cron_schedules_metabox_callback", false);
$intervalsMetabox->setContext(KT_MetaBox::CONTEXT_SIDE)
        ->register();

function kt_cron_schedules_metabox_callback() {
    $schedules = wp_get_schedules();
    if (KT::arrayIssetAndNotEmpty($schedules)) {
        echo "<ol>";
        foreach ($schedules as $key => $values) {
            if (KT::arrayIssetAndNotEmpty($values)) {
                $interval = KT::arrayTryGetValue($values, "interval");
                $display = KT::arrayTryGetValue($values, "display");
                echo "<li><b>$key</b> (<i>$display</i>) - $interval [s]</li>";
            }
        }
        echo "</ol>";
    }
}
