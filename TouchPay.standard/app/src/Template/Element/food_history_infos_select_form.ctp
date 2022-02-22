<?php
	echo $this->Form->create('FoodHistoryInfo', array('url' => $funcName, 'data-cy' => 'food-history-info-select-form'));
	echo $this->Form->hidden('employee_id');
?>
<div class="input-table">
<table>
	<tr>
		<td style="width: 100px;">検索年月</td>
		<td style="width: 180px;">
			<?php
	                       $monthNames = ['01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', '11' => '11' , 12 => '12'];
	                       $this->Form->templates([
				       'dateWidget' => '{{year}}-{{month}}'
			       ]);
		               echo $this->Form->input("FoodHistoryInfo[card_recept_time]",
				       array('type' => 'date',
				       'label' => '',
				       'data-cy' => 'food-history-infos-date',
				       'dateFormat' => 'YM',
				       'maxYear' => date('Y') + 1,
				       'minYear' => date('Y') - 2,
				       'monthNames' => $monthNames,
				       'year' => [
					       'id' => 'FoodHistoryInfoCardReceptTimeYear'
				       ],
				       'month' => [
					       'id' => 'FoodHistoryInfoCardReceptTimeYear'
					       ],
                                       )
			       ); 
                        ?>
		</td>
		<td style="width: 180px;">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="width: 100px;">社員コード</td>
		<td style="width: 180px;">
			<?php echo $this->Form->control("FoodHistoryInfo[employee_id]", array('type' => 'text', 'label' => '', 'maxlength' => 15, 'data-cy' => 'food-history-infos-emplyee-id')); ?>
		</td>
		<td style="width: 180px;"><?php echo $message['guidance01']; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td style="width: 100px;">氏名</td>
		<td style="width: 180px;">
			<?php echo $this->Form->control("FoodHistoryInfo[employee_name1]", array('type' => 'text', 'label' => '', 'maxlength' => 40, 'data-cy' => 'food-history-infos-emplyee-name')); ?>
		</td>
		<td style="width: 180px;"><?php echo $message['guidance02']; ?></td>
		<td>
			<input type ="checkbox" id ="dining_license_flg" name="dining_license_flg" >
			&nbsp;終了（✔が入力された時は、終了も含んで表示）
		</td>
	</tr>
</table>
</div>
<?php
        //echo $this->Form->end("検索");
        echo $this->Form->submit('検索', ['label' => false]);
	echo $this->Form->end();
?>
