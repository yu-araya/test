<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\I18n\Time;

class PitTouchController extends AppController
{
    public $uses = ['FoodHistoryInfo', 'EmployeeInfo', 'DayOffCalendar', 'ReservationInfo', 'ContentsSetVersion', 'FoodPeriod'];

    private $baseKbnHonsha = '1'; // 本社（ピットタッチのカレンダー、締め時間は本社を利用）

    public function beforeFilter(Event $event)
    {
        $this->loadComponent('S3Client');
        parent::beforeFilter($event);
    }

    public function proc()
    {
//        $this->layout = null;
        $this->autoRender = false;

        //ピットタッチURL
        define('PIT_TOUCH_URL', 'http://localhost/index.html');
        define('PIT_TOUCH_URL2', 'http://localhost/setting.html');
        define('PIT_TOUCH_URL3', 'http://localhost/reservation.html');
        //処理区分(1:食事タッチ情報登録
        define('FOOD_REGISTRATION', 1);
        //処理区分(2:有効ICカード件数取得
        define('SYORI_COUNT', 2);
        //処理区分(3:有効ICカードID取得
        define('SYORI_INFO', 3);
        //処理区分(4:ICカードチェック
        define('RESERVATION_IC_CARD_CHECK', 4);
        //処理区分(5:個人別カレンダー情報取得
        define('RESERVATION_INFO', 5);
        //処理区分(6:予約情報登録
        define('RESERVATION_REGISTRATION', 6);
        //処理区分(7:予約情報確認
        define('IC_CARD_RESERVATION', 7);
        //処理区分(8:売店用金額登録
        define("SHOP_REGISTRATION", 8);

        //アクセス元の取得
        $access_url = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $access_url = htmlspecialchars($_SERVER['HTTP_REFERER']);
        } else {
            $access_url = '';
        }

        //一時的に追加（重複データ対応のため）
        // 		$access_url = PIT_TOUCH_URL;

        //アクセス元がピットタッチの場合
        if (strpos($access_url, PIT_TOUCH_URL) !== false ||
            strpos($access_url, PIT_TOUCH_URL2) !== false ||
            strpos($access_url, PIT_TOUCH_URL3) !== false) {
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                doOptions();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                //処理区分
                $syori_kbn = isset($_POST['cardkbn']) ? $_POST['cardkbn'] : null;

                switch ($syori_kbn) {
                    case FOOD_REGISTRATION:			//食事タッチ情報登録
                        $this->FoodHistoryInfo->registFoodHistoryInfo();
                        break;
                    case SYORI_COUNT:				//有効ICカード件数取得
                        $this->EmployeeInfo->getUsefulCardIdCount();
                        break;
                    case SYORI_INFO:				//有効ICカードID取得
                        $start_count = isset($_POST['scount']) ? $_POST['scount'] : null;
                        $select_count = isset($_POST['select']) ? $_POST['select'] : null;
                        $this->EmployeeInfo->getUsefulCardIdInfo($start_count, $select_count);
                        break;
                    case RESERVATION_IC_CARD_CHECK:	//ICカードチェック
                        $this->EmployeeInfo->checkIcCard();
                        break;
                    case RESERVATION_INFO:			//個人別予約情報取得
                        $this->getReservationInfo();
                        break;
                    case RESERVATION_REGISTRATION:	//予約情報登録
                        $this->setReservationInfo();
                        break;
                    case IC_CARD_RESERVATION:		//予約情報確認
                        $start_count = isset($_POST['scount']) ? $_POST['scount'] : null;
                        $select_count = isset($_POST['select']) ? $_POST['select'] : null;
                        $this->EmployeeInfo->getUsefulCardIdReservationInfo($start_count, $select_count);
                        break;
                    case SHOP_REGISTRATION:        //売店用金額登録
                        $cardId = isset($_POST["cid"]) ? $_POST["cid"] : null;
                        $time =  isset($_POST["tim"]) ? $_POST["tim"] : null;
                        $cost =  isset($_POST["cost"]) ? $_POST["cost"] : null;
                        $foodDivision = $this->property['tenkey_food_division'][''];
                        $employeeInfo = $this->EmployeeInfo->findValidEmpInfoByIcCardNumber($cardId, $time);
                        if (empty($employeeInfo)) {
                            $this->createResponseHeader('1');
                            break;
                        }
                        $result = $this->FoodHistoryInfo->registShopData($employeeInfo, $cardId, $time, $cost, $foodDivision);
                        if ($result) {
                            $this->createResponseHeader('0');
                        }
                        break;
                }
            }
        }
    }

    private function createResponseHeader($resultCode)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods:GET,POST, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("text/plain;charset=UTF-8");
        printf("dsp=".$resultCode);
        printf("\r\nsnd=1002");
    }


    /**
     * 予約情報の取得.
     */
    private function getReservationInfo()
    {
        $employeeId = isset($_POST['empid']) ? $_POST['empid'] : null;
        $targetYm = isset($_POST['date']) ? $_POST['date'] : null;

        $result = [];
        $calendar = [];
        if (!empty($employeeId)) {
            // 社員情報取得
            $employeeInfo = $this->EmployeeInfo->findEX('all', [
                    'conditions' => ['employee_id' => $employeeId],
            ]);

            $targetY = date('Y');
            $targetM = date('m');

            if (!empty($targetYm)) {
                $targetY = substr($targetYm, 0, 4);
                $targetM = substr($targetYm, 4, 2);
            }

            // カレンダー・予約情報取得
            $calendar = $this->_makeCalendar($employeeId, $targetY, $targetM);

            $result = [
                'target_ym' => $targetY.$targetM,
                'employee_info' => $employeeInfo[0]['EmployeeInfo'],
                'calendar' => $calendar,
            ];
        }

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods:GET,POST, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('text/plain;charset=UTF-8');

        if (empty($result)) {
            printf('dsp=');
            printf("\r\nsnd=1003");
        } else {
            printf('dsp='.json_encode($result));
            printf("\r\nsnd=1002");
        }
    }

    /**
     * 予約情報を登録.
     */
    private function setReservationInfo()
    {
        $employeeId = isset($_POST['empid']) ? $_POST['empid'] : null;
        $employeeKbn = isset($_POST['empkbn']) ? $_POST['empkbn'] : null;
        $targetYm = isset($_POST['date']) ? $_POST['date'] : null;
        $addList = isset($_POST['adddate']) ? explode(',', $_POST['adddate']) : null;
        $deleteList = isset($_POST['deletedate']) ? explode(',', $_POST['deletedate']) : null;

        $result = true;
        $resultMessage = '';
        $count = 0;

        try {
            // 営業日を特定するために休日カレンダー取得
            $dayOffList = [];
            if (!empty($addList)) {
                $dayOffCalendar = $this->DayOffCalendar->findEX('all', [
                    'fields' => ['day_off_datetime'],
                    'conditions' => [
                        'base_kbn' => $this->baseKbnHonsha,
                        'OR' => [
                            ["DATE_FORMAT(day_off_datetime, '%Y-%m') = ".date('Y-m')],
                            ["DATE_FORMAT(day_off_datetime, '%Y-%m') = ".date('Y-m', strtotime(date('Y-m').'+1 month'))],
                            ["DATE_FORMAT(day_off_datetime, '%Y-%m') = ".date('Y-m', strtotime(date('Y-m').'+2 month'))],
                        ],
                    ],
                    'order' => ['day_off_datetime'],
                ]);
                foreach ($dayOffCalendar as $dayOff) {
                    array_push($dayOffList, substr($dayOff['DayOffCalendar']['day_off_datetime'], 0, 10));
                }
            }

            // 予約情報の登録
            foreach ($addList as $data) {
                if (empty($data)) {
                    continue;
                }

                $dataArr = explode('-', $data);
                $date = $dataArr[0];
                $foodDivision = $dataArr[1];
                $baseKbn = $this->getBaseKbnFoodDivision($foodDivision);

                $hdate = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);
                $foodPrice = $this->FoodPeriod->getFoodPrice($foodDivision, $hdate);

                // チェック
                if (!$this->_checkReservation($baseKbn, $hdate, $dayOffList)) {
                    if (empty($resultMessage)) {
                        $resultMessage = sprintf($this->property['message']['errorMsg41'], $hdate);
                    } else {
                        $resultMessage = sprintf($this->property['message']['errorMsg41'], $hdate).'<br>'.$resultMessage;
                    }
                    continue;
                }

                // 予約情報取得
                $reservationInfo = $this->_getReservationInfo($employeeId, $hdate, false);

                unset($resData);
                if (empty($reservationInfo)) {
                    $resData = [
                        'ReservationInfo' => [
                            'employee_id' => $employeeId,
                            'employee_kbn' => $employeeKbn,
                            'food_division' => $foodDivision,
                            'reservation_date' => new Time($hdate),
                            'food_cost' => $foodPrice,
                        ],
                    ];
                } else {
                    $resData = $reservationInfo[0]; // 複数ある場合は最過去レコード
                    $resData['ReservationInfo']['food_division'] = $foodDivision;
                    $resData['ReservationInfo']['reason'] = null;
                    $resData['ReservationInfo']['state_flg'] = 0;
                    $resData['ReservationInfo']['food_cost'] = $foodPrice;
                    $resData['ReservationInfo']['delete_flg'] = 0;
                    $resData['ReservationInfo']['reservation_date'] = new Time($resData['ReservationInfo']['reservation_date']);
                    $resData['ReservationInfo']['created'] = new Time($resData['ReservationInfo']['created']);
                    unset($resData['ReservationInfo']['modified']);
                }

                if ($this->ReservationInfo->saveEX($resData['ReservationInfo'])) {
                    ++$count;
                } else {
                    $result = false;
                }
            }

            // 予約情報の削除
            foreach ($deleteList as $data) {
                if (empty($data)) {
                    continue;
                }

                $dataArr = explode('-', $data);
                $date = $dataArr[0];
                $foodDivision = $dataArr[1];
                $baseKbn = $this->getBaseKbnFoodDivision($foodDivision);

                $hdate = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);

                // チェック
                if (!$this->_checkReservationCancel($baseKbn, $hdate)) {
                    if (empty($resultMessage)) {
                        $resultMessage = sprintf($this->property['message']['errorMsg44'], $hdate);
                    } else {
                        $resultMessage = sprintf($this->property['message']['errorMsg44'], $hdate).'<br><br>'.$resultMessage;
                    }
                    continue;
                }

                // 予約情報取得
                $reservationInfo = $this->_getReservationInfo($employeeId, $hdate, true);

                unset($resData);
                $resData = $reservationInfo[0]; // 複数ある場合は最過去レコード
                $resData['ReservationInfo']['state_flg'] = 2;
                unset($resData['ReservationInfo']['reservation_date']);
                unset($resData['ReservationInfo']['created']);
                unset($resData['ReservationInfo']['modified']);

                if ($this->ReservationInfo->saveEX($resData['ReservationInfo'])) {
                    ++$count;
                } else {
                    $result = false;
                }
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $result = false;
        }

        $json = ['message' => $resultMessage];

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods:GET,POST, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('text/plain;charset=UTF-8');

        printf('dsp='.json_encode($json));
        printf("\r\nsnd=1002");
    }

    /**
     * 予約情報チェック処理.
     */
    private function _checkReservation($baseKbn, $date, $dayOffList)
    {
        $sysYmd = date('Y-m-d');
        $sysTime = date('H:i');
        // 翌営業日を取得（休日カレンダーから算出）
        $nextDate = date('Y-m-d', strtotime($sysYmd.'+1 day'));
        if (!empty($dayOffList)) {
            for ($i = 0; $i < 30; ++$i) {
                if (!in_array($nextDate, $dayOffList, true)) {
                    break;
                }
                $nextDate = date('Y-m-d', strtotime($nextDate.'+1 day'));
            }
        }

        // 締めチェック
        if ($sysYmd >= $date) {
            // 当日・過去日はエラー
            return false;
        } elseif ($nextDate == $date) {
            if ($this->property['reservation_closing_time'][$baseKbn] <= $sysTime) {
                // 締め時間越えはエラー
                return false;
            }
        }

        return true;
    }

    /**
     * 予約情報取消しチェック処理.
     */
    private function _checkReservationCancel($baseKbn, $date)
    {
        $sysYmd = date('Y-m-d');
        $sysTime = date('H:i');

        // 締めチェック
        if ($sysYmd > $date) {
            // 過去日はエラー
            return false;
        } elseif ($sysYmd == $date) {
            if ($this->property['reservation_cancel_closing_time'][$baseKbn] <= $sysTime) {
                // 締め時間越えはエラー
                return false;
            }
        }

        return true;
    }

    /**
     * 指定パラメータの予約情報取得.
     */
    private function _getReservationInfo($employeeId, $date, $deleteFlag)
    {
        $condisions = [
            'employee_id' => $employeeId,
            "DATE_FORMAT(reservation_date, '%Y-%m-%d') LIKE ('$date%')",
        ];

        if ($deleteFlag) {
            $condisions += ['state_flg IN' => ['0', '1']];
        }

        $reservationInfo = $this->ReservationInfo->findEX('all', [
            'conditions' => $condisions,
            'order' => 'REPLACE(state_flg, 1, 0), id',
        ]);

        return $reservationInfo;
    }

    /**
     * カレンダーの配列を作成（休日フラグ、予約件数付き）.
     */
    private function _makeCalendar($employeeId, $year, $month)
    {
        // カレンダー作成のベースとなるリスト取得
        $calendar = $this->getCalendar($year, $month);

        // 休日を取得
        $dayOffList = $this->DayOffCalendar->findEX('all', [
            'fields' => ['day_off_datetime'],
            'conditions' => ['base_kbn' => $this->baseKbnHonsha, "DATE_FORMAT(day_off_datetime, '%Y%m') = '".$year.$month."' "],
            'order' => ['day_off_datetime'],
        ]);

        // 予約件数を取得
        $reservationList = $this->ReservationInfo->getEmployeeReservationInfo($year.$month, $employeeId);

        // 予約は１ヶ月先の月末まで
        $editMonthFlag = '1';
        if (($year.$month) > date('Ym', strtotime(date('Y-m-d').'+1 month'))) {
            $editMonthFlag = '0';
        }

        $sysYmd = date('Ymd');
        $sysTime = date('H:i');

        for ($i = 0; $i < count($calendar); ++$i) {
            if (empty($calendar[$i]['day'])) {
                continue;
            }

            $calendar[$i]['day_off'] = '0';
            $calendar[$i]['food_division'] = '';
            $calendar[$i]['edit_flag'] = '0';

            // 休日情報を付加
            if (!empty($dayOffList)) {
                foreach ($dayOffList as $dayOff) {
                    if ($calendar[$i]['day'] == intval(substr($dayOff['DayOffCalendar']['day_off_datetime'], 8, 2))) {
                        $calendar[$i]['day_off'] = '1';
                        continue;
                    }
                }
            }

            // 予約件数を付加
            foreach ($reservationList as $reservation) {
                if ($calendar[$i]['day'] == intval(substr($reservation['reservation_date'], 8, 2))) {
                    if (!empty($calendar[$i]['food_division'])) {
                        $calendar[$i]['food_division'] .= ',';
                    }
                    $calendar[$i]['food_division'] .= $reservation['food_division'];
                }
            }

            if ($calendar[$i]['day_off'] == '0' && $editMonthFlag == '1') {
                $date = $year.$month.sprintf('%02d', $calendar[$i]['day']);
                // 締め判定
                if ($sysYmd < $date) {
                    // 未来日
                    $calendar[$i]['edit_flag'] = '1';
                } elseif ($sysYmd == $date) {
                    // 当日（締め時間の判定）
                    if ($this->property['reservation_cancel_closing_time'][$this->baseKbnHonsha] > $sysTime) {
                        $calendar[$i]['edit_flag'] = '1';
                    }
                }
            }
        }

        return $calendar;
    }

    public function doOptions()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods:GET,POST, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header('Content-type:text/plain;charset=UTF-8');
        echo ' ';
    }

    /**
     * コンテンツアップデート.
     */
    public function contentsUpdate()
    {
        $this->autoRender = false;

        if (isset($_POST['contentsVersion'])) {
            // $this->_contentSetUpdate();
            // コンテンツ更新リクエスト
            $terminal_id = $_POST['tid'];
            $version = explode('rev', $_POST['contentsVersion'])[0];
            $revision = explode('rev', $_POST['contentsVersion'])[1];

            // 最新コンテンツverの取得
            // $contentsSetVersion = $this->ContentsSetVersion->findAllByTerminalIdAndDeleteFlg($terminal_id, '0');
            $contentsSetVersion = $this->ContentsSetVersion->findEX('all', [
                'conditions' => [
                    'terminal_id' => $terminal_id,
                    'delete_flg' => 0, ],
                ]);

            if (!empty($contentsSetVersion) && count($contentsSetVersion) == 1) {
                $contentsType = $contentsSetVersion[0]['ContentsSetVersion']['contents_type'];
                $strFnc = '';
                $strUrl = '';

                if ($this->isContentsSetVersionLatest($version, $revision, $contentsType)) {
                    // 更新不要
                    $strFnc = "fnc=00\r\n";
                } else {
                    // 更新要
                    $strFnc = "fnc=04\r\n";

                    $directoryName = $this->getDirectoryName();
                    $contentsSetFilename = $this->property['contents_set_filename'][$contentsType];
                    $s3FilePath = "$directoryName/contentsset/$contentsSetFilename";

                    $fileGetResult = $this->S3Client->getFile($s3FilePath, "contentsset/$contentsSetFilename");

                    $strUrl = 'url='.$this->property['contents_set_download_url']['url'].'/'.$contentsSetFilename."\r\n";

                    // 管理バージョンを最新バージョンに変更
                    // ※端末側が本当にコンテンツセットを更新したかどうかはサーバー側は判定できないので、バージョン管理のみ
                    $contentsSetVersion[0]['ContentsSetVersion']['version'] = explode('rev', $this->property['latest_contents_set_version'][$contentsType])[0];
                    $contentsSetVersion[0]['ContentsSetVersion']['revision'] = explode('rev', $this->property['latest_contents_set_version'][$contentsType])[1];
                    $contentsSetVersion[0]['ContentsSetVersion']['created'] = new Time($contentsSetVersion[0]['ContentsSetVersion']['created']);
                    $contentsSetVersion[0]['ContentsSetVersion']['modified'] = new Time($contentsSetVersion[0]['ContentsSetVersion']['modified']);
                    $this->ContentsSetVersion->saveEX($contentsSetVersion[0]['ContentsSetVersion']);
                }
                header('HTTP/1.1 200 OK');
                header('ContentType:text/plain;charset=UTF-8');
                header('Status:200');
                printf("res=00\r\n");
                printf($strFnc);
                printf($strUrl);
            } else {
                // 複数件 or 0件なのでエラーを返却
                header('HTTP/1.1 200 OK');
                header('ContentType:text/plain;charset=UTF-8');
                header('Status:200');
                printf("res=01\r\n");
                printf("errCode=01\r\n");
            }
        }
    }
}
