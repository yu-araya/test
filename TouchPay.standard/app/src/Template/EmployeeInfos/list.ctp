<ul id="breadcrumbs">
	<li><a>社員情報</a></li>
	<li>
		<a href="<?php echo $this->Url->build('/employee-infos/select', false); ?>" style="text-decoration: none" target="_self">
			検索
		</a>
	</li>
	<li><a>検索一覧</a></li>
</ul>
<br>
<p>検索条件</p><br>
<?php
	if(!empty($employeeId)){
		echo '<p>社員ID：'.$employeeId.'</p>';
	}
	if(!empty($employee_name1)){
		echo '<p>氏名&emsp;：'.$employee_name1.'</p>';
	}
	if(empty($employeeId) && empty($employee_name1)){
		echo '<p>なし</p>';
	}
?>

<br>
<?php
if(0 < count($dataList)){
	// 登録データ一覧テーブル配置
	echo $this->element('employee_infos_list_form',
		array('funcName' => 'detail', 'dataList' => $dataList));
}else{
	echo '<div class="no-data">登録内容はありません</div>';
}
?>
