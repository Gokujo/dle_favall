<?php

/**
 * @author: Maxim Harder (devcraft.club)
 *        	SaNcHeS (skripters.biz)
 * @description: Файл управления над работой плагина FavALL
 */

if(!defined('DATALIFEENGINE')) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );
}

if( !$is_logged ) die( "error" );

if( $_REQUEST['user_hash'] == "" OR $_REQUEST['user_hash'] != $dle_login_hash ) {
	die( "error" );
}

$data = $_POST;
if(!$data) die();
$mod_file = $data['file'];

if ( file_exists( DLEPlugins::Check(ENGINE_DIR . '/ajax/favall/' . $mod_file . '.php') ) ) {

	if ( file_exists( DLEPlugins::Check(ENGINE_DIR . '/data/favall_config.php') ) ) include_once (DLEPlugins::Check(ENGINE_DIR . '/data/favall_config.php'));

	include_once (DLEPlugins::Check(ENGINE_DIR . '/ajax/favall/' . $mod_file . '.php'));

} else {

	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../' );
	die( "Hacking attempt!" );

}