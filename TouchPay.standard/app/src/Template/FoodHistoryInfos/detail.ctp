<ul id="breadcrumbs">
	<li><a>社員食堂精算</a></li>
	<li>
		<a href="<?php echo $this->Url->build('/food-history-infos/select', false); ?>" style="text-decoration: none" target="_self">
			検索
		</a>
	</li>
	<li>
		<a href="<?php echo $this->Url->build('/food-history-infos/lists', false); ?>" style="text-decoration: none" target="_self">
			検索一覧
		</a>
	</li>
	<li><a>詳細</a></li>
</ul>
<br>
<p>対象年月&emsp;：<?php echo substr($yyyymm, 0, 4); ?>年<?php echo substr($yyyymm, 4, 2); ?>月</p>
<p>社員コード：<?php echo $employeeId; ?></p>
<?php
	$employeeName1 = '';
	if(!empty($employeeInfo)){
		$employeeName1 = $employeeInfo[0]['EmployeeInfo']['employee_name1'];
	}
?>
<p>氏名&emsp;&emsp;&emsp;：<?php echo $employeeName1; ?></p>
<br>

<div class="food-history-info-detail-area">
	<?php
		//合計金額算出
		$sum_food_division = 0;
		for($i=0; $i < count($dataList); $i++){
			//状態フラグが削除の場合は加算しない
			if($dataList[$i]['FoodHistoryInfo__state_flg'] != '2'){
				$sum_food_division += 1;
			}
		}
	?>

	<?php
	if(0 < count($dataList)){
	?>
	<div class="food-history-infos-sum-area">
		<?php echo $message['guidance03']; ?>
		<B>当月合計：<?php echo number_format($sum_food_division); ?>回</B>
	</div>
	<?php
		// 登録データ一覧テーブル配置
		echo $this->element('food_history_infos_detail_form',
			array('funcName' => 'update', 'dataList' => $dataList));
	}else{
		echo '<div class="no-data">登録内容はありません</div>';
	}
	?>
</div>