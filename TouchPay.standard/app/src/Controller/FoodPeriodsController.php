<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\i18n\Time;

class FoodPeriodsController extends AppController {
    public $uses = ['FoodPeriod', 'FoodDivision'];
    private $pageLine = 20;

    const ADD_SUCCESS = '食事期間を登録しました。';
    const UPDATE_SUCCESS = '食事期間を更新しました。';
    const DELETE_SUCCESS = '食事期間を削除しました。';
    const DUPLICATE_START_DATE = '開始日が重複しています。';

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->set('menuLink', $this->getMenuLink(13));
        $this->set('title_for_layout', '食事期間一覧');
    }

    public function index($id) {
	$foodDivision = $this->FoodDivision->objectToArray($this->FoodDivision->findByFoodDivision($id));
        $this->set('foodDivision', $foodDivision);
        $foodPeriods = $this->FoodPeriod->findEX('all', [
            'conditions' => ['food_division' => $id],
            'order' => ['start_date'],
        ]);
        $this->set('foodPeriods', $foodPeriods);
    }

    public function add() {
        $data = $this->request->data['FoodPeriod']['FoodPeriod'];
        $data['created'] = $data['modified'] = new Time(date('Y-m-d H:i:s'));
	$data['delete_flg'] = 0;
        return $this->save($data, self::ADD_SUCCESS);
    }

    public function update($index) {
        $data = $this->request->data['FoodPeriod'][$index];
        unset($data['created']);
        $data['modified'] = new Time(date('Y-m-d H:i:s'));
        $data['delete_flg'] = 0;
	return $this->save($data, self::UPDATE_SUCCESS);
    }

    public function delete($index) {
        $data = $this->request->data['FoodPeriod'][$index];
        unset($data['created']);
        $data['modified'] = new Time(date('Y-m-d H:i:s'));
        $data['delete_flg'] = 1;
        return $this->save($data, self::DELETE_SUCCESS);
    }

    private function save($data, $successMessage) {
        $errorMessage = "";
        if ($this->request->is('post')) {
	    $this->FoodPeriod->set($data);
	    $foodPeriodEntity = $this->FoodPeriod->newEntity($data);
            if (!$foodPeriodEntity->errors()) {
                if ($data['delete_flg'] == 0 && $this->FoodPeriod->isFoodTermExisting($data, true)) {
                    $errorMessage = self::DUPLICATE_START_DATE;
                } elseif ($this->FoodPeriod->saveEX($data)) {
                    $this->Flash->success($successMessage);
                    return $this->redirect(['action' => 'index', $data['food_division']]);
                }
            }
            $this->set('targetFoodPeriod', $data);
	}
	$validate_errorMessage = (array)$foodPeriodEntity->errors();
	foreach($validate_errorMessage as $key1 => $value1) {
	    foreach($value1 as $key2 => $value2) {
	            $errorMessage = $value2;
	    }
	}	
//	$this->Flash->set(self::getFirstMessage($foodPeriodEntity->errors(), $errorMessage));
	$this->Flash->set($errorMessage);
        $this->index($data['food_division']);
        $this->render('index');
    }

    private static function getFirstMessage($invalidFields, $default) {
	if(isset($invalidFields)){
            foreach ($invalidFields as $errors) {
                foreach ($errors as $value) {
                    return $value;
                }
	    }
        }
        return $default;
    }
}
