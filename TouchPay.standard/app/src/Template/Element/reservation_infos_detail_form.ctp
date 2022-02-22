<table class="detail-table base<?php echo $baseKbn; ?>">
<thead>
	<tr>
		<th>社員コード</th>
		<th>氏名</th>
		<th>メニュー</th>
		<th>登録日/時間</th>
		<th>備考</th>
		<th colspan="3">処理</th>
	</tr>
</thead>
<tbody>
<?php
	for ($i = 0; $i < count($dataList); $i++) {
		echo $this->Form->create('ReservationInfo', array('url' => ['controller' => 'reservation-infos', 'action' => $funcName]));
		echo $this->Form->hidden('id', array('value' => $dataList[$i]['id']));

		$tr_class = '';

		//削除明細の背景色設定
		if ($dataList[$i]['state_flg'] == '2') {
			$tr_class = 'delete';
		}

		//修正時にエラーがある場合、打ったままの状態にする
		$disabled = true;
		$modChedk = '';
		if(isset($this->request->data['FoodHistoryInfo'])){
			if($this->request->data['FoodHistoryInfo']['id'] == $dataList[$i]['FoodHistoryInfo']['id']){
				//入力可能状態としておく
				$disabled = false;

				$dataList[$i]['FoodHistoryInfo']['reason'] = $this->request->data['FoodHistoryInfo']['reason'];

				//修正チェックボックスを選択状態にする
				if(isset($this->request->data['update_check'])){
					$modChedk = "checked='checked'";
				}

				//エラー明細の背景色設定
				$tr_class = "input_error";
			}
		}
?>
	<tr id="line<?php echo $i; ?>" class="<?php echo $tr_class; ?>">
		<td class="employee_id" data-cy="reservationId<?php echo $i; ?>"><?php echo $dataList[$i]['employee_id']; ?></td>
		<td class="employee_name" data-cy="reservationName<?php echo $i; ?>"><?php echo strval($dataList[$i]['employee_name1']); ?></td>
		<td class="food_division" data-cy="reservationFood<?php echo $i; ?>"><?php echo strval($dataList[$i]['food_division_name']); ?></td>
		<td class="date" data-cy="reservationTime<?php echo $i; ?>"><?php echo strval($dataList[$i]['created']); ?></td>
		<td class="reason">
			<?php
				echo $this->Form->control('reason',
					array('type' => 'text'
						,'label' => ''
						,'id' => 'reason'.$i
						,'value' => $dataList[$i]['reason']
						,'maxlength' => 50
						,'data-cy' => 'reservationMemo' . $i
						,'disabled' => $disabled,
					));
			?>
		</td>
		<td class="update">
			<input type ="checkbox" id ="update_check<?php echo $i ?>" name="update_check" data-cy="reservationFix<?php echo $i; ?>" onClick="changeStyle(this, <?php echo $i ?>);" <?php echo $modChedk; ?>>
			<label for="update_check<?php echo $i ?>">修正</label>
		</td>
		<td class="delete">
			<?php
				if($dataList[$i]['state_flg'] == '2'){
					echo "<input type ='hidden' id ='delete_check$i'>";
				}else{
					echo "<input type ='checkbox' id ='delete_check$i' name='delete_check' data-cy = 'reservationDelete$i' onClick='changeStyle(this, $i);'>";
					echo "<label for='delete_check$i'>削除</label>";
				}
			?>
		</td>
		<td class="submit">
			<?php echo $this->Form->submit('反映', array('id'=>'submit'.$i, 'data-cy' => 'reservationRefrect' . $i)); ?>
			<script type="text/javascript">
				changeStyle2(<?php echo $i ?>);
			</script>
		</td>
	</tr>
<?php
		echo $this->Form->end();
	}
?>
</tbody>
</table>
<br>
