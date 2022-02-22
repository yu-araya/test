<?php

namespace App\Controller;

use App\Controller\AppController;

class PitTouchBizController extends AppController
{
    public $uses = ['FoodHistoryInfo', 'EmployeeInfo', 'ReservationInfo', 'RegistError'];

    /**
     * 食事予約を登録する.
     */
    public function reserve()
    {
        $this->layout = null;
        $this->autoRender = false;

        // リクエストを配列に変換
        $request = $this->__convertRequestIntoArray();

        if (boolval($this->__isValidRequest($request)) === false) {
            // 必須項目なしエラー
            $this->__rtnInvalidRequest(__METHOD__, $request);

            return;
        }

        $employeeInfo = $this->EmployeeInfo->findValidEmpInfoByIcCardNumber($request['cardId'], $request['cardReceptTime']);
        if (empty($employeeInfo)) {
            // 社員データ存在なしエラー
            $this->__rtnNoEmployee(__METHOD__, $request);

            return;
        }

        // DB予約登録
        try {
            $this->ReservationInfo->saveEX($this->__convertRequestIntoReservationDataArray($employeeInfo['EmployeeInfo'], $request));
            printf('res=00');
            printf("\r\nfnc=00");
            printf("\r\nsnd=1001");
            printf("\r\nlmp=01");
        } catch (PDOException $e) {
            // 登録失敗
            $this->__rtnDataBaseError(__METHOD__, $request, $employeeInfo['EmployeeInfo']);
            $this->log($e->getMessage(), 'error');
        }
    }

    /**
     * 喫食登録.
     */
    public function eat()
    {
        $this->layout = null;
        $this->autoRender = false;

        $request = $this->__convertRequestIntoArray();

        if (boolval($this->__isValidRequest($request)) === false) {
            $this->__rtnInvalidRequest(__METHOD__, $request);

            return;
        }

        $employeeInfo = $this->EmployeeInfo->findValidEmpInfoByIcCardNumber($request['cardId'], $request['cardReceptTime']);
        if (empty($employeeInfo)) {
            // 該当社員存在なしエラー
            $this->__rtnNoEmployee(__METHOD__, $request);

            return;
        }

        try {
            // 予約存在チェック
            $reserveFoodDivision = $this->FoodDivision->getReserveFoodDivision($request['foodDivision']);
            if (!$this->ReservationInfo->existsCardReseptDateReservation($employeeInfo['EmployeeInfo']['employee_id'], $reserveFoodDivision, $request['cardReceptTime'])) {
                $this->__rtnNoReservation(__METHOD__, $request, $employeeInfo['EmployeeInfo']);

                return;
            }

            // 喫食登録
            $this->FoodHistoryInfo->saveEX($this->__convertRequestIntoFoodHistoryDataArray($employeeInfo['EmployeeInfo'], $request));
            $this->__rtnSuccess();
        } catch (PDOException $e) {
            // 登録失敗
            $this->__rtnDataBaseError(__METHOD__, $request, $employeeInfo['EmployeeInfo']);
            $this->log($e->getMessage(), 'error');
        }
    }

    /**
     * リクエストを配列に変換する.
     */
    private function __convertRequestIntoArray()
    {
        return [
            'terminalId' => isset($_POST['tid']) ? $_POST['tid'] : null,
            'cardId' => isset($_POST['cid']) ? $_POST['cid'] : null,
            'cardType' => isset($_POST['typ']) ? $_POST['typ'] : null,
            'cardReceptTime' => isset($_POST['tim']) ? $_POST['tim'] : null,
            'status' => isset($_POST['sts']) ? $_POST['sts'] : null,
            'instrumentDivision' => isset($_POST['idi']) ? $_POST['idi'] : null,
            'foodDivision' => isset($_POST['fno']) ? $_POST['fno'] : null,
        ];
    }

    /**
     * リクエストと社員情報から登録する予約データを作成する.
     */
    private function __convertRequestIntoReservationDataArray($employeeInfo, $request)
    {
        return [
            'employee_id' => $employeeInfo['employee_id'],
            'employee_kbn' => $employeeInfo['employee_kbn'],
            'food_division' => $request['foodDivision'],
            'reason' => null,
            'reservation_date' => $this->__getReservationDate($request['cardReceptTime'], $request['foodDivision']),
            'state_flg' => '0',
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'food_cost' => '0',
            'delete_flg' => '0',
        ];
    }

    /**
     * リクエストと社員情報から登録する予約データを作成する.
     */
    private function __convertRequestIntoFoodHistoryDataArray($employeeInfo, $request)
    {
        return [
            'employee_id' => $employeeInfo['employee_id'],
            'employee_kbn' => $employeeInfo['employee_kbn'],
            'ic_card_number' => $request['cardId'],
            'instrument_division' => $request['instrumentDivision'],
            'food_division' => $request['foodDivision'],
            'reason' => null,
            'card_recept_time' => $request['cardReceptTime'],
            'state_flg' => '0',
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
            'delete_flg' => '0',
        ];
    }

    /**
     * リクエストの必須項目にデータが無い場合はfalseを返却する.
     */
    private function __isValidRequest($request)
    {
        return isset($request['cardId'])
            && isset($request['cardReceptTime'])
            && isset($request['instrumentDivision'])
            && isset($request['foodDivision']);
    }

    /**
     * カード読み込み時刻から予約日を決定する.
     */
    private function __getReservationDate($cardReceptTime, $foodDivision)
    {
        if (date('H:i:s', strtotime($cardReceptTime)) >= $this->property['reservation_closing_time'][$foodDivision]) {
            // 予約締め時間を過ぎたら翌日の予約として扱う
            $reservationData = date('Y-m-d', strtotime($cardReceptTime.' +1 day'));
        } else {
            $reservationData = date('Y-m-d', strtotime($cardReceptTime));
        }

        return $reservationData;
    }

    /**
     * 成功を返却.
     */
    private function __rtnSuccess()
    {
        printf('res=00');
        printf("\r\nfnc=00");
        printf("\r\nsnd=1001");
        printf("\r\nlmp=01");
    }

    /**
     * 不正リクエストを返却.
     */
    private function __rtnInvalidRequest($method, $request)
    {
        $this->log($method.' Invalid Request:'.print_r($request, 'true'), 'error');
        printf('res=99');
        printf("\r\nfnc=00");
        printf("\r\nsnd=1002");
        printf("\r\nlmp=02");
        $registErrorData = [
            'RegistError' => [
                'occurrence_datetime' => date('Y-m-d H:i:s'),
                'function_name' => (explode('::', $method)[1] == 'eat') ? '食事登録' : '予約登録',
                'error_level' => 'Error',
                'reason' => $this->property['message']['errorMsg45'],
                'employee_id' => null,
                'ic_card_number' => null,
                'instrument_division' => null,
                'food_division' => null,
                'card_recept_time' => $request['cardReceptTime'],
            ],
        ];
        $this->RegistError->saveEX($registErrorData);
    }

    /**
     * 社員データなしエラーを返却.
     */
    private function __rtnNoEmployee($method, $request)
    {
        printf('res=00');
        printf("\r\nfnc=00");
        printf("\r\nsnd=1002");
        printf("\r\nlmp=02");

        $this->log($method.' Invalid Employee Info Request:'.print_r($request, 'true'), 'error');
        $registErrorData = [
            'RegistError' => [
                'occurrence_datetime' => date('Y-m-d H:i:s'),
                'function_name' => (explode('::', $method)[1] == 'eat') ? '食事登録' : '予約登録',
                'error_level' => 'Warning',
                'reason' => $this->property['message']['errorMsg46'],
                'employee_id' => null,
                'ic_card_number' => $request['cardId'],
                'instrument_division' => $request['instrumentDivision'],
                'food_division' => $request['foodDivision'],
                'card_recept_time' => $request['cardReceptTime'],
            ],
        ];
        $this->RegistError->saveEX($registErrorData);
    }

    /**
     * 予約なしを返却.
     */
    private function __rtnNoReservation($method, $request, $employeeInfo)
    {
        printf('res=00');
        printf("\r\nfnc=00");
        printf("\r\nsnd=1002");
        printf("\r\nlmp=02");

        $this->log($method.' Not Found Reservation:'.print_r($request, 'true'), 'info');
        $registErrorData = [
            'RegistError' => [
                'occurrence_datetime' => date('Y-m-d H:i:s'),
                'function_name' => (explode('::', $method)[1] == 'eat') ? '食事登録' : '予約登録',
                'error_level' => 'Info',
                'reason' => $this->property['message']['errorMsg47'],
                'employee_id' => $employeeInfo['employee_id'],
                'ic_card_number' => $request['cardId'],
                'instrument_division' => $request['instrumentDivision'],
                'food_division' => $request['foodDivision'],
                'card_recept_time' => $request['cardReceptTime'],
            ],
        ];
        $this->RegistError->saveEX($registErrorData);
    }

    /**
     * DBエラーを返却.
     */
    private function __rtnDataBaseError($method, $request, $employeeInfo)
    {
        printf('res=99');
        printf("\r\nfnc=00");
        printf("\r\nsnd=1002");
        printf("\r\nlmp=02");

        $this->log($method.' DataBase Error has occurred. Request:'.print_r($request, 'true'), 'error');
        $registErrorData = [
            'RegistError' => [
                'occurrence_datetime' => date('Y-m-d H:i:s'),
                'function_name' => (explode('::', $method)[1] == 'eat') ? '食事登録' : '予約登録',
                'error_level' => 'Error',
                'reason' => $this->property['message']['errorMsg48'],
                'employee_id' => $employeeInfo['employee_id'],
                'ic_card_number' => $request['cardId'],
                'instrument_division' => $request['instrumentDivision'],
                'food_division' => $request['foodDivision'],
                'card_recept_time' => $request['cardReceptTime'],
            ],
        ];
        $this->RegistError->saveEX($registErrorData);
    }
}
