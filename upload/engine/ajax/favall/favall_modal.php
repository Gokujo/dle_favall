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
$json = array("success" => true, "result" => "", "title" => "");
$sicret_key = "fa2v_vk56a@K#e_all_53gh(\$.=p2";
if (isset($_SERVER["HTTP_HOST"])) {
    $host = $_SERVER["HTTP_HOST"];
} else {
    $host = getenv("HTTP_HOST");
}
define("FAVALL", true);
$license_favall = "";
require_once (DLEPlugins::Check(ENGINE_DIR . "/modules/sitelogin.php"));
if (!$is_logged && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Вы не авторизованы.";
}
$json_type = $_POST["json_type"];
if (!$json_type && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Ошибка данных типа запроса.";
}
$id_post = (int) $_POST["id_post"];
if ($id_post < 1 && $json["success"] == true) {
    $json["success"] = false;
    $json["result"] = "Ошибка ID данных поста.";
}
if ($json_type == "open" && $json["success"] == true) {
    $row = $db->super_query("SELECT `id`, `title` FROM " . PREFIX . "_post WHERE `id` = '" . $id_post . "' LIMIT 1");
    if (!$row["id"]) {
        $json["success"] = false;
        $json["result"] = "Пост под таким ID ненайден.";
    }
    if ($json["success"] == true) {
        $json["title"] = $row["title"];
        $db->query("SELECT
       f.id_fav,
       f.name_fav,
       u.id_fav_list,
       f.type_fav,
       f.position_fav,
       GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all,
       count( u.id_post_list ) AS id_post_list_count
FROM
    " . PREFIX . "_favall f
         LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list AND '1' = u.id_user_list )
WHERE
      f.type_fav = '" . $member_id["user_id"] . "' OR
      f.user_id_fav = '" . $member_id["user_id"] . "'
GROUP by
         f.id_fav,
         f.type_fav,
         f.position_fav
ORDER BY f.type_fav ASC,
         f.position_fav ASC");
        $tpl->load_template("favall/favall_list_modal.tpl");
        while ($row = $db->get_row()) {
            $tpl->set("{id_fav}", $row["id_fav"]);
            $tpl->set("{name}", $row["name_fav"]);
            $tpl->set("{url}", $config["http_home_url"] . "favall/" . $row["id_fav"] . "/user/" . $member_id["name"] . "/");
            $fav_status = false;
            $id_post_list_all = explode(",", $row["id_post_list_all"]);
            foreach ($id_post_list_all as $x) {
                if ($x == $id_post) {
                    $fav_status = true;
                }
            }
            if ($fav_status == true) {
                $fav_active = " active";
                $tpl->set_block("'\\[fav_add\\](.*?)\\[/fav_add\\]'si", "");
                $tpl->set("[fav_dell]", "");
                $tpl->set("[/fav_dell]", "");
            } else {
                $fav_active = "";
                $tpl->set("[fav_add]", "");
                $tpl->set("[/fav_add]", "");
                $tpl->set_block("'\\[fav_dell\\](.*?)\\[/fav_dell\\]'si", "");
            }
            $tpl->set("{sum}", $row["id_post_list_count"]);
            if (0 < $row["id_post_list_count"]) {
                $tpl->set("[url]", "");
                $tpl->set("[/url]", "");
            } else {
                $tpl->set_block("'\\[url\\](.*?)\\[/url\\]'si", "");
            }
            $tpl->copy_template = "<div class=\"favall_p" . $fav_active . "\" data-favall_id=\"" . $row["id_fav"] . "\">" . $tpl->copy_template . "</div>";
            $tpl->compile("favall_list_modal");
        }
        if ($tpl->result["favall_list_modal"]) {
            $json["result"] = "<div class=\"favall_all_p\" data-favall_id_post=\"" . $id_post . "\">" . $tpl->result["favall_list_modal"] . "</div>";
        } else {
            $json["result"] = "<div class=\"favall_all_p\" style=\"text-align:center;\">Администрация не создавала закладки.</div>";
        }
    }
} else {
    if ($json_type == "add_dell" && $json["success"] == true) {
        $row = $db->super_query("SELECT `id`, `title` FROM " . PREFIX . "_post WHERE `id` = '" . $id_post . "' LIMIT 1");
        if (!$row["id"]) {
            $json["success"] = false;
            $json["result"] = "Пост под таким ID ненайден.";
        }
        if ($json["success"] == true) {
            $id_favall = (int) $_POST["id_favall"];
            if ($id_favall < 1 && $json["success"] == true) {
                $json["success"] = false;
                $json["result"] = "Ошибка ID данных закладки.";
            }
        }
        if ($json["success"] == true) {
            $row = $db->super_query("SELECT f.id_fav, f.name_fav, u.id_fav_list, GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all, count( u.id_post_list ) AS id_post_list_count FROM " . PREFIX . "_favall f LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list AND '" . $member_id["user_id"] . "' = u.id_user_list ) WHERE f.id_fav = '" . $id_favall . "' AND ( f.type_fav = '1' OR f.user_id_fav = '" . $member_id["user_id"] . "') GROUP BY f.id_fav LIMIT 1");
            if (!$row["id_fav"]) {
                $json["success"] = false;
                $json["result"] = "Закладка под таким ID ненайден.";
            }
            if ($json["success"] == true) {
                $fav_status = true;
                if ($row["id_fav_list"]) {
                    $id_post_list_all = explode(",", $row["id_post_list_all"]);
                    foreach ($id_post_list_all as $x) {
                        if ($id_post == $x) {
                            $fav_status = false;
                        }
                    }
                }
                if ($fav_status) {
                    $db->query("INSERT INTO " . PREFIX . "_favall_user ( id_fav_list, id_user_list, id_post_list ) values ( '" . $id_favall . "', '" . $member_id["user_id"] . "', '" . $id_post . "' )");
                    $row["id_post_list_count"] = $row["id_post_list_count"] + 1;
                } else {
                    $db->query("DELETE FROM " . PREFIX . "_favall_user WHERE `id_fav_list` = '" . $id_favall . "' AND `id_user_list` = '" . $member_id["user_id"] . "' AND `id_post_list` = '" . $id_post . "'");
                    $row["id_post_list_count"] = $row["id_post_list_count"] - 1;
                    if ($row["id_post_list_all"] < 1) {
                        $row["id_post_list_all"] = 0;
                    }
                }
                $tpl->load_template("favall/favall_list_modal.tpl");
                $tpl->set("{id_fav}", $row["id_fav"]);
                $tpl->set("{name}", $row["name_fav"]);
                $tpl->set("{url}", $config["http_home_url"] . "favall/" . $row["id_fav"] . "/user/" . $member_id["name"] . "/");
                if ($fav_status) {
                    $fav_active = " active";
                    $tpl->set_block("'\\[fav_add\\](.*?)\\[/fav_add\\]'si", "");
                    $tpl->set("[fav_dell]", "");
                    $tpl->set("[/fav_dell]", "");
                } else {
                    $fav_active = "";
                    $tpl->set("[fav_add]", "");
                    $tpl->set("[/fav_add]", "");
                    $tpl->set_block("'\\[fav_dell\\](.*?)\\[/fav_dell\\]'si", "");
                }
                $tpl->set("{sum}", $row["id_post_list_count"]);
                if (0 < $row["id_post_list_count"]) {
                    $tpl->set("[url]", "");
                    $tpl->set("[/url]", "");
                } else {
                    $tpl->set_block("'\\[url\\](.*?)\\[/url\\]'si", "");
                }
                $tpl->copy_template = "<div class=\"favall_p" . $fav_active . "\" data-favall_id=\"" . $row["id_fav"] . "\">" . $tpl->copy_template . "</div>";
                $tpl->compile("favall_list_modal");
                $json["result"] = $tpl->result["favall_list_modal"];
            }
        }
    }
}
echo json_encode($json);

?>