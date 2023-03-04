function addform()
{
	index = index+1;
	form = '<tr id="'+index+'">';
	form += '<td align="center"><input type="checkbox" name="active['+index+']" class="lcs_check left" checked /></td>';
	form += '<td align="center"><input name="value['+index+']" value="" placeholder="Ник / Steam / Ip"></td>';
	form += '<td align="center"><input name="passwd['+index+']" placeholder="Введите пароль"></td>';
	form += '<td align="center" class="adminflags"><input name="flags['+index+']" id="flags_'+index+'">';
	form += '<div onclick="flags_admin(\''+index+'\')"><i class="fa fa-pencil-square-o"></i></div></td>';
	form += '<td align="center"><select name="type['+index+']">';
	form += '<option value="a">Ник/Пароль</option><option value="c">SteamID/Пароль</option><option value="ce">SteamID</option><option value="de">IP Адрес</option></select></td>';
	form += '<td align="center"><input name="time['+index+']" class="date-picker" data-date-format="dd.mm.yy" ></td>';
	form += '<td align="center"><input name="info['+index+']"></td>';
	form += '<td align="center"><button class="btn-error btn-max" onclick="return delete_admin('+index+');">Удалить</button></td>';
	form += '</tr>';
	$('#forms').append(form);

	$('input').lc_switch();
	$('.date-picker').datepicker();
	return false;
}

function delete_admin(id)
{
	$('#'+id).empty();
	return false;
}

$('#privilege').ajaxForm({
	dataType: 'json',
	success: function(data)
	{
		$.each(data, function(i, val)
		{
			if(i == 'e')
				bootbox.dialog('<h3 class="red">Ошибка</h3>'+val,
					[{
						"label" : "OK",
						"class" : "btn-small btn-primary",
					}]
				);

			if(i == 's')
				location.reload();
		});
	}
});

function flags_admin(id)
{
	bootbox.dialog('<table class="table_pad form">'
		+'	<tbody>'
		+'		<tr>'
		+'			<td width="5%"><input type="checkbox" id="sel_all_flags"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_all_flags">Выбрать все (кроме z)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td colspan="2"><h3>Список флагов:</h3></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_a" value="a" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_a"><b>a</b> Иммунитет (не может быть кикнут / забанен и т.д)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_b" value="b" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_b"><b>b</b> Резервирование слотов (может использовать зарезервированные слоты)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_c" value="c" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_c"><b>c</b> Команда amx_kick</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_d" value="d" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_d"><b>d</b> Команда amx_ban и amx_unban</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_e" value="e" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_e"><b>e</b> Команда amx_slay и amx_slap</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_f" value="f" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_f"><b>f</b> Команда amx_map</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_g" value="g" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_g"><b>g</b> Команда amx_cvar (не все CVAR\'ы доступны)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_h" value="h" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_h"><b>h</b> Команда amx_cfg</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_i" value="i" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_i"><b>i</b> amx_chat и другие команды чата</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_j" value="j" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_j"><b>j</b> amx_vote и другие команды голосований (Vote)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_k" value="k" type="checkbox"></td></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_k"><b>k</b> Доступ к изменению значения команды sv_password (через команду amx_cvar)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_l" value="l" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_l"><b>l</b> Доступ к amx_rcon и rcon_password (через команду amx_cvar)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_m" value="m" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_m"><b>m</b> Уровень доступа A (для иных плагинов)</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_n" value="n" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_n"><b>n</b> Уровень доступа B</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_o" value="o" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_o"><b>o</b> Уровень доступа C</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_p" value="p" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_p"><b>p</b> Уровень доступа D</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_q" value="q" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_q"><b>q</b> Уровень доступа E</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_r" value="r" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_r"><b>r</b> Уровень доступа F</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_s" value="s" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_s"><b>s</b> Уровень доступа G</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_t" value="t" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_t"><b>t</b> Уровень доступа H</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_u" value="u" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_u"><b>u</b> Основной доступ</label></div></td>'
		+'		</tr>'
		+'		<tr>'
		+'			<td width="5%"><input class="amxflag" id="sel_flag_z" value="z" type="checkbox"></td>'
		+'			<td><div class="btn btn-none"><label for="sel_flag_z"><b>z</b> Игрок (не администратор)</label></div></td>'
		+'		</tr>'
		+'	</tbody>'
		+'</table>',
		[{
			"label" : "Применить",
			"class" : "btn-success",
			callback: function()
			{
				var flags = [];

				$("input[id^=sel_flag]:checked").each(function(){
					flags.push($(this).val());
				});

				$('#flags_'+id).val(flags.join(''));
			}
		},{
			"label" : "Отмена"
		}]
	);

	$('#sel_all_flags').click(function()
	{
		if(!$(this).attr('checked'))
			$('.amxflag').removeAttr('checked');
		else
			$('.amxflag:not(:last)').attr('checked', true);
	});

	$('.amxflag').live('click', function()
	{
		var flags = $('.amxflag').size();
		var select = $('.amxflag:checked').size();

		if(flags == select)
			$('#sel_all_flags').attr('checked', true);
		else
			$('#sel_all_flags').removeAttr('checked');
	});
}