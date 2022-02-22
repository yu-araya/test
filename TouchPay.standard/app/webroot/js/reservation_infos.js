$(document).ready(function(){
	$("#employee_id").change(function(e) {
		$.ajax({
			url: $("#url_search_employee").val() + "getEmployee",
			type: "POST",
			dataType: "json",
			timeout : 10000,
			cache : false,
			data: {
				search_employee_id: e.target.value,
				_csrfToken: $("input[name='_csrfToken']").val()
			},
			success: function(response){
				if (response && response.length == 1) {
					$("#employee_name").val(response[0].EmployeeInfo.employee_name1);
				} else {
					$("#employee_name").val('');
				}
			},
			error: function(xhr, ts, err){
				$("#employee_name").val('');
			}
		});
	});

	$("#item2_count").change(function(e) {
		if (e.target.value != '') {
			var countOrder = $("#count_order").val();
			var calc = Number(countOrder) - Number(e.target.value);
			if (calc < 0) {
				calc = 0;
			}
			$("#item1_count").val(calc)
		} else {
			$("#item1_count").val($("#count_order").val())
		}
	});

	$("#item1_count").change(function(e) {
		if (e.target.value != '') {
			var countOrder = $("#count_order").val();
			var calc = Number(countOrder) - Number(e.target.value);
			if (calc < 0) {
				calc = 0;
			}
			$("#item2_count").val(calc)
		} else {
			$("#item1_count").val($("#count_order").val())
		}
	});
});

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

function changeTargetDate(form){
	form.action = form.action + '/index';
	form.submit();
}
