<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Utility\Hash;
use Exception;
use Cake\Routing\Router;

class ApiController extends AppController
{
    public $uses = ['ContentsSetVersion', 'Category', 'AppSetting', 'FoodHistoryInfo', 'EmployeeInfo', 'InstrumentDivision', 'ReservationInfo', 'FoodDivision', 'MenuDisplayInTab', 'FoodPeriod', 'Tab', 'DayOffCalendar', 'Option', 'FoodHistoryReservation'];
    public $components = ['RequestHandler'];

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->autoRender = false;

        // ログイン認証を除外
        $this->Auth->allow();
    }

    /**
     * 設定情報取得.
     */
    public function setting()
    {
        $this->createResponse('00', '', $this->getSetting(''));
    }

    /**
     * 最新社員情報とメニューの取得.
     */
    public function reload()
    {
        // 全社員情報取得
        $employees = $this->EmployeeInfo->findAllValidEmpInfo(date('Y-m-d H:m:s'));
        $employees = Hash::extract($employees, '{n}.EmployeeInfo');
        $tabLists = $this->Tab->getTabLists();
        $instrumentDivision = $this->InstrumentDivision->getInstrumentDivisionList();
        // メニュー情報取得
        $menus = $this->Category->getDisplayTabletMenu();
        $responseBody = [
            'Tab' => $tabLists,
            'InstrumentDivision' => $instrumentDivision,
            'EmployeeInfo' => $employees,
            'Menu' => $menus,
        ];

        $this->createResponse('00', '', $responseBody);
    }

    /**
     * 社員情報取得.
     */
    public function user()
    {
        $this->setResponseTemplate();
        $employees = [];
        if (isset($this->request->query['iccard_num'])) {
            $employees = $this->EmployeeInfo->findValidEmpInfoByIcCardNumber(
                $this->request->query['iccard_num'],
                date('Y-m-d H:m:s')
            );
        } else {
            // 全社員情報取得
            $employees = $this->EmployeeInfo->findAllValidEmpInfo(date('Y-m-d H:m:s'));
        }
        $this->createResponse('00', '', $employees);
    }

    /**
     * 注文登録API.
     */
    public function foodhistory()
    {
        $requestBody = $this->request->input('json_decode', true);

        $errCount = $this->saveOrder($requestBody);
        if ($errCount == 0) {
            $this->createResponse('00', '', ['errCount' => 0]);
        } else {
            $this->log('Order registError.Check Request :'.print_r($this->request->input()), 'error');
            $this->createResponse('04', $this->property['message']['apiMessage04'], ['errCount' => $errCount]);
        }
    }

    /**
     * 注文キャンセル.
     */
    public function cancel()
    {
        $result = $this->cancelOrder($this->request->input('json_decode', true)['id']);
        $this->log($result['resultCode'].$result['errorMessage'], 'error');
        $this->createResponse($result['resultCode'], $result['errorMessage'], []);
    }

    /**
     * メニュー名の取得.
     */
    public function menu()
    {
        $this->createResponse('00', '', ['foodDivisionList' => $this->Category->getDisplayTabletMenu()]);
    }

    /**
     * 予約情報API.
     */
    public function reservation()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                // 予約情報取得
                $employeeId = $this->request->query['employee_id'];
                $reservationDate = '';
                if (isset($this->request->query['reservation_date']) && !empty($this->request->query['reservation_date'])) {
                    $reservationDate = $this->request->query['reservation_date'];
                } else {
                    $reservationDate = date('Y-m-d');
                }
                $result = $this->getReservation($employeeId, $reservationDate);
                $this->createResponse('00', '', $result);
                break;
            case 'POST':
                // 予約情報登録
                $requestBody = $this->request->input('json_decode', true);
                $this->log($requestBody, 'error');

                $errCount = $this->saveReservation($requestBody);
                if ($errCount == 0) {
                    $this->createResponse('00', '', ['errCount' => 0]);
                } else {
                    $this->log('Order registError.Check Request :'.print_r($this->request->input()), 'error');
                    $this->createResponse('04', $this->property['message']['apiMessage04'], ['errCount' => $errCount]);
                }
                break;
            default:
                break;
        }
    }

    /**
     * 処理結果レスポンスを冒頭につけてjsonエンコード.
     */
    private function createResponse($resultCode, $errorMessage, $responseBody, $statusCode = '200')
    {
        $resultInfo = [
            'result' => [
                'resultCode' => $resultCode,
                'errorMessage' => $errorMessage,
            ],
        ];
        $this->setResponseTemplate($statusCode);
        $this->response->body(json_encode(array_merge($resultInfo, $responseBody)));
    }

    /**
     * レスポンス定義.
     */
    private function setResponseTemplate($statusCode = 200)
    {
        $this->response->statusCode($statusCode);
        $this->response->header('Access-Control-Allow-Origin', '*');
        $this->response->header('Access-Control-Allow-Methods:GET,POST, OPTIONS');
        $this->response->header('Access-Control-Allow-Headers: *');
        $this->response->type('Content-type:application/json; charset=UTF-8');
    }

    /**
     * 注文登録.
     */
    private function saveOrder($requestBody)
    {
        $emloyee = $this->EmployeeInfo->findEX('first', [
            'conditions' => [
                'employee_id' => $requestBody['employeeId'],
            ],
        ]);

        $errCount = 0;
        foreach ($requestBody['order'] as $key => $order) {
            for ($i = 0; $i < intval($order['count']); ++$i) {
//                $this->FoodHistoryInfo->create();
                $saveData = [
                    'employee_id' => $requestBody['employeeId'],
                    'employee_kbn' => $emloyee['EmployeeInfo']['employee_kbn'],
                    'ic_card_number' => $emloyee['EmployeeInfo']['ic_card_number'],
                    'instrument_division' => $requestBody['instrumentDivision'],
                    'food_division' => $order['foodDivision'],
                    'food_cost' => $order['foodCost'],
                    'card_recept_time' => new Time($requestBody['cardReceptDateTime']),
                    'state_flg' => '0',
                    'delete_flg' => '0',
                ];
                if (!$this->FoodHistoryInfo->saveEX($saveData)) {
                    $this->log('Order regist fail.'
                    .'employee_id:'.$requestBody['employeeId']
                    .'ic_card_number:'.$requestBody['ic_card_number']
                    .'food_division:'.$order['food_division']
                    .'food_cost'.$order['foodCost']
                    .'card_recept_time:'.$requestBody['card_recept_time'], 'error');
                    ++$errCount;
                }
            }
        }

        return $errCount;
    }

    /**
     * 予約情報を取得する.
     */
    private function getReservation($employee_id, $reservationDate = null)
    {
        $queryData = [
            'fields' => [
                'employee_id',
                'employee_kbn',
                'food_division',
                'reservation_date',
            ],
            'conditions' => [
                'employee_id' => $employee_id,
                "DATE_FORMAT(reservation_date, '%Y-%m-%d') = '$reservationDate'",
            ],
        ];
        $this->log($queryData, 'error');

        return ['ReservationInfo' => Hash::extract($this->ReservationInfo->findEX('all', $queryData), '{n}.ReservationInfo'),
        ];
    }

    /**
     * 注文をキャンセルする.
     */
    private function cancelOrder($foodHistoryId)
    {
        $result = [];
        $this->FoodHistoryInfo->id = $foodHistoryId;
        $foodHistoryInfo = $this->FoodHistoryInfo->findEX('first', ['conditions' => ['id' => $foodHistoryId]]);
        $this->log(print_r($foodHistoryInfo, true), 'error');
        if (!empty($foodHistoryInfo)) {
            $data = [
                'state_flg' => '2', // 削除
            ];
            if ($this->FoodHistoryInfo->saveEX($data)) {
                $result = ['resultCode' => '00', 'errorMessage' => ''];
            } else {
                $this->log('Cancel error. food_history_info:'.print_r($foodHistoryInfo, true), 'error');
                $result = ['resultCode' => '04', 'errorMessage' => $this->property['message']['apiMessage05']];
            }
        } else {
            $this->log('Order not found. food_history_info_id:'.$this->request->data['foodHistoryInfosId'], 'error');
            $result = ['resultCode' => '04', 'errorMessage' => $this->property['message']['apiMessage06']];
        }

        return $result;
    }

    /**
     * 設定情報取得.
     */
    private function getSetting($clientId)
    {
        return $this->AppSetting->findEX('first', [
            'conditions' => [
                'client_id' => $clientId,
            ],
        ]);
    }

    /**
     * 予約登録.
     */
    private function saveReservation($requestBody)
    {
        $emloyee = $this->EmployeeInfo->findEX('first', [
            'conditions' => [
                'employee_id' => $requestBody['employeeId'],
            ],
        ]);

        $errCount = 0;
        foreach ($requestBody['order'] as $key => $order) {
            for ($i = 0; $i < intval($order['count']); ++$i) {
//                $this->ReservationInfo->create();
                $saveData = [
                    'employee_id' => $requestBody['employeeId'],
                    'employee_kbn' => $emloyee['EmployeeInfo']['employee_kbn'],
                    'food_division' => $order['foodDivision'],
                    'reservation_date' => $requestBody['reservation_date'],
                    'state_flg' => '0',
                    'delete_flg' => '0',
                ];
                if (!$this->ReservationInfo->saveEX($saveData)) {
                    $this->log('Order regist fail.'
                    .'employee_id:'.$requestBody['employeeId']
                    .'food_division:'.$requestBody['food_division']
                    .'card_recept_time:'.$requestBody['card_recept_time'], 'error');
                    ++$errCount;
                }
            }
        }

        return $errCount;
    }

    /**
     * オプション設定を取得する.
     */
    public function getOptions()
    {
        $options = $this->Option->findEX('all');
        $result = [];
        foreach ($options as $key => $value) {
            $result[$value['Option']['option_key']] = boolval($value['Option']['option_state']);
        }
        $this->createResponse('00', '', $result);
    }

    /**
     * ログイン時の認証
     *
     * 社員コードとパスワードを受け取り、社員情報を返却す
     */
    public function login()
    {
        $requestBody = $this->request->input('json_decode', true);
        $employeeId = $requestBody['params']['employeeId'];
        $password = $requestBody['params']['password'];
        $loginUserInfo = $this->EmployeeInfo->userLoginConfirm($employeeId, $password);
        if (!empty($loginUserInfo)) {
            $result = [
                'id' => $loginUserInfo['EmployeeInfo']['employee_id'],
                'name' => $loginUserInfo['EmployeeInfo']['employee_name1'],
                'password' => $loginUserInfo['EmployeeInfo']['password'],
            ];
            $this->createResponse('00', '', $result);
        } else {
            $this->createResponse('00', $this->property['message']['errorMsg46'], 'error');
        }
    }

    public function loadUserInfo()
    {
        $userId = $this->request->query['userId'];
        $loginUserInfo = $this->EmployeeInfo->getEmployeeInfo($userId);
        if (!empty($loginUserInfo)) {
            $this->createResponse('00', '', $loginUserInfo);
        } else {
            $this->createResponse('00', $this->property['message']['errorMsg46'], 'error');
        }
    }

    /**
     * 事業所リストを取得する.
     */
    public function loadInstrument()
    {
        $requestBody = $this->request->input('json_decode', true);
        $instrumentList = $this->InstrumentDivision->getInstrumentDivisionList();
        $result = [];
        foreach ($instrumentList as $instrument) {
            array_push($result, [
                'division' => $instrument['instrument_division'],
                'name' => $instrument['instrument_name'],
            ]);
        }
        $this->createResponse('00', '', ['instrumentList' => $result]);
    }

    /**
     * 食事区分を取得する.
     */
    public function loadFoodDivision()
    {
        $requestBody = $this->request->input('json_decode', true);
        $foodDivisionList = $this->FoodDivision->getReserveFoodDivisionListForApi();
        $this->createResponse('00', '', ['foodDivisionList' => $foodDivisionList]);
    }

    /**
     * 予約状況を取得する.
     *
     * @param employeeId
     */
    public function loadReservationData()
    {
        $requestBody = $this->request->input('json_decode', true);
        $employeeId = $requestBody['params']['employeeId'];
        $startDate = date('Y-m-d', strtotime(date('Y-m-01').'-2 month'));
        $endDate = date('Y-m-t', strtotime(date('Y-m-01').'+2 month'));
        $reservationData = $this->ReservationInfo->getReservationDataWithinPeriod($employeeId, $startDate, $endDate);
        $this->createResponse('00', '', ['reservationData' => $reservationData]);
    }

    /**
     * カレンダーの休日情報を取得する.
     */
    public function loadHolidayData()
    {
        $dayOffCalendarList = $this->DayOffCalendar->findEX('all');
        $holidays = [];
        foreach ($dayOffCalendarList as $record) {
            array_push($holidays, [
                'instrumentDivision' => $record['DayOffCalendar']['base_kbn'],
                'holidayDate' => date('Y/m/d', strtotime($record['DayOffCalendar']['day_off_datetime'])),
            ]);
        }
        $this->createResponse('00', '', ['holidays' => $holidays]);
    }

    /**
     * 予約状況を　登録する.
     *
     * @param employeeId
     * @param foodDivision
     * @param reservationDate
     * @param count
     */
    public function registReservation()
    {
        $requestBody = $this->request->input('json_decode', true);
        $employeeId = $requestBody['employeeId'];
        $foodDivision = $requestBody['foodDivision'];
        $tmpDate = new \DateTime($requestBody['reservationDate']);
        $tmpDate->setTimeZone(new \DateTimeZone('Asia/Tokyo'));
        $reservationDate = $tmpDate->format('Y-m-d H:i:s');
        $count = $requestBody['count'];
        $employeeKbn = $this->EmployeeInfo->getEmpoyeeKbn($employeeId);
        $foodCost = $this->FoodPeriod->getFoodPrice($foodDivision, $reservationDate);
        $conditions = [
            'employee_id' => $employeeId,
            'food_division' => $foodDivision,
            'reservation_date' => $reservationDate,
            'state_flg <>' => 2,
        ];
        // 削除処理実行
        $this->ReservationInfo->deleteAll($conditions);

        $registData = [
            'employee_id' => $employeeId,
            'employee_kbn' => $employeeKbn,
            'food_division' => $foodDivision,
            'reservation_date' => $reservationDate,
            'state_flg' => '0',
            'food_cost' => $foodCost,
            'delete_flg' => '0',
        ];
        $result = [];
        // 登録処理実行
        for ($i = 0; $i < $count; ++$i) {
            $this->ReservationInfo->set($registData);
            $this->ReservationInfo->saveEX();
            array_push($result, $this->ReservationInfo->getAffectedRows());
        }
        if ($count == 0 || count($result) > 0) {
            $this->createResponse('00', '', ['result' => true]);
        } else {
            $this->createResponse('00', '', ['result' => false]);
        }
    }

    /**
     * パスワードを変更.
     *
     * @param employeeId
     * @param password
     */
    public function passwordChange()
    {
        $requestBody = $this->request->input('json_decode', true);
        $employeeId = $requestBody['employeeId'];
        $password = $requestBody['password'];
        $data = $this->EmployeeInfo->findEX('first', [
            'conditions' => ['employee_id' => $employeeId],
        ]);
        if($this->EmployeeInfo->updatePassword($employeeId, $password)) {
            $this->createResponse('00', '', ['result' => true]);
        } else {
            $this->createResponse('00', '', ['result' => false]);
        }
    }

    /**
     * 個人の3年分の喫食状況を取得.
     *
     * @param employeeId
     */
    public function loadFoodHistory()
    {
        $employeeId = $this->request->query['employeeId'];
        $startDate = date('Y-m-d', strtotime(date('Y-m-01').'-3 year'));
        $foodHistoryList = $this->FoodHistoryInfo->getUserFoodHistory($employeeId, $startDate);
        $reservatioList = $this->ReservationInfo->getUserReservation($employeeId, $startDate);
        $result = [];
        foreach ($foodHistoryList as $record) {
            $data = [
                'instrumentDivision' => $record['FoodHistoryInfo']['instrument_division'],
                'instrumentName' => $record['InstrumentDivision']['instrument_name'],
                'foodDivision' => $record['FoodHistoryInfo']['food_division'],
                'foodDivisionName' => $record['FoodDivision']['food_division_name'],
                'foodCost' => $record['FoodHistoryInfo']['food_cost'],
                'cardReceptTime' => date('Y/m/d H:i:s', strtotime($record['FoodHistoryInfo']['card_recept_time'])),
                'deleteFlg' => $record['FoodHistoryInfo']['state_flg'] == '2',
            ];
            array_push($result, $data);
        }
        foreach ($reservatioList as $record) {
            $data = [
                'instrumentDivision' => $record['FoodDivision']['instrument_division'],
                'instrumentName' => $record['InstrumentDivision']['instrument_name'],
                'foodDivision' => $record['ReservationInfo']['food_division'],
                'foodDivisionName' => $record['FoodDivision']['food_division_name'],
                'foodCost' => $record['ReservationInfo']['food_cost'],
                'cardReceptTime' => date('Y/m/d H:i:s', strtotime($record['ReservationInfo']['reservation_date'])),
                'deleteFlg' => $record['ReservationInfo']['state_flg'] == '2',
            ];
            array_push($result, $data);
        }
        $this->createResponse('00', '', ['foodHistoryDatas' => $result]);
    }

    /**
     * コンテンツバージョンを取得する.
     *
     * @return string version
     */
    public function getVersion()
    {
        $contents = $this->request->query['contents'];
        if (empty($contents)) {
            $this->log('getVersion API error : No param', 'info');
            $this->createResponse('00', '', []);

            return;
        } elseif (mb_strlen($contents) > 10) {
            $this->log('getVersion API error : Too long param', 'info');
            $this->createResponse('00', '', []);

            return;
        }

        $version = $this->ContentsSetVersion->getContentsVersion($contents);
        if (empty($version)) {
            $this->log('getVersion API error : No record', 'info');
            $this->createResponse('00', '', []);

            return;
        }
        $this->createResponse('00', '', ['version' => $version]);
    }

    /**
     * 注文監視API.
     *
     * @return void
     */
    public function watchOrder()
    {
        try {
            $requestAll = $this->request->query['requestAll'];

            if (is_null($requestAll)) {
                $this->createResponse('00', '', ['result' => false]);

                return;
            }

            $orderList = $this->FoodHistoryInfo->getDailyOrder($requestAll);

            if (is_null($orderList)) {
                $this->createResponse('00', '', ['result' => false]);
            }
            $this->createResponse('00', '', ['result' => true, 'orderList' => $orderList]);
        } catch (Exception $e) {
            $this->createResponse('00', '', ['result' => false], '500');
        }
    }

    public function getMenus()
    {
        try {
            $menus = $this->getFoodDivisionList();
            if (is_null($menus)) {
                $this->createResponse('00', '', ['result' => false]);
            }

            $this->createResponse('00', '', ['result' => true, 'menus' => $menus]);
        } catch (Exception $e) {
            $this->createResponse('00', '', ['result' => false], '500');
        }
    }

    /**
     * タブレット用ログ出力機能.
     */
    public function infolog()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $requestBody = $this->request->input('json_decode', true);
                $message = $requestBody['message'];

                if (!$message) {
                    $this->createResponse('00', '', ['result' => ['errormessage' => "no request body error. message:$message"]], '500');

                    return;
                }

                $this->log($message, 'info');
                $this->createResponse('00', '', []);
                break;
            default:
                break;
        }
    }

    /**
     * 本日より前後2ヶ月（計５ヶ月分）のPDFファイルを取得する
     * 存在しない場合空の配列を返却.
     *
     * @return void
     */
    public function getMenuPdfPath()
    {
        // S3から全ファイル取得
        $this->getFilesFromS3();

        $pdfUrls = [];
        $targetNames = [];
        for ($i = -2; $i < 3; ++$i) {
            $targetNames[] = date('Ym', strtotime($i.' month')).'.pdf';
        }
        foreach ($targetNames as $fileName) {
            if (file_exists('../webroot/menu/'.$fileName)) {
                $pdfUrls[] = Router::url('/', true).'menu/'.$fileName;
            }
        }
        $this->createResponse('00', '', ['pdfUrls' => $pdfUrls]);
    }

    public function loadDailyOrderData()
    {
        $employeeId = $this->request->query['employeeId'];
        $orderList = $this->FoodHistoryReservation->getDailyOrderList($employeeId);
        $this->log($orderList, 'error');
        $result = [];
        foreach ($orderList as $data) {
            array_push($result, [
                'foodDivisionName' => $data['FoodHistoryReservation']['FoodDivisions']['food_division_name'],
                'foodCost' => $data['FoodHistoryReservation']['FoodDivisions']['food_cost'],
                'orderDate' => date('Y/m/d H:i:s', strtotime($data['FoodHistoryReservation']['target_date'])),
            ]);
        }
        $this->createResponse('00', '', ['dailyOrderList' => $result]);
    }

}
