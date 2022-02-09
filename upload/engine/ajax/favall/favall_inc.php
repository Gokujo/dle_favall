<?php
/*
 * @ SkripTers.biz Decoder PHP
 */


@error_reporting(30719 ^ 2 ^ 8);
@ini_set("display_errors", true);
@ini_set("html_errors", false);
@ini_set("error_reporting", 30719 ^ 2 ^ 8);
define("DATALIFEENGINE", true);
define("ROOT_DIR", substr(dirname(__FILE__), 0, -19));
define("ENGINE_DIR", ROOT_DIR . "/engine");
include (DLEPlugins::Check(ENGINE_DIR . "/data/favall_config.php"));
include (DLEPlugins::Check(ENGINE_DIR . "/data/config.php"));
include (DLEPlugins::Check(ROOT_DIR . "/language/Russian/website.lng"));
if ($config["http_home_url"] == "") {
    $config["http_home_url"] = explode("engine/ajax/favall/favall_inc.php", $_SERVER["PHP_SELF"]);
    $config["http_home_url"] = reset($config["http_home_url"]);
    $config["http_home_url"] = "http://" . $_SERVER["HTTP_HOST"] . $config["http_home_url"];
}
require_once (DLEPlugins::Check(ENGINE_DIR . "/classes/mysql.php"));
require_once (DLEPlugins::Check(ENGINE_DIR . "/data/dbconfig.php"));
require_once (DLEPlugins::Check(ENGINE_DIR . "/modules/functions.php"));
dle_session();
$json = array("success" => true, "result" => "", "fav_name" => "", "fav_id" => "");
$sicret_key = "fa2v_vk56a@K#e_all_53gh(\$.=p2";
if (isset($_SERVER["HTTP_HOST"])) {
    $host = $_SERVER["HTTP_HOST"];
} else {
    $host = getenv("HTTP_HOST");
}
define("FAVALL", true);

require_once (DLEPlugins::Check(ENGINE_DIR . "/modules/sitelogin.php"));
if (!$is_logged && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Ошибка доступа.";
}
if ($member_id["user_group"] != 1 && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Ошибка доступа.";
}
$fav_type = $_POST["fav_type"];
if (!$fav_type && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Ошибка данных типа запроса.";
}
if ($fav_type == "add") {
    $fav_name = $_POST["fav_name"];
    if (!$fav_name && $json["success"] == true) {
        $json["success"] = false;
        $json["result"] = "Поле \"Имя\" обязательно к заполнению.";
    }
    if ($json["success"] == true) {
        $bad_words = explode("|~|", $favall_config["bad_words"]);
        foreach ($bad_words as $i) {
			if(!empty($i)) {
				if (preg_match("/{$i}/i", $fav_name)) {
					$json["success"] = false;
					$json["result"]  = "Поле \"Имя\" содержит не допустимое слово.";
				}
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
        $db->query("INSERT INTO " . PREFIX . "_favall ( name_fav, type_fav, user_id_fav ) values ( '" . $fav_name . "', '1', '" . $member_id["user_id"] . "' )");
        $json["fav_id"] = $db->insert_id();
        $json["result"] = "
			<li class=\"dd-item\" data-id=\"{$json['fav_id']}\" data-favall_id=\"{$json['fav_id']}\">
				<div class=\"dd-handle\"></div>
				<div class=\"dd-content\">
					<b class='col-md-1'>ID:{$json['fav_id']}</b>
					<span class='col-md-6'>{$fav_name} (0)</span>
					<span class='col-md-4'>[../favall/{$json['fav_id']}/user/{$member_id['name']}/]</span>
					<div class=\"pull-right col-md-1\">
						<a href=\"#\">
										  <i title=\"правка\" alt=\"правка\" class=\"fa fa-pencil-square-o\"></i>
						</a>
						<a href=\"#\">
							<i title=\"удалить\" alt=\"удалить\" class=\"fa fa-trash-o text-danger\"></i>
						</a>
					</div>
				</div>
			</li>";
    }
}
if ($fav_type == "edit" || $fav_type == "edit_user") {
    $fav_id = (int) $_POST["fav_id"];
    if ($fav_id < 1) {
        $json["success"] = false;
        $json["result"] = "Ошибка ID данных.";
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT `id_fav`, `name_fav` FROM " . PREFIX . "_favall WHERE `id_fav` = '" . $fav_id . "'");
        if (!$row["id_fav"]) {
            $json["success"] = false;
            $json["result"] = "Закладки под таким ID в базе ненайдено.";
        } else {
			$_POST['fav_name'] = trim($_POST['fav_name']);
			$db->super_query("UPDATE " . PREFIX . "_favall SET name_fav = '{$_POST['fav_name']}' WHERE `id_fav` = '" . $fav_id . "'");
			$row["name_fav"] = $_POST['fav_name'];
			$json["success"] = true;
			$json["result"] = "Название закладки изменено";
		}
    }
    if ($json["success"] == true) {
        $json["fav_name"] = $row["name_fav"];
        $json["fav_id"] = $row["id_fav"];
    }
}
if ($fav_type == "update" || $fav_type == "update_user") {
    $fav_id = (int) $_POST["fav_id"];
    if ($fav_id < 1) {
        $json["success"] = false;
        $json["result"] = "Ошибка ID данных.";
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT SQL_CALC_FOUND_ROWS f.id_fav, f.name_fav, f.position_fav, count( u.id_post_list ) AS id_post_list_count, p.name FROM " . PREFIX . "_favall f LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list ) LEFT JOIN " . PREFIX . "_users p ON ( f.user_id_fav = p.user_id ) WHERE f.id_fav = '" . $fav_id . "' GROUP BY f.id_fav ORDER BY f.id_fav DESC LIMIT 1");
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
            if (preg_match("/" . $i . "/i", $fav_name)) {
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

	$_POST['fav_name'] = trim($_POST['fav_name']);
    if ($json["success"] == true) {
        $db->query("UPDATE " . PREFIX . "_favall SET `name_fav` = '" . $fav_name . "' WHERE `id_fav` = '" . $row["id_fav"] . "'");
        if (0 < $row["id_post_list_count"]) {
            $link_fl = "<a href=\"" . $config["http_home_url"] . "favall/" . $row["id_fav"] . "/user/" . $row["name"] . "/\" target=\"_blank\">.../" . $row["id_fav"] . "/user/" . $row["name"] . "/</a>";
        } else {
            $link_fl = ".../" . $row["id_fav"] . "/user/" . $row["name"] . "/";
        }
		$json["result"] = "Название закладки изменено";
		$json["fav_name"] = $_POST['fav_name'];
    }
}
if ($fav_type == "dell" || $fav_type == "dell_user") {
    $fav_id = (int) $_POST["fav_id"];
    if ($fav_id < 1) {
        $json["success"] = false;
        $json["result"] = "Ошибка ID данных.";
    }
    if ($json["success"] == true) {
        $row = $db->super_query("SELECT `id_fav` FROM " . PREFIX . "_favall WHERE `id_fav` = '" . $fav_id . "'");
		$json["success"] = true;
		$json["result"] = "Закладкa была удалена.";
        if (!$row["id_fav"]) {
            $json["success"] = false;
            $json["result"] = "Закладки под таким ID в базе ненайдено.";
        }
    }
    if ($json["success"] == true) {
        $db->query("DELETE FROM " . PREFIX . "_favall WHERE `id_fav` = '" . $fav_id . "'");
        $db->query("DELETE FROM " . PREFIX . "_favall_user WHERE `id_fav_list` = '" . $fav_id . "'");
    }
}
if ($fav_type == "update_list") {
	if (isset($_POST["update_list"])) {
		$list = json_decode($_POST["update_list"], true);
		if (!count($list)) {
			$json["success"] = false;
			$json["result"]  = "Ошибка данных списка закладок.";
		}
		if ($json["success"] == true) {
			$n = 0;
			foreach ($list as $i) {
				$n++;
				$id = (int)$i["favall_id"];
				$db->query(
					"UPDATE " . PREFIX . "_favall SET `position_fav` = '" . $n . "' WHERE `id_fav` = '" . $id . "'"
				);
			}
		}
	} else {
		$json["success"] = false;
		$json["result"]  = "Ошибка данных списка закладок.";
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
        $search_q .= " AND f.name_fav LIKE '%" . $name_s . "%' OR p.name LIKE '%" . $name_s . "%'";
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
                    $sql_result = $db->query("SELECT SQL_CALC_FOUND_ROWSf.id_fav, f.name_fav, f.position_fav, GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all, count( u.id_post_list ) AS id_post_list_count, p.name FROM " . PREFIX . "_favall f LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list ) LEFT JOIN " . PREFIX . "_users p ON ( f.user_id_fav = p.user_id ) WHERE f.type_fav = '2' " . $search_q . " GROUP BYf.id_favORDER BY " . $order_by . " " . $descasc . " LIMIT " . $count_s . ", " . $favall_config["count_admin"]);
                    $count_all = $db->super_query("SELECT FOUND_ROWS() as count");
                    $json["count"] = $count_all["count"];
                    while ($row = $db->get_row($sql_result)) {
                        if ($row["id_post_list_all"]) {
                            $id_post_list_all_array = explode(",", $row["id_post_list_all"]);
                            $id_post_list_all_count = count($id_post_list_all_array);
                        } else {
                            $id_post_list_all_count = "0";
                        }
                        if (0 < $id_post_list_all_count) {
                            $link_fl = "<a href=\"" . $config["http_home_url"] . "favall/" . $row["id_fav"] . "/user/" . $row["name"] . "/\" target=\"_blank\">.../" . $row["id_fav"] . "/user/" . $row["name"] . "/</a>";
                        } else {
                            $link_fl = ".../" . $row["id_fav"] . "/user/" . $row["name"] . "/";
                        }
                        $json["result"] .= "<li class=\"dd-item dd3-item\" data-favall_user_id=\"" . $row["id_fav"] . "\"><div class=\"dd3-content list_form\"><b>id:" . $row["id_fav"] . "</b><span class=\"list1\">" . $row["name_fav"] . " (" . $id_post_list_all_count . ")</span><span class=\"list2\">" . $link_fl . "</span><span class=\"list3\"><div data-favall_user=\"edit\"><img style=\"vertical-align: middle;border:none;\" title=\"правка\" alt=\"правка\" src=\"engine/skins/images/notepad.png\"></div><div data-favall_user=\"dell\"><img style=\"vertical-align: middle;border:none;\" alt=\"удалить\" title=\"удалить\" src=\"engine/skins/images/delete.png\"></div></span></div></li>";
                    }
            }
    }
}
echo json_encode($json, JSON_UNESCAPED_UNICODE);

?>