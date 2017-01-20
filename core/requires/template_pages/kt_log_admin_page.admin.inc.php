<?php

add_action("init", "kt_init_logger_tools_admin_page", 99);

function kt_init_logger_tools_admin_page() {
    if (KT_Logger::getAllowToolsAdminPage()) {
        $template = new KT_Custom_Metaboxes_Subpage(
                "tools.php", __("List of KT logs", "KT_CORE_DOMAIN"), __("(KT) Logs", "KT_CORE_DOMAIN"), "edit_theme_options", KT_Log_Model::FORM_PREFIX
        );

        $crudList = new KT_CRUD_Admin_List("KT_Log_Model", KT_Log_Model::TABLE);
        $crudList->setTemplateTitle(__("List of recorded (KT) logs", "KT_CORE_DOMAIN"));
        $crudList->getRepository()->setOrder(KT_Log_Model::DATE_COLUMN, KT_Repository::ORDER_DESC);

        $crudList->addColumn(KT_Log_Model::LEVEL_ID_COLUMN)
                ->setLabel(__("Level", "KT_CORE_DOMAIN"))
                ->setType(KT_CRUD_Admin_Column::CUSTOM_TYPE)
                ->setCustomCallbackFunction("getLevelColumnValue", true);
        $crudList->addColumn(KT_Log_Model::SCOPE_COLUMN)
                ->setLabel(__("Scope", "KT_CORE_DOMAIN"));
        $crudList->addColumn(KT_Log_Model::MESSAGE_COLUMN)
                ->setLabel(__("Message", "KT_CORE_DOMAIN"))
                ->setType(KT_CRUD_Admin_Column::CUSTOM_TYPE)
                ->setCustomCallbackFunction("getMessageColumnValue", true);
        $crudList->addColumn(KT_Log_Model::DATE_COLUMN)
                ->setLabel(__("Date", "KT_CORE_DOMAIN"));
        $crudList->addColumn(KT_Log_Model::LOGGED_USER_NAME_COLUMN)
                ->setLabel(__("User", "KT_CORE_DOMAIN"));
        $crudList->addColumn(KT_Log_Model::FILE_COLUMN)
                ->setLabel(__("File", "KT_CORE_DOMAIN"))
                ->setType(KT_CRUD_Admin_Column::CUSTOM_TYPE)
                ->setCustomCallbackFunction("getFileColumnValue", true);
        $crudList->addColumn(KT_Log_Model::LINE_COLUMN)
                ->setLabel(__("Row", "KT_CORE_DOMAIN"));

        $template->setCrudList($crudList);

        // --- registrace stránky ------------------

        $template->setDefaultCallBackFunction(KT_Custom_Metaboxes_Base::CRUD_LIST_SCREEN)
                ->register();
    }
}
