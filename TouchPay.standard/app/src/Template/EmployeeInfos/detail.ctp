<?php
$standardPlan = boolval($property['plan_config']['standard_plan']);
?>
<ul id="breadcrumbs">
	<li><a>社員情報</a></li>
	<li>
		<a href="<?php echo $this->Url->build('/employee-infos/select', false); ?>" style="text-decoration: none" target="_self">
			検索
		</a>
	</li>
	<li>
		<a href="<?php echo $this->Url->build('/employee-infos/lists', false); ?>" style="text-decoration: none" target="_self">
			検索一覧
		</a>
	</li>
	<li><a>詳細</a></li>
</ul>
<br>
<?php
	echo $this->Form->create('EmployeeInfo', array('url' => ['controller' => 'employee-infos', 'action' => 'update']));
	echo $this->Form->hidden("EmployeeInfo[id]", array('value' => $employeeInfo[0]['EmployeeInfo']['id']));

	// 登録データ配置
	echo $this->element('employee_infos_detail_form', array('mode' => 'update'));

?>
<div class="detail-submit-area employee-infos-detail-submit">
			<?php echo $this->Form->submit("修正", array('name' => 'update_proc', 'data-cy' => 'updateEmployee')); ?>
			<?php
				if ($employeeInfo[0]['EmployeeInfo']['delete_flg'] == 0) {
					echo $this->Form->submit("削除", array('name' => 'delete_proc', 'onClick' => "return confirm('社員情報を削除します。よろしいですか？')", 'data-cy' => 'employeeDelete'));
				} else {
					echo $this->Form->submit("削除の取消し", array('name' => 'undelete_proc', 'onClick' => "return confirm('社員情報の削除を取消します。よろしいですか？')", 'data-cy' => 'cancelDelete'));
				}
			?>
		<?php if (!$standardPlan): ?>
			<?php echo $this->Form->submit("パスワードリセット", array('name' => 'reset_password', 'onClick' => "return confirm('パスワードをリセットします。よろしいですか？')")); ?>
		<?php endif; ?>

<?php
	echo $this->Form->end();
?>
</div>
