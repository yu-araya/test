<?php echo $message['guidance03']; ?>

<table class="detail-table employee-kbn-list-table">
<thead>
<?php
// 出力
echo $this->Html->tableHeaders(array('社員区分', '社員区分名', '処理'));
?>
</thead>
<tbody>
<?php
foreach($dataList as $record){
	if($record['EmployeeKbn']['delete_flg'] != '0'){
		$tr_class = "delete";
	}else{
		$tr_class = '';
	}
?>
	<tr class="<?php echo $tr_class; ?>">
		<td data-cy="employeeKbnId<?php echo $record['EmployeeKbn']['employee_kbn']; ?>"><?php echo $record['EmployeeKbn']['employee_kbn']; ?></td>
		<td class="employee_name" data-cy="employeeKbnName<?php echo $record['EmployeeKbn']['employee_kbn']; ?>"><?php echo $record['EmployeeKbn']['employee_kbn_name']; ?></td>
<!--
		<td>
			<?php
				if ($record['EmployeeKbn']['food_allowance_flg'] == '1') {
					echo '✔';
				}
			?>
		</td>
-->
		<td data-cy = "employeeKbnDetail<?php echo $record['EmployeeKbn']['employee_kbn']; ?>">
			<?php
				echo $this->Html->link('詳細',
					array('controller' => 'employee_kbns',
						'action' => $funcName,
						$record['EmployeeKbn']['id']
					));
			?>
		</td>
	</tr>
<?php
}
?>
</tbody>
</table>
