<div style="float:left;">
<?php
echo $this->Paginator->counter('検索結果：{{count}}件  '.'全{{pages}}ページ  ');
?>
<div class="paging">
<?php
echo $this->Paginator->first('<< 先頭', array(), null, array('class' => 'first disabled'));
echo $this->Paginator->prev('< 前', array(), null, array('class' => 'prev disabled'));
echo $this->Paginator->numbers(array('separator' => ''));
echo $this->Paginator->next('次 >', array(), null, array('class' => 'next disabled'));
echo $this->Paginator->last('最後 >>', array(), null, array('class' => 'last disabled'));
?>
</div>
<table class="detail-table">
<thead>
<?php
$headerList = array('社員区分', '社員コード', '氏名');

$headerList = array_merge($headerList, $baseHeaderList);

array_push($headerList, '終了');
array_push($headerList, '処理');
// 出力
echo $this->Html->tableHeaders($headerList);
?>
</thead>
<tbody>
<?php
foreach($dataList as $record){
	$dining_license_flg = $record['dining_license_flg'];

	if(empty($record['employee_id'])){
		$employee_id = "NONE";
	}else{
		$employee_id = $record['employee_id'];
	}
?>	
	<tr data-cy="food-history-info-<?php echo $employee_id?>">
		<td data-cy="employee-kbn"><?php echo strval($record['employee_kbn_name']); ?></td>
		<td class="employee_id" data-cy="employee-id"><?php echo strval($record['employee_id']); ?></td>
		<td class="employee_name" data-cy="employee-name1"><?php echo strval($record['employee_name1']); ?></td>
		<?php foreach($baseHeaderList as $key => $name) : ?>
		<td class="number" data-cy="basekbn-<?php echo $key ?>"><?php echo number_format($record['sum_food_division'.($key + 1)]); ?></td>
		<?php endforeach ?>
		<td class="number" >
			<?php 
			    if(isset($dining_license_flg) && strcmp($dining_license_flg, '1') == 0){
					echo '終了'; 
				}
			?>
		</td>
		<td class="function">
			<?php
				echo $this->Html->link('詳細',
					array('controller' => 'food_history_infos',
						'action' => $funcName,
						$yyyymm['year'].$yyyymm['month'],
						$employee_id
					),
				    array('data-cy'=> 'detail-'.$employee_id)
				);
			?>
		</td>
	</tr>
<?php
}
?>
</tbody>
</table>
</div>
