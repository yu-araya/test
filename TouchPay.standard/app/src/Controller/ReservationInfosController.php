<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\I18n\Time;

//use App\Controller\AppController;

class ReservationInfosController extends AppController
{
    public $uses = ['ReservationInfo', 'DayOffCalendar', 'EmployeeInfo', 'ReservationDecision', 'Administrator', 'EmployeeKbn', 'FoodDivision', 'FoodPeriod'];
    public $components = ['RequestHandler'];

    public function beforeFilter(Event $event)
    {
        $this->loadComponent('S3Client');
        parent::beforeFilter($event);
    }

    /**
     * 予約状況照会初期画面.
     */
    public function index($pBaseKbn = null, $pYm = null)
    {
        //POST+session利用時のブラウザバックでのキャッシュ有効期間切れ対応
        $this->response = $this->response->withCache('-1 minute', '+1 day');

        // パラメータが設定されている場合（更新後のリダイレクト等）はリクエスト情報を上書き設定
        if (!empty($pBaseKbn)) {
            $this->request->data = $this->_setParam($pBaseKbn, $pYm);
        }

        if (isset($this->request->data['ReservationInfo'])) {
            $baseKbn = $this->request->data['ReservationInfo']['base_kbn'];
            $yyyymm = $this->request->data['ReservationInfo']['target_date'];
        } else {
            $baseKbn = '';
            foreach ($this->getInstrumentDivisionList() as $key => $value) {
                $baseKbn = $key;
                break;
            }
            $yyyymm = [
                'year' => date('Y'),
                'month' => date('m'),
            ];
        }

        // S3からメニューを取得する
        $this->getFilesFromS3();

        $this->set('title_for_layout', '予約状況照会（'.$this->getInstrumentDivisionList()[$baseKbn].'）画面');
        $this->set('menuLink', $this->getMenuLink($baseKbn + AppController::MENU_BASE));

        // カレンダーを取得
        $calendar = $this->_makeCalendar($baseKbn, $yyyymm['year'], $yyyymm['month']);

        // ビューにセット
        $this->set('baseKbnList', $this->getInstrumentDivisionList());
        $this->set('baseKbn', $baseKbn);
        $this->set('yyyymm', $yyyymm);
        $this->set('calendar', $calendar);
    }

    public function detail($pBaseKbn, $pYmd)
    {
        $this->set('title_for_layout', '予約状況照会（'.$this->getInstrumentDivisionList()[$pBaseKbn].'）詳細画面');
        $this->set('menuLink', $this->getMenuLink($pBaseKbn + AppController::MENU_BASE));
        // パラメータが正しくない場合は前画面へ
        if (!array_key_exists($pBaseKbn, $this->getInstrumentDivisionList()) || strlen($pYmd) != 8) {
            return $this->redirect(['action' => 'index']);
        }

        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();

        // 予約可能食事区分の取得
        $foodDivisionArray = $this->getReservationFoodDivisionList($pBaseKbn);

        // 予約情報取得
        $dataList = $this->ReservationInfo->getReservationDetail($pYmd, $foodDivisionArray, $employeeKbnArray);

        // 確定情報取得
        $reservationDecision = [];
        if ($pBaseKbn == '1') {
            $reservationDecision = $this->ReservationDecision->findEX('all', [
                'fields' => ['reservation_date'],
                'conditions' => [
                    'DATE_FORMAT(reservation_date, \'%Y%m%d\') LIKE ' => $pYmd.'%',
                ],
            ]);
        }

        //セッション情報書込
        $this->getRequest()->getSession()->write('detail_base_kbn', $pBaseKbn);
        $this->getRequest()->getSession()->write('detail_yyyymmdd', $pYmd);

        // ビューにセット
        $this->set('baseKbnList', $this->getInstrumentDivisionList());
        $this->set('baseKbn', $pBaseKbn);
        $this->set('yyyymmdd', $pYmd);
        $this->set('dataList', $dataList);
        $this->set('reservationDecision', $reservationDecision);
        $this->set('foodDivisionReservationList', $this->getFoodDivisionReservationList($pBaseKbn));

        $this->set('table', $this->ReservationInfo->schema());	//テーブル情報取得
    }

    public function update()
    {
        $baseKbn = $this->getRequest()->getSession()->read('detail_base_kbn');
        $ymd = $this->getRequest()->getSession()->read('detail_yyyymmdd');

        //修正
        if (isset($this->request->data['update_check'])) {
            // 状態フラグを設定
            $this->request->data['state_flg'] = '1';

            if ($this->ReservationInfo->saveEX($this->request->data)) {
                $this->Flash->success($this->property['message']['infoMsg02']);

                return $this->redirect(['action' => 'detail'.'/'.$baseKbn.'/'.$ymd]);
            } else {
                $this->Flash->set($this->property['message']['exceptionMsg01']);
            }
            //削除
        } elseif (isset($this->request->data['delete_check'])) {
            // 状態フラグを設定
            $this->request->data['state_flg'] = '2';

            if ($this->ReservationInfo->saveEX($this->request->data)) {
                $this->Flash->success($this->property['message']['infoMsg03']);

                return $this->redirect(['action' => 'detail'.'/'.$baseKbn.'/'.$ymd]);
            } else {
                $this->Flash->set($this->property['message']['exceptionMsg01']);
            }
        }
        $this->detail($baseKbn, $ymd);
        $this->render('detail');
    }

    public function insert()
    {
        $resultMessage = '';

        $baseKbn = $this->request->data['ReservationInfo']['base_kbn'];
        $reservationDate = $this->request->data['ReservationInfo']['reservation_date'];
        $reservationDate2 = str_replace('-', '', $this->request->data['ReservationInfo']['reservation_date']);
        $this->request->data['ReservationInfo']['reservation_date'] = new Time($this->request->data['ReservationInfo']['reservation_date']);
        $foodDivision = $this->request->data['ReservationInfo']['food_division'];
        // 入力チェック
        if ($this->inputValidate($this->request->data['ReservationInfo'], $resultMessage)) {
            // 社員情報取得
            $employeeInfo = $this->EmployeeInfo->findEX('all', ['conditions' => ['employee_id' => $this->request->data['ReservationInfo']['employee_id']]]);

            // 金額
            $this->request->data['ReservationInfo']['food_cost'] = $this->FoodPeriod->getFoodPrice($foodDivision, $reservationDate);

            // 社員区分
            $this->request->data['ReservationInfo']['employee_kbn'] = $employeeInfo[0]['EmployeeInfo']['employee_kbn'];

            if ($this->ReservationInfo->saveEX($this->request->data['ReservationInfo'])) {
                $this->Flash->success($this->property['message']['infoMsg01']);

                return $this->redirect(['action' => 'detail'.'/'.$baseKbn.'/'.$reservationDate2]);
            } else {
                $this->Flash->set($this->property['message']['exceptionMsg01']);
            }
        } else {
            $this->Flash->set($resultMessage);
        }
        $this->detail($baseKbn, $reservationDate2);
        $this->render('detail');
    }

    public function registration()
    {
        $this->set('menuLink', $this->getMenuLink(2));

        // 翌週・翌々週のデータを取得
        $now = date('w');
        if ($now < 4) {
            $nextSunday = date('Y-m-d', strtotime('next week -1 day'));
        } else {
            $nextSunday = date('Y-m-d', strtotime('next week +1 week -1 day'));
        }
        $next2Sunday = date('Y-m-d', strtotime($nextSunday.' +1 week'));

        // データ取得
        $reserveData = $this->ReservationInfo->getWeeklyReservationData($nextSunday, $next2Sunday);
        if (is_null($reserveData)) {
            $reserveData = [];
        }
        $reserveFoodDivisionList = $this->FoodDivision->getReserveFoodDivisionList();

        // 検索処理
        $this->set('dataList', $reserveData);
        $this->set('nextSunday', $nextSunday);
        $this->set('reserveFoodDivisionList', $reserveFoodDivisionList);
        $this->set('title_for_layout', '予約一括登録画面');
        $this->render('registration');
    }

    /**
     * バリデーション処理.
     */
    private function inputValidate($data, &$resultMessage)
    {
        $employee_id = $this->convertEncode($data['employee_id']);

        //社員コード必須チェック
        if ($employee_id == '') {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg26'];

            return false;
        } else {
            //半角英数字チェック
            if (!preg_match('/^([a-zA-Z0-9])*$/u', $employee_id)) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg13'];

                return false;
            }
            //文字数チェック
            if (strlen($employee_id) > 10) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg34'], '社員コード', '10');

                return false;
            }
            //社員コード存在チェック
            $resultCount = $this->EmployeeInfo->getCheckEmployeeId($employee_id);
            if ($resultCount == 0) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg38'], '社員コード');

                return false;
            }
        }

        return true;
    }

    /**
     * メニューのアップロード.
     */
    public function uploadMenu()
    {
        // 結果
        $resultMessage = '';
        $errorFlag = false;

        // ファイルが選択されているかどうか
        if (!empty($this->data)) {
            //$up_file = $this->data['ReservationInfo']['result']['tmp_name'];
            //$fileName = $this->data['ReservationInfo']['result']['name'];
            $up_file = $this->data['result']['select_file']['tmp_name'];
            $fileName = $this->data['result']['select_file']['name'];
            if (is_uploaded_file($up_file)) {
                // ファイルの拡張子チェック
                if ($this->isPdf($fileName)) {
                    $fileName = $this->data['result']['target_date'].'.pdf';
                    $tmpFilePath = '../webroot/menu/'.$fileName;
                    move_uploaded_file($up_file, $tmpFilePath);
                    $directoryName = $this->getDirectoryName();
                    $result = $this->S3Client->putFile($tmpFilePath, "$directoryName/menu/$fileName");
                    if ($result) {
                        $resultMessage = 'ファイルのアップロードに成功しました。';
                    } else {
                        // S3バケットへのputに失敗した場合
                        $resultMessage = 'ファイルのアップロードに失敗しました。再度試していただくか、サポートへお問い合わせください。';
                        $errorFlag = true;
                    }
                } else {
                    // PDFファイル以外が選択された場合
                    $resultMessage = 'アップロードできるファイルはPDFファイルのみとなります。';
                    $errorFlag = true;
                }
            } else {
                $resultMessage = 'アップロードするファイルを選択してください。';
                $errorFlag = true;
            }
        } else {
            $resultMessage = 'エラーが発生致しました。';
            $errorFlag = true;
        }
        // 画面表示
        if (!$errorFlag) {
            // エラーがない場合は青字
            $this->Flash->success($resultMessage);

            return $this->redirect(['action' => 'index'.'/'.$this->data['result']['base_kbn'].'/'.$this->data['result']['target_date']]);
        } else {
            $this->Flash->set($resultMessage);

            return $this->redirect(['action' => 'index'.'/'.$this->data['result']['base_kbn'].'/'.$this->data['result']['target_date']]);
        }
    }

    /**
     * ファイルの拡張子が".csv"であるかどうかのチェック.
     *
     * @param ファイル名
     *
     * @return true:CSVです false:CSVではありません
     */
    private function isPdf($fileName)
    {
        // 拡張子の取得
        $extension = substr($fileName, strpos($fileName, '.') + 1);

        // 拡張子がCSVであるかのチェック
        if (strcasecmp($extension, 'pdf') == 0) {
            return true;
        }

        return false;
    }

    /**
     * 予約一括登録.
     */
    public function uploadWeekData()
    {
        // 結果
        $resultMessage = '';
        $resultCount = 0;

        $errorFlag = $this->excelFileUpload($resultMessage);
        // 画面表
        if (!$errorFlag) {
            // エラーがない場合は青字
            $this->Flash->success($resultMessage);
        } else {
            $this->Flash->set($resultMessage);
        }

        return $this->redirect(['action' => 'registration']);
    }

    /**
     * 選択したExcelをアップロードする.
     */
    private function excelFileUpload(&$resultMessage)
    {
        // ファイルが選択されているかどうか
        if (empty($this->data)) {
            $resultMessage = 'エラーが発生致しました。';

            return true;
        }
        $up_file = $this->data['ReservationInfo']['result']['tmp_name'];
        $fileName = $this->data['ReservationInfo']['result']['name'];
        if (!is_uploaded_file($up_file)) {
            $resultMessage = 'アップロードするファイルを選択してください。';

            return true;
        }
        // ファイルの拡張子チェック
        if (!$this->isExcel($fileName)) {
            // CSVファイル以外が選択された場合
            $resultMessage = 'アップロードできるファイルはEXCELファイルとなります。';

            return true;
        }
        $fileName = '../tmp/excel/'.'upload.excel';
        move_uploaded_file($up_file, $fileName);
        // EXCELファイルの内容をDBにインポート
        return $this->uploadFromExcel($fileName, $resultMessage, $resultCount);
    }

    /**
     * アップロードしたEXCELのデータを取得.
     *
     * @param ファイル名
     */
    private function uploadFromExcel($fileName, &$resultMessage, &$resultCount)
    {
        try {
            $excelData = $this->readXlsx($fileName);

            if (count($excelData) <= 1) {
                $resultMessage = $this->property['message']['errorMsg37'];

                return true;
            }
            if (!$this->dateColumnValidate($excelData, $resultMessage)) {
                return true;
            }
            $this->updateDBFromUpdateFile($excelData, $resultMessage, $resultCount);
            if (empty($resultMessage)) {
                $resultMessage = 'EXCELファイルのアップロードに成功しました。';

                return false;
            } elseif ($resultCount > 0) {
                // 1件でも成功していれば文言付加
                $resultMessage = 'EXCELファイルのアップロードに成功しました。'.'<br><br>'.$resultMessage;

                return true;
            }

            return true;
        } catch (Exception $e) {
            $this->log($e->getMessage());

            return false;
        }
    }

    /**
     * Excel1行目の日付のチェック.
     */
    private function dateColumnValidate($inputData, &$resultMessage)
    {
        $header = $inputData[0];
        $errorDate = [];
        for ($i = 3; $i < count($header); ++$i) {
            if ($header[$i] !== null && !$this->isDate($this->convertEncode($this->formatToDateStr($header[$i])))) {
                array_push($errorDate, $i + 1);
            }
        }
        if (!empty($errorDate)) {
            $resultMessage = implode(',', $errorDate).'列目の日付が不正です。';

            return false;
        }

        return true;
    }

    /**
     * アップロードしたファイルから取得してデータをDBに書き込む
     *
     * @param inputData データ
     */
    private function updateDBFromUpdateFile($inputData, &$resultMessage, &$resultCount)
    {
        $employeeIdList = [];
        $reserveFoodList = $this->FoodDivision->getReserveFoodDivision();
        $header = $inputData[0];
        foreach ($inputData as $key => $record) {
            if ($key === 0) {
                continue;
            }

            // カラム数チェック
            if (count($record) < 4) {
                if (!empty($resultMessage)) {
                    $resultMessage .= '<br>';
                }
                $resultMessage .= $this->property['message']['errorMsg31'].'（'.($key).'レコード目）';
                continue;
            }

            $data = [
                    'employee_id' => $this->convertEncode(strval($record[0])),			// 社員コード
                    'food_division' => $this->convertEncode(strval($record[2])),	// 食事区分
                    'week_data' => $this->getWeekData($record),
                ];

            $filter = array_filter($data);
            //空のレコードの場合何もしない
            if (empty($filter)) {
                continue;
            }

            // バリデーションの実施
            $errorMessage = '';
            if (!$this->excelInputValidate($header, $data, $reserveFoodList, $errorMessage, $key, $resultCount)) {
                if (!empty($resultMessage)) {
                    $resultMessage .= '<br>';
                }
                $resultMessage .= $errorMessage;
                continue;
            }

            //予約処理実行
            $this->insertReserveInfo($header, $data, $resultCount);
        }
    }

    /**
     * Excelの日〜土カラムのデータを配列にする.
     */
    private function getWeekData($record)
    {
        $weekData = [];
        for ($i = 3; $i < count($record); ++$i) {
            array_push($weekData, $record[$i]);
        }

        return $weekData;
    }

    /**
     * バリデーション処理.
     */
    private function excelInputValidate($header, $data, $reserveFoodList, &$resultMessage, $key)
    {
        $err_record = '';
        if (!is_null($key)) {
            $err_record = '（'.($key).'レコード目）';
        }

        $employee_id = $this->convertEncode($data['employee_id']);
        $food_division = $this->convertEncode($data['food_division']);
        $week_data = $data['week_data'];

        //社員コード必須チェック
        if ($employee_id == '') {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg26'].$err_record;

            return false;
        } else {
            //半角英数字チェック
            if (!preg_match('/^([a-zA-Z0-9])*$/u', $employee_id)) {
                //エラー処理
                $resultMessage = $this->property['message']['errorMsg13'].$err_record;

                return false;
            }
            //文字数チェック
            if (strlen($employee_id) > 10) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg34'], '社員コード', '10').$err_record;

                return false;
            }
            //社員コード存在チェック
            $resultCount = $this->EmployeeInfo->getCheckEmployeeId($employee_id);
            if ($resultCount == 0) {
                //エラー処理
                $resultMessage = sprintf($this->property['message']['errorMsg38'], '社員コード').$err_record;

                return false;
            }
        }

        // 食事区分存在チェック
        if (!in_array($food_division, $reserveFoodList)) {
            // エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg52'], implode(',', $reserveFoodList)).$err_record;

            return false;
        }

        // 日〜土が空または数値であるかチェック
        $errorDays = [];
        $allZeroFlg = true;
        foreach ($week_data as $key => $value) {
            if (!($value === null || preg_match('/^([0-9])*$/u', $value))) {
                $strDate = strtotime($this->formatToDateStr($header[($key + 3)]));
                $errorDate = date('n/j', $strDate);
                array_push($errorDays, $errorDate);
            } elseif (!($value == null)) {
                $allZeroFlg = false;
            }
        }
        if (!empty($errorDays)) {
            // エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg60'], implode(',', $errorDays)).$err_record;

            return false;
        } elseif ($allZeroFlg) {
            // エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg61']).$err_record;

            return false;
        }

        return true;
    }

    /**
     * 予約の登録処理.
     */
    private function insertReserveInfo($header, $data, &$resultCount)
    {
        $employee_id = $this->convertEncode($data['employee_id']);
        $food_division = $this->convertEncode($data['food_division']);
        $week_data = $data['week_data'];

        foreach ($week_data as $key => $value) {
            // ヘッダーに日付が入力されていない場合スキップ
            if ($header[$key + 3] === null) {
                continue;
            }

            $insertDate = $this->formatToDateStr($header[($key + 3)]);
            // 社員IDと予約日が重複しているか確認
            $reserveCount = $this->ReservationInfo->existsCardReseptDateReservation($employee_id, $food_division, $insertDate);
            if ($reserveCount) {
                $conditions = [
                    'employee_id' => $employee_id,
                    'food_division' => $food_division,
                    'reservation_date' => $insertDate,
                ];
                // 更新処理実行
                $this->ReservationInfo->deleteAll($conditions);
            }
            if ($value != null) {
                $employee_kbn = $this->EmployeeInfo->getEmpoyeeKbn($employee_id);
                $foodCost = $this->FoodPeriod->getFoodPrice($food_division, $insertDate);
                $registData = [
                    'employee_id' => $employee_id,
                    'employee_kbn' => $employee_kbn,
                    'food_division' => $food_division,
                    'reservation_date' => $insertDate,
                    'state_flg' => '0',
                    'food_cost' => $foodCost,
                    'delete_flg' => '0',
                ];
                $result = [];
                for ($i = 0; $i < $value; ++$i) {
                    $this->ReservationInfo->set($registData);
                    $this->ReservationInfo->saveEX();
                    array_push($result, $this->ReservationInfo->getAffectedRows());
                }
                if (count($result) > 0) {
                    ++$resultCount;
                } else {
                    $this->log($e->getMessage());
                }
            }
        }
    }

    /**
     * カレンダーの配列を作成（休日フラグ、予約件数付き）.
     */
    private function _makeCalendar($baseKbn, $year, $month)
    {
        // カレンダー作成のベースとなるリスト取得
        $calendar = $this->getCalendar($year, $month);

        // 休日を取得
        $dayOffList = $this->DayOffCalendar->findEX('all', [
            'fields' => ['day_off_datetime'],
            'conditions' => ['base_kbn' => $baseKbn, 'day_off_datetime LIKE ' => $year.'-'.$month.'%'],
            'order' => ['day_off_datetime'],
        ]);

        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();

        // 予約可能食事区分の取得
        $foodDivisionList = $this->getReservationFoodDivisionList($baseKbn);

        // 予約件数を取得
        $reservationList = $this->ReservationInfo->getDailySummary($year.$month, $foodDivisionList, $employeeKbnArray);

        // 確定日付を取得
        $decisionList = $this->ReservationDecision->findEX('all', [
            'fields' => ['reservation_date'],
            'conditions' => [
                'reservation_date LIKE ' => $year.'-'.$month.'%',
            ],
            'order' => ['reservation_date ASC'],
        ]);

        for ($i = 0; $i < count($calendar); ++$i) {
            if (empty($calendar[$i]['day'])) {
                $calendar[$i]['day_off'] = '';
                continue;
            }

            $calendar[$i]['day_off'] = '0';
            $calendar[$i]['reservation_count'] = 0;
            $calendar[$i]['decision_flag'] = 0;
            // 休日情報を付加
            foreach ((array) $dayOffList as $dayOff) {
                if (isset($dayOff['DayOffCalendar']['day_off_datetime']) && $calendar[$i]['day'] == intval(substr($dayOff['DayOffCalendar']['day_off_datetime'], 8, 2))) {
                    $calendar[$i]['day_off'] = '1';
                    $calendar[$i]['reservation_count'] = '';
                    continue;
                }
            }

            // 予約件数を付加
            foreach ((array) $reservationList as $reservation) {
                if (isset($reservation['reservation_date']) && $calendar[$i]['day'] == intval(substr($reservation['reservation_date'], 8, 2))) {
                    $calendar[$i]['reservation_count'] = intval($reservation['count']);
                    break;
                }
            }

            // 確定情報を付加
            foreach ((array) $decisionList as $decision) {
                if (isset($decision['ReservationDecision']['reservation_date']) && $calendar[$i]['day'] == intval(substr($decision['ReservationDecision']['reservation_date'], 8, 2))) {
                    $calendar[$i]['decision_flag'] = '1';
                    break;
                }
            }
        }

        return $calendar;
    }

    private function _setParam($baseKbn, $ym)
    {
        if (empty($ym)) {
            $yyyymm = [
                'year' => date('Y'),
                'month' => date('m'),
            ];
        } else {
            $yyyymm = [
                'year' => substr($ym, 0, 4),
                'month' => substr($ym, 4, 2),
            ];
        }

        $param = [
            'ReservationInfo' => [
                'base_kbn' => $baseKbn,
                'target_date' => $yyyymm,
            ],
        ];

        return $param;
    }
}
