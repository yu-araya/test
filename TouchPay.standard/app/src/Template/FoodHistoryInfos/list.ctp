<ul id="breadcrumbs">
	<li><a>社員食堂精算</a></li>
	<li>
		<a href="<?php echo $this->Url->build('/food-history-infos/select', false); ?>" style="text-decoration: none" target="_self">
			検索
		</a>
	</li>
	<li><a>検索一覧</a></li>
</ul>
<br>
<?php
	if(!empty($yyyymm)){
		echo '<p>検索年月&emsp;：'.$yyyymm['year'].'年 '.$yyyymm['month'].'月'.'</p>';
	}
	if(!empty($employeeId)){
		echo '<p>社員コード：'.$employeeId.'</p>';
	}
	if(!empty($employee_name1)){
		echo '<p>氏名&emsp;&emsp;&emsp;：'.$employee_name1.'</p>';
	}
	if(!empty($dining_license_flg)){
		echo '<p>終了&emsp;&emsp;&emsp;：✔</p>';
	}
?>

<br>
<div>
<?php
if(0 < count($dataList)){
	// 登録データ一覧テーブル配置
	echo $this->element('food_history_infos_list_form',
		array('funcName' => 'detail', 'dataList' => $dataList));
}else{
	echo '<div class="no-data">登録内容はありません</div>';
}
?>
</div>
