<?php
$employee_kbn = '';
$employee_kbn_name = '';
$food_allowance_flg = '0';
if(!empty($data)){
	$employee_kbn = $data[0]['EmployeeKbn']['employee_kbn'];
	$employee_kbn_name = $data[0]['EmployeeKbn']['employee_kbn_name'];
        $food_allowance_flg = isset($data[0]['EmployeeKbn']['food_allowance_flg']) ? $data[0]['EmployeeKbn']['food_allowance_flg'] : '0';
}
?>

<div class="input-table">
<table>
    <tr>
		<td>社員区分&emsp;<?php echo $mode == "add" ? "（必須）" : "" ?></td>
		<td>
			<?php
				if ($mode == "add") {
					echo $this->Form->control("EmployeeKbn[employee_kbn]",
						array('type' => 'text',
							'label' => false,
							'autocomplete' => 'off',
							'maxlength' => 2,
							'size' => 3,
							'value' => $employee_kbn,
							'data-cy' => 'employeeKbnId'
						));
				} else {
					echo '<div>'.$employee_kbn.'</div>';
					echo $this->Form->hidden("EmployeeKbn[employee_kbn]", array('value' => $employee_kbn));
				}
			?>
		</td>
	</tr>
	<tr>
		<td>社員区分名（必須）</td>
		<td>
			<?php
				echo $this->Form->control("EmployeeKbn[employee_kbn_name]",
					array('type' => 'text',
						'label' => false,
						'autocomplete' => 'off',
						'maxlength' => 50,
						'size' => 30,
						'value' => $employee_kbn_name,
						'data-cy' => 'employeeKbnName'
					));
			?>
		</td>
	</tr>
	<tr>
<!--
		<td>食事手当</td>
		<td>
			<?php echo $this->Form->control("EmployeeKbn[food_allowance_flg]", array('type' => 'checkbox', 'label' => '', 'checked' => $food_allowance_flg)); ?>
		</td>
-->
	</tr>
</table>
</div>
