<?php
	echo $this->Form->create('EmployeeInfo', array('url' => $funcName, 'data-cy' => 'employeeInfosSelectForm'));
	echo $this->Form->hidden('employee_id');
?>
<div class="input-table">
<table>
	<tr>
		<td style="width: 100px;">社員コード</td>
		<td style="width: 180px;">
			<?php echo $this->Form->control("EmployeeInfo[employee_id]", array('type' => 'text', 'label' => '', 'maxlength' => 15, 'data-cy' => 'cyEmployeeId')); ?>
		</td>
		<td><?php echo $message['guidance01']; ?></td>
	</tr>
	<tr>
		<td style="width: 100px;">氏名</td>
		<td style="width: 180px;">
			<?php echo $this->Form->control("EmployeeInfo[employee_name1]", array('type' => 'text', 'label' => '', 'maxlength' => 40, 'data-cy' => 'cyEmployeeName')); ?>
		</td>
		<td><?php echo $message['guidance02']; ?></td>
	</tr>
</table>
</div>
<?php
	//echo $this->Form->end("検索");
        echo $this->Form->submit('検索', ['label' => false]);
	echo $this->Form->end();
?>
