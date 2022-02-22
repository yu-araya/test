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
	$("#iccard_valid_s_time").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
	$("#iccard_valid_e_time").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
	$("#iccard_valid_s_time2").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
	$("#iccard_valid_e_time2").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
});