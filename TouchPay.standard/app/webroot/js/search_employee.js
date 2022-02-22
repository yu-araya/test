$(document).ready(function(){
	$("#search_employee").autocomplete({
		delay: 100,
		minLength: 2,
		source: function(req, resp) {
			$.ajax({
				url: $("#url_search_employee").val() + "getEmployee",
				type: "POST",
				dataType: "json",
				timeout : 10000,
				cache : false,
				data: {
					search_employee: $("#search_employee").val(),
					_csrfToken: $("input[name='_csrfToken']").val()
				},
				success: function(response){
					if(response){
						var suggestList = new Array();
						for (var i = 0; i < response.length; i++) {
							var obj = {
								"label": response[i].EmployeeInfo.employee_id + '：' + response[i].EmployeeInfo.employee_name1,
								"value": response[i].EmployeeInfo.employee_id + '：' + response[i].EmployeeInfo.employee_name1
							};
							suggestList.push(obj);
						}
						resp(suggestList);

						$("ul.ui-autocomplete .ui-menu-item .ui-menu-item-wrapper").off().on('click', function(e) {
							setEmployee(e.target.innerText);
						});

						var position = $("#search_employee").offset().top;
						$("body, html").animate({scrollTop: position}, 1200, "swing");
					}
				},
				error: function(xhr, ts, err){
					resp(new Array());
				}
			});
		}
	});

	$("#employee-id-search-button").click(function(e) {
		if ($(".employee-search").hasClass('display_none')) {
			displaySearch(true);
			$("#search_employee").focus();

			var position = $("#search_employee").offset().top;
			$("body, html").animate({scrollTop: position}, 1200, "swing");
		}
	});

	$("#search_employee").keydown(function(e) {
		if (e.keyCode != 13) return;
		setEmployee(e.target.value);
		return false;
	});

	$("#search_employee").focusout(function(e) {
		displaySearch(false);
	});

	$("#search_employee").attr('spellcheck', false);
	$("#employee_id").attr('spellcheck', false);

	function setEmployee(value) {
		var arr = value.split('：');
		$("#employee_id").val(arr[0]);
		$("#employee_name").val(arr[1]);
		displaySearch(false);
	}

	function displaySearch(flag) {
		if (flag) {
			$(".employee-search").show(200);
			$(".employee-search").removeClass('display_none');
			$("button.search").prop("disabled", true);
		} else {
			$(".employee-search").hide(200);
			$(".employee-search").addClass('display_none');
			$("#search_employee").val('');
			$("#employee-id-search-button").prop("disabled", false);
		}
	}
});
