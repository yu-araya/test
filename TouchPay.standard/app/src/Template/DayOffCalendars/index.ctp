<?php echo $this->Html->script(array('day_of_calendars'), array('inline' => false)); ?>

<ul id="breadcrumbs">
	<li><a>カレンダーメンテナンス</a></li>
</ul>
<br>
<?php
	echo $this->Form->create('DayOffCalendar', array('url' => ['controller' => 'day-off-calendars', 'action' => 'index'], 'id' => 'DayOffCalendarIndexForm'));
?>
<div class="input-table">
<table border="0">
	<tr>
		<td style="width: 100px;">事業所</td>
		<td>
			<?php
				echo $this->Form->control("DayOffCalendar[base_kbn]",
					array('options' => $baseKbnList,
						'label' => false,
						'onchange' => 'this.form.submit();',
						'id' => 'DayOffCalendarBaseKbn',
						'value' => $baseKbn
					));
			?>
		</td>
	</tr>
	<tr>
		<td style="width: 100px;">対象年月</td>
		<td>
			<?php
                                $monthNames = ['01' => '01', '02' => '02', '03' => '03', '04' => '04', '05' => '05', '06' => '06', '07' => '07', '08' => '08', '09' => '09', 10 => '10', '11' => '11' , 12 => '12'];
                                $this->Form->templates([
                                       'dateWidget' => '{{year}}-{{month}}'
                                ]);
				echo $this->Form->input("DayOffCalendar[target_date]",
					array(
						'type' => 'date',
						'label' => '',
						'dateFormat' => 'YM',
						'maxYear' => date('Y') + 2,
						'minYear' => date('Y') - 2,
						'monthNames' => $monthNames,
						'year' => [
						     'data-cy' => 'calendars-date',
                                                     'onchange' => 'this.form.submit();',
						     'id' => 'DayOffCalendarTargetDateYear'
                                                ],
                                                'month' => [
						     'data-cy' => 'calendars-date',
                                                     'onchange' => 'this.form.submit();',
						     'id' => 'DayOffCalendarTargetDateMonth'
                                                ],
						'value' => $yyyymm['year'].'-'.$yyyymm['month'].'-01',
						'data-cy' => 'calendars-date'
					));
			?>
		</td>
	</tr>
</table>
</div>
<?php
	echo $this->Form->end();
?>
<br>

<?php
	echo $this->Form->create('DayOffCalendar', array('id' => 'dayOffUpdateForm', 'url' => ['controller' => 'day-off-calendars', 'action' => 'update']));
?>
<table class="calendar day-off-calendar base<?php echo $baseKbn; ?>">
	<tr>
		<th>日</th>
		<th>月</th>
		<th>火</th>
		<th>水</th>
		<th>木</th>
		<th>金</th>
		<th>土</th>
	</tr>
	<?php
		$sysYmd = date('Y-m-d');
		$count = 0;
		echo '<tr>';
		foreach ($calendar as $key => $value) {

			if (empty($value['day'])) {
				echo '<td>';
			} else {
				$date = $yyyymm['year'].'-'.$yyyymm['month'].'-'.sprintf('%02d', $value['day']);
				$class = $value['day_off'] == '1' ? 'day_off' : '';

				if ($date == $sysYmd) {
					$class .= ' today';
				}
				echo '<td class="'. $class .'">';

				if ($value['day_off'] == '1') {
					echo '<a href="javascript:void(0)" onclick="changeWeekday(\''. $date .'\') " ' . 'data-cy="date' . $value['day'] .'">'. $value['day'] .'</a>';
				} else {
					echo '<a href="javascript:void(0)" onclick="changeDayOff(\''. $date .'\') " ' . 'data-cy="date' . $value['day'] .'">'. $value['day'] .'</a>';
				}
			}

			echo '</td>';
			$count++;

			if ($count == 7) {
				echo '</tr>';
				echo '<tr>';
				$count = 0;
			}
		}
		echo '</tr>';
	?>
</table>
<div class="table_comment"><span class="color_red">赤色</span>は食事の予約が出来ません。数字を押下すると変更ができます。</div>
<input type="hidden" id="base_kbn" name="base_kbn">
<input type="hidden" id="day_off_datetime" name="day_off_datetime">
<input type="hidden" id="day_off_flag" name="day_off_flag">
<?php
	echo $this->Form->end();
?>

<div class="process_box bulk">
	<span class="title">カレンダー一括登録</span>
	<div class="comment">アップロードを行うファイルを選択してください（<?php echo $this->Html->link('サンプルファイル', '/webroot/files/calendar_upload.csv'); ?>）</div>
	<?php
		echo $this->Form->create('DayOffCalendar', array('url' => ['controller' => 'day-off-calendars', 'action' => 'uploadDayOffCalendar'], 'type' => 'file', 'data-cy' => 'uploadFile'));
		echo $this->Form->file("DayOffCalendar[result]", array('data-cy' => 'selectFile'));
		echo $this->Form->hidden("DayOffCalendar[base_kbn]", array('value' => $baseKbn));
		echo $this->Form->hidden("DayOffCalendar[target_date]", array('value' => $yyyymm['year'].$yyyymm['month']));
		//echo $this->Form->end('アップロード');
		echo $this->Form->submit('アップロード', ['label' => false]);
		echo $this->Form->end();
	?>
</div>
