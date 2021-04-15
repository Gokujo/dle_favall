function openListFavall( json_type, id_post, id_favall ) {
	$.ajax({
		url: dle_root + "engine/ajax/favall/favall_modal.php",
		dataType: 'json',
		type: 'post',
		data: {
			json_type: json_type,
			id_post: id_post,
			id_favall: id_favall
		},
		success: function( data ) {
			if ( data.success ) {
				if ( json_type == 'open' ) {
					openModalFavall( data )

				} else if ( json_type == 'add_dell' ) {
					$( '[data-favall_id="' + id_favall + '"]' ).replaceWith( data.result );
					console.log( data.result );

				}

			} else {
				DLEalert( data.result, 'Информация' );

			}

		}

	});

};

function openModalFavall( data ) {
	var b = {};

	b['Закрыть'] = function() {
		$( this ).dialog( 'close' );
	};

	$('#dlepopup').remove();

	$('body').append('<div id="dlepopup" title="Закладки (' + data.title + ')" style="display:none">' + data.result + '</div>');

	$('#dlepopup').dialog({
		//draggable: false,
		modal: true,
		closeOnEscape: true,
		dialogClass: 'dlepopup_favall',
		autoOpen: true,
		show: 'fade',
		hide: 'fade',
		width: 570
	});

};

$( document ).on( 'click', '[data-favall_open]', function () {
	var status	= true,
		id_post	= Number( $( this ).data( 'favall_open' ) );

	if ( id_post < 1 ) {
		DLEalert( 'Ошибка ID данных.', 'Информация' );
		status = false;

	}

	if ( status == true ) {
		openListFavall( 'open', id_post, '' );

	}

});

$( document ).on( 'click', '[data-favall="add_dell"]', function () {
	var $favall_f	= $( this ).closest( '[data-favall_id]' ),
		id_favall	= $favall_f.data( 'favall_id' ),
		$favall_p	= $( this ).closest( '[data-favall_id_post]' ),
		id_post		= $favall_p.data( 'favall_id_post' );

	openListFavall( 'add_dell', id_post, id_favall );

});

/********************************************/
function favallAddOrEditOrDel( fav_type, fav_name, fav_id ) {
	$.ajax({
		url: dle_root + "engine/ajax/favall/favall_manager.php",
		dataType: 'json',
		type: 'post',
		data: {
			fav_type: fav_type,
			fav_name: fav_name,
			fav_id: fav_id
		},
		success: function( data ) {
			if ( data.success ) {
				if ( fav_type == 'add' ) {
					$( '[data-favall="fav_name"]' ).val( '' );
					$( '[data-favall="list"]' ).prepend( data.result );
					$( '[data-favall_id="' + data.fav_id + '"]' ).fadeIn( 1000 );
					$( '[data-favall_all_form_block="2"]' ).slideUp();

				} else if ( fav_type == 'edit' ) {
					favAllModal( data );

				} else if ( fav_type == 'update' ) {
					dialog_btn.dialog( 'close' );
					$( '[data-favall_id="' + fav_id + '"]' ).html( data.result );

				} else if ( fav_type == 'dell' ) {
					DLEalert( 'Закладка успешно удалена.', 'Информация' );
					$( '[data-favall_id="' + fav_id + '"]' ).fadeOut( 1000 );

					setTimeout( function() {
						$( '[data-favall_id="' + fav_id + '"]' ).remove();

						if ( $( '[data-favall="list"] li' ).length < 1 ) {
							$( '[data-favall_all_form_block="2"]' ).slideDown();

						}

					}, 1000 );

				}

			} else {
				if ( fav_type == 'add' || fav_type == 'edit' || fav_type == 'dell' ) {
					DLEalert( data.result, 'Информация' );

				} else if ( fav_type == 'update' ) {
					$( '.error_update' ).html( '<div style="background: #FEE2E1;color: #9F270D;font-size: 12px;margin-top: 10px;padding: 5px 0;text-align: center;">' + data.result + '</div>' );

				}

			}

		}

	});

};

$(document).on('click', '[data-favall="add"]', function () {
	var status = true,
		fav_name = $( '[data-favall="fav_name"]' ).val();

	if ( ! fav_name ) {
		status = false;
		DLEalert( 'Поле "Имя" обязательно к заполнению.', 'Информация' );

	}

	if ( status == true ) {
		favallAddOrEditOrDel( 'add', fav_name, '' );

	}

});

$(document).on('click', '[data-favall="edit"]', function () {
	var form	= $( this ).closest( '[data-favall_id]' ),
		id		= form.data( 'favall_id' );

	favallAddOrEditOrDel( 'edit', '', id );

});

var dialog_btn = '';
function favAllModal( data ) {
	var b = {};

	b[dle_act_lang[3]] = function() {
		$( this ).dialog( 'close' );
	};

	b['Сохранить'] = function() {

		$( '.error_update' ).html( '' );

		if ( $( '[data-favall_up="fav_name"]' ).val().length < 1 ) {
			$( '[data-favall_up="fav_name"]' ).addClass( 'ui-state-error' );

		} else {
			favallAddOrEditOrDel( 'update', $( '[data-favall_up="fav_name"]' ).val(), $( '[data-favall_up="fav_id"]' ).val() );

		}
		
		dialog_btn = $( this );

	};

	$('#dlepopup').remove();

	$('body').append('\
<div id="dlepopup" title="Редактирование закладки" style="display:none">\
	<table width="100%">\
		<tbody>\
			<tr>\
				<td width="260">Имя:</td>\
				<td><input type="text" name="fav_name_up" data-favall_up="fav_name" class="ui-widget-content ui-corner-all" style="width:164px; padding: 5px;" value="' + data.fav_name + '"/></td>\
			</tr>\
			<tr>\
				<td colspan="2"><div class="error_update"></div><input type="hidden" name="mod" data-favall_up="fav_id" value="' + data.fav_id + '"></td>\
			</tr>\
		</tbody>\
	</table>\
</div>');
						
	$('#dlepopup').dialog({
		autoOpen: true,
		width: 500,
		buttons: b
	});

};

$(document).on('click', '[data-favall="dell"]', function () {
	var form	= $( this ).closest( '[data-favall_id]' ),
		id		= form.data( 'favall_id' ),
		name	= $( '[data-favall_id="' + id + '"] .list1' ).html();

	DLEconfirm( 'Вы уверены, что хотите удалить закладку?<br /><span style="display:block;font-size:14px;margin-top:10px;text-align:center;">' + name + '</span>', 'Подтверждение', function () {
		favallAddOrEditOrDel( 'dell', '', id );

	});

});

function favallUpdateList( fav_type, update_list ) {
	$.ajax({
		url: dle_root + "engine/ajax/favall/favall_manager.php",
		dataType: 'json',
		type: 'post',
		data: {
			fav_type: fav_type,
			update_list: update_list
		},
		success: function( data ) {
			if ( data.success ) {
				DLEalert( 'Сортировка закладок успешно сохранена.', 'Информация' );

			} else {
				DLEalert( data.result, 'Информация' );

			}

		}

	});

};

$(document).on('click', '[data-favall="update_list"]', function () {
	var update_list = $( '[data-favall="nestable"]' ).nestable( 'serialize' );
	if ( update_list != '' ) {
		favallUpdateList( 'update_list', update_list );

	}

});

/*******************/
function favallAjaxSearch( a, b, c, d, i ) {
	$.ajax({
		url: dle_root + "engine/ajax/favall/favall_manager.php",
		dataType: 'json',
		type: 'post',
		data: {
			count_s: a,
			name_s: b,
			sort_s: c,
			desc_asc_s: d,
			fav_type: i
		},
		success: function( data ) {
			if ( data.success ) {
				if ( data.result ) {
                    if ( i == 'change_content_user' ) {
                        $( '[data-favall_catalog="content"]' ).html( data.result );
    
                    } else if ( i == 'add_content_user' ) {
                        $( '[data-favall_catalog="content"]' ).append( data.result );
    
                    }

				} else {
					$( '[data-favall_catalog="content"]' ).html( 'По данныйм критериям поиска в базе закладок не найдено' );

				}

				if ( data.count <= $( '[data-favall_user_id]' ).length ) {
					$( '[data-add_content="form"]' ).fadeOut();

				} else {
					$( '[data-add_content="form"]' ).fadeIn();

				}

			}

		}

	});

};

var b_fls = '',
	c_fls = '',
	d_fls = '';
function favall_search( i ) {
	var a = $( '[data-favall_user_id]' ).length,
		b = $( '[data-favall_search="favall_name"]' ).val(),
		c = $( '[data-favall_search="favall_sort"]' ).val(),
		d = $( '[data-favall_search="favall_desc_asc"]' ).val();

	if ( b.length < 3 ) {
		b = '';

	}

	if ( i == 'add_content_user' ) {
		favallAjaxSearch( a, b, c, d, i );

	} else if ( i == 'change_content_user' ) {
		if ( b != b_fls || c != c_fls || d != d_fls ) {
			clearInterval( search_delay_name );
			b_fls = b;
			c_fls = c;
			d_fls = d;
			favallAjaxSearch( a, b, c, d, i );

		}

	}

};

$(document).on('click', '[data-add_content="btn"]', function () {
	favall_search( 'add_content_user' );

});

$(document).on('change', '[data-select="favall"]', function () {
	favall_search( 'change_content_user' );

});

//fast_favall_search
var search_delay_name = false,
	search_mc_value_name = '';
function fast_favall_search () {
	$( '[data-favall_search="favall_name"]' ).attr( 'autocomplete', 'off' );

	$( '[data-favall_search="favall_name"]' ).keyup( function() {
		fast_favall_search_favall_name ();
	
	});

};

function fast_favall_search_favall_name () {
	var a = $( '[data-favall_search="favall_name"]' ).val();

	if ( search_mc_value_name != a && a.length >= 3 || a.length < search_mc_value_name.length && a.length == 2 ) {
		search_mc_value_name = a;
		clearInterval( search_delay_name );

		search_delay_name = setInterval( function() {
			favall_search( 'change_content_user' ); 

		}, 600);

	}

};

$( document ).ready( function() {
	fast_favall_search();

	$( '[data-favall="nestable"]' ).nestable({
		maxDepth: 1
	});

});