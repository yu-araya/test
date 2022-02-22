<table class="detail-table reservation-next-week-table">
<thead>
<?php
// 出力
echo $this->Html->tableHeaders(array('予約日', '社員コード', '氏名', '事業所','メニュー'));
?>
</thead>
<tbody>
<?php
foreach($dataList as $record){
	if($record['ReservationInfo']['delete_flg'] != '0'){
		$tr_class = "delete";
	} else {
	       	$tr_class = '';
        }
?>
	<tr class="<?php echo $tr_class; ?>">
		<td><?php echo $tr_class. date('Y年n月j日', strtotime($record['ReservationInfo']['reservation_date'])); ?></td>
		<td class="employee_id"><?php echo strval($record['ReservationInfo']['employee_id']); ?></td>
		<td class="employee_name"><?php echo strval($record['EmployeeInfo']['employee_name1']); ?></td>
		<td class="instrument_division"><?php echo strval($record['InstrumentDivision']['instrument_name']); ?></td>
		<td class="food_division"><?php echo strval($record['FoodDivision']['food_division_name']); ?></td>
	</tr>
<?php
}
?>
</tbody>
</table>
</div>
