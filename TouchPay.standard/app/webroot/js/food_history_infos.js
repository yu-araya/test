function changeStyle(object, number){
	var targetElement = document.getElementById(object.id);
	if(document.getElementById('update_check' + number).checked == true || document.getElementById('delete_check' + number).checked == true ){
		//修正チェック時
		if(object.id == "update_check"+ number){
			setDisabled(number, false);
			setSubmitDisabled(number, false);
			document.getElementById('delete_check' + number).checked = false;
		//削除チェック時
		}else if (object.id == "delete_check"+ number){
			setDisabled(number, false);
			setSubmitDisabled(number, false);
			document.getElementById('update_check' + number).checked = false;
		}
	}else if(document.getElementById('update_check' + number).checked == false && document.getElementById('delete_check' + number).checked == false ){
		setDisabled(number, true);
		setSubmitDisabled(number, true);
	}
}

function setDisabled(number, value){
	document.getElementById('reason' + number).disabled = value;
}

function setSubmitDisabled(number, value){
	if (document.getElementById('submit' + number) != null) {
		document.getElementById('submit' + number).disabled = value;
	}
}

function changeStyle2(number){
	//修正・削除チェック時
	if(document.getElementById('update_check' + number).checked == true || document.getElementById('delete_check' + number).checked == true ){
		setSubmitDisabled(number, false);
	}else if(document.getElementById('update_check' + number).checked == false && document.getElementById('delete_check' + number).checked == false ){
		setSubmitDisabled(number, true);
	}
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
	$("#card_recept_time").datepicker({
		showOn: 'focus',
		buttonText: 'カレンダー',
		showButtonPanel: true,
		beforeShow: showAdditionalButton,
		onChangeMonthYear: showAdditionalButton
	});
});