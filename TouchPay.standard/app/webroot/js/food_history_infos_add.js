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
});

function changeInstrumentDivision(baseKbn) {
	var selectedIndex = $('#food_division_list').prop('selectedIndex');
	var id_name = '#foodhistoryinfo-food-division-list-' + baseKbn;
	$('#food_division_list').html($(id_name).html());
	$('#food_division_list').prop('selectedIndex', selectedIndex);
}
