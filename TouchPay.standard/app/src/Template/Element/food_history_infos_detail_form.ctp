<?php echo $this->Html->script(array('/webroot/js/food_history_infos'), array('inline' => false)); ?>

<table class="detail-table">
<thead>
	<tr>
		<th>日</th>
		<th>曜日</th>
		<th>事業所</th>
		<th>メニュー</th>
		<th>登録日/時間</th>
		<th>備考</th>
		<th colspan="3">処理</th>
	</tr>
</thead>
<tbody>
<?php
//曜日テーブル
$weekArray = array(0 => '日', 1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土');
	for($i=0; $i < count($dataList); $i++){

		if ($dataList[$i]['FoodHistoryInfo__data_type'] == '1') {
			echo $this->Form->create('FoodHistoryInfo', array('url' => ['action' => $funcName], 'data-cy' => 'food-history-detail-form-line'.$i));
			echo $this->Form->hidden('FoodHistoryInfo.id', array('value' => $dataList[$i]['FoodHistoryInfo__id']));
		} else if ($dataList[$i]['FoodHistoryInfo__data_type'] == '2') {
			echo $this->Form->create('ReservationInfo', array('url' => ['action' => $funcName]));
			echo $this->Form->hidden('ReservationInfo.id', array('value' => $dataList[$i]['FoodHistoryInfo__id']));
		}

		//削除明細の背景色設定
		if($dataList[$i]['FoodHistoryInfo__state_flg'] == '2'){
			$tr_class = "delete";
		}else{
			$tr_class = '';
		}

		//曜日名の取得
		$year = substr($dataList[$i]['FoodHistoryInfo__target_date'], 0, 4);
		$month = substr($dataList[$i]['FoodHistoryInfo__target_date'], 5, 2);
		$day = substr($dataList[$i]['FoodHistoryInfo__target_date'], 8, 2);
		$youbi = $weekArray[date("w", mktime(0, 0, 0, $month, $day, $year))];

		//修正時にエラーがある場合、打ったままの状態にする
		$disabled = true;
		$modChedk = '';
		if(isset($this->request->data['FoodHistoryInfo'])){
			if($this->request->data['FoodHistoryInfo']['id'] == $dataList[$i]['FoodHistoryInfo__id']){
				//入力可能状態としておく
				$disabled = false;

				$dataList[$i]['FoodHistoryInfo__reason'] = $this->request->data['FoodHistoryInfo']['reason'];

				//修正チェックボックスを選択状態にする
				if(isset($this->request->data['update_check'])){
					$modChedk = "checked='checked'";
				}

				//エラー明細の背景色設定
				$tr_class = "input_error";
			}
		}
?>
	<tr id="line<?php echo $i; ?>" data-cy="line<?php echo $i;?>" class="<?php echo $tr_class; ?>">
		<td data-cy="day"><?php echo intval(substr($dataList[$i]['FoodHistoryInfo__target_date'], 8, 2)); ?></td>
		<td data-cy="weekday"><?php echo $youbi; ?></td>
		<td data-cy="base-kbn"><?php echo (isset($dataList[$i]['FoodDivision__food_division']) && isset($foodDivisionToBase[$dataList[$i]['FoodDivision__food_division']]) ? $foodDivisionToBase[$dataList[$i]['FoodDivision__food_division']] : ''); ?></td>
		<td class="food_division" data-cy="food-division"><?php echo strval($dataList[$i]['FoodDivision__food_division_name']); ?></td>
		<td class="date" data-cy="created"><?php echo strval($dataList[$i]['FoodHistoryInfo__created']); ?></td>
		<td class="reason">
			<?php
		                if ($dataList[$i]['FoodHistoryInfo__data_type'] == '1') {
				    echo $this->Form->control('FoodHistoryInfo.reason',
					array('type' => 'text'
						,'label' => ''
						,'id' => 'reason'.$i
						,'value' => $dataList[$i]['FoodHistoryInfo__reason']
						,'maxlength' => 50
						,'disabled' => $disabled
						,'data-cy' => 'reason'));
				} else if ($dataList[$i]['FoodHistoryInfo__data_type'] == '2') {
                                    echo $this->Form->control('ReservationInfo.reason',
                                        array('type' => 'text'
                                                ,'label' => ''
                                                ,'id' => 'reason'.$i
                                                ,'value' => $dataList[$i]['FoodHistoryInfo__reason']
                                                ,'maxlength' => 50
                                                ,'disabled' => $disabled
                                                ,'data-cy' => 'reason'));
				}
			?>
		</td>
		<td class="update">
			<input type ="checkbox" id ="update_check<?php echo $i ?>" data-cy="update-check" name="update_check" onClick="changeStyle(this, <?php echo $i ?>);" <?php echo $modChedk; ?>>
			<label for="update_check<?php echo $i ?>">修正</label>
		</td>
		<td class="delete">
			<?php
				if($dataList[$i]['FoodHistoryInfo__state_flg'] == '2'){
					echo "<input type ='hidden' id ='delete_check$i'>";
				}else{
					echo "<input type ='checkbox' id ='delete_check$i' name='delete_check'  data-cy='delete-check' onClick='changeStyle(this, $i);'>";
					echo "<label for='delete_check$i'>削除</label>";
				}
			?>
		</td>
		<td class="submit"><?php echo $this->Form->submit('反映', array('id'=>'submit'.$i)); ?></td>
		<script type="text/javascript">
			changeStyle2(<?php echo $i ?>);
		</script>
	</tr>
<?php
		echo $this->Form->end();
	}
?>
</tbody>
</table>
<br>
