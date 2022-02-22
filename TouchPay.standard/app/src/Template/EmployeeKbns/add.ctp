<ul id="breadcrumbs">
	<li>
		<a href="<?php echo $this->Url->build('/employee-kbns/lists', false); ?>" style="text-decoration: none" target="_self">
			社員区分マスタメンテナンス
		</a>
	</li>
	<li><a>新規登録</a></li>
</ul>
<br>
<?php
	echo $this->Form->create('EmployeeKbn', array('url' => ['controller' => 'employee-kbns', 'action' => 'insert'], 'data-cy' => 'employee-kbn-add-form'));
	echo $this->Form->hidden("EmployeeKbn[employee_kbn]");

	// 登録データ配置
		echo $this->element('employee_kbns_detail_form', array('mode' => 'add'));
?>
<br>
<div>
	<?php //echo $this->Form->end('登録'); ?>
	<?php echo $this->Form->submit('登録', ['label' => false]); ?>
	<?php echo $this->Form->end(); ?>
</div>
