<div style="float:left;">
<?php
echo $this->Paginator->counter('検索結果：{{count}}件  '.'全{{pages}}ページ  ');
?>
&emsp;&emsp;<?php echo $message['guidance03']; ?>
<div class="paging">
<?php
echo $this->Paginator->first('<< 先頭', array(), null, array('class' => 'first disabled'));
echo $this->Paginator->prev('< 前', array(), null, array('class' => 'prev disabled'));
echo $this->Paginator->numbers(array('separator' => ''));
echo $this->Paginator->next('次 >', array(), null, array('class' => 'next disabled'));
echo $this->Paginator->last('最後 >>', array(), null, array('class' => 'last disabled'));
?>
</div>
<table class="detail-table employee-list-table">
<thead>
<?php
// 出力
echo $this->Html->tableHeaders(array('社員区分', '社員コード', '氏名', '処理'));
?>
</thead>
<tbody>
<?php
foreach($dataList as $record){
	if($record['delete_flg'] != '0'){
		$tr_class = "delete";
	}else{
		$tr_class = '';
	}
?>
	<tr class="<?php echo $tr_class; ?>">
		<td><?php echo strval($record['employee_kbn_name']); ?></td>
		<td data-cy = "employeeId<?php echo $record['employee_id'] ?>"><?php echo strval($record['employee_id']); ?></td>
		<td class="employee_name" data-cy = "employeeName<?php echo $record['employee_id'] ?>"><?php echo strval($record['employee_name1']); ?></td>
		<td data-cy = "employeeDetail<?php echo $record['employee_id'] ?>">
			<?php
				echo $this->Html->link('詳細',
					array('controller' => 'employee_infos',
						'action' => $funcName,
						$record['id']
					));
			?>
		</td>
	</tr>
<?php
}
?>
</tbody>
</table>
</div>
