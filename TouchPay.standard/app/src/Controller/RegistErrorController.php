<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class RegistErrorController extends AppController {
    public $uses = array('RegistError', 'FoodDivision');
    public $components = array('RequestHandler');

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->set('title_for_layout', '登録エラー一覧画面');
        $this->set('menuLink', $this->getMenuLink(9));
    }

    /**
     * 登録エラー一覧画面
     */
    public function index() {

        // エラー一覧を取得
        $this->paginate = array(
            'limit' => 25,
            'order' => array(
                'occurrence_datetime' => 'desc'
            )
        );
        $registError = $this->paginate();

        // 食事マスタを取得
        $foodDivision = $this->FoodDivision->findEX(
            'all',
            array(
            'fields' => array('FoodDivisions.food_division', 'FoodDivisions.food_division_name')
            )
        );

        // ビューにセット
        $this->set('registError', $registError);
        $this->set('foodDivision', $foodDivision);
    }
}
