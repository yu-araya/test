<?php
// 初期化
$this->Csv->clear();
//ファイル名設定
$this->Csv->setFilename($fileName);

//ヘッダー行追加
$this->Csv->addRow(array(
  'No',
  '社員区分名',
  '社員コード',
  '氏名',
  'ICカード番号',
  '食事名',
  '備考',
  'カード受付時間',
  '状態',
));

foreach ($dataList as $key => $record)
{
  $row = array(
    $key + 1,
    $record['employee_kbn_name'],
    $record['employee_id'],
    $record['employee_name1'],
    empty($record['ic_card_number']) ? $record['employee_ic_card_number'] : $record['ic_card_number'],
    $record['food_division_name'],
    $record['reason'],
    $record['target_date'],
    $record['state_flg'] == 2 ? '削除' : '',
  );
  //データ行追加
  $this->Csv->addRow($row);
}

//CSVファイルダウンロード出力
echo $this->Csv->render(true, 'sjis-win', 'utf-8');
?>
