function changeWeekday(date) {
	if(window.confirm('「' + date + '」の休日設定を解除します。よろしいですか？')) {
		submitUpdate(date, 0);
	}
}

function changeDayOff(date) {
	if(window.confirm('「' + date + '」を休日設定します。よろしいですか？\n※予約情報がある場合は削除されます。')) {
		submitUpdate(date, 1);
	}
}

function submitUpdate(date, day_off_flag) {
	document.getElementById('base_kbn').value = document.getElementById('DayOffCalendarBaseKbn').value;
	document.getElementById('day_off_datetime').value = date;
	document.getElementById('day_off_flag').value = day_off_flag;

	var target = document.getElementById("dayOffUpdateForm");
	target.method = "post";
	target.submit();
}
