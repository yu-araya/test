<?php
App::import('Vendor','TCPDF/tcpdf');

define('FONT_FAMILY', 'kozminproregular'); //kozminproregular kozgopromedium
define('MARGIN_LEFT', 35);
define('LINE_WEIGHT_DEFAULT', 0.2);

// 可変項目を配列に設定して作成
$dataList = array();
$dataList[] = array('to_name' => '三福', 'food_count' => $corp1_count, 'small_count' => $corp1_sub_count, 'small_flag' => true);
$dataList[] = array('to_name' => '㈱エッセン', 'food_count' => $corp2_count, 'small_count' => '', 'small_flag' => false);

//出力ファイル名
$fileName = mb_convert_encoding($fileName, "SJIS-win", "utf-8");

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// 喫食者リスト =========================================================================================
// ページを追加
$pdf->AddPage();

$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont(FONT_FAMILY, 'B', 16);
$pdf->Cell(0, 15, '【 配達者 様　　配達欄にチェックをお願い致します。】', 0, 1, 'C', false, '', 0, false, 'T', 'T');

$pdf->SetFont(FONT_FAMILY, 'B', 11);
$pdf->Cell(114, 0);
$pdf->SetLineWidth(0.6);
$pdf->Cell(20, 10, '個数', 1, 0, 'C');
$pdf->Cell(27, 10, '配達チェック', 1, 0, 'C', true);
$pdf->SetLineWidth(LINE_WEIGHT_DEFAULT);
$pdf->SetFont(FONT_FAMILY, '', 11);
$pdf->Cell(27, 10, '受取チェック', 1, 1, 'C');

$pdf->SetFont(FONT_FAMILY, '', 14);
$pdf->Cell(30, 20, substr($reservation_date, 4, 2).'月'.intval(substr($reservation_date, 6, 2)).'日', 1, 0, 'C');
$pdf->SetFont(FONT_FAMILY, '', 11);

$pdf->Cell(28, 10, 'A弁当', 1, 0, 'R');
$pdf->SetFont(FONT_FAMILY, '', 14);
$pdf->Cell(56, 10, '【 '. $dataList[0]['to_name'] .'　様 】', 1, 0, 'R');
$pdf->SetLineWidth(0.6);
$pdf->SetFont(FONT_FAMILY, 'B', 14);
$pdf->Cell(20, 10, intval($dataList[0]['food_count']), 1, 0, 'C');
$pdf->Cell(27, 10, '', 1, 0, 'C', true);
$pdf->SetLineWidth(LINE_WEIGHT_DEFAULT);
$pdf->Cell(27, 10, '', 1, 1, 'C');

$pdf->SetFont(FONT_FAMILY, '', 11);
$pdf->Cell(30, 20);

$pdf->Cell(28, 10, 'B弁当', 1, 0, 'R');
$pdf->SetFont(FONT_FAMILY, '', 14);
$pdf->Cell(56, 10, '【 '. $dataList[1]['to_name'] .'　様 】', 1, 0, 'R');
$pdf->SetLineWidth(0.6);
$pdf->SetFont(FONT_FAMILY, 'B', 14);
$pdf->Cell(20, 10, intval($dataList[1]['food_count']), 1, 0, 'C');
$pdf->Cell(27, 10, '', 1, 0, 'C', true);
$pdf->SetLineWidth(LINE_WEIGHT_DEFAULT);
$pdf->Cell(27, 10, '', 1, 1, 'C');

$pdf->Cell(0, 5, '', 0, 1); // 行調整

$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont(FONT_FAMILY, '', 11);

$pdf->Cell(0, 10, '【注文者　チェック】', 0, 1);
$pdf->Cell(10, 5, '', 1);
$pdf->Cell(30, 5, '個人コード', 1, 0, 'C');
$pdf->Cell(55, 5, '氏名', 1, 0, 'C');
$pdf->Cell(74, 5, '備考', 1, 0, 'C');
$pdf->Cell(19, 5, 'チェック', 1, 1, 'C');

$i = 1;
foreach ($reservationInfoList as $reservationInfo) {
	$bgColor = false;
	if ($i % 2 == 0) {
		$bgColor = true;
	}

	$height = 10;
	$pdf->SetFont(FONT_FAMILY, '', 11);

	$pdf->Cell(10, $height, $i, 1, 0, 'C', $bgColor);
	$pdf->Cell(30, $height, $reservationInfo['ReservationInfo']['employee_id'], 1, 0, 'C', $bgColor);

	$name = $reservationInfo['ReservationInfo']['employee_name1'];
	if (mb_strlen($name, 'UTF-8') > 10) {
		$pdf->SetFont(FONT_FAMILY, '', 10); // 文字数によってサイズを調整
	}
	$pdf->MultiCell(55, $height, $name, 1, 'L', $bgColor, 0, '', '', true, 0, false, true, $height, 'M');

	$reason = $reservationInfo['ReservationInfo']['reason'];
	if (mb_strlen($reason, 'UTF-8') > 40) {
		$pdf->SetFont(FONT_FAMILY, '', 8); // 文字数によってサイズを調整
	} else if (mb_strlen($reason, 'UTF-8') > 18) {
		$pdf->SetFont(FONT_FAMILY, '', 10); // 文字数によってサイズを調整
	}
	$pdf->MultiCell(74, $height, $reason, 1, 'L', $bgColor, 0, '', '', true, 0, false, true, $height, 'M');
	$pdf->Cell(19, $height, '', 1, 1, 'C', $bgColor);

	$i++;
}

// FAX注文書 =========================================================================================
foreach ($dataList as $data) {
	// ページを追加
	$pdf->AddPage();

	$pdf->Cell(0, 5, '', 0, 1); // 行調整

	$pdf->SetFillColor(230, 230, 230);
	$pdf->SetFont(FONT_FAMILY, 'B', 24);
	$pdf->Cell(5);
	$pdf->Cell(50, 12, 'FAX注文書', 0, 0, 'C', true);

	$pdf->SetFont(FONT_FAMILY, '', 11);
	$pdf->Cell(130, 0, '送付年月日　'.date('Y/m/d'), 0, 1, 'R');
	$pdf->Cell(185, 8, '送付枚数　1枚', 0, 0, 'R');

	$pdf->Cell(0, 35, '', 0, 1); // 行調整

	$pdf->Cell(MARGIN_LEFT);
	$pdf->SetFont(FONT_FAMILY, '', 12);
	$pdf->Cell(25, 12, '送付先：', 'B');
	$pdf->SetFont(FONT_FAMILY, '', 16);
	$pdf->Cell(95, 12, $data['to_name'].'　御中', 'B', 0);

	$pdf->Cell(0, 15, '', 0, 1); // 行調整

	$pdf->Cell(MARGIN_LEFT);
	$pdf->SetFont(FONT_FAMILY, '', 12);
	$pdf->Cell(25, 12, '用件：', 'B');
	$pdf->SetFont(FONT_FAMILY, '', 16);
	$pdf->Cell(95, 12, '弁当発注の件', 'B', 1);

	$pdf->Cell(0, 15, '', 0, 1); // 行調整

	$pdf->SetFont(FONT_FAMILY, '', 12);
	$pdf->Cell(MARGIN_LEFT);
	$pdf->Cell(0, 8, 'いつもお世話になっております。上記の件につきまして、', 0, 1);
	$pdf->Cell(MARGIN_LEFT);
	$pdf->Cell(0, 8, 'FAXさせて頂きます。よろしくお願い致します。', 0, 1);

	$pdf->Cell(0, 15, '', 0, 1); // 行調整

	$pdf->SetFont(FONT_FAMILY, '', 14);
	$pdf->Cell(MARGIN_LEFT);
	$pdf->Cell(40, 12, substr($reservation_date, 4, 2).'月'.intval(substr($reservation_date, 6, 2)).'日', 1, 0, 'C');
	$pdf->SetFont(FONT_FAMILY, '', 18);
	$pdf->Cell(40, 12, intval($data['food_count']), 'LTB', 0, 'R');
	$pdf->SetFont(FONT_FAMILY, '', 14);
	$pdf->Cell(15, 12, '個　', 'RTB', !$data['small_flag'], 'R');

	if ($data['small_flag'] == true) {
		$pdf->SetFont(FONT_FAMILY, 'B', 12);
		$pdf->Cell(20, 12, ' 内 ご飯小 ', 0, 0);
		$pdf->SetFont(FONT_FAMILY, 'B', 16);
		$pdf->Cell(0, 12, intval($data['small_count']), 0, 1);

		$pdf->SetFont(FONT_FAMILY, '', 14);
		$pdf->Cell(MARGIN_LEFT);
		$pdf->Cell(40, 12, '配達チェック', 1, 0, 'C', true);
		$pdf->Cell(55, 12, '', 1, 1, 'R', true);

		$pdf->Cell(MARGIN_LEFT);
		$pdf->Cell(40, 12, '受取チェック', 1, 0, 'C');
		$pdf->Cell(55, 12, '', 1, 1, 'R');
	}

	$pdf->Cell(0, 20, '', 0, 1); // 行調整

	$pdf->SetFont(FONT_FAMILY, '', 12);
	$pdf->Cell(MARGIN_LEFT);
	$pdf->Cell(0, 0, '発信元：', 0, 1);

	$pdf->SetFont(FONT_FAMILY, '', 16);
	$pdf->Cell(MARGIN_LEFT);
	$pdf->Cell(0, 15, $corp_name, 0, 1);

	$pdf->SetFont(FONT_FAMILY, '', 12);
	$pdf->Cell(MARGIN_LEFT);
	$pdf->Cell(120, 0, $address, 0, 1, 'R');
	$pdf->Cell(34.5);
	$pdf->SetFont(FONT_FAMILY, '', 11);
	$pdf->Cell(118, 8, 'TEL '.$tel.' (ﾀﾞｲﾚｸﾄ)　FAX '.$fax, 0, 1, 'R');

	$pdf->Cell(0, 5, '', 0, 1); // 行調整

	$pdf->SetFont(FONT_FAMILY, '', 12);
	$pdf->Cell(50);
	$pdf->Cell(25, 10, '担当者名', 'B');
	$pdf->Cell(80, 10, $staff, 'B', 1);
}

//ファイル出力
$pdf->Output($fileName.'.pdf', 'D');
?>
