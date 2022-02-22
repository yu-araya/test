<?php
// 初期化
$this->Csv->clear();
// ファイル名設定
$this->Csv->setFilename($fileName);

$headerList = array('No', '社員区分名', '社員コード', '氏名');
if($exportMenu){
    foreach ($foodDivisionList as $key => $value) {
        $name = $foodDivisionToBase[$key].':'.$value;
        array_push($headerList, $name.'（数量計）');
        array_push($headerList, $name.'（金額計）');
    }
}

array_push($headerList, '実績数量計');
array_push($headerList, '実績金額計');
array_push($headerList, '予約数量計');
array_push($headerList, '予約金額計');
array_push($headerList, '総数量計');
array_push($headerList, '総金額計');

// ヘッダー行追加
$this->Csv->addRow($headerList);

foreach ($dataList as $key => $data) {
    $record = $data;
    $row = array(
        $key + 1,
        $record['employee_kbn_name'],
        $record['employee_id'],
        $record['employee_name1'],
    );

    $totalCount = 0;
    $totalCost = 0;
    $reserveCount = 0;
    $reserveCost = 0;
    $count = count($foodDivisionList);
    $i = 1;
    foreach ($foodDivisionList as $key => $value) {
        if($exportMenu){
            $row[] = $record['food_count'.($i)];
            $row[] = $record['food_cost'.($i)];
        }
        // 予約と実績で集計を分ける(Model諸々直すのが苦痛のためValueで比較)
        if (in_array($value, $reservationDivisionList) > 0) {
            $reserveCount += $record['food_count'.($i)];
            $reserveCost  += $record['food_cost'.($i)];
        } else {
            $totalCount += $record['food_count'.($i)];
            $totalCost  += $record['food_cost'.($i)];
        }
        $i++;
    }
    $row[] = $totalCount;
    $row[] = $totalCost;
    $row[] = $reserveCount;
    $row[] = $reserveCost;
    $row[] = $totalCount + $reserveCount;
    $row[] = $totalCost + $reserveCost;

    // データ行追加
    $this->Csv->addRow($row);
}

// CSVファイルダウンロード出力
echo $this->Csv->render(true, 'sjis-win', 'utf-8');
