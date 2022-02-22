<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class JsonController extends AppController {
    public $uses = ['FoodHistoryInfo', 'FoodDivision', 'InstrumentDivision'];

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow('meals');
    }


    public function meals($lastFhiId = null, $lastUpdated = null) {
        $limit = $this->property['meals']['limit'];
        $newList = $this->listFoodHistoryInfos($lastFhiId, $limit);

        $to = (new DateTime())->modify('-1 sec');
        if ($lastUpdated) {
            $deleted = $this->listDeletedIds(self::getLastUpdatedDateTime($lastUpdated), $to);
        }

        $this->autoRender = false;
        return json_encode([
            'limit' => $limit,
            'newlyArrived' => $newList,
            'cancelled' => $deleted,
            'lastUpdated' => $to->format('YmdHis'),
            'interval' => $this->property['meals']['interval'],
            'suspend' => $this->property['meals']['suspend']
        ]);
    }


    private static function getLastUpdatedDateTime($date) {
        $date = $date ? DateTime::createFromFormat('YmdHis', $date) : new DateTime();
        $error = DateTime::getLastErrors();
        if (!empty($error['warning_count'])) {
            $date = new DateTime();
        }
        return $date;
    }


    private function listDeletedIds($from, $to) {

        //$this->log("### since: {$from->format('Y-m-d H:i:s')}");
	/*    
	$deleted = $this->FoodHistoryInfo->findEX('list', [
            'fields' => ['FoodHistoryInfo.id', 'FoodHistoryInfo.modified'],
            'conditions' => [
                'FoodHistoryInfo.modified >' => $from->format('Y-m-d H:i:s'),
                'FoodHistoryInfo.modified <=' => $to->format('Y-m-d H:i:s'),
                ["OR" => [
                    'FoodHistoryInfo.state_flg NOT IN' => [0, 1],
                    'FoodHistoryInfo.delete_flg !=' => 0,
                ]]
            ],
            'recursive' => 0
        ]);
	 */
	$query = $this->FoodHistoryInfo->find('list', [
            'keyField' => 'id',
            'valueField' => 'modified',
        ]);
        $deleted = $query->where(['FoodHistoryInfo.modified >' => $from->format('Y-m-d H:i:s'),
                'FoodHistoryInfo.modified <=' => $to->format('Y-m-d H:i:s'),
                ["OR" => [
                    'FoodHistoryInfo.state_flg NOT IN' => [0, 1],
                    'FoodHistoryInfo.delete_flg !=' => 0,
                ]]])->toArray();

        return $deleted;
    }


    private function listFoodHistoryInfos($lastFhiId, $limit) {
        $this->FoodHistoryInfo->bindModel(
            ['hasOne' => [
                'FoodDivision' => [
                    'className' => 'FoodDivision',
                    'foreignKey' => false,
                    'conditions' => ['FoodHistoryInfo.food_division = FoodDivision.food_division']
                ],
                'InstrumentDivision' => [
                    'className' => 'InstrumentDivision',
                    'foreignKey' => false,
                    'conditions' => ['FoodHistoryInfo.instrument_division = InstrumentDivision.instrument_division']
                ],
                'EmployeeInfo' => [
                    'className' => 'EmployeeInfo',
                    'foreignKey' => false,
                    'conditions' => ['FoodHistoryInfo.employee_id = EmployeeInfo.employee_id']
                ],
                'EmployeeKbn' => [
                    'className' => 'EmployeeKbn',
                    'foreignKey' => false,
                    'conditions' => ['FoodHistoryInfo.employee_kbn = EmployeeKbn.employee_kbn']
                ],
            ]
        ]
        );

        $fields = array_merge(
            array_map(function ($col) {
                return "FoodHistoryInfo.$col";
            }, ['id', 'employee_id', 'ic_card_number', 'card_recept_time', 'created']),
            array_map(function ($col) {
                return "FoodDivision.$col";
            }, ['food_division_name', 'food_cost']),
            array_map(function ($col) {
                return "InstrumentDivision.$col";
            }, ['instrument_name']),
            array_map(function ($col) {
                return "EmployeeInfo.$col";
            }, ['employee_name1', 'employee_name2']),
            array_map(function ($col) {
                return "EmployeeKbn.$col";
            }, ['employee_kbn_name'])
        );

        if (empty($lastFhiId)) {
            $newList = $this->FoodHistoryInfo->findEX('all', [
                'fields' => $fields,
                'conditions' => [
                    'FoodHistoryInfo.state_flg IN' => [0, 1],
                    'FoodHistoryInfo.delete_flg' => 0
                ],
                'order' => ['FoodHistoryInfo.created desc', 'FoodHistoryInfo.card_recept_time desc'],
                'limit' => $limit
            ]);
            $newList = array_reverse($newList);
        } else {
            $newList = $this->FoodHistoryInfo->findEX('all', [
                'fields' => $fields,
                'conditions' => [
                    'FoodHistoryInfo.id >' => $lastFhiId,
                    'FoodHistoryInfo.state_flg IN' => [0, 1],
                    'FoodHistoryInfo.delete_flg' => 0
                ],
                'order' => ['FoodHistoryInfo.created', 'FoodHistoryInfo.card_recept_time']
            ]);
        }
        return $newList;
    }
}
