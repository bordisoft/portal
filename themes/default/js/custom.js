$(document).ready(function(){

	$(document).on('click', '.request', function(){
		if($(this).parent('li') && $(this).parent('li').hasClass('active')){ return false; }
		var location = $(this).attr('data-location').split('.'),
			data = {route:location[0]};
		data.task = ("undefined" != typeof(location[2]) ? location[2] : ("undefined" != typeof(location[1]) ? location[1] : 'index'));
		data.action = "undefined" != typeof(location[2]) && in_array(location[1],['get','set']) ? location[1] : 'get';
		ajaxRequest(data,true);
		return false;
	});

	$(document).on('click', '.locale', function(){
		var i_lang = $(this).attr('data-value'),
			data = {
				route: $(this).attr('data-route'),
				task:"locale",
				action:"set",
				args:{
					"i_lang":i_lang,
					route: $(this).attr('data-route'),
					task: $(this).attr('data-task'),
				}
			};
		ajaxRequest(data,true);
		return false;
	});

	$(document).on('click', 'form.validate button[type=submit]', function(){
		var form = $(this).closest('form');
		if(formValidate(form))
		{
			if(in_array(form.find('input[name=task]').val(),["login","restore"]) && form.find('input[name=action]').val() == 'authorization')
			{
				$('<input type="hidden" name="token" value="'+token+'" />').appendTo(form);
				form.submit();
				loader(form);
			}
			else
			{
				ajaxRequest(form.serializeToObject(),true,form,form.attr("data-callback"));
				return false;
			}
		}
		else { return false; }
	});

	$(document).on('keypress', 'form.validate .check:not(.email)', function(){
		$(this).bind('input propertychange', function() {
			if (!isCorrectField($(this))) {
				$(this).val($(this).val().replace(generateRegex($(this).attr('class')),""));
				showMessage($(this),"incorrect");
			}
		});
	});

	$(document).on('bind', 'form.validate input.email', function(){
		$(this).focusout(function() {
			if ($(this).val() != '') {
				if (!isCorrectField($(this))) { showMessage($(this),"incorrect"); }
			}
		})
	});

	$(document).on("change", "form.credit_card select[id$=i_payment_method]", function(){ ccFields($(this).closest("form.credit_card")); });

	$(document).on("change", "select.has-child", function(){
		var value = $(this).val(),
			child = $('#'+$(this).attr('data-child')).find("select[name*="+$(this).attr('data-child')+"]"),
			childs = $('#'+$(this).attr('data-child')).find('select.has-parent'),
			html = "";
		childs.each(function(){
			html = (value && value == $(this).attr('data-parent')) ? $(this).html() : html;
		});
		child.html(html);
		child.prop("disabled",(html ? "" : "disabled"));
	});

	$(document).on("click", ".page", function(){
		if(!$(this).parent("li").hasClass("active"))
		{
			var page = parseInt($(this).attr("data-value")),
				name = $(this).attr("data-name").replace(/\[/g,"\\[").replace(/\]/g,"\\]");
			$(this).closest("form").find("input[name="+name+"]").val(page);
			$(this).closest(".pagination").find("li").removeClass("active").each(function(){
				if(page == $(this).find("a.page").attr("data-value")) { $(this).addClass("active"); }
			});
			if(!$(this).closest("form").find("button[type=submit]").length)
				$(this).closest("form").append("<button style=\"display:none;\" type=\"submit\">Submit</button>");
			$(this).closest("form").find("button[type=submit]").trigger("click");
		}
		return false;
	});

	$(document).on("click", ".refresh", function(){
		var data = {
			args:{ route: $(this).attr("data-route"), task: $(this).attr("data-task") },
			route: $(this).attr("data-route"),
			task: "refresh",
			action: "set"
		};
		ajaxRequest(data,true);
	});

	$(document).on("change", ".on-page", function(){
		var service = $(this).attr("id").replace("-limit",""),
			limit = parseInt($(this).val()),
			from = parseInt($("#"+service+"-from").val());
		from = Math.floor(from/limit)*limit;
		$("#"+service+"-from").val(from);
		if(!$(this).closest("form").find("button[type=submit]").length)
			$(this).closest("form").append("<button style=\"display:none;\" type=\"submit\">Submit</button>");
		$(this).closest("form").find("button[type=submit]").trigger("click");
	});

	$(document).on("click",'.add-button,.cancel-button,.edit-button',function(){
		var form = $(this).closest("form"),
			action = $(this).hasClass("add-button") ? "add" : ($(this).hasClass("cancel-button") ? "cancel" : "edit"),
			container = form.find(".template").is(":visible") ? form.find(".template") : ("add" == action ? form.find(".template") : $(this).closest(".editable"));
		container.find("input,select").each(function(){
			if($(this).attr("name")) $(this).prop("disabled",("cancel" == action ? "disabled" : ""));
		});
		container.find(".cancel-button,.save-button")[("cancel" == action ? "hide" : "show")]();
		container.find("select,input[type=\"text\"]").each(function(){
			if($(this).hasClass("readonly")) { $(this).removeClass("readonly");$(this).addClass("active"); }
			else { $(this).addClass("readonly");$(this).removeClass("active"); }
		});
		container[(form.find(".template").is(":visible") && "cancel" == action ? (form.find("table tbody tr").length && form.find("table tbody tr").length > 1 ? "hide" : "show") : "show")]();
		form.find(".edit-button")[("cancel" == action ? "show" : "hide")]();
		form.find(".add-button")[("cancel" == action ? "show" : "hide")]();
	});

	$(document).on("click",".save-button",function(){
		var form = $(this).closest("form");
		if(formValidate(form))
		{
			var	container = $(this).closest(".editable").length > 0 ? $(this).closest(".editable") : form.find(".template"),
				route = container.attr("data-route"),
				task = container.attr("data-task"),
				action = container.attr("data-action"),
				i_name = container.attr("data-i_value"),
				route_field = form.find("input[name=route]"),
				task_field = form.find("input[name=task]"),
				action_field = form.find("input[name=action]"),
				i_value = container.attr("data-"+i_name);
			if(route_field.length > 0) route_field.remove();
			if(task_field.length > 0) task_field.remove();
			if(action_field.length > 0) action_field.remove();
			form.append("<input type=\"hidden\" name=\"args["+i_name+"]\" value=\""+i_value+"\"/>"+
				"<input type=\"hidden\" name=\"action\" value=\""+action+"\"/>"+
				"<input type=\"hidden\" name=\"route\" value=\""+route+"\"/>"+
				"<input type=\"hidden\" name=\"task\" value=\""+task+"\"/>"+
				"<button style=\"display:none;\" type=\"submit\">Submit</button>");
			form.find("button[type=submit]").trigger("click");
		}
	});

	$(document).on("change", ".filter", function(){
		var form = $(this).closest("form"),
			limit = $("#"+$(this).attr("id").replace("-filter","")+"-limit"),
			from = $("#"+$(this).attr("id").replace("-filter","")+"-from");
		limit.val(10);
		from.val(0);
		if(!form.find("button[type=submit]").length)
			form.append("<button style=\"display:none;\" type=\"submit\">Submit</button>");
		form.find("button[type=submit]").trigger("click");
	});
//	$(document).on('click', '.add-button', function(){
//	var containers = $(this).prev('table').find('tbody tr'),
//		template = containers.first(),
//		edit_button = template.find('.edit-button'),
//		cancel_button = template.find('.cancel-button');
//	containers.each(function(){
//		if($(this).hasClass('edited'))
//		{
//			edit_table_element($(this));
//		}
//	});
//	cancel_button.click(function(){
//		var template = $(this).closest('tr'),
//			add_button = $(this).closest('table').next('.add-button');
//		template.hide();
//		add_button.show();
//	});
//	edit_button.trigger('click');
//	template.show();
//	$(this).hide();
//});

//
//	if($('.ppay-fequency').length > 0)
//	{
//		$('.ppay-fequency').each(function(){
//			var container = $(this).closest('tr');
//			$(this).change(function(){ppayment_frequency(container);});
//			container.find('.edit-button').click(function(){ppayment_frequency(container);});
//		});
//	}
//
//	if($('.edit-button').length > 0)
//	{
//		$('.edit-button').click(function(){
//			var container = $(this).closest('tr'),
//				edited_fields = container.closest('table').find('tr.edited');
//			if(edited_fields)
//			{
//				edited_fields.each(function(){
//					edit_table_element($(this));
//				});
//			}
//			edit_table_element(container);
//		});
//	}
});

$.fn.serializeToObject = function() {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name] !== undefined) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		}
		else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

/* ajax */
function ajaxRequest(data,async,el,callback)
{
	async = "undefined" != typeof(async) ? async : true;
	el = el || $('body');
	callback = callback || 'defaulAjaxCallback';
	$.ajax({
		type: "POST",
		url: url,
		beforeSend: loader(el),
		data: $.extend(data,{token:token}),
		async: async
	}).complete(function(result){/*console.log(result.responseText);*/
		var data = {};
		try
		{
			data = JSON.parse(result.responseText);
	    }
		catch (e)
	    {
			var notifications = '<div class="alert alert-danger alert-dismissable"><i class="fa fa-ban"></i>'+
					'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
					'<b>Error 500:</b> Internal server error</div>';
			data = {notifications : notifications};
	    }
		window[callback](data);
		loader(el,'remove');
	});
}

function defaulAjaxCallback(data)
{
	var varibales = {};
	for(var i in data)
	{
		if(data[i])
		{
			if(in_array(i,['title','route','task','token']))
			{
				varibales[i] = data[i];
			}
			else if(data[i]) { $('#'+i).html(data[i]); }
		}
	}
	if(varibales.task && varibales.route)
	{
		var possible_items = {};
		$("#sidebar").find("a.request").each(function(){
			var child = varibales.route+("index" == varibales.task ? "" : "."+varibales.task),
				parent = varibales.route;
			if($(this).attr("data-location") && in_array($(this).attr("data-location"),[child,parent]))
			{
				possible_items[$(this).attr("data-location")] = $(this);
			}
		});
		if(Object.keys(possible_items).length > 0)
		{
			$('#sidebar a.request').each(function(){ $(this).closest('li').removeClass("active"); });
			var li = possible_items[Object.keys(possible_items)[Object.keys(possible_items).length - 1]].closest('li');
			li.addClass("active");
			if(!li.hasClass("treeview")){ li.closest("li.treeview").addClass("active"); }
		}
	}
	if(varibales.title){ document.title = varibales.title; }
	token = varibales.token ? varibales.token : token;
}

 /* ppayments */
function ppayment_frequency(container)
{
	var frequency = container.find('select.ppay-fequency').val(),
		pay_balance_button = container.closest('table').find('thead .pay-balance-button'),
		cancel_button = container.find('.cancel-button'),
		balance_threshold = container.find('input[name=balance_threshold]'),
		amount = container.find('input[name=amount]');
	if(1 == frequency)
	{
		balance_threshold.prop('readonly',false);
		pay_balance_button.off('click');
		pay_balance_button.hide();
		if('Pay balance' == amount.val())
		{
			amount.val('');
		}
		var ev = $._data(cancel_button, 'events');
		if(!ev || ev && !ev.click)
		{
			cancel_button.off('click',ppayment_frequency_cancel);
		}
	}
	else
	{
		balance_threshold.prop('readonly',true);
		balance_threshold.val('0.00');
		pay_balance_button.show();
		pay_balance_button.click(function(){
			var amount = $(this).closest('table').find('.edited input[name=amount]');
			amount.val('Pay balance');
			amount.focus(function(){
				$(this).val('');
				$(this).off('focus');
			});
		});
		cancel_button.on('click',ppayment_frequency_cancel);
	}
}

function ppayment_frequency_cancel(event)
{
	var cancel_button = $(event.currentTarget),
		pay_balance_button = cancel_button.closest('table').find('thead .pay-balance-button');
	pay_balance_button.hide();
	cancel_button.off('click',ppayment_frequency_cancel);
}

/* credit cards */
function ccFields(form)
{
	form.each(function(){
		var cc_fields = $(this).find('input,textarea,select'),
			_this = $(this).find("select[id$=i_payment_method]").length > 0 ?  $(this).find("select[id$=i_payment_method]") :  $(this).find("input[id$=i_payment_method]");
		if(_this.length > 0)
		{
			var value = parseInt(_this.val()),
				exp_month = $(this).find("select[id$=exp_month]").length > 0 ? $(this).find("select[id$=exp_month]") : $(this).find("input[id$=exp_month]"),
				exp_year = $(this).find("select[id$=exp_year]").length > 0 ? $(this).find("select[id$=exp_year]") : $(this).find("input[id$=exp_year]"),
				iso_3166_1_a2 = $(this).find("select[id$=iso_3166_1_a2]"),
				fn = isNaN(value) ? 'hide' : 'show',
				disabled = isNaN(value) ? "disabled" : "",
				card_payment = !(value == 8 || value == 10);
			cc_fields.each(function(){
				if($(this).attr("name") && $(this).attr("name").indexOf("args[") != -1)
				{
					$(this).prop('disabled',disabled);
				}
				$(this).closest(".box-body")[fn]();
			});
			if(!isNaN(value) && exp_month.length > 0 && exp_year.length > 0)
			{
				var elements = [exp_month,exp_year];
				for(var i in elements) { elements[i].prop('disabled',(card_payment ? "" : "disabled"))[(card_payment ? "show" : "hide")](); }
			}
			_this.prop('disabled',"").closest(".box-body").show();
			if(iso_3166_1_a2.length > 0) { iso_3166_1_a2.trigger("change"); }
		}
	});
}

/* adding element to table */
function update_table_element(container,action)
{
	action = action || 'update';
	var data = {route:container.attr('data-route'), token:container.attr('data-token')},
		i_object = container.attr('data-i_object'),
		fields = container.find('input[type=text],input[type=checkbox],select');
	data[i_object] = container.attr('data-'+i_object);
	if(data['route'] && data['token'] && data[i_object])
	{
		if(form_validate(container))
		{
			var form = '<form action="'+url+'?route='+data['route']+'" method="POST">';
			fields.each(function(){
				if(!$(this).prop('disabled') && $(this).attr('name') && (!$(this).attr('type')
								|| $(this).attr('type') == 'text' || $(this).attr('type') == 'checkbox' && $(this).prop('checked')))
				{
					form += '<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'" />';
				}
			});
			form += '<input type="hidden" name="'+i_object+'" value="'+data[i_object]+'" />';
			form += '<input type="hidden" name="action" value="'+action+'" />';
			form += '<input type="hidden" name="token" value="'+data['token']+'" />';
			form += '</form>';
			$(form).appendTo('body').submit();
			edit_table_element(container);
		}
	}
}

function edit_table_element(container)
{
	var
		template = container.closest('table').find('tbody tr').first(),
		edited_fields = container.closest('table').find('tr.edited'),
		add_button = container.closest('table').next('.add-button'),
		edit_button = container.find('.edit-button'),
		cancel_button = container.find('.cancel-button'),
		save_button = container.find('.save-button'),
		remove_button = container.find('.remove-button'),
		fields = container.find('input[type=text],input[type=checkbox],select'),
		calendars = container.find('span.add-on'),
		disabled = null;
	fields.each(function(){
		if(disabled === null)
		{
			if($(this).prop('disabled'))
			{
				disabled = false;
			}
			else
			{
				disabled = true;
			}
		}
		$(this).prop('disabled',disabled);
	});
	if(!disabled)
	{
		edit_button.hide();
		if(remove_button)
		{
			remove_button.show();
			remove_button.click(function(){update_table_element($(this).closest('tr'),'delete');});
		}
		cancel_button.show();
		cancel_button.click(function(){
			var _container = $(this).closest('tr'),
				fields = _container.find('input[type=text],input[type=checkbox],select');
			fields.each(function(){
				if($(this).attr('name'))
				{
					var tag_name = $(this).prop("tagName");
					switch (tag_name)
					{
						case 'INPUT':
							var old_value = $(this).attr('data-value');
							if($(this).attr('type') == 'checkbox')
							{
								var checked = (old_value == '0' || 'N' == old_value) ? false : true;
								$(this).prop('checked',checked);
							}
							else
							{
								$(this).val(old_value);
							}
							break;
						case 'SELECT':
							var options = $(this).find('option');
							options.each(function(){
								if($(this).attr('selected'));
								{
									$(this).prop('selected',true);
									$(this).parent().val($(this).attr('value'));
								}
							});
							break;
					}
				}
			});
			edit_table_element(_container);
		});
		save_button.show();
		save_button.click(function(){update_table_element($(this).closest('tr'));});
		template.hide();
		add_button.show();
		calendars.show();
		if(!container.hasClass('edited'))
		{
			container.addClass('edited');
		}
	}
	else
	{
		if(remove_button)
		{
			remove_button.hide();
			remove_button.off('click');
		}
		calendars.hide();
		edit_button.show();
		save_button.hide();
		save_button.off('click');
		cancel_button.hide();
		cancel_button.off('click');
		container.removeClass('edited');
	}
}

/* forms */
function formValidate(form)
{
	var fields = form.find('input[type=text],input[type=checkbox],input[type=password],select,textarea'),
		invalid = [];

	fields.each(function(){
		if($(this).attr("name"))
		{
			var el = $(this);
			if(el.hasClass('mand') && isEmptyField(el))
			{
				invalid.push(el);
				showMessage(el,"empty");
			}
			else if(el.hasClass('check') && !isCorrectField(el))
			{
				invalid.push(el);
				showMessage(el,"incorrect");
			}
		}
	});

	return !(invalid.length > 0);
}

function loader(el,action)
{
	action = 'undefined' != typeof(action) && in_array(action,['add','remove']) ? action : 'add';
	el = 'undefined' != typeof(el) && el.hasClass('box')
		? el : ('undefined' != typeof(el) && (el.closest('.loader').length > 0 && !(el.closest('.loader').is("body")) ? el.closest('.loader')
				: ('undefined' != typeof(el) && el.closest('.box').length > 0 ? el.closest('.box') : $('body'))));

	if('add' == action) {
		$("button[type=submit]").prop("disabled","disabled");
		$("a.request, a.locale").css("pointer-events","none");
		el.append('<div class="overlay"></div><div class="loading-img"></div>');
	}
	else {
		$("button[type=submit]").prop("disabled","");
		$("a.request, a.locale").css("pointer-events","");
		el.find('div.overlay, div.loading-img').remove();
	}
}

/* supplementary */
function in_array(needle, haystack, strict) {
	var found = false,
	key,
	strict = !!strict;

	for (key in haystack) {
		if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
			found = true;
			break;
		}
	 }

	return found;
}