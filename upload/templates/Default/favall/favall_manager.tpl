[add_favall]
<div class="favall_block">

	<div class="favall_block_t">Добавление новой закладки</div>
	<div class="favall_block_c">

    	<div class="favall_block_p">
        	<div class="favall_block_p_l">Имя:</div>
        	<div class="favall_block_p_r"><input class="faval_input" type="text" name="fav_name" data-favall="fav_name"></div>
            <div class="favall_block_p_i">Имя вашей новой закладки. Данное поле обязательно к заполнению.</div>
        </div>

    	<div class="favall_block_p">
        	<div class="favall_block_p_l"></div>
        	<div class="favall_block_p_r"><div class="faval_btn_add" data-favall="add">Добавить</div></div>
        </div>

	</div>

</div>
[/add_favall]
<div class="favall_block">

	<div class="favall_block_t">Закладки по умолчанию</div>
	<div class="favall_block_c favall_block_c2">
    	<div class="favall_block_p favall_block_p2">{def_favall}</div>
	</div>

	<div class="favall_block_t">Список ваших закладок</div>
	<div class="favall_block_c favall_block_c2">
    	<div class="favall_block_p favall_block_p2">{user_favall}</div>
    	<div class="favall_block_p">
        	<div class="faval_btn_save" data-favall="update_list">Сохранить порядок сортировки</div>
        </div>
	</div>

</div>