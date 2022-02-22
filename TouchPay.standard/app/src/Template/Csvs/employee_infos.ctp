<?php
// 初期化
$this->Csv->clear();
//ファイル名設定
$this->Csv->setFilename($fileName);

//ヘッダー行追加
$this->Csv->addRow(array(
    '社員区分',
    '社員コード',
    '氏名',
    '所属',
    'ICカード番号',
    '有効期間（開始）',
    '有効期間（終了）',
    'ICカード番号２',
    '有効期間（開始）２',
    '有効期間（終了）２',
    '削除フラグ'
));

foreach ($dataList as $data) {
    $record = $data['EmployeeInfo'];

    $row = array(
        $record['employee_kbn'],
        $record['employee_id'],
        $record['employee_name1'],
        $record['employee_name2'],
        $record['ic_card_number'],
        substr($record['iccard_valid_s_time'], 0, 10),
        substr($record['iccard_valid_e_time'], 0, 10),
        $record['ic_card_number2'],
        substr($record['iccard_valid_s_time2'], 0, 10),
        substr($record['iccard_valid_e_time2'], 0, 10),
        $record['delete_flg']
    );
    //データ行追加
    $this->Csv->addRow($row);
}

//CSVファイルダウンロード出力
echo $this->Csv->render(true, 'sjis-win', 'utf-8');
