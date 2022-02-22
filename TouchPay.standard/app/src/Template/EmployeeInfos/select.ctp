<ul id="breadcrumbs">
	<li><a>社員情報</a></li>
	<li><a>検索</a></li>
</ul>
<br>
<?php
	// データ入力フォーム配置
	echo $this->element('employee_infos_select_form',
		array('funcName' => '/employee-infos/lists', 'buttonValue' => '検索'))
?>
<br>
<button type="button" onclick="location.href='<?php echo $this->Url->build('/employee-infos/add'); ?>'" data-cy="employeeAdd">新規登録</button>
