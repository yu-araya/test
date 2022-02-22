<?php echo $this->Html->script(array('/webroot/js/reservation_infos.js'), array('inline' => false)); ?>

<ul id="breadcrumbs">
	<li>
		<a href="<?php echo $this->Url->build('/reservation-infos/index'.'/'.$baseKbn.'/'.substr($yyyymmdd, 0, 6), false); ?>" style="text-decoration: none" target="_self">
			予約状況照会
		</a>
	</li>
	<li><a>詳細</a></li>
</ul>
<br>
<div class="search-condition-area">
	<table>
		<tr>
			<td>
				事業所
			</td>
			<td>
				<p><?php echo $baseKbnList[$baseKbn]; ?></p>
			</td>
		</tr>
		<tr>
			<td>
					対象年月日
			</td>
			<td>
				<p>
					<?php echo substr($yyyymmdd, 0, 4); ?>年<?php echo substr($yyyymmdd, 4, 2); ?>月<?php echo substr($yyyymmdd, 6, 2); ?>日
					<?php
						if (!empty($reservationDecision)) {
							echo '<div class="decision"><div>注文確定済</div></div>';
						}
					?>
				</p>
			</td>
		</tr>
	</table>
</div>
<br>

	<?php
		$count = 0;
		foreach ($dataList as $data) {
			if ($data['state_flg'] != '2') {
				$count++;
			}
		}
	?>

<?php
	if(0 < count($dataList)){
		// 登録データ一覧テーブル配置
?>
			<B>合計：<?php echo number_format($count); ?>個</B>
			<input type="hidden" id="count_order" value="<?php echo $count; ?>">
<?php
		echo $this->element('reservation_infos_detail_form',
			array('funcName' => 'update', 'dataList' => $dataList));
	} else {
		echo '<div class="no-data">登録内容はありません</div>';
		echo '<br>';
	}
?>

<div class="related"></div>
<div class="process_box reservation_add">
	<span class="title" data-cy="caretOut">新規登録</span>
	<?php
		echo $this->Form->create('ReservationInfo', array('url' => ['controller' => 'reservation-infos', 'action' => 'insert'], 'data-cy' => 'addReservation'));
	?>
	<div class="input-table add-new-reservation-area">
	<table>
	    <tr>
			<td class="width_90">社員コード</td>
			<td class="width_100">
				<?php
					echo $this->Form->control("ReservationInfo[employee_id]",
						array('type' => 'text',
							'id' => 'employee_id',
							'class' => 'searched-employee-id-input',
							'label' => false,
							'autocomplete' => 'off',
							'maxlength' => $table->column('employee_id')['length'],
							'size' => 12,
							'data-cy' => 'inputEmployeeId',
							'placeholder' => '社員コードを入力してください'
						));
				?>
			</td>
			<td><button id="employee-id-search-button" class="employee-id-search-button" type="button" data-cy="employeeSearch">氏名検索</button>
			<?php
				// 社員検索
				echo $this->element('search_employee');
			?>
			</td>
		</tr>
		<tr>
			<td>社員名</td>
			<td>
				<?php
					echo $this->Form->control("ReservationInfo[employee_name]",
						array('type' => 'text',
							'id' => 'employee_name',
							'label' => false,
							'readonly' => true,
							'tabindex' => -1,
							'class' => 'readonly_input',
							'data-cy' => 'displayEmployeeName',
						));
				?>
			</td>
		</tr>
			<tr>
			<td class="width_90">メニュー</td>
			<td colspan="2">
				<?php
					$food_division = '1';
					if (isset($this->request->data['ReservationInfo']['food_division'])) {
						$food_division = $this->request->data['ReservationInfo']['food_division'];
					}

					echo $this->Form->control("ReservationInfo[food_division]",
						array('options' => $foodDivisionReservationList,
							'label' => false,
							'data-cy' => 'menu',
						        'selected' => $food_division,
                                                        'id' => 'ReservationInfoFoodDivision',
                                                        'value' => $food_division
						));
				?>
			</td>
		</tr>
	    <tr>
			<td class="width_90">備考</td>
			<td colspan="2">
				<?php
					if (isset($this->request->data['ReservationInfo']['reason'])) {
                                                $reason = $this->request->data['ReservationInfo']['reason'];
                                        }
					echo $this->Form->control("ReservationInfo[reason]",
						array('type' => 'text',
							'label' => '',
							'autocomplete' => 'off',
							'maxlength' => $table->column('reason')['length'],
							'class' => 'input_reason',
							'data-cy' => 'addMemo',
							'id' => "ReservationInfoReason",
                                                        'value' => $reason
						));
				?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<?php
					echo $this->Form->hidden("ReservationInfo[base_kbn]", array('value' => $baseKbn));
					echo $this->Form->hidden("ReservationInfo[reservation_date]", array('value' => substr($yyyymmdd, 0, 4).'-'.substr($yyyymmdd, 4, 2).'-'.substr($yyyymmdd, 6, 2)));
					echo $this->Form->submit('登録', ['label' => false]);
					echo $this->Form->end();
				?>
			</td>
		</tr>
	</table>
	</div>
</div>
<br>
