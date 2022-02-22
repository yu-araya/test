<div class="paging">
<?php
echo $this->Paginator->first('<< 先頭', array(), null, array('class' => 'first disabled'));
echo $this->Paginator->prev('< 前', array(), null, array('class' => 'prev disabled'));
echo $this->Paginator->numbers(array('separator' => ''));
echo $this->Paginator->next('次 >', array(), null, array('class' => 'next disabled'));
echo $this->Paginator->last('最後 >>', array(), null, array('class' => 'last disabled'));
?>
</div>
<table class="regist_error_list">
<thead>
	<tr>
		<th>発生日時</th>
		<th>エラーレベル</th>
		<th>エラー発生機能</th>
		<th>エラー内容</th>
		<th>社員番号</th>
		<th>ICカード番号</th>
		<th>機器番号</th>
		<th>メニュー名</th>
		<th>カードタッチ日時</th>
	</tr>
</thead>
<tbody>
<?php
foreach($registError as $record){
?>	
	<tr>
		<td><?php echo strval($this->Date->formatDatetime($record['occurrence_datetime'])); ?></td>
		<td><?php echo strval($record['error_level']); ?></td>
		<td><?php echo strval($record['function_name']); ?></td>
		<td><?php echo strval($record['reason']); ?></td>
		<td><?php echo strval($record['employee_id']); ?></td>
		<td><?php echo strval($record['ic_card_number']); ?></td>
		<td><?php echo strval($record['instrument_division']); ?></td>
		<td><?php echo strval($foodDivision[$record['food_division']]['FoodDivision']['food_division_name']); ?></td>
		<td><?php echo strval($this->Date->formatDatetime($record['card_recept_time'])); ?></td>
	</tr>
<?php
}
?>
</tbody>
</table>
