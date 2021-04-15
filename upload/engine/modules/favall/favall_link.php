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
 Файл: favall_link.php
-----------------------------------------------------
 Назначение: вывод ссылок на закладки
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

$tpl2 = new dle_template();
$tpl2->dir = TEMPLATE_DIR;

if ( $type_favall == 'login_panel' ) {
	$user_id = $member_id['user_id'];
	$name = $member_id['name'];

} else if ( $type_favall == 'profile' OR $type_favall == 'profile_popup' ) {
	$user_id = $row['user_id'];
	$name = $row['name'];

}

if ( $user_id ) {
	$db->query ( "SELECT 
					   	f.id_fav, 
					   	f.name_fav, 
					   	f.type_fav, 
						u.id_fav_list, 
						GROUP_CONCAT( u.id_post_list SEPARATOR ',' ) AS id_post_list_all, 
						count( u.id_post_list ) AS id_post_list_count 
							FROM 
								" . PREFIX . "_favall f 
									LEFT JOIN 
								" . PREFIX . "_favall_user u 
									ON ( f.id_fav = u.id_fav_list AND '{$user_id}' =  u.id_user_list ) 
										WHERE 
											f.type_fav = '1' 
												OR 
											f.user_id_fav = '{$user_id}' 
												GROUP BY
													f.id_fav 
														ORDER BY 
															f.type_fav 
																ASC, 
															f.position_fav 
																ASC");

}

$num = 0;
$tpl2->load_template( 'favall/favall_link.tpl' );

while ( $row_f = $db->get_row () ) {
	$num++;

	$tpl2->set( '{num}', $num );

	if ( $type_favall == 'login_panel' ) {
		$tpl2->set( '[login_panel]', "" );
		$tpl2->set( '[/login_panel]', "" );	
		$tpl2->set_block( "'\\[profile\\](.*?)\\[/profile\\]'si", "" );
		$tpl2->set_block( "'\\[profile_popup\\](.*?)\\[/profile_popup\\]'si", "" );

	} else if ( $type_favall == 'profile' ) {
		$tpl2->set_block( "'\\[login_panel\\](.*?)\\[/login_panel\\]'si", "" );
		$tpl2->set( '[profile]', "" );
		$tpl2->set( '[/profile]', "" );
		$tpl2->set_block( "'\\[profile_popup\\](.*?)\\[/profile_popup\\]'si", "" );


	} else if ( $type_favall == 'profile_popup' ) {
		$tpl2->set_block( "'\\[login_panel\\](.*?)\\[/login_panel\\]'si", "" );
		$tpl2->set_block( "'\\[profile\\](.*?)\\[/profile\\]'si", "" );
		$tpl2->set( '[profile_popup]', "" );
		$tpl2->set( '[/profile_popup]', "" );

	}

	$tpl2->set( '{type}', $row_f['type_fav'] );
	$tpl2->set( '{sum}', $row_f['id_post_list_count'] );
	if ( $row_f['id_post_list_count'] > 0 ) {
		$tpl2->set( '[url]', "" );
		$tpl2->set( '[/url]', "" );
		$tpl2->set_block( "'\\[no_url\\](.*?)\\[/no_url\\]'si", "" );

	} else {
		$tpl2->set_block( "'\\[url\\](.*?)\\[/url\\]'si", "" );
		$tpl2->set( '[no_url]', "" );
		$tpl2->set( '[/no_url]', "" );

	}

	$tpl2->set( '{name_fav}', $row_f['name_fav'] );

	$tpl2->set( '{url}', $config['http_home_url'] . 'favall/' . $row_f['id_fav'] . '/user/' . $name . '/');

	$tpl2->compile( 'favall_link' );

}

$tpl2->result['favall_link'] = preg_replace_callback ( "#\\[declination=(\d+)\\](.+?)\\[/declination\\]#is", "declination", $tpl2->result['favall_link'] );

$favall_link = $tpl2->result['favall_link'];

unset( $tpl2 );

?>