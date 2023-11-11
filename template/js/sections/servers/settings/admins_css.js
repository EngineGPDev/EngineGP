function addform()
{
	index = index+1;
	form = '<tr id="'+index+'">';
	form += '<td align="center"><input type="checkbox" name="active['+index+']" class="lcs_check left" checked /></td>';
	form += '<td align="center"><input name="value['+index+']" value="" placeholder="Ник / Steam / Ip"></td>';
	form += '<td align="center"><input name="passwd['+index+']" placeholder="Введите пароль"></td>';
	form += '<td align="center" class="adflag"><input name="flags['+index+']" id="flags_'+index+'">';
	form += '<div onclick="flags_admin(\''+index+'\')"><i class="fa fa-pencil-square-o"></i></div></td>';
	form += '<td align="center"><input name="immunity['+index+']"></td>';
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

var flags = '';

function flags_admin(id)
{
	bootbox.dialog('<h3>Флаги: <span id="flags"></span></h3><hr>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_a" onclick="flags_add(\'a\')" onmouseover="flags_info(\'a\')">a</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_b" onclick="flags_add(\'b\')" onmouseover="flags_info(\'b\')">b</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_c" onclick="flags_add(\'c\')" onmouseover="flags_info(\'c\')">c</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_d" onclick="flags_add(\'d\')" onmouseover="flags_info(\'d\')">d</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_e" onclick="flags_add(\'e\')" onmouseover="flags_info(\'e\')">e</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_f" onclick="flags_add(\'f\')" onmouseover="flags_info(\'f\')">f</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_g" onclick="flags_add(\'g\')" onmouseover="flags_info(\'g\')">g</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_h" onclick="flags_add(\'h\')" onmouseover="flags_info(\'h\')">h</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_i" onclick="flags_add(\'i\')" onmouseover="flags_info(\'i\')">i</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_j" onclick="flags_add(\'j\')" onmouseover="flags_info(\'j\')">j</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_k" onclick="flags_add(\'k\')" onmouseover="flags_info(\'k\')">k</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_l" onclick="flags_add(\'l\')" onmouseover="flags_info(\'l\')">l</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_m" onclick="flags_add(\'m\')" onmouseover="flags_info(\'m\')">m</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_n" onclick="flags_add(\'n\')" onmouseover="flags_info(\'n\')">n</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_o" onclick="flags_add(\'o\')" onmouseover="flags_info(\'o\')">o</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_p" onclick="flags_add(\'p\')" onmouseover="flags_info(\'p\')">p</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_q" onclick="flags_add(\'q\')" onmouseover="flags_info(\'q\')">q</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_r" onclick="flags_add(\'r\')" onmouseover="flags_info(\'r\')">r</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_s" onclick="flags_add(\'s\')" onmouseover="flags_info(\'s\')">s</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_t" onclick="flags_add(\'t\')" onmouseover="flags_info(\'t\')">t</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_u" onclick="flags_add(\'u\')" onmouseover="flags_info(\'u\')">u</div>'
		+'<div class="btn btn-error margin-right margin-top btn-shift" id="flag_z" onclick="flags_add(\'z\')" onmouseover="flags_info(\'z\')">z</div>'
		+'<div class="margin-top" id="flags_info"></div>',
		[{
			"label" : "Применить",
			"class" : "btn-success",
			callback: function(){
				$('#flags_'+id).val(flags);
				flags = '';
			}
		},{
			"label" : "Отмена"
		}]
	);
}

function flags_add(flag)
{
	if($('#flag_'+flag).hasClass('btn-success'))
		flags_del(flag);
	else{
		btn = document.getElementById('flag_'+flag);
		flags += flag;	
		btn.className = btn.className.replace('btn-error', 'btn-success');
		$('#flags').html(flags);
	}
}

function flags_del(flag)
{
	flags = flags.replace(flag, '');
	document.getElementById('flag_'+flag).className = btn.className.replace('btn-success', 'btn-error');
	$('#flags').html(flags);
}

function flags_info(flag)
{
	switch(flag)
	{
		case 'a':
			$('#flags_info').html('Иммунитет к командам: kick / ban / slay');
			break;
		case 'b':
			$('#flags_info').html('Резервный слот');
			break;
		case 'c':
			$('#flags_info').html('Доступ к команде amx_kick');
			break;
		case 'd':
			$('#flags_info').html('Доступ к командам amx_ban / amx_unban');
			break;
		case 'e':
			$('#flags_info').html('Доступ к командам amx_slay / amx_slap');
			break;
		case 'f':
			$('#flags_info').html('Доступ к команде amx_map');
			break;
		case 'g':
			$('#flags_info').html('Доступ к команде amx_cvar');
			break;
		case 'h':
			$('#flags_info').html('Доступ к команде amx_cfg');
			break;
		case 'i':
			$('#flags_info').html('Доступ к команде amx_chat');
			break;
		case 'j':
			$('#flags_info').html('Доступ к команде amx_vote');
			break;
		case 'k':
			$('#flags_info').html('Доступ к команде sv_passord');
			break;
		case 'l':
			$('#flags_info').html('Доступ к команде amx_rcon / rcon_password');
			break;
		case 'm':
			$('#flags_info').html('Пользоваельский уровень A');
			break;
		case 'n':
			$('#flags_info').html('Пользоваельский уровень B');
			break;
		case 'o':
			$('#flags_info').html('Пользоваельский уровень C');
			break;
		case 'p':
			$('#flags_info').html('Пользоваельский уровень D');
			break;
		case 'q':
			$('#flags_info').html('Пользоваельский уровень E');
			break;
		case 'r':
			$('#flags_info').html('Пользоваельский уровень F');
			break;
		case 's':
			$('#flags_info').html('Пользоваельский уровень G');
			break;
		case 't':
			$('#flags_info').html('Пользоваельский уровень H');
			break;
		case 'u':
			$('#flags_info').html('Доступ к AMXMODX MENU');
			break;
		case 'z':
			$('#flags_info').html('Полный доступ (Главный администратор)');
			break;
	}
}