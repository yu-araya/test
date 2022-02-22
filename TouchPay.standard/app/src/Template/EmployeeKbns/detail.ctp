<ul id="breadcrumbs">
	<li><a>社員区分マスタメンテナンス</a></li>
	<li>
		<a href="<?php echo $this->Url->build('/employee-kbns/lists', false); ?>" style="text-decoration: none" target="_self">
			一覧
		</a>
	</li>
	<li><a>詳細</a></li>
</ul>
<br>
<?php
		echo $this->Form->create('EmployeeKbn', array('url' => ['controller' => 'employee-kbns', 'action' => 'update']));
		echo $this->Form->hidden("EmployeeKbn[id]", array('value' => $data[0]['EmployeeKbn']['id']));
		echo $this->Form->hidden("EmployeeKbn[delete_flg]", array('value' => $data[0]['EmployeeKbn']['delete_flg']));
		
		// 登録データ配置
		echo $this->element('employee_kbns_detail_form', array('mode' => 'update'));
		?>
	<div class="detail-submit-area">
	<?php echo $this->Form->submit("修正", array('name' => 'update_proc', 'data-cy' => 'employeeKbnUpdate')); ?>
	<?php
		if ($data[0]['EmployeeKbn']['delete_flg'] == 0) {
			echo $this->Form->submit("削除", array('name' => 'delete_proc', 'onClick' => "return confirm('社員区分情報を削除します。よろしいですか？')", 'data-cy' => 'employeeKbnDelete'));
		} else {
			echo $this->Form->submit("削除の取消し", array('name' => 'undelete_proc', 'onClick' => "return confirm('社員区分情報の削除を取消します。よろしいですか？')", 'data-cy' => 'employeeKbnDeleteCancel'));
		}
	?>
	<br>

	<?php
		echo $this->Form->end();
	?>
</div>
