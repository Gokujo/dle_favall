<?php
/*
 * @ SkripTers.biz Decoder PHP
 */

@error_reporting(30719 ^ 2 ^ 8);
@ini_set("display_errors", true);
@ini_set("html_errors", false);
@ini_set("error_reporting", 30719 ^ 2 ^ 8);
define("DATALIFEENGINE", true);
define("ROOT_DIR", substr( __DIR__, 0, -19));
define("ENGINE_DIR", ROOT_DIR . "/engine");

require_once (ENGINE_DIR . '/classes/plugins.class.php');
include_once (DLEPlugins::Check(ENGINE_DIR . "/data/config.php"));
include_once (DLEPlugins::Check(ENGINE_DIR . "/data/favall_config.php"));
include_once (DLEPlugins::Check(ROOT_DIR . "/language/Russian/website.lng"));
require_once (DLEPlugins::Check(ENGINE_DIR . "/classes/mysql.php"));
require_once (DLEPlugins::Check(ENGINE_DIR . "/data/dbconfig.php"));
require_once (DLEPlugins::Check(ENGINE_DIR . "/modules/functions.php"));

if (empty($config["http_home_url"])) {
	$config["http_home_url"] = explode("engine/ajax/favall/favall_inc.php", $_SERVER["PHP_SELF"]);
	$config["http_home_url"] = reset($config["http_home_url"]);
	$config["http_home_url"] = "http://" . $_SERVER["HTTP_HOST"] . $config["http_home_url"];
}

if (strpos($config['http_home_url'], "//") === 0) {
	$config['http_home_url'] = isSSL() ? $config['http_home_url'] = "https:".$config['http_home_url'] : $config['http_home_url'] = "http:".$config['http_home_url'];
} elseif (strpos($config['http_home_url'], "/") === 0) {
	$config['http_home_url'] = isSSL() ? $config['http_home_url'] = "https://".$_SERVER['HTTP_HOST'].$config['http_home_url'] : "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];
} elseif( isSSL() AND stripos( $config['http_home_url'], 'http://' ) !== false ) {
	$config['http_home_url'] = str_replace( "http://", "https://", $config['http_home_url'] );
}

if (substr ( $config['http_home_url'], - 1, 1 ) != '/') $config['http_home_url'] .= '/';
dle_session();
require_once (DLEPlugins::Check(ENGINE_DIR . "/classes/templates.class.php"));
$tpl = new dle_template();
$tpl->dir = ROOT_DIR . "/templates/" . totranslit($config["skin"], false, false);
define("TEMPLATE_DIR", $tpl->dir);
$json = array("success" => true, "result" => "", "fav_name" => "", "fav_id" => "");
$sicret_key = "fa2v_vk56a@K#e_all_53gh(\$.=p2";
if (isset($_SERVER["HTTP_HOST"])) {
    $host = $_SERVER["HTTP_HOST"];
} else {
    $host = getenv("HTTP_HOST");
}
define("FAVALL", true);
$license_favall = "";

require_once (DLEPlugins::Check(ENGINE_DIR . "/modules/sitelogin.php"));

if (!$is_logged) {
    $member_id["user_group"] = 5;
}
$fav_type = $_POST["fav_type"];
if (!$fav_type && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Ошибка данных типа запроса.";
}
if ($fav_type == "add") {
    if ($member_id["user_group"] == 5) {
        $json["success"] = false;
        $json["result"] = "Нет прав доступа, Вы не авторизованы.";
    }
    if ($json["success"] == true) {
        $fav_name = $_POST["fav_name"];
        if (!$fav_name && $json["success"] == true) {
            $json["success"] = false;
            $json["result"] = "Поле \"Имя\" обязательно к заполнению.";
        }
    }
    if ($json["success"] == true) {
        $bad_words = explode("|~|", $favall_config["bad_words"]);
        if(in_array($fav_name, $bad_words)){
			$json["success"] = false;
			$json["result"] = "Поле \"Имя\" содержит не допустимое слово.";
		}
    }
    if ($json["success"] == true && preg_match("/[\\||\\'|\\<|\\>|\\[|\\]|\"|\\!|\\?|\$|\\@|\\/|\\\\|\\&\\~\\*\\{\\+\\,\\.]/", $fav_name)) {
        $json["success"] = false;
        $json["result"] = "Поле \"Имя\" содержит не допустимый(е) символы.";
    }
    if ($json["success"] == true) {
        $fav_name_strlen = dle_strlen(trim($fav_name), $config["charset"]);
        if ($fav_name_strlen < 5 && $json["success"] == true) {
            $json["success"] = false;
            $json["result"] = "Длинна поля \"Имя\" не может быть меньше 5 символов.";
        }
        if (30 < $fav_name_strlen && $json["success"] == true) {
            $json["success"] = false;
            $json["result"] = "Длинна поля \"Имя\" не может быть больше 30 символов.";
        }
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT `name_fav`, `type_fav` FROM " . PREFIX . "_favall WHERE ( `name_fav` = '" . $fav_name . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "' ) OR ( `name_fav` = '" . $fav_name . "' AND `type_fav` = '1' )");
        if ($row["name_fav"]) {
            $json["success"] = false;
            if ($row["type_fav"] == 1) {
                $json["result"] = "Закладки с таким именем создана по умолчанию.";
            } else {
                $json["result"] = "У Вас уже есть закладка с таким именем.";
            }
        }
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT count(*) AS count FROM " . PREFIX . "_favall WHERE `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "'");
        if ($favall_config["count_favall_user"] <= $row["count"]) {
            $json["success"] = false;
            $json["result"] = "Вы достигли максимального колличество закладок.<div style=\"background: #FEE2E1;color: #9F270D;font-size: 12px;margin-top: 10px;padding: 5px 0;text-align: center;\">Максимальное число закладок " . $favall_config["count_favall_user"] . ".</div>";
        }
    }
    if ($json["success"] == true) {
        $db->query("INSERT INTO " . PREFIX . "_favall ( name_fav, type_fav, user_id_fav ) values ( '" . $fav_name . "', '2', '" . $member_id["user_id"] . "' )");
        $json["fav_id"] = $db->insert_id();
        $json["result"] = "<li class=\"dd-item dd3-item\" style=\"display:none;\" data-favall_id=\"" . $json["fav_id"] . "\"><div class=\"dd-handle dd3-handle\">.</div><div class=\"dd3-content list_form\"><span class=\"list1\">" . $fav_name . " (0)</span><span class=\"list2\"> <span>.../favall/" . $json["fav_id"] . "/user/" . $member_id["name"] . "/</span></span><span class=\"list3\"><div data-favall=\"edit\" class=\"favall_edit\"></div><div data-favall=\"dell\" class=\"favall_dell\"></div></span></div></li>";
    }
}
if ($fav_type == "edit") {
    if ($member_id["user_group"] == 5) {
        $json["success"] = false;
        $json["result"] = "Нет прав доступа, Вы не авторизованы.";
    }
    if ($json["success"] == true) {
        $fav_id = (int) $_POST["fav_id"];
        if ($fav_id < 1) {
            $json["success"] = false;
            $json["result"] = "Ошибка ID данных.";
        }
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT `id_fav`, `name_fav` FROM " . PREFIX . "_favall WHERE `id_fav` = '" . $fav_id . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "'");
        if (!$row["id_fav"]) {
            $json["success"] = false;
            $json["result"] = "Закладки под таким ID в базе ненайдено.";
        }
    }
    if ($json["success"] == true) {
        $json["fav_name"] = $row["name_fav"];
        $json["fav_id"] = $row["id_fav"];
    }
}
if ($fav_type == "update") {
    if ($member_id["user_group"] == 5) {
        $json["success"] = false;
        $json["result"] = "Нет прав доступа, Вы не авторизованы.";
    }
    if ($json["success"] == true) {
        $fav_id = (int) $_POST["fav_id"];
        if ($fav_id < 1) {
            $json["success"] = false;
            $json["result"] = "Ошибка ID данных.";
        }
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT f.id_fav, f.name_fav, GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all FROM " . PREFIX . "_favall f LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list ) WHERE f.id_fav = '" . $fav_id . "' AND f.type_fav = '2' AND  f.user_id_fav = '" . $member_id["user_id"] . "'GROUP BY f.id_fav LIMIT 1");
        if (!$row["id_fav"]) {
            $json["success"] = false;
            $json["result"] = "Закладки под таким ID в базе ненайдено.";
        }
    }
    if ($json["success"] == true) {
        $fav_name = $_POST["fav_name"];
        if (!$fav_name && $json["success"] == true) {
            $json["success"] = false;
            $json["result"] = "Поле \"Имя\" обязательно к заполнению.";
        }
    }
    if ($json["success"] == true) {
        $bad_words = explode("|~|", $favall_config["bad_words"]);
        foreach ($bad_words as $i) {
            if (preg_match("/" . $i . "/i", $fav_name) && $json["success"] == true) {
                $json["success"] = false;
                $json["result"] = "Поле \"Имя\" содержит не допустимое слово.";
            }
        }
    }
    if ($json["success"] == true && preg_match("/[\\||\\'|\\<|\\>|\\[|\\]|\"|\\!|\\?|\$|\\@|\\/|\\\\|\\&\\~\\*\\{\\+\\,\\.]/", $fav_name)) {
        $json["success"] = false;
        $json["result"] = "Поле \"Имя\" содержит не допустимый(е) символы.";
    }
    if ($json["success"] == true) {
        $fav_name_strlen = dle_strlen(trim($fav_name), $config["charset"]);
        if ($fav_name_strlen < 5 && $json["success"] == true) {
            $json["success"] = false;
            $json["result"] = "Длинна поля \"Имя\" не может быть меньше 5 символов.";
        }
        if (30 < $fav_name_strlen && $json["success"] == true) {
            $json["success"] = false;
            $json["result"] = "Длинна поля \"Имя\" не может быть больше 30 символов.";
        }
    }
    if ($json["success"] == true) {
        $row_d = $db->super_query("SELECT `name_fav`, `type_fav` FROM " . PREFIX . "_favall WHERE ( `id_fav` != '" . $row["id_fav"] . "' AND `name_fav` = '" . $fav_name . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "' ) OR ( `id_fav` != '" . $row["id_fav"] . "' AND `name_fav` = '" . $fav_name . "' AND `type_fav` = '1' )");
        if ($row_d["name_fav"]) {
            $json["success"] = false;
            if ($row_d["type_fav"] == 1) {
                $json["result"] = "Закладки с таким именем создана по умолчанию.";
            } else {
                $json["result"] = "У Вас уже есть закладка с таким именем.";
            }
        }
    }
    if ($json["success"] == true) {
        $db->query("UPDATE " . PREFIX . "_favall SET `name_fav` = '" . $fav_name . "' WHERE `id_fav` = '" . $row["id_fav"] . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "'");
        if ($row["id_post_list_all"]) {
            $id_post_list_all_array = explode(",", $row["id_post_list_all"]);
            $id_post_list_all_count = count($id_post_list_all_array);
        } else {
            $id_post_list_all_count = "0";
        }
        if (0 < $id_post_list_all_count) {
            $link_fl = "<a href=\"" . $config["http_home_url"] . "favall/" . $row["id_fav"] . "/user/" . $member_id["name"] . "/\" target=\"_blank\">.../favall/" . $row["id_fav"] . "/user/" . $member_id["name"] . "/</a>";
        } else {
            $link_fl = ".../favall/" . $row["id_fav"] . "/user/" . $member_id["name"] . "/";
        }
        $json["result"] = "<div class=\"dd-handle dd3-handle\">Drag</div><div class=\"dd3-content list_form\"><span class=\"list1\">" . $fav_name . " (" . $id_post_list_all_count . ")</span><span class=\"list2\"> <span>" . $link_fl . "</span></span><span class=\"list3\"><div data-favall=\"edit\" class=\"favall_edit\"></div><div data-favall=\"dell\" class=\"favall_dell\"></div></span></div>";
    }
}
if ($fav_type == "dell") {
    if ($member_id["user_group"] == 5) {
        $json["success"] = false;
        $json["result"] = "Нет прав доступа, Вы не авторизованы.";
    }
    if ($json["success"] == true) {
        $fav_id = (int) $_POST["fav_id"];
        if ($fav_id < 1) {
            $json["success"] = false;
            $json["result"] = "Ошибка ID данных.";
        }
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT `id_fav` FROM " . PREFIX . "_favall WHERE `id_fav` = '" . $fav_id . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "'");
        if (!$row["id_fav"]) {
            $json["success"] = false;
            $json["result"] = "Закладки под таким ID в базе ненайдено.";
        }
    }
    if ($json["success"] == true) {
        $db->query("DELETE FROM " . PREFIX . "_favall WHERE `id_fav` = '" . $fav_id . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "'");
        $db->query("DELETE FROM " . PREFIX . "_favall_user WHERE `id_fav_list` = '" . $fav_id . "'");
    }
}
if ($fav_type == "update_list") {
    if ($member_id["user_group"] == 5) {
        $json["success"] = false;
        $json["result"] = "Нет прав доступа, Вы не авторизованы.";
    }
    if ($json["success"] == true && !count($_POST["update_list"])) {
        $json["success"] = false;
        $json["result"] = "Ошибка данных списка закладок.";
    }
    if ($json["success"] == true) {
        $n = 0;
        foreach ($_POST["update_list"] as $i) {
            $n++;
            $id = (int) $i["favall_id"];
            $db->query("UPDATE " . PREFIX . "_favall SET `position_fav` = '" . $n . "' WHERE `id_fav` = '" . $id . "' AND `type_fav` = '2' AND `user_id_fav` = '" . $member_id["user_id"] . "'");
        }
    }
}
if ($fav_type == "add_content_user" || $fav_type == "change_content_user") {
    if ($fav_type == "add_content_user") {
        $count_s = (int) $_POST["count_s"];
        if ($count_s < 1) {
            $count_s = 0;
        }
    } else {
        $count_s = 0;
    }
    $search_q = "";
    $name_s = $db->safesql(htmlspecialchars(trim(strip_tags(convert_unicode($_POST["name_s"], $config["charset"]))), ENT_QUOTES, $config["charset"]));
    $name_li = dle_strlen(trim($name_s), $config["charset"]);
    if ($name_li < 30 && 2 < $name_li) {
        $search_q .= " AND ( f.name_fav LIKE '%" . $name_s . "%' OR p.name LIKE '%" . $name_s . "%' )";
    }
    $sort_s = (int) $_POST["sort_s"];
    switch ($sort_s) {
        case "1":
            $order_by = "f.id_fav";
            break;
        case "2":
            $order_by = "id_post_list_count";
            break;
        case "3":
            $order_by = "f.name_fav";
            break;
        case "4":
            $order_by = "p.name";
            break;
        default:
            $order_by = "f.id_fav";
            $desc_asc_s = (int) $_POST["desc_asc_s"];
            switch ($desc_asc_s) {
                case "1":
                    $descasc = "DESC";
                    break;
                case "2":
                    $descasc = "ASC";
                    break;
                default:
                    $descasc = "DESC";
                    $sql_result = $db->query("SELECT SQL_CALC_FOUND_ROWSf.id_fav, f.name_fav, f.position_fav, GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all, count( u.id_post_list ) AS id_post_list_count, p.name, p.foto FROM " . PREFIX . "_favall f LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list ) LEFT JOIN " . PREFIX . "_users p ON ( f.user_id_fav = p.user_id ) WHERE f.type_fav = '2' " . $search_q . " GROUP BYf.id_favORDER BY " . $order_by . " " . $descasc . " LIMIT " . $count_s . ", " . $favall_config["count_web"]);
                    $count_all = $db->super_query("SELECT FOUND_ROWS() as count");
                    $json["count"] = $count_all["count"];
                    $tpl->load_template("favall/favall_catalog_list.tpl");
                    while ($row = $db->get_row($sql_result)) {
                        $tpl->set("{favall_id}", $row["id_fav"]);
                        $tpl->set("{favall_name}", $row["name_fav"]);
                        $tpl->set("{favall_user}", $row["name"]);
                        if ($config["allow_alt_url"]) {
                            $go_page = $config["http_home_url"] . "user/" . urlencode($row["name"]) . "/";
                        } else {
                            $go_page = (string) $PHP_SELF . "?subaction=userinfo&user=" . urlencode($row["name"]);
                        }
                        $go_page = "onclick=\"ShowProfile('" . urlencode($row["name"]) . "', '" . htmlspecialchars($go_page, ENT_QUOTES, $config["charset"]) . "', '" . $user_group[$member_id["user_group"]]["admin_editusers"] . "'); return false;\"";
                        if ($config["allow_alt_url"]) {
                            $tpl->set("[favall_user_url]", "<a " . $go_page . " href=\"" . $config["http_home_url"] . "user/" . urlencode($row["name"]) . "/\">");
                        } else {
                            $tpl->set("[favall_user_url]", "<a " . $go_page . " href=\"" . $PHP_SELF . "?subaction=userinfo&amp;user=" . urlencode($row["name"]) . "\">");
                        }
                        $tpl->set("[/favall_user_url]", "</a>");
                        $tpl->set("{favall_count}", $row["id_post_list_count"]);
                        if (count(explode("@", $row["foto"])) == 2) {
                            $tpl->set("{foto}", "http://www.gravatar.com/avatar/" . md5(trim($row["foto"])) . "?s=" . intval($user_group[$row["user_group"]]["max_foto"]));
                        } else {
                            if ($row["foto"] && file_exists(ROOT_DIR . "/uploads/fotos/" . $row["foto"])) {
                                $tpl->set("{foto}", $config["http_home_url"] . "uploads/fotos/" . $row["foto"]);
                            } else {
                                $tpl->set("{foto}", "{THEME}/dleimages/noavatar.png");
                            }
                        }
                        if (0 < $row["id_post_list_count"]) {
                            $tpl->set("{favall_url}", $config["http_home_url"] . "favall/" . $row["id_fav"] . "/user/" . $row["name"] . "/");
                            $tpl->set("[favall_url]", "");
                            $tpl->set("[/favall_url]", "");
                        } else {
                            $tpl->set("{favall_url}", "");
                            $tpl->set_block("'\\[favall_url\\](.*?)\\[/favall_url\\]'si", "");
                        }
                        $tpl->compile("favall_list");
                        $tpl->result["favall_list"] = preg_replace_callback("#\\[declination_fav=(\\d+)\\](.+?)\\[/declination_fav\\]#is", "declination_fav", $tpl->result["favall_list"]);
                        $tpl->result["favall_list"] = str_ireplace("{THEME}", $config["http_home_url"] . "templates/" . $config["skin"], $tpl->result["favall_list"]);
                    }
                    $json["result"] = $tpl->result["favall_list"];
            }
    }
}
echo json_encode($json);
function declination_fav($d)
{
    $num = $d[1] % 100;
    if (19 < $num) {
        $num = $num % 10;
    }
    $text = explode("|", $d[2]);
    switch ($num) {
        case 1:
            return $text[0] . $text[1];
        case 2:
        case 3:
        case 4:
            return $text[0] . $text[2];
        default:
            return $text[0] . $text[3];
    }
}

?>