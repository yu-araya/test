<?php echo $this->Html->script(array('/webroot/js/employee_infos'), array('inline' => false)); ?>
<?php
$employee_kbn = $table->column('employee_kbn')['default'];
$employee_id = $table->column('employee_id')['default'];
$employee_name1 = $table->column('employee_name1')['default'];
$employee_name2 = $table->column('employee_name2')['default'];
$ic_card_number = $table->column('ic_card_number')['default'];
$iccard_valid_s_time = $table->column('iccard_valid_s_time')['default'];
$iccard_valid_e_time = $table->column('iccard_valid_e_time')['default'];
$ic_card_number2 = $table->column('ic_card_number2')['default'];
$iccard_valid_s_time2 = $table->column('iccard_valid_s_time2')['default'];
$iccard_valid_e_time2 = $table->column('iccard_valid_e_time2')['default'];
$dining_license_flg = $table->column('dining_license_flg')['default'];
$memo = $table->column('memo')['default'];

if(!empty($employeeInfo)){
	$employee_kbn = $employeeInfo[0]['EmployeeInfo']['employee_kbn'];
	$employee_id = $employeeInfo[0]['EmployeeInfo']['employee_id'];
	$employee_name1 = $employeeInfo[0]['EmployeeInfo']['employee_name1'];
	$employee_name2 = $employeeInfo[0]['EmployeeInfo']['employee_name2'];
	$ic_card_number = $employeeInfo[0]['EmployeeInfo']['ic_card_number'];
	$iccard_valid_s_time = $employeeInfo[0]['EmployeeInfo']['iccard_valid_s_time'];
	$iccard_valid_e_time = $employeeInfo[0]['EmployeeInfo']['iccard_valid_e_time'];
	$ic_card_number2 = $employeeInfo[0]['EmployeeInfo']['ic_card_number2'];
	$iccard_valid_s_time2 = $employeeInfo[0]['EmployeeInfo']['iccard_valid_s_time2'];
	$iccard_valid_e_time2 = $employeeInfo[0]['EmployeeInfo']['iccard_valid_e_time2'];
	$dining_license_flg = $employeeInfo[0]['EmployeeInfo']['dining_license_flg'];
	$memo = $employeeInfo[0]['EmployeeInfo']['memo'];
}
?>

<div class="input-table">

<table>
    <tr>
		<td>社員区分&emsp;&emsp;（必須）</td>
		<td>
			<?php
                                if (!empty($this->request->data['EmployeeInfo']['employee_kbn'])) {
                                    $employee_kbn = $this->request->data['EmployeeInfo']['employee_kbn'];
                                }
				echo $this->Form->control("EmployeeInfo[employee_kbn]",
					array('options' => $employeeKbnInputList,
						'selected' => $employee_kbn,
						'label' => false,
						'data-cy' => 'cyEmployeeKbn',
						'id' => 'EmployeeInfoEmployeeKbn',
						'value' => $employee_kbn
					));
			?>
		</td>
	</tr>
    <tr>
		<td>社員コード&emsp;<?php echo $mode == "add" ? "（必須）" : "" ?></td>
		<td>
			<?php
				if ($mode == "add") {
					echo $this->Form->control("EmployeeInfo[employee_id]",
						array('type' => 'text',
							'label' => '',
							'autocomplete' => 'off',
							'maxlength' => $table->column('employee_id')['length'],
							'size' => $table->column('employee_id')['length'],
							'value' => $employee_id,
							'data-cy' => 'cyEmployeeId'
						));
				} else {
					echo '<div>'.$employee_id.'</div>';
					echo $this->Form->hidden("EmployeeInfo[employee_id]", array('value' => $employee_id));
				}
			?>
		</td>
	</tr>
	<tr>
		<td>氏名</td>
		<td>
			<?php
				echo $this->Form->control("EmployeeInfo[employee_name1]",
					array('type' => 'text',
						'label' => '',
						'autocomplete' => 'off',
						'maxlength' => $table->column('employee_name1')['length'],
						'size' => 30,
						'value' => $employee_name1,
						'data-cy' => 'cyEmployeeName'
					));
			?>
		</td>
	</tr>
	<tr>
		<td>所属</td>
		<td>
			<?php
				echo $this->Form->control("EmployeeInfo[employee_name2]",
					array('type' => 'text',
						'label' => '',
						'autocomplete' => 'off',
						'maxlength' => $table->column('employee_name2')['length'],
						'size' => 30,
						'value' => $employee_name2,
						'data-cy' => 'cyEmployeeName2'
					));
			?>
		</td>
	</tr>
	<tr>
		<td>ICカード番号（正）</td>
		<td>
			<?php
				echo $this->Form->control("EmployeeInfo[ic_card_number]",
					array('type' => 'text',
						'label' => '',
						'autocomplete' => 'off',
						'maxlength' => $table->column('ic_card_number')['length'],
						'size' => $table->column('ic_card_number')['length'],
						'value' => $ic_card_number,
						'data-cy' => 'cyCardNo'
					));
			?>
		</td>
	</tr>
	<tr>
		<td>使用期間</td>
		<td>
			<div class="date_term">
			<?php
                                if (!empty($this->request->data['EmployeeInfo']['iccard_valid_s_time'])) {
                                    $iccard_valid_s_time = $this->request->data['EmployeeInfo']['iccard_valid_s_time'];
                                }
				echo $this->Form->control("EmployeeInfo[iccard_valid_s_time]",
					array('type' => 'text',
						'id' => 'iccard_valid_s_time',
						'autocomplete' => 'off',
						'label' => '',
						'maxlength' => 10,
						'size' => 12,
						'value' => substr($iccard_valid_s_time, 0,10),
						'data-cy' => 'useTermStart'
					));
			?>～
			<?php
                                if (!empty($this->request->data['EmployeeInfo']['iccard_valid_e_time'])) {
                                    $iccard_valid_e_time = $this->request->data['EmployeeInfo']['iccard_valid_e_time'];
                                }
				echo $this->Form->control("EmployeeInfo[iccard_valid_e_time]",
					array('type' => 'text',
						'id' => 'iccard_valid_e_time',
						'autocomplete' => 'off',
						'label' => '',
						'maxlength' => 10,
						'size' => 12,
						'value' => substr($iccard_valid_e_time, 0,10),
						'data-cy' => 'useTermEnd'
					));
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>&nbsp;※入力形式：2018-01-01（2018年1月1日）</td>
	</tr>
	<tr>
		<td>ICカード番号（副）</td>
		<td>
			<?php
				echo $this->Form->control("EmployeeInfo[ic_card_number2]",
					array('type' => 'text',
						'label' => '',
						'autocomplete' => 'off',
						'maxlength' => $table->column('ic_card_number2')['length'],
						'size' => $table->column('ic_card_number2')['length'],
						'value' => $ic_card_number2,
						'data-cy' => 'cyCardNo2'
					));
			?>
		</td>
	</tr>
	<tr>
		<td class="item_title">使用期間</td>
		<td>
			<div class="date_term">
			<?php
                                if (!empty($this->request->data['EmployeeInfo']['iccard_valid_s_time2'])) {
                                    $iccard_valid_s_time2 = $this->request->data['EmployeeInfo']['iccard_valid_s_time2'];
                                }
				echo $this->Form->control("EmployeeInfo[iccard_valid_s_time2]",
					array('type' => 'text',
						'id' => 'iccard_valid_s_time2',
						'autocomplete' => 'off',
						'label' => '',
						'maxlength' => 10,
						'size' => 12,
						'value' => substr($iccard_valid_s_time2,0,10),
						'data-cy' => 'useTermStart2'
					));
			?>～
			<?php
                                if (!empty($this->request->data['EmployeeInfo']['iccard_valid_e_time2'])) {
                                    $iccard_valid_e_time2 = $this->request->data['EmployeeInfo']['iccard_valid_e_time2'];
                                }
				echo $this->Form->control("EmployeeInfo[iccard_valid_e_time2]",
					array('type' => 'text',
						'id' => 'iccard_valid_e_time2',
						'autocomplete' => 'off',
						'label' => '',
						'maxlength' => 10,
						'size' => 12,
						'value' => substr($iccard_valid_e_time2,0,10),
						'data-cy' => 'useTermEnd2'
					));
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td class="item_title"></td>
		<td>&nbsp;※入力形式：2018-01-01（2018年1月1日）</td>
	</tr>
	<tr>
		<td class="item_title">社員食堂使用不可</td>
		<td>
			<?php echo $this->Form->control("EmployeeInfo[dining_license_flg]", array('type' => 'checkbox', 'label' => '', 'checked' => $dining_license_flg, 'data-cy' => 'useImpossible')); ?>
			（✔が入力されている時は、「社員別食堂精算」で終了と表示されます。）
		</td>
	</tr>
	<tr>
		<td class="item_title">備考</td>
		<td>
			<div class="date_term">
			<?php
				echo $this->Form->control("EmployeeInfo[memo]",
					array('type' => 'textarea',
						'label' => '',
						'style' => 'resize: none;',
						'cols' => '45',
						'rows' => '2',
						'maxlength' => $table->column('memo')['length'],
						'value' => $memo,
						'data-cy' => 'memoNote'
					)
				);
			?>
			</div>
			<span class="memo_comment">※40文字まで入力できます。</span>
		</td>
	</tr>

</table>
</div>
