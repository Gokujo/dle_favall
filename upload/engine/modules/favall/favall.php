<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2013 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: favall.php
-----------------------------------------------------
 Назначение: вывод закладок
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

include (DLEPlugins::Check(ENGINE_DIR . '/data/favall_config.php'));

function declination_fav ( $d ) {
	$num = $d[1] % 100;

	if ( $num > 19 ) {
		$num = $num % 10;

	}
	$text = explode( '|', $d[2] );

	switch ( $num ) {
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

if ( $_GET['manager'] != 'manager' AND $_GET['catalog'] != 'catalog' ) {
	$favall_status = TRUE;
	$favall_info = 'По данному адресу закладок на сайте не найдено, либо у вас нет доступа для просмотра информации по данному адресу.';

	if( ! isset( $cstart ) ) {
		$cstart = 0;

	}

	if( $cstart ) {
		$cstart = $cstart - 1;
		$cstart = $cstart * $config['news_number'];
		$start_from = $cstart;

	}
	$cstart = (int)$cstart;

	$list = (int)$_GET['list']; 

	if ( isset ( $_GET['user'] ) ) {

		$user = @strip_tags ( str_replace ( '/', '', urldecode ( $_GET['user'] ) ) );

//		if ( $config['charset'] == "windows-1251" AND $config['charset'] != detect_encoding( $user ) ) {
//			$user = iconv( "UTF-8", "windows-1251//IGNORE", $user );
//		}

		$user = $db->safesql( $user ); 

		if( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $user ) ) {
			$user = '';

		}

	} else {
		$user = '';

	}

	if ( $list AND $user ) {
		$row = $db->super_query( "SELECT 
										f.id_fav, 
										f.name_fav, 
										GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all, 
										p.user_id 
											FROM 
												" . USERPREFIX . "_users p 
													LEFT JOIN 
												" . PREFIX . "_favall_user u 
													ON ( p.user_id = u.id_user_list ) 
													LEFT JOIN 
												" . PREFIX . "_favall f 
													ON ( u.id_fav_list = f.id_fav ) 
														WHERE 
															p.name = '{$user}' 
																AND 
															f.id_fav = '{$list}' 
																GROUP BY
																	f.id_fav 
																		ORDER BY 
																			u.id_fav_list
																				DESC
																					LIMIT 1" );

		if ( ! $row['user_id'] ) {
			$favall_status = FALSE;
			$favall_info = 'Пользователь с таким именем не найден.';

		}

		if ( $row['id_fav'] AND $favall_status == TRUE ) {
			$name_fav = 'Пользователь ' . $user . ' ' . $config['speedbar_separator'] . ' Закладка ' . $row['name_fav'];

			$url_page = $config['http_home_url'] . "favall/" . $row['id_fav'] . "/user/" . $user;
			$user_query = "do=favall&list=" . $row['id_fav'] . "&user=" . $user;

		} else {
			$favall_status = FALSE;
			$favall_info = 'По данному адресу закладок на сайте не найдено, либо у вас нет доступа для просмотра информации по данному адресу.';

		}

		$id_post_list_all = explode( ',', $row['id_post_list_all'] );
		$id_post_array = array();
		foreach ( $id_post_list_all AS $i ) {
			$id_post_array[] = (int)$i;

		}
		$id_post_list_all = implode( ',', array_unique( $id_post_array ) );

	}

	if ( $id_post_list_all AND $favall_status == TRUE ) {
		$sql_select = "SELECT 
							p.id, 
							p.autor, 
							p.date, 
							p.short_story, 
							CHAR_LENGTH(p.full_story) as full_story, 
							p.xfields, 
							p.title, 
							p.category, 
							p.alt_name, 
							p.comm_num, 
							p.allow_comm, 
							p.fixed, 
							p.tags, 
							e.news_read, 
							e.allow_rate, 
							e.rating, 
							e.vote_num, 
							e.votes, 
							e.view_edit, 
							e.editdate, 
							e.editor, 
							e.reason 
								FROM 
									" . PREFIX . "_post p 
										LEFT JOIN 
									" . PREFIX . "_post_extras e 
										ON ( p.id = e.news_id ) 
											WHERE 
												id in ({$id_post_list_all}) 
													AND 
												approve = 1 
													ORDER BY 
														FIELD( id, {$id_post_list_all} )
															LIMIT {$cstart}, {$config['news_number']}";

		$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE id in ({$id_post_list_all}) AND approve = 1";

		$allow_active_news = TRUE;
		$tpl_favall = TRUE;
		include (DLEPlugins::Check( ENGINE_DIR . '/modules/show.short.php' ));

	}

	if ( ! $tpl->result['content'] ) {
		$tpl->load_template( 'info.tpl' );
		$tpl->set( '{error}', $favall_info );
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();

	}

} else if ( $_GET['manager'] == 'manager' AND $_GET['catalog'] != 'catalog' AND $member_id['user_group'] != 5 ) {
	$name_fav = 'Управлени персональными закладками.';

	$db->query ( "SELECT 
						f.id_fav, 
						f.name_fav, 
						f.type_fav, 
						count( u.id_post_list ) AS id_post_list_count 
							FROM 
								" . PREFIX . "_favall f 
									LEFT JOIN 
								" . PREFIX . "_favall_user u 
									ON ( f.id_fav = u.id_fav_list AND '{$member_id['user_id']}' =  u.id_user_list ) 
										WHERE 
											f.type_fav = '1' 
												OR 
											f.user_id_fav = '{$member_id['user_id']}' 
												GROUP BY
													f.id_fav
														ORDER BY 
															f.type_fav 
																ASC, 
															f.position_fav 
																ASC" );

	$def_favall = '';
	$user_favall = '';
	while ( $row = $db->get_row () ) {
		if ( $row['id_post_list_count'] > 0 ) {
				$link_fl = '<a href="' . $config['http_home_url'] . 'favall/' . $row['id_fav'] . '/user/' . $member_id['name'] . '/" target="_blank">.../favall/' . $row['id_fav'] . '/user/' . $member_id['name'] . '/</a>';

		} else {
				$link_fl = '.../favall/' . $row['id_fav'] . '/user/' . $member_id['name'] . '/';

		}

		if ( $row['type_fav'] == '1' ) {
			$def_favall .= <<<HTML
							<div class="def_list_form">
								<span class="def_list1">{$row['name_fav']} ({$row['id_post_list_count']})</span>
								<span class="def_list2">{$link_fl}</span>
							</div>
HTML;

		} else {
			$user_favall .= <<<HTML
						<li class="dd-item dd3-item" data-favall_id="{$row['id_fav']}">
							<div class="dd-handle dd3-handle">.</div>
							<div class="dd3-content list_form">
								<span class="list1">{$row['name_fav']} ({$row['id_post_list_count']})</span>
								<span class="list2"> 
									<span>{$link_fl}</span>										
								</span>
								<span class="list3">
									<div data-favall="edit" class="favall_edit"></div>
									<div data-favall="dell" class="favall_dell"></div>
								</span>
							</div>
						</li>
HTML;

		}

	}
	if ( $def_favall ) {
		$no_def_favall = 'style="display:none;"';

	}

	$def_favall = <<<HTML
				{$def_favall}
				<div class="no_def_favall" {$no_def_favall}>Администрация не создавала закладки по умолчанию.</div>
HTML;


	if ( $user_favall ) {
		$no_user_favall = 'style="display:none;"';

	}

	$user_favall = <<<HTML
				<div class="dd" id="nestable" data-favall="nestable">
					<ol class="dd-list" data-favall="list">
						{$user_favall}
					</ol>
				</div>
				<div data-favall_all_form_block="2" class="no_user_favall" {$no_user_favall}>Список закладок пуст.</div>
HTML;


	$tpl->load_template( 'favall/favall_manager.tpl' );

	if ( $favall_config['count_favall_user'] > 0 ) {
		$tpl->set( '[add_favall]', '' );
		$tpl->set( '[/add_favall]', '' );

	} else {
		$tpl->set_block( "'\\[add_favall\\](.*?)\\[/add_favall\\]'si", "" );

	}
	$tpl->set( '{def_favall}', $def_favall );
	$tpl->set( '{user_favall}', $user_favall );

	$tpl->compile( 'content' );
	$tpl->clear();

} else if ( $_GET['manager'] != 'manager' AND $_GET['catalog'] == 'catalog' ) {
	$name_fav = 'Каталог закладок.';
// u.id_fav_list != 'NULL'
	$sql_result = $db->query( "SELECT 
									SQL_CALC_FOUND_ROWS 
									f.id_fav, 
									f.name_fav, 
									f.position_fav, 
									GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all, 
									count( u.id_post_list ) AS id_post_list_count, 
									p.name, 
									p.foto 
										FROM 
											" . PREFIX . "_favall f 
												LEFT JOIN 
											" . PREFIX . "_favall_user u 
												ON ( f.id_fav = u.id_fav_list ) 
												LEFT JOIN 
											" . PREFIX . "_users p 
												ON ( f.user_id_fav = p.user_id ) 
													WHERE 
														f.type_fav = '2' 
															GROUP BY
																f.id_fav
																	ORDER BY 
																		f.id_fav 
																			DESC 
																				LIMIT 0, {$favall_config['count_web']}" );

	$count_all = $db->super_query( "SELECT FOUND_ROWS() as count" );
	$count_all = $count_all['count'];

	$tpl->load_template( 'favall/favall_catalog.tpl' );

	$tpl->set( '{input_name}', '<input style="" type="text" name="favall_name" data-favall_search="favall_name" autocomplete="off">' );
	$tpl->set( '{select_sort}', '<select class="uniform" style="" name="favall_sort" data-favall_search="favall_sort" data-select="favall"><option value="1">По дате</option><option value="2">По колличеству</option><option value="3">По имени закладки</option><option value="4">По имени юзера</option></select>' );
	$tpl->set( '{select_asc_desc}', '<select class="uniform" style="" name="favall_desc_asc" data-favall_search="favall_desc_asc" data-select="favall"><option value="1">По возрастанию</option><option value="2">По убыванию</option></select>' );

	if ( $count_all > 0 ) {

		$tpl2 = new dle_template();
		$tpl2->dir = TEMPLATE_DIR;

		$tpl2->load_template( 'favall/favall_catalog_list.tpl' );

		while ( $row = $db->get_row( $sql_result ) ) {
			$tpl2->set( '{favall_id}', $row['id_fav'] );
			$tpl2->set( '{favall_name}', $row['name_fav'] );
	
			$tpl2->set( '{favall_user}', $row['name'] );
			if ( $config['allow_alt_url'] ) {
				$go_page = $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/";					

			} else {
				$go_page = "$PHP_SELF?subaction=userinfo&user=" . urlencode( $row['name'] );

			}
			$go_page = "onclick=\"ShowProfile('" . urlencode( $row['name'] ) . "', '" . htmlspecialchars( $go_page, ENT_QUOTES, $config['charset'] ) . "', '" . $user_group[$member_id['user_group']]['admin_editusers'] . "'); return false;\"";

			if( $config['allow_alt_url'] ) {
				$tpl2->set( '[favall_user_url]', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\">" );

			} else {
				$tpl2->set( '[favall_user_url]', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\">" );

			}
			$tpl2->set( '[/favall_user_url]', '</a>' );

			$tpl2->set( '{favall_count}', $row['id_post_list_count'] );

			if ( count( explode( "@", $row['foto'] ) ) == 2 ) {
				$tpl2->set( '{foto}', 'http://www.gravatar.com/avatar/' . md5( trim( $row['foto'] ) ) . '?s=' . intval( $user_group[$row['user_group']]['max_foto'] ) );

			} else {
				if( $row['foto'] and ( file_exists( ROOT_DIR . "/uploads/fotos/" . $row['foto'] ) ) ) {
					$tpl2->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );

				} else {
					$tpl2->set( '{foto}', "{THEME}/dleimages/noavatar.png" );

				}

			}
			if ( $row['id_post_list_count'] > 0 ) {
				$tpl2->set( '{favall_url}', $config['http_home_url'] . 'favall/' . $row['id_fav'] . '/user/' . $row['name'] . '/' );
				$tpl2->set( '[favall_url]', '' );
				$tpl2->set( '[/favall_url]', '' );

			} else {
				$tpl2->set( '{favall_url}', '' );
				$tpl2->set_block( "'\\[favall_url\\](.*?)\\[/favall_url\\]'si", "" );

			}

			$tpl2->compile( 'favall_list' );

			$tpl2->result['favall_list'] = preg_replace_callback ( "#\\[declination_fav=(\d+)\\](.+?)\\[/declination_fav\\]#is", "declination_fav", $tpl2->result['favall_list'] );

		}
		$favall_list = '<div class="favall_catalog_form" data-favall_catalog="content">' . $tpl2->result['favall_list'] . '</div>';
		unset( $tpl2 );

		$tpl->set( '{favall_list}', $favall_list );
		$tpl->set( '{favall_btn}', '<div class="favall_catalog_form" data-add_content="form"><div class="favall_catalog_btn" data-add_content="btn">Показать ещё</div></div>' );

		$tpl->set( '[favall_list]', '' );
		$tpl->set( '[/favall_list]', '' );
		$tpl->set_block( "'\\[no_favall_list\\](.*?)\\[/no_favall_list\\]'si", "" );

	} else {
		$tpl->set( '{favall_list}', '' );
		$tpl->set_block( "'\\[favall_list\\](.*?)\\[/favall_list\\]'si", "" );
		$tpl->set( '[no_favall_list]', '' );
		$tpl->set( '[/no_favall_list]', '' );

	}

	$tpl->compile( 'content' );

	$tpl->clear();

}

?>