<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;

class FoodHistoryInfosController extends AppController {
    public $uses = array('FoodHistoryInfo', 'EmployeeInfo', 'Administrator', 'FoodDivision', 'FoodPeriod', 'InstrumentDivision', 'ReservationInfo', 'EmployeeKbn');
    public $pageLine = 20;

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->set('menuLink', $this->getMenuLink(3));
    }

    public function select() {
        $this->set('title_for_layout', '社員別食堂精算　検索画面');

        //セッション情報削除
        $this->getRequest()->getSession()->delete('detail_yyyymm');
        $this->getRequest()->getSession()->delete('detail_employeeId');
        $this->getRequest()->getSession()->delete('search_yyyymm');
        $this->getRequest()->getSession()->delete('search_employeeId');
        $this->getRequest()->getSession()->delete('search_name');
    }

    public function lists() {
        //POST+session利用時のブラウザバックでのキャッシュ有効期間切れ対応
        $this->response = $this->response->withCache('-1 minute', '+1 day');

        $this->set('title_for_layout', '社員別食堂精算　検索一覧画面');

        //セッション情報削除
        $this->getRequest()->getSession()->delete('detail_yyyymm');
        $this->getRequest()->getSession()->delete('detail_employeeId');

        //セッション情報があるかチェック
        if ($this->getRequest()->getSession()->check('search_yyyymm')) {
            //ある場合はセッションから設定
            $yyyymm = $this->getRequest()->getSession()->read('search_yyyymm');
            $employeeId = $this->getRequest()->getSession()->read('search_employeeId');
            $employee_name1 = $this->getRequest()->getSession()->read('search_employee_name1');
            $dining_license_flg = $this->getRequest()->getSession()->read('search_dining_license_flg');
        } else {
            //ない場合は前画面の情報から設定
            $yyyymm = $this->request->data['FoodHistoryInfo']['card_recept_time'];
            $employeeId = $this->request->data['FoodHistoryInfo']['employee_id'];
            $employee_name1 = $this->request->data['FoodHistoryInfo']['employee_name1'];
            $dining_license_flg = isset($this->request->data['dining_license_flg']) ? $_POST['dining_license_flg'] : null;

            //セッション情報書込
            $this->getRequest()->getSession()->write('search_yyyymm', $yyyymm);
            $this->getRequest()->getSession()->write('search_employeeId', $employeeId);
            $this->getRequest()->getSession()->write('search_employee_name1', $employee_name1);
            $this->getRequest()->getSession()->write('search_dining_license_flg', $dining_license_flg);
        }

        //データ取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();
        $sql = $this->FoodHistoryInfo->getFoodHistorysSql($yyyymm['year'].$yyyymm['month'], $employeeId, $employee_name1, $dining_license_flg, $employeeKbnArray);
	//$this->paginate = array('originalSql' => $sql, 'limit' => $this->pageLine);
        //$dataList = $this->paginate();
	$dataList = $this->PaginatorForPdo->paginateForPdo($sql, array(), array('limit' => $this->pageLine));

        //食事マスタ情報取得
        $foodDivisionList = $this->getFoodDivisionList();
        $this->set('foodDivisionList', $foodDivisionList);

        //ビューにセット
        $this->set('yyyymm', $yyyymm);
        $this->set('employeeId', $employeeId);
        $this->set('employee_name1', $employee_name1);
        $this->set('dining_license_flg', $dining_license_flg);
        $this->set('dataList', $dataList);
        $this->set('baseHeaderList', $this->getBaseHeaderList());
        $this->render('list');
    }

    private function getBaseHeaderList() {
        $headerList = [];
        foreach ($this->getInstrumentDivisionList() as $baseKbn => $baseName) {
            $headerList[] = "$baseName<br>（回数）";
        }
        foreach ($this->getInstrumentDivisionList() as $baseKbn => $baseName) {
            $result = $this->getFoodDivisionArrayFrom($baseKbn, true);
            if (count($result) > 0) {
                $headerList[] = "$baseName(予約)<br>（回数）";
            }
        }
        return $headerList;
    }
    
    public function detail($yyyymm = null, $employeeId = null) {
        $this->set('title_for_layout', '社員別食堂精算　詳細画面');

        if ($yyyymm == null) {
            //リダイレクトしてきた場合、セッションから情報を取得
            $yyyymm = $this->getRequest()->getSession()->read('detail_yyyymm');
            $employeeId = $this->getRequest()->getSession()->read('detail_employeeId');
        } else {
            //セッション情報書込
            $this->getRequest()->getSession()->write('detail_yyyymm', $yyyymm);
            $this->getRequest()->getSession()->write('detail_employeeId', $employeeId);
        }

        if ($employeeId == "NONE") {
            $employeeId ="";
        }

        //データ取得
        $employeeInfo = $this->EmployeeInfo->findEX('all', array('conditions' => array('employee_id' => $employeeId,)));
        $dataList = $this->FoodHistoryInfo->getEmployeeCostInfoList($yyyymm, $employeeId);

        //ビューにセット
        $this->set('yyyymm', $yyyymm);
        $this->set('employeeId', $employeeId);
        $this->set('employeeInfo', $employeeInfo);
        $this->set('dataList', $dataList);
        $this->set('baseKbnList', $this->getInstrumentDivisionList());
    }

    public function sumdaily() {
        //POST+session利用時のブラウザバックでのキャッシュ有効期間切れ対応
        $this->response = $this->response->withCache('-1 minute', '+1 day');

        $this->set('title_for_layout', '社員別食堂精算　集計画面');
        $this->set('menuLink', $this->getMenuLink(4));


        // 事業所区分
        if (isset($this->request->data['FoodHistoryInfo'])) {
            $baseKbn = $this->request->data['FoodHistoryInfo']['base_kbn'];
        } else {
            $baseKbn = '1';
        }


        // 対象年月
        if (isset($this->request->data['FoodHistoryInfo'])) {
            $yyyymm = $this->request->data['FoodHistoryInfo']['card_recept_time'];
        } else {
            $yyyymm = array(
                'year' => date('Y'),
                'month' => date('m')
            );
        }

        //食事マスタ情報取得
        $foodDivisionList = $this->getFoodDivisionList($baseKbn);
        
        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();

        //データ取得
        $dataList = $this->FoodHistoryInfo->getFoodHistorySummarySql($yyyymm['year'].$yyyymm['month'], $foodDivisionList, $employeeKbnArray);

        //ビューにセット
        $this->set('baseKbnList', $this->getInstrumentDivisionList());
        $this->set('baseKbn', $baseKbn);
        $this->set('yyyymm', $yyyymm);
        $this->set('dataList', $dataList);
        $this->set('foodDivisionList', $foodDivisionList);
        $this->render('sumdaily');
    }

    /**
     * CSVダウンロード
     */
    public function download() {
        $this->log(print_r($this->request, true), 'error');
        $this->layout = false;
        // 事業所区分
        if (isset($this->request->data['FoodHistoryInfo'])) {
            $baseKbn = $this->request->data['FoodHistoryInfo']['base_kbn'];
        } else {
            $baseKbn = '1';
        }


        // 対象年月
        if (isset($this->request->data['FoodHistoryInfo'])) {
            $yyyymm = array(
                'year' => substr($this->request->data['FoodHistoryInfo']['yyyymm'], 0, 4),
                'month' => substr($this->request->data['FoodHistoryInfo']['yyyymm'], 5, 2)
            );
        } else {
            $yyyymm = array(
                'year' => date('Y'),
                'month' => date('m')
            );
        }

        //食事マスタ情報取得
        $foodDivisionList = $this->getFoodDivisionList($baseKbn);
        
        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();

        //データ取得
        $dataList = $this->FoodHistoryInfo->getFoodHistorySummarySql($yyyymm['year'].$yyyymm['month'], $foodDivisionList, $employeeKbnArray);

        $this->set('yyyymm', $yyyymm);
        $this->set('dataList', $dataList);
        $this->set('foodDivisionList', $foodDivisionList);
    }

    public function add() {
        $this->set('title_for_layout', '社員別食堂精算　登録画面');
        $this->set('table', $this->FoodHistoryInfo->schema());	//テーブル情報取得
        $this->set('default_time', $this->property['default_card_recept_time']['']);

        
        //機器マスタ情報取得
        $instrumentDivisionList = $this->InstrumentDivision->getEatingInstrumentDivisionList();
        //食事マスタ情報取得
        $foodDivisionList = $this->FoodDivision->getFoodDivisionSelectList($instrumentDivisionList);

        $this->set('foodDivisionList', $foodDivisionList);
        $this->set('instrumentDivisionList', $instrumentDivisionList);
    }

    public function insert() {
        $resultMessage = "";
        //入力チェック
        if ($this->inputValidate($this->request->data['FoodHistoryInfo'], $resultMessage)) {
            //社員情報取得
            $employeeInfo = $this->EmployeeInfo->findEX('all', array('conditions' => array('employee_id' => $this->request->data['FoodHistoryInfo']['employee_id'])));
            $icCardNumber = $employeeInfo[0]['EmployeeInfo']['ic_card_number'];
            if (empty($icCardNumber)) {
                //主がなければ副を設定
                $icCardNumber = $employeeInfo[0]['EmployeeInfo']['ic_card_number2'];
            }

            //社員区分
            $this->request->data['FoodHistoryInfo']['employee_kbn'] = $employeeInfo[0]['EmployeeInfo']['employee_kbn'];
            //ICカード番号
            $this->request->data['FoodHistoryInfo']['ic_card_number'] = $icCardNumber;
            //カード受付時間を結合
            $time = $this->request->data['FoodHistoryInfo']['card_recept_time'].' '.$this->request->data['FoodHistoryInfo']['card_recept_time2'].':00';
            $this->request->data['FoodHistoryInfo']['card_recept_time'] = $time;

            // 金額を取得
            $foodDivision = $this->request->data['FoodHistoryInfo']['food_division'];
            // 日付を取得
            $date = $this->request->data['FoodHistoryInfo']['card_recept_time'];
            // food_periodから現在設定されている金額を取得、取得できなかった場合はfood_divisionに設定されている金額を流用
            $this->request->data['FoodHistoryInfo']['food_cost'] = $this->FoodPeriod->getFoodPrice($foodDivision, $date);
	    $this->request->data['FoodHistoryInfo']['card_recept_time'] = new Time($date);
	    unset($this->request->data['FoodHistoryInfo']['card_recept_time2']);
            if ($this->FoodHistoryInfo->saveEX($this->request->data['FoodHistoryInfo'])) {
                $this->Flash->success($this->property['message']['infoMsg01']);
                return $this->redirect(['action' => 'add']);
            } else {
                $this->Flash->set($this->property['message']['exceptionMsg01']);
            }
        } else {
            $this->Flash->set($resultMessage);
            $foodHistoryInfo = array();
            array_push($foodHistoryInfo, array('FoodHistoryInfo' => $this->request->data['FoodHistoryInfo']));
            $this->set('foodHistoryInfo', $foodHistoryInfo);
        }
        $this->add();
        $this->render('add');
    }

    public function update() {
        $yyyymm = $this->getRequest()->getSession()->read('detail_yyyymm');
        $employeeId = $this->getRequest()->getSession()->read('detail_employeeId');


        //修正
        if (isset($this->request->data['update_check'])) {
            if (isset($this->request->data['FoodHistoryInfo']['id'])) {
                // 実績更新
                // 状態フラグを設定
                $this->request->data['FoodHistoryInfo']['state_flg'] = '1';

                if ($this->FoodHistoryInfo->saveEX($this->request->data['FoodHistoryInfo'])) {
                    $this->Flash->success($this->property['message']['infoMsg02']);
                    return $this->redirect('/food-history-infos/detail');
                } else {
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
            } elseif (isset($this->request->data['ReservationInfo']['id'])) {
                // 予約更新
                // 状態フラグを設定
                $this->request->data['ReservationInfo']['state_flg'] = '1';
		$this->request->data['ReservationInfo']['employeeId'] = $employeeId;

                if ($this->ReservationInfo->saveEX($this->request->data['ReservationInfo'])) {
                    $this->Flash->success($this->property['message']['infoMsg02']);
                    return $this->redirect('/food-history-infos/detail');
                } else {
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
            }
            //削除
        } elseif (isset($this->request->data['delete_check'])) {
            if (isset($this->request->data['FoodHistoryInfo']['id'])) {
                // 実績更新
                // 状態フラグを設定
                $this->request->data['FoodHistoryInfo']['state_flg'] = '2';
    
                if ($this->FoodHistoryInfo->saveEX($this->request->data['FoodHistoryInfo'])) {
                    $this->Flash->success($this->property['message']['infoMsg03']);
                    return $this->redirect('/food-history-infos/detail'.'/'.$yyyymm.'/'.$employeeId);
                } else {
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
            } elseif (isset($this->request->data['ReservationInfo']['id'])) {
                // 予約更新
                // 状態フラグを設定
                $this->request->data['ReservationInfo']['state_flg'] = '2';
	        $this->request->data['ReservationInfo']['employeeId'] = $employeeId;
    
                if ($this->ReservationInfo->saveEX($this->request->data['ReservationInfo'])) {
                    $this->Flash->success($this->property['message']['infoMsg03']);
                    return $this->redirect('/food-history-infos/detail'.'/'.$yyyymm.'/'.$employeeId);
                } else {
                    $this->Flash->set($this->property['message']['exceptionMsg01']);
                }
            }
        }
        $this->detail($yyyymm, $employeeId);
        $this->render('detail');
    }

    private function check($data = null) {
        return true;
    }

    /**
     * バリデーション処理
     */
    private function inputValidate($data, &$resultMessage) {
        $employee_id = $this->convertEncode($data['employee_id']);
        $food_division = $this->convertEncode($data['food_division']);
        $card_recept_time = $this->convertEncode($data['card_recept_time']);
        $card_recept_time2 = $this->convertEncode($data['card_recept_time2']);

        //社員コード必須チェック
        if ($employee_id == '') {
            //エラー処理
            $resultMessage = $this->property['message']['errorMsg26'];
            return false;
        } else {
            //半角英数字チェック
            if (!preg_match("/^([a-zA-Z0-9])*$/u", $employee_id)) {
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

        //カード受付時間（日付）必須チェック
        if ($card_recept_time == '') {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], 'カード受付時間（日付）');
            return false;
        } else {
            //日付妥当性チェック
            if (!$this->isDate($card_recept_time)) {
                $resultMessage = sprintf($this->property['message']['errorMsg40'], 'カード受付時間（日付）', '日付');
                return false;
            }
        }

        //カード受付時間（時刻）必須チェック
        if ($card_recept_time2 == '') {
            //エラー処理
            $resultMessage = sprintf($this->property['message']['errorMsg39'], 'カード受付時間（時刻）');
            return false;
        } else {
            //時刻妥当性チェック
            if (!$this->isTime($card_recept_time2)) {
                $resultMessage = sprintf($this->property['message']['errorMsg40'], 'カード受付時間（時刻）', '時刻');
                return false;
            }
        }

        return true;
    }
}
