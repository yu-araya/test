<p>
	<ul id="breadcrumbs">
		<li><a>社員食堂精算集計</a></li>
	</ul>
</p>
<br>

<?php
	echo $this->Form->create('FoodHistoryInfo', array('url' => array('controller' => 'food-history-infos', 'action' => 'sumdaily')));
?>
<div class="input-table">
<table border="0">
	<tr>
		<td class="width_100">事業所</td>
		<td>
			<?php
				echo $this->Form->control("FoodHistoryInfo[base_kbn]",
					array('options' => $baseKbnList,
						'value' => $baseKbn,
						'label' => false,
						'data-cy' => 'baseKbnList',
			 	 	        'onchange' => 'this.form.submit();',
                                                'id' => 'FoodHistoryInfoBaseKbn'						
					));
			?>
		</td>
	</tr>
	<tr>
		<td style="width: 100px;">検索年月</td>
		<td>
			<?php
	                        $monthNames = ['01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', '11' => '11' , 12 => '12'];
	                        $this->Form->templates([
                                    'dateWidget' => '{{year}}-{{month}}'
                                ]);
				echo $this->Form->input("FoodHistoryInfo[card_recept_time]",
					array(
						'type' => 'date',
						'label' => '',
						'dateFormat' => 'YM',
						'maxYear' => date('Y') + 1,
						'minYear' => date('Y') - 2,
						'monthNames' => $monthNames,
						'year' => [
                                                     'onchange' => 'this.form.submit();',
						     'id' => 'FoodHistoryInfoCardReceptTimeYear'
					        ],
						'month' => [
						     'onchange' => 'this.form.submit();',
						     'id' => 'FoodHistoryInfoCardReceptTimeMonth'
					        ],
						'day' => false,
						'value' => $yyyymm['year'].'-'.$yyyymm['month'].'-01',
						'data-cy' => 'food-history-infos-sumdaily-date'
					));
			?>
		</td>
	</tr>
</table>
</div>
<?php
	echo $this->Form->end();
?>
<?php
if(0 < count($dataList)){
	echo $this->Form->create('FoodHistoryInfo', array('url' => ['action' => 'download']));
	echo $this->Form->control("FoodHistoryInfo[yyyymm]",
					array(
						'type' => 'hidden',
						'value' => $yyyymm['year'].'-'.$yyyymm['month'].'-01',
						)
					);
	echo $this->Form->control("FoodHistoryInfo[base_kbn]",
					array(
						'type' => 'hidden',
						'value' => $baseKbn
						)
					);
	//echo $this->Form->end('ＣＳＶ出力');
	echo $this->Form->submit('ＣＳＶ出力', ['label' => false]);
	echo $this->Form->end();
}
?>



<br>
<?php
if(0 < count($dataList)){
	// 登録データ一覧テーブル配置
	echo $this->element('food_history_infos_sumdaily_form',
		array('dataList' => $dataList));
}else{
	echo '<div class="no-data">登録内容はありません</div>';
}
?>
