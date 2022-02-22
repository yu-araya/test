<?php
//ファイル名設定

//曜日テーブル
$weekArray = array(0 => '日', 1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土');

//出力
$headerList = array('日', '曜日');

foreach ($foodDivisionList as $foodDivision) {
	array_push($headerList, $foodDivision);
}
array_push($headerList, '小計');

$this->Csv->clear();
$csvDataList[] = $headerList;
$this->Csv->addRow($headerList);

$total = 0;
foreach($dataList as $record){
	//曜日名の取得
	$year = substr($record['card_recept_time'], 0, 4);
	$month = substr($record['card_recept_time'], 5, 2);
	$day = substr($record['card_recept_time'], 8, 2);
	$youbi = $weekArray[date("w", mktime(0, 0, 0, $month, $day, $year))];
	$csvRow = array(
		$day,
		$youbi,
	);
	foreach ($foodDivisionList as $key => $foodDivision) {
		$csvRow[] = (number_format($record['count'.$key]));
	}
	$cost = $record['cost'];
	$total += $cost;
	$csvRow[] = number_format($cost);
	$this->Csv->addRow($csvRow);
}
//var_dump($this->Csv);
//exit();
$totalRow = ['合計', ''];

foreach ($foodDivisionList as $key => $foodDivision) {
	$count = 0;
	foreach($dataList as $record){
		$count += $record['count'.$key];
	}
	$totalRow[] = $count;
}
$totalRow[] = number_format($total);
$this->Csv->addRow($totalRow);

$this->Csv->setFilename($year .'年' .$month. '月度食堂生産集計.csv');

echo $this->Csv->render(true, 'sjis-win', 'utf-8');
?>
