<?php echo $this->Html->script(array('/webroot/js/reservation_infos.js'), array('inline' => false)); ?>

<ul id="breadcrumbs">
	<li><a>予約状況照会</a></li>
</ul>
<br>
<?php
	echo $this->Form->create(null, array('type' => 'post', 'url' => array('action' => 'index')));
?>
<div class="input-table">
<table border="0">
	<tr>
		<td style="width: 100px;">事業所</td>
		<td>
			<?php echo '<div data-cy="baseKbn">' . $baseKbnList[$baseKbn] . $this->Form->hidden('ReservationInfo[base_kbn]', array('value' => $baseKbn)) . '</div>'; ?>
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
				echo $this->Form->input("ReservationInfo[target_date]",
					array(
						'type' => 'date',
						'label' => '',
						'dateFormat' => 'YM',
						'maxYear' => date('Y') + 2,
						'minYear' => date('Y') - 2,
						'monthNames' => $monthNames,
						'year' => [
						    'onchange' => 'changeTargetDate(this.form);',
						    'id' => 'ReservationInfoTargetDateYear'
						],
						'month' => [
						    'onchange' => 'changeTargetDate(this.form);',
						    'id' => 'ReservationInfoTargetDateMonth'
						],
						'day' => false,
						'value' => $yyyymm['year'].'-'.$yyyymm['month'].'-01'
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

<table class="calendar base<?php echo $baseKbn; ?>">
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
				echo '<div class="date_box">';
				echo '<span class="date" data-cy="date' . $value['day'] . '">'. $value['day'] .'</span>';

				if ($value['day_off'] == '1') {
					echo '<span class="count">'. $value['reservation_count'] .'</span>';
				} else {
					echo '<a href="'. $this->Url->build('/reservation-infos/detail/'.$baseKbn.'/'.str_replace('-', '', $date), false) .'" class="count" '. 'test-	=day-' . sprintf('%02d', $value['day']) . ' data-cy="count' . $value['day'] .'">'. $value['reservation_count'] .'</a>';

					if ($baseKbn == '1' && $value['decision_flag'] == '1') {
						echo '<div class="decision">確</div>';
					}
				}

				echo '</div>';
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
<div class="table_comment">大きい数字は、予約数です。数字を押下すると詳細へ画面遷移します。</div>

<br>
<?php
	$fileName = $yyyymm['year'].$yyyymm['month'].'.pdf';

	if (file_exists('../webroot/menu/'. $fileName)) {
		$url = $this->Url->build('/menu/'. $fileName, true);
		echo $this->Html->link(intval($yyyymm['month']). '月の献立表', $url, array('target' => '_blank', 'data-cy' => 'menuLink'));
	} else {
		echo '<div class="color_red">'. intval($yyyymm['month']) .'月の献立表はアップロードされていません。</div>';
	}
?>

<div class="process_box menu_upload">
	<span class="title">献立表アップロード</span>
	<div class="comment">アップロードを行うファイルを選択してください</div></td>
	<?php
		echo $this->Form->create('ReservationInfo', array('url' => ['controller' => 'reservation-infos', 'action' => 'uploadMenu'], 'type' => 'file', 'data-cy' => 'uploadFile'));
		echo $this->Form->file("result[select_file]", array( 'data-cy' => 'selectFile')); 
		echo $this->Form->hidden("result[base_kbn]", array('value' => $baseKbn));
		echo $this->Form->hidden("result[target_date]", array('value' => $yyyymm['year'].$yyyymm['month']));
		//echo $this->Form->end('アップロード');
		echo $this->Form->submit('アップロード', ['label' => false]);
		echo $this->Form->end();
	?>
</div>
