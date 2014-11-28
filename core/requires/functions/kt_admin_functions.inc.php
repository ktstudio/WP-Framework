<?php

function kt_show_admin_page($path, $page, $action = null) {
    if (kt_isset_and_not_empty($page)) {
        echo "<div id=\"kt-template-right-part\">";
        if ($action == null) {
            $action = htmlspecialchars($_GET["action"]);
        }
        switch ($action) {
            case "update":
            case "create":
                require_once(path_join($path, "kt_{$page}_detail.tmp.php"));
                break;
            case "show":
                require_once(path_join($path, "kt_{$page}.tmp.php"));
                break;
            default: // list
                require_once (path_join($path, "kt_{$page}_list.tmp.php"));
        }

        echo "</div>";
    } else {
        throw new KT_Not_Set_Argument_Exception("page");
    }
}
