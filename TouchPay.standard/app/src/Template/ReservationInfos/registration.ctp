<ul id="breadcrumbs">
    <li><a href="<?php
                   echo $this->Url->build(['controller' => 'reservation-infos', 'action' => 'registration']);
                ?>"
            style="text-decoration: none" target="_self">
            予約一括登録
        </a>
    </li>
</ul>

<br>
<br>
予約情報を登録できます<br>
テンプレートをダウンロードし、任意の日付に変更してアップロードして下さい。

<div class="bulk-insert-area">
    <div class="process_box bulk">
        <span class="title">予約一括登録</span>
        <?php
            echo $this->Form->create('ReservationInfo', array('url' => ['controller' => 'reservation-infos', 'action' => 'uploadWeekData'], 'type' => 'file', 'data-cy' => 'uploadFile'));
        ?>
        <div class="comment">
            アップロードを行うファイルを選択してください（<?php echo $this->Html->link('テンプレート', '/webroot/files/week_reservation_sample.xlsx'); ?>）
        </div>
        <?php
            echo $this->Form->hidden("ReservationInfo[nextSunday]", ['value' => $nextSunday]);
            echo $this->Form->file("ReservationInfo[result]", array('data-cy' => 'selectFile'));
            //echo $this->Form->end('アップロード');
	    echo $this->Form->submit('アップロード', ['label' => false]);
            echo $this->Form->end();
        ?>
    </div>

    <div class="food-division">
        <span>予約食事区分</span>
        <table class="detail-table">
            <thead>
                <tr>
                    <th>区分</th>
                    <th>事業所</th>
                    <th>区分名</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($reserveFoodDivisionList as $value) {
                        $record = $value['FoodDivision'];
                        $key = $record['food_division'];
                        $export = '<tr><td data-cy = "reserveFoodDivisionKey' . $key . '">'.$key.'</td>';
                        $export .= '<td class="instrument_division" data-cy = "reserveFoodDivisionBase' . $key . '">'.$record['FoodDivision']['instrument_name'].'</td>';
                        $export .= '<td class="food_division" data-cy = "reserveFoodDivisionValue' . $key . '">'.$record['food_division_name'].'</td></tr>';
                        echo $export;
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<br>
<br>

<span class="next_week_reservation_span" data-cy="reservationStatus"><?php echo date('Y年n月j日', strtotime($nextSunday)); ?>週の予約状況</span>
<?php
if(0 < count($dataList)){
	// 登録データ一覧テーブル配置
	echo $this->element('reservation_infos_next_week_list',
		array('funcName' => '/reservation-infos/update', 'dataList' => $dataList));
}else{
	echo '<div style="text-align:center"><font size 3>登録内容はありません</font></div>';
}
?>
