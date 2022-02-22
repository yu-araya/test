<ul id="breadcrumbs">
	<li><a>社員区分マスタメンテナンス</a></li>
	<li><a>一覧</a></li>
</ul>
<?php
if(0 < count($dataList)){
	// 登録データ一覧テーブル配置
	echo $this->element('employee_kbns_list_form',
		array('funcName' => 'detail', 'dataList' => $dataList));
}else{
	echo '<div class="no-data">登録内容はありません</div>';
}
?>
<div class="button">
	<button type="button" onclick="location.href='<?php echo $this->Url->build('/employee-kbns/add'); ?>'", data-cy="addEmployeeKbn">新規登録</button>
<div>