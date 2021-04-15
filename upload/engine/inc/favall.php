<?PHP
/*
=====================================================
 DataLife Engine - by D0Gmatist
-----------------------------------------------------
 http://d0gmatist.pro/
-----------------------------------------------------
 Copyright (c) 2015
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: favall.php
-----------------------------------------------------
 Назначение: управление закладками
=====================================================
*/

if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

$version = [
	'name' => 'FavALL',
	'descr' => 'Пользовательские закладки',
	'version' => '4.5.0.2',
	'changelog' => [
		'4.5.0.2' => [
			'NULLED версия и адаптированная под 14.1 (by Skripters.biz)'
		]
	],
	'id' => 'favall',
];

include (DLEPlugins::Check(ENGINE_DIR . '/data/favall_config.php'));

$js_array[] = "engine/ajax/favall/favall.js";

$fav_list_none = 'Список закладок пуст.';

/**
 * @param string $title
 * @param string $description
 * @param string $field
 * @param string $class
 */
function showRow($title = "", $description = "", $field = "", $class = "") {

	echo "<tr>
        <td class=\"col-xs-6 col-sm-6 col-md-5\"><h6 class=\"media-heading text-semibold\">{$title}</h6><span class=\"text-muted text-size-small hidden-xs\">{$description}</span></td>
        <td class=\"col-xs-6 col-sm-6 col-md-7\">{$field}</td>
        </tr>";
}

/**
 * @param $options
 * @param $name
 * @param $selected
 *
 * @return string
 */
function makeDropDown($options, $name, $selected) {
	$output = "<select class=\"uniform\" name=\"$name\">\r\n";
	foreach ( $options as $value => $description ) {
		$output .= "<option value=\"$value\"";
		if( $selected == $value ) {
			$output .= " selected ";
		}
		$output .= ">$description</option>\n";
	}
	$output .= "</select>";
	return $output;
}

/**
 * @param $name
 * @param $selected
 *
 * @return string
 */
function makeCheckBox($name, $selected) {

	$selected = $selected ? "checked" : "";

	return "<input class=\"switch\" type=\"checkbox\" name=\"{$name}\" value=\"1\" {$selected}>";

}

echo <<<CSS
<style type="text/css" media="screen">
.favall_block_menu{background:#ffffff;box-shadow:0 0 6px rgba(0,0,0,0.35);margin:5px;padding:5px;}
.favall_block_menu .navigation_f{}
.favall_block_menu a{display:inline-block;padding:5px 10px;margin:5px;font-size:12px;line-height:18px;color:#333333;text-align:center;text-shadow:0 1px 1px rgba(255,255,255,0.75);background:#FFFFFF;border-color:#e6e6e6 #e6e6e6 #bfbfbf;border-color:rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);filter:progid:dximagetransform.microsoft.gradient(enabled=false);border:1px solid #cccccc;border-bottom-color:#b3b3b3;-webkit-border-radius:4px;-moz-border-radius:4px;border-radius:4px;cursor:pointer;width:160px;}
.favall_block_menu a.action{border:1px solid #A91E1E;-webkit-box-shadow:0 4px 8px -5px rgba(0,0,0,0.5);-moz-box-shadow:0 4px 8px -5px rgba(0,0,0,0.5);box-shadow:0 4px 8px -5px rgba(0,0,0,0.5);color:#A91E1E;}
.favall_block_menu a:hover{background:#ffffff;-webkit-box-shadow:0 4px 8px -5px rgba(0,0,0,0.5);-moz-box-shadow:0 4px 8px -5px rgba(0,0,0,0.5);box-shadow:0 4px 8px -5px rgba(0,0,0,0.5);}
.favall_block_menu a img{margin:0 auto;display:block;}
.favall_block_menu a span{display:block;}
.form-group-last{display:block;}
.col-lg-last{display:inline-block;font-size:14px;padding:10px 0;text-align:center;width:300px;}
.col-lg-last input, .col-lg-last select{font-size:15px;padding:2px 4px;}
.favallgMenu{padding:5px 20px;}
.favallgMenu .col-sm-1.action-nav-button{margin-left:5px;margin-right:5px;min-width:150px;padding:0;}
.favallgMenu .btn.btn-default.changeOption.tip.action{background:#f3f3f3;border-color:#cfcfcf;-webkit-box-shadow:inset 0 0 5px #f3f3f3;-moz-box-shadow:inset 0 0 5px #f3f3f3;box-shadow:inset 0 0 5px #f3f3f3;color:#4C9ECD;}
.favallgMenu .btn.btn-default.changeOption.tip.action i{color:#4C9ECD;}
#favall_block{background:#ffffff;box-shadow:0 0 6px rgba(0,0,0,0.35);margin:5px;padding:5px;}
.list_form{font-size:12px;font-weight:400;position:relative;}
.list_form b{float:left;width:40px;}
.list1{float:left;margin:0 0 0 10px;overflow:hidden;width:250px;}
.list2{left:345px;overflow:hidden;position:absolute;right:55px;}
.list2 a{color:#E70505;}
.list2 a:hover{color:#E70505;text-decoration:underline;}
.list3{float:right;text-align:right;height:20px;width:40px;}
.list3 div{cursor:pointer;display:inline-block;}
.dd-handle{top:3px !important;}
.dd-content{margin: 3px 0 !important;}
</style>
CSS;

$action = $_REQUEST['action'];
################################# Для активного меню
if ( ! $action ) {
	$h_action = ' action';

	echoheader( "<i class=\"fa fa-bookmark position-left\"></i><span class=\"text-semibold\">{$version['name']} (v{$version['version']})</span>", 'Управление закладками по умолчанию' );

} else if ( $action == 'all_favall' ) {
	$a_action = ' action';
	echoheader( "<i class=\"fa fa-bookmark position-left\"></i><span class=\"text-semibold\">{$version['name']} (v{$version['version']})</span>", 'Управление закладками юзеров' );

} else if ( $action == 'config' ) {
	$c_action = ' action';
	echoheader( "<i class=\"fa fa-bookmark position-left\"></i><span class=\"text-semibold\">{$version['name']} (v{$version['version']})</span>", 'Управление настройками модуля' );

}

################################# Меню

function get_active($var) {
	global $action;
	if (!isset($action) && empty($var)) return 'active';
	if ($action == $var) return 'active';
	return '';
}

echo <<<HTML

<div class="navbar navbar-default navbar-component navbar-xs systemsettings">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="fa fa-bars"></i></a></li>
	</ul>
	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav">
			<li class="
HTML;
echo get_active('');
echo <<<HTML

"><a href="?mod=favall" class="tip" title="Закладки по умолчанию"><i class="fa fa-bookmark"></i><span>Закладки по умолчанию</span></a></li>
			<li class="
HTML;
echo get_active('all_favall');
echo <<<HTML

"><a href="?mod=favall&action=all_favall" class="tip" title="Закладки юзеров"><i class="icon-file-alt"></i><span>Закладки юзеров</span></a></li>
			<li class="
HTML;
echo get_active('config');
echo <<<HTML
"><a href="?mod=favall&action=config" class="tip" title="Настройки модуля"><i class="icon-file-alt"></i><span>Настройки модуля</span></a></li>
			
		</ul>
	</div>
</div>
HTML;

################################# Управление закладками по умолчанию
if ( ! $action ) {

echo <<<HTML
<div class="panel panel-flat">
  <div class="panel-body">
    Добавление новой закладки
  </div>
  <div class="table-responsive">
  <table class="table table-striped">
HTML;
	showRow('Имя', 'Имя вашей новой закладки. Данное поле обязательно к заполнению.', '<input class="form-control" type="text" name="fav_name" data-favall="fav_name">' );
echo <<<HTML
</table>
</div>

</div>
	<div style="margin-bottom:30px;">
		<button type="button" class="btn bg-teal btn-raised position-left legitRipple action-button" data-favall="add"><i class="fa fa-floppy-o position-left"></i>Сохранить</button>
	</div>
HTML;

	$fav_list = '';

	$sql_result = $db->query( "SELECT f.id_fav,
										    f.name_fav,
										    GROUP_CONCAT(u.id_post_list SEPARATOR ',') AS id_post_list_all
									 FROM " . PREFIX . "_favall f
									 LEFT JOIN " . PREFIX . "_favall_user u ON ( f.id_fav = u.id_fav_list )
									 WHERE
										 f.type_fav = '1'
									 GROUP BY
										 f.id_fav
									 ORDER BY
										 f.position_fav ASC"
							);
	$fav_list_count = $db->num_rows();

	while ( $row = $db->get_row( $sql_result ) ) {
		if ( $row['id_post_list_all'] ) {
			$id_post_list_all_array = explode( ',', $row['id_post_list_all'] );
			$id_post_list_all_count = count( $id_post_list_all_array );

		} else {
			$id_post_list_all_count = '0';

		}

		if ( $id_post_list_all_count > 0 ) {
			$link_fl = '<a href="' . $config['http_home_url'] . 'favall/' . $row['id_fav'] . '/user/' . $member_id['name'] . '/" target="_blank">.../favall/' . $row['id_fav'] . '/user/' . $member_id['name'] . '/</a>';

		} else {
			$link_fl = '.../favall/' . $row['id_fav'] . '/user/' . $member_id['name'] . '/';

		}

		$fav_list .= <<<HTML
			<li class="dd-item row" data-id="{$row['id_fav']}" data-favall_id="{$row['id_fav']}">
				<div class="dd-handle"></div>
				<div class="dd-content">
					<b class="col-md-1">ID:{$row['id_fav']}</b>
					<div class="col-md-6">
						<span class="fav_name">
							{$row['name_fav']}
						</span> 
						({$id_post_list_all_count})
					</div>
					<span class="col-md-4">[{$link_fl}]</span>
					<div class="pull-right col-md-1">
						<a data-favall="edit_global">
							<i title="правка" alt="правка" class="fa fa-pencil-square-o"></i>
						</a>
						<a data-favall="delete_global">
							<i title="удалить" alt="удалить" class="fa fa-trash-o text-danger"></i>
						</a>
					</div>
				</div>
			</li>
HTML;

	}

	if ( $fav_list ) {
		$fav_list_s = ' style="display:none;"';

	}

	if ( $fav_list_count == 0 ) $fav_list = <<<HTML
			<li class="dd-item" data-id="0" data-favall_id="0">
				{$fav_list_none}
			</li>
HTML;


echo <<<HTML
<div class="panel panel-flat">
    <div class="panel-heading">
        Список закладок по умолчанию
    </div>
	<div class="panel-body">
		<div class="dd" id="nestable">
			<ol class="dd-list" id="default_favs">
				{$fav_list} 
			</ol>
		</div>
	</div>
</div>
HTML;


} else if ( $action == 'all_favall' ) {

	$fav_list = '';

	$sql_result = $db->query( "SELECT SQL_CALC_FOUND_ROWS f.id_fav,
															   f.name_fav,
															   f.position_fav,
															   GROUP_CONCAT(u.id_post_list SEPARATOR ',') AS id_post_list_all,
															   count(u.id_post_list)                      AS id_post_list_count,
															   p.name
									FROM " . PREFIX . "_favall f
									LEFT JOIN " . PREFIX . "_favall_user u  ON ( f.id_fav = u.id_fav_list )
									LEFT JOIN " . PREFIX . "_users p        ON ( f.user_id_fav = p.user_id )
									WHERE
										f.type_fav = '2'
									GROUP BY
										f.id_fav
									ORDER BY
										f.id_fav DESC
									LIMIT 0, {$favall_config['count_admin']}" );

	$count_all = $db->num_rows();

	while ( $row = $db->get_row( $sql_result ) ) {
		if ( $row['id_post_list_count'] > 0 ) {
			$link_fl = '<a href="' . $config['http_home_url'] . 'favall/' . $row['id_fav'] . '/user/' . $row['name'] . '/" target="_blank">.../' . $row['id_fav'] . '/user/' . $row['name'] . '/</a>';

		} else {
			$link_fl = '.../' . $row['id_fav'] . '/user/' . $row['name'] . '/';

		}

		$fav_list .= <<<HTML
			<li class="dd-item row" data-id="{$row['id_fav']}" data-favall_id="{$row['id_fav']}">
				<div class="dd-handle"></div>
				<div class="dd-content">
					<b class="col-md-1">ID:{$row['id_fav']}</b>
					<span class="col-md-6">{$row['name_fav']} ({$row['id_post_list_count']})</span>
					<span class="col-md-4">[{$link_fl}]</span>
					<div class="pull-right col-md-1">
						<a data-favall="edit_user">
							<i title="правка" alt="правка" class="fa fa-pencil-square-o"></i>
						</a>
						<a data-favall="delete_user">
							<i title="удалить" alt="удалить" class="fa fa-trash-o text-danger"></i>
						</a>
					</div>
				</div>
			</li>
HTML;

	}

	if ( $fav_list ) {
		$fav_list_s = ' style="display:none;"';
	}

	if ( $count_all ) {
		$count_all_s = ' style="display:none;"';
	}

	if ($count_all == 0) $fav_list = <<<HTML
			<li data-favall_user_id="0">
				{$fav_list_none}
			</li>
HTML;

echo <<<HTML
<div class="panel panel-flat">
    <div class="panel-heading">
        Список закладок юзеров
    </div>
	<div class="panel-body">
		<div class="row">
			<div class=" col-md-4">
				Название закладки или логин юзера<br />
				<input style="" class="form-control" type="text" name="favall_name" data-favall_search="favall_name">
			</div>
			<div class=" col-md-4">
				Сортировать по<br />
				<select class="uniform" style="" name="favall_sort" data-favall_search="favall_sort" data-select="favall">
					<option value="1">По ID</option>
					<option value="2">По колличеству</option>
					<option value="3">По имени закладки</option>
					<option value="4">По имени юзера</option>
				</select>
			</div>
			<div class="col-md-4">
				Направление сортировки<br />
				<select class="uniform" style="" name="favall_desc_asc" data-favall_search="favall_desc_asc" data-select="favall">
					<option value="1">По возрастанию</option>
					<option value="2">По убыванию</option>
				</select>
			</div>
		</div>
		<div class="dd row" id="nestable_user">
			<ol class="dd-list">
				{$fav_list} 
			</ol>
		</div>
	</div>
</div>


<div style="margin-bottom:30px;">
	<button type="button" class="btn bg-teal btn-raised position-left legitRipple" data-favall="user_load_more"><i class="fa fa-refresh position-left"></i>Показать ещё</button>
</div>
HTML;



} else if ( $action == 'config' ) {
	if ( $_POST) {
		$favall_con = $_POST['favall_con'];
		if ( $favall_config ) {
			$favall_con = $favall_con + $favall_config;

		}

		$handler = fopen( (DLEPlugins::Check(ENGINE_DIR . '/data/favall_config.php')), "w" );
		fwrite( $handler, "<?PHP \n\n// FavALL Configurations\n\n\$favall_config = array (\n\n" );
		foreach ( $favall_con as $name => $value ) {
			if ( $name == 'count_favall_user' ) {
				$value = (int)$value;

			} else if ( $name == 'count_web' OR $name == 'count_admin' ) {
				$value = (int)$value;
				if ( $value < 10 ) {
					$value = 10;

				} else if ( $value > 200 ) {
					$value = 200;

				}

			} else if ( $name == 'bad_words' ) {
				$value = str_replace( "\n", "|~|", str_replace( "\r", "", $value ) );

			}

			fwrite( $handler, "\t'{$name}' => '{$value}',\n" );

		}
		fwrite( $handler, ");\n\n?>" );
		fclose( $handler );

		clear_cache( array( 'mc_' ) );

		include (DLEPlugins::Check(ENGINE_DIR . '/data/favall_config.php'));

	}

	$favall_config['bad_words'] = str_replace( "|~|", "\n", $favall_config['bad_words'] );

echo <<<HTML
<form method="POST" action="?mod=favall&action=config">
	<div class="panel panel-flat">
		<div class="panel-body">
			Настройки
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
HTML;
	showRow('Максимально количество закладок у юзера:', 'Что бы запретить создание закладок, введите 0', "<input class=\"form-control\" type=\"text\" name=\"favall_con[count_favall_user]\" value=\"{$favall_config['count_favall_user']}\">" );
	showRow('Количество отображаемых закладок юзеров, при просмотре на сайте:', 'Минимально 10, максимально 200', "<input class=\"form-control\" type=\"text\" name=\"favall_con[count_web]\" value=\"{$favall_config['count_web']}\">" );
	showRow('Запретные слова в названии закладок юзеров:', 'Каждое слово с новой строки', "<textarea class=\"form-control\" style=\"width:100%;\" name=\"favall_con[bad_words]\" rows=\"6\">{$favall_config['bad_words']}</textarea>" );
	showRow('Количество отображаемых закладок юзеров, при просмотре в админ панеле:', 'Минимально 10, максимально 200', "<input class=\"form-control\" type=\"text\" name=\"favall_con[count_admin]\" value=\"{$favall_config['count_admin']}\">" );
	echo <<<HTML
			</table>
		</div>
	</div>
	<div style="margin-bottom:30px;">
		<input type="hidden" name="config_save" value="config_save">
		<button type="submit" class="btn bg-teal btn-raised position-left legitRipple" data-favall="config_save"><i class="fa fa-floppy-o position-left"></i>Сохранить</button>
	</div>
</form>


HTML;


}


	echo <<<HTML

<script>
	$(document).on('click', '[data-favall="add"]', function () {
	    let val = $( '[data-favall="fav_name"]' ).val();
		ShowLoading('');
		if ( !val ) {
		    HideLoading('');
			DLEalert( 'Поле "Имя" обязательно к заполнению.', 'Информация' );
		} else {
			$.ajax({
				url: 'engine/ajax/controller.php?mod=favall',
				data: {
					user_hash: '{$dle_login_hash}',
					file: 'favall_inc',
					fav_type: 'add',
					fav_name: val
				},
				type: 'POST',
				success: function (data) {
					HideLoading('');
					try {
						data = JSON.parse(data);
					} catch {
						
					}
					if (data.success) {
						$('#default_favs').append(data.result);
					} else {
						DLEalert(data.result, 'Ошибка');
					}
				}
			});
		}
	});
	
	$(document).on('click', '[data-favall="edit_global"]', function () {
	    let parent = $(this).parents('.dd-content'),
	    	grand_parent = $(parent).parents('.dd-item'),
	    	fav_id = $(grand_parent).data('favall_id'),
	    	fav_name = $(parent).find('span.fav_name').first().text()
	    	;
	    DLEprompt('Укажите новое значение', fav_name, 'Закладка: ' + fav_name, function(v) {
	       
			ShowLoading('');
			$.ajax({
				url: 'engine/ajax/controller.php?mod=favall',
				data: {
					user_hash: '{$dle_login_hash}',
					file: 'favall_inc',
					fav_type: 'update',
					fav_id: fav_id,
					fav_name: v
				},
				type: 'POST',
				success: function (data) {
					HideLoading('');
					try {
						data = JSON.parse(data);
					} catch {
						
					}
					if (data.success) {
					    DLEalert(data.result, 'Успех!');
						$(parent).find('span.fav_name').first().text(data.fav_name);
					} else {
						DLEalert(data.result, 'Ошибка');
					}
				}
			});
	    });
	});
	
	$(document).on('click', '[data-favall="delete_global"]', function () {
	    let parent = $(this).parents('.dd-content'),
	    	grand_parent = $(parent).parents('.dd-item'),
	    	fav_id = $(grand_parent).data('favall_id'),
	    	fav_name = $(parent).find('span.fav_name').first().text()
	    	;
	    DLEconfirm('Вы точно хотите удалить "' + fav_name + '"?', 'Закладка: ' + fav_name, function() {
	       
			ShowLoading('');
			$.ajax({
				url: 'engine/ajax/controller.php?mod=favall',
				data: {
					user_hash: '{$dle_login_hash}',
					file: 'favall_inc',
					fav_type: 'dell',
					fav_id: fav_id
				},
				type: 'POST',
				success: function (data) {
					HideLoading('');
					try {
						data = JSON.parse(data);
					} catch {
						
					}
					if (data.success) {
					    DLEalert(data.result, 'Успех!');
						$(grand_parent).remove();
					} else {
						DLEalert(data.result, 'Ошибка');
					}
				}
			});
	    });
	});
	
	$(document).on('click', '[data-favall="edit_user"]', function () {
	    let parent = $(this).parents('.dd-content'),
	    	grand_parent = $(parent).parents('.dd-item'),
	    	fav_id = $(grand_parent).data('favall_id'),
	    	fav_name = $(parent).find('span.fav_name').first().text()
	    	;
	    DLEprompt('Укажите новое значение', fav_name, 'Закладка: ' + fav_name, function(v) {
	       
			ShowLoading('');
			$.ajax({
				url: 'engine/ajax/controller.php?mod=favall',
				data: {
					user_hash: '{$dle_login_hash}',
					file: 'favall_inc',
					fav_type: 'update_user',
					fav_id: fav_id,
					fav_name: v
				},
				type: 'POST',
				success: function (data) {
					HideLoading('');
					try {
						data = JSON.parse(data);
					} catch {
						
					}
					if (data.success) {
					    DLEalert(data.result, 'Успех!');
						$(parent).find('span.fav_name').first().text(data.fav_name);
					} else {
						DLEalert(data.result, 'Ошибка');
					}
				}
			});
	    });
	});
	
	$(document).on('click', '[data-favall="delete_user"]', function () {
	    let parent = $(this).parents('.dd-content'),
	    	grand_parent = $(parent).parents('.dd-item'),
	    	fav_id = $(grand_parent).data('favall_id'),
	    	fav_name = $(parent).find('span.fav_name').first().text()
	    	;
	    DLEconfirm('Вы точно хотите удалить "' + fav_name + '"?', 'Закладка: ' + fav_name, function() {
	       
			ShowLoading('');
			$.ajax({
				url: 'engine/ajax/controller.php?mod=favall',
				data: {
					user_hash: '{$dle_login_hash}',
					file: 'favall_inc',
					fav_type: 'dell_user',
					fav_id: fav_id
				},
				type: 'POST',
				success: function (data) {
					HideLoading('');
					try {
						data = JSON.parse(data);
					} catch {
						
					}
					if (data.success) {
					    DLEalert(data.result, 'Успех!');
						$(grand_parent).remove();
					} else {
						DLEalert(data.result, 'Ошибка');
					}
				}
			});
	    });
	});
	
	jQuery(function($){
		$('.dd').nestable({
			maxDepth: 500
		});

		$('.dd').nestable('collapseAll');
		
		$('.dd-handle a').on('mousedown', function(e){
			e.stopPropagation();
		});

		$('.dd-handle a').on('touchstart', function(e){
			e.stopPropagation();
		});

		$('.nestable-action').on('click', function(e)
		{
			var target = $(e.target),
				action = target.data('action');
			if (action === 'expand-all') {
				$('.dd').nestable('expandAll');
			}
			if (action === 'collapse-all') {
				$('.dd').nestable('collapseAll');
			}
		});
		
		$('#nestable').nestable().on('change',function(){
			ShowLoading('');
			$.ajax({
				url: 'engine/ajax/controller.php?mod=favall',
				data: {
					user_hash: '{$dle_login_hash}',
					file: 'favall_inc',
					fav_type: 'update_list',
					update_list: JSON.stringify($('.dd').nestable('serialize'))
				},
				type: 'POST',
				success: function (data) {
				    HideLoading('');
					try {
						data = JSON.parse(data);
					} catch {
						
					}
					if (data.success === false) {
						DLEalert(data.result, 'Ошибка');
					}
				}
			});

		});


	});


</script>

<div class="footer text-muted text-size-small" style="bottom: 50px;">
	Автор оригинала: D0Gmatist / Site: <a href="http://d0gmatist.pro/" target="_blank">http://d0gmatist.pro/</a> /Skype: D0Gmatist / Email: 375256232834@yandex.by<br>
	Правки под 14.1: MaHarder (<a href="https://devcraft.club" target="_blank">DevCraftClub</a>) & 
	SaNcHeS (специально для <a href="https://skripters.biz" target="_blank">https://skripters.biz</a>, 2021
</div>
HTML;



echofooter();

?>