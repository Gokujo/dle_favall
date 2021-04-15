function favallAddOrEditOrDel( a, b, c ) {
	$.ajax({
		url: "engine/ajax/favall/favall_inc.php",
		dataType: 'json',
		type: 'post',
		data: {
			fav_type: a,
			fav_name: b,
			fav_id: c
		},
		success: function( d ) {
			if ( d.success ) {
				if ( a == 'add' ) {
					$( '[data-favall="fav_name"]' ).val( '' );
					$( '[data-favall="list"]' ).prepend( d.result );
					$( '[data-favall_id="' + d.fav_id + '"]' ).fadeIn( 1000 );
					$( '[data-favall_all_form_block="2"]' ).slideUp();

				} else if ( a == 'edit' ) {
					favAllModal( d, 'update' );

				} else if ( a == 'edit_user' ) {
					favAllModal( d, 'update_user' );

				} else if ( a == 'update' ) {
					dialog_btn.dialog( 'close' );
					$( '[data-favall_id="' + c + '"]' ).html( d.result );

				} else if ( a == 'update_user' ) {
					dialog_btn.dialog( 'close' );
					$( '[data-favall_user_id="' + c + '"]' ).html( d.result );

				} else if ( a == 'dell' ) {
					DLEalert( 'Закладка успешно удалена.', 'Информация' );
					$( '[data-favall_id="' + c + '"]' ).fadeOut( 1000 );

					setTimeout( function() {
						$( '[data-favall_id="' + c + '"]' ).remove();

						if ( $( '[data-favall="list"] li' ).length < 1 ) {
							$( '[data-favall_all_form_block="2"]' ).slideDown();

						}

					}, 1000 );

				} else if ( a == 'dell_user' ) {
					DLEalert( 'Закладка успешно удалена.', 'Информация' );
					$( '[data-favall_user_id="' + c + '"]' ).fadeOut( 1000 );

					setTimeout( function() {
						$( '[data-favall_user_id="' + c + '"]' ).remove();

						if ( $( '[data-user_favall="list"] li' ).length < 1 ) {
							$( '[data-favall_all_form_block="2"]' ).slideDown();

						}

					}, 1000 );

				}

			} else {
				if ( a == 'add' || a == 'edit' || a == 'edit_user' || a == 'dell' || a == 'dell_user' ) {
					DLEalert( d.result, 'Информация' );

				} else if ( a == 'update' ) {
					$( '.error_update' ).html( '<div style="background: #FEE2E1;color: #9F270D;font-size: 12px;margin-top: 10px;padding: 5px 0;text-align: center;">' + d.result + '</div>' );

				}

			}

		}

	});

};

$(document).on('click', '[data-favall="add"]', function () {
	var status = true,
		a = $( '[data-favall="fav_name"]' ).val();

	if ( ! a ) {
		status = false;
		DLEalert( 'Поле "Имя" обязательно к заполнению.', 'Информация' );

	}

	if ( status == true ) {
		favallAddOrEditOrDel( 'add', a, '' );

	}

});

$(document).on('click', '[data-favall="edit"]', function () {
	var form	= $( this ).closest( '[data-favall_id]' ),
		a		= form.data( 'favall_id' );

	favallAddOrEditOrDel( 'edit', '', a );

});

$(document).on('click', '[data-favall_user="edit"]', function () {
	var form	= $( this ).closest( '[data-favall_user_id]' ),
		a		= form.data( 'favall_user_id' );

	favallAddOrEditOrDel( 'edit_user', '', a );

});

var dialog_btn = '';
function favAllModal( a, c ) {
	var b = {};

	b[dle_act_lang[3]] = function() {
		$( this ).dialog( 'close' );
	};

	b['Сохранить'] = function() {

		$( '.error_update' ).html( '' );

		if ( $( '[data-favall_up="fav_name"]' ).val().length < 1 ) {
			$( '[data-favall_up="fav_name"]' ).addClass( 'ui-state-error' );

		} else {
			favallAddOrEditOrDel( c, $( '[data-favall_up="fav_name"]' ).val(), $( '[data-favall_up="fav_id"]' ).val() );

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
				<td><input type="text" name="fav_name_up" data-favall_up="fav_name" class="ui-widget-content ui-corner-all" style="width:164px; padding: 5px;" value="' + a.fav_name + '"/></td>\
			</tr>\
			<tr>\
				<td colspan="2"><div class="error_update"></div><input type="hidden" name="mod" data-favall_up="fav_id" value="' + a.fav_id + '"></td>\
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
		a		= form.data( 'favall_id' ),
		b	= $( '[data-favall_id="' + a + '"] .list1' ).html();

	DLEconfirm( 'Вы уверены, что хотите удалить закладку?<br /><span style="display:block;font-size:14px;margin-top:10px;text-align:center;">' + b + '</span>', 'Подтверждение', function () {
		favallAddOrEditOrDel( 'dell', '', a );

	});

});

$(document).on('click', '[data-favall_user="dell"]', function () {
	var form	= $( this ).closest( '[data-favall_user_id]' ),
		a		= form.data( 'favall_user_id' ),
		b	= $( '[data-favall_user_id="' + a + '"] .list1' ).html();

	DLEconfirm( 'Вы уверены, что хотите удалить закладку?<br /><span style="display:block;font-size:14px;margin-top:10px;text-align:center;">' + b + '</span>', 'Подтверждение', function () {
		favallAddOrEditOrDel( 'dell_user', '', a );

	});

});

function favallUpdateList( a, b ) {
	$.ajax({
		url: "engine/ajax/favall/favall_inc.php",
		dataType: 'json',
		type: 'post',
		data: {
			fav_type: a,
			update_list: b
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
	var a = $( '[data-favall="nestable"]' ).nestable( 'serialize' );
	if ( a != '' ) {
		favallUpdateList( 'update_list', a );

	}

});

function favallAjaxSearch( a, b, c, d, i ) {
	$.ajax({
		url: "engine/ajax/favall/favall_inc.php",
		dataType: 'json',
		type: 'post',
		data: {
			count_s: a,
			name_s: b,
			sort_s: c,
			desc_asc_s: d,
			fav_type: i
		},
		success: function( g ) {
			if ( g.success ) {
				if ( i == 'change_content_user' ) {
					$( '[data-user_favall="list"]' ).html( g.result );

				} else if ( i == 'add_content_user' ) {
					$( '[data-user_favall="list"]' ).append( g.result );

				}

				if ( ! g.result ) {
					$( '[data-favall_all_form_block="1"]' ).fadeIn();

				} else {
					$( '[data-favall_all_form_block="1"]' ).fadeOut();

				}

				if ( g.count <= $( '[data-favall_user_id]' ).length ) {
					$( '[data_add_user="form"]' ).fadeOut();

				} else {
					$( '[data_add_user="form"]' ).fadeIn();

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

$(document).on('click', '[data_add_user="btn"]', function () {
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