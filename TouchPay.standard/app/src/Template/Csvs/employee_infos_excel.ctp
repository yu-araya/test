<?php
// 新規ファイル作成準備
$excel = PHPExcel_IOFactory::load(dirname(__FILE__)."/template.xlsx");
$sheet = $excel->getActiveSheet();
$sheet->setTitle('EmployeeInfo');

// データの設定

foreach ($dataList as $num => $data) {
    $record = $data['EmployeeInfo'];
    $num = $num + 2;
    $sheet->setCellValueByColumnAndRow(strval(0), strval($num), $record['employee_kbn']);
    $sheet->setCellValueByColumnAndRow(strval(1), strval($num), $record['employee_id']);
    $sheet->setCellValueByColumnAndRow(strval(2), strval($num), $record['employee_name1']);
    $sheet->setCellValueByColumnAndRow(strval(3), strval($num), $record['employee_name2']);
    $sheet->setCellValueByColumnAndRow(strval(4), strval($num), $record['ic_card_number']);
    $sheet->setCellValueByColumnAndRow(strval(5), strval($num), $record['iccard_valid_s_time']?substr($record['iccard_valid_s_time'], 0, 10):null);
    $sheet->setCellValueByColumnAndRow(strval(6), strval($num), $record['iccard_valid_e_time']?substr($record['iccard_valid_e_time'], 0, 10):null);
    $sheet->setCellValueByColumnAndRow(strval(7), strval($num), $record['ic_card_number2']);
    $sheet->setCellValueByColumnAndRow(strval(8), strval($num), $record['iccard_valid_s_time2']?substr($record['iccard_valid_s_time2'], 0, 10):null);
    $sheet->setCellValueByColumnAndRow(strval(9), strval($num), $record['iccard_valid_e_time2']?substr($record['iccard_valid_e_time2'], 0, 10):null);
    $sheet->setCellValueByColumnAndRow(strval(10), strval($num), $record['delete_flg']);
}
// Excelファイルの出力準備
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename*=UTF-8\'\'' . rawurlencode($excelName.".xlsx"));

PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
// Excelファイルの出力（ダウンロード）
$writer = PHPExcel_IOFactory::createWriter($excel, "Excel2007");
$writer->save('php://output');

exit;
