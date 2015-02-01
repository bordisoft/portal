function isEmptyField(field)
{
	var empty = false;
	if(!field.prop('disabled'))
	{
		if (field.prop("tagName") == 'SELECT' && field.find( "option:selected" ).val() == '') { empty = true; }
		else if('checkbox' == field.attr('type') && !(field.prop('checked'))) { empty = true; }
		else if ((field.attr("id") == 'email' || field.attr("class").match(/email/g)) && !isCorrectField(field)) { empty = true; }
		else if (field.val() == '') { empty = true; }
	}

	return empty;
}

function isCorrectField(field)
{
	var validate = false;
	if(!field.prop('disabled'))
	{
		var field_class = field.attr('class'),
			regexp = generateRegex(field_class);
		validate = regexp.test(field.val());
	}

	return !(validate !== false);
}

function generateRegex(field_class)
{
	var pattern;
	if(field_class.match(/email/g))
	{
		pattern = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
	}
	else
	{
		pattern = '[^';
		if (field_class.match(/letters/g)) {
			pattern += "a-zA-zА-Яа-я";
		}
		if (field_class.match(/digits/g)) {
			pattern += "\\d,\\.";
		}
		if (field_class.match(/float/g)) {
			pattern += "\\d\\.";
		}
		if (field_class.match(/dashes/g)) {
			pattern += "\\-\\_";
		}
		if (field_class.match(/spaces/g)) {
			pattern += '\\s';
		}
		if (field_class.match(/punctuation/g)) {
			pattern += "\\,\\.\\'\\@\\/";
		}
		pattern += ']';
	}

	return new RegExp(pattern, "g");
}

function showMessage(el,reason)
{
	console.log(el.attr('name'));
	var type =  "empty" == reason ? null : "other";
	if (el.prop("tagName") == 'SELECT' && "empty" == reason){ type = 'select'; }
	else if('checkbox' == el.attr('type') && "empty" == reason) { type = 'checkbox'; }
	else if('email' == el.attr('name')) { type = 'email'; }
	var show_popover = (window.innerWidth >= 1000) ? true : false;
	if (el.is(":visible")) {
		type = type || null;
		var placement = 'right',
			custom_style = '',
			container = el.parent('div.form-group').length > 0 && el.hasClass('form-control') ? el.parent('div.form-group') : null,
			label = container ? (container.find('label').length > 0 ? container.find('label') : null) : null,
			text;
		switch (type) {
			case 'email':
				text = JS_ENTER_VALID_EMAIL;
				break;
			case 'checkbox':
				text = JS_MAND_CHECKBOX;
				placement = 'left';
				custom_style += ' margin-left:-30px;';
				break;
			case 'select':
				text = JS_SELECT_ITEM;
				break;
			case 'captcha':
				text = JS_INCORRECT_CAPTCHA;
				break;
			case 'data':
				text = JS_INVALID_DATA;
				break;
			case 'other':
				text = JS_INVALID_CHARACTER;
				break;
			default:
				text = ((el.prev('label').length > 0) ? "<b>\""+el.prev('label').html().trim()+"</b>\"" : JS_THIS)+" "+JS_MAND_FIELD;
		}
		var showError = function () {
			if(container)
			{
				container.addClass('has-error');
				text = $($.parseHTML('<label style="display:block;" class="control-label"><i class="fa fa-times-circle-o"></i> '+text+'</label>'));
				text.insertBefore(el);
				if(label) { label.hide(); }
			}
			else
			{
				el.attr("style","border-color:red; box-shadow:inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(233, 133, 102, 0.6)");
				if (show_popover)
				{
					el.popover({ content: text, trigger: 'manual', html: true, placement: placement });
					el.popover('show');
					var popover = el.parent().find("div.popover"),
						style = popover.attr('style');
					custom_style += ' text-align:center; width:'+get_width(text)+'px;';
					popover.attr('style', style + custom_style);
					popover.find("div.arrow").attr('style','top:50%');
				}
			}
		}
		, hideError = function () {
			if(container)
			{
				container.removeClass('has-error');
				text.remove();
				if(label) { label.show(); }
			}
			else if (show_popover)
			{
				if (show_popover) { el.popover('destroy'); }
				el.removeAttr("style");
			}
		};
		showError();
		setTimeout(hideError, 3000);
	}
}

function get_width(text)
{
	$('body').append('<span id="tmp" style="visibility:hidden;padding:9px 14px;font-family:\'Open Sans\',sans-serif;font-size:13px;"></span>');
	$('#tmp').html(text);
	var width = $('#tmp').width()+35;
	$('#tmp').remove();

	return width;
}