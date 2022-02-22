<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;

class CsvsController extends AppController
{
    public $uses = ['FoodHistoryInfo', 'Administrator', 'EmployeeInfo', 'FoodDivision', 'ReservationInfo', 'EmployeeKbn'];
    public $helpers = ['Csv'];

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->set('menuLink', $this->getMenuLink(5));
        // 事業所情報
        $baseKbnList = [0 => '全て'] + $this->getInstrumentDivisionList();
        $this->set('baseKbnList', $baseKbnList);
    }

    public function select()
    {
        $this->set('title_for_layout', 'ＣＳＶファイル出力画面');
        // 食事マスタ情報
        $this->set('foodDivisionList', $this->getFoodDivisionList());
        // 社員区分リスト
        $employeeKbnList = $this->getEmployeeKbnList();
        $employeeKbns = ['全て'] + $employeeKbnList;
        $this->set('employeeKbns', $employeeKbns);
    }

    /**
     * 【人事部向け】社員情報.
     */
    public function employeeInfos()
    {
        $this->log('社員情報', 'debug');

        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();
        $dataList = $this->EmployeeInfo->findEX('all', [
            'conditions' => ['employee_kbn IN' => $employeeKbnArray],
            'order' => ['LPAD(employee_kbn, 2, 0)', 'employee_id'],
        ]);

        if (!empty($dataList)) {
            Configure::write('debug', 0); //警告を出さない
            $this->layout = false;

            //ファイル名
            $this->set('fileName', '社員情報_'.date('Ymd'));
            //ビューにセット
            $this->set('dataList', $dataList);
        } else {
	    $this->Flash->success($this->property['message']['infoMsg04']);
            $this->select();
            $this->render('select');
        }
    }

    /**
     * 【人事部向け】社員情報.
     */
    public function employeeInfosExcel()
    {
        $this->log('EXCEL出力', 'debug');

        // 参照可能社員区分取得
        $employeeKbnArray = $this->getConditionEmployeeKbnList();
        $dataList = $this->EmployeeInfo->findEX('all', [
            'conditions' => ['employee_kbn IN' => $employeeKbnArray],
            'order' => ['LPAD(employee_kbn, 2, 0)', 'employee_id'],
        ]);

        if (!empty($dataList)) {
            Configure::write('debug', 0); //警告を出さない
            $this->layout = false;

            //ファイル名
            $this->set('excelName', '社員情報_'.date('Ymd'));
            //ビューにセット
            $this->set('dataList', $dataList);
        } else {
            //$this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg04'], 'default', ['class' => 'success']);
            $this->Flash->success($this->property['message']['infoMsg04']);
            $this->select();
            $this->render('select');
        }
    }

    /**
     * 【総務部向け】月別全体実績状況
     */
    public function gaAllPerformance()
    {
        $this->log('月別全体実績状況', 'debug');

        $startYmd = $this->request->data['Csvs']['summary_start_date'];
        $endYmd = $this->request->data['Csvs']['summary_end_date'];

        if (!$this->isValidStartAndEndDate($startYmd, $endYmd, '%Y-%m-%d')) {
            $this->select();
            $this->render('select');

            return;
        }

        // 社員区分取得
        $employeeKbn = $this->request->data['Csvs']['employee_kbn_all'];
        $employeeKbnArray = $this->getEmployeeKbnCondition($employeeKbn);
        // 事業所区分取得
        $baseKbnGa = $this->request->data['Csvs']['base_kbn_ga'];
        $this->log($baseKbnGa, 'error');

        // 食事区分取得
        if($baseKbnGa == 0){
            $foodDivisionList = $this->getFoodDivisionList();
        } else {
            $foodDivisionList = $this->getFoodDivisionList($baseKbnGa);
        }
        // 予約区分取得
        if(!$this->property['plan_config']['standard_plan']){
            $reservationDivisionList = $this->getreservationDivisionList();
        }

        // データ取得
        $dataList = $this->EmployeeInfo->getCsvHrEmployeePerformance($startYmd, $endYmd, $employeeKbnArray, array_keys($foodDivisionList));

        if (!empty($dataList)) {
            Configure::write('debug', 0); //警告を出さない
            $this->layout = false;

            // ファイル名
            $this->set('fileName', '月別全体実績状況_'.$startYmd.'_'.$endYmd);
            // ビューにセット
            $this->set('dataList', $dataList);
            $this->set('exportMenu', $this->request->data['Csvs']['exportMenu']);
            $this->set('foodDivisionList', $foodDivisionList);
            $this->set('reservationDivisionList', $reservationDivisionList);
        } else {
//            $this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg04'], 'default', ['class' => 'success']);
            $this->Flash->success($this->property['message']['infoMsg04']);
            $this->select();
            $this->render('select');
        }
    }

    /**
     * 【総務部向け】月別個別予約・実績状況
     */
    public function gaPerformance()
    {
        $this->log('月別個別予約・実績状況', 'debug');

        $startYmd = $this->request->data['Csvs']['detail_start_date'];
        $endYmd = $this->request->data['Csvs']['detail_end_date'];
        $baseKbn = $this->request->data['Csvs']['base_kbn'];

        if (!$this->isValidStartAndEndDate($startYmd, $endYmd, '%Y-%m-%d')) {
            $this->select();
            $this->render('select');

            return;
        }

        // 社員区分取得
        $employeeKbn = $this->request->data['Csvs']['employee_kbn'];
        $employeeKbnArray = $this->getEmployeeKbnCondition($employeeKbn);
        // 事業所から絞り込み用の食事区分を抽出
        $tempList = $this->getFoodDivisionList($baseKbn);
        $foodDivisionArray = [];
        foreach ($tempList as $key => $value) {
            array_push($foodDivisionArray, $key);
        }

        // データ取得
        $dataList = $this->FoodHistoryInfo->getCsvGaPerformance($startYmd, $endYmd, $foodDivisionArray, $employeeKbnArray);

        if (!empty($dataList)) {
            Configure::write('debug', 0); //警告を出さない
            $this->layout = false;

            // ファイル名
            $baseName = $this->getInstrumentDivisionList()[$baseKbn];
            $this->set('fileName', '月別個別予約・実績状況（'.$baseName.'）_'.$startYmd.'_'.$endYmd);
            // ビューにセット
            $this->set('dataList', $dataList);
        } else {
            //$this->getRequest()->getSession()->setFlash($this->property['message']['infoMsg04'], 'default', ['class' => 'success']);
            $this->Flash->success($this->property['message']['infoMsg04']);
            $this->select();
            $this->render('select');
        }
    }

    /**
     * 開始、終了日付チェック.
     */
    private function isValidStartAndEndDate($startYmd, $endYmd, $format)
    {
        $isValidDate = true;

        if (empty($startYmd) === true || empty($endYmd) === true) {
            // 空白
            //$this->getRequest()->getSession()->setFlash($this->property['message']['errorMsg49']);
            $this->Flash->set($this->property['message']['errorMsg49']);
            $isValidDate = false;
        } else {
            if (!strptime($startYmd, $format) || !strptime($endYmd, $format)) {
                // フォーマット不正
                //$this->getRequest()->getSession()->setFlash($this->property['message']['errorMsg50']);
                $this->Flash->set($this->property['message']['errorMsg50']);
                $isValidDate = false;
            } elseif (strtotime($startYmd) > strtotime($endYmd)) {
                // 開始日付より終了日付の方が未来
                //$this->getRequest()->getSession()->setFlash($this->property['message']['errorMsg51']);
                $this->Flash->set($this->property['message']['errorMsg51']);
                $isValidDate = false;
            }
        }

        return $isValidDate;
    }

    /**
     * クエリ条件用の社員区分配列を取得
     * 画面上で全てが選択されている場合　参照可能社員区分全てを返却
     * その他区分が選択されている場合　その値のみを配列にして返却.
     *
     * @param employeeKbn
     */
    private function getEmployeeKbnCondition($employeeKbn)
    {
        if ($employeeKbn === '0') {
            // 参照可能社員区分取得
            return $this->getConditionEmployeeKbnList();
        } else {
            $result = [];
            array_push($result, $employeeKbn);

            return $result;
        }
    }
}
