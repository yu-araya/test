function changeRadio(event) {
	var showClass = 'hr';
	var hideClass = 'ga';
	if (event.target.value == '2') {
		showClass = 'ga';
		hideClass = 'hr';
	}
	$('.' + hideClass).hide(1);
	$('.' + showClass).show(100);
	$('*[name=select_kbn]').val(event.target.value);
}
var showAdditionalButton = function (input)
{
    setTimeout(function()
    {
        var buttonPanel = $(input)
            .datepicker("widget")
            .find(".ui-datepicker-buttonpane"),
        btn = $('<button class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" type="button">クリア</button>');
        btn
            .unbind("click")
            .bind("click", function()
            {
                $.datepicker._clearDate(input);
            });
        btn.appendTo(buttonPanel);
    }, 1);
};

$(function(){
	$("#summary_start_date").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
	$("#summary_end_date").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
	$("#detail_start_date").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
	$("#detail_end_date").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
});